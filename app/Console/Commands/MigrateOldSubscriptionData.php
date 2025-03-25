<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateOldSubscriptionData extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:migrate-old-data {--dry-run : Tryb testowy bez zapisywania danych} {--limit=100 : Maksymalna liczba rekordów do przetworzenia}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Migruje dane z starego systemu subskrypcji do nowego modelu danych';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');
        
        if ($dryRun) {
            $this->info("Uruchomiono w trybie testowym (dry-run) - zmiany NIE będą zapisywane.");
        }
        
        $this->info("Rozpoczynam migrację danych z starego systemu subskrypcji...");
        
        // Sprawdź czy tabela starego systemu istnieje
        if (!Schema::hasTable('old_subscriptions')) {
            $this->error("Tabela 'old_subscriptions' nie istnieje. Nie można przeprowadzić migracji.");
            return 1;
        }
        
        // Pobierz plany subskrypcji
        $plans = SubscriptionPlan::all()->keyBy('code');
        
        if ($plans->isEmpty()) {
            $this->error("Nie znaleziono planów subskrypcji. Uruchom seeder planów subskrypcyjnych.");
            return 1;
        }
        
        // Pobierz stare subskrypcje do migracji
        $oldSubscriptions = DB::table('old_subscriptions')
            ->where('migrated', false)
            ->limit($limit)
            ->get();
            
        $count = $oldSubscriptions->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono starych subskrypcji do migracji.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} starych subskrypcji do migracji.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $migratedCount = 0;
        $errorCount = 0;
        
        foreach ($oldSubscriptions as $oldSub) {
            if (!$dryRun) {
                DB::beginTransaction();
            }
            
            try {
                // Znajdź użytkownika
                $user = User::find($oldSub->user_id);
                
                if (!$user) {
                    throw new \Exception("Nie znaleziono użytkownika o ID: {$oldSub->user_id}");
                }
                
                // Mapuj stary plan na nowy
                $planCode = $this->mapOldPlanToNew($oldSub->old_plan_type);
                
                if (!isset($plans[$planCode])) {
                    throw new \Exception("Nie znaleziono planu o kodzie: {$planCode}");
                }
                
                $plan = $plans[$planCode];
                
                // Utwórz nową subskrypcję
                $subscription = new UserSubscription([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => $this->mapOldStatusToNew($oldSub->status),
                    'start_date' => Carbon::parse($oldSub->created_at),
                    'end_date' => $oldSub->expires_at ? Carbon::parse($oldSub->expires_at) : null,
                    'payment_method' => $this->mapOldPaymentMethodToNew($oldSub->payment_method),
                    'payment_details' => "Zmigrowano z ID: {$oldSub->id}",
                    'admin_notes' => "Automatyczna migracja ze starego systemu. Stare dane: " . json_encode([
                        'old_id' => $oldSub->id,
                        'old_plan' => $oldSub->old_plan_type,
                        'old_status' => $oldSub->status,
                    ]),
                    'auto_renew' => $oldSub->auto_renew == 1,
                ]);
                
                // Zapisz tylko jeśli nie jest to tryb testowy
                if (!$dryRun) {
                    $subscription->save();
                    
                    // Migruj płatności jeśli istnieją
                    $this->migrateOldPayments($oldSub, $subscription);
                    
                    // Oznacz starą subskrypcję jako zmigrowaną
                    DB::table('old_subscriptions')
                        ->where('id', $oldSub->id)
                        ->update(['migrated' => true]);
                }
                
                $this->line("");
                $this->info("Zmigrowano subskrypcję dla użytkownika: {$user->email}, Plan: {$plan->name}");
                
                if (!$dryRun) {
                    DB::commit();
                }
                
                $migratedCount++;
                
            } catch (\Exception $e) {
                if (!$dryRun) {
                    DB::rollBack();
                }
                
                $this->error("Błąd podczas migracji subskrypcji ID {$oldSub->id}: " . $e->getMessage());
                Log::error("Błąd podczas migracji subskrypcji ID {$oldSub->id}: " . $e->getMessage());
                $errorCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        
        if ($dryRun) {
            $this->info("Zakończono testową migrację danych. Przetworzono: {$count}, potencjalnie do migracji: {$migratedCount}, błędy: {$errorCount}");
        } else {
            $this->info("Zakończono migrację danych. Pomyślnie zmigrowano: {$migratedCount}, błędy: {$errorCount}");
        }
        
        return 0;
    }
    
    /**
     * Mapuje stary typ planu na nowy kod planu
     */
    private function mapOldPlanToNew($oldPlanType)
    {
        $mapping = [
            'free' => 'free',
            'basic' => 'basic',
            'standard' => 'standard',
            'premium' => 'premium',
            'premium_yearly' => 'premium_yearly',
            // dodaj inne mapowania jeśli potrzebne
        ];
        
        return $mapping[$oldPlanType] ?? 'free';
    }
    
    /**
     * Mapuje stary status na nowy status
     */
    private function mapOldStatusToNew($oldStatus)
    {
        $mapping = [
            'active' => 'active',
            'pending' => 'pending',
            'cancelled' => 'canceled',
            'expired' => 'expired',
            'trial' => 'trial',
            // dodaj inne mapowania jeśli potrzebne
        ];
        
        return $mapping[$oldStatus] ?? 'pending';
    }
    
    /**
     * Mapuje starą metodę płatności na nową
     */
    private function mapOldPaymentMethodToNew($oldMethod)
    {
        $mapping = [
            'credit_card' => 'card',
            'bank_transfer' => 'bank_transfer',
            'paypal' => 'paypal',
            'free' => 'free',
            // dodaj inne mapowania jeśli potrzebne
        ];
        
        return $mapping[$oldMethod] ?? 'other';
    }
    
    /**
     * Migruje stare płatności do nowego systemu
     */
    private function migrateOldPayments($oldSubscription, $newSubscription)
    {
        // Pobierz stare płatności
        $oldPayments = DB::table('old_payments')
            ->where('subscription_id', $oldSubscription->id)
            ->get();
            
        foreach ($oldPayments as $oldPayment) {
            Payment::create([
                'user_id' => $newSubscription->user_id,
                'user_subscription_id' => $newSubscription->id,
                'transaction_id' => $oldPayment->transaction_id ?? 'old_' . $oldPayment->id,
                'amount' => $oldPayment->amount,
                'currency' => $oldPayment->currency ?? 'PLN',
                'status' => $this->mapOldPaymentStatusToNew($oldPayment->status),
                'payment_method' => $this->mapOldPaymentMethodToNew($oldPayment->payment_method),
                'payment_details' => "Zmigrowano z ID: {$oldPayment->id}",
                'payment_date' => Carbon::parse($oldPayment->created_at),
                'invoice_id' => $oldPayment->invoice_id,
                'description' => "Płatność za subskrypcję {$newSubscription->subscription_plan->name}",
            ]);
        }
    }
    
    /**
     * Mapuje stary status płatności na nowy
     */
    private function mapOldPaymentStatusToNew($oldStatus)
    {
        $mapping = [
            'completed' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'refunded' => 'refunded',
            // dodaj inne mapowania jeśli potrzebne
        ];
        
        return $mapping[$oldStatus] ?? 'pending';
    }
} 
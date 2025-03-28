<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateUsersToFreeSubscription extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'subscriptions:migrate-users-to-free {--limit=50 : Maksymalna liczba użytkowników do przetworzenia}';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Przypisuje plan darmowy użytkownikom, którzy nie mają żadnej subskrypcji';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        $this->info("Rozpoczynam migrację użytkowników do planu darmowego...");
        
        // Znajdź użytkowników, którzy nie mają żadnej subskrypcji
        // i nie są administratorami (dla nich mamy oddzielną komendę)
        $users = User::whereDoesntHave('userSubscriptions')
            ->whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })
            ->limit($limit)
            ->get();
            
        $count = $users->count();
        
        if ($count === 0) {
            $this->info("Nie znaleziono użytkowników bez subskrypcji.");
            return 0;
        }
        
        $this->info("Znaleziono {$count} użytkowników bez subskrypcji.");
        
        // Pobierz plan darmowy
        $freePlan = SubscriptionPlan::where('code', 'free')->first();
        
        if (!$freePlan) {
            $this->error("Nie znaleziono planu darmowego (code: 'free'). Uruchom seeder planów subskrypcyjnych.");
            return 1;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $migratedCount = 0;
        $errorCount = 0;
        
        foreach ($users as $user) {
            DB::beginTransaction();
            
            try {
                // Utwórz subskrypcję darmową dla użytkownika
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $freePlan->id,
                    'status' => 'active',
                    'start_date' => Carbon::now(),
                    'end_date' => null, // bezterminowo
                    'payment_method' => 'free',
                    'payment_details' => 'Automatyczne przypisanie planu darmowego',
                    'admin_notes' => 'Automatycznie przypisano podczas migracji użytkowników',
                    'auto_renew' => false, // plan darmowy nie wymaga odnawiania
                ]);
                
                $this->line("");
                $this->info("Przypisano plan darmowy dla użytkownika: {$user->email}");
                Log::info("Przypisano plan darmowy dla użytkownika: {$user->email}");
                
                $migratedCount++;
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Błąd podczas migracji użytkownika {$user->email}: " . $e->getMessage());
                Log::error("Błąd podczas migracji użytkownika {$user->email}: " . $e->getMessage());
                $errorCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        $this->info("Zakończono migrację użytkowników do planu darmowego.");
        $this->info("Pomyślnie zmigrowano: {$migratedCount}, błędy: {$errorCount}");
        
        return 0;
    }
}

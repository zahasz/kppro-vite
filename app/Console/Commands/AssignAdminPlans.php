<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignAdminPlans extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'admin:assign-plans';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Przypisuje plan Premium dla wszystkich administratorów';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        $this->info('Rozpoczynam przypisywanie planów Premium dla administratorów...');
        
        // Pobierz plan Premium
        $premiumPlan = SubscriptionPlan::where('code', 'premium')->first();
        
        if (!$premiumPlan) {
            $this->error('Plan Premium nie istnieje! Uruchom najpierw seeder planów subskrypcyjnych.');
            return 1;
        }
        
        // Pobierz wszystkich administratorów
        $adminUsers = User::role(['admin', 'super-admin'])->get();
        
        if ($adminUsers->isEmpty()) {
            $this->info('Nie znaleziono żadnych administratorów w systemie.');
            return 0;
        }
        
        $this->info("Znaleziono {$adminUsers->count()} administratorów.");
        
        $assignedCount = 0;
        $skippedCount = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($adminUsers as $admin) {
                // Sprawdź czy admin ma już subskrypcję
                $existingSubscription = UserSubscription::where('user_id', $admin->id)->first();
                
                if ($existingSubscription) {
                    $this->line("Administrator {$admin->email} ma już przypisaną subskrypcję - pomijam.");
                    $skippedCount++;
                    continue;
                }
                
                // Utwórz nową subskrypcję
                UserSubscription::create([
                    'user_id' => $admin->id,
                    'subscription_plan_id' => $premiumPlan->id,
                    'status' => 'active',
                    'start_date' => Carbon::now(),
                    'end_date' => null, // bezterminowo
                    'payment_method' => 'free', // administratorzy mają dostęp bez opłat
                    'payment_details' => 'Automatyczne przypisanie dla administratora',
                    'admin_notes' => 'Przypisane automatycznie przez komendę admin:assign-plans',
                    'auto_renew' => true,
                ]);
                
                $this->info("Przypisano plan Premium dla administratora: {$admin->email}");
                $assignedCount++;
            }
            
            DB::commit();
            
            $this->info("Zakończono przypisywanie planów Premium dla administratorów.");
            $this->info("Przypisano plany: {$assignedCount}, pominięto: {$skippedCount}");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Wystąpił błąd podczas przypisywania planów: " . $e->getMessage());
            return 1;
        }
    }
}

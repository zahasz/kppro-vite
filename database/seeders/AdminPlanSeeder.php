<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;

class AdminPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Tworzenie planu dla administratora
        $plan = Plan::create([
            'name' => 'Plan Administratora',
            'description' => 'Plan z pełnym dostępem do wszystkich funkcji systemu',
            'price' => 0.00, // Darmowy plan dla administratora
            'interval' => 'year',
            'trial_period_days' => null,
            'features' => json_encode([
                'Pełny dostęp do panelu administratora',
                'Zarządzanie użytkownikami',
                'Zarządzanie rolami i uprawnieniami',
                'Zarządzanie subskrypcjami',
                'Dostęp do logów systemowych'
            ]),
            'is_active' => true
        ]);

        // Przypisanie planu do administratora
        $admin = User::role('admin')->first();
        
        if ($admin) {
            Subscription::create([
                'user_id' => $admin->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'price' => 0.00,
                'start_date' => Carbon::now(),
                'end_date' => null, // Bezterminowo
            ]);
        }
    }
} 
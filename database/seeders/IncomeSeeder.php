<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pobierz pierwszego użytkownika lub stwórz nowego jeśli nie istnieje
        $user = User::first() ?? User::factory()->create();

        // Przykładowe przychody dla bieżącego roku
        $currentYear = date('Y');
        $statuses = ['received', 'pending', 'cancelled'];
        $categories = ['salary', 'invoice', 'service', 'product', 'other'];
        $paymentMethods = ['cash', 'transfer', 'card', 'blik', 'other'];

        // Generuj przychody dla każdego miesiąca
        for ($month = 1; $month <= 12; $month++) {
            // 3-5 przychodów na miesiąc
            $count = rand(3, 5);
            
            for ($i = 0; $i < $count; $i++) {
                Income::create([
                    'title' => 'Przychód ' . $i + 1,
                    'amount' => rand(1000, 10000) + (rand(0, 99) / 100),
                    'income_date' => Carbon::create($currentYear, $month, rand(1, 28)),
                    'category' => $categories[array_rand($categories)],
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => $statuses[array_rand($statuses)],
                    'description' => 'Opis przychodu ' . ($i + 1),
                    'document_number' => 'DOC/' . $currentYear . '/' . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . ($i + 1),
                    'user_id' => $user->id
                ]);
            }
        }
    }
}

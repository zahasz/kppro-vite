<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use Illuminate\Database\Seeder;

class BudgetCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Gotówka
            [
                'name' => 'Gotówka w kasie',
                'type' => 'cash',
                'amount' => 5000,
                'planned_amount' => 10000,
                'description' => 'Środki pieniężne w kasie firmy',
            ],

            // Konta bankowe
            [
                'name' => 'Konto główne firmowe',
                'type' => 'company_bank',
                'amount' => 25000,
                'planned_amount' => 50000,
                'description' => 'Główne konto firmowe',
            ],
            [
                'name' => 'Konto oszczędnościowe',
                'type' => 'company_bank',
                'amount' => 15000,
                'planned_amount' => 20000,
                'description' => 'Konto oszczędnościowe firmowe',
            ],
            [
                'name' => 'Konto prywatne',
                'type' => 'private_bank',
                'amount' => 8000,
                'planned_amount' => 10000,
                'description' => 'Prywatne konto bankowe',
            ],

            // Pożyczki zaciągnięte
            [
                'name' => 'Kredyt inwestycyjny',
                'type' => 'loans_taken',
                'amount' => 50000,
                'planned_amount' => 50000,
                'description' => 'Kredyt na rozwój firmy',
            ],

            // Pożyczki udzielone
            [
                'name' => 'Pożyczka dla kontrahenta',
                'type' => 'loans_given',
                'amount' => 10000,
                'planned_amount' => 10000,
                'description' => 'Pożyczka udzielona firmie XYZ',
            ],

            // Inwestycje
            [
                'name' => 'Akcje spółki ABC',
                'type' => 'investments',
                'amount' => 20000,
                'planned_amount' => 25000,
                'description' => 'Inwestycja w akcje spółki ABC',
            ],
            [
                'name' => 'Lokata terminowa',
                'type' => 'investments',
                'amount' => 30000,
                'planned_amount' => 30000,
                'description' => 'Lokata 6-miesięczna',
            ],

            // Leasing
            [
                'name' => 'Samochód służbowy',
                'type' => 'leasing',
                'amount' => 35000,
                'planned_amount' => 40000,
                'description' => 'Leasing samochodu służbowego',
            ],
            [
                'name' => 'Sprzęt biurowy',
                'type' => 'leasing',
                'amount' => 15000,
                'planned_amount' => 15000,
                'description' => 'Leasing sprzętu biurowego',
            ],
        ];

        foreach ($categories as $category) {
            BudgetCategory::create($category);
        }
    }
} 
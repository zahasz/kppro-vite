<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\Contractor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuwanie istniejących faktur
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        InvoiceItem::truncate();
        Invoice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Usunięto istniejące faktury.');

        $admin = User::where('email', 'admin@kppro.pl')->first();
        
        if (!$admin) {
            $this->command->error('Użytkownik admin nie istnieje. Najpierw uruchom AdminSeeder.');
            return;
        }

        $contractors = Contractor::where('user_id', $admin->id)->get();
        
        if ($contractors->isEmpty()) {
            $this->command->error('Brak kontrahentów. Najpierw uruchom ContractorSeeder.');
            return;
        }

        // Przykładowe faktury
        $invoices = [
            [
                'number' => 'FV/2025/03/001',
                'contractor_id' => $contractors->where('company_name', 'IT Solutions Pro Sp. z o.o.')->first()?->id,
                'payment_method' => 'przelew',
                'issue_date' => Carbon::now(),
                'sale_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(14),
                'status' => 'issued',
                'items' => [
                    [
                        'name' => 'Usługi programistyczne',
                        'description' => 'Rozwój aplikacji webowej - marzec 2025',
                        'quantity' => 80,
                        'unit' => 'godz.',
                        'unit_price' => 150.00,
                        'tax_rate' => 23.00,
                    ],
                    [
                        'name' => 'Hosting i utrzymanie',
                        'description' => 'Hosting aplikacji - marzec 2025',
                        'quantity' => 1,
                        'unit' => 'szt.',
                        'unit_price' => 500.00,
                        'tax_rate' => 23.00,
                    ]
                ]
            ],
            [
                'number' => 'FV/2025/03/002',
                'contractor_id' => $contractors->where('company_name', 'Firma Budowlana "Budex" Sp. z o.o.')->first()?->id,
                'payment_method' => 'przelew',
                'issue_date' => Carbon::now()->subDays(5),
                'sale_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(9),
                'status' => 'issued',
                'items' => [
                    [
                        'name' => 'Projekt budowlany',
                        'description' => 'Projekt budowlany budynku biurowego',
                        'quantity' => 1,
                        'unit' => 'szt.',
                        'unit_price' => 15000.00,
                        'tax_rate' => 23.00,
                    ],
                    [
                        'name' => 'Konsultacje architektoniczne',
                        'description' => 'Konsultacje dotyczące projektu',
                        'quantity' => 10,
                        'unit' => 'godz.',
                        'unit_price' => 200.00,
                        'tax_rate' => 23.00,
                    ]
                ]
            ],
            [
                'number' => 'FV/2025/03/003',
                'contractor_id' => $contractors->where('company_name', 'Transport i Logistyka "Trans-Log"')->first()?->id,
                'payment_method' => 'przelew',
                'issue_date' => Carbon::now()->subDays(10),
                'sale_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->subDays(3),
                'status' => 'overdue',
                'items' => [
                    [
                        'name' => 'Usługi transportowe',
                        'description' => 'Transport materiałów budowlanych',
                        'quantity' => 1,
                        'unit' => 'usł.',
                        'unit_price' => 3500.00,
                        'tax_rate' => 23.00,
                    ]
                ]
            ],
            [
                'number' => 'FV/2025/03/004',
                'contractor_id' => $contractors->where('company_name', 'Hurtownia Spożywcza "Smak" S.A.')->first()?->id,
                'payment_method' => 'gotówka',
                'issue_date' => Carbon::now()->subDays(15),
                'sale_date' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->subDays(15),
                'status' => 'paid',
                'items' => [
                    [
                        'name' => 'Catering',
                        'description' => 'Usługa cateringowa na spotkanie firmowe',
                        'quantity' => 20,
                        'unit' => 'os.',
                        'unit_price' => 50.00,
                        'tax_rate' => 8.00,
                    ],
                    [
                        'name' => 'Napoje',
                        'description' => 'Napoje bezalkoholowe',
                        'quantity' => 40,
                        'unit' => 'szt.',
                        'unit_price' => 5.00,
                        'tax_rate' => 23.00,
                    ]
                ]
            ],
            [
                'number' => 'FV/2025/03/005',
                'contractor_id' => $contractors->where('company_name', 'Biuro Rachunkowe "Bilans"')->first()?->id,
                'payment_method' => 'przelew',
                'issue_date' => Carbon::now()->subDays(20),
                'sale_date' => Carbon::now()->subDays(20),
                'due_date' => Carbon::now()->subDays(6),
                'status' => 'paid',
                'items' => [
                    [
                        'name' => 'Usługi księgowe',
                        'description' => 'Prowadzenie księgowości - luty 2025',
                        'quantity' => 1,
                        'unit' => 'mies.',
                        'unit_price' => 1200.00,
                        'tax_rate' => 23.00,
                    ],
                    [
                        'name' => 'Rozliczenie ZUS',
                        'description' => 'Rozliczenie składek ZUS - luty 2025',
                        'quantity' => 5,
                        'unit' => 'os.',
                        'unit_price' => 50.00,
                        'tax_rate' => 23.00,
                    ]
                ]
            ]
        ];

        foreach ($invoices as $invoiceData) {
            $contractorId = $invoiceData['contractor_id'];
            
            if (!$contractorId) {
                $this->command->warn('Nie znaleziono kontrahenta dla faktury ' . $invoiceData['number']);
                continue;
            }
            
            $contractor = Contractor::find($contractorId);
            
            if (!$contractor) {
                $this->command->warn('Nie znaleziono kontrahenta o ID ' . $contractorId . ' dla faktury ' . $invoiceData['number']);
                continue;
            }

            // Obliczanie sum
            $netTotal = 0;
            $taxTotal = 0;
            $grossTotal = 0;

            foreach ($invoiceData['items'] as $item) {
                $netValue = $item['quantity'] * $item['unit_price'];
                $taxValue = $netValue * ($item['tax_rate'] / 100);
                $grossValue = $netValue + $taxValue;

                $netTotal += $netValue;
                $taxTotal += $taxValue;
                $grossTotal += $grossValue;
            }

            // Tworzenie faktury
            $invoice = Invoice::create([
                'user_id' => $admin->id,
                'number' => $invoiceData['number'],
                'contractor_name' => $contractor->company_name,
                'contractor_nip' => $contractor->nip,
                'contractor_address' => $this->formatAddress($contractor),
                'payment_method' => $invoiceData['payment_method'],
                'issue_date' => $invoiceData['issue_date'],
                'sale_date' => $invoiceData['sale_date'],
                'due_date' => $invoiceData['due_date'],
                'net_total' => $netTotal,
                'tax_total' => $taxTotal,
                'gross_total' => $grossTotal,
                'currency' => 'PLN',
                'status' => $invoiceData['status'],
                'issued_by' => $admin->name ?? 'System',
            ]);

            // Tworzenie pozycji faktury
            foreach ($invoiceData['items'] as $index => $itemData) {
                $netValue = $itemData['quantity'] * $itemData['unit_price'];
                $taxValue = $netValue * ($itemData['tax_rate'] / 100);
                $grossValue = $netValue + $taxValue;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'name' => $itemData['name'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'net_price' => $netValue,
                    'tax_rate' => $itemData['tax_rate'],
                    'tax_amount' => $taxValue,
                    'gross_price' => $grossValue,
                    'position' => $index + 1
                ]);
            }
        }

        $this->command->info('Utworzono ' . count($invoices) . ' przykładowych faktur.');
    }

    /**
     * Formatuje adres kontrahenta
     */
    private function formatAddress(Contractor $contractor): string
    {
        $address = [];
        
        if ($contractor->street) {
            $address[] = $contractor->street;
        }
        
        $cityPart = [];
        if ($contractor->postal_code) {
            $cityPart[] = $contractor->postal_code;
        }
        if ($contractor->city) {
            $cityPart[] = $contractor->city;
        }
        
        if (!empty($cityPart)) {
            $address[] = implode(' ', $cityPart);
        }
        
        if ($contractor->country && $contractor->country !== 'Polska') {
            $address[] = $contractor->country;
        }
        
        return implode(', ', $address);
    }
} 
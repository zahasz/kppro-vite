<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\CompanyProfile;
use App\Models\BudgetCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class SetupSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Rozpoczynam konfigurację systemu...');
        
        // Utwórz podstawowe ustawienia systemowe
        $this->createSystemSettings();
        
        // Utwórz role i uprawnienia
        $this->createRolesAndPermissions();
        
        // Utwórz administratora wraz z profilem firmy
        $this->createAdminUser();
        
        // Utwórz podstawowe kategorie i statusy
        $this->createBasicCategories();
        
        // Utwórz podstawowe kategorie budżetowe
        $this->createBudgetCategories();
        
        // Utwórz podstawowe produkty
        $this->createBasicProducts();
        
        $this->command->info('Konfiguracja systemu zakończona pomyślnie!');
    }

    /**
     * Tworzy podstawowe ustawienia systemowe
     */
    protected function createSystemSettings(): void
    {
        $this->command->info('Tworzenie podstawowych ustawień systemowych...');
        
        // Sprawdzenie czy tabela istnieje
        if (!Schema::hasTable('settings')) {
            $this->command->info('Tabela settings nie istnieje. Pomijam tworzenie ustawień.');
            return;
        }
        
        // Dodanie podstawowych ustawień
        $settings = [
            'system_name' => 'KPPRO System',
            'system_version' => '1.0.0',
        ];
        
        foreach ($settings as $key => $value) {
            // Sprawdź czy ustawienie już istnieje
            $exists = DB::table('settings')->where('key', $key)->exists();
            
            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        
        $this->command->info('Podstawowe ustawienia systemowe zostały utworzone.');
    }

    /**
     * Tworzy wszystkie role i uprawnienia w systemie
     */
    protected function createRolesAndPermissions(): void
    {
        $this->command->info('Tworzenie ról i uprawnień...');
        
        // Czyszczenie cache'u
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tworzenie uprawnień
        $permissions = [
            // Użytkownicy
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Role
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Ustawienia
            'settings.view',
            'settings.edit',

            // Magazyn - Materiały
            'warehouse.materials.view',
            'warehouse.materials.create',
            'warehouse.materials.edit',
            'warehouse.materials.delete',
            
            // Magazyn - Sprzęt
            'warehouse.equipment.view',
            'warehouse.equipment.create',
            'warehouse.equipment.edit',
            'warehouse.equipment.delete',
            
            // Magazyn - Narzędzia
            'warehouse.tools.view',
            'warehouse.tools.create',
            'warehouse.tools.edit',
            'warehouse.tools.delete',
            
            // Magazyn - Garaż
            'warehouse.garage.view',
            'warehouse.garage.create',
            'warehouse.garage.edit',
            'warehouse.garage.delete',

            // Finanse
            'finances.view',
            'finances.create',
            'finances.edit',
            'finances.delete',
            'finances.approve', // Zatwierdzanie transakcji
            'finances.reports', // Generowanie raportów

            // Budżet
            'budget.view',
            'budget.create',
            'budget.edit',
            'budget.delete',
            'budget.approve', // Zatwierdzanie budżetu

            // Faktury
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.approve', // Zatwierdzanie faktur

            // Kontrahenci
            'contractors.view',
            'contractors.create',
            'contractors.edit',
            'contractors.delete',

            // Umowy
            'contracts.view',
            'contracts.create',
            'contracts.edit',
            'contracts.delete',
            'contracts.approve', // Zatwierdzanie umów

            // Kosztorysy
            'estimates.view',
            'estimates.create',
            'estimates.edit',
            'estimates.delete',
            'estimates.approve', // Zatwierdzanie kosztorysów

            // Zadania
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
            'tasks.assign', // Przydzielanie zadań
            
            // Produkty
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
        ];

        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[] = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Tworzenie ról
        $roles = [
            'admin' => $permissions, // Administrator ma wszystkie uprawnienia

            'manager' => [ // Kierownik
                'users.view',
                'users.create',
                'users.edit',
                'roles.view',
                'settings.view',
                'warehouse.materials.view',
                'warehouse.materials.create',
                'warehouse.materials.edit',
                'warehouse.equipment.view',
                'warehouse.equipment.create',
                'warehouse.equipment.edit',
                'warehouse.tools.view',
                'warehouse.tools.create',
                'warehouse.tools.edit',
                'warehouse.garage.view',
                'warehouse.garage.create',
                'warehouse.garage.edit',
                'finances.view',
                'finances.create',
                'finances.edit',
                'finances.reports',
                'budget.view',
                'budget.create',
                'budget.edit',
                'invoices.view',
                'invoices.create',
                'invoices.edit',
                'contractors.view',
                'contractors.create',
                'contractors.edit',
                'contracts.view',
                'contracts.create',
                'contracts.edit',
                'estimates.view',
                'estimates.create',
                'estimates.edit',
                'tasks.view',
                'tasks.create',
                'tasks.edit',
                'tasks.assign',
                'products.view',
                'products.create',
                'products.edit',
            ],

            'accountant' => [ // Księgowy
                'finances.view',
                'finances.create',
                'finances.edit',
                'finances.approve',
                'finances.reports',
                'budget.view',
                'budget.create',
                'budget.edit',
                'budget.approve',
                'invoices.view',
                'invoices.create',
                'invoices.edit',
                'invoices.approve',
                'contractors.view',
                'contractors.create',
                'contractors.edit',
                'products.view',
            ],

            'warehouse_keeper' => [ // Magazynier
                'warehouse.materials.view',
                'warehouse.materials.create',
                'warehouse.materials.edit',
                'warehouse.equipment.view',
                'warehouse.equipment.create',
                'warehouse.equipment.edit',
                'warehouse.tools.view',
                'warehouse.tools.create',
                'warehouse.tools.edit',
                'warehouse.garage.view',
                'warehouse.garage.create',
                'warehouse.garage.edit',
                'products.view',
                'products.create',
                'products.edit',
            ],

            'employee' => [ // Pracownik
                'warehouse.materials.view',
                'warehouse.equipment.view',
                'warehouse.tools.view',
                'warehouse.garage.view',
                'tasks.view',
                'tasks.edit',
                'products.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
            $role->syncPermissions($rolePermissions);
        }
        
        $this->command->info('Role i uprawnienia zostały utworzone.');
    }

    /**
     * Tworzy użytkownika administratora oraz profil jego firmy
     */
    protected function createAdminUser(): void
    {
        $this->command->info('Tworzenie użytkownika administratora...');
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@kppro.pl'],
            [
                'name' => 'Administrator',
                'first_name' => 'Admin',
                'last_name' => 'Istrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'pl',
                'timezone' => 'Europe/Warsaw'
            ]
        );

        // Przypisanie roli admin do użytkownika
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Tworzenie lub aktualizacja profilu firmy
        $companyProfile = CompanyProfile::firstOrNew(['user_id' => $admin->id]);
        $companyProfile->fill([
            'company_name' => 'KPPRO Sp. z o.o.',
            'legal_form' => 'limited_liability',
            'tax_number' => '1234567890',
            'regon' => '123456789',
            'krs' => '0000123456',
            'street' => 'ul. Przykładowa 123',
            'city' => 'Warszawa',
            'state' => 'mazowieckie',
            'country' => 'Polska',
            'postal_code' => '00-001',
            'phone' => '+48 123 456 789',
            'phone_additional' => '+48 987 654 321',
            'email' => 'biuro@kppro.pl',
            'email_additional' => 'kontakt@kppro.pl',
            'website' => 'https://kppro.pl',
            'bank_name' => 'Bank Przykładowy S.A.',
            'bank_account' => 'PL 12 1234 5678 9012 3456 7890 1234',
            'swift' => 'PKPLPLPW',
            'notes' => 'Przykładowe notatki o firmie',
            // Dane dotyczące faktur VAT
            'invoice_prefix' => '',
            'invoice_numbering_pattern' => 'FV/{YEAR}/{MONTH}/{NUMBER}',
            'invoice_next_number' => 1,
            'invoice_payment_days' => 14,
            'default_payment_method' => 'przelew',
            'default_currency' => 'PLN',
            'invoice_notes' => 'Dziękujemy za skorzystanie z naszych usług.',
            'invoice_footer' => 'Faktura wystawiona elektronicznie, nie wymaga podpisu.'
        ]);
        $admin->companyProfile()->save($companyProfile);
        
        // Tworzenie konta testowego
        $testUser = User::firstOrCreate(
            ['email' => 'test@kppro.pl'],
            [
                'name' => 'Konto Testowe',
                'first_name' => 'Konto',
                'last_name' => 'Testowe',
                'password' => Hash::make('Test123!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'pl',
                'timezone' => 'Europe/Warsaw'
            ]
        );

        if (!$testUser->hasRole('manager')) {
            $testUser->assignRole('manager');
        }

        $this->command->info('Administrator i konto testowe zostali utworzeni pomyślnie.');
    }
    
    /**
     * Tworzy podstawowe kategorie i statusy
     */
    protected function createBasicCategories(): void
    {
        $this->command->info('Tworzenie podstawowych kategorii i statusów...');
        
        // Sprawdzenie czy tabela kategorii istnieje
        if (Schema::hasTable('categories')) {
            // Dodanie przykładowych kategorii
            $categories = [
                'Przychody' => 'income',
                'Wydatki' => 'expense',
                'Inwestycje' => 'investment',
                'Oszczędności' => 'savings',
            ];

            foreach ($categories as $name => $slug) {
                // Sprawdź czy kategoria już istnieje
                $exists = DB::table('categories')
                    ->where('slug', $slug)
                    ->exists();
                    
                if (!$exists) {
                    DB::table('categories')->insert([
                        'name' => $name,
                        'slug' => $slug,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
            
            $this->command->info('Podstawowe kategorie zostały utworzone.');
        } else {
            $this->command->info('Tabela categories nie istnieje. Pomijam tworzenie kategorii.');
        }

        // Sprawdzenie czy tabela statusów istnieje
        if (Schema::hasTable('statuses')) {
            // Dodanie przykładowych statusów
            $statuses = [
                'Nowy' => 'new',
                'W trakcie' => 'in_progress',
                'Zakończony' => 'completed',
                'Anulowany' => 'cancelled',
            ];

            foreach ($statuses as $name => $slug) {
                // Sprawdź czy status już istnieje
                $exists = DB::table('statuses')
                    ->where('slug', $slug)
                    ->exists();
                    
                if (!$exists) {
                    DB::table('statuses')->insert([
                        'name' => $name,
                        'slug' => $slug,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
            
            $this->command->info('Podstawowe statusy zostały utworzone.');
        } else {
            $this->command->info('Tabela statuses nie istnieje. Pomijam tworzenie statusów.');
        }
    }

    /**
     * Tworzy podstawowe kategorie budżetowe
     */
    protected function createBudgetCategories(): void
    {
        $this->command->info('Tworzenie podstawowych kategorii budżetowych...');
        
        // Sprawdzenie czy tabela istnieje
        if (!Schema::hasTable('budget_categories')) {
            $this->command->info('Tabela budget_categories nie istnieje. Pomijam tworzenie kategorii budżetowych.');
            return;
        }
        
        // Sprawdzenie czy jest model
        if (!class_exists('App\Models\BudgetCategory')) {
            $this->command->info('Model BudgetCategory nie istnieje. Pomijam tworzenie kategorii budżetowych.');
            return;
        }
        
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

            // Inwestycje
            [
                'name' => 'Lokata terminowa',
                'type' => 'investments',
                'amount' => 30000,
                'planned_amount' => 30000,
                'description' => 'Lokata 6-miesięczna',
            ],
        ];

        foreach ($categories as $category) {
            if (class_exists('App\Models\BudgetCategory')) {
                BudgetCategory::firstOrCreate(
                    ['name' => $category['name'], 'type' => $category['type']],
                    $category
                );
            } else {
                DB::table('budget_categories')->insert(array_merge($category, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]));
            }
        }
        
        $this->command->info('Podstawowe kategorie budżetowe zostały utworzone.');
    }
    
    /**
     * Tworzy podstawowe produkty
     */
    protected function createBasicProducts(): void
    {
        $this->command->info('Tworzenie podstawowych produktów...');
        
        // Sprawdzenie czy tabela istnieje
        if (!Schema::hasTable('products')) {
            $this->command->info('Tabela products nie istnieje. Pomijam tworzenie produktów.');
            return;
        }
        
        // Pobierz użytkownika administratora
        $admin = User::where('email', 'admin@kppro.pl')->first();
        
        if (!$admin) {
            $this->command->info('Nie znaleziono administratora. Pomijam tworzenie produktów.');
            return;
        }
        
        $products = [
            [
                'name' => 'Usługa konsultacyjna',
                'description' => 'Usługa doradcza w zakresie strategii biznesowej',
                'unit' => 'godz.',
                'unit_price' => 150.00,
                'tax_rate' => 23.00,
                'status' => 'active'
            ],
            [
                'name' => 'Wdrożenie systemu CRM',
                'description' => 'Kompleksowe wdrożenie systemu zarządzania relacjami z klientami',
                'unit' => 'usł.',
                'unit_price' => 5000.00,
                'tax_rate' => 23.00,
                'status' => 'active'
            ],
            [
                'name' => 'Szkolenie z obsługi oprogramowania',
                'description' => 'Szkolenie dla pracowników z obsługi oprogramowania',
                'unit' => 'dzień',
                'unit_price' => 1200.00,
                'tax_rate' => 23.00,
                'status' => 'active'
            ],
            [
                'name' => 'Hosting i utrzymanie',
                'description' => 'Miesięczny abonament za hosting i utrzymanie systemu',
                'unit' => 'mies.',
                'unit_price' => 250.00,
                'tax_rate' => 23.00,
                'status' => 'active'
            ],
        ];
        
        foreach ($products as $product) {
            // Sprawdź czy produkt już istnieje
            $exists = DB::table('products')
                ->where('name', $product['name'])
                ->where('user_id', $admin->id)
                ->exists();
                
            if (!$exists) {
                DB::table('products')->insert(array_merge($product, [
                    'user_id' => $admin->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]));
            }
        }
        
        $this->command->info('Podstawowe produkty zostały utworzone.');
    }
}

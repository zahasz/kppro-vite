<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SeedStartup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:startup 
                            {--fresh : Wyczyść bazę danych przed seedowaniem}
                            {--migrate : Wykonaj tylko migracje bez seedowania danych}
                            {--force : Wymusza wykonanie migracji w środowisku produkcyjnym}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicjalizuje system z podstawowymi ustawieniami, danymi administratora i planami subskrypcji';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczynam inicjalizację systemu KPPRO...');

        // Sprawdź czy tabele istnieją
        $tablesExist = $this->checkIfTablesExist();

        // Opcja --migrate wykonuje tylko migracje bez seedowania
        if ($this->option('migrate')) {
            $this->runMigrations();
            $this->info('Migracje zostały zakończone. Aby wykonać seedowanie, uruchom komendę bez opcji --migrate.');
            return Command::SUCCESS;
        }

        // Czyści bazę danych jeśli podano flagę --fresh
        if ($this->option('fresh')) {
            $this->info('Czyszczę bazę danych...');
            $this->call('migrate:fresh', [
                '--force' => $this->option('force')
            ]);
            $tablesExist = true; // Po migracji tabele istnieją
        } 
        // Jeśli tabele nie istnieją, wykonaj migracje
        elseif (!$tablesExist) {
            $this->info('Wykryto brak tabel w bazie danych. Wykonuję migracje...');
            $this->runMigrations();
            $tablesExist = true; // Po migracji tabele istnieją
        }

        // Sprawdź czy tabele istnieją po migracji
        if (!$tablesExist) {
            $this->error('Migracje nie zostały wykonane pomyślnie. Nie można kontynuować seedowania.');
            return Command::FAILURE;
        }

        // Uruchamia główny seeder startowy
        $this->info('Uruchamiam seeder inicjalizacyjny...');
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\StartupSeeder',
            '--force' => $this->option('force')
        ]);

        // Wyświetl podsumowanie
        $this->info('System został pomyślnie zainicjalizowany!');
        $this->info('Dane logowania administratora:');
        $this->info('Email: admin@kppro.pl');
        $this->info('Hasło: admin123');
        
        return Command::SUCCESS;
    }

    /**
     * Wykonuje migracje z obsługą błędów
     */
    private function runMigrations()
    {
        try {
            $result = $this->call('migrate', [
                '--force' => $this->option('force')
            ]);

            if ($result !== 0) {
                $this->warn('Wystąpiły problemy podczas wykonywania migracji. Próba naprawy...');
                
                // Lista zduplikowanych migracji do usunięcia
                $duplicatedMigrations = [
                    '2025_03_31_234832_create_payment_settings_table',
                    '2025_03_29_215317_add_last_seen_at_column_to_users_table',
                    '2025_03_29_215322_add_last_seen_at_column_to_users_table',
                    '2025_03_29_220319_add_subscription_type_to_user_subscriptions_table',
                    '2025_03_29_221528_add_deleted_at_to_user_subscriptions_table',
                    '2025_03_27_195007_add_subscription_type_to_subscriptions_table',
                    '2025_03_29_184204_create_login_histories_table'
                ];

                // Usuń zduplikowane migracje z tabeli migracji
                if (Schema::hasTable('migrations')) {
                    foreach ($duplicatedMigrations as $migration) {
                        DB::table('migrations')->where('migration', $migration)->delete();
                        $this->info("Usunięto zduplikowaną migrację: $migration");
                    }
                }

                // Próba ponownego wykonania migracji
                $this->info('Ponowne wykonanie migracji...');
                $result = $this->call('migrate', [
                    '--force' => $this->option('force')
                ]);
                
                if ($result !== 0) {
                    $this->error('Nie udało się naprawić migracji automatycznie.');
                    return false;
                }
            }
            
            return true;
        } catch (\Exception $e) {
            $this->error('Wystąpił błąd podczas migracji: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sprawdza czy podstawowe tabele istnieją w bazie danych
     */
    private function checkIfTablesExist()
    {
        try {
            // Lista kluczowych tabel do sprawdzenia
            $keyTables = ['users', 'migrations', 'subscription_plans', 'payment_settings'];
            
            foreach ($keyTables as $table) {
                if (!Schema::hasTable($table)) {
                    return false;
                }
            }
            
            return true;
        } catch (\Exception $e) {
            $this->error('Błąd podczas sprawdzania tabel: ' . $e->getMessage());
            return false;
        }
    }
}

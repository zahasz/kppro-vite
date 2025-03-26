<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin user...');

        try {
            // Tworzenie użytkownika admin
            $admin = User::create([
                'name' => 'Administrator',
                'first_name' => 'Admin',
                'last_name' => 'Istrator',
                'email' => 'admin@kppro.pl',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'pl',
                'timezone' => 'Europe/Warsaw'
            ]);

            // Upewniamy się, że rola admin istnieje
            $adminRole = Role::firstOrCreate(['name' => 'admin']);

            // Przypisanie roli admin
            $admin->assignRole('admin');

            $this->info('Admin user created successfully!');
            $this->info('Email: admin@kppro.pl');
            $this->info('Password: admin123');
        } catch (\Exception $e) {
            $this->error('Error creating admin user:');
            $this->error($e->getMessage());
        }
    }
}

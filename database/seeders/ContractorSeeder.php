<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contractor;
use App\Models\User;

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@kppro.pl')->first();

        if (!$admin) {
            return;
        }

        Contractor::create([
            'user_id' => $admin->id,
            'company_name' => 'Firma Budowlana "Budex" Sp. z o.o.',
            'nip' => '1234567890',
            'email' => 'kontakt@budex.pl',
            'phone' => '+48 123 456 789',
            'status' => 'active'
        ]);

        $contractors = [
            [
                'company_name' => 'IT Solutions Pro Sp. z o.o.',
                'nip' => '9876543210',
                'email' => 'biuro@itsolutions.pl',
                'phone' => '+48 987 654 321',
                'status' => 'active'
            ],
            [
                'company_name' => 'Hurtownia Spożywcza "Smak" S.A.',
                'nip' => '5555666677',
                'email' => 'zamowienia@smak.pl',
                'phone' => '+48 555 666 777',
                'status' => 'inactive'
            ],
            [
                'company_name' => 'Transport i Logistyka "Trans-Log"',
                'nip' => '1112223334',
                'email' => 'biuro@trans-log.pl',
                'phone' => '+48 111 222 333',
                'status' => 'active'
            ],
            [
                'company_name' => 'Biuro Rachunkowe "Bilans"',
                'nip' => '4445556667',
                'email' => 'ksiegowosc@bilans.pl',
                'phone' => '+48 444 555 666',
                'status' => 'blocked'
            ]
        ];

        foreach ($contractors as $contractor) {
            $contractor['user_id'] = $admin->id;
            Contractor::create($contractor);
        }
    }
} 
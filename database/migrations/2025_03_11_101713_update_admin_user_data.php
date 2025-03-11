<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Aktualizacja danych administratora
        DB::table('users')
            ->where('email', 'admin@kppro.pl')
            ->update([
                'username' => 'Administrator',
                'phone' => '+48 123 456 789',
                'position' => 'Administrator systemu'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert these changes
    }
};

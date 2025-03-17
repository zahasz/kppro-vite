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
        // Aktualizacja nazwy użytkownika administratora
        DB::table('users')
            ->where('email', 'admin@kppro.pl')
            ->update([
                'username' => 'admin',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Przywrócenie pustej wartości
        DB::table('users')
            ->where('email', 'admin@kppro.pl')
            ->update([
                'username' => null,
            ]);
    }
};

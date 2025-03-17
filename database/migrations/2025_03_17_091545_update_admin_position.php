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
        // Sprawdzenie, czy kolumna position istnieje
        if (!Schema::hasColumn('users', 'position')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('position')->nullable()->after('last_name');
            });
        }

        // Aktualizacja stanowiska administratora
        DB::table('users')
            ->where('email', 'admin@kppro.pl')
            ->update([
                'position' => 'Administrator systemu',
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
                'position' => null,
            ]);
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            // Sprawdzamy, czy kolumny już istnieją
            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language')->nullable()->after('position');
            }
            
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->nullable()->after('language');
            }
            
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('timezone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['language', 'timezone', 'preferences'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

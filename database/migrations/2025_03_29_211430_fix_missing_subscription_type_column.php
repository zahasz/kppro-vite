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
        // Sprawdź czy tabela istnieje
        if (Schema::hasTable('subscriptions')) {
            // Sprawdź czy kolumna nie istnieje
            if (!Schema::hasColumn('subscriptions', 'subscription_type')) {
                // Dodaj kolumnę bezpośrednio przez SQL, aby uniknąć problemów z escapowaniem
                DB::statement('ALTER TABLE subscriptions ADD COLUMN subscription_type VARCHAR(255) DEFAULT "manual" AFTER `status`');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('subscriptions')) {
            if (Schema::hasColumn('subscriptions', 'subscription_type')) {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->dropColumn('subscription_type');
                });
            }
        }
    }
}; 
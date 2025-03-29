<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            // Dodaj kolumnę subscription_type jeśli nie istnieje
            if (!Schema::hasColumn('subscriptions', 'subscription_type')) {
                Schema::table('subscriptions', function (Blueprint $table) {
                    $table->string('subscription_type')->default('manual')->after('status');
                });
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

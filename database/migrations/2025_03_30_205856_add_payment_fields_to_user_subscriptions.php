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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Dodaj payment_method jeśli nie istnieje
            if (!Schema::hasColumn('user_subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            
            // Dodaj payment_details jeśli nie istnieje
            if (!Schema::hasColumn('user_subscriptions', 'payment_details')) {
                $table->text('payment_details')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('user_subscriptions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('user_subscriptions', 'payment_details')) {
                $table->dropColumn('payment_details');
            }
        });
    }
}; 
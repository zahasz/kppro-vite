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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('auto_retry_failed_payments')->default(true);
            $table->integer('payment_retry_attempts')->default(3);
            $table->integer('payment_retry_interval')->default(3);
            $table->integer('grace_period_days')->default(3);
            $table->string('default_payment_gateway')->nullable();
            $table->boolean('renewal_notifications')->default(true);
            $table->integer('renewal_notification_days')->default(7);
            $table->boolean('auto_cancel_after_failed_payments')->default(true);
            $table->integer('renewal_charge_days_before')->default(3);
            $table->boolean('enable_accounting_integration')->default(false);
            $table->string('accounting_api_url')->nullable();
            $table->string('accounting_api_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
}; 
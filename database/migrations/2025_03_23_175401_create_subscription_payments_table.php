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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_subscription_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PLN');
            $table->enum('status', ['paid', 'pending', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['card', 'paypal', 'bank_transfer'])->nullable();
            $table->string('payment_details')->nullable();
            $table->string('authorization_id')->nullable();
            $table->string('gateway')->nullable();
            $table->string('invoice_number')->nullable();
            $table->datetime('invoice_date')->nullable();
            $table->string('refund_id')->nullable();
            $table->datetime('refund_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};

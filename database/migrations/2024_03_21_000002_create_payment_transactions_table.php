<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('user_subscriptions')->onDelete('cascade');
            $table->string('gateway_code');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status');
            $table->string('payment_method');
            $table->text('error_message')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('gateway_code')
                ->references('code')
                ->on('payment_gateways')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
}; 
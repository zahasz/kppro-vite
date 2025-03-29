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
        Schema::create('subscription_plan_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('subscription_permission_id');
            $table->string('value')->nullable();
            $table->timestamps();

            // Unikalne połączenie plan-uprawnienie
            $table->unique(['subscription_plan_id', 'subscription_permission_id'], 'plan_permission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_permission');
    }
};

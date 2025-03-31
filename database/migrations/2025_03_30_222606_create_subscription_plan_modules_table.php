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
        Schema::create('subscription_plan_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('module_id');
            $table->json('limitations')->nullable()->comment('Limity dla danego modułu w planie, np. maksymalna liczba dokumentów');
            $table->timestamps();
            
            $table->foreign('subscription_plan_id')
                ->references('id')
                ->on('subscription_plans')
                ->onDelete('cascade');
                
            $table->foreign('module_id')
                ->references('id')
                ->on('modules')
                ->onDelete('cascade');
                
            $table->unique(['subscription_plan_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_modules');
    }
};

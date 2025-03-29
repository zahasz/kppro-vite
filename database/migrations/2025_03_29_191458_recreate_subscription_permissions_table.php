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
        // Najpierw upewnijmy się, że nie istnieją stare tabele
        Schema::dropIfExists('subscription_permission_plan');
        Schema::dropIfExists('subscription_permissions');
        
        // Tworzymy tabelę uprawnień subskrypcji
        Schema::create('subscription_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('feature_flag')->nullable();
            $table->timestamps();
        });

        // Tabela powiązań między uprawnieniami a planami subskrypcji
        Schema::create('subscription_permission_plan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('plan_id');
            $table->timestamps();

            $table->foreign('permission_id')
                  ->references('id')
                  ->on('subscription_permissions')
                  ->onDelete('cascade');

            // Sprawdzamy, czy tabela subscription_plans istnieje
            if (Schema::hasTable('subscription_plans')) {
                $table->foreign('plan_id')
                      ->references('id')
                      ->on('subscription_plans')
                      ->onDelete('cascade');
            }

            $table->unique(['permission_id', 'plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_permission_plan');
        Schema::dropIfExists('subscription_permissions');
    }
};

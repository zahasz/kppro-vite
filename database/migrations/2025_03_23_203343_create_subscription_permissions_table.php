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
        // Usuń istniejącą tabelę jeśli istnieje
        if (Schema::hasTable('subscription_permissions')) {
            Schema::drop('subscription_permissions');
        }
        
        if (Schema::hasTable('subscription_permission_plan')) {
            Schema::drop('subscription_permission_plan');
        }
        
        // Utwórz tabelę na nowo
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

            $table->foreign('plan_id')
                  ->references('id')
                  ->on('subscription_plans')
                  ->onDelete('cascade');

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

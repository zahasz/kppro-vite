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
        if (Schema::hasTable('subscription_permission_plan')) {
            // Jeśli tabela istnieje, modyfikujemy ją
            Schema::table('subscription_permission_plan', function (Blueprint $table) {
                // Usuwamy stare klucze obce
                if (Schema::hasColumn('subscription_permission_plan', 'permission_id')) {
                    // Usuń stare klucze obce
                    $table->dropForeign(['permission_id']);
                    $table->dropForeign(['plan_id']);
                    
                    // Zmień nazwy kolumn
                    $table->renameColumn('permission_id', 'subscription_permission_id');
                    $table->renameColumn('plan_id', 'subscription_plan_id');
                    
                    // Dodaj nowe klucze obce
                    $table->foreign('subscription_permission_id')
                          ->references('id')
                          ->on('subscription_permissions')
                          ->onDelete('cascade');
                          
                    $table->foreign('subscription_plan_id')
                          ->references('id')
                          ->on('subscription_plans')
                          ->onDelete('cascade');
                }
                
                // Dodaj kolumnę value jeśli nie istnieje
                if (!Schema::hasColumn('subscription_permission_plan', 'value')) {
                    $table->string('value')->nullable();
                }
            });
        } else {
            // Jeśli tabela nie istnieje, tworzymy ją
            Schema::create('subscription_permission_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subscription_permission_id')->constrained()->onDelete('cascade');
                $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
                $table->string('value')->nullable(); // Opcjonalna wartość dla uprawnień z limitami
                $table->timestamps();
                
                // Unikalny klucz dla kombinacji uprawnień i planów
                $table->unique(['subscription_permission_id', 'subscription_plan_id'], 'perm_plan_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nie usuwamy tabeli, tylko wycofujemy zmiany
        if (Schema::hasTable('subscription_permission_plan')) {
            Schema::table('subscription_permission_plan', function (Blueprint $table) {
                if (Schema::hasColumn('subscription_permission_plan', 'value')) {
                    $table->dropColumn('value');
                }
                
                if (Schema::hasColumn('subscription_permission_plan', 'subscription_permission_id')) {
                    // Usuń nowe klucze obce
                    $table->dropForeign(['subscription_permission_id']);
                    $table->dropForeign(['subscription_plan_id']);
                    
                    // Zmień nazwy kolumn z powrotem
                    $table->renameColumn('subscription_permission_id', 'permission_id');
                    $table->renameColumn('subscription_plan_id', 'plan_id');
                    
                    // Dodaj stare klucze obce
                    $table->foreign('permission_id')
                          ->references('id')
                          ->on('subscription_permissions')
                          ->onDelete('cascade');
                          
                    $table->foreign('plan_id')
                          ->references('id')
                          ->on('subscription_plans')
                          ->onDelete('cascade');
                }
            });
        }
    }
}; 
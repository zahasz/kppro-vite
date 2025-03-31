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
            // Sprawdź czy brakuje kolumn w tabeli user_subscriptions
            if (!Schema::hasColumn('user_subscriptions', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            // Usuń kolumnę plan_id jeśli istnieje (zamieniona na subscription_plan_id)
            if (Schema::hasColumn('user_subscriptions', 'plan_id')) {
                // Sprawdź czy istnieje klucz obcy przed jego usunięciem
                $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table->getTable());
                $hasForeignKey = false;
                
                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('plan_id', $foreignKey->getLocalColumns())) {
                        $hasForeignKey = true;
                        $table->dropForeign($foreignKey->getName());
                        break;
                    }
                }
                
                $table->dropColumn('plan_id');
            }
            
            // Dodaj subscription_plan_id, jeśli nie istnieje
            if (!Schema::hasColumn('user_subscriptions', 'subscription_plan_id')) {
                $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            }
            
            // Dodaj pozostałe kolumny, jeśli nie istnieją
            if (!Schema::hasColumn('user_subscriptions', 'status')) {
                $table->string('status')->default('active');
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'subscription_type')) {
                $table->string('subscription_type')->default('manual');
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'renewal_status')) {
                $table->string('renewal_status')->nullable();
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'start_date')) {
                $table->timestamp('start_date')->nullable();
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'end_date')) {
                $table->timestamp('end_date')->nullable();
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'next_billing_date')) {
                $table->timestamp('next_billing_date')->nullable();
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'auto_renew')) {
                $table->boolean('auto_renew')->default(false);
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'last_invoice_id')) {
                $table->unsignedBigInteger('last_invoice_id')->nullable();
            }
            
            if (!Schema::hasColumn('user_subscriptions', 'last_invoice_number')) {
                $table->string('last_invoice_number')->nullable();
            }
            
            // Dodaj indeksy dla optymalizacji zapytań
            $table->index('subscription_plan_id');
            $table->index('status');
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nie implementujemy down() ponieważ usunięcie tych kolumn mogłoby spowodować utratę danych
    }
}; 
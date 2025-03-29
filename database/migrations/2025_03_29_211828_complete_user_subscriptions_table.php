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
            // Sprawdź czy tabela jest pusta (ma tylko id i timestamp) i uzupełnij ją
            if (Schema::hasColumns('user_subscriptions', ['id', 'created_at', 'updated_at']) && 
                count(Schema::getColumnListing('user_subscriptions')) <= 3) {
                
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
                $table->string('status')->default('active');
                $table->string('subscription_type')->default('manual');
                $table->string('renewal_status')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->timestamp('next_billing_date')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->unsignedBigInteger('last_invoice_id')->nullable();
                $table->string('last_invoice_number')->nullable();
                $table->string('payment_method')->nullable();
                $table->text('payment_details')->nullable();
                $table->text('admin_notes')->nullable();
                $table->boolean('auto_renew')->default(false);
                $table->softDeletes();

                // Dodaj indeksy dla optymalizacji zapytań
                $table->index('status');
                $table->index('subscription_type');
                $table->index('start_date');
                $table->index('end_date');
                $table->index('next_billing_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nie implementujemy down() ponieważ tabela mogła istnieć wcześniej
        // i usunięcie tych kolumn mogłoby spowodować utratę danych
    }
};

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
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('auto_generate')->default(true);
            $table->integer('generation_day')->default(1);
            $table->string('invoice_prefix')->nullable();
            $table->string('invoice_suffix')->nullable();
            $table->boolean('reset_numbering')->default(false);
            $table->integer('payment_days')->default(14);
            $table->string('default_currency', 3)->default('PLN');
            $table->decimal('default_tax_rate', 5, 2)->default(23.00);
            $table->string('vat_number')->nullable();
            $table->text('invoice_notes')->nullable();
            $table->boolean('email_notifications')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_settings');
    }
};

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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('legal_form')->nullable();
            $table->string('tax_number');
            $table->string('regon')->nullable();
            $table->string('krs')->nullable();
            $table->string('street');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country');
            $table->string('postal_code');
            $table->string('phone');
            $table->string('phone_additional')->nullable();
            $table->string('email');
            $table->string('email_additional')->nullable();
            $table->string('website')->nullable();
            $table->string('bank_name');
            $table->string('bank_account');
            $table->string('swift')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('notes')->nullable();
            
            // Dodatkowe pola dotyczące faktur VAT
            $table->string('invoice_prefix')->nullable();
            $table->string('invoice_numbering_pattern')->nullable()->default('FV/{YEAR}/{MONTH}/{NUMBER}');
            $table->integer('invoice_next_number')->nullable()->default(1);
            $table->integer('invoice_payment_days')->nullable()->default(14);
            $table->string('default_payment_method')->nullable()->default('przelew');
            $table->string('default_currency')->nullable()->default('PLN');
            $table->text('invoice_notes')->nullable();
            $table->text('invoice_footer')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};

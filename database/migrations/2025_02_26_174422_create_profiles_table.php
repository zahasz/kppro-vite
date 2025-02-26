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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['individual', 'company'])->default('individual');
            
            // Podstawowe dane
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('tax_number')->nullable(); // NIP
            $table->string('regon')->nullable();
            $table->string('krs')->nullable();
            
            // Dane kontaktowe
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            
            // Adres
            $table->string('street')->nullable();
            $table->string('street_number')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Polska');
            
            // Dane bankowe
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            
            // Ustawienia
            $table->string('default_payment_method')->nullable();
            $table->integer('default_payment_deadline_days')->default(14);
            $table->string('invoice_notes')->nullable();
            $table->string('logo_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

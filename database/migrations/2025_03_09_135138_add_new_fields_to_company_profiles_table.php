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
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('legal_form')->nullable()->after('company_name');
            $table->string('krs')->nullable()->after('regon');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('phone_additional')->nullable()->after('phone');
            $table->string('email_additional')->nullable()->after('email');
            $table->string('swift')->nullable()->after('bank_account');
            $table->text('notes')->nullable()->after('logo_path');
            
            // Dodatkowe pola dotyczące faktur VAT
            $table->string('invoice_prefix')->nullable()->after('notes');
            $table->string('invoice_numbering_pattern')->nullable()->after('invoice_prefix')->default('FV/{YEAR}/{MONTH}/{NUMBER}');
            $table->integer('invoice_next_number')->nullable()->after('invoice_numbering_pattern')->default(1);
            $table->integer('invoice_payment_days')->nullable()->after('invoice_next_number')->default(14);
            $table->string('default_payment_method')->nullable()->after('invoice_payment_days')->default('przelew');
            $table->string('default_currency')->nullable()->after('default_payment_method')->default('PLN');
            $table->text('invoice_notes')->nullable()->after('default_currency');
            $table->text('invoice_footer')->nullable()->after('invoice_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'legal_form',
                'krs',
                'state',
                'country',
                'phone_additional',
                'email_additional',
                'swift',
                'notes',
                'invoice_prefix',
                'invoice_numbering_pattern',
                'invoice_next_number',
                'invoice_payment_days',
                'default_payment_method',
                'default_currency',
                'invoice_notes',
                'invoice_footer'
            ]);
        });
    }
};

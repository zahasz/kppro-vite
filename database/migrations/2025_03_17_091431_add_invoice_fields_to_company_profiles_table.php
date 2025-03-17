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
            // Sprawdzamy, czy kolumny już istnieją
            if (!Schema::hasColumn('company_profiles', 'invoice_prefix')) {
                $table->string('invoice_prefix')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('company_profiles', 'invoice_numbering_pattern')) {
                $table->string('invoice_numbering_pattern')->nullable()->after('invoice_prefix')->default('FV/{YEAR}/{MONTH}/{NUMBER}');
            }
            if (!Schema::hasColumn('company_profiles', 'invoice_next_number')) {
                $table->integer('invoice_next_number')->nullable()->after('invoice_numbering_pattern')->default(1);
            }
            if (!Schema::hasColumn('company_profiles', 'invoice_payment_days')) {
                $table->integer('invoice_payment_days')->nullable()->after('invoice_next_number')->default(14);
            }
            if (!Schema::hasColumn('company_profiles', 'default_payment_method')) {
                $table->string('default_payment_method')->nullable()->after('invoice_payment_days')->default('przelew');
            }
            if (!Schema::hasColumn('company_profiles', 'default_currency')) {
                $table->string('default_currency')->nullable()->after('default_payment_method')->default('PLN');
            }
            if (!Schema::hasColumn('company_profiles', 'invoice_notes')) {
                $table->text('invoice_notes')->nullable()->after('default_currency');
            }
            if (!Schema::hasColumn('company_profiles', 'invoice_footer')) {
                $table->text('invoice_footer')->nullable()->after('invoice_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn([
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

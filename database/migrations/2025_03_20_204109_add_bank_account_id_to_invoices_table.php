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
        if (!Schema::hasColumn('invoices', 'bank_account_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                // Dodanie kolumny bank_account_id z kluczem obcym do tabeli bank_accounts
                $table->foreignId('bank_account_id')->nullable()->after('payment_method')
                    ->references('id')->on('bank_accounts')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'bank_account_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['bank_account_id']);
                $table->dropColumn('bank_account_id');
            });
        }
    }
};

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
        // Tworzenie tabeli dla kont bankowych
        if (!Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_profile_id')->constrained()->onDelete('cascade');
                $table->string('account_name');
                $table->string('account_number');
                $table->string('bank_name');
                $table->string('swift')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // Modyfikacja tabeli company_profiles - dodanie pola default_bank_account_id
        if (!Schema::hasColumn('company_profiles', 'default_bank_account_id')) {
            Schema::table('company_profiles', function (Blueprint $table) {
                $table->foreignId('default_bank_account_id')->nullable()->after('bank_account')
                    ->references('id')->on('bank_accounts')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Usunięcie kolumny default_bank_account_id z tabeli company_profiles
        if (Schema::hasColumn('company_profiles', 'default_bank_account_id')) {
            Schema::table('company_profiles', function (Blueprint $table) {
                $table->dropForeign(['default_bank_account_id']);
                $table->dropColumn('default_bank_account_id');
            });
        }

        // Usunięcie tabeli bank_accounts
        Schema::dropIfExists('bank_accounts');
    }
};

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
                'notes'
            ]);
        });
    }
};

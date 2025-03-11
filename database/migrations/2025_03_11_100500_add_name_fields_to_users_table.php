<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language')->default('pl')->after('avatar');
            }
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('Europe/Warsaw')->after('language');
            }
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('timezone');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('preferences');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            }
        });

        // Wypełnij pola first_name i last_name na podstawie istniejącego pola name
        DB::table('users')->get()->each(function ($user) {
            $nameParts = explode(' ', $user->name);
            $lastName = array_pop($nameParts);
            $firstName = implode(' ', $nameParts);

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => strtolower(str_replace(' ', '.', $user->name))
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'position',
                'avatar',
                'language',
                'timezone',
                'preferences',
                'is_active',
                'two_factor_enabled',
                'two_factor_secret'
            ]);
        });
    }
};

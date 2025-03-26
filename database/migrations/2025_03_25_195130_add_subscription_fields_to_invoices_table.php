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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('user_id')->constrained('subscriptions');
            $table->boolean('is_renewal')->default(false)->after('subscription_id');
            $table->string('subscription_period')->nullable()->after('is_renewal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
            $table->dropColumn('is_renewal');
            $table->dropColumn('subscription_period');
        });
    }
};

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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('subscription_type')->default('manual')->after('status');
            $table->string('renewal_status')->nullable()->after('subscription_type');
            $table->timestamp('next_payment_date')->nullable()->after('renewal_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('subscription_type');
            $table->dropColumn('renewal_status');
            $table->dropColumn('next_payment_date');
        });
    }
};

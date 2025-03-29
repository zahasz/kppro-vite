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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->timestamp('notified_at')->nullable();
            $table->string('last_notification_type')->nullable();
            $table->integer('renewal_attempts')->default(0);
            $table->timestamp('last_renewal_attempt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('notified_at');
            $table->dropColumn('last_notification_type');
            $table->dropColumn('renewal_attempts');
            $table->dropColumn('last_renewal_attempt');
        });
    }
};

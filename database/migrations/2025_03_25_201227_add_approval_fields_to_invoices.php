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
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->text('payment_note')->nullable()->after('status');
            $table->boolean('auto_generated')->default(false)->after('payment_note');
            $table->boolean('notification_sent')->default(false)->after('auto_generated');
            $table->timestamp('reminder_sent_at')->nullable()->after('notification_sent');
            $table->integer('reminders_count')->default(0)->after('reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'approval_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'payment_note',
                'auto_generated',
                'notification_sent',
                'reminder_sent_at',
                'reminders_count',
            ]);
        });
    }
};

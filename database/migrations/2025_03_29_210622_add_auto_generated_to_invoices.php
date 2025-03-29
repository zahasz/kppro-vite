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
            if (!Schema::hasColumn('invoices', 'auto_generated')) {
                $table->boolean('auto_generated')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('invoices', 'approval_status')) {
                $table->string('approval_status')->nullable()->after('auto_generated');
            }
            
            if (!Schema::hasColumn('invoices', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_status');
            }
            
            if (!Schema::hasColumn('invoices', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
            
            if (!Schema::hasColumn('invoices', 'subscription_id')) {
                $table->unsignedBigInteger('subscription_id')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'auto_generated')) {
                $table->dropColumn('auto_generated');
            }
            
            if (Schema::hasColumn('invoices', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
            
            if (Schema::hasColumn('invoices', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            
            if (Schema::hasColumn('invoices', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            
            if (Schema::hasColumn('invoices', 'subscription_id')) {
                $table->dropColumn('subscription_id');
            }
        });
    }
};

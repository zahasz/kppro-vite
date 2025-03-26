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
            $table->foreignId('renewal_invoice_id')->nullable()->after('invoice_generated')->constrained('invoices');
            $table->boolean('expiration_notified')->default(false)->after('renewal_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['renewal_invoice_id']);
            $table->dropColumn('invoice_generated');
            $table->dropColumn('renewal_invoice_id');
            $table->dropColumn('expiration_notified');
        });
    }
};

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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                // Dodanie pola user_id jeśli nie istnieje
                if (!Schema::hasColumn('invoices', 'user_id')) {
                    $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
                }

                // Dodanie pola dla numeru zamówienia
                if (!Schema::hasColumn('invoices', 'order_number')) {
                    $table->string('order_number')->nullable()->after('number');
                }

                // Dodanie pola dla warunków dostawy
                if (!Schema::hasColumn('invoices', 'delivery_terms')) {
                    $table->string('delivery_terms')->nullable()->after('payment_method');
                }

                // Dodanie pola dla sposobu dostawy
                if (!Schema::hasColumn('invoices', 'delivery_method')) {
                    $table->string('delivery_method')->nullable()->after('delivery_terms');
                }

                // Dodanie pola dla adresu dostawy
                if (!Schema::hasColumn('invoices', 'delivery_address')) {
                    $table->text('delivery_address')->nullable()->after('delivery_method');
                }

                // Dodanie pola dla osoby wystawiającej
                if (!Schema::hasColumn('invoices', 'issued_by')) {
                    $table->string('issued_by')->nullable()->after('notes');
                }

                // Dodanie pola dla osoby odbierającej
                if (!Schema::hasColumn('invoices', 'received_by')) {
                    $table->string('received_by')->nullable()->after('issued_by');
                }

                // Dodanie pola dla numeru faktury korygowanej
                if (!Schema::hasColumn('invoices', 'corrected_invoice_number')) {
                    $table->string('corrected_invoice_number')->nullable()->after('number');
                }

                // Dodanie pola dla powodu korekty
                if (!Schema::hasColumn('invoices', 'correction_reason')) {
                    $table->text('correction_reason')->nullable()->after('corrected_invoice_number');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn([
                    'order_number',
                    'delivery_terms',
                    'delivery_method',
                    'delivery_address',
                    'issued_by',
                    'received_by',
                    'corrected_invoice_number',
                    'correction_reason'
                ]);

                if (Schema::hasColumn('invoices', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};

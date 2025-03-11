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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('number')->unique();
            $table->string('order_number')->nullable();
            $table->string('corrected_invoice_number')->nullable();
            $table->text('correction_reason')->nullable();
            $table->string('contractor_name');
            $table->string('contractor_nip');
            $table->text('contractor_address');
            $table->string('payment_method');
            $table->string('delivery_terms')->nullable();
            $table->string('delivery_method')->nullable();
            $table->text('delivery_address')->nullable();
            $table->date('issue_date');
            $table->date('sale_date');
            $table->date('due_date');
            $table->decimal('net_total', 10, 2);
            $table->decimal('tax_total', 10, 2);
            $table->decimal('gross_total', 10, 2);
            $table->string('currency')->default('PLN');
            $table->text('notes')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('received_by')->nullable();
            $table->enum('status', ['draft', 'issued', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

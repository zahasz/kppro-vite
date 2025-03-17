<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('quantity', 10, 2);
                $table->string('unit')->default('szt.');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('net_price', 10, 2);
                $table->decimal('tax_rate', 5, 2);
                $table->decimal('tax_amount', 10, 2);
                $table->decimal('gross_price', 10, 2);
                $table->integer('position')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
}; 
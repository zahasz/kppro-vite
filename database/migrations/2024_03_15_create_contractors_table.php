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
        if (!Schema::hasTable('contractors')) {
            Schema::create('contractors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('company_name');
                $table->string('nip', 20);
                $table->string('regon', 14)->nullable();
                $table->string('email');
                $table->string('phone', 20);
                $table->string('street')->nullable();
                $table->string('postal_code', 10)->nullable();
                $table->string('city')->nullable();
                $table->string('country')->default('Polska');
                $table->string('bank_name')->nullable();
                $table->string('bank_account_number', 50)->nullable();
                $table->string('swift_code', 11)->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
}; 
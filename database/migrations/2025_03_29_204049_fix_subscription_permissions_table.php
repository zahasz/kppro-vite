<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Próba bezpośredniego wyłączenia kluczy obcych
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Teraz możemy bezpiecznie usunąć tabele
        Schema::dropIfExists('subscription_plan_permission');
        Schema::dropIfExists('subscription_permissions');
        
        // Włączenie z powrotem kluczy obcych
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Tworzymy tabelę uprawnień subskrypcji
        Schema::create('subscription_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('feature_flag')->nullable();
            $table->timestamps();
        });

        // Tworzymy tabelę powiązań
        Schema::create('subscription_plan_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_permission_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->timestamps();

            // Unikalne powiązanie
            $table->unique(['subscription_plan_id', 'subscription_permission_id'], 'plan_permission_unique');
        });

        // Dodajemy podstawowe uprawnienia
        DB::table('subscription_permissions')->insert([
            [
                'name' => 'Liczba użytkowników',
                'code' => 'max_users',
                'category' => 'core',
                'feature_flag' => 'user_management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Liczba faktur',
                'code' => 'max_invoices',
                'category' => 'core',
                'feature_flag' => 'invoicing',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Liczba produktów',
                'code' => 'max_products',
                'category' => 'core',
                'feature_flag' => 'product_management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Liczba klientów',
                'code' => 'max_clients',
                'category' => 'core',
                'feature_flag' => 'client_management',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('subscription_plan_permission');
        Schema::dropIfExists('subscription_permissions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

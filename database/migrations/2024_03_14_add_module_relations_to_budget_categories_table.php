use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('budget_categories', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('salary_id')->nullable()->constrained('salaries')->nullOnDelete();
            $table->timestamp('last_sync_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('budget_categories', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['salary_id']);
            $table->dropColumn(['invoice_id', 'salary_id', 'last_sync_at']);
        });
    }
}; 
namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Income;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractorsController extends Controller
{
    public function index(Request $request)
    {
        // Przygotowanie zapytania bazowego dla przychodów
        $incomeQuery = Income::query();

        // Filtrowanie po roku
        $year = $request->input('year', date('Y'));
        $incomeQuery->whereYear('income_date', $year);

        // Filtrowanie po miesiącu
        if ($request->filled('month')) {
            $incomeQuery->whereMonth('income_date', $request->month);
        }

        // Filtrowanie po statusie
        if ($request->filled('income_status')) {
            $incomeQuery->where('status', $request->income_status);
        }

        // Filtrowanie po typie przychodu
        if ($request->filled('income_type')) {
            $incomeQuery->where('category', $request->income_type);
        }

        // Pobieranie przychodów z filtrami
        $totalIncome = (clone $incomeQuery)->sum('amount');
        $bookedIncome = (clone $incomeQuery)->where('status', 'received')->sum('amount');
        $unbookedIncome = (clone $incomeQuery)->where('status', 'pending')->sum('amount');
        $dueIncome = (clone $incomeQuery)->where('status', 'cancelled')->sum('amount');

        // Pobieranie kontrahentów z paginacją
        $contractors = Contractor::paginate(10);

        return view('contractors.index', compact(
            'contractors',
            'totalIncome',
            'bookedIncome',
            'unbookedIncome',
            'dueIncome'
        ));
    }

    // ... pozostałe metody kontrolera ...
} 
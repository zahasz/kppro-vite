namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminPanelController extends Controller
{
    /**
     * Wyświetla dashboard panelu administracyjnego.
     */
    public function index()
    {
        $stats = [
            'active_users' => User::where('status', 'active')->count(),
            'new_users' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'users_online' => 12, // Przykładowa wartość, w rzeczywistości pobrana z systemu śledzenia sesji
            'inactive_users' => User::where('last_login_at', '<', Carbon::now()->subMonths(6))->count(),
            
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'monthly_revenue' => $this->formatPrice(UserSubscription::where('status', 'active')
                ->whereMonth('start_date', Carbon::now()->month)
                ->sum('price')),
            'most_popular_plan' => $this->getMostPopularPlan(),
            'expiring_soon' => UserSubscription::where('status', 'active')
                ->where('end_date', '<=', Carbon::now()->addDays(7))
                ->where('end_date', '>=', Carbon::now())
                ->count(),
        ];

        return view('admin.index', compact('stats'));
    }

    /**
     * Wyświetla listę planów subskrypcji.
     */
    public function subscriptionsIndex(Request $request)
    {
        $subscriptionPlans = SubscriptionPlan::withCount(['activeSubscriptions'])
            ->orderBy('display_order')
            ->paginate(10);

        $stats = [
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'monthly_revenue' => $this->formatPrice(UserSubscription::where('status', 'active')->sum('price')),
            'average_subscription_value' => $this->formatPrice(UserSubscription::where('status', 'active')->avg('price')),
        ];

        return view('admin.subscriptions.index', compact('subscriptionPlans', 'stats'));
    }

    /**
     * Wyświetla formularz tworzenia planu subskrypcji.
     */
    public function createSubscriptionPlan()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Zapisuje nowy plan subskrypcji.
     */
    public function storeSubscriptionPlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subscription_plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,quarterly,annual,lifetime',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan subskrypcji został utworzony.');
    }

    /**
     * Wyświetla formularz edycji planu subskrypcji.
     */
    public function editSubscriptionPlan(SubscriptionPlan $plan)
    {
        return view('admin.subscriptions.edit', compact('plan'));
    }

    /**
     * Aktualizuje plan subskrypcji.
     */
    public function updateSubscriptionPlan(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subscription_plans,code,' . $plan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,quarterly,annual,lifetime',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan subskrypcji został zaktualizowany.');
    }

    /**
     * Usuwa plan subskrypcji.
     */
    public function destroySubscriptionPlan(SubscriptionPlan $plan)
    {
        // Sprawdź czy plan ma aktywne subskrypcje
        if ($plan->activeSubscriptions()->count() > 0) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Nie można usunąć planu, który ma aktywne subskrypcje.');
        }

        $plan->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan subskrypcji został usunięty.');
    }

    /**
     * Zmienia status planu subskrypcji.
     */
    public function togglePlanStatus(Request $request, SubscriptionPlan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();

        $status = $plan->is_active ? 'aktywny' : 'nieaktywny';

        return response()->json([
            'success' => true,
            'message' => "Status planu został zmieniony na: {$status}",
            'is_active' => $plan->is_active
        ]);
    }

    /**
     * Wyświetla listę subskrypcji użytkowników.
     */
    public function userSubscriptions(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan']);

        // Filtrowanie
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(10);

        return view('admin.subscriptions.users', compact('subscriptions'));
    }

    /**
     * Wyświetla formularz tworzenia subskrypcji dla użytkownika.
     */
    public function createUserSubscription()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();
        return view('admin.subscriptions.create-user-subscription', compact('plans'));
    }

    /**
     * Zapisuje nową subskrypcję użytkownika.
     */
    public function storeUserSubscription(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:active,pending,trial',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'payment_method' => 'required|string',
            'payment_details' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        $validated['price'] = $plan->price;

        $subscription = UserSubscription::create($validated);

        if ($request->has('send_notification') && $request->send_notification) {
            // Tutaj kod do wysłania powiadomienia
        }

        return redirect()->route('admin.subscriptions.users')
            ->with('success', 'Subskrypcja użytkownika została utworzona.');
    }

    /**
     * Wyświetla formularz edycji subskrypcji użytkownika.
     */
    public function editUserSubscription(UserSubscription $subscription)
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();
        $payments = SubscriptionPayment::where('user_subscription_id', $subscription->id)
            ->orderBy('payment_date', 'desc')
            ->get();
            
        return view('admin.subscriptions.edit-user-subscription', 
            compact('subscription', 'plans', 'payments'));
    }

    /**
     * Aktualizuje subskrypcję użytkownika.
     */
    public function updateUserSubscription(Request $request, UserSubscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:active,pending,canceled,expired,trial',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'payment_method' => 'required|string',
            'payment_details' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        if ($request->plan_id != $subscription->plan_id) {
            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
            $validated['price'] = $plan->price;
        }

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.users')
            ->with('success', 'Subskrypcja użytkownika została zaktualizowana.');
    }

    /**
     * Anuluje subskrypcję użytkownika.
     */
    public function cancelSubscription(Request $request, UserSubscription $subscription)
    {
        $subscription->status = 'canceled';
        $subscription->canceled_at = Carbon::now();
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subskrypcja została anulowana.'
        ]);
    }

    /**
     * Wyświetla historię płatności.
     */
    public function subscriptionPayments(Request $request)
    {
        $query = SubscriptionPayment::with(['userSubscription.user', 'userSubscription.plan']);

        // Filtrowanie
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('transaction_id', 'like', "%{$search}%")
                ->orWhereHas('userSubscription.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method') && $request->payment_method != 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $payments = $query->latest('payment_date')->paginate(10);

        // Statystyki
        $stats = [
            'monthly_revenue' => $this->formatPrice(SubscriptionPayment::where('status', 'completed')
                ->whereMonth('payment_date', Carbon::now()->month)
                ->sum('amount')),
            'payments_count' => SubscriptionPayment::count(),
            'average_payment' => $this->formatPrice(SubscriptionPayment::where('status', 'completed')->avg('amount')),
            'failed_payments' => SubscriptionPayment::where('status', 'failed')->count(),
        ];

        return view('admin.subscriptions.payments', compact('payments', 'stats'));
    }

    /**
     * Wyświetla szczegóły płatności.
     */
    public function showPaymentDetails(SubscriptionPayment $payment)
    {
        $payment->load(['userSubscription.user', 'userSubscription.plan']);
        
        return view('admin.subscriptions.payment-details', compact('payment'));
    }

    /**
     * Zwraca płatność.
     */
    public function refundPayment(Request $request, SubscriptionPayment $payment)
    {
        if ($payment->status != 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Można zwrócić tylko zrealizowane płatności.'
            ], 400);
        }

        $payment->status = 'refunded';
        $payment->refunded_at = Carbon::now();
        $payment->save();

        // Tutaj kod do zintegrowanej bramki płatności

        return response()->json([
            'success' => true,
            'message' => 'Płatność została zwrócona.'
        ]);
    }

    /**
     * Pobiera najbardziej popularny plan subskrypcji.
     */
    private function getMostPopularPlan()
    {
        $popular = UserSubscription::select('plan_id', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('plan_id')
            ->orderByDesc('total')
            ->first();
            
        if ($popular) {
            $plan = SubscriptionPlan::find($popular->plan_id);
            return $plan ? $plan->name : 'Brak danych';
        }
        
        return 'Brak danych';
    }

    /**
     * Formatuje cenę do wyświetlenia.
     */
    private function formatPrice($price)
    {
        return number_format($price ?? 0, 2, ',', ' ') . ' PLN';
    }
} 
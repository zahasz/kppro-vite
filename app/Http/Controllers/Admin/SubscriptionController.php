<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Konstruktor
     *
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Wyświetla listę planów subskrypcji
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('display_order')->get();
        
        // Statystyki
        $activeSubscriptionsCount = UserSubscription::where('status', 'active')->count();
        $monthlyRevenue = SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');
        
        $avgSubscriptionValue = SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->avg('amount');
            
        return view('admin.subscriptions.index', compact('plans', 'activeSubscriptionsCount', 'monthlyRevenue', 'avgSubscriptionValue'));
    }

    /**
     * Pokazuje formularz tworzenia nowego planu subskrypcji
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Zapisuje nowy plan subskrypcji
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subscription_plans,code',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'required|string|in:monthly,quarterly,annually,lifetime',
            'features' => 'array',
            'max_users' => 'nullable|integer|min:1',
            'max_invoices' => 'nullable|integer|min:1',
            'max_products' => 'nullable|integer|min:1',
            'max_clients' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $plan = new SubscriptionPlan();
        $plan->fill($validated);
        
        // Upewniamy się, że features jest zawsze tablicą
        if (!$request->has('features') || !is_array($request->input('features'))) {
            $plan->features = [];
        } else {
            $plan->features = $request->input('features');
        }
        
        $plan->is_active = $request->input('is_active', false);
        $plan->save();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan subskrypcji został utworzony pomyślnie.');
    }

    /**
     * Wyświetla formularz edycji planu subskrypcji
     *
     * @param SubscriptionPlan $plan
     * @return \Illuminate\View\View
     */
    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscriptions.create', compact('plan'));
    }

    /**
     * Aktualizuje plan subskrypcji
     *
     * @param Request $request
     * @param SubscriptionPlan $plan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subscription_plans,code,' . $plan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'required|string|in:monthly,quarterly,annually,lifetime',
            'features' => 'array',
            'max_users' => 'nullable|integer|min:1',
            'max_invoices' => 'nullable|integer|min:1',
            'max_products' => 'nullable|integer|min:1',
            'max_clients' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $plan->fill($validated);
        
        // Upewniamy się, że features jest zawsze tablicą
        if (!$request->has('features') || !is_array($request->input('features'))) {
            $plan->features = [];
        } else {
            $plan->features = $request->input('features');
        }
        
        $plan->is_active = $request->input('is_active', false);
        $plan->save();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan subskrypcji został zaktualizowany pomyślnie.');
    }

    /**
     * Zmienia status aktywności planu subskrypcji
     *
     * @param Request $request
     * @param SubscriptionPlan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive(Request $request, SubscriptionPlan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();

        return response()->json([
            'success' => true,
            'active' => $plan->is_active,
            'message' => $plan->is_active ? 'Plan został aktywowany.' : 'Plan został dezaktywowany.'
        ]);
    }

    /**
     * Wyświetla subskrypcje użytkowników
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        try {
            $query = UserSubscription::with(['user', 'plan']);

            // Filtrowanie
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->input('status') != 'all') {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('subscription_type') && $request->input('subscription_type')) {
                $query->where('subscription_type', '=', $request->input('subscription_type'));
            }

            $subscriptions = $query->latest()->paginate(15);

            return view('admin.subscriptions.users', compact('subscriptions'));
        } catch (\Exception $e) {
            \Log::error('Błąd przy wyświetlaniu subskrypcji: ' . $e->getMessage());
            return view('admin.subscriptions.users', ['subscriptions' => collect([])])->withErrors(['error' => 'Wystąpił błąd podczas pobierania subskrypcji: ' . $e->getMessage()]);
        }
    }

    /**
     * Wyświetla formularz przypisania subskrypcji do użytkownika
     *
     * @return \Illuminate\View\View
     */
    public function createUserSubscription()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();
        return view('admin.subscriptions.create-user-subscription', compact('plans'));
    }

    /**
     * Zapisuje nową subskrypcję użytkownika
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUserSubscription(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|string|in:active,pending_payment,trial',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'payment_method' => 'required|string',
            'payment_details' => 'nullable|string',
            'notes' => 'nullable|string',
            'send_notification' => 'boolean',
            'subscription_type' => 'required|in:manual,automatic',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Dodaj subscription_type do validated data
        $validated['subscription_type'] = $validated['subscription_type'] ?? UserSubscription::TYPE_MANUAL;

        $result = $this->subscriptionService->createSubscription($user, $plan, $validated);

        if ($result['success']) {
            return redirect()->route('admin.subscriptions.users')
                ->with('success', 'Subskrypcja została przypisana do użytkownika pomyślnie.');
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Wyświetla formularz edycji subskrypcji użytkownika
     *
     * @param UserSubscription $subscription
     * @return \Illuminate\View\View
     */
    public function editUserSubscription(UserSubscription $subscription)
    {
        $subscription->load(['user', 'plan', 'payments']);
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();
        
        return view('admin.subscriptions.edit-user-subscription', compact('subscription', 'plans'));
    }

    /**
     * Aktualizuje subskrypcję użytkownika
     *
     * @param Request $request
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserSubscription(Request $request, UserSubscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|string|in:active,pending_payment,trial,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'payment_method' => 'required|string',
            'payment_details' => 'nullable|string',
            'notes' => 'nullable|string',
            'auto_renew' => 'boolean',
            'subscription_type' => 'required|in:manual,automatic',
        ]);

        // Upewnij się, że nowy plan istnieje
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        
        // Dodaj subscription_type do validated data
        $validated['subscription_type'] = $validated['subscription_type'] ?? $subscription->subscription_type;
        
        $result = $this->subscriptionService->updateSubscription($subscription, $validated);

        if ($result['success']) {
            return redirect()->route('admin.subscriptions.users')
                ->with('success', 'Subskrypcja użytkownika została zaktualizowana pomyślnie.');
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Anuluje subskrypcję użytkownika
     *
     * @param Request $request
     * @param UserSubscription $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelUserSubscription(Request $request, UserSubscription $subscription)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
            'immediate' => 'boolean',
        ]);

        $result = $this->subscriptionService->cancelSubscription($subscription, $validated);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    /**
     * Lista płatności za subskrypcje
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function payments(Request $request)
    {
        $query = SubscriptionPayment::with(['user', 'subscription.plan']);

        // Filtrowanie
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('payment_method') && $request->input('payment_method') != 'all') {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->has('date_from') && !empty($request->input('date_from'))) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && !empty($request->input('date_to'))) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $payments = $query->latest()->paginate(15);

        // Statystyki
        $currentMonth = Carbon::now()->month;
        $previousMonth = Carbon::now()->subMonth()->month;

        // Przychód miesięczny
        $monthlyRevenue = SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->sum('amount');
        
        $previousMonthRevenue = SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', $previousMonth)
            ->sum('amount');
        
        $monthlyRevenueChange = $previousMonthRevenue > 0 
            ? round((($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : 0;

        // Liczba płatności
        $paymentsCount = SubscriptionPayment::whereMonth('created_at', $currentMonth)->count();
        $previousMonthPaymentsCount = SubscriptionPayment::whereMonth('created_at', $previousMonth)->count();
        
        $paymentsCountChange = $previousMonthPaymentsCount > 0 
            ? round((($paymentsCount - $previousMonthPaymentsCount) / $previousMonthPaymentsCount) * 100, 1)
            : 0;

        // Średnia wartość płatności
        $avgPaymentValue = SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->avg('amount') ?? 0;
        
        $previousMonthAvgPaymentValue = SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', $previousMonth)
            ->avg('amount') ?? 0;
        
        $avgPaymentValueChange = $previousMonthAvgPaymentValue > 0 
            ? round((($avgPaymentValue - $previousMonthAvgPaymentValue) / $previousMonthAvgPaymentValue) * 100, 1)
            : 0;

        // Nieudane płatności
        $failedPaymentsCount = SubscriptionPayment::where('status', 'failed')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        
        $previousMonthFailedPaymentsCount = SubscriptionPayment::where('status', 'failed')
            ->whereMonth('created_at', $previousMonth)
            ->count();
        
        $failedPaymentsChange = $previousMonthFailedPaymentsCount > 0 
            ? round((($failedPaymentsCount - $previousMonthFailedPaymentsCount) / $previousMonthFailedPaymentsCount) * 100, 1)
            : 0;

        return view('admin.subscriptions.payments', compact(
            'payments',
            'monthlyRevenue',
            'monthlyRevenueChange',
            'paymentsCount',
            'paymentsCountChange',
            'avgPaymentValue',
            'avgPaymentValueChange',
            'failedPaymentsCount',
            'failedPaymentsChange'
        ));
    }

    /**
     * Szczegóły płatności
     *
     * @param SubscriptionPayment $payment
     * @return \Illuminate\View\View
     */
    public function showPayment(SubscriptionPayment $payment)
    {
        $payment->load(['user', 'subscription.plan']);
        
        // Pobieranie faktury jeśli istnieje
        $invoice = $payment->invoice;
        
        // Historia zdarzeń płatności
        $paymentEvents = $payment->events()
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.subscriptions.payment-details', compact('payment', 'invoice', 'paymentEvents'));
    }

    /**
     * Eksportuje płatności do CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPayments(Request $request)
    {
        // Budujemy zapytanie z tymi samymi filtrami co w widoku
        $query = SubscriptionPayment::with(['user', 'subscription.plan']);

        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('payment_method') && $request->input('payment_method') != 'all') {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->has('date_from') && !empty($request->input('date_from'))) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && !empty($request->input('date_to'))) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $payments = $query->latest()->get();

        // Tworzenie CSV
        $filename = 'platnosci_eksport_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID transakcji',
            'Data',
            'Użytkownik',
            'Email',
            'Plan',
            'Kwota',
            'Status',
            'Metoda płatności'
        ];

        $callback = function() use ($payments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->transaction_id,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->user->name ?? 'N/D',
                    $payment->user->email ?? 'N/D',
                    $payment->subscription->plan->name ?? 'N/D',
                    number_format($payment->amount, 2, ',', ' ') . ' ' . $payment->currency,
                    $this->formatStatus($payment->status),
                    $this->formatPaymentMethod($payment->payment_method)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Zwraca formatowany status płatności
     * 
     * @param string $status
     * @return string
     */
    private function formatStatus($status)
    {
        switch ($status) {
            case 'completed':
                return 'Opłacona';
            case 'pending':
                return 'Oczekująca';
            case 'failed':
                return 'Nieudana';
            case 'refunded':
                return 'Zwrócona';
            default:
                return ucfirst($status);
        }
    }

    /**
     * Zwraca formatowaną metodę płatności
     * 
     * @param string $method
     * @return string
     */
    private function formatPaymentMethod($method)
    {
        switch ($method) {
            case 'card':
                return 'Karta płatnicza';
            case 'paypal':
                return 'PayPal';
            case 'bank_transfer':
                return 'Przelew bankowy';
            default:
                return ucfirst($method);
        }
    }

    /**
     * Obsługuje generowanie faktury
     *
     * @param SubscriptionPayment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateInvoice(SubscriptionPayment $payment)
    {
        // Logika generowania faktury
        // ...
        
        return redirect()->route('admin.subscriptions.payment.show', $payment->id)
            ->with('success', 'Faktura została wygenerowana pomyślnie.');
    }

    /**
     * Zwraca płatność
     *
     * @param SubscriptionPayment $payment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refundPayment(SubscriptionPayment $payment)
    {
        // Sprawdzenie czy płatność można zwrócić
        if ($payment->status !== 'completed' || $payment->refunded_at) {
            return redirect()->back()
                ->with('error', 'Tej płatności nie można zwrócić.');
        }
        
        try {
            DB::beginTransaction();
            
            // Zmiana statusu płatności
            $payment->status = 'refunded';
            $payment->refunded_at = now();
            $payment->save();
            
            // Dodanie wpisu do historii zdarzeń
            $payment->events()->create([
                'event_type' => 'payment_refunded',
                'description' => 'Płatność została zwrócona.',
                'data' => [
                    'refunded_by' => auth()->user()->name,
                    'refunded_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
            
            // Aktualizacja statusu subskrypcji jeśli istnieje
            if ($payment->subscription) {
                $payment->subscription->status = 'canceled';
                $payment->subscription->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.subscriptions.payment.show', $payment->id)
                ->with('success', 'Płatność została zwrócona pomyślnie.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas zwracania płatności: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas zwracania płatności: ' . $e->getMessage());
        }
    }
} 
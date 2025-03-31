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
use App\Models\SubscriptionPermission;

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
        
        // Pobierz subskrypcje użytkowników
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.subscriptions.index', compact('plans', 'activeSubscriptionsCount', 'monthlyRevenue', 'avgSubscriptionValue', 'subscriptions'));
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
        $planPermissions = $plan->permissions->pluck('id')->toArray();
        
        // Grupowanie uprawnień według kategorii
        $permissionsByCategory = SubscriptionPermission::orderBy('category')->get()->groupBy('category');
        
        return view('admin.subscriptions.edit', compact('plan', 'planPermissions', 'permissionsByCategory'));
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
            'code' => 'required|string|max:100|unique:subscription_plans,code,' . $plan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|string|in:monthly,yearly',
            'max_invoices' => 'nullable|integer|min:0',
            'max_products' => 'nullable|integer|min:0',
            'max_clients' => 'nullable|integer|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:subscription_permissions,id',
        ]);
        
        // Aktualizacja podstawowych danych planu
        $plan->name = $validated['name'];
        $plan->code = $validated['code'];
        $plan->description = $validated['description'] ?? '';
        $plan->price = $validated['price'];
        $plan->billing_period = $validated['billing_period'];
        $plan->max_invoices = $validated['max_invoices'] ?? 0;
        $plan->max_products = $validated['max_products'] ?? 0;
        $plan->max_clients = $validated['max_clients'] ?? 0;
        $plan->trial_days = $validated['trial_days'] ?? 0;
        $plan->is_active = $request->has('is_active');
        $plan->is_public = $request->has('is_public');
        
        $plan->save();
        
        // Aktualizacja uprawnień planu
        if ($request->has('permissions')) {
            $plan->permissions()->sync($request->permissions);
        } else {
            $plan->permissions()->detach();
        }
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan subskrypcyjny został zaktualizowany.');
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
     * Wyświetla listę subskrypcji użytkowników
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
            
            // Pobierz plany subskrypcji do filtrowania
            $plans = SubscriptionPlan::orderBy('display_order')->get();
            
            // Pobierz użytkowników do filtrowania
            $users = User::orderBy('name')->get();
            
            // Statystyki dla podsumowania
            $stats = [
                'active' => UserSubscription::where('status', 'active')->count(),
                'pending' => UserSubscription::where('status', 'pending')->count(),
                'cancelled' => UserSubscription::where('status', 'cancelled')->count(),
                'expired' => UserSubscription::where('status', 'expired')->count()
            ];

            return view('admin.subscriptions.users', compact('subscriptions', 'plans', 'users', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Błąd przy wyświetlaniu subskrypcji: ' . $e->getMessage());
            return view('admin.subscriptions.users', [
                'subscriptions' => collect([]),
                'plans' => collect([]),
                'users' => collect([]),
                'stats' => [
                    'active' => 0,
                    'pending' => 0,
                    'cancelled' => 0,
                    'expired' => 0
                ]
            ])->withErrors(['error' => 'Wystąpił błąd podczas pobierania subskrypcji: ' . $e->getMessage()]);
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
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
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
        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
        
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

    /**
     * Wyświetla listę wszystkich uprawnień dostępnych w systemie
     */
    public function permissions()
    {
        // Pobierz wszystkie uprawnienia i pogrupuj je według kategorii
        $permissions = SubscriptionPermission::all()->groupBy('category');
        
        // Pobierz wszystkie plany subskrypcyjne
        $plans = SubscriptionPlan::orderBy('display_order')->get();
        
        return view('admin.subscriptions.permissions', compact('permissions', 'plans'));
    }
    
    /**
     * Wyświetla szczegóły konkretnego planu subskrypcyjnego
     */
    public function show(SubscriptionPlan $plan)
    {
        // Pobierz plan z relacją uprawnień
        $plan->load('permissions');
        
        return view('admin.subscriptions.show', compact('plan'));
    }
    
    /**
     * Wyświetla formularz do przypisywania uprawnień do planu
     */
    public function assignPermissions(SubscriptionPlan $plan)
    {
        // Pobierz plan z relacją uprawnień
        $plan->load('permissions');
        
        // Pogrupuj uprawnienia według kategorii
        $permissions = SubscriptionPermission::all()->groupBy('category');
        
        // Przygotuj tablicę z ID uprawnień przypisanych do planu
        $assignedPermissions = $plan->permissions->pluck('id')->toArray();
        
        return view('admin.subscriptions.assign_permissions', compact('plan', 'permissions', 'assignedPermissions'));
    }
    
    /**
     * Zapisuje przypisane uprawnienia do planu
     */
    public function storePermissions(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:subscription_permissions,id',
            'permission_values' => 'array',
            'permission_values.*' => 'nullable|string'
        ]);
        
        // Przygotuj dane do synchronizacji
        $permissionsWithValues = [];
        if (isset($validated['permissions'])) {
            foreach ($validated['permissions'] as $permissionId) {
                $permissionsWithValues[$permissionId] = [
                    'value' => $validated['permission_values'][$permissionId] ?? null
                ];
            }
        }
        
        // Synchronizuj uprawnienia z planem
        $plan->permissions()->sync($permissionsWithValues);
        
        return redirect()
            ->route('admin.subscriptions.edit', $plan)
            ->with('success', 'Uprawnienia zostały pomyślnie zaktualizowane');
    }

    /**
     * Obsługuje ręczną sprzedaż subskrypcji (dla gotówki/przelewu)
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function manualSale()
    {
        try {
            // Pobierz wszystkich użytkowników i plany subskrypcji
            $users = \App\Models\User::orderBy('name')->get();
            $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();

            // Możliwe metody płatności dla ręcznej sprzedaży
            $paymentMethods = [
                'cash' => 'Gotówka',
                'bank_transfer' => 'Przelew bankowy',
                'card' => 'Karta płatnicza (terminal)',
                'other' => 'Inna metoda'
            ];

            // Jeśli to jest żądanie POST, obsłuż sprzedaż
            if (request()->isMethod('post')) {
                // Walidacja danych
                $validated = request()->validate([
                    'user_id' => 'required|exists:users,id',
                    'subscription_plan_id' => 'required|exists:subscription_plans,id',
                    'payment_method' => 'required|string|in:cash,bank_transfer,card,other',
                    'payment_details' => 'nullable|string',
                    'admin_notes' => 'nullable|string',
                ]);

                // Pobierz użytkownika i plan
                $user = \App\Models\User::findOrFail($validated['user_id']);
                $plan = \App\Models\SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

                // Sprawdź czy profil firmy istnieje, jeśli nie - utwórz
                if (!$user->companyProfile) {
                    $companyProfile = new \App\Models\CompanyProfile();
                    $companyProfile->user_id = $user->id;
                    $companyProfile->company_name = 'Firma ' . $user->name;
                    $companyProfile->tax_number = 'Brak'; // Domyślna wartość
                    $companyProfile->save();
                    
                    // Utwórz domyślne konto bankowe
                    $bankAccount = new \App\Models\BankAccount();
                    $bankAccount->company_profile_id = $companyProfile->id;
                    $bankAccount->account_name = 'Główne konto firmowe';
                    $bankAccount->account_number = 'PL00 0000 0000 0000 0000 0000 0000';
                    $bankAccount->bank_name = 'Bank';
                    $bankAccount->is_default = true;
                    $bankAccount->save();
                    
                    $companyProfile->default_bank_account_id = $bankAccount->id;
                    $companyProfile->save();
                    
                    // Odśwież użytkownika
                    $user->refresh();
                }

                // Przygotuj dane dla serwisu subskrypcji
                $today = now();
                $subscriptionData = [
                    'status' => 'active',
                    'price' => $plan->price,
                    'start_date' => $today,
                    'end_date' => $this->calculateEndDate($today, $plan->billing_period),
                    'subscription_type' => 'manual',
                    'payment_method' => $validated['payment_method'],
                    'payment_details' => $validated['payment_details'] ?? 'Płatność przyjęta przez administratora',
                    'admin_notes' => $validated['admin_notes'] ?? 'Ręczna sprzedaż subskrypcji przez administratora',
                    'create_payment' => true, // Automatycznie utwórz płatność
                    'send_notification' => true, // Wyślij powiadomienie do użytkownika
                ];

                // Rozpocznij transakcję
                \Illuminate\Support\Facades\DB::beginTransaction();

                // Utwórz subskrypcję
                $subscriptionService = app(\App\Services\SubscriptionService::class);
                $result = $subscriptionService->createSubscription($user, $plan, $subscriptionData);

                if (!$result['success']) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Wystąpił błąd podczas tworzenia subskrypcji: ' . ($result['message'] ?? 'Nieznany błąd'))
                        ->withInput();
                }

                // Pobierz utworzoną subskrypcję
                $subscription = $result['subscription'];

                // Wygeneruj płatność
                $payment = null;
                foreach ($subscription->payments as $p) {
                    if (!$payment || $p->created_at > $payment->created_at) {
                        $payment = $p;
                    }
                }

                // Wygeneruj fakturę dla płatności
                $invoice = null;
                if ($payment) {
                    // Pobierz metodę generowania faktury przez refleksję (bo jest prywatna)
                    $reflection = new \ReflectionClass($subscriptionService);
                    $method = $reflection->getMethod('generateInvoiceForPayment');
                    $method->setAccessible(true);
                    $invoice = $method->invoke($subscriptionService, $payment);

                    if ($invoice) {
                        // Zaktualizuj subskrypcję o identyfikator faktury
                        $subscription->last_invoice_id = $invoice->id;
                        $subscription->last_invoice_number = $invoice->number;
                        $subscription->save();
                    }
                }

                // Zatwierdź transakcję
                \Illuminate\Support\Facades\DB::commit();

                // Przekieruj do strony z subskrypcjami z komunikatem sukcesu
                return redirect()->route('admin.subscriptions.users')
                    ->with('success', 'Subskrypcja została sprzedana pomyślnie!' . 
                           ($invoice ? ' Faktura nr: ' . $invoice->number : ' Nie utworzono faktury.'));
            }

            // Wyświetl formularz ręcznej sprzedaży
            return view('admin.subscriptions.manual-sale', compact('users', 'plans', 'paymentMethods'));
        } catch (\Exception $e) {
            // W przypadku błędu, cofnij transakcję
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
            }
            
            // Zapisz szczegóły błędu do logów
            \Illuminate\Support\Facades\Log::error('Błąd podczas ręcznej sprzedaży subskrypcji: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Przekieruj z informacją o błędzie
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas przetwarzania sprzedaży: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Oblicza datę zakończenia subskrypcji na podstawie daty początkowej i okresu rozliczeniowego
     *
     * @param \Carbon\Carbon $startDate
     * @param string $billingPeriod
     * @return \Carbon\Carbon
     */
    private function calculateEndDate(\Carbon\Carbon $startDate, string $billingPeriod): \Carbon\Carbon
    {
        $endDate = $startDate->copy();
        
        switch ($billingPeriod) {
            case 'monthly':
                $endDate->addMonth();
                break;
            case 'quarterly':
                $endDate->addMonths(3);
                break;
            case 'annually':
            case 'yearly':
                $endDate->addYear();
                break;
            case 'biannually':
                $endDate->addMonths(6);
                break;
            case 'lifetime':
                $endDate->addYears(100); // Praktycznie bez daty końcowej
                break;
            default:
                $endDate->addMonth(); // Domyślnie jeden miesiąc
        }
        
        return $endDate;
    }
} 
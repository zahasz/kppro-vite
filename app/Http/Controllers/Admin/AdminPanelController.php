<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Models\BillingSettings;
use App\Models\Invoice;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;

class AdminPanelController extends Controller
{
    public function index()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        // Pobierz statystyki
        $stats = Cache::remember('admin_dashboard_stats', 3600, function () {
            $activeSubscriptions = Subscription::where('status', 'active')->count();
            $totalSubscriptions = Subscription::count();
            
            // Statystyki subskrypcji według typu
            $manualSubscriptions = UserSubscription::where('subscription_type', 'manual')->count();
            $automaticSubscriptions = UserSubscription::where('subscription_type', 'automatic')->count();
            
            // Oblicz procenty
            $manualPercentage = $totalSubscriptions > 0 ? round(($manualSubscriptions / $totalSubscriptions) * 100) : 0;
            $automaticPercentage = $totalSubscriptions > 0 ? round(($automaticSubscriptions / $totalSubscriptions) * 100) : 0;
            
            // Przychód z aktywnych subskrypcji
            $activeSubscriptionsValue = Subscription::where('status', 'active')->sum('price');
            
            // Pobierz dane z poprzedniego miesiąca dla porównania
            $lastMonth = Carbon::now()->subMonth();
            $activeSubscriptionsLastMonth = Subscription::where('status', 'active')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count();
            
            // Oblicz zmianę procentową
            $subMonthlyChange = 0;
            if ($activeSubscriptionsLastMonth > 0) {
                $subMonthlyChange = round((($activeSubscriptions - $activeSubscriptionsLastMonth) / $activeSubscriptionsLastMonth) * 100);
            }
            
            // Przychód z subskrypcji w bieżącym miesiącu
            $monthSubscriptionsValue = Subscription::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                ->sum('price');
            
            // Przychód z subskrypcji w poprzednim miesiącu
            $lastMonthSubscriptionsValue = Subscription::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                        ->sum('price');
            
            // Zmiana procentowa przychodu
            $revenueMonthlyChange = 0;
            if ($lastMonthSubscriptionsValue > 0) {
                $revenueMonthlyChange = round((($monthSubscriptionsValue - $lastMonthSubscriptionsValue) / $lastMonthSubscriptionsValue) * 100);
            }
            
            // Oblicz średnią wartość subskrypcji
            $avgSubValueLastMonth = $activeSubscriptionsLastMonth > 0 
                ? $lastMonthSubscriptionsValue / $activeSubscriptionsLastMonth 
                : 0;
            
            $avgSubValueCurrentMonth = $activeSubscriptions > 0 
                ? $activeSubscriptionsValue / $activeSubscriptions 
                : 0;
            
            // Zmiana procentowa średniej wartości subskrypcji
            $avgValueMonthlyChange = 0;
            if ($avgSubValueLastMonth > 0) {
                $avgValueMonthlyChange = round((($avgSubValueCurrentMonth - $avgSubValueLastMonth) / $avgSubValueLastMonth) * 100);
            }
            
            return [
                'active_subscriptions' => $activeSubscriptions,
                'active_subscriptions_value' => $activeSubscriptionsValue,
                'month_subscriptions_value' => $monthSubscriptionsValue,
                'sub_monthly_change' => $subMonthlyChange,
                'revenue_monthly_change' => $revenueMonthlyChange,
                'avg_value_monthly_change' => $avgValueMonthlyChange,
                'manual_subscriptions' => $manualSubscriptions,
                'automatic_subscriptions' => $automaticSubscriptions,
                'manual_percentage' => $manualPercentage,
                'automatic_percentage' => $automaticPercentage
            ];
        });
        
        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $users = User::with('roles')->paginate(10);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function createUser()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function storeUser(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->is_active = isset($request->is_active) ? true : false;
            $user->save();

            // Przypisz role
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
            
            // Przypisz uprawnienia
            if ($request->has('permissions')) {
                $user->syncPermissions($request->permissions);
            }

            // Wyczyść pamięć podręczną
            Cache::forget('stats.users_count');
            
            return redirect()->route('admin.users.index')->with('success', 'Użytkownik został utworzony.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia użytkownika: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Wystąpił błąd podczas tworzenia użytkownika.');
        }
    }

    public function editUser(User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $roles = Role::all();
        $permissions = Permission::all();
        $userRoles = $user->roles ? $user->roles->pluck('id')->toArray() : [];
        $userPermissions = $user->permissions ? $user->permissions->pluck('id')->toArray() : [];
        
        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'userRoles', 'userPermissions'));
    }

    public function updateUser(Request $request, User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if (isset($request->is_active)) {
            $user->is_active = $request->is_active;
        }

        $user->save();

        // Synchronizuj role
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }
        
        // Synchronizuj uprawnienia
        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        } else {
            $user->syncPermissions([]);
        }

        // Wyczyść pamięć podręczną
        Cache::forget('stats.users_count');
        
        return redirect()->route('admin.users')->with('success', 'Użytkownik został zaktualizowany.');
    }

    public function deleteUser(User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->route('admin.users')->with('error', 'Nie można usunąć ostatniego administratora systemu.');
        }

        try {
            $user->delete();
            Cache::forget('stats.users_count');
            return redirect()->route('admin.users')->with('success', 'Użytkownik został usunięty.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania użytkownika: ' . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Wystąpił błąd podczas usuwania użytkownika.');
        }
    }

    public function roles()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function createRole()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::create(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            Cache::forget('stats.roles_count');
            return redirect()->route('admin.roles')->with('success', 'Rola została utworzona.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia roli: ' . $e->getMessage());
            return redirect()->route('admin.roles')->with('error', 'Wystąpił błąd podczas tworzenia roli.');
        }
    }

    public function editRole(Role $role)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions ? $role->permissions->pluck('id')->toArray() : [];
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Nie można modyfikować roli administratora.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role->update(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            return redirect()->route('admin.roles')->with('success', 'Rola została zaktualizowana.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji roli: ' . $e->getMessage());
            return redirect()->route('admin.roles')->with('error', 'Wystąpił błąd podczas aktualizacji roli.');
        }
    }

    public function deleteRole(Role $role)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Nie można usunąć roli administratora.');
        }

        try {
            $role->delete();
            Cache::forget('stats.roles_count');
            return redirect()->route('admin.roles')->with('success', 'Rola została usunięta.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania roli: ' . $e->getMessage());
            return redirect()->route('admin.roles')->with('error', 'Wystąpił błąd podczas usuwania roli.');
        }
    }

    public function showRole(Role $role)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $role->load(['permissions', 'users']);
        return view('admin.roles.show', compact('role'));
    }

    public function permissions()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $plans = SubscriptionPlan::orderBy('display_order')->get();
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'subscription')->get();
        
        return view('admin.subscriptions.permissions', compact('plans', 'permissions'));
    }

    public function createPermission()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        return view('admin.permissions.create');
    }

    public function storePermission(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?: 'web',
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Uprawnienie zostało utworzone.');
    }
    
    /**
     * Manages subscription plans
     */
    public function subscriptions()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Użyj modelu SubscriptionPlan zamiast Plan
        $plans = \App\Models\SubscriptionPlan::all();
        
        // Pobierz najnowsze subskrypcje do pokazania w tabeli
        $subscriptions = \App\Models\UserSubscription::with(['user', 'plan'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.subscriptions.index', compact('plans', 'subscriptions'));
    }
    
    /**
     * Shows form to create a new subscription plan
     */
    public function createSubscription()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        return view('admin.subscriptions.create');
    }
    
    /**
     * Stores a new subscription plan
     */
    public function storeSubscription(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:monthly,quarterly,yearly,lifetime',
            'features' => 'nullable|array',
            'subscription_type' => 'required|string|in:manual,automatic,both',
            'trial_period_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $plan = new Plan();
        $plan->name = $request->name;
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->interval = $request->interval;
        $plan->features = $request->features ? array_filter($request->features) : [];
        $plan->subscription_type = $request->subscription_type;
        $plan->trial_period_days = $request->trial_period_days ?? 0;
        $plan->is_active = $request->has('is_active');
        $plan->save();
        
        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan subskrypcji został utworzony.');
    }

    /**
     * Shows form to edit a subscription plan
     */
    public function editSubscription(Plan $plan)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        return view('admin.subscriptions.edit', compact('plan'));
    }
    
    /**
     * Updates a subscription plan
     */
    public function updateSubscription(Request $request, Plan $plan)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:monthly,quarterly,yearly,lifetime',
            'features' => 'nullable|array',
            'subscription_type' => 'required|string|in:manual,automatic,both',
            'trial_period_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $plan->name = $request->name;
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->interval = $request->interval;
        $plan->features = $request->features ? array_filter($request->features) : [];
        $plan->subscription_type = $request->subscription_type;
        $plan->trial_period_days = $request->trial_period_days ?? 0;
        $plan->is_active = $request->has('is_active');
        $plan->save();
        
        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan subskrypcji został zaktualizowany.');
    }

    /**
     * Removes a subscription plan
     */
    public function destroySubscription(Plan $plan)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        try {
            DB::beginTransaction();
            
            // Usuwamy najpierw wszystkie zależne subskrypcje
            $plan->subscriptions()->delete();
            
            // Teraz usuwamy plan
            $plan->delete();
            
            DB::commit();
            return redirect()->route('admin.subscriptions.index')->with('success', 'Plan subskrypcji został usunięty wraz z powiązanymi subskrypcjami.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas usuwania planu subskrypcji: ' . $e->getMessage());
            return redirect()->route('admin.subscriptions.index')->with('error', 'Wystąpił błąd podczas usuwania planu subskrypcji.');
        }
    }

    /**
     * Manages user subscriptions
     */
    public function userSubscriptions(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $query = UserSubscription::with(['user', 'plan']);
        
        // Filtrowanie po statusie
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Filtrowanie po użytkowniku
        if ($request->has('user_id') && $request->input('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        
        // Filtrowanie po planie
        if ($request->has('plan_id') && $request->input('plan_id')) {
            $query->where('subscription_plan_id', $request->input('plan_id'));
        }
        
        // Sortowanie
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);
        
        $subscriptions = $query->paginate(15);
        $users = User::orderBy('name')->get();
        $plans = SubscriptionPlan::orderBy('display_order')->get();
        
        // Oblicz statystyki dla podsumowania
        $stats = [
            'active' => UserSubscription::where('status', 'active')->count(),
            'pending' => UserSubscription::where('status', 'pending')->count(),
            'cancelled' => UserSubscription::where('status', 'cancelled')->count(),
            'expired' => UserSubscription::where('status', 'expired')->count(),
        ];
        
        return view('admin.subscriptions.users', compact('subscriptions', 'users', 'plans', 'stats'));
    }

    /**
     * Shows form to create a new user subscription
     */
    public function createUserSubscription()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $users = User::orderBy('name')->get();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('display_order')->get();
        
        return view('admin.subscriptions.create-user-subscription', compact('users', 'plans'));
    }
    
    /**
     * Stores a new user subscription
     */
    public function storeUserSubscription(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,cancelled,expired,pending',
            'subscription_type' => 'required|in:manual,automatic',
            'renewal_status' => 'nullable|in:enabled,disabled',
            'payment_method' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'auto_renew' => 'boolean',
            'create_payment' => 'boolean',
            'send_notification' => 'boolean',
            'next_payment_date' => 'nullable|date'
        ]);
        
        try {
            $user = User::findOrFail($validated['user_id']);
            $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
            
            // Przygotowanie danych dla serwisu
            $subscriptionData = $validated;
            $subscriptionData['auto_renew'] = $request->has('auto_renew');
            $subscriptionData['create_payment'] = $request->has('create_payment');
            $subscriptionData['send_notification'] = $request->has('send_notification');
            
            // Korzystanie z serwisu subskrypcji do utworzenia subskrypcji
            $subscriptionService = app(\App\Services\SubscriptionService::class);
            $result = $subscriptionService->createSubscription($user, $plan, $subscriptionData);
            
            if ($result['success']) {
                return redirect()->route('admin.subscriptions.users')
                    ->with('success', 'Subskrypcja została pomyślnie utworzona dla użytkownika. '.
                        ($subscriptionData['create_payment'] ? 'Wygenerowano również fakturę.' : ''));
            } else {
                return redirect()->back()
                    ->with('error', 'Wystąpił błąd podczas tworzenia subskrypcji: ' . $result['message'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia subskrypcji użytkownika: ' . $e->getMessage(), [
                'user_id' => $validated['user_id'] ?? null,
                'plan_id' => $validated['subscription_plan_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Wystąpił nieoczekiwany błąd podczas tworzenia subskrypcji: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Shows form to edit a user subscription
     */
    public function editUserSubscription(UserSubscription $subscription)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $users = User::orderBy('name')->get();
        $plans = SubscriptionPlan::orderBy('display_order')->get();
        
        return view('admin.subscriptions.edit-user-subscription', compact('subscription', 'users', 'plans'));
    }
    
    /**
     * Updates a user subscription
     */
    public function updateUserSubscription(Request $request, UserSubscription $subscription)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,cancelled,expired,pending',
            'subscription_type' => 'required|in:manual,automatic',
            'renewal_status' => 'nullable|in:enabled,disabled',
            'payment_method' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'auto_renew' => 'boolean'
        ]);
        
        $subscription->fill($validated);
        $subscription->next_billing_date = $request->has('auto_renew') && $request->input('auto_renew') 
            ? Carbon::parse($request->input('start_date'))->addMonth() 
            : null;
        $subscription->save();
        
        return redirect()->route('admin.subscriptions.users')
            ->with('success', 'Subskrypcja użytkownika została zaktualizowana pomyślnie.');
    }

    /**
     * Removes a user subscription
     */
    public function deleteUserSubscription(UserSubscription $subscription)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $subscription->delete();
        
        return redirect()->route('admin.subscriptions.users')
            ->with('success', 'Subskrypcja użytkownika została usunięta pomyślnie.');
    }
    
    /**
     * Shows subscription statistics
     */
    public function subscriptionStats()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobierz statystyki
        $stats = Cache::remember('subscription_stats', 3600, function () {
            // Aktywne subskrypcje
            $activeSubscriptions = UserSubscription::where('status', 'active')->count();
            $totalSubscriptions = UserSubscription::count();
            
            // Statystyki według typu
            $manualSubscriptions = UserSubscription::where('subscription_type', UserSubscription::TYPE_MANUAL)->count();
            $automaticSubscriptions = UserSubscription::where('subscription_type', UserSubscription::TYPE_AUTOMATIC)->count();
            
            // Oblicz procenty
            $manualPercentage = $totalSubscriptions > 0 ? round(($manualSubscriptions / $totalSubscriptions) * 100) : 0;
            $automaticPercentage = $totalSubscriptions > 0 ? round(($automaticSubscriptions / $totalSubscriptions) * 100) : 0;
            
            // Przychód z aktywnych subskrypcji
            $activeSubscriptionsValue = UserSubscription::where('status', 'active')->sum('price');
            
            // Statystyki za bieżący miesiąc
            $currentMonth = Carbon::now();
            $currentMonthStart = $currentMonth->copy()->startOfMonth();
            $currentMonthEnd = $currentMonth->copy()->endOfMonth();
            
            $subscriptionsThisMonth = UserSubscription::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
            $revenueThisMonth = UserSubscription::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->sum('price');
            
            // Statystyki za poprzedni miesiąc
            $lastMonth = $currentMonth->copy()->subMonth();
            $lastMonthStart = $lastMonth->copy()->startOfMonth();
            $lastMonthEnd = $lastMonth->copy()->endOfMonth();
            
            $subscriptionsLastMonth = UserSubscription::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
            $revenueLastMonth = UserSubscription::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('price');
            
            // Oblicz zmiany procentowe
            $subscriptionChange = $subscriptionsLastMonth > 0 
                ? round((($subscriptionsThisMonth - $subscriptionsLastMonth) / $subscriptionsLastMonth) * 100) 
                : 0;
                
            $revenueChange = $revenueLastMonth > 0 
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100) 
                : 0;
            
            // Średnia wartość subskrypcji
            $avgSubValue = $activeSubscriptions > 0 ? round($activeSubscriptionsValue / $activeSubscriptions, 2) : 0;
            
            // Statystyki według planów
            $planStats = SubscriptionPlan::withCount(['userSubscriptions' => function($query) {
                $query->where('status', 'active');
            }])->get()->map(function($plan) {
                return [
                    'name' => $plan->name,
                    'active_count' => $plan->userSubscriptions_count,
                    'revenue' => $plan->userSubscriptions->where('status', 'active')->sum('price')
                ];
            });
            
            // Statystyki miesięczne (ostatnie 12 miesięcy)
            $monthlyStats = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                
                $monthlyStats[] = [
                    'month' => $month->format('M Y'),
                    'subscriptions' => UserSubscription::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                    'revenue' => UserSubscription::whereBetween('created_at', [$monthStart, $monthEnd])->sum('price')
                ];
            }
            
            return [
                'active_subscriptions' => $activeSubscriptions,
                'total_subscriptions' => $totalSubscriptions,
                'manual_subscriptions' => $manualSubscriptions,
                'automatic_subscriptions' => $automaticSubscriptions,
                'manual_percentage' => $manualPercentage,
                'automatic_percentage' => $automaticPercentage,
                'active_subscriptions_value' => $activeSubscriptionsValue,
                'subscriptions_this_month' => $subscriptionsThisMonth,
                'revenue_this_month' => $revenueThisMonth,
                'subscriptions_last_month' => $subscriptionsLastMonth,
                'revenue_last_month' => $revenueLastMonth,
                'subscription_change' => $subscriptionChange,
                'revenue_change' => $revenueChange,
                'avg_sub_value' => $avgSubValue,
                'plan_stats' => $planStats,
                'monthly_stats' => $monthlyStats
            ];
        });
        
        return view('admin.subscriptions.stats', compact('stats'));
    }

    /**
     * Shows subscription payments
     */
    public function subscriptionPayments(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Kod obsługi płatności subskrypcji
        // Tymczasowo, zwróć widok z pustymi danymi
        return view('admin.subscriptions.payments', [
            'payments' => [],
            'stats' => [
                'total_count' => 0,
                'total_amount' => 0,
                'successful_count' => 0,
                'successful_amount' => 0,
                'failed_count' => 0,
                'refunded_count' => 0,
                'refunded_amount' => 0,
            ]
        ]);
    }

    /**
     * Shows detailed information about a subscription payment
     */
    public function subscriptionPaymentDetails($payment)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tymczasowo, zwróć widok z pustymi danymi
        return view('admin.subscriptions.payment-details', ['payment' => null]);
    }

    /**
     * Wyświetla listę faktur subskrypcyjnych
     */
    public function invoicesList()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobierz wszystkie faktury związane z subskrypcjami
        $invoices = \App\Models\Invoice::whereNotNull('subscription_id')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.billing.invoices', compact('invoices'));
    }
    
    /**
     * Wyświetla szczegóły faktury
     */
    public function invoiceShow($invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $invoice = \App\Models\Invoice::findOrFail($invoice);
        $invoice->load(['items', 'user']);
        
        return view('admin.billing.invoice-details', compact('invoice'));
    }
    
    /**
     * Generuje PDF faktury
     */
    public function invoicePdf($invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $invoice = \App\Models\Invoice::findOrFail($invoice);
        
        // Tutaj kod generowania PDF
        // ...
        
        return redirect()->back()->with('info', 'Funkcja generowania PDF zostanie zaimplementowana wkrótce.');
    }
    
    /**
     * Ręcznie generuje faktury subskrypcyjne
     */
    public function generateInvoices(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        try {
            \Artisan::call('invoices:generate-automatic --force');
            $output = \Artisan::output();
            
            \Log::info('Wywołano ręczne generowanie faktur subskrypcyjnych z panelu administracyjnego', [
                'user_id' => auth()->id(),
                'output' => $output
            ]);
            
            return redirect()->route('admin.billing.invoices')
                ->with('success', 'Rozpoczęto generowanie faktur subskrypcyjnych.');
        } catch (\Exception $e) {
            \Log::error('Błąd podczas generowania faktur subskrypcyjnych z panelu administracyjnego', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.billing.invoices')
                ->with('error', 'Wystąpił błąd podczas generowania faktur: ' . $e->getMessage());
        }
    }

    /**
     * Wyświetla statystyki faktur
     */
    public function invoiceStatistics(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Obsługa filtrowania
        $period = $request->input('period', 'month');
        $settings = BillingSettings::getActive();
        
        // Ustalenie zakresu dat na podstawie wybranego okresu
        $endDate = now();
        
        switch ($period) {
            case 'month':
                $startDate = now()->subMonth();
                $prevStartDate = now()->subMonths(2);
                $prevEndDate = now()->subMonth()->subDay();
                break;
            case 'quarter':
                $startDate = now()->subMonths(3);
                $prevStartDate = now()->subMonths(6);
                $prevEndDate = now()->subMonths(3)->subDay();
                break;
            case 'year':
                $startDate = now()->subYear();
                $prevStartDate = now()->subYears(2);
                $prevEndDate = now()->subYear()->subDay();
                break;
            case 'all':
            default:
                $startDate = now()->subYears(5); // maksymalnie 5 lat wstecz
                $prevStartDate = null;
                $prevEndDate = null;
                break;
        }
        
        // Pobierz dane faktur dla bieżącego okresu
        $invoices = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->with('items')
            ->get();
            
        // Jeśli jest okres porównawczy
        $prevInvoices = null;
        if ($prevStartDate && $prevEndDate) {
            $prevInvoices = Invoice::whereBetween('issue_date', [$prevStartDate, $prevEndDate])
                ->with('items')
                ->get();
        }
        
        // Przygotuj statystyki
        $stats = $this->calculateInvoiceStats($invoices, $prevInvoices);
        
        // Przygotuj dane dla wykresów
        $charts = $this->prepareInvoiceCharts($invoices, $period);
        
        // Pobierz ostatnie faktury
        $latestInvoices = Invoice::orderBy('issue_date', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.billing.statistics', compact('stats', 'charts', 'latestInvoices', 'settings', 'period'));
    }
    
    /**
     * Wyświetla stronę ustawień faktur
     */
    public function invoiceSettings()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $settings = BillingSettings::getActive();
        
        return view('admin.billing.settings', compact('settings'));
    }
    
    /**
     * Wyświetla stronę do ręcznego generowania faktur
     */
    public function invoiceGeneratePage()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $settings = BillingSettings::getActive();
        $lastInvoice = Invoice::where('auto_generated', true)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $activeSubscriptions = \DB::table('user_subscriptions')
            ->join('users', 'user_subscriptions.user_id', '=', 'users.id')
            ->join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->where('user_subscriptions.status', 'active')
            ->select(
                'user_subscriptions.id', 
                'users.name as user_name', 
                'users.email as user_email',
                'subscription_plans.name as plan_name',
                'user_subscriptions.next_billing_date'
            )
            ->orderBy('user_subscriptions.next_billing_date')
            ->get();
            
        $pendingCount = $activeSubscriptions->where('next_billing_date', '<=', now())->count();
        
        return view('admin.billing.generate', compact('settings', 'lastInvoice', 'activeSubscriptions', 'pendingCount'));
    }
    
    /**
     * Aktualizuje ustawienia faktur
     */
    public function updateInvoiceSettings(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Walidacja danych
        $validated = $request->validate([
            'auto_generate' => 'sometimes|boolean',
            'generation_day' => 'required|integer|min:1|max:28',
            'invoice_prefix' => 'nullable|string|max:20',
            'invoice_suffix' => 'nullable|string|max:20',
            'reset_numbering' => 'sometimes|boolean',
            'payment_days' => 'required|integer|min:0|max:60',
            'default_currency' => 'required|string|size:3',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'vat_number' => 'nullable|string|max:20',
            'invoice_notes' => 'nullable|string|max:1000',
            'email_notifications' => 'sometimes|boolean',
        ]);
        
        // Pobierz lub utwórz ustawienia
        $settings = \App\Models\BillingSettings::first();
        if (!$settings) {
            $settings = new \App\Models\BillingSettings();
        }
        
        // Aktualizuj pola ustawień
        $settings->auto_generate = $request->has('auto_generate');
        $settings->generation_day = $validated['generation_day'];
        $settings->invoice_prefix = $validated['invoice_prefix'];
        $settings->invoice_suffix = $validated['invoice_suffix'];
        $settings->reset_numbering = $request->has('reset_numbering');
        $settings->payment_days = $validated['payment_days'];
        $settings->default_currency = $validated['default_currency'];
        $settings->default_tax_rate = $validated['default_tax_rate'];
        $settings->vat_number = $validated['vat_number'];
        $settings->invoice_notes = $validated['invoice_notes'];
        $settings->email_notifications = $request->has('email_notifications');
        
        // Zapisz ustawienia
        $settings->save();
        
        return redirect()->route('admin.billing.settings')
            ->with('success', 'Ustawienia faktur zostały zaktualizowane.');
    }

    /**
     * Wyświetla logi systemowe
     */
    public function systemLogs()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        // Przygotuj dane logów
        $logs = [];
        
        $logFiles = glob(storage_path('logs/*.log'));
        
        if (count($logFiles) > 0) {
            foreach($logFiles as $logFile) {
                $content = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
                
                foreach($content as $line) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)/', $line, $matches)) {
                        $logs[] = [
                            'date' => $matches[1],
                            'level' => strtolower($matches[3]),
                            'message' => $matches[4],
                            'context' => []
                        ];
                    }
                }
            }
            
            // Ogranicz ilość logów
            $logs = array_slice($logs, 0, 100);
        }
        
        return view('admin.system.logs', compact('logs'));
    }

    /**
     * Czyści logi systemowe.
     */
    public function clearSystemLogs()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        // Usuń pliki logów
        $logFiles = glob(storage_path('logs/*.log'));
        foreach($logFiles as $logFile) {
            if (is_file($logFile)) {
                // Zamiast usuwać plik, tworzymy pusty plik
                file_put_contents($logFile, '');
            }
        }

        return redirect()->route('admin.system.logs')->with('success', 'Logi systemowe zostały wyczyszczone.');
    }

    public function systemInfo()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Nieznany',
            'database' => DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) . ' ' . DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'timezone' => config('app.timezone'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
        
        return view('admin.system.info', compact('info'));
    }

    public function backupSystem()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $backupPath = storage_path('app/backups');
        $backups = [];
        
        if (file_exists($backupPath) && is_dir($backupPath)) {
            $files = glob($backupPath . '/*.zip');
            
            foreach ($files as $file) {
                $backups[] = [
                    'name' => basename($file),
                    'size' => $this->formatBytes(filesize($file)),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }
        
        return view('admin.system.backup', compact('backups'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function createBackup()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        try {
            // Wywołaj komendę kopii zapasowej
            \Artisan::call('backup:run');
            return redirect()->route('admin.system.backup')->with('success', 'Kopia zapasowa została utworzona.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia kopii zapasowej: ' . $e->getMessage());
            return redirect()->route('admin.system.backup')->with('error', 'Wystąpił błąd podczas tworzenia kopii zapasowej.');
        }
    }

    public function loginHistory(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        try {
            // Sprawdź czy tabela istnieje
            $tableExists = Schema::hasTable('login_histories');
            Log::info('Sprawdzanie tabeli login_histories', ['exists' => $tableExists]);

            if ($tableExists) {
                // Loguj początek zapytania
                Log::info('Rozpoczynanie pobierania historii logowań');
                
                try {
                    $query = \App\Models\LoginHistory::with('user');
                    
                    // Zapisz liczbę wszystkich rekordów przed filtrowaniem
                    $totalCount = $query->count();
                    Log::info('Liczba wszystkich rekordów w tabeli', ['count' => $totalCount]);
                    
                    // Filtrowanie po użytkowniku
                    if ($request->filled('user')) {
                        $userSearch = $request->input('user');
                        $query->whereHas('user', function($q) use ($userSearch) {
                            $q->where('name', 'like', "%{$userSearch}%")
                              ->orWhere('email', 'like', "%{$userSearch}%");
                        });
                    }
                    
                    // Filtrowanie po statusie
                    if ($request->filled('status')) {
                        $query->where('status', $request->input('status'));
                    }
                    
                    // Filtrowanie po dacie od
                    if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->input('date_from'));
                    }
                    
                    // Filtrowanie po dacie do
                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->input('date_to'));
                    }
                    
                    $loginHistory = $query->orderBy('created_at', 'desc')->paginate(15)
                                          ->appends($request->except('page'));
                    
                    // Loguj sukces
                    Log::info('Pobrano historię logowań', [
                        'count' => $loginHistory->count(),
                        'total' => $loginHistory->total()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Błąd podczas wykonywania zapytania do historii logowań', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Jeśli błąd dotyczy relacji lub struktury tabeli
                    $loginHistory = collect([]);
                }
            } else {
                Log::warning('Tabela login_histories nie istnieje');
                $loginHistory = collect([]);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania historii logowań', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $loginHistory = collect([]);
        }

        return view('admin.system.login-history', compact('loginHistory'));
    }

    public function details($type)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        try {
            if ($type === 'online') {
                $users = User::whereNotNull('last_seen_at')
                    ->where('last_seen_at', '>=', now()->subMinutes(5))
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Nigdy',
                            'is_online' => $user->isOnline(),
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowy typ szczegółów'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania szczegółów', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas pobierania danych'
            ], 500);
        }
    }

    public function onlineUsers()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        try {
            $users = User::whereNotNull('last_seen_at')
                ->where('last_seen_at', '>=', now()->subMinutes(5))
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Nigdy',
                        'is_online' => $user->isOnline(),
                    ];
                });

            // Sprawdź czy to jest żądanie AJAX czy standardowe
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
            }
            
            // Jeśli to nie jest AJAX, wyświetl widok
            return view('admin.users.online');
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania aktywnych użytkowników', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wystąpił błąd podczas pobierania danych'
                ], 500);
            }
            
            return view('admin.users.online')->with('error', 'Wystąpił błąd podczas pobierania danych');
        }
    }

    /**
     * Czyści cache związane ze statystykami subskrypcji
     */
    private function clearSubscriptionCache()
    {
        $cacheKeys = [
            'stats.active_subscriptions',
            'stats.active_subscriptions_value',
            'stats.today_subscriptions',
            'stats.today_subscriptions_value',
            'stats.month_subscriptions',
            'stats.month_subscriptions_value',
            'stats.year_subscriptions',
            'stats.year_subscriptions_value',
            'stats.total_active_subscriptions',
            'stats.total_active_value',
            'stats.prev_month',
            'revenue.today',
            'revenue.yesterday',
            'revenue.month',
            'revenue.last_month',
            'revenue.year',
            'revenue.last_year',
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Czyścimy również cache związane z miesięcznymi i rocznymi danymi
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Wyczyść cache miesięczne
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            Cache::forget('revenue.monthly.' . $date->format('Y-m'));
            Cache::forget('revenue.chart.' . $date->format('Y-m'));
            Cache::forget('revenue.plans.' . $date->format('Y-m'));
        }
        
        // Wyczyść cache kwartalne
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            Cache::forget('revenue.quarterly.' . $currentYear . 'q' . $quarter);
        }
        
        // Wyczyść cache roczne
        for ($i = 0; $i < 3; $i++) {
            $year = Carbon::now()->subYears($i)->year;
            Cache::forget('revenue.annual.' . $year);
            Cache::forget('revenue.plans.year.' . $year);
        }
    }

    /**
     * Wyświetla listę planów subskrypcji - wersja z Alpine.js
     */
    public function subscriptionsAlpine()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobierz plany subskrypcji
        $plans = Plan::orderBy('price', 'asc')->get();
        
        // Pobierz statystyki
        $stats = Cache::remember('admin_subscription_stats', 3600, function () {
            // Aktywne subskrypcje
            $activeSubscriptions = Subscription::where('status', 'active')->count();
            $totalSubscriptions = Subscription::count();
            
            // Statystyki subskrypcji według typu
            $manualSubscriptions = UserSubscription::where('subscription_type', UserSubscription::TYPE_MANUAL)->count();
            $automaticSubscriptions = UserSubscription::where('subscription_type', UserSubscription::TYPE_AUTOMATIC)->count();
            
            // Oblicz procenty
            $manualPercentage = $totalSubscriptions > 0 ? round(($manualSubscriptions / $totalSubscriptions) * 100) : 0;
            $automaticPercentage = $totalSubscriptions > 0 ? round(($automaticSubscriptions / $totalSubscriptions) * 100) : 0;
            
            // Przychód z aktywnych subskrypcji
            $activeSubscriptionsValue = Subscription::where('status', 'active')->sum('price');
            
            // Pobierz dane z poprzedniego miesiąca dla porównania
            $lastMonth = Carbon::now()->subMonth();
            $activeSubscriptionsLastMonth = Subscription::where('status', 'active')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count();
            
            // Oblicz zmianę procentową
            $subMonthlyChange = 0;
            if ($activeSubscriptionsLastMonth > 0) {
                $subMonthlyChange = round((($activeSubscriptions - $activeSubscriptionsLastMonth) / $activeSubscriptionsLastMonth) * 100);
            }
            
            // Przychód z subskrypcji w bieżącym miesiącu
            $monthSubscriptionsValue = Subscription::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('price');
            
            // Przychód z subskrypcji w poprzednim miesiącu
            $lastMonthSubscriptionsValue = Subscription::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('price');
            
            // Zmiana procentowa przychodu
            $revenueMonthlyChange = 0;
            if ($lastMonthSubscriptionsValue > 0) {
                $revenueMonthlyChange = round((($monthSubscriptionsValue - $lastMonthSubscriptionsValue) / $lastMonthSubscriptionsValue) * 100);
            }
            
            // Oblicz średnią wartość subskrypcji
            $avgSubValueLastMonth = $activeSubscriptionsLastMonth > 0 
                ? $lastMonthSubscriptionsValue / $activeSubscriptionsLastMonth 
                : 0;
            
            $avgSubValueCurrentMonth = $activeSubscriptions > 0 
                ? $activeSubscriptionsValue / $activeSubscriptions 
                : 0;
            
            // Zmiana procentowa średniej wartości subskrypcji
            $avgValueMonthlyChange = 0;
            if ($avgSubValueLastMonth > 0) {
                $avgValueMonthlyChange = round((($avgSubValueCurrentMonth - $avgSubValueLastMonth) / $avgSubValueLastMonth) * 100);
            }
            
            return [
                'active_subscriptions' => $activeSubscriptions,
                'active_subscriptions_value' => $activeSubscriptionsValue,
                'month_subscriptions_value' => $monthSubscriptionsValue,
                'sub_monthly_change' => $subMonthlyChange,
                'revenue_monthly_change' => $revenueMonthlyChange,
                'avg_value_monthly_change' => $avgValueMonthlyChange,
                'manual_subscriptions' => $manualSubscriptions,
                'automatic_subscriptions' => $automaticSubscriptions,
                'manual_percentage' => $manualPercentage,
                'automatic_percentage' => $automaticPercentage
            ];
        });
        
        return view('admin.subscriptions.alpine', [
            'plans' => $plans,
            'stats' => $stats,
            'plansJson' => json_encode($plans),
            'statsJson' => json_encode($stats)
        ]);
    }

    /**
     * Oblicza statystyki dla faktur
     */
    private function calculateInvoiceStats($invoices, $prevInvoices)
    {
        $stats = [
            'total_value' => $invoices->sum('gross_total'),
            'invoices_count' => $invoices->count(),
            'paid_count' => $invoices->where('status', 'paid')->count(),
            'paid_percentage' => $invoices->count() > 0 
                ? round(($invoices->where('status', 'paid')->count() / $invoices->count()) * 100) 
                : 0,
            'overdue_count' => $invoices->where('status', 'overdue')->count(),
            'overdue_percentage' => $invoices->count() > 0 
                ? round(($invoices->where('status', 'overdue')->count() / $invoices->count()) * 100) 
                : 0,
            
            // Domyślne wartości dla wzrostu/spadku
            'total_growth' => 0,
            'count_growth' => 0,
        ];
        
        // Oblicz wzrost/spadek w porównaniu do poprzedniego okresu
        if ($prevInvoices && $prevInvoices->count() > 0) {
            $prevTotal = $prevInvoices->sum('gross_total');
            $prevCount = $prevInvoices->count();
            
            if ($prevTotal > 0) {
                $stats['total_growth'] = round((($stats['total_value'] - $prevTotal) / $prevTotal) * 100);
            }
            
            if ($prevCount > 0) {
                $stats['count_growth'] = round((($stats['invoices_count'] - $prevCount) / $prevCount) * 100);
            }
        }
        
        return $stats;
    }

    /**
     * Przygotowuje dane dla wykresów faktur
     */
    private function prepareInvoiceCharts($invoices, $period)
    {
        $charts = [
            'monthly_revenue' => $this->prepareMonthlyRevenueChart($invoices, $period),
            'invoice_status' => $this->prepareInvoiceStatusChart($invoices),
        ];
        
        return $charts;
    }
    
    /**
     * Przygotowuje dane dla wykresu przychodów miesięcznych
     */
    private function prepareMonthlyRevenueChart($invoices, $period)
    {
        $result = [
            'labels' => [],
            'values' => []
        ];
        
        // Określ format grupowania i liczbę okresów w zależności od wybranego przedziału
        switch ($period) {
            case 'month':
                $format = 'd M';
                $groupBy = 'date';
                $periods = 30;
                break;
            case 'quarter':
                $format = 'W, M';
                $groupBy = 'week';
                $periods = 13;
                break;
            case 'year':
                $format = 'M Y';
                $groupBy = 'month';
                $periods = 12;
                break;
            case 'all':
            default:
                $format = 'M Y';
                $groupBy = 'month';
                $periods = 24; // Ostatnie 24 miesiące dla opcji "wszystkie"
                break;
        }
        
        // Grupuj dane według określonego formatu
        $groupedData = $invoices->groupBy(function ($invoice) use ($groupBy) {
            switch ($groupBy) {
                case 'date':
                    return $invoice->issue_date->format('Y-m-d');
                case 'week':
                    return $invoice->issue_date->format('Y-W');
                case 'month':
                default:
                    return $invoice->issue_date->format('Y-m');
            }
        });
        
        // Przygotuj dane dla wykresu, uwzględniając tylko określoną liczbę okresów
        $limitedData = $groupedData->take(-$periods);
        
        foreach ($limitedData as $key => $group) {
            // Ustal etykietę na osi X
            switch ($groupBy) {
                case 'date':
                    $date = \Carbon\Carbon::createFromFormat('Y-m-d', $key);
                    $label = $date->format($format);
                    break;
                case 'week':
                    [$year, $week] = explode('-', $key);
                    $date = \Carbon\Carbon::now()->setISODate($year, $week);
                    $label = 'Tydzień ' . $week . ', ' . $date->format('M');
                    break;
                case 'month':
                default:
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $key);
                    $label = $date->translatedFormat($format);
                    break;
            }
            
            $result['labels'][] = $label;
            $result['values'][] = $group->sum('gross_total');
        }
        
        return $result;
    }
    
    /**
     * Przygotowuje dane dla wykresu statusów faktur
     */
    private function prepareInvoiceStatusChart($invoices)
    {
        $result = [
            'labels' => ['Opłacone', 'Wystawione', 'Zaległe', 'Inne'],
            'values' => [
                $invoices->where('status', 'paid')->count(),
                $invoices->where('status', 'issued')->count(),
                $invoices->where('status', 'overdue')->count(),
                $invoices->whereNotIn('status', ['paid', 'issued', 'overdue'])->count(),
            ]
        ];
        
        return $result;
    }
} 
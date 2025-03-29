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

        $permissions = Permission::paginate(10);
        return view('admin.permissions.index', compact('permissions'));
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
        
        $plans = Plan::all();
        
        return view('admin.subscriptions.index', compact('plans'));
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
        
        $query = Subscription::with(['user', 'plan']);
        
        // Filtrowanie według statusu
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filtrowanie według planu
        if ($request->has('plan') && $request->plan) {
            $query->where('plan_id', $request->plan);
        }
        
        // Filtrowanie według typu subskrypcji
        if ($request->has('subscription_type') && $request->subscription_type) {
            $query->where('subscription_type', '=', $request->subscription_type);
        }
        
        // Wyszukiwanie po nazwie lub emailu użytkownika
        if ($request->has('search') && $request->search) {
            $search = '%' . $request->search . '%';
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }
        
        $subscriptions = $query->latest()->paginate(15);
        $plans = Plan::all();
        
        // Przygotuj statystyki
        $stats = [
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
        ];
        
        return view('admin.subscriptions.users', compact('subscriptions', 'plans', 'stats'));
    }

    /**
     * Shows form to create a new user subscription
     */
    public function createUserSubscription()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $users = User::all();
        $plans = Plan::where('is_active', true)->get();
        
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
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'subscription_type' => 'required|in:manual,automatic',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,pending,cancelled,expired',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'next_payment_date' => 'nullable|date|after_or_equal:start_date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        $subscription = new Subscription();
        $subscription->user_id = $request->user_id;
        $subscription->plan_id = $request->plan_id;
        $subscription->subscription_type = $request->subscription_type;
        $subscription->price = $request->price;
        $subscription->status = $request->status;
        $subscription->start_date = $request->start_date;
        $subscription->end_date = $request->end_date;
        $subscription->next_payment_date = $request->next_payment_date;
        $subscription->payment_method = $request->payment_method;
        $subscription->notes = $request->notes;
        $subscription->save();
        
        return redirect()->route('admin.subscriptions.users')->with('success', 'Subskrypcja została utworzona.');
    }

    /**
     * Shows form to edit a user subscription
     */
    public function editUserSubscription(Subscription $subscription)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $users = User::all();
        $plans = Plan::all();
        
        return view('admin.subscriptions.edit-user-subscription', compact('subscription', 'users', 'plans'));
    }
    
    /**
     * Updates a user subscription
     */
    public function updateUserSubscription(Request $request, Subscription $subscription)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'subscription_type' => 'required|in:manual,automatic',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,pending,cancelled,expired',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'next_payment_date' => 'nullable|date|after_or_equal:start_date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        $subscription->user_id = $request->user_id;
        $subscription->plan_id = $request->plan_id;
        $subscription->subscription_type = $request->subscription_type;
        $subscription->price = $request->price;
        $subscription->status = $request->status;
        $subscription->start_date = $request->start_date;
        $subscription->end_date = $request->end_date;
        $subscription->next_payment_date = $request->next_payment_date;
        $subscription->payment_method = $request->payment_method;
        $subscription->notes = $request->notes;
        $subscription->save();
        
        return redirect()->route('admin.subscriptions.users')->with('success', 'Subskrypcja została zaktualizowana.');
    }

    /**
     * Removes a user subscription
     */
    public function deleteUserSubscription(Subscription $subscription)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $subscription->delete();
        
        return redirect()->route('admin.subscriptions.users')->with('success', 'Subskrypcja została usunięta.');
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
            $activeSubscriptions = Subscription::where('status', 'active')->count();
            $totalSubscriptions = Subscription::count();
            
            // Statystyki według typu
            $manualSubscriptions = UserSubscription::where('subscription_type', UserSubscription::TYPE_MANUAL)->count();
            $automaticSubscriptions = UserSubscription::where('subscription_type', UserSubscription::TYPE_AUTOMATIC)->count();
            
            // Oblicz procenty
            $manualPercentage = $totalSubscriptions > 0 ? round(($manualSubscriptions / $totalSubscriptions) * 100) : 0;
            $automaticPercentage = $totalSubscriptions > 0 ? round(($automaticSubscriptions / $totalSubscriptions) * 100) : 0;
            
            // Przychód z aktywnych subskrypcji
            $activeSubscriptionsValue = Subscription::where('status', 'active')->sum('price');
            
            // Statystyki za bieżący miesiąc
            $currentMonth = Carbon::now();
            $currentMonthStart = $currentMonth->copy()->startOfMonth();
            $currentMonthEnd = $currentMonth->copy()->endOfMonth();
            
            $subscriptionsThisMonth = Subscription::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
            $revenueThisMonth = Subscription::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->sum('price');
            
            // Statystyki za poprzedni miesiąc
            $lastMonth = $currentMonth->copy()->subMonth();
            $lastMonthStart = $lastMonth->copy()->startOfMonth();
            $lastMonthEnd = $lastMonth->copy()->endOfMonth();
            
            $subscriptionsLastMonth = Subscription::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
            $revenueLastMonth = Subscription::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('price');
            
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
            $planStats = Plan::withCount(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])->get()->map(function($plan) {
                return [
                    'name' => $plan->name,
                    'active_count' => $plan->subscriptions_count,
                    'revenue' => $plan->subscriptions->where('status', 'active')->sum('price')
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
                    'subscriptions' => Subscription::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                    'revenue' => Subscription::whereBetween('created_at', [$monthStart, $monthEnd])->sum('price')
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
    public function subscriptionPaymentDetails($paymentId)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tymczasowo, zwróć widok z pustymi danymi
        return view('admin.subscriptions.payment-details', ['payment' => null]);
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

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania aktywnych użytkowników', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas pobierania danych'
            ], 500);
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
} 
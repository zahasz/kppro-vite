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
            $manualSubscriptions = Subscription::where('subscription_type', Subscription::TYPE_MANUAL)->count();
            $automaticSubscriptions = Subscription::where('subscription_type', Subscription::TYPE_AUTOMATIC)->count();
            
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

    // Metody zarządzania subskrypcjami
    
    /**
     * Wyświetla listę planów subskrypcji
     */
    public function subscriptions()
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
            $manualSubscriptions = Subscription::where('subscription_type', Subscription::TYPE_MANUAL)->count();
            $automaticSubscriptions = Subscription::where('subscription_type', Subscription::TYPE_AUTOMATIC)->count();
            
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
        
        return view('admin.subscriptions.index', compact('plans', 'stats'));
    }
    
    /**
     * Wyświetla formularz tworzenia nowego planu subskrypcji
     */
    public function createSubscription()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Przekazanie do widoku typów subskrypcji dostępnych w systemie
        $subscriptionTypes = [
            'manual' => 'Ręczna (bez automatycznego odnowienia)',
            'automatic' => 'Automatyczna (z automatycznym odnowieniem)',
            'both' => 'Oba typy (użytkownik wybiera przy zakupie)'
        ];
        
        return view('admin.subscriptions.create', compact('subscriptionTypes'));
    }
    
    /**
     * Zapisuje nowy plan subskrypcji
     */
    public function storeSubscription(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Walidacja danych
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'interval' => 'required|string|in:monthly,quarterly,biannually,annually,lifetime',
            'trial_period_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'subscription_type' => 'required|string|in:manual,automatic,both',
            'is_active' => 'boolean',
        ]);
        
        // Utworzenie nowego planu subskrypcji
        $plan = new Plan();
        $plan->name = $validated['name'];
        $plan->description = $validated['description'];
        $plan->price = $validated['price'];
        $plan->interval = $validated['interval'];
        $plan->trial_period_days = $validated['trial_period_days'] ?? 0;
        $plan->subscription_type = $validated['subscription_type'];
        $plan->features = $validated['features'] ?? [];
        $plan->is_active = $request->has('is_active');
        $plan->save();
        
        // Wyczyść cache związany ze statystykami subskrypcji
        $this->clearSubscriptionCache();
        
        return redirect()->route('admin.subscriptions.index')
                         ->with('success', 'Plan subskrypcji został dodany pomyślnie.');
    }
    
    /**
     * Wyświetla formularz edycji planu subskrypcji
     */
    public function editSubscription($id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie pobieranie planu subskrypcji z bazy danych
        // Na razie przekazujemy pusty plan do widoku
        
        return view('admin.subscriptions.create');
    }
    
    /**
     * Aktualizuje plan subskrypcji
     */
    public function updateSubscription(Request $request, $id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie logika aktualizacji planu subskrypcji
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.index')
                         ->with('success', 'Plan subskrypcji został zaktualizowany.');
    }
    
    /**
     * Usuwa plan subskrypcji
     */
    public function deleteSubscription($id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie logika usuwania planu subskrypcji
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.index')
                         ->with('success', 'Plan subskrypcji został usunięty.');
    }
    
    /**
     * Wyświetla listę subskrypcji użytkowników
     */
    public function userSubscriptions()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobierz subskrypcje z relacjami do użytkownika i planu
        $subscriptions = Subscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Grupowanie subskrypcji według statusu
        $activeCount = Subscription::where('status', 'active')->count();
        $pendingCount = Subscription::where('status', 'pending')->count();
        $cancelledCount = Subscription::where('status', 'cancelled')->count();
        $expiredCount = Subscription::where('status', 'expired')->count();
        
        // Grupowanie według typu subskrypcji
        $manualCount = Subscription::where('subscription_type', Subscription::TYPE_MANUAL)->count();
        $automaticCount = Subscription::where('subscription_type', Subscription::TYPE_AUTOMATIC)->count();
        
        // Pobierz statystyki przychodów
        $monthlyRevenue = Subscription::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('price');
            
        $yearlyRevenue = Subscription::whereYear('created_at', Carbon::now()->year)
            ->sum('price');
            
        $totalActiveRevenue = Subscription::where('status', 'active')->sum('price');
        
        // Pobierz listę planów dla filtra
        $plans = Plan::where('is_active', true)->get();
        
        return view('admin.subscriptions.users', compact(
            'subscriptions', 
            'activeCount', 
            'pendingCount', 
            'cancelledCount', 
            'expiredCount',
            'manualCount',
            'automaticCount',
            'monthlyRevenue',
            'yearlyRevenue',
            'totalActiveRevenue',
            'plans'
        ));
    }
    
    /**
     * Wyświetla formularz przypisania subskrypcji do użytkownika
     */
    public function createUserSubscription()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobieramy listę użytkowników i planów subskrypcji
        $users = User::where('is_active', true)->get();
        $plans = Plan::where('is_active', true)->get();
        
        return view('admin.subscriptions.create-user-subscription', compact('users', 'plans'));
    }
    
    /**
     * Zapisuje nową subskrypcję użytkownika
     */
    public function storeUserSubscription(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'subscription_type' => 'required|in:manual,automatic',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,pending',
            'payment_method' => 'required|string',
            'renewal_status' => 'nullable|in:enabled,disabled'
        ]);
        
        try {
            // Pobierz plan
            $plan = Plan::findOrFail($request->plan_id);
            
            // Oblicz datę końcową na podstawie typu i interwału planu
            $startDate = Carbon::parse($request->start_date);
            $endDate = null;
            
            // Jeśli to subskrypcja ręczna lub automatyczna z określonym interwałem
            if ($request->subscription_type === Subscription::TYPE_MANUAL || 
                ($request->subscription_type === Subscription::TYPE_AUTOMATIC && $plan->interval !== 'lifetime')) {
                
                switch ($plan->interval) {
                    case 'monthly':
                        $endDate = $startDate->copy()->addMonth();
                        break;
                    case 'quarterly':
                        $endDate = $startDate->copy()->addMonths(3);
                        break;
                    case 'biannually':
                        $endDate = $startDate->copy()->addMonths(6);
                        break;
                    case 'annually':
                        $endDate = $startDate->copy()->addYear();
                        break;
                    default:
                        $endDate = null;
                }
            }
            
            // Ustaw datę następnej płatności dla automatycznych subskrypcji
            $nextPaymentDate = null;
            if ($request->subscription_type === Subscription::TYPE_AUTOMATIC && $endDate) {
                $nextPaymentDate = $endDate->copy();
            }
            
            // Utwórz subskrypcję
            $subscription = new Subscription();
            $subscription->user_id = $request->user_id;
            $subscription->plan_id = $request->plan_id;
            $subscription->status = $request->status;
            $subscription->price = $request->price;
            $subscription->start_date = $startDate;
            $subscription->end_date = $endDate;
            $subscription->subscription_type = $request->subscription_type;
            $subscription->payment_method = $request->payment_method;
            
            // Dla automatycznych subskrypcji
            if ($request->subscription_type === Subscription::TYPE_AUTOMATIC) {
                $subscription->renewal_status = $request->renewal_status ?? Subscription::RENEWAL_ENABLED;
                $subscription->next_payment_date = $nextPaymentDate;
            } else {
                $subscription->renewal_status = Subscription::RENEWAL_DISABLED;
            }
            
            if ($request->has('trial_ends_at') && $request->trial_ends_at) {
                $subscription->trial_ends_at = Carbon::parse($request->trial_ends_at);
            }
            
            $subscription->save();
            
            // Rejestracja płatności jeśli podano
            if ($request->has('payment_id') && $request->payment_id) {
                $subscription->last_payment_id = $request->payment_id;
                $subscription->save();
                
                // Tutaj można by było logikę rejestracji płatności w systemie
            }
            
            // Wyczyść cache związane ze statystykami subskrypcji
            $this->clearSubscriptionCache();
        
        return redirect()->route('admin.subscriptions.users')
                         ->with('success', 'Subskrypcja została przypisana do użytkownika.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia subskrypcji: ' . $e->getMessage());
            return redirect()->back()
                    ->withInput()
                    ->with('error', 'Wystąpił błąd podczas tworzenia subskrypcji: ' . $e->getMessage());
        }
    }
    
    /**
     * Wyświetla formularz edycji subskrypcji użytkownika
     */
    public function editUserSubscription($id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        try {
            $subscription = Subscription::with(['user', 'plan'])->findOrFail($id);
            $users = User::where('is_active', true)->get();
            $plans = Plan::where('is_active', true)->get();
            
            return view('admin.subscriptions.edit-user-subscription', compact('subscription', 'users', 'plans'));
        } catch (\Exception $e) {
            Log::error('Błąd podczas edycji subskrypcji: ' . $e->getMessage());
            return redirect()->route('admin.subscriptions.users')
                    ->with('error', 'Nie znaleziono subskrypcji o podanym ID.');
        }
    }
    
    /**
     * Aktualizuje subskrypcję użytkownika
     */
    public function updateUserSubscription(Request $request, $id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'subscription_type' => 'required|in:manual,automatic',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,pending,cancelled',
            'payment_method' => 'required|string',
            'renewal_status' => 'nullable|in:enabled,disabled'
        ]);
        
        try {
            $subscription = Subscription::findOrFail($id);
            $plan = Plan::findOrFail($request->plan_id);
            
            // Oblicz datę końcową na podstawie typu i interwału planu
            $startDate = Carbon::parse($request->start_date);
            $endDate = null;
            
            // Tylko jeśli zmieniono datę rozpoczęcia lub plan, przelicz datę końcową
            if ($startDate->format('Y-m-d') !== $subscription->start_date->format('Y-m-d') || 
                $request->plan_id != $subscription->plan_id) {
                
                // Jeśli to subskrypcja ręczna lub automatyczna z określonym interwałem
                if ($request->subscription_type === Subscription::TYPE_MANUAL || 
                    ($request->subscription_type === Subscription::TYPE_AUTOMATIC && $plan->interval !== 'lifetime')) {
                    
                    switch ($plan->interval) {
                        case 'monthly':
                            $endDate = $startDate->copy()->addMonth();
                            break;
                        case 'quarterly':
                            $endDate = $startDate->copy()->addMonths(3);
                            break;
                        case 'biannually':
                            $endDate = $startDate->copy()->addMonths(6);
                            break;
                        case 'annually':
                            $endDate = $startDate->copy()->addYear();
                            break;
                        default:
                            $endDate = null;
                    }
                }
            } else {
                // Zachowaj oryginalną datę końcową
                $endDate = $subscription->end_date;
            }
            
            // Zmiana typu subskrypcji z automatycznej na ręczną lub odwrotnie
            if ($request->subscription_type !== $subscription->subscription_type) {
                if ($request->subscription_type === Subscription::TYPE_AUTOMATIC) {
                    // Z ręcznej na automatyczną - włącz odnowienie
                    $subscription->renewal_status = $request->renewal_status ?? Subscription::RENEWAL_ENABLED;
                    
                    // Ustaw datę następnej płatności
                    if ($endDate) {
                        $subscription->next_payment_date = $endDate->copy();
                    }
                } else {
                    // Z automatycznej na ręczną - wyłącz odnowienie
                    $subscription->renewal_status = Subscription::RENEWAL_DISABLED;
                    $subscription->next_payment_date = null;
                }
            } elseif ($request->subscription_type === Subscription::TYPE_AUTOMATIC) {
                // Aktualizacja ustawień odnowienia dla istniejącej subskrypcji automatycznej
                $subscription->renewal_status = $request->renewal_status ?? $subscription->renewal_status;
                
                // Jeśli zmieniono datę końcową, zaktualizuj datę następnej płatności
                if ($endDate && $endDate != $subscription->end_date) {
                    $subscription->next_payment_date = $endDate->copy();
                }
            }
            
            // Aktualizuj dane subskrypcji
            $subscription->user_id = $request->user_id;
            $subscription->plan_id = $request->plan_id;
            $subscription->status = $request->status;
            $subscription->price = $request->price;
            $subscription->start_date = $startDate;
            $subscription->end_date = $endDate;
            $subscription->subscription_type = $request->subscription_type;
            $subscription->payment_method = $request->payment_method;
            
            // Dla anulowanych subskrypcji, zapisz datę anulowania, jeśli jej nie ma
            if ($request->status === 'cancelled' && !$subscription->cancelled_at) {
                $subscription->cancelled_at = now();
            }
            
            // Dla subskrypcji z powrotem aktywowanych
            if ($request->status === 'active' && $subscription->cancelled_at) {
                // Opcjonalnie: kasuj datę anulowania
                // $subscription->cancelled_at = null;
            }
            
            if ($request->has('trial_ends_at') && $request->trial_ends_at) {
                $subscription->trial_ends_at = Carbon::parse($request->trial_ends_at);
            } elseif ($request->has('trial_ends_at') && !$request->trial_ends_at) {
                $subscription->trial_ends_at = null;
            }
            
            $subscription->save();
            
            // Wyczyść cache związane ze statystykami subskrypcji
            $this->clearSubscriptionCache();
        
        return redirect()->route('admin.subscriptions.users')
                         ->with('success', 'Subskrypcja użytkownika została zaktualizowana.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji subskrypcji: ' . $e->getMessage());
            return redirect()->back()
                    ->withInput()
                    ->with('error', 'Wystąpił błąd podczas aktualizacji subskrypcji: ' . $e->getMessage());
        }
    }
    
    /**
     * Usuwa subskrypcję użytkownika
     */
    public function deleteUserSubscription($id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie logika usuwania subskrypcji użytkownika
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.users')
                         ->with('success', 'Subskrypcja użytkownika została usunięta.');
    }
    
    /**
     * Wyświetla historię płatności subskrypcji
     */
    public function subscriptionPayments()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie pobieranie historii płatności z bazy danych
        // Na razie używamy widoku z przykładowymi danymi
        
        return view('admin.subscriptions.payments');
    }
    
    /**
     * Wyświetla szczegóły płatności
     */
    public function showPaymentDetails($id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie pobieranie szczegółów płatności z bazy danych
        // Na razie używamy widoku z przykładowymi danymi
        
        return view('admin.subscriptions.payment-details');
    }
    
    /**
     * Procesuje zwrot płatności
     */
    public function refundPayment(Request $request, $id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj kod do zwrotu płatności
        return redirect()->back()->with('success', 'Zwrot płatności został zrealizowany.');
    }

    /**
     * Wyświetla stronę z powiadomieniami subskrypcji
     */
    public function subscriptionNotifications()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobieranie powiadomień subskrypcji
        // Jeśli model powiadomień nie istnieje, można stworzyć dane próbne
        $notifications = collect([
            (object)[
                'id' => 1,
                'title' => 'Nowa subskrypcja',
                'message' => 'Jan Kowalski wykupił Plan Premium',
                'created_at' => now()->subMinutes(5),
                'read' => false
            ],
            (object)[
                'id' => 2,
                'title' => 'Zakończona subskrypcja',
                'message' => 'Subskrypcja Anna Nowak (Plan Basic) wygasła',
                'created_at' => now()->subHours(2),
                'read' => false
            ],
            (object)[
                'id' => 3,
                'title' => 'Odnowienie subskrypcji',
                'message' => 'Tomasz Wiśniewski odnowił Plan Professional',
                'created_at' => now()->subDays(1),
                'read' => true
            ],
        ]);
        
        return view('admin.subscriptions.notifications', compact('notifications'));
    }

    /**
     * Wyświetla dashboard przychodów
     */
    public function revenueDashboard()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobierz statystyki przychodów
        $revenueStats = Cache::remember('revenue_dashboard_stats', 3600, function () {
            // Pobierz dzisiejsze przychody
            $totalToday = Subscription::whereDate('created_at', Carbon::today())->sum('price');
            $totalYesterday = Subscription::whereDate('created_at', Carbon::yesterday())->sum('price');
            
            // Pobierz przychody z bieżącego miesiąca
            $totalMonth = Subscription::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('price');
                
            // Pobierz przychody z poprzedniego miesiąca
            $totalLastMonth = Subscription::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('price');
                
            // Pobierz przychody z bieżącego roku
            $totalYear = Subscription::whereYear('created_at', Carbon::now()->year)
                ->sum('price');
                
            // Pobierz przychody z poprzedniego roku
            $totalLastYear = Subscription::whereYear('created_at', Carbon::now()->subYear()->year)
                ->sum('price');
                
            // Pobierz statystyki subskrypcji według typu
            $manualCount = Subscription::where('subscription_type', Subscription::TYPE_MANUAL)->count();
            $automaticCount = Subscription::where('subscription_type', Subscription::TYPE_AUTOMATIC)->count();
            $totalCount = $manualCount + $automaticCount;
            
            // Oblicz wartości każdego typu
            $manualValue = Subscription::where('subscription_type', Subscription::TYPE_MANUAL)->sum('price');
            $automaticValue = Subscription::where('subscription_type', Subscription::TYPE_AUTOMATIC)->sum('price');
            $totalValue = $manualValue + $automaticValue;
            
            // Oblicz procenty
            $manualPercentage = $totalValue > 0 ? round(($manualValue / $totalValue) * 100) : 0;
            $automaticPercentage = $totalValue > 0 ? round(($automaticValue / $totalValue) * 100) : 0;
            
            // Oblicz porównania procentowe
            $comparedYesterday = $totalYesterday > 0 
                ? number_format(($totalToday - $totalYesterday) / $totalYesterday * 100, 1) . '%' 
                : 'brak danych porównawczych';
                
            if ($totalYesterday > 0 && $totalToday > $totalYesterday) {
                $comparedYesterday = '+' . $comparedYesterday;
            }
            
            $comparedLastMonth = $totalLastMonth > 0 
                ? number_format(($totalMonth - $totalLastMonth) / $totalLastMonth * 100, 1) . '%' 
                : 'brak danych porównawczych';
                
            if ($totalLastMonth > 0 && $totalMonth > $totalLastMonth) {
                $comparedLastMonth = '+' . $comparedLastMonth;
            }
            
            $comparedLastYear = $totalLastYear > 0 
                ? number_format(($totalYear - $totalLastYear) / $totalLastYear * 100, 1) . '%' 
                : 'brak danych porównawczych';
                
            if ($totalLastYear > 0 && $totalYear > $totalLastYear) {
                $comparedLastYear = '+' . $comparedLastYear;
            }
            
            return [
                'total_today' => $totalToday,
                'total_yesterday' => $totalYesterday,
                'total_month' => $totalMonth,
                'total_last_month' => $totalLastMonth,
                'total_year' => $totalYear,
                'total_last_year' => $totalLastYear,
                'compared_yesterday' => $comparedYesterday,
                'compared_last_month' => $comparedLastMonth,
                'compared_last_year' => $comparedLastYear,
                'manual_count' => $manualCount,
                'automatic_count' => $automaticCount,
                'manual_value' => $manualValue,
                'automatic_value' => $automaticValue,
                'manual_percentage' => $manualPercentage,
                'automatic_percentage' => $automaticPercentage
            ];
        });
        
        // Dane dla wykresu miesięcznego
        $chartData = Cache::remember('revenue_monthly_chart', 3600, function () {
            $data = [];
            $data['months'] = [];
            $data['values'] = [];
            
            // Pobierz dane z ostatnich 6 miesięcy
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthName = $this->getPolishMonthName($month->month);
                
                $data['months'][] = $monthName;
                
                $value = Subscription::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('price');
                    
                $data['values'][] = $value;
            }
            
            return $data;
        });
        
        return view('admin.revenue.dashboard', compact('revenueStats', 'chartData'));
    }
    
    /**
     * Zwraca polską nazwę miesiąca na podstawie numeru
     */
    private function getPolishMonthName($monthNumber)
    {
        $months = [
            1 => 'Styczeń',
            2 => 'Luty',
            3 => 'Marzec',
            4 => 'Kwiecień',
            5 => 'Maj',
            6 => 'Czerwiec',
            7 => 'Lipiec',
            8 => 'Sierpień',
            9 => 'Wrzesień',
            10 => 'Październik',
            11 => 'Listopad',
            12 => 'Grudzień'
        ];
        
        return $months[$monthNumber] ?? 'Nieznany';
    }
    
    /**
     * Wyświetla raporty miesięczne przychodów
     */
    public function revenueMonthly()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobieranie danych o rzeczywistych przychodach miesięcznych
        $monthlyReports = collect();
        $currentMonth = Carbon::now();
        
        // Pobieramy dane z ostatnich 6 miesięcy
        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = Carbon::create($month->year, $month->month, 1, 0, 0, 0);
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            // Pobierz dane dla tego miesiąca z cache lub z bazy
            $monthData = Cache::remember('revenue.monthly.'.$month->format('Y-m'), now()->addDay(), function () use ($month, $monthStart, $monthEnd) {
                $totalRevenue = Subscription::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('price');
                
                $subscriptionsCount = Subscription::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();
                
                // Poprzedni miesiąc do obliczeń wzrostu
                $prevMonth = $month->copy()->subMonth();
                $prevMonthStart = Carbon::create($prevMonth->year, $prevMonth->month, 1, 0, 0, 0);
                $prevMonthEnd = $prevMonthStart->copy()->endOfMonth();
                
                $prevTotalRevenue = Subscription::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
                    ->sum('price');
                
                // Obliczenie wzrostu
                $growth = 0;
                if ($prevTotalRevenue > 0) {
                    $growth = (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100;
                }
                
                return [
                    'total' => $totalRevenue,
                    'subscriptions_count' => $subscriptionsCount,
                    'growth' => $growth,
                ];
            });
            
            // Format wzrostu
            $growthFormatted = $monthData['growth'] == 0 ? '0%' : 
                               ($monthData['growth'] > 0 ? '+' : '') . 
                               number_format($monthData['growth'], 1) . '%';
            
            // Dodaj do kolekcji
            $monthlyReports->push((object)[
                'month' => $this->getPolishMonthName($month->month) . ' ' . $month->year,
                'total' => $monthData['total'],
                'subscriptions_count' => $monthData['subscriptions_count'],
                'growth' => $growthFormatted,
            ]);
        }
        
        // Pobieranie szczegółowych danych dla bieżącego miesiąca
        $currentMonthData = $monthlyReports->first();
        
        // Dane dla wykresów dziennych w bieżącym miesiącu
        $daysInMonth = $currentMonth->daysInMonth;
        $dailyData = [];
        $dailyLabels = [];
        
        // Zbieramy dane dzienne dla bieżącego miesiąca
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($currentMonth->year, $currentMonth->month, $day);
            
            if ($date->isFuture()) {
                break; // Nie pobieramy danych dla przyszłych dni
            }
            
            $dailyLabels[] = $day; // Numer dnia miesiąca
            
            $dailyRevenue = Cache::remember('revenue.daily.'.$date->format('Y-m-d'), now()->addHours(12), function () use ($date) {
                return Subscription::whereDate('created_at', $date->format('Y-m-d'))
                    ->sum('price');
            });
            
            $dailyData[] = round($dailyRevenue, 2);
        }
        
        // Dane o planach subskrypcji za bieżący miesiąc
        $planData = Cache::remember('revenue.plans.'.$currentMonth->format('Y-m'), now()->addDay(), function () use ($monthStart, $monthEnd) {
            $plansRevenue = DB::table('subscriptions')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->select('plans.name', DB::raw('count(subscriptions.id) as count'), DB::raw('sum(subscriptions.price) as total'))
                ->whereBetween('subscriptions.created_at', [$monthStart, $monthEnd])
                ->groupBy('plans.name')
                ->get();
                
            return $plansRevenue;
        });
        
        // Pobierz nazwę i rok wybranego miesiąca (domyślnie bieżący)
        $selectedMonth = request('month', $currentMonth->month);
        $selectedYear = request('year', $currentMonth->year);
        $monthName = $this->getPolishMonthName($selectedMonth);
        
        // Podsumowanie dla wybranego miesiąca
        $summary = [
            'total' => $currentMonthData->total ?? 0,
            'new_subscriptions' => $currentMonthData->subscriptions_count ?? 0,
            'average' => $currentMonthData->subscriptions_count > 0 ? 
                         $currentMonthData->total / $currentMonthData->subscriptions_count : 0,
        ];
        
        // Wykresy dla wybranego miesiąca
        $chartData = [
            'days' => $dailyLabels,
            'values' => $dailyData,
            'plans' => $planData,
        ];
        
        return view('admin.revenue.monthly', compact('monthlyReports', 'summary', 'chartData', 'monthName', 'year'));
    }
    
    /**
     * Wyświetla raporty roczne przychodów
     */
    public function revenueAnnual()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobieranie rzeczywistych danych o przychodach rocznych
        $annualReports = collect();
        
        // Pobieramy dane z ostatnich 3 lat
        for ($i = 0; $i < 3; $i++) {
            $year = Carbon::now()->subYears($i)->year;
            $yearStart = Carbon::create($year, 1, 1, 0, 0, 0);
            $yearEnd = Carbon::create($year, 12, 31, 23, 59, 59);
            
            // Pobierz dane dla tego roku z cache lub z bazy
            $yearData = Cache::remember('revenue.annual.'.$year, now()->addDays(7), function () use ($year, $yearStart, $yearEnd) {
                $totalRevenue = Subscription::whereBetween('created_at', [$yearStart, $yearEnd])
                    ->sum('price');
                
                $subscriptionsCount = Subscription::whereBetween('created_at', [$yearStart, $yearEnd])
                    ->count();
                
                // Poprzedni rok do obliczeń wzrostu
                $prevYear = $year - 1;
                $prevYearStart = Carbon::create($prevYear, 1, 1, 0, 0, 0);
                $prevYearEnd = Carbon::create($prevYear, 12, 31, 23, 59, 59);
                
                $prevTotalRevenue = Subscription::whereBetween('created_at', [$prevYearStart, $prevYearEnd])
                    ->sum('price');
                
                // Obliczenie wzrostu
                $growth = 0;
                if ($prevTotalRevenue > 0) {
                    $growth = (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100;
                }
                
                return [
                    'total' => $totalRevenue,
                    'subscriptions_count' => $subscriptionsCount,
                    'growth' => $growth,
                ];
            });
            
            // Format wzrostu
            $growthFormatted = $yearData['growth'] == 0 ? '0%' : 
                              ($yearData['growth'] > 0 ? '+' : '') . 
                              number_format($yearData['growth'], 1) . '%';
            
            // Dodaj do kolekcji
            $annualReports->push((object)[
                'year' => (string)$year,
                'total' => $yearData['total'],
                'subscriptions_count' => $yearData['subscriptions_count'],
                'growth' => $growthFormatted,
            ]);
        }
        
        // Pobieranie danych kwartalnych dla aktualnego roku
        $currentYear = Carbon::now()->year;
        $quarterlyData = [
            'revenue' => [
                'q1' => 0, 'q2' => 0, 'q3' => 0, 'q4' => 0,
            ],
            'new_subs' => [
                'q1' => 0, 'q2' => 0, 'q3' => 0, 'q4' => 0,
            ],
        ];
        
        // Pobierz dane z poszczególnych kwartałów
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $quarter * 3;
            
            $quarterStart = Carbon::create($currentYear, $startMonth, 1, 0, 0, 0);
            $quarterEnd = Carbon::create($currentYear, $endMonth, 1)->endOfMonth();
            
            // Jeśli kwartał jest w przyszłości, pomijamy
            if ($quarterStart->isFuture()) {
                continue;
            }
            
            // Pobierz dane kwartalne
            $quarterData = Cache::remember('revenue.quarterly.'.$currentYear.'q'.$quarter, now()->addDays(3), function () use ($quarterStart, $quarterEnd) {
                $quarterRevenue = Subscription::whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->sum('price');
                
                $quarterNewSubs = Subscription::whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->count();
                
                return [
                    'revenue' => $quarterRevenue,
                    'new_subs' => $quarterNewSubs,
                ];
            });
            
            $quarterlyData['revenue']['q'.$quarter] = round($quarterData['revenue'], 2);
            $quarterlyData['new_subs']['q'.$quarter] = $quarterData['new_subs'];
        }
        
        // Dane dla wykresu miesięcznych przychodów w aktualnym roku
        $monthlyValues = [];
        $planDistribution = [];
        
        // Pobierz dane o miesięcznych przychodach
        for ($month = 1; $month <= 12; $month++) {
            // Jeśli miesiąc jest w przyszłości, ustawiam 0
            if ($month > Carbon::now()->month) {
                $monthlyValues[] = 0;
                continue;
            }
            
            $monthStart = Carbon::create($currentYear, $month, 1, 0, 0, 0);
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            $monthlyRevenue = Cache::remember('revenue.monthly.'.$currentYear.'-'.$month, now()->addDays(2), function () use ($monthStart, $monthEnd) {
                return Subscription::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('price');
            });
            
            $monthlyValues[] = round($monthlyRevenue, 2);
        }
        
        // Pobieranie danych o planach
        $plansData = Cache::remember('revenue.plans.year.'.$currentYear, now()->addDays(3), function () use ($currentYear) {
            $yearStart = Carbon::create($currentYear, 1, 1, 0, 0, 0);
            $yearEnd = Carbon::create($currentYear, 12, 31, 23, 59, 59);
            
            return DB::table('subscriptions')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->select('plans.name', DB::raw('count(subscriptions.id) as count'), DB::raw('sum(subscriptions.price) as total'))
                ->whereBetween('subscriptions.created_at', [$yearStart, $yearEnd])
                ->groupBy('plans.name')
                ->get();
        });
        
        // Przygotowanie danych dla wykresu podziału planów
        $planNames = [];
        $planCounts = [];
        $planColors = ['#546E95', '#7A9CC6', '#A5B9D9', '#D0DAE8', '#EDF2F7'];
        
        foreach ($plansData as $index => $plan) {
            $planNames[] = $plan->name;
            $planCounts[] = $plan->count;
        }
        
        // Dane dla wykresów
        $chartData = [
            'monthly_values' => $monthlyValues,
            'plan_names' => $planNames,
            'plan_counts' => $planCounts,
            'plan_colors' => array_slice($planColors, 0, count($planNames)),
        ];
        
        return view('admin.revenue.annual', compact('annualReports', 'quarterlyData', 'chartData'));
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

    public function loginHistory()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        try {
            if (Schema::hasTable('login_histories')) {
                $loginHistory = DB::table('login_histories')
                    ->join('users', 'login_histories.user_id', '=', 'users.id')
                    ->select('login_histories.*', 'users.name', 'users.email')
                    ->orderBy('login_histories.created_at', 'desc')
                    ->paginate(15);
            } else {
                $loginHistory = collect([]);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania historii logowań: ' . $e->getMessage());
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
            $manualSubscriptions = Subscription::where('subscription_type', Subscription::TYPE_MANUAL)->count();
            $automaticSubscriptions = Subscription::where('subscription_type', Subscription::TYPE_AUTOMATIC)->count();
            
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
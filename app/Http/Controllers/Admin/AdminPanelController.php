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

        try {
            $stats = [
                'users_count' => Cache::remember('stats.users_count', now()->addMinutes(5), function () {
                    return User::count();
                }),
                
                'online_users' => Cache::remember('stats.online_users', now()->addMinute(), function () {
                    return User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
                }),
                
                // Subskrypcje
                'active_subscriptions' => Cache::remember('stats.active_subscriptions', now()->addMinutes(5), function () {
                    return Subscription::where('status', 'active')->count();
                }),
                
                'active_subscriptions_value' => Cache::remember('stats.active_subscriptions_value', now()->addMinutes(5), function () {
                    return Subscription::where('status', 'active')->sum('price');
                }),
                
                'today_subscriptions' => Cache::remember('stats.today_subscriptions', now()->addMinutes(5), function () {
                    return Subscription::whereDate('created_at', Carbon::today())->count();
                }),
                
                'today_subscriptions_value' => Cache::remember('stats.today_subscriptions_value', now()->addMinutes(5), function () {
                    return Subscription::whereDate('created_at', Carbon::today())->sum('price');
                }),
                
                'month_subscriptions' => Cache::remember('stats.month_subscriptions', now()->addMinutes(5), function () {
                    return Subscription::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->count();
                }),
                
                'month_subscriptions_value' => Cache::remember('stats.month_subscriptions_value', now()->addMinutes(5), function () {
                    return Subscription::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('price');
                }),
                
                'year_subscriptions' => Cache::remember('stats.year_subscriptions', now()->addMinutes(5), function () {
                    return Subscription::whereYear('created_at', Carbon::now()->year)->count();
                }),
                
                'year_subscriptions_value' => Cache::remember('stats.year_subscriptions_value', now()->addMinutes(5), function () {
                    return Subscription::whereYear('created_at', Carbon::now()->year)->sum('price');
                }),
                
                'total_active_subscriptions' => Cache::remember('stats.total_active_subscriptions', now()->addMinutes(5), function () {
                    return Subscription::where('status', 'active')->count();
                }),
                
                'total_active_value' => Cache::remember('stats.total_active_value', now()->addMinutes(5), function () {
                    return Subscription::where('status', 'active')->sum('price');
                }),
            ];

            // Dodaj statystyki aktywnych użytkowników
            if (Schema::hasColumn('users', 'last_active_at')) {
                $stats['active_users'] = Cache::remember('stats.active_users', now()->addMinute(), function () {
                    return User::where('last_active_at', '>=', now()->subMinutes(15))->count();
                });
            }

            // Dodaj statystyki logowania
            if (Schema::hasTable('login_histories')) {
                $stats['total_login_attempts'] = Cache::remember('stats.total_login_attempts', now()->addHours(1), function () {
                    return DB::table('login_histories')->count();
                });
                
                $stats['failed_login_attempts'] = Cache::remember('stats.failed_login_attempts', now()->addHours(1), function () {
                    return DB::table('login_histories')->where('status', 'failed')->count();
                });
            }

            Log::info('Statystyki panelu admina zostały wygenerowane pomyślnie', [
                'online_users' => $stats['online_users'],
                'active_users' => $stats['active_users'] ?? 0
            ]);

        } catch (Exception $e) {
            Log::error('Błąd podczas generowania statystyk panelu admina', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $stats = [
                'users_count' => 0,
                'online_users' => 0,
                'active_users' => 0,
                'active_subscriptions' => 0,
                'active_subscriptions_value' => 0,
                'today_subscriptions' => 0,
                'today_subscriptions_value' => 0,
                'month_subscriptions' => 0,
                'month_subscriptions_value' => 0,
                'year_subscriptions' => 0,
                'year_subscriptions_value' => 0,
                'total_active_subscriptions' => 0,
                'total_active_value' => 0,
                'total_login_attempts' => 0,
                'failed_login_attempts' => 0
            ];
        }

        // Aktywność użytkowników w ostatnich 7 dniach
        $userActivity = [];
        
        try {
            if (Schema::hasTable('login_histories')) {
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $count = DB::table('login_histories')
                        ->whereDate('created_at', $date)
                        ->where('status', 'success')
                        ->count();
                    $userActivity[$date] = $count;
                }
            } else {
                // Domyślne wartości, gdy tabela nie istnieje
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $userActivity[$date] = 0;
                }
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania aktywności użytkowników: ' . $e->getMessage());
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $userActivity[$date] = 0;
            }
        }

        return view('admin.index', compact('stats', 'userActivity'));
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
        
        // Tutaj w przyszłości będzie pobieranie planów subskrypcji z bazy danych
        // Na razie używamy widoku z przykładowymi danymi
        
        return view('admin.subscriptions.index');
    }
    
    /**
     * Wyświetla formularz tworzenia nowego planu subskrypcji
     */
    public function createSubscription()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        return view('admin.subscriptions.create');
    }
    
    /**
     * Zapisuje nowy plan subskrypcji
     */
    public function storeSubscription(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie logika zapisywania planu subskrypcji
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.index')
                         ->with('success', 'Plan subskrypcji został utworzony.');
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
        
        // Tutaj będzie pobieranie subskrypcji użytkowników z bazy danych
        // Na razie używamy widoku z przykładowymi danymi
        
        return view('admin.subscriptions.users');
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
        // Na razie używamy widoku z przykładowymi danymi
        
        return view('admin.subscriptions.create-user-subscription');
    }
    
    /**
     * Zapisuje nową subskrypcję użytkownika
     */
    public function storeUserSubscription(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie logika zapisywania subskrypcji użytkownika
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.users')
                         ->with('success', 'Subskrypcja została przypisana do użytkownika.');
    }
    
    /**
     * Wyświetla formularz edycji subskrypcji użytkownika
     */
    public function editUserSubscription($id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie pobieranie subskrypcji użytkownika z bazy danych
        // Na razie używamy widoku z przykładowymi danymi
        
        return view('admin.subscriptions.edit-user-subscription');
    }
    
    /**
     * Aktualizuje subskrypcję użytkownika
     */
    public function updateUserSubscription(Request $request, $id)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Tutaj będzie logika aktualizacji subskrypcji użytkownika
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.users')
                         ->with('success', 'Subskrypcja użytkownika została zaktualizowana.');
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
        
        // Tutaj będzie logika procesowania zwrotu
        // Na razie przekierowujemy z komunikatem sukcesu
        
        return redirect()->route('admin.subscriptions.payments')
                         ->with('success', 'Płatność została zwrócona.');
    }

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
        switch ($type) {
            case 'users':
                $data = User::select('id', 'name', 'email', 'created_at', 'last_seen_at')
                    ->latest()
                    ->take(50)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'created_at' => $user->created_at->format('d.m.Y H:i'),
                            'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Nigdy',
                            'last_seen_raw' => $user->last_seen_at ? $user->last_seen_at->toDateTimeString() : null,
                            'is_online' => $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5))
                        ];
                    });
                break;

            case 'online':
                $data = User::select('id', 'name', 'email', 'last_seen_at')
                    ->where('last_seen_at', '>=', now()->subMinutes(5))
                    ->orderBy('last_seen_at', 'desc')
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Nigdy',
                            'last_seen_raw' => $user->last_seen_at ? $user->last_seen_at->toDateTimeString() : null
                        ];
                    });
                break;

            default:
                return response()->json(['error' => 'Nieprawidłowy typ danych'], 400);
        }

        return response()->json(['data' => $data]);
    }
} 
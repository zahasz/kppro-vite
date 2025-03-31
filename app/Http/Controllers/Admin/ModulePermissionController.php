<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Module;
use App\Models\UserModulePermission;
use App\Services\ModulePermissionService;
use Illuminate\Support\Facades\Gate;

class ModulePermissionController extends Controller
{
    /**
     * @var ModulePermissionService
     */
    protected $modulePermissionService;
    
    /**
     * Konstruktor
     * 
     * @param ModulePermissionService $modulePermissionService
     */
    public function __construct(ModulePermissionService $modulePermissionService)
    {
        $this->modulePermissionService = $modulePermissionService;
    }
    
    /**
     * Wyświetla listę użytkowników z ich uprawnieniami do modułów
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $users = User::with('modules')->get();
        $modules = Module::where('is_active', true)->get();
        
        return view('admin.modules.index', compact('users', 'modules'));
    }
    
    /**
     * Wyświetla uprawnienia do modułów dla konkretnego użytkownika
     * 
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function userModules(User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $userModules = $this->modulePermissionService->getUserModulesWithAccess($user);
        $modules = Module::where('is_active', true)->get();
        
        return view('admin.modules.user', compact('user', 'userModules', 'modules'));
    }
    
    /**
     * Przyznaje dostęp do modułu dla użytkownika
     * 
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function grantAccess(Request $request, User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $validated = $request->validate([
            'module_code' => 'required|exists:modules,code',
            'restrictions' => 'nullable|array',
            'valid_until' => 'nullable|date|after:today',
        ]);
        
        $options = [
            'restrictions' => $validated['restrictions'] ?? null,
            'valid_until' => $validated['valid_until'] ?? null,
            'granted_by' => auth()->user()->name,
        ];
        
        $success = $this->modulePermissionService->grantModuleAccess(
            $user, 
            $validated['module_code'],
            $options
        );
        
        if ($success) {
            return redirect()->route('admin.modules.user', $user->id)
                ->with('success', 'Dostęp do modułu został przyznany.');
        } else {
            return redirect()->route('admin.modules.user', $user->id)
                ->with('error', 'Wystąpił błąd podczas przyznawania dostępu do modułu.');
        }
    }
    
    /**
     * Blokuje dostęp do modułu dla użytkownika
     * 
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function denyAccess(Request $request, User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $validated = $request->validate([
            'module_code' => 'required|exists:modules,code',
        ]);
        
        $success = $this->modulePermissionService->denyModuleAccess(
            $user, 
            $validated['module_code'],
            auth()->user()->name
        );
        
        if ($success) {
            return redirect()->route('admin.modules.user', $user->id)
                ->with('success', 'Dostęp do modułu został zablokowany.');
        } else {
            return redirect()->route('admin.modules.user', $user->id)
                ->with('error', 'Wystąpił błąd podczas blokowania dostępu do modułu.');
        }
    }
    
    /**
     * Usuwa indywidualne uprawnienie do modułu dla użytkownika
     * 
     * @param User $user
     * @param Module $module
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeAccess(User $user, Module $module)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $permission = UserModulePermission::where([
            'user_id' => $user->id,
            'module_id' => $module->id,
        ])->first();
        
        if ($permission) {
            $permission->delete();
            return redirect()->route('admin.modules.user', $user->id)
                ->with('success', 'Indywidualne uprawnienie do modułu zostało usunięte.');
        }
        
        return redirect()->route('admin.modules.user', $user->id)
            ->with('error', 'Nie znaleziono uprawnienia do usunięcia.');
    }
}

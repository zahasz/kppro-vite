<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AdminPanelController extends Controller
{
    public function index()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $stats = [
            'users_count' => Cache::remember('stats.users_count', now()->addMinutes(5), function () {
                return User::count();
            }),
            'roles_count' => Cache::remember('stats.roles_count', now()->addMinutes(5), function () {
                return Role::count();
            }),
            'permissions_count' => Cache::remember('stats.permissions_count', now()->addMinutes(5), function () {
                return Permission::count();
            }),
        ];

        return view('admin.index', compact('stats'));
    }

    public function users()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $users = User::with(['roles', 'permissions'])->paginate(10);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
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

    public function permissions()
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $permissions = Permission::paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function updateUserRoles(Request $request, User $user)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name'
        ]);

        $user->syncRoles($request->roles);
        Cache::tags(['user-roles'])->forget('user-'.$user->id);

        return redirect()->back()->with('success', 'Role użytkownika zostały zaktualizowane.');
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->syncPermissions($request->permissions);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', 'Uprawnienia roli zostały zaktualizowane.');
    }

    public function storeRole(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles')->with('success', 'Rola została utworzona.');
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
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles')->with('success', 'Rola została zaktualizowana.');
    }

    public function destroyRole(Role $role)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Nie można usunąć roli administratora.');
        }

        $role->delete();

        return redirect()->route('admin.roles')->with('success', 'Rola została usunięta.');
    }
} 
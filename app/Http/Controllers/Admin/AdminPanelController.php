<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;

class AdminPanelController extends Controller
{
    public function index()
    {
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

        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::with(['roles', 'permissions'])->paginate(10);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function permissions()
    {
        $permissions = Permission::paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function updateUserRoles(Request $request, User $user)
    {
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
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->syncPermissions($request->permissions);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', 'Uprawnienia roli zostały zaktualizowane.');
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::paginate(25);
        $permissionGroups = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('admin.permissions.index', compact('permissions', 'permissionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.permissions.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'roles' => ['array'],
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('roles')) {
            foreach ($request->roles as $roleId) {
                $role = Role::findById($roleId);
                $role->givePermissionTo($permission->name);
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Uprawnienie zostało pomyślnie utworzone.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $roles = Role::all();
        $permission->load('roles');
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'roles' => ['array'],
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        // Synchronizacja ról
        $roles = Role::all();
        foreach ($roles as $role) {
            if (in_array($role->id, $request->roles ?? [])) {
                $role->givePermissionTo($permission->name);
            } else {
                $role->revokePermissionTo($permission->name);
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Uprawnienie zostało pomyślnie zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Sprawdź, czy to jest uprawnienie systemowe, którego nie można usunąć
        if ($permission->is_system) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Nie możesz usunąć uprawnienia systemowego.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Uprawnienie zostało pomyślnie usunięte.');
    }
} 
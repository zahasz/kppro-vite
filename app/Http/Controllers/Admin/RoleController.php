<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount('users')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rola została pomyślnie utworzona.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });
        
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
        ]);

        // Sprawdź, czy to jest rola systemowa, której nie można edytować
        if ($role->is_system) {
            $role->update([
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);
        } else {
            $role->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rola została pomyślnie zaktualizowana.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Zapobiegaj usunięciu roli systemowej
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Nie możesz usunąć roli systemowej.');
        }

        // Sprawdź, czy rola ma przypisanych użytkowników
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Nie możesz usunąć roli, która ma przypisanych użytkowników.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rola została pomyślnie usunięta.');
    }
} 
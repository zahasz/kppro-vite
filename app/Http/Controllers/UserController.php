<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);
        
        if ($request->has('roles')) {
            $user->roles()->attach($request->roles, [
                'assigned_at' => now(),
                'assigned_by' => Auth::id()
            ]);
        }
        
        return redirect()->route('users.index')
            ->with('success', 'Użytkownik został utworzony pomyślnie.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles', 'profile');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);
        
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }
        
        return redirect()->route('users.index')
            ->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Nie możesz usunąć własnego konta.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'Użytkownik został usunięty.');
    }

    protected function validateUser(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $user ? "unique:users,email,{$user->id}" : 'unique:users'],
            'role' => ['required', 'string', 'in:admin,manager,user'],
            'is_active' => ['boolean'],
            'language' => ['string', 'in:pl,en'],
            'timezone' => ['string', 'timezone'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id']
        ];

        if (!$user || $request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $request->validate($rules);
    }
}

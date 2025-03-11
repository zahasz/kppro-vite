<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Upewnij się, że wszystkie potrzebne relacje są załadowane
        $user->load(['company']);

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Aktualizacja danych użytkownika
        $userData = $request->validated();
        
        // Ustawienie pola name tylko jeśli zmieniono first_name lub last_name
        if (isset($userData['first_name']) || isset($userData['last_name'])) {
            $firstName = $userData['first_name'] ?? $user->first_name;
            $lastName = $userData['last_name'] ?? $user->last_name;
            $userData['name'] = trim($firstName . ' ' . $lastName);
        }
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->fill($userData);
        $user->save();

        // Aktualizacja lub tworzenie danych firmy
        if ($request->hasAny(['company.name', 'company.logo', 'company.address', 'company.city', 
                            'company.postal_code', 'company.nip', 'company.regon', 'company.phone', 
                            'company.email', 'company.website'])) {
            $companyData = $request->get('company', []);
            
            if ($request->hasFile('company.logo')) {
                if ($user->company && $user->company->logo) {
                    Storage::disk('public')->delete($user->company->logo);
                }
                $companyData['logo'] = $request->file('company.logo')->store('logos', 'public');
            }

            if ($user->company) {
                $user->company->update($companyData);
            } else {
                $company = Company::create($companyData);
                $user->company()->associate($company);
                $user->save();
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

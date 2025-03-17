<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Company;
use App\Models\CompanyProfile;
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
        $user->load(['company', 'companyProfile']);
        
        // Sprawdź, czy profil firmy istnieje
        if (!$user->companyProfile) {
            // Jeśli nie istnieje, utwórz pusty profil
            $companyProfile = new CompanyProfile();
            $companyProfile->user_id = $user->id;
            $companyProfile->save();
            
            // Załaduj ponownie relację
            $user->refresh();
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Walidacja danych użytkownika
        $userData = $request->validate([
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:1024'],
        ]);
        
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

        // Aktualizacja danych profilu firmy
        if ($request->has([
            'company_name', 'legal_form', 'tax_number', 'regon', 'krs', 
            'street', 'city', 'state', 'country', 'postal_code', 
            'phone', 'phone_additional', 'email', 'email_additional', 'website',
            'bank_name', 'bank_account', 'swift', 'notes',
            'invoice_prefix', 'invoice_numbering_pattern', 'invoice_next_number',
            'invoice_payment_days', 'default_payment_method', 'default_currency',
            'invoice_notes', 'invoice_footer'
        ])) {
            $companyProfileData = $request->only([
                'company_name', 'legal_form', 'tax_number', 'regon', 'krs', 
                'street', 'city', 'state', 'country', 'postal_code', 
                'phone', 'phone_additional', 'email', 'email_additional', 'website',
                'bank_name', 'bank_account', 'swift', 'notes',
                'invoice_prefix', 'invoice_numbering_pattern', 'invoice_next_number',
                'invoice_payment_days', 'default_payment_method', 'default_currency',
                'invoice_notes', 'invoice_footer'
            ]);
            
            if ($request->hasFile('logo')) {
                if ($user->companyProfile && $user->companyProfile->logo_path) {
                    Storage::disk('public')->delete($user->companyProfile->logo_path);
                }
                $companyProfileData['logo_path'] = $request->file('logo')->store('company-logos', 'public');
            }

            $user->companyProfile->fill($companyProfileData);
            $user->companyProfile->save();
        }

        // Aktualizacja lub tworzenie danych firmy (stary model)
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

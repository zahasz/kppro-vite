<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CompanyProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Wyświetla listę kont bankowych użytkownika
     */
    public function index(): View
    {
        $user = Auth::user();
        $bankAccounts = collect();
        
        if ($user->companyProfile) {
            $bankAccounts = $user->companyProfile->bankAccounts;
        }
        
        return view('bank-accounts.index', [
            'user' => $user,
            'bankAccounts' => $bankAccounts
        ]);
    }

    /**
     * Zapisuje nowe konto bankowe
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'swift' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->back()->with('error', 'Najpierw musisz utworzyć profil firmy');
        }

        $bankAccount = new BankAccount($validated);
        $bankAccount->company_profile_id = $companyProfile->id;
        $bankAccount->save();

        // Jeśli to jest pierwsze konto lub ustawiono jako domyślne
        if ($validated['is_default'] ?? $companyProfile->bankAccounts()->count() === 1) {
            $bankAccount->setAsDefault();
        }

        return redirect()->back()->with('success', 'Konto bankowe zostało dodane');
    }

    /**
     * Aktualizuje konto bankowe
     */
    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        if (!Auth::user()->can('update', $bankAccount)) {
            abort(403, 'Brak uprawnień do aktualizacji tego konta bankowego.');
        }

        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'swift' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        $bankAccount->update($validated);

        // Jeśli ustawiono jako domyślne
        if ($validated['is_default'] ?? false) {
            $bankAccount->setAsDefault();
        }

        return redirect()->back()->with('success', 'Konto bankowe zostało zaktualizowane');
    }

    /**
     * Usuwa konto bankowe
     */
    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        if (!Auth::user()->can('delete', $bankAccount)) {
            abort(403, 'Brak uprawnień do usunięcia tego konta bankowego.');
        }

        $wasDefault = $bankAccount->is_default;
        $companyProfile = $bankAccount->companyProfile;

        $bankAccount->delete();

        // Jeśli usunięto domyślne konto, a istnieją inne konta, ustawiamy nowe domyślne
        if ($wasDefault && $companyProfile->bankAccounts()->count() > 0) {
            $newDefault = $companyProfile->bankAccounts()->first();
            $newDefault->setAsDefault();
        }

        return redirect()->back()->with('success', 'Konto bankowe zostało usunięte');
    }

    /**
     * Ustawia konto jako domyślne
     */
    public function setDefault(BankAccount $bankAccount): RedirectResponse
    {
        if (!Auth::user()->can('update', $bankAccount)) {
            abort(403, 'Brak uprawnień do modyfikacji tego konta bankowego.');
        }

        $bankAccount->setAsDefault();

        return redirect()->back()->with('success', 'Domyślne konto bankowe zostało zaktualizowane');
    }
}

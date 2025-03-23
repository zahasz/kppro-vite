<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function update(Request $request)
    {
        \Log::info('Rozpoczęcie aktualizacji profilu firmy', ['user_id' => auth()->id()]);
        
        // Logowanie wszystkich danych z żądania
        \Log::info('Wszystkie dane z żądania', [
            'all' => $request->all(),
            'method' => $request->method(),
            'has_invoice_footer' => $request->has('invoice_footer'),
            'raw_invoice_footer' => $request->input('invoice_footer')
        ]);
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_form' => 'nullable|string|in:sole_proprietorship,partnership,limited_partnership,limited_liability,joint_stock',
            'tax_number' => 'required|string|max:255',
            'regon' => 'nullable|string|max:255',
            'krs' => 'nullable|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'phone_additional' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'email_additional' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'swift' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:1024',
            'invoice_prefix' => 'nullable|string|max:50',
            'invoice_numbering_pattern' => 'nullable|string|max:255',
            'invoice_next_number' => 'nullable|integer|min:1',
            'invoice_payment_days' => 'nullable|integer|min:0',
            'default_payment_method' => 'nullable|string|max:50',
            'default_currency' => 'nullable|string|max:10',
            'invoice_notes' => 'nullable|string|max:1000',
            'invoice_footer' => 'nullable|string|max:1000',
        ]);
        \Log::info('Walidacja przeszła pomyślnie');

        // Sprawdź dane stopki faktury
        \Log::info('Wartość stopki faktury z formularza', [
            'invoice_footer' => $request->input('invoice_footer'),
            'ma_stopke' => $request->has('invoice_footer'),
            'validated_invoice_footer' => $validated['invoice_footer'] ?? 'brak wartości'
        ]);

        $user = auth()->user();
        $companyProfile = $user->companyProfile;
        
        if (!$companyProfile) {
            \Log::info('Tworzenie nowego profilu firmy');
            $companyProfile = new CompanyProfile();
            $companyProfile->user_id = $user->id;
        } else {
            \Log::info('Aktualizacja istniejącego profilu firmy', ['profile_id' => $companyProfile->id]);
        }

        if ($request->hasFile('logo')) {
            \Log::info('Przetwarzanie pliku logo');
            if ($companyProfile->logo_path) {
                \Log::info('Usuwanie starego logo', ['path' => $companyProfile->logo_path]);
                Storage::disk('public')->delete($companyProfile->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('company-logos', 'public');
            \Log::info('Zapisano nowe logo', ['path' => $validated['logo_path']]);
        }

        unset($validated['logo']);
        
        foreach ($validated as $key => $value) {
            $companyProfile->$key = $value;
        }
        
        $result = $companyProfile->save();
        \Log::info('Wynik zapisywania profilu', ['success' => $result, 'profile_id' => $companyProfile->id]);

        return back()->with('status', 'company-profile-updated');
    }

    /**
     * Pobierz dane profilu firmy w formacie JSON
     */
    public function getJson()
    {
        \Log::info('Wywołanie metody getJson w CompanyProfileController');
        
        $user = auth()->user();
        \Log::info('Użytkownik zalogowany: ' . ($user ? 'Tak (ID: ' . $user->id . ')' : 'Nie'));
        
        $companyProfile = $user ? $user->companyProfile : null;
        \Log::info('CompanyProfile: ' . ($companyProfile ? 'Istnieje (ID: ' . $companyProfile->id . ')' : 'Brak'));
        
        $company = $user ? $user->company : null;
        \Log::info('Company: ' . ($company ? 'Istnieje (ID: ' . $company->id . ')' : 'Brak'));

        $data = [
            'success' => true,
            'company' => []
        ];

        if ($companyProfile) {
            $data['company'] = [
                'name' => $companyProfile->company_name,
                'street' => $companyProfile->street,
                'postal_code' => $companyProfile->postal_code,
                'city' => $companyProfile->city,
                'nip' => $companyProfile->tax_number,
                'regon' => $companyProfile->regon,
                'bank_name' => $companyProfile->bank_name,
                'bank_account' => $companyProfile->bank_account,
                'invoice_prefix' => $companyProfile->invoice_prefix,
                'invoice_numbering_pattern' => $companyProfile->invoice_numbering_pattern,
                'invoice_next_number' => $companyProfile->invoice_next_number,
                'invoice_payment_days' => $companyProfile->invoice_payment_days,
                'default_payment_method' => $companyProfile->default_payment_method,
                'default_currency' => $companyProfile->default_currency,
                'invoice_notes' => $companyProfile->invoice_notes,
                'invoice_footer' => $companyProfile->invoice_footer,
            ];
        } elseif ($company) {
            $data['company'] = [
                'name' => $company->name,
                'street' => $company->address,
                'postal_code' => $company->postal_code,
                'city' => $company->city,
                'nip' => $company->nip,
                'regon' => $company->regon,
                'bank_name' => '',
                'bank_account' => '',
                'invoice_prefix' => '',
                'invoice_numbering_pattern' => 'FV/{YEAR}/{MONTH}/{NUMBER}',
                'invoice_next_number' => 1,
                'invoice_payment_days' => 14,
                'default_payment_method' => 'przelew',
                'default_currency' => 'PLN',
                'invoice_notes' => '',
                'invoice_footer' => '',
            ];
        } else {
            $data['company'] = [
                'name' => config('app.name'),
                'street' => '',
                'postal_code' => '',
                'city' => '',
                'nip' => '',
                'regon' => '',
                'bank_name' => '',
                'bank_account' => '',
                'invoice_prefix' => '',
                'invoice_numbering_pattern' => 'FV/{YEAR}/{MONTH}/{NUMBER}',
                'invoice_next_number' => 1,
                'invoice_payment_days' => 14,
                'default_payment_method' => 'przelew',
                'default_currency' => 'PLN',
                'invoice_notes' => '',
                'invoice_footer' => '',
            ];
        }

        return response()->json($data);
    }

    /**
     * Tworzy testowy profil firmy dla zalogowanego użytkownika
     */
    public function createTestProfile()
    {
        $user = auth()->user();
        
        // Sprawdź czy użytkownik już ma profil firmy
        if ($user->companyProfile) {
            return back()->with('info', 'Profil firmy już istnieje.');
        }
        
        // Utwórz przykładowy profil firmy
        $companyProfile = $user->companyProfile()->create([
            'company_name' => 'KPPRO',
            'tax_number' => '1234567890',
            'street' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'Polska',
            'phone' => '123456789',
            'email' => $user->email,
            'bank_name' => 'Bank Testowy',
            'bank_account' => '12345678901234567890',
            'invoice_footer' => 'Dziękujemy za skorzystanie z naszych usług!'
        ]);
        
        return back()->with('success', 'Przykładowy profil firmy został utworzony.');
    }
}

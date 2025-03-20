<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;
        return view('settings.index', compact('user', 'companyProfile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_form' => 'required|string|max:50',
            'tax_number' => 'required|string|max:20',
            'regon' => 'nullable|string|max:20',
            'krs' => 'nullable|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'phone_additional' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'email_additional' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:50',
            'swift' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'invoice_prefix' => 'nullable|string|max:20',
            'invoice_numbering_pattern' => 'required|string|max:100',
            'invoice_next_number' => 'required|integer|min:1',
            'invoice_payment_days' => 'required|integer|min:0',
            'default_payment_method' => 'required|string|max:50',
            'default_currency' => 'required|string|max:3',
            'invoice_notes' => 'nullable|string',
            'invoice_footer' => 'nullable|string'
        ]);

        $companyProfile->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Ustawienia zostały zaktualizowane.');
    }
} 
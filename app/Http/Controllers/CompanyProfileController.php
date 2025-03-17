<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function update(Request $request)
    {
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

        $user = auth()->user();
        $companyProfile = $user->companyProfile ?? new CompanyProfile();

        if ($request->hasFile('logo')) {
            if ($companyProfile->logo_path) {
                Storage::delete($companyProfile->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('company-logos', 'public');
        }

        unset($validated['logo']);
        
        $companyProfile->fill($validated);
        $user->companyProfile()->save($companyProfile);

        return back()->with('status', 'company-profile-updated');
    }
}

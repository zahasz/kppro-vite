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
            'tax_number' => 'nullable|string|max:255',
            'regon' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024',
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

        return back()->with('status', 'Profil firmy został zaktualizowany.');
    }
}

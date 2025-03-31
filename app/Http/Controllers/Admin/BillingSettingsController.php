<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingSettings;
use App\Models\PaymentGateway;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class BillingSettingsController extends Controller
{
    /**
     * Wyświetla ustawienia fakturowania
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Sprawdź uprawnienia
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Pobierz ustawienia fakturowania
        $settings = BillingSettings::getActive();
        
        // Pobierz ustawienia płatności
        $paymentSettings = PaymentSettings::getActive();
        
        // Pobierz aktywne bramki płatności do wyświetlenia w formularzu
        $paymentGateways = PaymentGateway::where('is_active', true)->orderBy('display_order')->get();
        
        return view('admin.billing.settings', [
            'settings' => $settings,
            'paymentSettings' => $paymentSettings,
            'paymentGateways' => $paymentGateways,
        ]);
    }
    
    /**
     * Aktualizuje ustawienia fakturowania
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Sprawdź uprawnienia
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        // Walidacja danych
        $validator = Validator::make($request->all(), [
            'auto_generate' => 'sometimes|boolean',
            'generation_day' => 'required|integer|min:1|max:28',
            'invoice_prefix' => 'nullable|string|max:20',
            'invoice_suffix' => 'nullable|string|max:20',
            'reset_numbering' => 'sometimes|boolean',
            'payment_days' => 'required|integer|min:0|max:60',
            'default_currency' => 'required|string|size:3',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'vat_number' => 'nullable|string|max:20',
            'invoice_notes' => 'nullable|string|max:1000',
            'email_notifications' => 'sometimes|boolean',
        ]);
        
        // W przypadku błędów, wróć do formularza z komunikatami
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Pobierz lub utwórz ustawienia
        $settings = BillingSettings::getActive();
        
        // Aktualizuj pola ustawień
        $settings->auto_generate = $request->has('auto_generate');
        $settings->generation_day = $request->input('generation_day');
        $settings->invoice_prefix = $request->input('invoice_prefix');
        $settings->invoice_suffix = $request->input('invoice_suffix');
        $settings->reset_numbering = $request->has('reset_numbering');
        $settings->payment_days = $request->input('payment_days');
        $settings->default_currency = $request->input('default_currency');
        $settings->default_tax_rate = $request->input('default_tax_rate');
        $settings->vat_number = $request->input('vat_number');
        $settings->invoice_notes = $request->input('invoice_notes');
        $settings->email_notifications = $request->has('email_notifications');
        
        // Zapisz ustawienia
        $settings->save();
        
        // Przekieruj z komunikatem sukcesu
        return redirect()->route('admin.billing.settings')
            ->with('success', 'Ustawienia faktur zostały zaktualizowane pomyślnie.');
    }
} 
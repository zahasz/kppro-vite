<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PaymentSettingsController extends Controller
{
    /**
     * Konstruktor kontrolera
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('admin')) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Aktualizacja ustawień płatności
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auto_retry_failed_payments' => 'boolean',
            'payment_retry_attempts' => 'required|integer|min:1|max:5',
            'payment_retry_interval' => 'required|integer|min:1|max:10',
            'grace_period_days' => 'required|integer|min:0|max:30',
            'default_payment_gateway' => 'nullable|string|exists:payment_gateways,code',
            'renewal_notifications' => 'boolean',
            'renewal_notification_days' => 'required|integer|min:1|max:30',
            'auto_cancel_after_failed_payments' => 'boolean',
            'renewal_charge_days_before' => 'required|integer|min:0|max:7',
            'enable_accounting_integration' => 'boolean',
            'accounting_api_url' => 'nullable|url|required_if:enable_accounting_integration,1',
            'accounting_api_key' => 'nullable|string|required_if:enable_accounting_integration,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Pobierz lub utwórz ustawienia
        $settings = PaymentSettings::getActive();

        // Ustawienia automatycznych prób płatności
        $settings->auto_retry_failed_payments = $request->has('auto_retry_failed_payments');
        $settings->payment_retry_attempts = $request->input('payment_retry_attempts');
        $settings->payment_retry_interval = $request->input('payment_retry_interval');
        $settings->grace_period_days = $request->input('grace_period_days');
        $settings->default_payment_gateway = $request->input('default_payment_gateway');

        // Ustawienia odnowień subskrypcji
        $settings->renewal_notifications = $request->has('renewal_notifications');
        $settings->renewal_notification_days = $request->input('renewal_notification_days');
        $settings->auto_cancel_after_failed_payments = $request->has('auto_cancel_after_failed_payments');
        $settings->renewal_charge_days_before = $request->input('renewal_charge_days_before');

        // Integracja z systemami zewnętrznymi
        $settings->enable_accounting_integration = $request->has('enable_accounting_integration');
        $settings->accounting_api_url = $request->input('accounting_api_url');
        
        // Jeśli przesłano nowy klucz API, zaktualizuj go
        if ($request->filled('accounting_api_key')) {
            $settings->accounting_api_key = $request->input('accounting_api_key');
        }

        $settings->save();

        return redirect()->route('admin.billing.settings')
            ->with('success', 'Ustawienia płatności zostały zaktualizowane pomyślnie.');
    }
} 
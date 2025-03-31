<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
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
     * Wyświetla listę bramek płatności
     */
    public function index()
    {
        $gateways = PaymentGateway::orderBy('display_order')->get();
        
        return view('admin.payments.index', [
            'gateways' => $gateways,
        ]);
    }

    /**
     * Wyświetla formularz tworzenia nowej bramki płatności
     */
    public function create()
    {
        return view('admin.payments.create');
    }

    /**
     * Zapisuje nową bramkę płatności
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_gateways,code',
            'class_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
            'test_mode' => 'boolean',
            'display_order' => 'integer|min:0',
            'config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gateway = new PaymentGateway();
        $gateway->name = $request->name;
        $gateway->code = $request->code;
        $gateway->class_name = $request->class_name;
        $gateway->description = $request->description;
        $gateway->is_active = $request->boolean('is_active', false);
        $gateway->test_mode = $request->boolean('test_mode', true);
        $gateway->display_order = $request->display_order ?? 0;
        $gateway->config = $request->config ?? [];

        // Zapisz logo, jeśli zostało przesłane
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('payment-gateways', 'public');
            $gateway->logo_path = $path;
        }

        $gateway->save();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Bramka płatności została dodana pomyślnie.');
    }

    /**
     * Wyświetla formularz edycji bramki płatności
     */
    public function edit(PaymentGateway $gateway)
    {
        return view('admin.payments.edit', [
            'gateway' => $gateway,
        ]);
    }

    /**
     * Aktualizuje bramkę płatności
     */
    public function update(Request $request, PaymentGateway $gateway)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_gateways,code,' . $gateway->id,
            'class_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
            'test_mode' => 'boolean',
            'display_order' => 'integer|min:0',
            'config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gateway->name = $request->name;
        $gateway->code = $request->code;
        $gateway->class_name = $request->class_name;
        $gateway->description = $request->description;
        $gateway->is_active = $request->boolean('is_active', false);
        $gateway->test_mode = $request->boolean('test_mode', true);
        $gateway->display_order = $request->display_order ?? 0;
        
        // Aktualizuj konfigurację, jeśli została przesłana
        if ($request->has('config')) {
            $gateway->config = $request->config;
        }

        // Zaktualizuj logo, jeśli zostało przesłane
        if ($request->hasFile('logo')) {
            // Usuń stare logo, jeśli istnieje
            if ($gateway->logo_path) {
                Storage::disk('public')->delete($gateway->logo_path);
            }
            
            $path = $request->file('logo')->store('payment-gateways', 'public');
            $gateway->logo_path = $path;
        }

        $gateway->save();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Bramka płatności została zaktualizowana pomyślnie.');
    }

    /**
     * Usuwa bramkę płatności
     */
    public function destroy(PaymentGateway $gateway)
    {
        // Sprawdź, czy bramka jest używana w transakcjach
        $transactionsCount = PaymentTransaction::where('gateway_code', $gateway->code)->count();
        
        if ($transactionsCount > 0) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Nie można usunąć bramki płatności, ponieważ jest używana w transakcjach.');
        }
        
        // Usuń logo, jeśli istnieje
        if ($gateway->logo_path) {
            Storage::disk('public')->delete($gateway->logo_path);
        }
        
        $gateway->delete();
        
        return redirect()->route('admin.payments.index')
            ->with('success', 'Bramka płatności została usunięta pomyślnie.');
    }

    /**
     * Zmienia status aktywności bramki płatności
     */
    public function toggleStatus(PaymentGateway $gateway)
    {
        $gateway->is_active = !$gateway->is_active;
        $gateway->save();
        
        return redirect()->route('admin.payments.index')
            ->with('success', 'Status bramki płatności został zmieniony.');
    }

    /**
     * Zmienia tryb testowy bramki płatności
     */
    public function toggleTestMode(PaymentGateway $gateway)
    {
        $gateway->test_mode = !$gateway->test_mode;
        $gateway->save();
        
        return redirect()->route('admin.payments.index')
            ->with('success', 'Tryb testowy bramki płatności został zmieniony.');
    }
    
    /**
     * Wyświetla historię transakcji płatności
     */
    public function transactions(Request $request)
    {
        $query = PaymentTransaction::with(['user', 'subscription', 'paymentGateway']);
        
        // Filtrowanie
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('gateway') && $request->gateway != '') {
            $query->where('gateway_code', $request->gateway);
        }
        
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sortowanie
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $transactions = $query->orderBy($sortField, $sortDirection)->paginate(20);
        $gateways = PaymentGateway::orderBy('name')->get();
        
        return view('admin.payments.transactions', [
            'transactions' => $transactions,
            'gateways' => $gateways,
            'statusOptions' => [
                'pending' => 'Oczekująca',
                'completed' => 'Zakończona',
                'failed' => 'Nieudana',
                'refunded' => 'Zwrócona',
                'canceled' => 'Anulowana',
            ],
        ]);
    }
    
    /**
     * Wyświetla szczegóły transakcji
     */
    public function transactionDetails(PaymentTransaction $transaction)
    {
        $transaction->load(['user', 'subscription.plan', 'paymentGateway', 'invoice']);
        
        return view('admin.payments.transaction-details', [
            'transaction' => $transaction,
        ]);
    }
    
    /**
     * Aktualizuje status transakcji (manualnie)
     */
    public function updateTransactionStatus(Request $request, PaymentTransaction $transaction)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed,refunded,canceled',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Zapisz poprzedni status do logów
        $previousStatus = $transaction->status;
        
        // Zaktualizuj status
        $transaction->status = $request->status;
        
        // Zapisz notatkę jako metadane
        $metadata = $transaction->metadata ?? [];
        $metadata['status_change_notes'] = [
            'time' => now()->toIso8601String(),
            'user_id' => auth()->id(),
            'previous_status' => $previousStatus,
            'new_status' => $request->status,
            'notes' => $request->notes,
        ];
        $transaction->metadata = $metadata;
        
        $transaction->save();
        
        // Jeśli status zmienił się na 'completed', zaktualizuj subskrypcję
        if ($request->status === 'completed' && $previousStatus !== 'completed' && $transaction->subscription) {
            $subscriptionService = app(\App\Services\SubscriptionService::class);
            $subscriptionService->activateSubscription($transaction->subscription);
            
            // Wygeneruj fakturę, jeśli jeszcze nie istnieje
            if (!$transaction->invoice_id) {
                try {
                    $invoice = $subscriptionService->generateInvoiceForPayment($transaction);
                    
                    if ($invoice) {
                        $transaction->invoice_id = $invoice->id;
                        $transaction->save();
                    }
                } catch (\Exception $e) {
                    Log::error('Błąd podczas generowania faktury: ' . $e->getMessage(), [
                        'transaction_id' => $transaction->id,
                        'exception' => $e,
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.payments.transactions')
            ->with('success', 'Status transakcji został zaktualizowany pomyślnie.');
    }
    
    /**
     * Obsługuje żądanie zwrotu płatności
     */
    public function refundPayment(Request $request, PaymentTransaction $transaction)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0.01|max:' . $transaction->amount,
            'reason' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Sprawdź, czy transakcja może być zwrócona
        if (!$transaction->isCompleted()) {
            return redirect()->back()
                ->with('error', 'Tylko zakończone transakcje mogą być zwrócone.');
        }
        
        $amount = $request->has('amount') ? $request->amount : $transaction->amount;
        $reason = $request->reason;
        
        // Użyj menedżera bramek płatności do wykonania zwrotu
        $gatewayManager = app(\App\Services\PaymentGatewayManager::class);
        $result = $gatewayManager->refundPayment($transaction, $amount, $reason);
        
        if ($result['success']) {
            return redirect()->route('admin.payments.transactions')
                ->with('success', 'Zwrot został przetworzony pomyślnie.');
        } else {
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas przetwarzania zwrotu: ' . ($result['message'] ?? 'Nieznany błąd'));
        }
    }
} 
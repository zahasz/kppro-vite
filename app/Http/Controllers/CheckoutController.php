<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\PaymentGatewayManager;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Menedżer bramek płatności
     */
    protected $gatewayManager;

    /**
     * Serwis subskrypcji
     */
    protected $subscriptionService;

    /**
     * Konstruktor kontrolera
     */
    public function __construct(PaymentGatewayManager $gatewayManager, SubscriptionService $subscriptionService)
    {
        $this->gatewayManager = $gatewayManager;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Wyświetla stronę wyboru metody płatności
     */
    public function selectPaymentMethod(UserSubscription $subscription)
    {
        // Sprawdź czy subskrypcja należy do zalogowanego użytkownika
        if ($subscription->user_id !== Auth::id()) {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Brak dostępu do tej subskrypcji.');
        }

        // Sprawdź czy subskrypcja czeka na płatność
        if ($subscription->status !== 'pending') {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Ta subskrypcja nie oczekuje na płatność.');
        }

        // Pobierz aktywne bramki płatności
        $gateways = PaymentGateway::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        if ($gateways->isEmpty()) {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Brak dostępnych metod płatności. Prosimy o kontakt z administratorem.');
        }

        return view('checkout.payment', [
            'subscription' => $subscription,
            'gateways' => $gateways,
        ]);
    }

    /**
     * Inicjuje proces płatności
     */
    public function initiatePayment(Request $request, UserSubscription $subscription)
    {
        // Sprawdź czy subskrypcja należy do zalogowanego użytkownika
        if ($subscription->user_id !== Auth::id()) {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Brak dostępu do tej subskrypcji.');
        }

        // Sprawdź czy subskrypcja czeka na płatność
        if ($subscription->status !== 'pending') {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Ta subskrypcja nie oczekuje na płatność.');
        }

        // Walidacja żądania
        $validator = Validator::make($request->all(), [
            'gateway' => 'required|string|exists:payment_gateways,code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gatewayCode = $request->gateway;
        $user = Auth::user();
        $plan = $subscription->plan;

        // Rozpocznij transakcję
        DB::beginTransaction();

        try {
            // Utwórz transakcję płatności
            $transaction = new PaymentTransaction();
            $transaction->user_id = $user->id;
            $transaction->subscription_id = $subscription->id;
            $transaction->amount = $subscription->price;
            $transaction->currency = 'PLN';
            $transaction->status = 'pending';
            $transaction->gateway_code = $gatewayCode;
            $transaction->description = 'Płatność za subskrypcję: ' . $plan->name;
            $transaction->save();

            // Przekieruj do bramki płatności
            $redirectUrl = $this->gatewayManager->getRedirectUrl($gatewayCode, $transaction);

            DB::commit();

            if ($redirectUrl) {
                return redirect()->to($redirectUrl);
            } else {
                Log::error('Błąd podczas uzyskiwania URL przekierowania z bramki płatności', [
                    'gateway' => $gatewayCode,
                    'transaction_id' => $transaction->id,
                ]);

                return redirect()->route('user.subscriptions')
                    ->with('error', 'Wystąpił błąd podczas inicjowania płatności. Prosimy spróbować ponownie.');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Wyjątek podczas inicjowania płatności: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'gateway' => $gatewayCode,
                'exception' => $e,
            ]);

            return redirect()->route('user.subscriptions')
                ->with('error', 'Wystąpił błąd podczas inicjowania płatności: ' . $e->getMessage());
        }
    }

    /**
     * Wyświetla potwierdzenie płatności
     */
    public function showConfirmation($transactionId)
    {
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->with(['user', 'paymentGateway', 'subscription.plan', 'invoice'])
            ->firstOrFail();
        
        return view('checkout.confirmation', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Obsługuje powrót z bramki płatności
     */
    public function handleReturn(Request $request)
    {
        // Sprawdź parametry zwrócone przez bramkę płatności
        $transactionId = $request->transaction_id ?? $request->get('tid') ?? $request->get('order_id');
        $status = $request->status ?? $request->get('result') ?? null;
        
        if (!$transactionId) {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Brak identyfikatora transakcji w odpowiedzi od bramki płatności.');
        }
        
        // Znajdź transakcję
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->orWhere('reference_id', $transactionId)
            ->with(['user', 'paymentGateway', 'subscription'])
            ->first();
        
        if (!$transaction) {
            // Logowanie błędu
            Log::error('Nie znaleziono transakcji dla ID: ' . $transactionId, [
                'request_params' => $request->all(),
            ]);
            
            return redirect()->route('user.subscriptions')
                ->with('error', 'Nie znaleziono transakcji płatności.');
        }
        
        // Sprawdź czy transakcja należy do zalogowanego użytkownika
        if ($transaction->user_id !== Auth::id()) {
            return redirect()->route('user.subscriptions')
                ->with('error', 'Brak dostępu do tej transakcji.');
        }
        
        // Sprawdź status transakcji
        if ($transaction->status === PaymentTransaction::STATUS_COMPLETED) {
            // Transakcja zakończona pomyślnie
            return redirect()->route('checkout.confirmation', $transaction->transaction_id);
        } elseif ($transaction->status === PaymentTransaction::STATUS_FAILED) {
            // Transakcja zakończona niepowodzeniem
            return redirect()->route('user.subscriptions')
                ->with('error', 'Płatność nie powiodła się. Powód: ' . $transaction->error_message);
        } else {
            // Transakcja w toku lub inny status - sprawdź aktualny stan
            try {
                $result = $this->gatewayManager->refreshTransactionStatus($transaction);
                
                if ($result['success'] && $transaction->status === PaymentTransaction::STATUS_COMPLETED) {
                    return redirect()->route('checkout.confirmation', $transaction->transaction_id);
                } else {
                    return redirect()->route('user.subscriptions')
                        ->with('warning', 'Status płatności jest w trakcie weryfikacji. Sprawdź szczegóły subskrypcji za kilka minut.');
                }
            } catch (\Exception $e) {
                Log::error('Błąd podczas sprawdzania statusu transakcji: ' . $e->getMessage(), [
                    'transaction_id' => $transaction->id,
                    'exception' => $e,
                ]);
                
                return redirect()->route('user.subscriptions')
                    ->with('error', 'Wystąpił błąd podczas sprawdzania statusu płatności. Prosimy o kontakt z obsługą.');
            }
        }
    }

    /**
     * Obsługuje webhook od bramki płatności
     */
    public function webhook(Request $request, $gateway)
    {
        // Znajdź bramkę płatności
        $paymentGateway = PaymentGateway::where('code', $gateway)
            ->where('is_active', true)
            ->first();
        
        if (!$paymentGateway) {
            return response()->json(['error' => 'Nieznana bramka płatności'], 404);
        }
        
        // Loguj otrzymane dane
        Log::info('Otrzymano webhook od bramki ' . $gateway, [
            'payload' => $request->all(),
            'headers' => $request->header(),
        ]);
        
        try {
            // Przetwórz webhook przez odpowiednią bramkę
            $result = $this->gatewayManager->handleWebhook($gateway, $request->all(), $request->headers->all());
            
            if ($result['success']) {
                return response()->json(['status' => 'ok', 'message' => $result['message']]);
            } else {
                Log::warning('Błąd przetwarzania webhooka: ' . ($result['message'] ?? 'Nieznany błąd'), [
                    'gateway' => $gateway,
                    'result' => $result,
                ]);
                
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            }
        } catch (\Exception $e) {
            Log::error('Wyjątek podczas przetwarzania webhooka: ' . $e->getMessage(), [
                'gateway' => $gateway,
                'exception' => $e,
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Wewnętrzny błąd serwera'], 500);
        }
    }
} 
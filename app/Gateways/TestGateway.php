<?php

namespace App\Gateways;

use App\Interfaces\GatewayInterface;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class TestGateway implements GatewayInterface
{
    /**
     * Model bramki płatności
     *
     * @var PaymentGateway
     */
    protected $gateway;

    /**
     * Konstruktor
     *
     * @param PaymentGateway $gateway
     */
    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Pobiera URL przekierowania do bramki płatności
     *
     * @param PaymentTransaction $transaction
     * @return string|null
     */
    public function getRedirectUrl(PaymentTransaction $transaction)
    {
        // W przypadku bramki testowej, generujemy unikalny identyfikator transakcji
        if (!$transaction->transaction_id) {
            $transaction->transaction_id = 'TEST_' . date('Ymd_His') . '_' . Str::random(6);
            $transaction->payment_method = 'test_payment';
            $transaction->description = $transaction->description ?: 'Testowa płatność #' . time();
            $transaction->save();
        }
        
        // Dla bramki testowej tworzymy stronę z symulacją płatności
        $params = [
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'transaction_id' => $transaction->transaction_id,
            'description' => $transaction->description,
            'return_url' => route('checkout.return', ['transaction_id' => $transaction->transaction_id]),
        ];
        
        // Testowa bramka przekierowuje bezpośrednio na stronę potwierdzenia
        return route('checkout.confirmation', $transaction->transaction_id);
    }

    /**
     * Sprawdza status płatności
     *
     * @param PaymentTransaction $transaction
     * @return array
     */
    public function checkPaymentStatus(PaymentTransaction $transaction)
    {
        // Sprawdzamy, czy transakcja już została przetworzona
        if ($transaction->isCompleted()) {
            return [
                'success' => true,
                'status' => 'completed',
                'message' => 'Płatność już została przetworzona pomyślnie',
                'details' => [
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'payment_method' => $transaction->payment_method,
                    'processed_at' => $transaction->updated_at->toIso8601String(),
                ],
            ];
        }
        
        // Sprawdzamy, czy transakcja została anulowana lub zwrócona
        if ($transaction->isCanceled() || $transaction->isRefunded()) {
            return [
                'success' => false,
                'status' => $transaction->status,
                'message' => 'Płatność została ' . ($transaction->isCanceled() ? 'anulowana' : 'zwrócona'),
                'details' => null,
            ];
        }
        
        // Sprawdzamy, czy transakcja nie powiodła się
        if ($transaction->isFailed()) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Płatność nie powiodła się: ' . $transaction->error_message,
                'details' => null,
            ];
        }
        
        // W przypadku bramki testowej, dla transakcji oczekujących zwracamy sukces
        if ($transaction->status === PaymentTransaction::STATUS_PENDING) {
            // Zaktualizuj status subskrypcji
            $subscriptionService = app(SubscriptionService::class);
            
            if ($transaction->subscription) {
                $subscriptionService->activateSubscription($transaction->subscription);
            }
            
            // Aktualizuj status transakcji na zakończony
            $transaction->markAsCompleted([
                'gateway' => 'test',
                'payment_method' => $transaction->payment_method,
                'processed_at' => now()->toIso8601String(),
            ]);
            
            // Zwróć informację o sukcesie
            return [
                'success' => true,
                'status' => 'completed',
                'message' => 'Płatność została przetworzona pomyślnie',
                'details' => [
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'payment_method' => $transaction->payment_method,
                    'processed_at' => now()->toIso8601String(),
                ],
            ];
        }
        
        // Jeśli status jest już inny niż pending, zwróć aktualny stan
        return [
            'success' => true,
            'status' => $transaction->status,
            'message' => 'Status płatności: ' . $transaction->status,
            'details' => $transaction->gateway_response,
        ];
    }

    /**
     * Przetwarza zwrot płatności
     *
     * @param PaymentTransaction $transaction
     * @param float|null $amount
     * @param string $reason
     * @return array
     */
    public function refundPayment(PaymentTransaction $transaction, $amount = null, $reason = '')
    {
        // Sprawdzamy, czy transakcja już została zwrócona
        if ($transaction->isRefunded()) {
            return [
                'success' => false,
                'message' => 'Płatność została już zwrócona',
                'details' => $transaction->gateway_response,
            ];
        }
        
        // Sprawdzamy, czy transakcja jest zakończona (tylko zakończone transakcje można zwrócić)
        if (!$transaction->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Nie można zwrócić płatności o statusie: ' . $transaction->status,
                'details' => null,
            ];
        }
        
        // W przypadku bramki testowej, zwracamy zawsze sukces
        $refundAmount = $amount ?? $transaction->amount;
        
        Log::info('Przetwarzanie zwrotu w bramce testowej', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $refundAmount,
            'reason' => $reason,
        ]);
        
        // Wygeneruj identyfikator zwrotu
        $refundId = 'REFUND_' . date('Ymd_His') . '_' . Str::random(6);
        
        // Przygotuj dane zwrotu
        $refundDetails = [
            'refund_id' => $refundId,
            'original_transaction_id' => $transaction->transaction_id,
            'amount' => $refundAmount,
            'currency' => $transaction->currency,
            'reason' => $reason,
            'processed_at' => now()->toIso8601String(),
        ];
        
        // Zaktualizuj status transakcji
        $transaction->markAsRefunded($refundDetails);
        
        return [
            'success' => true,
            'refund_id' => $refundId,
            'amount' => $refundAmount,
            'message' => 'Zwrot został przetworzony pomyślnie',
            'details' => $refundDetails,
        ];
    }

    /**
     * Obsługuje webhook od bramki płatności
     *
     * @param array $payload
     * @param array $headers
     * @return array
     */
    public function handleWebhook(array $payload, array $headers = [])
    {
        // Logujemy informację o otrzymanym webhooku
        Log::info('Webhook testowej bramki płatności', [
            'payload' => $payload,
            'headers' => $headers,
        ]);
        
        // W przypadku bramki testowej, webhook może być używany do symulacji różnych zdarzeń płatności
        if (isset($payload['event_type']) && isset($payload['transaction_id'])) {
            try {
                // Znajdź transakcję na podstawie identyfikatora
                $transaction = PaymentTransaction::where('transaction_id', $payload['transaction_id'])->first();
                
                if (!$transaction) {
                    return [
                        'success' => false,
                        'message' => 'Nie znaleziono transakcji o ID: ' . $payload['transaction_id'],
                    ];
                }
                
                // Przetwarzaj różne typy zdarzeń
                switch ($payload['event_type']) {
                    case 'payment.completed':
                        if ($transaction->status === PaymentTransaction::STATUS_PENDING) {
                            // Symuluj zakończenie płatności
                            $subscriptionService = app(SubscriptionService::class);
                            
                            if ($transaction->subscription) {
                                $subscriptionService->activateSubscription($transaction->subscription);
                            }
                            
                            $transaction->markAsCompleted([
                                'gateway' => 'test',
                                'webhook_event' => 'payment.completed',
                                'payment_method' => $transaction->payment_method,
                                'processed_at' => now()->toIso8601String(),
                            ]);
                            
                            return [
                                'success' => true,
                                'message' => 'Webhook przetworzony: płatność zakończona',
                                'transaction_id' => $transaction->transaction_id,
                                'status' => $transaction->status,
                            ];
                        }
                        break;
                        
                    case 'payment.failed':
                        if ($transaction->status === PaymentTransaction::STATUS_PENDING) {
                            $transaction->markAsFailed(
                                $payload['error_message'] ?? 'Płatność nie powiodła się (webhook)',
                                [
                                    'gateway' => 'test',
                                    'webhook_event' => 'payment.failed',
                                    'processed_at' => now()->toIso8601String(),
                                ]
                            );
                            
                            return [
                                'success' => true,
                                'message' => 'Webhook przetworzony: płatność nie powiodła się',
                                'transaction_id' => $transaction->transaction_id,
                                'status' => $transaction->status,
                            ];
                        }
                        break;
                        
                    case 'payment.refunded':
                        if ($transaction->status === PaymentTransaction::STATUS_COMPLETED) {
                            $refundId = 'WEBHOOK_REFUND_' . date('Ymd_His') . '_' . Str::random(6);
                            
                            $transaction->markAsRefunded([
                                'gateway' => 'test',
                                'webhook_event' => 'payment.refunded',
                                'refund_id' => $refundId,
                                'amount' => $payload['amount'] ?? $transaction->amount,
                                'processed_at' => now()->toIso8601String(),
                            ]);
                            
                            return [
                                'success' => true,
                                'message' => 'Webhook przetworzony: płatność zwrócona',
                                'transaction_id' => $transaction->transaction_id,
                                'status' => $transaction->status,
                            ];
                        }
                        break;
                        
                    default:
                        return [
                            'success' => false,
                            'message' => 'Nieznany typ zdarzenia: ' . $payload['event_type'],
                        ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Nie można przetworzyć transakcji o statusie: ' . $transaction->status,
                ];
            } catch (\Exception $e) {
                Log::error('Błąd podczas przetwarzania webhooka testowej bramki płatności: ' . $e->getMessage(), [
                    'exception' => $e,
                    'payload' => $payload,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Wystąpił błąd podczas przetwarzania webhooka: ' . $e->getMessage(),
                ];
            }
        }
        
        // Domyślna odpowiedź, jeśli nie ma danych do przetworzenia
        return [
            'success' => true,
            'message' => 'Webhook został odebrany, ale nie zawiera danych do przetworzenia',
        ];
    }
} 
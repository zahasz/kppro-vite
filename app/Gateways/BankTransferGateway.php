<?php

namespace App\Gateways;

use App\Interfaces\GatewayInterface;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BankTransferGateway implements GatewayInterface
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
        // W przypadku przelewu bankowego, generujemy unikalny identyfikator transakcji
        if (!$transaction->transaction_id) {
            $transaction->transaction_id = 'BANK' . time() . Str::random(8);
            $transaction->payment_method = 'bank_transfer';
            $transaction->save();
        }
        
        // Dla przelewu bankowego przekierowujemy na stronę z instrukcjami
        $params = [
            'transaction_id' => $transaction->transaction_id,
        ];
        
        // Przekieruj na stronę z instrukcjami przelewu
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
        // W przypadku przelewu bankowego, status musi być ustawiony ręcznie przez administratora
        return [
            'success' => true,
            'status' => $transaction->status,
            'message' => 'Status płatności przelewem bankowym: ' . $transaction->status,
            'details' => null,
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
        // W przypadku przelewu bankowego, zwrot musi być obsłużony ręcznie
        $refundAmount = $amount ?? $transaction->amount;
        
        Log::info('Żądanie zwrotu dla przelewu bankowego', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $refundAmount,
            'reason' => $reason,
        ]);
        
        // Wygeneruj identyfikator zwrotu
        $refundId = 'REFUND_BANK' . time() . Str::random(6);
        
        return [
            'success' => true,
            'refund_id' => $refundId,
            'amount' => $refundAmount,
            'message' => 'Zlecenie zwrotu zostało zapisane. Zwrot należy wykonać ręcznie.',
            'details' => [
                'refund_id' => $refundId,
                'original_transaction_id' => $transaction->transaction_id,
                'amount' => $refundAmount,
                'currency' => $transaction->currency,
                'reason' => $reason,
                'manual_process_required' => true,
            ],
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
        // W przypadku przelewu bankowego, webhook nie jest używany
        Log::info('Webhook bramki przelewu bankowego (nieobsługiwany)', [
            'payload' => $payload,
            'headers' => $headers,
        ]);
        
        return [
            'success' => true,
            'message' => 'Webhook został przetworzony, choć dla przelewu bankowego nie jest używany',
        ];
    }
    
    /**
     * Pobiera dane do przelewu
     * 
     * @return array
     */
    public function getTransferDetails()
    {
        return [
            'account_number' => $this->gateway->getConfig('account_number', 'Brak numeru konta'),
            'account_name' => $this->gateway->getConfig('account_name', 'Brak nazwy odbiorcy'),
            'bank_name' => $this->gateway->getConfig('bank_name', 'Brak nazwy banku'),
        ];
    }
} 
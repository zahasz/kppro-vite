<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Interfaces\GatewayInterface;
use Illuminate\Support\Facades\Log;

class PaymentGatewayManager
{
    /**
     * Rejestr bramek płatności
     *
     * @var array
     */
    protected $gateways = [];

    /**
     * Konstruktor menedżera bramek płatności
     */
    public function __construct()
    {
        $this->loadGateways();
    }

    /**
     * Ładuje wszystkie aktywne bramki płatności z bazy danych
     */
    protected function loadGateways()
    {
        try {
            $gateways = PaymentGateway::where('is_active', true)->get();
            
            foreach ($gateways as $gateway) {
                $this->gateways[$gateway->code] = $gateway;
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas ładowania bramek płatności: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    /**
     * Pobiera bramkę płatności o określonym kodzie
     *
     * @param string $code
     * @return PaymentGateway|null
     */
    public function getGateway($code)
    {
        return $this->gateways[$code] ?? null;
    }

    /**
     * Pobiera wszystkie aktywne bramki płatności
     *
     * @return array
     */
    public function getAvailableGateways()
    {
        return $this->gateways;
    }

    /**
     * Pobiera URL przekierowania do bramki płatności
     *
     * @param string $gatewayCode
     * @param PaymentTransaction $transaction
     * @return string|null
     */
    public function getRedirectUrl($gatewayCode, PaymentTransaction $transaction)
    {
        $gateway = $this->getGateway($gatewayCode);
        
        if (!$gateway) {
            Log::error('Nieznana bramka płatności: ' . $gatewayCode);
            return null;
        }
        
        try {
            $gatewayInstance = $gateway->getGatewayInstance();
            
            if (!$gatewayInstance instanceof GatewayInterface) {
                Log::error('Klasa bramki płatności nie implementuje interfejsu GatewayInterface', [
                    'gateway_code' => $gatewayCode,
                    'class_name' => get_class($gatewayInstance),
                ]);
                return null;
            }
            
            return $gatewayInstance->getRedirectUrl($transaction);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania URL przekierowania: ' . $e->getMessage(), [
                'gateway_code' => $gatewayCode,
                'transaction_id' => $transaction->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }

    /**
     * Sprawdza status transakcji w bramce płatności
     *
     * @param PaymentTransaction $transaction
     * @return array
     */
    public function checkPaymentStatus(PaymentTransaction $transaction)
    {
        $gateway = $this->getGateway($transaction->gateway_code);
        
        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Nieznana bramka płatności: ' . $transaction->gateway_code,
            ];
        }
        
        try {
            $gatewayInstance = $gateway->getGatewayInstance();
            
            if (!$gatewayInstance instanceof GatewayInterface) {
                return [
                    'success' => false,
                    'message' => 'Klasa bramki płatności nie implementuje interfejsu GatewayInterface',
                ];
            }
            
            return $gatewayInstance->checkPaymentStatus($transaction);
        } catch (\Exception $e) {
            Log::error('Błąd podczas sprawdzania statusu płatności: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'gateway_code' => $transaction->gateway_code,
                'exception' => $e,
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas sprawdzania statusu płatności: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Odświeża status transakcji w bramce płatności
     *
     * @param PaymentTransaction $transaction
     * @return array
     */
    public function refreshTransactionStatus(PaymentTransaction $transaction)
    {
        $result = $this->checkPaymentStatus($transaction);
        
        if ($result['success']) {
            // Aktualizuj status transakcji
            $status = $result['status'] ?? null;
            
            if ($status) {
                switch ($status) {
                    case 'completed':
                    case 'success':
                        $transaction->markAsCompleted($result['details'] ?? null);
                        break;
                    case 'failed':
                    case 'error':
                        $transaction->markAsFailed($result['message'] ?? 'Płatność nie powiodła się', $result['details'] ?? null);
                        break;
                    case 'pending':
                        // Status pozostaje bez zmian
                        break;
                    case 'refunded':
                        $transaction->markAsRefunded($result['details'] ?? null);
                        break;
                    case 'canceled':
                        $transaction->markAsCanceled($result['message'] ?? 'Płatność została anulowana', $result['details'] ?? null);
                        break;
                }
            }
        }
        
        return $result;
    }

    /**
     * Obsługuje zwrot płatności
     *
     * @param PaymentTransaction $transaction
     * @param float|null $amount
     * @param string $reason
     * @return array
     */
    public function refundPayment(PaymentTransaction $transaction, $amount = null, $reason = '')
    {
        $gateway = $this->getGateway($transaction->gateway_code);
        
        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Nieznana bramka płatności: ' . $transaction->gateway_code,
            ];
        }
        
        try {
            $gatewayInstance = $gateway->getGatewayInstance();
            
            if (!$gatewayInstance instanceof GatewayInterface) {
                return [
                    'success' => false,
                    'message' => 'Klasa bramki płatności nie implementuje interfejsu GatewayInterface',
                ];
            }
            
            $result = $gatewayInstance->refundPayment($transaction, $amount, $reason);
            
            if ($result['success']) {
                $transaction->markAsRefunded($result['details'] ?? null);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania zwrotu: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'gateway_code' => $transaction->gateway_code,
                'amount' => $amount,
                'reason' => $reason,
                'exception' => $e,
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas przetwarzania zwrotu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obsługuje webhook od bramki płatności
     *
     * @param string $gatewayCode
     * @param array $payload
     * @param array $headers
     * @return array
     */
    public function handleWebhook($gatewayCode, array $payload, array $headers = [])
    {
        $gateway = $this->getGateway($gatewayCode);
        
        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Nieznana bramka płatności: ' . $gatewayCode,
            ];
        }
        
        try {
            $gatewayInstance = $gateway->getGatewayInstance();
            
            if (!$gatewayInstance instanceof GatewayInterface) {
                return [
                    'success' => false,
                    'message' => 'Klasa bramki płatności nie implementuje interfejsu GatewayInterface',
                ];
            }
            
            return $gatewayInstance->handleWebhook($payload, $headers);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania webhooka: ' . $e->getMessage(), [
                'gateway_code' => $gatewayCode,
                'payload' => $payload,
                'exception' => $e,
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas przetwarzania webhooka: ' . $e->getMessage(),
            ];
        }
    }
} 
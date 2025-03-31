<?php

namespace App\Interfaces;

use App\Models\PaymentTransaction;

interface GatewayInterface
{
    /**
     * Pobiera URL przekierowania do bramki płatności
     *
     * @param PaymentTransaction $transaction
     * @return string|null
     */
    public function getRedirectUrl(PaymentTransaction $transaction);

    /**
     * Sprawdza status płatności
     *
     * @param PaymentTransaction $transaction
     * @return array
     */
    public function checkPaymentStatus(PaymentTransaction $transaction);

    /**
     * Przetwarza zwrot płatności
     *
     * @param PaymentTransaction $transaction
     * @param float|null $amount
     * @param string $reason
     * @return array
     */
    public function refundPayment(PaymentTransaction $transaction, $amount = null, $reason = '');

    /**
     * Obsługuje webhook od bramki płatności
     *
     * @param array $payload
     * @param array $headers
     * @return array
     */
    public function handleWebhook(array $payload, array $headers = []);
} 
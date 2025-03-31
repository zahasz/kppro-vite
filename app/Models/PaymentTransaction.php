<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    /**
     * Stałe statusów transakcji
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CANCELED = 'canceled';

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'reference_id',
        'user_id',
        'subscription_id',
        'invoice_id',
        'amount',
        'currency',
        'status',
        'gateway_code',
        'payment_method',
        'payment_details',
        'description',
        'error_message',
        'gateway_response',
        'metadata',
    ];

    /**
     * Atrybuty, które powinny być rzutowane na typy.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Relacja do użytkownika
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacja do subskrypcji
     */
    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    /**
     * Relacja do faktury
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relacja do bramki płatności
     */
    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_code', 'code');
    }

    /**
     * Aktualizuje status transakcji
     *
     * @param string $status
     * @param string|null $errorMessage
     * @param array|null $gatewayResponse
     * @return bool
     */
    public function updateStatus($status, $errorMessage = null, $gatewayResponse = null)
    {
        $this->status = $status;
        
        if ($errorMessage) {
            $this->error_message = $errorMessage;
        }
        
        if ($gatewayResponse) {
            $this->gateway_response = $gatewayResponse;
        }
        
        return $this->save();
    }

    /**
     * Oznacza transakcję jako zakończoną
     *
     * @param array|null $gatewayResponse
     * @return bool
     */
    public function markAsCompleted($gatewayResponse = null)
    {
        return $this->updateStatus(self::STATUS_COMPLETED, null, $gatewayResponse);
    }

    /**
     * Oznacza transakcję jako nieudaną
     *
     * @param string $errorMessage
     * @param array|null $gatewayResponse
     * @return bool
     */
    public function markAsFailed($errorMessage, $gatewayResponse = null)
    {
        return $this->updateStatus(self::STATUS_FAILED, $errorMessage, $gatewayResponse);
    }

    /**
     * Oznacza transakcję jako zwróconą
     *
     * @param array|null $gatewayResponse
     * @return bool
     */
    public function markAsRefunded($gatewayResponse = null)
    {
        return $this->updateStatus(self::STATUS_REFUNDED, null, $gatewayResponse);
    }

    /**
     * Oznacza transakcję jako anulowaną
     *
     * @param string|null $reason
     * @param array|null $gatewayResponse
     * @return bool
     */
    public function markAsCanceled($reason = null, $gatewayResponse = null)
    {
        return $this->updateStatus(self::STATUS_CANCELED, $reason, $gatewayResponse);
    }

    /**
     * Sprawdza, czy transakcja jest zakończona
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Sprawdza, czy transakcja jest nieudana
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Sprawdza, czy transakcja jest w toku
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Sprawdza, czy transakcja została zwrócona
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Sprawdza, czy transakcja została anulowana
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }
} 
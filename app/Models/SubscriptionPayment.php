<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPayment extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_subscription_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_details',
        'refunded_at',
        'refund_amount',
        'refund_reason',
        'refund_transaction_id',
    ];
    
    /**
     * Atrybuty, które powinny być rzutowane na typy natywne.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'refund_amount' => 'float',
        'refunded_at' => 'datetime',
    ];
    
    /**
     * Stałe statusów płatności.
     */
    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    
    /**
     * Pobierz użytkownika, do którego należy płatność.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Pobierz subskrypcję, do której należy płatność.
     */
    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'user_subscription_id');
    }
    
    /**
     * Sprawdź, czy płatność została zakończona.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    
    /**
     * Sprawdź, czy płatność oczekuje na przetworzenie.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    /**
     * Sprawdź, czy płatność nie powiodła się.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }
    
    /**
     * Sprawdź, czy płatność została zwrócona.
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED || $this->status === self::STATUS_PARTIALLY_REFUNDED;
    }
    
    /**
     * Sprawdź, czy płatność została całkowicie zwrócona.
     *
     * @return bool
     */
    public function isFullyRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }
    
    /**
     * Sprawdź, czy płatność została częściowo zwrócona.
     *
     * @return bool
     */
    public function isPartiallyRefunded()
    {
        return $this->status === self::STATUS_PARTIALLY_REFUNDED;
    }
    
    /**
     * Pobierz sformatowaną kwotę.
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', ' ') . ' ' . $this->currency;
    }
    
    /**
     * Pobierz sformatowaną kwotę zwrotu.
     *
     * @return string|null
     */
    public function getFormattedRefundAmountAttribute()
    {
        if ($this->refund_amount === null) {
            return null;
        }
        
        return number_format($this->refund_amount, 2, ',', ' ') . ' ' . $this->currency;
    }
    
    /**
     * Pobierz sformatowany status.
     *
     * @return string
     */
    public function getFormattedStatusAttribute()
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return 'Opłacona';
            case self::STATUS_PENDING:
                return 'Oczekująca';
            case self::STATUS_FAILED:
                return 'Nieudana';
            case self::STATUS_REFUNDED:
                return 'Zwrócona';
            case self::STATUS_PARTIALLY_REFUNDED:
                return 'Częściowo zwrócona';
            default:
                return ucfirst($this->status);
        }
    }
    
    /**
     * Pobierz klasę CSS dla statusu.
     *
     * @return string
     */
    public function getStatusClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_PENDING:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_FAILED:
                return 'bg-red-100 text-red-800';
            case self::STATUS_REFUNDED:
                return 'bg-gray-100 text-gray-800';
            case self::STATUS_PARTIALLY_REFUNDED:
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
    
    /**
     * Pobierz sformatowaną metodę płatności.
     *
     * @return string
     */
    public function getFormattedPaymentMethodAttribute()
    {
        switch ($this->payment_method) {
            case 'card':
                return 'Karta płatnicza';
            case 'paypal':
                return 'PayPal';
            case 'bank_transfer':
                return 'Przelew bankowy';
            case 'free':
                return 'Darmowy plan';
            default:
                return $this->payment_method;
        }
    }
    
    /**
     * Pobierz skrócony identyfikator transakcji.
     *
     * @return string
     */
    public function getShortTransactionIdAttribute()
    {
        if (strlen($this->transaction_id) <= 12) {
            return $this->transaction_id;
        }
        
        return substr($this->transaction_id, 0, 8) . '...';
    }
}

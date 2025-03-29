<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'price',
        'start_date',
        'end_date',
        'trial_ends_at',
        'next_billing_date',
        'last_invoice_id',
        'last_invoice_number',
        'cancelled_at',
        'subscription_type',
        'renewal_status',
        'payment_method',
        'payment_details',
        'admin_notes',
        'auto_renew'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
        'auto_renew' => 'boolean'
    ];

    // Typy subskrypcji
    const TYPE_MANUAL = 'manual';
    const TYPE_AUTOMATIC = 'automatic';
    
    // Statusy subskrypcji
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    
    // Statusy odnowienia
    const RENEWAL_ENABLED = 'enabled';
    const RENEWAL_DISABLED = 'disabled';

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan that the subscription belongs to
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Get the payments associated with this subscription
     */
    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_id');
    }

    /**
     * Get the last invoice generated for this subscription
     */
    public function lastInvoice()
    {
        return $this->belongsTo(Invoice::class, 'last_invoice_id');
    }

    /**
     * Get all invoices generated for this subscription
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }

    /**
     * Scope a query to only include active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if the subscription is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the subscription is in trial period
     */
    public function onTrial()
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the subscription is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED || $this->cancelled_at !== null;
    }

    /**
     * Check if the subscription has ended
     */
    public function hasEnded()
    {
        return $this->end_date !== null && $this->end_date->isPast();
    }

    /**
     * Check if the subscription is manual
     */
    public function isManual()
    {
        return $this->subscription_type === self::TYPE_MANUAL;
    }
    
    /**
     * Check if the subscription is automatic
     */
    public function isAutomatic()
    {
        return $this->subscription_type === self::TYPE_AUTOMATIC;
    }
    
    /**
     * Check if renewal is enabled
     */
    public function isRenewalEnabled()
    {
        return $this->renewal_status === self::RENEWAL_ENABLED || $this->auto_renew === true;
    }
    
    /**
     * Enable automatic renewal
     */
    public function enableRenewal()
    {
        if ($this->isAutomatic()) {
            $this->update([
                'renewal_status' => self::RENEWAL_ENABLED,
                'auto_renew' => true
            ]);
            return true;
        }
        return false;
    }
    
    /**
     * Disable automatic renewal
     */
    public function disableRenewal()
    {
        if ($this->isAutomatic()) {
            $this->update([
                'renewal_status' => self::RENEWAL_DISABLED,
                'auto_renew' => false
            ]);
            return true;
        }
        return false;
    }

    /**
     * Cancel the subscription
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'renewal_status' => self::RENEWAL_DISABLED,
            'auto_renew' => false
        ]);
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatusAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Aktywna';
            case 'cancelled':
                return 'Anulowana';
            case 'expired':
                return 'Wygasła';
            case 'pending':
                return 'Oczekująca';
            case 'trial':
                return 'Okres próbny';
            default:
                return $this->status;
        }
    }

    /**
     * Get formatted subscription type for display
     */
    public function getFormattedTypeAttribute()
    {
        switch ($this->subscription_type) {
            case self::TYPE_MANUAL:
                return 'Ręczna';
            case self::TYPE_AUTOMATIC:
                return 'Automatyczna';
            default:
                return $this->subscription_type;
        }
    }

    /**
     * Check if the subscription can be renewed
     */
    public function canBeRenewed()
    {
        // Tylko automatyczne subskrypcje mogą być odnawiane
        if (!$this->isAutomatic()) {
            return false;
        }
        
        // Sprawdź czy subskrypcja jest aktywna
        if (!$this->isActive()) {
            return false;
        }
        
        // Sprawdź czy funkcja automatycznego odnawiania jest włączona
        if (!$this->isRenewalEnabled()) {
            return false;
        }
        
        // Sprawdź czy subskrypcja nie została anulowana
        if ($this->isCancelled()) {
            return false;
        }
        
        // Sprawdź czy data zakończenia subskrypcji jest bliska (np. 7 dni)
        $renewalWindow = config('subscription.renewal_window_days', 7);
        $shouldRenewAfter = Carbon::now();
        $shouldRenewBefore = Carbon::now()->addDays($renewalWindow);
        
        return $this->end_date && 
               $this->end_date->greaterThanOrEqualTo($shouldRenewAfter) && 
               $this->end_date->lessThanOrEqualTo($shouldRenewBefore);
    }

    /**
     * Sprawdza czy subskrypcja wygasła
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->end_date) {
            return false;
        }
        
        return $this->status === self::STATUS_EXPIRED || $this->end_date->isPast();
    }

    /**
     * Sprawdza czy subskrypcja jest w okresie próbnym
     *
     * @return bool
     */
    public function isInTrial()
    {
        if (!$this->trial_ends_at) {
            return false;
        }
        
        return $this->trial_ends_at->isFuture();
    }
}

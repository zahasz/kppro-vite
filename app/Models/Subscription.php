<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'price',
        'start_date',
        'end_date',
        'trial_ends_at',
        'cancelled_at',
        'subscription_type',
        'renewal_status',
        'next_payment_date',
        'payment_method',
        'last_payment_id',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'next_payment_date' => 'datetime',
        'price' => 'decimal:2',
    ];

    const TYPE_MANUAL = 'manual';
    const TYPE_AUTOMATIC = 'automatic';
    
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
        return $this->belongsTo(Plan::class);
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
        return $this->status === 'active' && 
               ($this->end_date === null || $this->end_date->isFuture());
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'renewal_status' => self::RENEWAL_DISABLED,
        ]);
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
    
    public function isRenewalEnabled()
    {
        return $this->renewal_status === self::RENEWAL_ENABLED;
    }
    
    public function enableRenewal()
    {
        if ($this->isAutomatic()) {
            $this->update(['renewal_status' => self::RENEWAL_ENABLED]);
            return true;
        }
        return false;
    }
    
    public function disableRenewal()
    {
        if ($this->isAutomatic()) {
            $this->update(['renewal_status' => self::RENEWAL_DISABLED]);
            return true;
        }
        return false;
    }
} 
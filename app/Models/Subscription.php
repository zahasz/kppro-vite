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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

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
    
    public function isAutomatic()
    {
        return $this->subscription_type === self::TYPE_AUTOMATIC;
    }
    
    public function isManual()
    {
        return $this->subscription_type === self::TYPE_MANUAL;
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
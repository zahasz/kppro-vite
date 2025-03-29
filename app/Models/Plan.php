<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'interval',
        'trial_period_days',
        'subscription_type',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'features' => 'array',
        'is_active' => 'boolean',
        'trial_period_days' => 'integer',
    ];

    // Typy subskrypcji
    const TYPE_MANUAL = 'manual';
    const TYPE_AUTOMATIC = 'automatic';
    const TYPE_BOTH = 'both';

    /**
     * Get all subscriptions for the plan
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    /**
     * Get only active subscriptions for the plan
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id')->where('status', 'active');
    }

    /**
     * Sprawdza czy plan obsługuje ręczną subskrypcję
     */
    public function supportsManualSubscription()
    {
        return $this->subscription_type === self::TYPE_MANUAL || $this->subscription_type === self::TYPE_BOTH;
    }

    /**
     * Sprawdza czy plan obsługuje automatyczną subskrypcję
     */
    public function supportsAutomaticSubscription()
    {
        return $this->subscription_type === self::TYPE_AUTOMATIC || $this->subscription_type === self::TYPE_BOTH;
    }
} 
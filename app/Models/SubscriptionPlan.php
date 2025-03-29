<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'currency',
        'billing_period',
        'max_users',
        'max_invoices',
        'max_products',
        'max_clients',
        'features',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'price' => 'float',
        'features' => 'array',
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'max_invoices' => 'integer',
        'max_products' => 'integer',
        'max_clients' => 'integer',
        'display_order' => 'integer'
    ];

    /**
     * Pobiera aktywne subskrypcje dla tego planu
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'subscription_plan_id')->where('status', 'active');
    }

    /**
     * Pobiera wszystkie subskrypcje dla tego planu
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'subscription_plan_id');
    }
}

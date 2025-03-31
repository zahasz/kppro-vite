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

    /**
     * Alias dla metody subscriptions - dla zachowania zgodności z kodem w kontrolerze
     */
    public function userSubscriptions()
    {
        return $this->subscriptions();
    }

    /**
     * Relacja z uprawnieniami
     */
    public function permissions()
    {
        return $this->belongsToMany(SubscriptionPermission::class, 'subscription_permission_plan');
    }

    /**
     * Relacja z modułami
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'subscription_plan_modules', 'subscription_plan_id', 'module_id')
            ->withPivot('limitations')
            ->withTimestamps();
    }

    /**
     * Sprawdza, czy plan ma dostęp do określonego modułu
     * 
     * @param string $moduleCode
     * @return bool
     */
    public function hasModuleAccess(string $moduleCode): bool
    {
        return $this->modules()->where('code', $moduleCode)->exists();
    }

    /**
     * Pobiera limity dla modułu w tym planie
     * 
     * @param string $moduleCode
     * @return array|null
     */
    public function getModuleLimitations(string $moduleCode)
    {
        $module = $this->modules()->where('code', $moduleCode)->first();
        return $module ? $module->pivot->limitations : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można przypisać masowo
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    /**
     * Atrybuty, które powinny być rzutowane
     * 
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Pobiera uprawnienia użytkowników do tego modułu
     */
    public function userPermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }

    /**
     * Pobiera użytkowników z dostępem do tego modułu
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_module_permissions')
            ->withPivot(['access_granted', 'restrictions', 'valid_until', 'granted_by'])
            ->withTimestamps();
    }

    /**
     * Pobiera plany subskrypcji, które mają dostęp do tego modułu
     */
    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'subscription_plan_modules')
            ->withTimestamps();
    }

    /**
     * Sprawdza, czy moduł jest aktywny
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}

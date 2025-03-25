<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPermission extends Model
{
    use HasFactory;

    /**
     * Pola, które można przypisać masowo.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'feature_flag'
    ];

    /**
     * Relacja wiele do wielu z planami subskrypcji.
     */
    public function plans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'subscription_permission_plan', 'permission_id', 'plan_id')
            ->withTimestamps();
    }
}

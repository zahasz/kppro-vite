<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można przypisać masowo
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'module_id',
        'access_granted',
        'restrictions',
        'valid_until',
        'granted_by',
    ];

    /**
     * Atrybuty, które powinny być rzutowane
     * 
     * @var array
     */
    protected $casts = [
        'access_granted' => 'boolean',
        'restrictions' => 'array',
        'valid_until' => 'datetime',
    ];

    /**
     * Pobiera użytkownika, do którego należy uprawnienie
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pobiera moduł, do którego odnosi się uprawnienie
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Sprawdza, czy uprawnienie jest aktywne (nie wygasło)
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        if (!$this->access_granted) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Pobiera ograniczenia jako obiekt
     * 
     * @return object|null
     */
    public function getRestrictions()
    {
        return $this->restrictions ? (object) $this->restrictions : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_system'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean'
    ];

    // Relacje
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    // Metody pomocnicze
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasAnyPermission(array $permissions)
    {
        return !empty(array_intersect($permissions, $this->permissions ?? []));
    }

    public function hasAllPermissions(array $permissions)
    {
        return empty(array_diff($permissions, $this->permissions ?? []));
    }

    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
    }

    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $this->permissions = array_values($permissions);
            $this->save();
        }
    }

    public function syncPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();
    }
}

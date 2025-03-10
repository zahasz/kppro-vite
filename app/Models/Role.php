<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Contracts\Role as RoleContract;

class Role extends SpatieRole implements RoleContract
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'is_system'
    ];

    protected $casts = [
        'is_system' => 'boolean'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->guarded = [];
        $this->guard_name = $this->guard_name ?: config('auth.defaults.guard');
    }
} 
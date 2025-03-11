<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'address',
        'city',
        'postal_code',
        'nip',
        'regon',
        'phone',
        'email',
        'website'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/logo.svg');
    }
} 
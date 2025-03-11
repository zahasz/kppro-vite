<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'nip',
        'regon',
        'email',
        'phone',
        'street',
        'postal_code',
        'city',
        'country',
        'bank_name',
        'bank_account_number',
        'swift_code',
        'notes',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
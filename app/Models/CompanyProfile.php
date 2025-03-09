<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'legal_form',
        'tax_number',
        'regon',
        'krs',
        'street',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'phone_additional',
        'email',
        'email_additional',
        'website',
        'bank_name',
        'bank_account',
        'swift',
        'logo_path',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

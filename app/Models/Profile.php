<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'company_name',
        'tax_number',
        'regon',
        'krs',
        'email',
        'phone',
        'website',
        'street',
        'street_number',
        'apartment_number',
        'postal_code',
        'city',
        'country',
        'bank_name',
        'bank_account_number',
        'default_payment_method',
        'default_payment_deadline_days',
        'invoice_notes',
        'logo_path'
    ];

    protected $casts = [
        'default_payment_deadline_days' => 'integer',
    ];

    // Relacje
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Akcesory
    public function getFullNameAttribute()
    {
        if ($this->type === 'company') {
            return $this->company_name;
        }
        
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->street) {
            $address[] = $this->street . ' ' . $this->street_number;
            if ($this->apartment_number) {
                $address[count($address) - 1] .= '/' . $this->apartment_number;
            }
        }
        
        if ($this->postal_code || $this->city) {
            $address[] = trim($this->postal_code . ' ' . $this->city);
        }
        
        if ($this->country && $this->country !== 'Polska') {
            $address[] = $this->country;
        }
        
        return implode(', ', $address);
    }

    // Metody pomocnicze
    public function isCompany()
    {
        return $this->type === 'company';
    }

    public function isIndividual()
    {
        return $this->type === 'individual';
    }
}

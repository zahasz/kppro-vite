<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        'default_bank_account_id',
        'logo_path',
        'notes',
        'invoice_prefix',
        'invoice_numbering_pattern',
        'invoice_next_number',
        'invoice_payment_days',
        'default_payment_method',
        'default_currency',
        'invoice_notes',
        'invoice_footer',
    ];

    protected $casts = [
        'invoice_next_number' => 'integer',
        'invoice_payment_days' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacja z kontami bankowymi
     *
     * @return HasMany
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Relacja z domyślnym kontem bankowym
     *
     * @return BelongsTo
     */
    public function defaultBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'default_bank_account_id');
    }

    /**
     * Generuje następny numer faktury na podstawie wzorca numeracji
     */
    public function generateNextInvoiceNumber(): string
    {
        $pattern = $this->invoice_numbering_pattern ?? 'FV/{YEAR}/{MONTH}/{NUMBER}';
        $number = $this->invoice_next_number ?? 1;
        
        $replacements = [
            '{YEAR}' => date('Y'),
            '{MONTH}' => date('m'),
            '{NUMBER}' => str_pad($number, 3, '0', STR_PAD_LEFT),
            '{DAY}' => date('d'),
        ];
        
        $invoiceNumber = $this->invoice_prefix ? $this->invoice_prefix . ' ' : '';
        $invoiceNumber .= str_replace(array_keys($replacements), array_values($replacements), $pattern);
        
        // Zwiększ licznik
        $this->invoice_next_number = $number + 1;
        $this->save();
        
        return $invoiceNumber;
    }
}

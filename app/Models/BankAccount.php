<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_profile_id',
        'account_name',
        'account_number',
        'bank_name',
        'swift',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Relacja z profilem firmy
     *
     * @return BelongsTo
     */
    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    /**
     * Ustawia to konto jako domyślne, resetując inne konta
     */
    public function setAsDefault(): void
    {
        // Najpierw resetujemy wszystkie konta dla tego profilu firmy
        self::where('company_profile_id', $this->company_profile_id)
            ->update(['is_default' => false]);
        
        // Ustawiamy to konto jako domyślne
        $this->is_default = true;
        $this->save();
        
        // Aktualizujemy referencję w profilu firmy
        $this->companyProfile->default_bank_account_id = $this->id;
        $this->companyProfile->save();
    }
}

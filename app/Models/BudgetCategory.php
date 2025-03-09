<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
        'amount',
        'planned_amount',
        'description',
        'invoice_id',
        'salary_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'planned_amount' => 'decimal:2',
    ];

    const TYPES = [
        'cash' => 'Gotówka',
        'company_bank' => 'Konto firmowe',
        'private_bank' => 'Konto prywatne',
        'investments' => 'Inwestycje',
        'leasing' => 'Leasing',
        'income' => 'Przychody',
        'expenses' => 'Wydatki',
        'loans_taken' => 'Pożyczki zaciągnięte',
        'loans_given' => 'Pożyczki udzielone',
        'invoices_to_pay' => 'Faktury do opłacenia',
        'invoices_receivable' => 'Należności z faktur',
        'salaries_to_pay' => 'Wynagrodzenia do rozliczenia'
    ];

    // Relacja do modułu faktur
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relacja do modułu wynagrodzeń
    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    // Automatyczne aktualizowanie kwoty na podstawie powiązanych modułów
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if ($category->type === 'invoices_to_pay' && $category->invoice_id) {
                $category->amount = $category->invoice->amount_to_pay ?? 0;
            }
            if ($category->type === 'invoices_receivable' && $category->invoice_id) {
                $category->amount = $category->invoice->amount_receivable ?? 0;
            }
            if ($category->type === 'salaries_to_pay' && $category->salary_id) {
                $category->amount = $category->salary->amount_to_pay ?? 0;
            }
        });
    }

    // Metoda do aktualizacji kwoty z powiązanego modułu
    public function updateAmountFromRelatedModule()
    {
        if ($this->type === 'invoices_to_pay' && $this->invoice_id) {
            $this->update(['amount' => $this->invoice->amount_to_pay ?? 0]);
        }
        if ($this->type === 'invoices_receivable' && $this->invoice_id) {
            $this->update(['amount' => $this->invoice->amount_receivable ?? 0]);
        }
        if ($this->type === 'salaries_to_pay' && $this->salary_id) {
            $this->update(['amount' => $this->salary->amount_to_pay ?? 0]);
        }
    }
} 
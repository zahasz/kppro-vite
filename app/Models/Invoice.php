<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'number',
        'order_number',
        'corrected_invoice_number',
        'correction_reason',
        'contractor_name',
        'contractor_nip',
        'contractor_address',
        'payment_method',
        'delivery_terms',
        'delivery_method',
        'delivery_address',
        'issue_date',
        'sale_date',
        'due_date',
        'net_total',
        'tax_total',
        'gross_total',
        'currency',
        'notes',
        'issued_by',
        'received_by',
        'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'sale_date' => 'date',
        'due_date' => 'date',
        'net_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'gross_total' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', now());
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['issued', 'overdue']);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('issue_date', now()->month)
                    ->whereYear('issue_date', now()->year);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    public function markAsOverdue()
    {
        if ($this->status !== 'paid' && $this->due_date < now()) {
            $this->update(['status' => 'overdue']);
        }
    }
}

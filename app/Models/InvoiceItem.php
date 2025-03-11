<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'name',
        'description',
        'quantity',
        'unit',
        'net_price',
        'net_value',
        'tax_rate',
        'tax_value',
        'gross_value',
        'position'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'net_price' => 'decimal:2',
        'net_value' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'gross_value' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!$item->position) {
                $item->position = $item->invoice->items()->max('position') + 1;
            }
        });

        static::saving(function ($item) {
            $item->net_value = $item->quantity * $item->net_price;
            $item->tax_value = $item->net_value * ($item->tax_rate / 100);
            $item->gross_value = $item->net_value + $item->tax_value;
        });
    }
}

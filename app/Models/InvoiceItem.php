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
        'unit_price',
        'net_price',
        'tax_rate',
        'tax_amount',
        'gross_price',
        'position'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'net_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'gross_price' => 'decimal:2'
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
            $item->net_price = $item->quantity * $item->unit_price;
            $item->tax_amount = $item->net_price * ($item->tax_rate / 100);
            $item->gross_price = $item->net_price + $item->tax_amount;
        });
    }
}

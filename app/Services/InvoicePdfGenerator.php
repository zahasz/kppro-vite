<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfGenerator
{
    protected Invoice $invoice;
    
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
    
    public function generate(): string
    {
        $pdf = PDF::loadView('pdf.invoice', [
            'invoice' => $this->invoice,
            'contractor' => $this->invoice->contractor,
            'items' => $this->invoice->items,
            'company' => config('company'),
        ]);
        
        $filename = sprintf(
            'invoices/%s/%s.pdf',
            $this->invoice->created_at->format('Y/m'),
            $this->invoice->number
        );
        
        Storage::put($filename, $pdf->output());
        
        return storage_path('app/' . $filename);
    }
    
    protected function calculateTotals(): array
    {
        $totals = [
            'net' => 0,
            'vat' => 0,
            'gross' => 0,
            'by_vat_rate' => [],
        ];
        
        foreach ($this->invoice->items as $item) {
            $net = $item->quantity * $item->net_price;
            $vat = $net * ($item->vat_rate / 100);
            $gross = $net + $vat;
            
            $totals['net'] += $net;
            $totals['vat'] += $vat;
            $totals['gross'] += $gross;
            
            if (!isset($totals['by_vat_rate'][$item->vat_rate])) {
                $totals['by_vat_rate'][$item->vat_rate] = [
                    'net' => 0,
                    'vat' => 0,
                    'gross' => 0,
                ];
            }
            
            $totals['by_vat_rate'][$item->vat_rate]['net'] += $net;
            $totals['by_vat_rate'][$item->vat_rate]['vat'] += $vat;
            $totals['by_vat_rate'][$item->vat_rate]['gross'] += $gross;
        }
        
        return $totals;
    }
} 
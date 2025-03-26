<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktura {{ $invoice->number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-info, .contractor-info {
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .info-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="invoice-title">
                Faktura VAT {{ $invoice->number }}
            </div>
            <div>
                Data wystawienia: {{ $invoice->issue_date->format('d.m.Y') }}<br>
                Data sprzedaży: {{ $invoice->sale_date->format('d.m.Y') }}<br>
                Termin płatności: {{ $invoice->due_date->format('d.m.Y') }}
            </div>
        </div>

        <div class="row">
            <div class="company-info">
                <div class="info-box">
                    <div class="info-title">Sprzedawca:</div>
                    {{ $company['name'] }}<br>
                    {{ $company['address'] }}<br>
                    {{ $company['postal_code'] }} {{ $company['city'] }}<br>
                    NIP: {{ $company['tax_id'] }}<br>
                    {{ $company['phone'] }}<br>
                    {{ $company['email'] }}
                </div>
            </div>

            <div class="contractor-info">
                <div class="info-box">
                    <div class="info-title">Nabywca:</div>
                    {{ $contractor->name }}<br>
                    {{ $contractor->address }}<br>
                    {{ $contractor->postal_code }} {{ $contractor->city }}<br>
                    NIP: {{ $contractor->tax_id }}<br>
                    {{ $contractor->phone }}<br>
                    {{ $contractor->email }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Lp.</th>
                    <th>Nazwa</th>
                    <th>J.m.</th>
                    <th>Ilość</th>
                    <th>Cena netto</th>
                    <th>Wartość netto</th>
                    <th>VAT %</th>
                    <th>Kwota VAT</th>
                    <th>Wartość brutto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->net_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->quantity * $item->net_price, 2) }}</td>
                        <td class="text-right">{{ $item->vat_rate }}%</td>
                        <td class="text-right">{{ number_format($item->quantity * $item->net_price * $item->vat_rate / 100, 2) }}</td>
                        <td class="text-right">{{ number_format($item->quantity * $item->net_price * (1 + $item->vat_rate / 100), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <th>Stawka VAT</th>
                    <th>Wartość netto</th>
                    <th>Kwota VAT</th>
                    <th>Wartość brutto</th>
                </tr>
                @foreach($invoice->calculateTotals()['by_vat_rate'] as $rate => $totals)
                    <tr>
                        <td>{{ $rate }}%</td>
                        <td class="text-right">{{ number_format($totals['net'], 2) }}</td>
                        <td class="text-right">{{ number_format($totals['vat'], 2) }}</td>
                        <td class="text-right">{{ number_format($totals['gross'], 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th>Razem</th>
                    <td class="text-right">{{ number_format($invoice->total_net, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->total_vat, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->total_gross, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="payment-info">
            <p>
                <strong>Sposób płatności:</strong> {{ $invoice->payment_method }}<br>
                <strong>Termin płatności:</strong> {{ $invoice->due_date->format('d.m.Y') }}<br>
                <strong>Numer konta:</strong> {{ $company['bank_account'] }}
            </p>
        </div>

        @if($invoice->notes)
            <div class="notes">
                <p><strong>Uwagi:</strong><br>{{ $invoice->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p>Dokument wygenerowany elektronicznie</p>
        </div>
    </div>
</body>
</html> 
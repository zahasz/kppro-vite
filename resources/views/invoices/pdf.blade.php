<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Faktura {{ $invoice->number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 30px;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .parties {
            margin-bottom: 30px;
        }
        .party {
            margin-bottom: 20px;
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
        .summary {
            float: right;
            width: 300px;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signatures {
            margin-top: 100px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 200px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Faktura VAT {{ $invoice->number }}</h1>
        @if($invoice->corrected_invoice_number)
            <h2>Korekta do faktury: {{ $invoice->corrected_invoice_number }}</h2>
            <p>Powód korekty: {{ $invoice->correction_reason }}</p>
        @endif
    </div>

    <div class="invoice-info">
        <p><strong>Data wystawienia:</strong> {{ $invoice->issue_date->format('d.m.Y') }}</p>
        <p><strong>Data sprzedaży:</strong> {{ $invoice->sale_date->format('d.m.Y') }}</p>
        <p><strong>Termin płatności:</strong> {{ $invoice->due_date->format('d.m.Y') }}</p>
        <p><strong>Sposób płatności:</strong> {{ $invoice->payment_method }}</p>
        @if($invoice->order_number)
            <p><strong>Numer zamówienia:</strong> {{ $invoice->order_number }}</p>
        @endif
    </div>

    <div class="parties">
        <div class="party">
            <h3>Sprzedawca:</h3>
            <p>{{ config('app.company_name') }}</p>
            <p>{{ config('app.company_address') }}</p>
            <p>NIP: {{ config('app.company_nip') }}</p>
        </div>

        <div class="party">
            <h3>Nabywca:</h3>
            <p>{{ $invoice->contractor_name }}</p>
            <p>{{ $invoice->contractor_address }}</p>
            <p>NIP: {{ $invoice->contractor_nip }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Lp.</th>
                <th>Nazwa</th>
                <th>Ilość</th>
                <th>J.m.</th>
                <th>Cena netto</th>
                <th>Wartość netto</th>
                <th>VAT %</th>
                <th>Kwota VAT</th>
                <th>Wartość brutto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->position }}</td>
                    <td>
                        {{ $item->name }}
                        @if($item->description)
                            <br><small>{{ $item->description }}</small>
                        @endif
                    </td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ number_format($item->net_price, 2) }} {{ $invoice->currency }}</td>
                    <td>{{ number_format($item->net_value, 2) }} {{ $invoice->currency }}</td>
                    <td>{{ number_format($item->tax_rate, 0) }}%</td>
                    <td>{{ number_format($item->tax_value, 2) }} {{ $invoice->currency }}</td>
                    <td>{{ number_format($item->gross_value, 2) }} {{ $invoice->currency }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <th>Wartość netto</th>
                <td>{{ number_format($invoice->net_total, 2) }} {{ $invoice->currency }}</td>
            </tr>
            <tr>
                <th>Kwota VAT</th>
                <td>{{ number_format($invoice->tax_total, 2) }} {{ $invoice->currency }}</td>
            </tr>
            <tr>
                <th>Wartość brutto</th>
                <td>{{ number_format($invoice->gross_total, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->notes)
        <div class="footer">
            <p><strong>Uwagi:</strong> {{ $invoice->notes }}</p>
        </div>
    @endif

    <div class="signatures">
        <div class="signature">
            {{ $invoice->issued_by ?: 'Wystawił' }}
        </div>
        <div class="signature">
            {{ $invoice->received_by ?: 'Odebrał' }}
        </div>
    </div>
</body>
</html> 
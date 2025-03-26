<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktura {{ $invoice->number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #007bff;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Faktura {{ $invoice->number }}</h1>
        </div>
        
        <div class="content">
            <p>Szanowny Kliencie,</p>
            
            <p>Dziękujemy za korzystanie z naszych usług. W załączniku znajduje się faktura za opłaconą subskrypcję.</p>
            
            <h2>Szczegóły faktury</h2>
            
            <table>
                <tr>
                    <th>Numer faktury</th>
                    <td>{{ $invoice->number }}</td>
                </tr>
                <tr>
                    <th>Data wystawienia</th>
                    <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d.m.Y') }}</td>
                </tr>
                <tr>
                    <th>Termin płatności</th>
                    <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') }}</td>
                </tr>
                <tr>
                    <th>Kwota netto</th>
                    <td>{{ number_format($invoice->net_total, 2) }} {{ $invoice->currency }}</td>
                </tr>
                <tr>
                    <th>Kwota VAT</th>
                    <td>{{ number_format($invoice->tax_total, 2) }} {{ $invoice->currency }}</td>
                </tr>
                <tr>
                    <th>Kwota brutto</th>
                    <td><strong>{{ number_format($invoice->gross_total, 2) }} {{ $invoice->currency }}</strong></td>
                </tr>
            </table>
            
            <p>Faktura jest załączona do tego e-maila w formacie PDF.</p>
            
            <p>W przypadku jakichkolwiek pytań, prosimy o kontakt z naszym działem obsługi klienta.</p>
            
            <p>Z poważaniem,<br>
            Zespół obsługi klienta</p>
        </div>
        
        <div class="footer">
            <p>Ta wiadomość została wygenerowana automatycznie, prosimy na nią nie odpowiadać.</p>
            <p>&copy; {{ date('Y') }} KPPRO. Wszelkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html> 
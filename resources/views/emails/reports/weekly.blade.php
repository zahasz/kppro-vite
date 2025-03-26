<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport tygodniowy ({{ $report['period']['start'] }} - {{ $report['period']['end'] }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #17a2b8;
        }
        .content {
            padding: 20px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #17a2b8;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
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
            background-color: #17a2b8;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .summary-box {
            background-color: #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #495057;
        }
        .trend-up {
            color: #28a745;
        }
        .trend-down {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Raport tygodniowy</h1>
            <p>{{ $report['period']['start'] }} - {{ $report['period']['end'] }}</p>
        </div>
        
        <div class="content">
            <div class="summary-box">
                <h3>Podsumowanie</h3>
                <p>
                    <strong>Przychód całkowity:</strong> {{ number_format($report['revenue']['total'], 2) }} PLN<br>
                    <strong>Nowe subskrypcje:</strong> {{ $report['subscriptions']['new'] }}<br>
                    <strong>Odnowione subskrypcje:</strong> {{ $report['subscriptions']['renewed'] }}<br>
                    <strong>Zakończone subskrypcje:</strong> {{ $report['subscriptions']['ended'] }}
                </p>
            </div>
            
            <div class="section">
                <h2>Subskrypcje</h2>
                <table>
                    <tr>
                        <th>Metryka</th>
                        <th>Wartość</th>
                    </tr>
                    <tr>
                        <td>Nowe subskrypcje</td>
                        <td>{{ $report['subscriptions']['new'] }}</td>
                    </tr>
                    <tr>
                        <td>Odnowione subskrypcje</td>
                        <td>{{ $report['subscriptions']['renewed'] }}</td>
                    </tr>
                    <tr>
                        <td>Zakończone subskrypcje</td>
                        <td>{{ $report['subscriptions']['ended'] }}</td>
                    </tr>
                    @if(isset($report['best_selling_plan']) && $report['best_selling_plan'])
                    <tr>
                        <td>Najpopularniejszy plan</td>
                        <td>{{ $report['best_selling_plan']['name'] }} ({{ $report['best_selling_plan']['count'] }} sprzedaży)</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div class="section">
                <h2>Przychody</h2>
                <table>
                    <tr>
                        <th>Źródło</th>
                        <th>Kwota</th>
                    </tr>
                    <tr>
                        <td>Nowe subskrypcje</td>
                        <td>{{ number_format($report['revenue']['new_subscriptions'], 2) }} PLN</td>
                    </tr>
                    <tr>
                        <td>Odnowienia</td>
                        <td>{{ number_format($report['revenue']['renewals'], 2) }} PLN</td>
                    </tr>
                    <tr>
                        <td><strong>Razem</strong></td>
                        <td><strong>{{ number_format($report['revenue']['total'], 2) }} PLN</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="section">
                <h2>Nieopłacone faktury</h2>
                <p>Aktualnie mamy {{ $report['unpaid_invoices']['count'] }} nieopłaconych faktur na łączną kwotę {{ number_format($report['unpaid_invoices']['value'], 2) }} PLN.</p>
                
                @if($report['unpaid_invoices']['count'] > 0)
                <p style="text-align: center;">
                    <a href="{{ url('/admin/billing/invoices?status=issued') }}" class="btn">Zobacz nieopłacone faktury</a>
                </p>
                @endif
            </div>
            
            <p style="text-align: center;">
                <a href="{{ url('/admin/billing/reports') }}" class="btn">Zobacz pełny raport</a>
            </p>
        </div>
        
        <div class="footer">
            <p>Ten raport został wygenerowany automatycznie.</p>
            <p>&copy; {{ date('Y') }} KPPRO. Wszelkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html> 
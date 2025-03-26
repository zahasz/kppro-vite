<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twoja subskrypcja wygasa za {{ $daysRemaining }} dni</title>
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
            border-bottom: 3px solid #dc3545;
        }
        .content {
            padding: 20px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            background-color: #28a745;
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
            <h1>Przypomnienie o subskrypcji</h1>
        </div>
        
        <div class="content">
            <p>Szanowny Kliencie {{ $user->name }},</p>
            
            <div class="warning">
                <strong>Uwaga:</strong> Twoja subskrypcja wygasa za {{ $daysRemaining }} dni.
            </div>
            
            <h2>Szczegóły subskrypcji</h2>
            
            <p>
                <strong>Data wygaśnięcia:</strong> {{ \Carbon\Carbon::parse($subscription->ends_at)->format('d.m.Y') }}<br>
                @if(isset($subscription->plan_id))
                <strong>Plan:</strong> {{ DB::table('subscription_plans')->where('id', $subscription->plan_id)->value('name') ?? 'Standardowy' }}<br>
                @endif
                <strong>Pozostało dni:</strong> {{ $daysRemaining }}
            </p>
            
            <p>Aby kontynuować korzystanie z naszych usług bez przerwy, zachęcamy do odnowienia subskrypcji przed jej wygaśnięciem.</p>
            
            <p style="text-align: center;">
                <a href="{{ url('/subscriptions/renew') }}" class="btn">Odnów subskrypcję teraz</a>
            </p>
            
            <p>Po wygaśnięciu subskrypcji dostęp do niektórych funkcji może zostać ograniczony lub całkowicie wyłączony.</p>
            
            <p>Jeśli masz pytania dotyczące odnowienia subskrypcji, skontaktuj się z naszym działem obsługi klienta.</p>
            
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
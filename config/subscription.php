<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ustawienia Subskrypcji
    |--------------------------------------------------------------------------
    |
    | Ten plik zawiera konfigurację modułu subskrypcji, w tym parametry
    | dotyczące odnowień, płatności, powiadomień itp.
    |
    */

    // Dni przed wygaśnięciem subskrypcji, kiedy próbujemy ją odnowić
    'renewal_window_days' => env('SUBSCRIPTION_RENEWAL_WINDOW_DAYS', 7),
    
    // Czy generować faktury automatycznie po płatnościach
    'auto_generate_invoices' => env('SUBSCRIPTION_AUTO_GENERATE_INVOICES', true),
    
    // Domyślna waluta dla subskrypcji
    'default_currency' => env('SUBSCRIPTION_DEFAULT_CURRENCY', 'PLN'),
    
    // Domyślna metoda płatności
    'default_payment_method' => env('SUBSCRIPTION_DEFAULT_PAYMENT_METHOD', 'card'),
    
    // Domyślny czas trwania okresu próbnego (w dniach)
    'default_trial_period_days' => env('SUBSCRIPTION_DEFAULT_TRIAL_PERIOD_DAYS', 14),
    
    // Maksymalna liczba prób odnowienia przy niepowodzeniu płatności
    'max_renewal_attempts' => env('SUBSCRIPTION_MAX_RENEWAL_ATTEMPTS', 3),
    
    // Dni zwłoki między próbami odnowienia
    'renewal_retry_days' => env('SUBSCRIPTION_RENEWAL_RETRY_DAYS', 3),
    
    // Czy wysyłać powiadomienia o zbliżającym się odnowieniu
    'send_renewal_notifications' => env('SUBSCRIPTION_SEND_RENEWAL_NOTIFICATIONS', true),
    
    // Liczba dni przed odnowieniem, kiedy wysyłamy powiadomienie
    'renewal_notification_days' => env('SUBSCRIPTION_RENEWAL_NOTIFICATION_DAYS', 3),
    
    // Czas ważności sesji płatności w minutach
    'payment_session_lifetime' => env('SUBSCRIPTION_PAYMENT_SESSION_LIFETIME', 30),
    
    // Domyślny typ subskrypcji: manual, automatic
    'default_subscription_type' => env('SUBSCRIPTION_DEFAULT_TYPE', 'manual'),
    
    // Mapowanie statusów płatności
    'payment_statuses' => [
        'completed' => 'Opłacona',
        'pending' => 'Oczekująca',
        'failed' => 'Nieudana',
        'refunded' => 'Zwrócona',
        'partially_refunded' => 'Częściowo zwrócona',
    ],
    
    // Mapowanie metod płatności
    'payment_methods' => [
        'card' => 'Karta płatnicza',
        'bank_transfer' => 'Przelew bankowy',
        'paypal' => 'PayPal',
        'cash' => 'Gotówka',
        'other' => 'Inna',
    ],
    
    // Mapowanie statusów subskrypcji
    'subscription_statuses' => [
        'active' => 'Aktywna',
        'pending' => 'Oczekująca',
        'cancelled' => 'Anulowana',
        'expired' => 'Wygasła',
        'trial' => 'Okres próbny',
    ],
]; 
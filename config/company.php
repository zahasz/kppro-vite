<?php

return [
    'name' => env('COMPANY_NAME', 'Nazwa Firmy Sp. z o.o.'),
    'address' => env('COMPANY_ADDRESS', 'ul. Przykładowa 1'),
    'postal_code' => env('COMPANY_POSTAL_CODE', '00-001'),
    'city' => env('COMPANY_CITY', 'Warszawa'),
    'tax_id' => env('COMPANY_TAX_ID', '1234567890'),
    'phone' => env('COMPANY_PHONE', '+48 123 456 789'),
    'email' => env('COMPANY_EMAIL', 'kontakt@firma.pl'),
    'bank_account' => env('COMPANY_BANK_ACCOUNT', '12 1234 1234 1234 1234 1234 1234'),
    'website' => env('COMPANY_WEBSITE', 'https://firma.pl'),
    
    'invoice_prefix' => env('INVOICE_PREFIX', 'FV'),
    'invoice_numbering_pattern' => env('INVOICE_NUMBERING_PATTERN', '{PREFIX}/{MONTH}/{YEAR}/{NUMBER}'),
    'invoice_next_number' => env('INVOICE_NEXT_NUMBER', 1),
    'invoice_payment_days' => env('INVOICE_PAYMENT_DAYS', 14),
    
    'subscription_settings' => [
        'auto_generate_invoices' => env('AUTO_GENERATE_INVOICES', true),
        'invoice_generation_day' => env('INVOICE_GENERATION_DAY', 1),
        'payment_reminder_days' => env('PAYMENT_REMINDER_DAYS', 3),
        'overdue_reminder_days' => env('OVERDUE_REMINDER_DAYS', 7),
    ],
]; 
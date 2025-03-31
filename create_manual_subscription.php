<?php

// Załaduj autoloader Composera
require __DIR__ . '/vendor/autoload.php';

// Załaduj aplikację Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Użyj faseady DB dla trybu transakcji
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Carbon\Carbon;

echo "Rozpoczynam proces ręcznej sprzedaży subskrypcji...\n";

try {
    // Znajdź administratora
    $user = User::where('role', 'admin')
        ->orWhere('is_admin', true)
        ->orWhere('email', 'admin@example.com')
        ->first();

    if (!$user) {
        echo "Nie znaleziono administratora w systemie. Tworzę tymczasowego użytkownika...\n";
        $user = User::first();
        
        if (!$user) {
            echo "Brak jakiegokolwiek użytkownika w systemie. Nie można kontynuować.\n";
            exit(1);
        }
    }

    echo "Znaleziono użytkownika: {$user->name} (ID: {$user->id})\n";

    // Znajdź plan subskrypcji (przykładowo 'business')
    $plan = SubscriptionPlan::where('code', 'business')->first();
    
    if (!$plan) {
        $plan = SubscriptionPlan::where('is_active', true)->first();
    }

    if (!$plan) {
        echo "Nie znaleziono żadnego aktywnego planu subskrypcji. Nie można kontynuować.\n";
        exit(1);
    }

    echo "Wybrany plan subskrypcji: {$plan->name} (ID: {$plan->id})\n";

    // Rozpocznij transakcję
    DB::beginTransaction();

    echo "Tworzę nową subskrypcję...\n";

    // Utwórz subskrypcję ręcznie
    $subscription = new UserSubscription();
    $subscription->user_id = $user->id;
    $subscription->subscription_plan_id = $plan->id;
    $subscription->status = 'active';
    $subscription->price = $plan->price;
    $subscription->start_date = Carbon::now();
    $subscription->end_date = Carbon::now()->addMonth(); // Miesięczna subskrypcja
    $subscription->subscription_type = 'manual';
    $subscription->renewal_status = null;
    $subscription->payment_method = 'cash';
    $subscription->payment_details = 'Płatność gotówką przyjęta przez administratora';
    $subscription->admin_notes = 'Ręczna sprzedaż subskrypcji przez administratora';
    $subscription->save();

    echo "Subskrypcja utworzona (ID: {$subscription->id})\n";

    // Utwórz płatność
    echo "Tworzę płatność...\n";
    
    $payment = new SubscriptionPayment();
    $payment->user_id = $user->id;
    $payment->subscription_id = $subscription->id;
    $payment->transaction_id = 'manual-cash-' . time();
    $payment->amount = $plan->price;
    $payment->currency = $plan->currency ?? 'PLN';
    $payment->status = 'completed';
    $payment->payment_method = 'cash';
    $payment->payment_details = 'Płatność gotówką';
    $payment->save();

    echo "Płatność utworzona (ID: {$payment->id})\n";

    // Wygeneruj fakturę
    echo "Generuję fakturę...\n";

    // Pobierz serwis subskrypcji
    $subscriptionService = app(SubscriptionService::class);
    
    // Wywołaj metodę prywatną przez Reflection API
    $reflection = new ReflectionClass($subscriptionService);
    $method = $reflection->getMethod('generateInvoiceForPayment');
    $method->setAccessible(true);
    $invoice = $method->invoke($subscriptionService, $payment);

    if ($invoice) {
        echo "Faktura wygenerowana (Numer: {$invoice->number}, ID: {$invoice->id})\n";
        
        // Aktualizuj subskrypcję o identyfikator faktury
        $subscription->last_invoice_id = $invoice->id;
        $subscription->last_invoice_number = $invoice->number;
        $subscription->save();
    } else {
        echo "Nie udało się wygenerować faktury. Sprawdź logi systemowe.\n";
    }

    // Zatwierdź transakcję
    DB::commit();

    echo "Proces sprzedaży subskrypcji zakończony pomyślnie!\n";
    echo "====================================================\n";
    echo "Podsumowanie:\n";
    echo "Użytkownik: {$user->name} (ID: {$user->id})\n";
    echo "Plan subskrypcji: {$plan->name} (ID: {$plan->id})\n";
    echo "Status subskrypcji: {$subscription->status}\n";
    echo "Data rozpoczęcia: {$subscription->start_date->format('Y-m-d')}\n";
    echo "Data zakończenia: {$subscription->end_date->format('Y-m-d')}\n";
    echo "Metoda płatności: {$subscription->payment_method}\n";
    echo "Kwota: {$payment->amount} {$payment->currency}\n";
    
    if ($invoice) {
        echo "Numer faktury: {$invoice->number}\n";
        echo "Link do faktury: " . url("/admin/billing/invoices/{$invoice->id}") . "\n";
    }

} catch (\Exception $e) {
    // W przypadku błędu, cofnij transakcję
    DB::rollBack();
    
    echo "BŁĄD: {$e->getMessage()}\n";
    echo "Wystąpił w: {$e->getFile()}:{$e->getLine()}\n";
    echo "Ślad wywołań:\n{$e->getTraceAsString()}\n";
} 
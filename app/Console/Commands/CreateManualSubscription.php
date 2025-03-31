<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateManualSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:create-manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tworzy ręczną subskrypcję dla administratora i generuje fakturę';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczynam proces ręcznej sprzedaży subskrypcji...');

        try {
            // Znajdź administratora
            $user = User::where('role', 'admin')
                ->orWhere('is_admin', true)
                ->orWhere('email', 'admin@example.com')
                ->first();

            if (!$user) {
                $this->warn('Nie znaleziono administratora w systemie. Używam pierwszego użytkownika...');
                $user = User::first();
                
                if (!$user) {
                    $this->error('Brak jakiegokolwiek użytkownika w systemie. Nie można kontynuować.');
                    return 1;
                }
            }

            $this->info("Znaleziono użytkownika: {$user->name} (ID: {$user->id})");

            // Znajdź plan subskrypcji (przykładowo 'business')
            $plan = SubscriptionPlan::where('code', 'business')->first();
            
            if (!$plan) {
                $this->warn('Plan "business" nie został znaleziony. Używam pierwszego aktywnego planu...');
                $plan = SubscriptionPlan::where('is_active', true)->first();
            }

            if (!$plan) {
                $this->error('Nie znaleziono żadnego aktywnego planu subskrypcji. Nie można kontynuować.');
                return 1;
            }

            $this->info("Wybrany plan subskrypcji: {$plan->name} (ID: {$plan->id})");

            // Rozpocznij transakcję
            DB::beginTransaction();

            $this->info('Tworzę nową subskrypcję...');

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

            $this->info("Subskrypcja utworzona (ID: {$subscription->id})");

            // Utwórz płatność
            $this->info('Tworzę płatność...');
            
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

            $this->info("Płatność utworzona (ID: {$payment->id})");

            // Wygeneruj fakturę
            $this->info('Generuję fakturę...');

            // Pobierz serwis subskrypcji
            $subscriptionService = app(SubscriptionService::class);
            
            // Spróbuj wywołać metodę publiczną lub użyj Reflection API jeśli metoda jest prywatna
            $invoice = null;
            
            if (method_exists($subscriptionService, 'generateInvoiceForPayment')) {
                $invoice = $subscriptionService->generateInvoiceForPayment($payment);
            } else {
                // Użyj Reflection API
                $reflection = new \ReflectionClass($subscriptionService);
                $method = $reflection->getMethod('generateInvoiceForPayment');
                $method->setAccessible(true);
                $invoice = $method->invoke($subscriptionService, $payment);
            }

            if ($invoice) {
                $this->info("Faktura wygenerowana (Numer: {$invoice->number}, ID: {$invoice->id})");
                
                // Aktualizuj subskrypcję o identyfikator faktury
                $subscription->last_invoice_id = $invoice->id;
                $subscription->last_invoice_number = $invoice->number;
                $subscription->save();
            } else {
                $this->warn('Nie udało się wygenerować faktury. Sprawdź logi systemowe.');
            }

            // Zatwierdź transakcję
            DB::commit();

            $this->info('Proces sprzedaży subskrypcji zakończony pomyślnie!');
            $this->info('====================================================');
            $this->info('Podsumowanie:');
            $this->info("Użytkownik: {$user->name} (ID: {$user->id})");
            $this->info("Plan subskrypcji: {$plan->name} (ID: {$plan->id})");
            $this->info("Status subskrypcji: {$subscription->status}");
            $this->info("Data rozpoczęcia: {$subscription->start_date->format('Y-m-d')}");
            $this->info("Data zakończenia: {$subscription->end_date->format('Y-m-d')}");
            $this->info("Metoda płatności: {$subscription->payment_method}");
            $this->info("Kwota: {$payment->amount} {$payment->currency}");
            
            if ($invoice) {
                $this->info("Numer faktury: {$invoice->number}");
                $this->info("Link do faktury: " . url("/admin/billing/invoices/{$invoice->id}"));
            }

            return 0;
        } catch (\Exception $e) {
            // W przypadku błędu, cofnij transakcję
            DB::rollBack();
            
            $this->error("BŁĄD: {$e->getMessage()}");
            $this->error("Wystąpił w: {$e->getFile()}:{$e->getLine()}");
            $this->error($e->getTraceAsString());
            
            return 1;
        }
    }
} 
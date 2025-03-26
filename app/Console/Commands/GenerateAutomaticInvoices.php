<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use App\Models\AdminNotification;
use App\Models\Contractor;
use App\Models\BankAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateAutomaticInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-automatic {--force : Wymuś generowanie niezależnie od harmonogramu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatycznie generuje faktury dla aktywnych subskrypcji i cyklicznych płatności';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczynam automatyczne generowanie faktur...');
        $count = 0;

        try {
            // Sprawdzenie, czy dziś jest dzień generowania faktur (np. 1. dzień miesiąca)
            $today = Carbon::today();
            $isScheduledDay = $today->day === 1; // Przykładowo 1. dzień miesiąca
            
            if (!$isScheduledDay && !$this->option('force')) {
                $this->info('Dzisiaj nie jest dzień generowania faktur. Użyj opcji --force, aby wymusić generowanie.');
                return 0;
            }
            
            $this->info('Generowanie faktur dla aktywnych subskrypcji...');
            
            // Pobierz aktywne subskrypcje, które wymagają faktury
            $subscriptions = DB::table('user_subscriptions')
                ->join('users', 'user_subscriptions.user_id', '=', 'users.id')
                ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                ->whereNull('user_subscriptions.ends_at')
                ->orWhere('user_subscriptions.ends_at', '>', now())
                ->whereRaw('(MONTH(user_subscriptions.next_billing_date) = ? AND YEAR(user_subscriptions.next_billing_date) = ?)', 
                    [$today->month, $today->year])
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.email as user_email',
                    'subscription_plans.id as plan_id',
                    'subscription_plans.name as plan_name',
                    'subscription_plans.price as plan_price',
                    'subscription_plans.tax_rate as plan_tax_rate',
                    'subscription_plans.billing_cycle as billing_cycle',
                    'user_subscriptions.id as subscription_id',
                    'user_subscriptions.next_billing_date'
                )
                ->get();
            
            $this->info("Znaleziono {$subscriptions->count()} aktywnych subskrypcji do fakturowania.");
            
            foreach ($subscriptions as $subscription) {
                DB::beginTransaction();
                
                try {
                    // Pobierz dane użytkownika i jego profil firmy
                    $user = User::find($subscription->user_id);
                    $companyProfile = $user->companyProfile;
                    
                    if (!$companyProfile) {
                        $this->warn("Użytkownik {$user->name} nie ma profilu firmy. Pomijam generowanie faktury.");
                        continue;
                    }
                    
                    // Pobierz konto bankowe
                    $bankAccount = BankAccount::where('is_default', true)->first();
                    
                    // Generuj numer faktury
                    $lastInvoice = Invoice::whereYear('issue_date', $today->year)
                        ->whereMonth('issue_date', $today->month)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    $invoiceNumber = $lastInvoice 
                        ? $this->generateNextNumber($lastInvoice->number)
                        : "FV/AUT/{$today->format('m')}/{$today->format('Y')}/001";
                    
                    // Oblicz wartości
                    $netAmount = $subscription->plan_price;
                    $taxRate = $subscription->plan_tax_rate ?? 23; // Domyślnie 23% VAT
                    $taxAmount = $netAmount * ($taxRate / 100);
                    $grossAmount = $netAmount + $taxAmount;
                    
                    // Stwórz fakturę
                    $invoice = new Invoice();
                    $invoice->user_id = $subscription->user_id;
                    $invoice->number = $invoiceNumber;
                    $invoice->contractor_name = $companyProfile->company_name;
                    $invoice->contractor_nip = $companyProfile->tax_number;
                    $invoice->contractor_address = $this->formatAddress($companyProfile);
                    $invoice->payment_method = $companyProfile->default_payment_method ?? 'przelew';
                    $invoice->issue_date = $today;
                    $invoice->sale_date = $today;
                    $invoice->due_date = $today->copy()->addDays($companyProfile->invoice_payment_days ?? 14);
                    $invoice->net_total = $netAmount;
                    $invoice->tax_total = $taxAmount;
                    $invoice->gross_total = $grossAmount;
                    $invoice->currency = $companyProfile->default_currency ?? 'PLN';
                    $invoice->status = 'issued';
                    $invoice->bank_account_id = $bankAccount->id ?? null;
                    $invoice->notes = "Automatycznie wygenerowana faktura za subskrypcję: {$subscription->plan_name}";
                    $invoice->auto_generated = true;
                    $invoice->approval_status = 'approved'; // Automatyczne zatwierdzenie
                    $invoice->approved_at = now();
                    $invoice->approved_by = 1; // ID administratora
                    $invoice->save();
                    
                    // Dodaj pozycję faktury
                    $invoice->items()->create([
                        'name' => "Subskrypcja {$subscription->plan_name}",
                        'description' => "Okres rozliczeniowy: {$today->format('d.m.Y')} - " . 
                            $today->copy()->addMonth()->format('d.m.Y'),
                        'quantity' => 1,
                        'unit' => 'szt.',
                        'unit_price' => $netAmount,
                        'tax_rate' => $taxRate,
                        'net_price' => $netAmount,
                        'tax_amount' => $taxAmount,
                        'gross_price' => $grossAmount,
                    ]);
                    
                    // Zaktualizuj datę następnego rozliczenia
                    DB::table('user_subscriptions')
                        ->where('id', $subscription->subscription_id)
                        ->update([
                            'next_billing_date' => $this->calculateNextBillingDate(
                                $subscription->next_billing_date, 
                                $subscription->billing_cycle
                            ),
                            'last_invoice_id' => $invoice->id,
                            'last_invoice_number' => $invoice->number
                        ]);
                    
                    // Dodaj powiadomienie dla administratora
                    AdminNotification::createInvoiceNotification(
                        'Wygenerowano automatyczną fakturę',
                        "Automatycznie wygenerowano fakturę nr {$invoice->number} dla użytkownika {$user->name}",
                        route('admin.billing.invoices.show', $invoice->id),
                        [
                            'invoice_id' => $invoice->id,
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->subscription_id
                        ]
                    );
                    
                    $count++;
                    DB::commit();
                    
                    $this->info("Wygenerowano fakturę nr {$invoice->number} dla użytkownika {$user->name}");
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Błąd podczas generowania faktury dla użytkownika ID: {$subscription->user_id}: " . $e->getMessage());
                    Log::error("Błąd automatycznego generowania faktury: " . $e->getMessage(), [
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->subscription_id,
                        'exception' => $e
                    ]);
                }
            }
            
            $this->info("Zakończono. Wygenerowano {$count} faktur.");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Wystąpił błąd: " . $e->getMessage());
            Log::error("Błąd wykonania komendy GenerateAutomaticInvoices: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
    
    /**
     * Generuje następny numer faktury
     */
    private function generateNextNumber($lastNumber)
    {
        $parts = explode('/', $lastNumber);
        $number = (int)end($parts);
        $month = Carbon::today()->format('m');
        $year = Carbon::today()->format('Y');
        
        return "FV/AUT/{$month}/{$year}/" . sprintf('%03d', $number + 1);
    }
    
    /**
     * Formatuje adres z danych profilu firmy
     */
    private function formatAddress($companyProfile)
    {
        $address = [];
        
        if ($companyProfile->street) {
            $address[] = $companyProfile->street;
        }
        
        if ($companyProfile->postal_code || $companyProfile->city) {
            $address[] = trim($companyProfile->postal_code . ' ' . $companyProfile->city);
        }
        
        if ($companyProfile->country) {
            $address[] = $companyProfile->country;
        }
        
        return implode(', ', $address);
    }
    
    /**
     * Oblicza datę następnego rozliczenia na podstawie cyklu rozliczeniowego
     */
    private function calculateNextBillingDate($currentDate, $billingCycle)
    {
        $date = Carbon::parse($currentDate);
        
        switch ($billingCycle) {
            case 'monthly':
                return $date->addMonth();
            case 'quarterly':
                return $date->addMonths(3);
            case 'biannual':
                return $date->addMonths(6);
            case 'annual':
                return $date->addYear();
            default:
                return $date->addMonth();
        }
    }
}

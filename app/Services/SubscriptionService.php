<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Events\SubscriptionCreated;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionRenewed;
use App\Notifications\SubscriptionActivated;
use App\Notifications\SubscriptionCancelled as SubscriptionCancelledNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * @var PaymentGatewayService
     */
    protected $paymentGateway;

    /**
     * Konstruktor
     *
     * @param PaymentGatewayService $paymentGateway
     */
    public function __construct(PaymentGatewayService $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Tworzy nową subskrypcję dla użytkownika
     *
     * @param User $user
     * @param SubscriptionPlan $plan
     * @param array $data
     * @return array
     */
    public function createSubscription(User $user, SubscriptionPlan $plan, array $data)
    {
        try {
            DB::beginTransaction();
            
            // Sprawdź czy użytkownik już ma aktywną subskrypcję tego samego planu
            $existingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_plan_id', $plan->id)
                ->where('status', 'active')
                ->first();
            
            if ($existingSubscription) {
                return [
                    'success' => false,
                    'message' => 'Użytkownik już posiada aktywną subskrypcję tego planu.'
                ];
            }
            
            // Oblicz daty na podstawie planu
            $startDate = Carbon::parse($data['start_date'] ?? now());
            $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : $this->calculateEndDate($startDate, $plan->billing_period);
            
            // Ustal datę następnej płatności tylko dla automatycznych subskrypcji
            $nextBillingDate = null;
            if (($data['subscription_type'] ?? 'manual') === UserSubscription::TYPE_AUTOMATIC) {
                $nextBillingDate = $endDate;
            }
            
            // Utwórz nową subskrypcję
            $subscription = new UserSubscription();
            $subscription->user_id = $user->id;
            $subscription->subscription_plan_id = $plan->id;
            $subscription->status = $data['status'] ?? 'active';
            $subscription->price = $data['price'] ?? $plan->price;
            $subscription->start_date = $startDate;
            $subscription->end_date = $endDate;
            $subscription->subscription_type = $data['subscription_type'] ?? UserSubscription::TYPE_MANUAL;
            $subscription->renewal_status = isset($data['auto_renew']) && $data['auto_renew'] ? UserSubscription::RENEWAL_ENABLED : null;
            $subscription->next_billing_date = $nextBillingDate;
            $subscription->payment_method = $data['payment_method'] ?? 'manual';
            $subscription->payment_details = $data['payment_details'] ?? null;
            $subscription->admin_notes = $data['admin_notes'] ?? null;
            $subscription->save();
            
            // Jeśli subskrypcja wymaga płatności, utwórz rekord płatności
            if (isset($data['create_payment']) && $data['create_payment']) {
                $this->createPaymentForSubscription($subscription, $data);
            }
            
            // Wyślij powiadomienie do użytkownika, jeśli potrzeba
            if (isset($data['send_notification']) && $data['send_notification']) {
                $user->notify(new SubscriptionActivated($subscription));
            }
            
            // Wyemituj zdarzenie o utworzeniu subskrypcji
            event(new SubscriptionCreated($subscription));
            
            DB::commit();
            
            return [
                'success' => true,
                'subscription' => $subscription
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas tworzenia subskrypcji: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas tworzenia subskrypcji: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Odnawia subskrypcję użytkownika
     *
     * @param UserSubscription $subscription
     * @return array
     */
    public function renewSubscription(UserSubscription $subscription)
    {
        // Sprawdzenie, czy subskrypcja może być odnowiona
        if (!$subscription->canBeRenewed()) {
            return [
                'success' => false,
                'message' => 'Subskrypcja nie może zostać odnowiona',
                'subscription' => $subscription
            ];
        }

        try {
            DB::beginTransaction();

            $user = $subscription->user;
            $plan = $subscription->plan;

            // Obliczanie nowej daty zakończenia
            $newStartDate = $subscription->end_date ?? Carbon::now(); // Używamy daty zakończenia jako nowej daty startowej
            $newEndDate = $this->calculateEndDate($newStartDate, $plan->billing_period);

            // Tworzymy płatność dla odnowienia
            $payment = $this->createPaymentForSubscription($subscription, [
                'transaction_id' => 'renewal-' . time(),
                'payment_status' => 'completed'
            ]);

            // Aktualizacja dat subskrypcji
            $subscription->start_date = $newStartDate;
            $subscription->end_date = $newEndDate;
            $subscription->status = 'active';
            $subscription->next_billing_date = $newEndDate; // Ustaw następną datę rozliczenia
            $subscription->last_invoice_id = null; // Resetujemy ostatnią fakturę, zostanie utworzona nowa
            $subscription->last_invoice_number = null;
            $subscription->save();

            // Generowanie faktury, jeśli jest włączone
            if (config('subscription.auto_generate_invoices', true) && $payment) {
                $this->generateInvoiceForPayment($payment);
            }

            DB::commit();

            // Wyzwolenie zdarzenia SubscriptionRenewed
            event(new SubscriptionRenewed($subscription));

            return [
                'success' => true,
                'message' => 'Subskrypcja została odnowiona pomyślnie',
                'subscription' => $subscription,
                'payment' => $payment
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas odnawiania subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'plan_id' => $subscription->subscription_plan_id,
                'exception' => $e
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas odnawiania subskrypcji: ' . $e->getMessage(),
                'subscription' => $subscription
            ];
        }
    }

    /**
     * Anuluje subskrypcję użytkownika
     *
     * @param UserSubscription $subscription
     * @param array $data
     * @return array
     */
    public function cancelSubscription(UserSubscription $subscription, array $data = [])
    {
        try {
            DB::beginTransaction();
            
            // Ustaw status anulowania
            $subscription->status = 'cancelled';
            $subscription->cancelled_at = Carbon::now();
            $subscription->renewal_status = UserSubscription::RENEWAL_DISABLED;
            $subscription->auto_renew = false;
            
            // Dodaj powód anulowania jeśli podano
            if (isset($data['reason']) && !empty($data['reason'])) {
                $subscription->admin_notes = ($subscription->admin_notes ? $subscription->admin_notes . "\n" : '') . 
                    "Anulowano: " . $data['reason'] . " (" . now()->format('Y-m-d H:i:s') . ")";
            }
            
            // Jeśli anulowanie jest natychmiastowe, ustaw datę końcową na teraz
            if (isset($data['immediate']) && $data['immediate']) {
                $subscription->end_date = Carbon::now();
            }
            
            $subscription->save();
            
            // Wyemituj zdarzenie o anulowaniu subskrypcji
            event(new SubscriptionCancelled($subscription));
            
            // Powiadom użytkownika, jeśli potrzeba
            if (isset($data['send_notification']) && $data['send_notification']) {
                $subscription->user->notify(new SubscriptionCancelledNotification($subscription));
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Subskrypcja została anulowana pomyślnie',
                'subscription' => $subscription
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas anulowania subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas anulowania subskrypcji: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Aktualizuje subskrypcję użytkownika
     *
     * @param UserSubscription $subscription
     * @param array $data
     * @return array
     */
    public function updateSubscription(UserSubscription $subscription, array $data)
    {
        try {
            DB::beginTransaction();
            
            // Jeśli zmieniamy plan, sprawdź czy istnieje
            if (isset($data['plan_id']) && $data['plan_id'] != $subscription->subscription_plan_id) {
                $plan = SubscriptionPlan::findOrFail($data['plan_id']);
                $subscription->subscription_plan_id = $plan->id;
                $subscription->price = $data['price'] ?? $plan->price;
            }
            
            // Aktualizuj dane subskrypcji
            $subscription->status = $data['status'] ?? $subscription->status;
            $subscription->start_date = isset($data['start_date']) ? Carbon::parse($data['start_date']) : $subscription->start_date;
            $subscription->end_date = isset($data['end_date']) ? Carbon::parse($data['end_date']) : $subscription->end_date;
            $subscription->subscription_type = $data['subscription_type'] ?? $subscription->subscription_type;
            
            // Ustaw automatyczne odnawianie
            if (isset($data['auto_renew'])) {
                $subscription->renewal_status = $data['auto_renew'] ? UserSubscription::RENEWAL_ENABLED : UserSubscription::RENEWAL_DISABLED;
                $subscription->auto_renew = $data['auto_renew'];
            }
            
            // Aktualizuj datę następnej płatności, jeśli subskrypcja jest automatyczna
            if ($subscription->isAutomatic() && isset($data['end_date'])) {
                $subscription->next_billing_date = Carbon::parse($data['end_date']);
            }
            
            $subscription->payment_method = $data['payment_method'] ?? $subscription->payment_method;
            $subscription->payment_details = $data['payment_details'] ?? $subscription->payment_details;
            $subscription->admin_notes = $data['admin_notes'] ?? $subscription->admin_notes;
            
            // Jeśli subskrypcja jest anulowana, ustaw datę anulowania
            if ($subscription->status === 'cancelled' && !$subscription->cancelled_at) {
                $subscription->cancelled_at = now();
            }
            
            $subscription->save();
            
            DB::commit();
            
            return [
                'success' => true,
                'subscription' => $subscription
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas aktualizacji subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas aktualizacji subskrypcji: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Zwraca płatność za subskrypcję
     *
     * @param SubscriptionPayment $payment
     * @param float|null $amount
     * @param string $reason
     * @return array
     */
    public function refundPayment(SubscriptionPayment $payment, float $amount = null, string $reason = '')
    {
        try {
            DB::beginTransaction();

            // Przetwarzanie zwrotu przez bramkę płatności
            $refundResult = $this->paymentGateway->processRefund($payment, $amount, $reason);

            if (!$refundResult['success']) {
                throw new \Exception($refundResult['message']);
            }

            // Aktualizacja statusu płatności
            $payment->refunded_at = Carbon::now();
            $payment->refund_amount = $amount ?? $payment->amount;
            $payment->refund_reason = $reason;
            $payment->refund_transaction_id = $refundResult['refund_transaction_id'];
            $payment->status = $amount && $amount < $payment->amount ? 'partially_refunded' : 'refunded';
            $payment->save();

            // Jeśli zwrot jest pełny, możemy anulować subskrypcję
            if (!$amount || $amount == $payment->amount) {
                $subscription = $payment->subscription;
                if ($subscription) {
                    $subscription->status = 'cancelled';
                    $subscription->cancelled_at = Carbon::now();
                    $subscription->cancellation_reason = 'Payment refunded: ' . $reason;
                    $subscription->save();
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Płatność została zwrócona pomyślnie',
                'payment' => $payment,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas zwrotu płatności: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas zwrotu płatności: ' . $e->getMessage(),
                'payment' => $payment,
            ];
        }
    }

    /**
     * Oblicza datę zakończenia subskrypcji na podstawie daty początkowej i okresu rozliczeniowego
     *
     * @param Carbon $startDate
     * @param string $billingPeriod
     * @return Carbon
     */
    private function calculateEndDate(Carbon $startDate, string $billingPeriod): Carbon
    {
        $endDate = $startDate->copy();
        
        switch ($billingPeriod) {
            case 'monthly':
                $endDate->addMonth();
                break;
            case 'quarterly':
                $endDate->addMonths(3);
                break;
            case 'annually':
            case 'yearly':
                $endDate->addYear();
                break;
            case 'biannually':
                $endDate->addMonths(6);
                break;
            case 'lifetime':
                $endDate->addYears(100); // Praktycznie bez daty końcowej
                break;
            default:
                $endDate->addMonth(); // Domyślnie jeden miesiąc
        }
        
        return $endDate;
    }

    /**
     * Tworzy płatność dla subskrypcji
     *
     * @param UserSubscription $subscription
     * @param array $data
     * @return SubscriptionPayment|null
     */
    private function createPaymentForSubscription(UserSubscription $subscription, array $data)
    {
        try {
            $plan = $subscription->plan;
            
            $payment = new SubscriptionPayment();
            $payment->user_id = $subscription->user_id;
            $payment->subscription_id = $subscription->id;
            $payment->transaction_id = $data['transaction_id'] ?? 'manual-' . time();
            $payment->amount = $subscription->price;
            $payment->currency = $plan->currency ?? 'PLN';
            $payment->status = $data['payment_status'] ?? 'completed';
            $payment->payment_method = $subscription->payment_method;
            $payment->payment_details = $subscription->payment_details;
            $payment->save();
            
            return $payment;
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia płatności dla subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'exception' => $e
            ]);
            
            return null;
        }
    }

    /**
     * Generuje fakturę dla płatności za subskrypcję
     *
     * @param SubscriptionPayment $payment
     * @return mixed
     */
    private function generateInvoiceForPayment(SubscriptionPayment $payment)
    {
        try {
            $subscription = $payment->subscription;
            $user = $payment->user;
            $plan = $subscription->plan;
            
            // Sprawdzenie czy użytkownik ma profil firmy
            $companyProfile = $user->companyProfile;
            if (!$companyProfile) {
                Log::warning('Nie można wygenerować faktury - brak profilu firmy', [
                    'user_id' => $user->id,
                    'payment_id' => $payment->id
                ]);
                return null;
            }
            
            // Pobierz domyślne konto bankowe
            $bankAccount = $companyProfile->defaultBankAccount ?? null;
            
            // Generuj numer faktury
            $invoiceNumber = $companyProfile->generateNextInvoiceNumber();
            
            // Utwórz fakturę
            $invoice = new \App\Models\Invoice();
            $invoice->user_id = $user->id;
            $invoice->subscription_id = $subscription->id;
            $invoice->number = $invoiceNumber;
            $invoice->contractor_name = $companyProfile->company_name;
            $invoice->contractor_nip = $companyProfile->tax_number;
            $invoice->contractor_address = $this->formatAddress($companyProfile);
            $invoice->payment_method = $subscription->payment_method;
            $invoice->issue_date = now();
            $invoice->sale_date = now();
            $invoice->due_date = now()->addDays($companyProfile->invoice_payment_days ?? 14);
            $invoice->net_total = $payment->amount / (1 + ($plan->tax_rate ?? 23) / 100);
            $invoice->tax_total = $payment->amount - $invoice->net_total;
            $invoice->gross_total = $payment->amount;
            $invoice->currency = $payment->currency;
            $invoice->status = 'issued';
            $invoice->bank_account_id = $bankAccount ? $bankAccount->id : null;
            $invoice->notes = "Faktura za subskrypcję: {$plan->name}";
            $invoice->auto_generated = true;
            $invoice->approval_status = 'approved';
            $invoice->approved_at = now();
            $invoice->approved_by = 1; // ID administratora
            $invoice->save();
            
            // Dodaj pozycję faktury
            $invoice->items()->create([
                'name' => "Subskrypcja {$plan->name}",
                'description' => "Okres rozliczeniowy: {$subscription->start_date->format('d.m.Y')} - {$subscription->end_date->format('d.m.Y')}",
                'quantity' => 1,
                'unit' => 'szt.',
                'unit_price' => $invoice->net_total,
                'tax_rate' => $plan->tax_rate ?? 23,
                'net_price' => $invoice->net_total,
                'tax_amount' => $invoice->tax_total,
                'gross_price' => $invoice->gross_total
            ]);
            
            // Powiąż fakturę z subskrypcją
            $subscription->last_invoice_id = $invoice->id;
            $subscription->last_invoice_number = $invoice->number;
            $subscription->save();
            
            // Dodaj powiadomienie dla administratora
            if (class_exists('\App\Models\AdminNotification')) {
                \App\Models\AdminNotification::createInvoiceNotification(
                    'Wygenerowano automatyczną fakturę',
                    "Automatycznie wygenerowano fakturę nr {$invoice->number} dla użytkownika {$user->name}",
                    route('admin.billing.invoices.show', $invoice->id),
                    [
                        'invoice_id' => $invoice->id,
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id
                    ]
                );
            }
            
            return $invoice;
        } catch (\Exception $e) {
            Log::error('Błąd podczas generowania faktury dla płatności: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'exception' => $e
            ]);
            
            return null;
        }
    }

    /**
     * Formatuje adres na podstawie profilu firmy
     *
     * @param \App\Models\CompanyProfile $companyProfile
     * @return string
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
     * Aktywuje subskrypcję użytkownika
     *
     * @param UserSubscription $subscription
     * @return array
     */
    public function activateSubscription(UserSubscription $subscription)
    {
        try {
            DB::beginTransaction();
            
            // Zaktualizuj status subskrypcji na aktywny
            $subscription->status = 'active';
            
            // Oblicz datę zakończenia jeśli nie jest ustawiona
            if (!$subscription->end_date) {
                $subscription->end_date = $this->calculateEndDate(
                    Carbon::parse($subscription->start_date ?: now()),
                    $subscription->plan->billing_period
                );
            }
            
            $subscription->save();
            
            // Powiadom użytkownika o aktywacji subskrypcji
            $subscription->user->notify(new SubscriptionActivated($subscription));
            
            // Wyemituj zdarzenie o aktywacji subskrypcji
            event(new SubscriptionCreated($subscription));
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Subskrypcja została aktywowana pomyślnie',
                'subscription' => $subscription
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas aktywacji subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas aktywacji subskrypcji: ' . $e->getMessage(),
                'subscription' => $subscription
            ];
        }
    }

    /**
     * Obsługuje nieudaną płatność za subskrypcję
     *
     * @param UserSubscription $subscription
     * @param string $reason
     * @return array
     */
    public function handleFailedPayment(UserSubscription $subscription, $reason = '')
    {
        try {
            DB::beginTransaction();
            
            // Pobierz ustawienia płatności
            $paymentSettings = \App\Models\PaymentSettings::getActive();
            
            // Zwiększ licznik nieudanych płatności
            $subscription->failed_payment_count = ($subscription->failed_payment_count ?? 0) + 1;
            $subscription->last_failed_payment_date = Carbon::now();
            $subscription->last_failed_payment_reason = $reason;
            
            // Sprawdź, czy przekroczono maksymalną liczbę prób
            $maxRetries = $paymentSettings->payment_retry_attempts ?? 3;
            
            if ($subscription->failed_payment_count >= $maxRetries) {
                // Jeśli automatyczne anulowanie jest włączone
                if ($paymentSettings->auto_cancel_after_failed_payments) {
                    // Anuluj subskrypcję
                    $subscription->status = 'cancelled';
                    $subscription->cancelled_at = Carbon::now();
                    $subscription->auto_renew = false;
                    $subscription->renewal_status = UserSubscription::RENEWAL_DISABLED;
                    $subscription->save();
                    
                    // Powiadom użytkownika
                    $subscription->user->notify(new \App\Notifications\SubscriptionCancelled($subscription));
                    
                    // Wyemituj zdarzenie
                    event(new \App\Events\SubscriptionCancelled($subscription));
                    
                    DB::commit();
                    
                    return [
                        'success' => false,
                        'message' => 'Subskrypcja została anulowana z powodu wielokrotnych nieudanych płatności',
                        'subscription' => $subscription
                    ];
                } else {
                    // Oznacz jako wymagającą ręcznej interwencji
                    $subscription->status = 'payment_failed';
                    $subscription->save();
                    
                    DB::commit();
                    
                    return [
                        'success' => false,
                        'message' => 'Przekroczono maksymalną liczbę prób płatności, wymagana interwencja',
                        'subscription' => $subscription
                    ];
                }
            } else {
                // Ustaw następną datę próby płatności
                $retryInterval = $paymentSettings->payment_retry_interval ?? 3; // dni
                $subscription->next_payment_retry = Carbon::now()->addDays($retryInterval);
                
                // Ustaw okres karencji
                $gracePeriod = $paymentSettings->grace_period_days ?? 3; // dni
                
                // Jeśli subskrypcja ma jeszcze okres karencji
                if ($gracePeriod > 0) {
                    // Data końca okresu karencji
                    $subscription->grace_period_ends_at = Carbon::now()->addDays($gracePeriod);
                    
                    // Zostawiamy status jako aktywny przez okres karencji
                    $subscription->status = 'active';
                } else {
                    // Brak okresu karencji - od razu oznaczamy jako problem z płatnością
                    $subscription->status = 'payment_retry';
                }
                
                $subscription->save();
                
                // Powiadom użytkownika o nieudanej płatności
                $subscription->user->notify(new \App\Notifications\PaymentFailed($subscription, $reason));
                
                DB::commit();
                
                return [
                    'success' => false,
                    'message' => 'Płatność nie powiodła się, zaplanowano ponowną próbę za ' . $retryInterval . ' dni',
                    'subscription' => $subscription,
                    'next_retry' => $subscription->next_payment_retry
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas obsługi nieudanej płatności: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'reason' => $reason,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas obsługi nieudanej płatności: ' . $e->getMessage(),
                'subscription' => $subscription
            ];
        }
    }
} 
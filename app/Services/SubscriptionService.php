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
        // Sprawdzenie czy użytkownik już ma aktywną subskrypcję
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription) {
            return [
                'success' => false,
                'message' => 'Użytkownik posiada już aktywną subskrypcję',
                'subscription' => null,
                'payment' => null,
            ];
        }

        try {
            DB::beginTransaction();

            // Obliczanie daty rozpoczęcia i zakończenia subskrypcji
            $startDate = $data['start_date'] ?? Carbon::now();
            $endDate = $this->calculateEndDate($plan, $startDate);

            // Tworzenie rekordu subskrypcji
            $subscription = new UserSubscription();
            $subscription->user_id = $user->id;
            $subscription->plan_id = $plan->id;
            $subscription->status = $data['status'] ?? 'pending_payment';
            $subscription->start_date = $startDate;
            $subscription->end_date = $endDate;
            $subscription->payment_method = $data['payment_method'] ?? 'card';
            $subscription->payment_details = $data['payment_details'] ?? null;
            $subscription->notes = $data['notes'] ?? null;
            $subscription->auto_renew = $data['auto_renew'] ?? true;
            $subscription->save();

            // Jeśli nie jest to darmowy plan, przetwarzamy płatność
            $payment = null;
            if ($plan->price > 0) {
                // Przetwarzanie płatności
                $paymentResult = $this->paymentGateway->processPayment(
                    $user,
                    $plan->price,
                    $plan->currency,
                    $subscription->payment_method,
                    "Nowa subskrypcja: {$plan->name}",
                    ['subscription_id' => $subscription->id]
                );

                if (!$paymentResult['success']) {
                    throw new \Exception($paymentResult['message']);
                }

                // Zapisanie informacji o płatności
                $payment = new SubscriptionPayment();
                $payment->user_id = $user->id;
                $payment->subscription_id = $subscription->id;
                $payment->transaction_id = $paymentResult['transaction_id'];
                $payment->amount = $plan->price;
                $payment->currency = $plan->currency;
                $payment->payment_method = $subscription->payment_method;
                $payment->payment_details = $subscription->payment_details;
                $payment->status = 'completed';
                $payment->save();

                // Aktualizacja statusu subskrypcji na aktywną
                $subscription->status = 'active';
                $subscription->save();
            } else {
                // Dla darmowego planu, automatycznie ustawiamy status na aktywny
                $subscription->status = 'active';
                $subscription->save();
            }

            DB::commit();

            // Wysyłanie powiadomienia do użytkownika
            if ($data['send_notification'] ?? true) {
                $user->notify(new SubscriptionActivated($subscription));
            }

            // Wyzwolenie zdarzenia SubscriptionCreated
            event(new SubscriptionCreated($subscription));

            return [
                'success' => true,
                'message' => 'Subskrypcja została utworzona pomyślnie',
                'subscription' => $subscription,
                'payment' => $payment,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas tworzenia subskrypcji: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas tworzenia subskrypcji: ' . $e->getMessage(),
                'subscription' => null,
                'payment' => null,
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
                'subscription' => $subscription,
                'payment' => null,
            ];
        }

        $user = $subscription->user;
        $plan = $subscription->plan;

        try {
            DB::beginTransaction();

            // Obliczanie nowej daty zakończenia
            $newStartDate = Carbon::now();
            $newEndDate = $this->calculateEndDate($plan, $newStartDate);

            // Jeśli nie jest to darmowy plan, przetwarzamy płatność
            $payment = null;
            if ($plan->price > 0) {
                // Przetwarzanie płatności odnowienia
                $paymentResult = $this->paymentGateway->processRenewal(
                    $user,
                    $plan->price,
                    $plan->currency,
                    $subscription->payment_method,
                    $subscription->payment_details,
                    "Odnowienie subskrypcji: {$plan->name}",
                    $subscription
                );

                if (!$paymentResult['success']) {
                    throw new \Exception($paymentResult['message']);
                }

                // Zapisanie informacji o płatności
                $payment = new SubscriptionPayment();
                $payment->user_id = $user->id;
                $payment->subscription_id = $subscription->id;
                $payment->transaction_id = $paymentResult['transaction_id'];
                $payment->amount = $plan->price;
                $payment->currency = $plan->currency;
                $payment->payment_method = $subscription->payment_method;
                $payment->payment_details = $subscription->payment_details;
                $payment->status = 'completed';
                $payment->save();
            }

            // Aktualizacja dat subskrypcji
            $subscription->start_date = $newStartDate;
            $subscription->end_date = $newEndDate;
            $subscription->status = 'active';
            $subscription->save();

            DB::commit();

            // Wyzwolenie zdarzenia SubscriptionRenewed
            event(new SubscriptionRenewed($subscription));

            return [
                'success' => true,
                'message' => 'Subskrypcja została odnowiona pomyślnie',
                'subscription' => $subscription,
                'payment' => $payment,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas odnawiania subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas odnawiania subskrypcji: ' . $e->getMessage(),
                'subscription' => $subscription,
                'payment' => null,
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
            // Ustawienie daty anulowania i statusu
            $subscription->cancelled_at = Carbon::now();
            $subscription->cancellation_reason = $data['reason'] ?? null;
            
            // Sprawdzenie czy anulowanie ma nastąpić natychmiast czy na koniec okresu
            if ($data['immediate'] ?? false) {
                $subscription->status = 'cancelled';
                $subscription->end_date = Carbon::now();
            } else {
                // Subskrypcja pozostanie aktywna do końca opłaconego okresu
                $subscription->status = 'active';
                $subscription->auto_renew = false;
            }
            
            $subscription->save();

            // Wysyłanie powiadomienia do użytkownika
            if ($data['send_notification'] ?? true) {
                $subscription->user->notify(new SubscriptionCancelledNotification($subscription));
            }

            // Wyzwolenie zdarzenia SubscriptionCancelled
            event(new SubscriptionCancelled($subscription));

            return [
                'success' => true,
                'message' => 'Subskrypcja została anulowana pomyślnie',
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            Log::error('Błąd podczas anulowania subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas anulowania subskrypcji: ' . $e->getMessage(),
                'subscription' => $subscription,
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

            // Aktualizacja pól subskrypcji
            if (isset($data['plan_id']) && $data['plan_id'] != $subscription->plan_id) {
                $newPlan = SubscriptionPlan::findOrFail($data['plan_id']);
                $subscription->plan_id = $newPlan->id;
                
                // Obliczanie nowej daty zakończenia jeśli zmienił się plan
                if (isset($data['start_date'])) {
                    $startDate = Carbon::parse($data['start_date']);
                    $subscription->start_date = $startDate;
                    $subscription->end_date = $this->calculateEndDate($newPlan, $startDate);
                } else {
                    $subscription->end_date = $this->calculateEndDate($newPlan, $subscription->start_date);
                }
            } else {
                // Aktualizacja daty rozpoczęcia/zakończenia
                if (isset($data['start_date'])) {
                    $subscription->start_date = $data['start_date'];
                }
                
                if (isset($data['end_date'])) {
                    $subscription->end_date = $data['end_date'];
                }
            }
            
            // Aktualizacja innych pól
            if (isset($data['status'])) {
                $subscription->status = $data['status'];
            }
            
            if (isset($data['payment_method'])) {
                $subscription->payment_method = $data['payment_method'];
            }
            
            if (isset($data['payment_details'])) {
                $subscription->payment_details = $data['payment_details'];
            }
            
            if (isset($data['notes'])) {
                $subscription->notes = $data['notes'];
            }
            
            if (isset($data['auto_renew'])) {
                $subscription->auto_renew = $data['auto_renew'];
            }
            
            $subscription->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Subskrypcja została zaktualizowana pomyślnie',
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas aktualizacji subskrypcji: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas aktualizacji subskrypcji: ' . $e->getMessage(),
                'subscription' => $subscription,
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
     * Oblicza datę zakończenia subskrypcji na podstawie planu i daty rozpoczęcia
     *
     * @param SubscriptionPlan $plan
     * @param Carbon|string $startDate
     * @return Carbon|null
     */
    protected function calculateEndDate(SubscriptionPlan $plan, $startDate)
    {
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate);
        }

        // Jeśli plan jest bezterminowy, zwracamy null
        if ($plan->billing_period === 'lifetime') {
            return null;
        }

        // W przeciwnym wypadku obliczamy datę zakończenia na podstawie okresu rozliczeniowego
        switch ($plan->billing_period) {
            case 'monthly':
                return $startDate->copy()->addMonth();
            case 'quarterly':
                return $startDate->copy()->addMonths(3);
            case 'semi_annually':
                return $startDate->copy()->addMonths(6);
            case 'annually':
                return $startDate->copy()->addYear();
            default:
                return $startDate->copy()->addMonth();
        }
    }
} 
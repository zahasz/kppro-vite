<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Pobierz dostępne plany subskrypcji.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'code' => $plan->code,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'formatted_price' => $plan->formatted_price,
                    'billing_period' => $plan->billing_period,
                    'formatted_billing_period' => $plan->formatted_billing_period,
                    'features' => $plan->features,
                    'max_invoices' => $plan->max_invoices,
                    'max_products' => $plan->max_products,
                    'max_contractors' => $plan->max_contractors,
                    'is_free' => $plan->isFree(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    /**
     * Pobierz aktualną subskrypcję dla zalogowanego użytkownika.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentSubscription()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Użytkownik niezalogowany'
            ], 401);
        }

        $subscription = UserSubscription::with('subscriptionPlan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Brak aktywnej subskrypcji'
            ], 404);
        }

        $result = [
            'id' => $subscription->id,
            'status' => $subscription->status,
            'start_date' => $subscription->start_date->format('Y-m-d'),
            'end_date' => $subscription->end_date ? $subscription->end_date->format('Y-m-d') : null,
            'auto_renew' => $subscription->auto_renew,
            'days_left' => $subscription->end_date ? Carbon::now()->diffInDays($subscription->end_date, false) : null,
            'payment_method' => $subscription->payment_method,
            'plan' => [
                'id' => $subscription->subscriptionPlan->id,
                'name' => $subscription->subscriptionPlan->name,
                'code' => $subscription->subscriptionPlan->code,
                'price' => $subscription->subscriptionPlan->price,
                'billing_period' => $subscription->subscriptionPlan->billing_period,
                'features' => $subscription->subscriptionPlan->features,
                'max_invoices' => $subscription->subscriptionPlan->max_invoices,
                'max_products' => $subscription->subscriptionPlan->max_products,
                'max_contractors' => $subscription->subscriptionPlan->max_contractors,
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Pobierz historię płatności zalogowanego użytkownika.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentHistory()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Użytkownik niezalogowany'
            ], 401);
        }

        $payments = $user->subscriptionPayments()
            ->with('userSubscription.subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'status' => $payment->status,
                    'formatted_status' => $payment->formatted_status,
                    'payment_method' => $payment->payment_method,
                    'invoice_number' => $payment->invoice_number,
                    'invoice_date' => $payment->invoice_date ? $payment->invoice_date->format('Y-m-d') : null,
                    'plan_name' => $payment->userSubscription->subscriptionPlan->name,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Sprawdź limity użytkownika na podstawie jego subskrypcji.
     *
     * @param  string  $resourceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLimits($resourceType)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Użytkownik niezalogowany'
            ], 401);
        }

        $subscription = UserSubscription::with('subscriptionPlan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Brak aktywnej subskrypcji'
            ], 404);
        }

        $plan = $subscription->subscriptionPlan;
        $limit = null;
        $currentCount = 0;

        switch ($resourceType) {
            case 'invoices':
                $limit = $plan->max_invoices;
                $currentCount = $user->invoices()->count();
                break;
            case 'products':
                $limit = $plan->max_products;
                $currentCount = $user->products()->count();
                break;
            case 'contractors':
                $limit = $plan->max_contractors;
                $currentCount = $user->contractors()->count();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Nieprawidłowy typ zasobu'
                ], 400);
        }

        $hasLimit = $limit !== null;
        $limitReached = $hasLimit && $currentCount >= $limit;

        return response()->json([
            'success' => true,
            'data' => [
                'resource_type' => $resourceType,
                'has_limit' => $hasLimit,
                'limit' => $limit,
                'current_count' => $currentCount,
                'limit_reached' => $limitReached,
                'remaining' => $hasLimit ? max(0, $limit - $currentCount) : null,
                'percentage_used' => $hasLimit ? round(($currentCount / max(1, $limit)) * 100, 2) : 0,
            ]
        ]);
    }
}

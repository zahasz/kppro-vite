<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Wyświetl informacje o aktualnej subskrypcji użytkownika
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('plan')
            ->first();

        return view('user.subscription', [
            'subscription' => $subscription,
            'user' => $user
        ]);
    }
}

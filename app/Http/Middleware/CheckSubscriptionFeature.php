<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Użytkownik niezalogowany.'
                ], 401);
            }
            return redirect()->route('login');
        }
        
        // Administratorzy mają dostęp do wszystkich funkcji
        if ($user->hasRole(['admin', 'super-admin'])) {
            return $next($request);
        }
        
        // Sprawdzenie czy użytkownik ma dostęp do danej funkcji
        if (!$user->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Twój plan subskrypcji nie zawiera dostępu do tej funkcji.',
                    'required_feature' => $feature,
                    'upgrade_url' => route('subscription.plans')
                ], 403);
            }
            
            return redirect()->route('subscription.plans')
                ->with('error', 'Twój plan subskrypcji nie zawiera dostępu do tej funkcji. Rozważ aktualizację planu.');
        }
        
        return $next($request);
    }
}

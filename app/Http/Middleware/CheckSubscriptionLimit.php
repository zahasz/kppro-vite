<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resourceType): Response
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
        
        // Administratorzy mają nieograniczone zasoby
        if ($user->hasRole(['admin', 'super-admin'])) {
            return $next($request);
        }
        
        // Sprawdzenie czy użytkownik nie przekroczył limitu
        if ($user->hasReachedLimit($resourceType)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Osiągnięto limit dla tego typu zasobu w Twoim planie subskrypcji.',
                    'resource_type' => $resourceType,
                    'upgrade_url' => route('subscription.plans')
                ], 403);
            }
            
            return redirect()->route('subscription.plans')
                ->with('error', 'Osiągnięto limit dla tego typu zasobu. Rozważ aktualizację planu, aby uzyskać większy limit.');
        }
        
        return $next($request);
    }
}

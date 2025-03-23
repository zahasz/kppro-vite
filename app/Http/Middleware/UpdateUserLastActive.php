<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UpdateUserLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Aktualizuj last_active_at co 5 minut, aby uniknąć zbyt wielu zapytań do bazy danych
            if (!$user->last_active_at || $user->last_active_at->diffInMinutes(now()) >= 5) {
                $user->last_active_at = Carbon::now();
                $user->save();
            }
        }

        return $next($request);
    }
} 
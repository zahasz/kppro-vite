<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
        // Aktualizuj last_seen_at tylko dla zalogowanych użytkowników
        if (auth()->check()) {
            $user = auth()->user();
            
            // Jeśli ostatnia aktywność była więcej niż minutę temu, lub nie było jej wcale
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 1) {
                try {
                    // Aktualizuj pole last_seen_at
                    $user->update(['last_seen_at' => now()]);
                    
                    // Wyczyść cache użytkownika i licznika online
                    Cache::forget("user.{$user->id}");
                    Cache::forget('online_users_count');
                } catch (\Exception $e) {
                    Log::error('Błąd podczas aktualizacji czasu ostatniej aktywności', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $next($request);
    }
} 
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
        $isAjax = $request->ajax() || $request->wantsJson();
        $currentTime = now();
        
        Log::info('UpdateUserLastActive middleware rozpoczęty', [
            'path' => $request->path(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'is_authenticated' => Auth::check(),
            'app_timezone' => config('app.timezone'),
            'current_time' => $currentTime->toDateTimeString(),
            'is_ajax' => $isAjax,
            'request_headers' => $request->headers->all(),
            'session_id' => session()->getId(),
            'ip' => $request->ip()
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            
            try {
                DB::enableQueryLog();
                
                // Sprawdź aktualny stan przed aktualizacją
                $beforeUpdate = $user->fresh();
                
                Log::info('Stan przed aktualizacją', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'current_last_seen' => $beforeUpdate->last_seen_at?->toDateTimeString(),
                    'now' => $currentTime->toDateTimeString(),
                    'is_online' => $beforeUpdate->isOnline()
                ]);

                // Aktualizacja przez metodę modelu
                $updated = $user->updateLastSeen();
                
                // Sprawdź stan po aktualizacji
                $afterUpdate = $user->fresh();
                
                $queryLog = DB::getQueryLog();
                
                Log::info('Status aktualizacji last_seen_at', [
                    'updated' => $updated,
                    'user_id' => $user->id,
                    'before_last_seen' => $beforeUpdate->last_seen_at?->toDateTimeString(),
                    'after_last_seen' => $afterUpdate->last_seen_at?->toDateTimeString(),
                    'current_time' => $currentTime->toDateTimeString(),
                    'is_online' => $afterUpdate->isOnline(),
                    'query_log' => $queryLog,
                    'session_id' => session()->getId()
                ]);

                // Wyczyść cache użytkownika
                Cache::tags(['users'])->forget('user.' . $user->id);
                
                DB::disableQueryLog();
                
            } catch (Exception $e) {
                Log::error('Błąd podczas aktualizacji last_seen_at', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => $user->id,
                    'request_path' => $request->path(),
                    'current_time' => $currentTime->toDateTimeString(),
                    'query_log' => DB::getQueryLog() ?? [],
                    'session_id' => session()->getId()
                ]);
            }
        } else {
            Log::info('Użytkownik niezalogowany', [
                'request_path' => $request->path(),
                'request_method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_ajax' => $isAjax
            ]);
        }

        Log::info('UpdateUserLastActive middleware zakończony', ['is_ajax' => $isAjax]);
        return $next($request);
    }
} 
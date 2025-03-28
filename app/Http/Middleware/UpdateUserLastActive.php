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
        \Log::info('UpdateUserLastActive - Start', [
            'path' => $request->path(),
            'method' => $request->method(),
            'is_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
        ]);

        if (auth()->check()) {
            $user = auth()->user();
            
            \Log::info('UpdateUserLastActive - Przed aktualizacją', [
                'user_id' => $user->id,
                'name' => $user->name,
                'last_seen_at' => $user->last_seen_at?->toDateTimeString(),
                'is_online' => $user->isOnline(),
                'request_path' => $request->path(),
                'request_method' => $request->method(),
                'request_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'timezone' => config('app.timezone'),
                'current_time' => now()->toDateTimeString(),
                'session_id' => session()->getId(),
            ]);

            try {
                DB::enableQueryLog();
                
                $beforeUpdate = $user->fresh();
                $updateResult = $user->updateLastSeen();
                $afterUpdate = $user->fresh();
                
                \Log::info('UpdateUserLastActive - Po aktualizacji', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'update_success' => $updateResult,
                    'before_update' => [
                        'last_seen_at' => $beforeUpdate->last_seen_at?->toDateTimeString(),
                        'is_online' => $beforeUpdate->isOnline(),
                    ],
                    'after_update' => [
                        'last_seen_at' => $afterUpdate->last_seen_at?->toDateTimeString(),
                        'is_online' => $afterUpdate->isOnline(),
                    ],
                    'queries' => DB::getQueryLog(),
                    'cache_status' => [
                        'cache_driver' => config('cache.default'),
                        'cache_prefix' => config('cache.prefix'),
                        'cache_ttl' => config('cache.ttl'),
                    ],
                    'session_id' => session()->getId(),
                ]);

                // Wyczyść cache użytkownika i licznika online
                Cache::forget("user.{$user->id}");
                Cache::forget('online_users_count');
                
                \Log::info('UpdateUserLastActive - Cache wyczyszczony', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'cache_keys' => [
                        "user.{$user->id}",
                        'online_users_count'
                    ],
                    'session_id' => session()->getId(),
                ]);
            } catch (\Exception $e) {
                \Log::error('UpdateUserLastActive - Błąd podczas aktualizacji', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'session_id' => session()->getId(),
                ]);
            }
        } else {
            Log::info('Użytkownik niezalogowany', [
                'request_path' => $request->path(),
                'request_method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_ajax' => $request->ajax() || $request->wantsJson()
            ]);
        }

        return $next($request);
    }
} 
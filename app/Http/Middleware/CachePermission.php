<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CachePermission
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $cacheKey = 'user_permissions_' . $user->id;

            if (!Cache::has($cacheKey)) {
                Cache::remember($cacheKey, now()->addHours(24), function () use ($user) {
                    return $user->getAllPermissions()->pluck('name')->toArray();
                });
            }
        }

        return $next($request);
    }
} 
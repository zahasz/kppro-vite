<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CachePermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $key = 'user_permissions_' . $request->user()->id;
            
            if (!Cache::has($key)) {
                Cache::remember($key, now()->addHours(24), function () use ($request) {
                    return [
                        'roles' => $request->user()->user_roles,
                        'permissions' => $request->user()->user_permissions,
                    ];
                });
            }
        }

        return $next($request);
    }
}

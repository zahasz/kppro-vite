<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Brak uprawnień do wykonania tej operacji.',
                    'required_permission' => $permission
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Brak uprawnień do wykonania tej operacji.');
        }

        return $next($request);
    }
}

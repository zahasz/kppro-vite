<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Sprawdza czy zalogowany użytkownik ma rolę administratora.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Brak uprawnień administracyjnych.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Brak uprawnień do dostępu do panelu administracyjnego.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ModulePermissionService;
use Illuminate\Support\Facades\Auth;

class CheckModuleAccess
{
    /**
     * @var ModulePermissionService
     */
    protected $modulePermissionService;

    /**
     * Konstruktor
     * 
     * @param ModulePermissionService $modulePermissionService
     */
    public function __construct(ModulePermissionService $modulePermissionService)
    {
        $this->modulePermissionService = $modulePermissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$this->modulePermissionService->userCanAccessModule($user, $moduleCode)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Brak dostępu do tego modułu. Sprawdź swój plan subskrypcji lub skontaktuj się z administratorem.',
                    'module' => $moduleCode
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Brak dostępu do tego modułu. Sprawdź swój plan subskrypcji lub skontaktuj się z administratorem.');
        }
        
        // Dodajemy informacje o ograniczeniach modułu do requestu
        $request->attributes->add([
            'module_restrictions' => $this->modulePermissionService->getModuleRestrictions($user, $moduleCode)
        ]);

        return $next($request);
    }
}

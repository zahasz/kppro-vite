<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return 'Test działa poprawnie!';
    }
}

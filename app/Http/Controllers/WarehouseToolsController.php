<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:warehouse.tools.view')->only(['index', 'show']);
        $this->middleware('permission:warehouse.tools.create')->only(['create', 'store']);
        $this->middleware('permission:warehouse.tools.edit')->only(['edit', 'update']);
        $this->middleware('permission:warehouse.tools.delete')->only('destroy');
    }

    public function index()
    {
        return view('warehouse.tools.index');
    }

    public function create()
    {
        return view('warehouse.tools.create');
    }

    public function store(Request $request)
    {
        // Logika zapisywania narzędzia
    }

    public function show($id)
    {
        // Logika wyświetlania szczegółów narzędzia
    }

    public function edit($id)
    {
        // Logika edycji narzędzia
    }

    public function update(Request $request, $id)
    {
        // Logika aktualizacji narzędzia
    }

    public function destroy($id)
    {
        // Logika usuwania narzędzia
    }
} 
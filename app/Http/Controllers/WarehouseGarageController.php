<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseGarageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:warehouse.garage.view')->only(['index', 'show']);
        $this->middleware('permission:warehouse.garage.create')->only(['create', 'store']);
        $this->middleware('permission:warehouse.garage.edit')->only(['edit', 'update']);
        $this->middleware('permission:warehouse.garage.delete')->only('destroy');
    }

    public function index()
    {
        return view('warehouse.garage.index');
    }

    public function create()
    {
        return view('warehouse.garage.create');
    }

    public function store(Request $request)
    {
        // Logika zapisywania pojazdu
    }

    public function show($id)
    {
        // Logika wyświetlania szczegółów pojazdu
    }

    public function edit($id)
    {
        // Logika edycji pojazdu
    }

    public function update(Request $request, $id)
    {
        // Logika aktualizacji pojazdu
    }

    public function destroy($id)
    {
        // Logika usuwania pojazdu
    }
} 
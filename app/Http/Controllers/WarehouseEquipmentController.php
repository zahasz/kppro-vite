<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseEquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:warehouse.equipment.view')->only(['index', 'show']);
        $this->middleware('permission:warehouse.equipment.create')->only(['create', 'store']);
        $this->middleware('permission:warehouse.equipment.edit')->only(['edit', 'update']);
        $this->middleware('permission:warehouse.equipment.delete')->only('destroy');
    }

    public function index()
    {
        return view('warehouse.equipment.index');
    }

    public function create()
    {
        return view('warehouse.equipment.create');
    }

    public function store(Request $request)
    {
        // Logika zapisywania sprzętu
    }

    public function show($id)
    {
        // Logika wyświetlania szczegółów sprzętu
    }

    public function edit($id)
    {
        // Logika edycji sprzętu
    }

    public function update(Request $request, $id)
    {
        // Logika aktualizacji sprzętu
    }

    public function destroy($id)
    {
        // Logika usuwania sprzętu
    }
} 
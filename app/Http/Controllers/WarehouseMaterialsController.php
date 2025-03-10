<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseMaterialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:warehouse.materials.view')->only(['index', 'show']);
        $this->middleware('permission:warehouse.materials.create')->only(['create', 'store']);
        $this->middleware('permission:warehouse.materials.edit')->only(['edit', 'update']);
        $this->middleware('permission:warehouse.materials.delete')->only('destroy');
    }

    public function index()
    {
        return view('warehouse.materials.index');
    }

    public function create()
    {
        return view('warehouse.materials.create');
    }

    public function store(Request $request)
    {
        // Logika zapisywania materiału
    }

    public function show($id)
    {
        // Logika wyświetlania szczegółów materiału
    }

    public function edit($id)
    {
        // Logika edycji materiału
    }

    public function update(Request $request, $id)
    {
        // Logika aktualizacji materiału
    }

    public function destroy($id)
    {
        // Logika usuwania materiału
    }
} 
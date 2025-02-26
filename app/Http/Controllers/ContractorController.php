<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ContractorController extends Controller
{
    /**
     * Wyświetl listę kontrahentów
     */
    public function index()
    {
        $contractors = Contractor::latest()->paginate(10);
        return view('contractors', compact('contractors'));
    }

    /**
     * Wyświetl formularz tworzenia nowego kontrahenta
     */
    public function create()
    {
        return view('contractors.create');
    }

    /**
     * Zapisz nowego kontrahenta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive,blocked'
        ]);

        $contractor = Contractor::create($validated);

        return response()->json($contractor, 201);
    }

    /**
     * Wyświetl szczegóły kontrahenta
     */
    public function show(Contractor $contractor)
    {
        return response()->json($contractor);
    }

    /**
     * Pobierz dane do edycji
     */
    public function edit(Contractor $contractor)
    {
        return response()->json($contractor);
    }

    /**
     * Zaktualizuj dane kontrahenta
     */
    public function update(Request $request, Contractor $contractor)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive,blocked'
        ]);

        $contractor->update($validated);

        return response()->json($contractor);
    }

    /**
     * Usuń kontrahenta
     */
    public function destroy(Contractor $contractor)
    {
        $contractor->delete();
        return response()->json(null, 204);
    }

    /**
     * Eksportuj listę do PDF
     */
    public function exportPDF()
    {
        $contractors = Contractor::all();
        
        // TODO: Implementacja generowania PDF
        
        return response()->json(['message' => 'Funkcja eksportu do PDF zostanie zaimplementowana wkrótce.']);
    }
} 
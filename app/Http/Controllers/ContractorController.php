<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ContractorController extends Controller
{
    /**
     * Wyświetl listę kontrahentów
     */
    public function index()
    {
        $contractors = Contractor::latest()->paginate(10);
        return view('contractors.index', compact('contractors'));
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
            'nip' => 'nullable|string|max:20',
            'regon' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:11',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        $contractor = Contractor::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'contractor' => $contractor
            ]);
        }

        return redirect()->route('contractors.index')
            ->with('success', 'Kontrahent został dodany pomyślnie.');
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
        return view('contractors.edit', compact('contractor'));
    }

    /**
     * Zaktualizuj dane kontrahenta
     */
    public function update(Request $request, Contractor $contractor)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'regon' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:11',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blocked'
        ]);

        $contractor->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'contractor' => $contractor
            ]);
        }

        return redirect()->route('contractors.index')
            ->with('success', 'Kontrahent został zaktualizowany pomyślnie.');
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
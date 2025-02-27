<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceController extends Controller
{
    public function index()
    {
        return view('finances.index');
    }

    public function scanner()
    {
        return view('finances.scanner');
    }

    public function incomes()
    {
        return view('finances.incomes');
    }

    public function expenses()
    {
        return view('finances.expenses');
    }

    public function reports()
    {
        return view('finances.reports');
    }

    public function salesInvoices()
    {
        return view('finances.invoices.sales');
    }

    public function purchaseInvoices()
    {
        return view('finances.invoices.purchases');
    }

    public function invoices()
    {
        return view('finances.invoices.index');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|in:operational,fixed,investment,other',
                'date' => 'required|date',
                'due_date' => 'required|date',
                'status' => 'required|string|in:paid,unpaid,overdue',
                'description' => 'nullable|string',
            ]);

            $cost = Cost::create($validated);

            if ($request->wantsJson()) {
                return response()->json($cost, 201);
            }

            return back()->with('success', 'Koszt został dodany.');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Błąd walidacji', 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Wystąpił błąd podczas dodawania kosztu.'], 500);
            }
            return back()->with('error', 'Wystąpił błąd podczas dodawania kosztu.')->withInput();
        }
    }

    public function show(Cost $cost)
    {
        try {
            return response()->json($cost);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Nie znaleziono kosztu.'], 404);
        }
    }

    public function update(Request $request, Cost $cost)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|in:operational,fixed,investment,other',
                'date' => 'required|date',
                'due_date' => 'required|date',
                'status' => 'required|string|in:paid,unpaid,overdue',
                'description' => 'nullable|string',
            ]);

            $cost->update($validated);

            if ($request->wantsJson()) {
                return response()->json($cost);
            }

            return back()->with('success', 'Koszt został zaktualizowany.');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Błąd walidacji', 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Wystąpił błąd podczas aktualizacji kosztu.'], 500);
            }
            return back()->with('error', 'Wystąpił błąd podczas aktualizacji kosztu.')->withInput();
        }
    }

    public function destroy(Cost $cost)
    {
        try {
            $cost->delete();
            
            if (request()->wantsJson()) {
                return response()->json(null, 204);
            }

            return back()->with('success', 'Koszt został usunięty.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Wystąpił błąd podczas usuwania kosztu.'], 500);
            }
            return back()->with('error', 'Wystąpił błąd podczas usuwania kosztu.');
        }
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategory;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $categories = BudgetCategory::all()->groupBy('type');
        
        $totals = [
            'totalAssets' => BudgetCategory::sum('amount'),
            'cash' => BudgetCategory::where('type', 'cash')->sum('amount'),
            'bankAccounts' => BudgetCategory::whereIn('type', ['company_bank', 'private_bank'])->sum('amount'),
            'accountsCount' => BudgetCategory::whereIn('type', ['company_bank', 'private_bank'])->count(),
            'investments' => BudgetCategory::whereIn('type', ['investments', 'leasing'])->sum('amount'),
            'income' => BudgetCategory::where('type', 'income')->sum('amount'),
            'expenses' => BudgetCategory::where('type', 'expenses')->sum('amount'),
            'monthlyIncome' => BudgetCategory::where('type', 'income')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'monthlyExpenses' => BudgetCategory::where('type', 'expenses')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        return view('finances.budget.index', $totals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(BudgetCategory::TYPES)),
            'amount' => 'required|numeric|min:0',
            'planned_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        BudgetCategory::create($validated);

        return redirect()->route('finances.budget.index')->with('success', 'Pozycja została dodana pomyślnie.');
    }

    public function update(Request $request, BudgetCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(BudgetCategory::TYPES)),
            'amount' => 'required|numeric|min:0',
            'planned_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategoria budżetu została zaktualizowana.');
    }

    public function destroy(BudgetCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Kategoria budżetu została usunięta.');
    }

    public function history()
    {
        $entries = BudgetCategory::orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($entry) {
                return [
                    'name' => $entry->name,
                    'amount' => number_format($entry->amount, 2, ',', ' '),
                    'type' => $entry->type,
                    'created_at' => $entry->created_at->format('d.m.Y, H:i')
                ];
            });

        return response()->json([
            'success' => true,
            'entries' => $entries
        ]);
    }
} 
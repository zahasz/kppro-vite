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
            'total_amount' => BudgetCategory::sum('amount'),
            'total_planned' => BudgetCategory::sum('planned_amount'),
            'total_cash' => BudgetCategory::where('type', 'cash')->sum('amount'),
            'total_bank' => BudgetCategory::whereIn('type', ['company_bank', 'private_bank'])->sum('amount'),
            'total_loans' => BudgetCategory::whereIn('type', ['loans_taken', 'loans_given'])->sum('amount'),
            'total_investments' => BudgetCategory::whereIn('type', ['investments', 'leasing'])->sum('amount'),
        ];

        return view('finances.budget.index', compact('categories', 'totals'));
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

        return back()->with('success', 'Kategoria budżetu została dodana.');
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
} 
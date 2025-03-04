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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'planned_amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);

            BudgetCategory::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pozycja została dodana pomyślnie'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Wystąpił błąd podczas zapisywania: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, BudgetCategory $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'planned_amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategoria budżetu została zaktualizowana pomyślnie'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Wystąpił błąd podczas aktualizacji: ' . $e->getMessage()
            ], 500);
        }
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

    public function details($type)
    {
        try {
            $query = BudgetCategory::query();
            
            switch ($type) {
                case 'investment':
                    $investments = $query->where('type', 'investments')->get();
                    
                    $total = $investments->sum('amount');
                    $plannedTotal = $investments->sum('planned_amount');
                    
                    return response()->json([
                        'success' => true,
                        'total' => number_format($total, 2, ',', ' '),
                        'plannedTotal' => number_format($plannedTotal, 2, ',', ' '),
                        'investments' => $investments->map(function ($inv) {
                            return [
                                'id' => $inv->id,
                                'name' => $inv->name,
                                'amount' => number_format($inv->amount, 2, ',', ' '),
                                'planned_amount' => number_format($inv->planned_amount, 2, ',', ' '),
                                'description' => $inv->description,
                                'completion_percentage' => $inv->planned_amount > 0 
                                    ? number_format(($inv->amount / $inv->planned_amount) * 100, 1, ',', ' ')
                                    : '0,0'
                            ];
                        }),
                        'stats' => [
                            'count' => $investments->count(),
                            'average' => $investments->count() > 0 
                                ? number_format($total / $investments->count(), 2, ',', ' ')
                                : '0,00',
                            'max' => number_format($investments->max('amount') ?? 0, 2, ',', ' '),
                            'completion' => $plannedTotal > 0 
                                ? number_format(($total / $plannedTotal) * 100, 1, ',', ' ')
                                : '0,0'
                        ]
                    ]);
                    
                case 'cash':
                    $cashEntries = $query->where('type', 'cash')
                        ->orderBy('created_at', 'desc')
                        ->get();
                    
                    return response()->json([
                        'success' => true,
                        'total' => number_format($cashEntries->sum('amount'), 2, ',', ' '),
                        'entries' => $cashEntries->map(function ($entry) {
                            return [
                                'id' => $entry->id,
                                'name' => $entry->name,
                                'amount' => number_format($entry->amount, 2, ',', ' '),
                                'created_at' => $entry->created_at->format('d.m.Y')
                            ];
                        }),
                        'stats' => [
                            'count' => $cashEntries->count(),
                            'average' => $cashEntries->count() > 0 
                                ? number_format($cashEntries->sum('amount') / $cashEntries->count(), 2, ',', ' ')
                                : '0,00',
                            'max' => number_format($cashEntries->max('amount') ?? 0, 2, ',', ' ')
                        ]
                    ]);
                    
                case 'bank':
                    $bankAccounts = $query->whereIn('type', ['company_bank', 'private_bank'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                    
                    return response()->json([
                        'success' => true,
                        'total' => number_format($bankAccounts->sum('amount'), 2, ',', ' '),
                        'accounts' => $bankAccounts->map(function ($account) {
                            return [
                                'id' => $account->id,
                                'name' => $account->name,
                                'amount' => number_format($account->amount, 2, ',', ' '),
                                'type' => $account->type,
                                'description' => $account->description
                            ];
                        }),
                        'stats' => [
                            'count' => $bankAccounts->count(),
                            'average' => $bankAccounts->count() > 0 
                                ? number_format($bankAccounts->sum('amount') / $bankAccounts->count(), 2, ',', ' ')
                                : '0,00',
                            'max' => number_format($bankAccounts->max('amount') ?? 0, 2, ',', ' ')
                        ]
                    ]);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Nieznany typ szczegółów'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Wystąpił błąd podczas pobierania szczegółów: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get(BudgetCategory $category)
    {
        try {
            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                    'amount' => $category->amount,
                    'planned_amount' => $category->planned_amount,
                    'description' => $category->description
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Wystąpił błąd podczas pobierania danych: ' . $e->getMessage()
            ], 500);
        }
    }
} 
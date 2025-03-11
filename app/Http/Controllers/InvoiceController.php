<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $user = Auth::user();
        
        $statistics = [
            'this_month' => [
                'total' => Invoice::thisMonth()->count(),
                'amount' => Invoice::thisMonth()->sum('gross_total')
            ],
            'unpaid' => [
                'total' => Invoice::unpaid()->count(),
                'amount' => Invoice::unpaid()->sum('gross_total')
            ],
            'overdue' => [
                'total' => Invoice::overdue()->count(),
                'amount' => Invoice::overdue()->sum('gross_total')
            ]
        ];

        $invoices = Invoice::with(['items'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('invoices.index', compact('invoices', 'statistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contractors = Contractor::where('user_id', Auth::id())->get();
        $lastInvoice = Invoice::where('user_id', Auth::id())
            ->whereYear('issue_date', now()->year)
            ->latest()
            ->first();

        $nextNumber = $lastInvoice 
            ? $this->generateNextNumber($lastInvoice->number)
            : $this->generateFirstNumber();

        return view('invoices.create', compact('contractors', 'nextNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Walidacja podstawowych danych faktury
            $validatedData = $request->validate([
                'number' => 'required|string|unique:invoices,number',
                'contractor_id' => 'required|exists:contractors,id',
                'payment_method' => 'required|string',
                'issue_date' => 'required|date',
                'sale_date' => 'required|date',
                'due_date' => 'required|date',
                'notes' => 'nullable|string',
                'status' => 'required|in:draft,issued',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.unit' => 'required|string',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.tax_rate' => 'required|numeric|min:0',
                'items.*.net_value' => 'required|numeric|min:0',
                'items.*.tax_value' => 'required|numeric|min:0',
                'items.*.gross_value' => 'required|numeric|min:0',
                'total_net' => 'required|numeric|min:0',
                'total_tax' => 'required|numeric|min:0',
                'total_gross' => 'required|numeric|min:0'
            ]);

            // Tworzenie faktury
            $invoice = Invoice::create([
                'number' => $validatedData['number'],
                'contractor_id' => $validatedData['contractor_id'],
                'user_id' => auth()->id(),
                'payment_method' => $validatedData['payment_method'],
                'issue_date' => $validatedData['issue_date'],
                'sale_date' => $validatedData['sale_date'],
                'due_date' => $validatedData['due_date'],
                'notes' => $validatedData['notes'],
                'status' => $validatedData['status'],
                'total_net' => $validatedData['total_net'],
                'total_tax' => $validatedData['total_tax'],
                'total_gross' => $validatedData['total_gross']
            ]);

            // Dodawanie pozycji faktury
            foreach ($validatedData['items'] as $item) {
                $invoice->items()->create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'net_value' => $item['net_value'],
                    'tax_value' => $item['tax_value'],
                    'gross_value' => $item['gross_value']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Faktura została pomyślnie zapisana',
                'redirect' => route('invoices.show', $invoice->id)
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Błąd walidacji danych',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas zapisywania faktury',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Można edytować tylko faktury w statusie roboczym.');
        }

        $contractors = Contractor::where('user_id', Auth::id())->get();
        return view('invoices.edit', compact('invoice', 'contractors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Można edytować tylko faktury w statusie roboczym.');
        }

        // Podobna logika jak w store()
        return back()->with('error', 'Funkcja w trakcie implementacji.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Można usunąć tylko faktury w statusie roboczym.');
        }

        $invoice->delete();
        return redirect()->route('invoices.index')
            ->with('success', 'Faktura została usunięta.');
    }

    private function generateNextNumber($lastNumber)
    {
        // Format: FV/001/MM/YYYY
        $parts = explode('/', $lastNumber);
        $number = (int)$parts[1];
        return sprintf("FV/%03d/%02d/%d", 
            $number + 1, 
            now()->month, 
            now()->year
        );
    }

    private function generateFirstNumber()
    {
        return sprintf("FV/%03d/%02d/%d", 
            1, 
            now()->month, 
            now()->year
        );
    }

    public function generatePdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('Faktura_' . $invoice->number . '.pdf');
    }
}

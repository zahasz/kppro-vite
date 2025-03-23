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
        $user = Auth::user();
        $contractors = Contractor::where('user_id', $user->id)->get();
        $lastInvoice = Invoice::where('user_id', $user->id)
            ->whereYear('issue_date', now()->year)
            ->latest()
            ->first();

        $nextNumber = $lastInvoice 
            ? $this->generateNextNumber($lastInvoice->number)
            : $this->generateFirstNumber();
            
        // Pobieranie danych profilu firmy
        $companyProfile = $user->companyProfile;
        
        // Pobieranie kont bankowych
        $bankAccounts = $companyProfile ? $companyProfile->bankAccounts : collect();
        
        // Domyślne wartości z profilu firmy
        $defaultPaymentMethod = $companyProfile ? $companyProfile->default_payment_method : 'przelew';
        $defaultPaymentDays = $companyProfile ? $companyProfile->invoice_payment_days : 14;
        $defaultCurrency = $companyProfile ? $companyProfile->default_currency : 'PLN';
        
        // Obliczanie domyślnej daty płatności
        $defaultDueDate = now()->addDays($defaultPaymentDays)->format('Y-m-d');

        return view('invoices.create', compact(
            'contractors', 
            'nextNumber', 
            'companyProfile', 
            'bankAccounts',
            'defaultPaymentMethod',
            'defaultDueDate',
            'defaultCurrency'
        ));
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
                'payment_method' => 'required|string|max:255',
                'issue_date' => 'required|date',
                'sale_date' => 'required|date',
                'due_date' => 'required|date',
                'bank_account_id' => 'nullable|exists:bank_accounts,id',
                'currency' => 'required|string|max:3',
                'notes' => 'nullable|string',
                'status' => 'required|in:draft,issued',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit' => 'required|string|max:20',
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
                'bank_account_id' => $validatedData['bank_account_id'],
                'currency' => $validatedData['currency'],
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

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Faktura została pomyślnie zapisana');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas zapisywania faktury: ' . $e->getMessage())
                ->withInput();
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
        $user = auth()->user();
        $companyProfile = $user->companyProfile;
        
        // Dodajemy logowanie, aby sprawdzić dostępność danych
        \Log::info('Edit invoice - User: ' . ($user ? 'ID: ' . $user->id : 'Niezalogowany'));
        \Log::info('Edit invoice - CompanyProfile: ' . ($companyProfile ? 'ID: ' . $companyProfile->id : 'Brak'));
        
        if ($companyProfile) {
            \Log::info('CompanyProfile data:', [
                'company_name' => $companyProfile->company_name,
                'street' => $companyProfile->street,
                'tax_number' => $companyProfile->tax_number
            ]);
        }
        
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Można edytować tylko faktury w statusie roboczym.');
        }

        $contractors = Contractor::where('user_id', auth()->id())->orderBy('name')->get();
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

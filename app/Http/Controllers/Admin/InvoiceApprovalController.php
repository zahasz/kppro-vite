<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class InvoiceApprovalController extends Controller
{
    /**
     * Wyświetla panel z fakturami oczekującymi na zatwierdzenie
     */
    public function index(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $query = Invoice::with(['user', 'items']);
        
        // Filtrowanie według statusu zatwierdzenia
        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        } else {
            $query->where('approval_status', 'pending');
        }
        
        // Filtrowanie według statusu płatności
        if ($request->has('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->where('is_paid', true);
            } elseif ($request->payment_status === 'unpaid') {
                $query->where('is_paid', false);
            }
        }
        
        // Filtrowanie według daty
        if ($request->has('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('issue_date', now());
                    break;
                case 'week':
                    $query->where('issue_date', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('issue_date', '>=', now()->subMonth());
                    break;
                case 'custom':
                    if ($request->has('date_from')) {
                        $query->where('issue_date', '>=', $request->date_from);
                    }
                    if ($request->has('date_to')) {
                        $query->where('issue_date', '<=', $request->date_to);
                    }
                    break;
            }
        }
        
        // Filtrowanie według wyszukiwania
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('contractor_name', 'like', "%{$search}%")
                  ->orWhere('contractor_nip', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subquery) use ($search) {
                      $subquery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sortowanie
        $orderBy = $request->order_by ?? 'issue_date';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);
        
        $invoices = $query->paginate(15);
        
        // Statystyki
        $statistics = [
            'pending' => Invoice::where('approval_status', 'pending')->count(),
            'approved' => Invoice::where('approval_status', 'approved')->count(),
            'rejected' => Invoice::where('approval_status', 'rejected')->count(),
            'paid' => Invoice::where('is_paid', true)->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'total_amount' => Invoice::where('approval_status', 'approved')->sum('gross_total'),
            'paid_amount' => Invoice::where('is_paid', true)->sum('gross_total'),
        ];
        
        return view('admin.invoices.approval.index', compact('invoices', 'statistics'));
    }
    
    /**
     * Wyświetla szczegóły faktury do zatwierdzenia
     */
    public function show(Invoice $invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $invoice->load(['user', 'items', 'bankAccount', 'approvedBy']);
        return view('admin.invoices.approval.show', compact('invoice'));
    }
    
    /**
     * Zatwierdza fakturę
     */
    public function approve(Request $request, Invoice $invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        DB::beginTransaction();
        
        try {
            $invoice->approval_status = 'approved';
            $invoice->approved_at = now();
            $invoice->approved_by = auth()->id();
            $invoice->save();
            
            // Dodaj powiadomienie
            AdminNotification::createInvoiceNotification(
                'Faktura zatwierdzona',
                "Faktura nr {$invoice->number} została zatwierdzona przez " . auth()->user()->name,
                route('admin.invoices.approval.show', $invoice->id),
                ['invoice_id' => $invoice->id, 'approved_by' => auth()->id()]
            );
            
            DB::commit();
            
            return redirect()->route('admin.invoices.approval.index')
                ->with('success', 'Faktura została zatwierdzona.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Błąd podczas zatwierdzania faktury: " . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'exception' => $e
            ]);
            
            return back()->with('error', 'Wystąpił błąd podczas zatwierdzania faktury.');
        }
    }
    
    /**
     * Odrzuca fakturę
     */
    public function reject(Request $request, Invoice $invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|min:5'
        ]);
        
        DB::beginTransaction();
        
        try {
            $invoice->approval_status = 'rejected';
            $invoice->rejection_reason = $request->rejection_reason;
            $invoice->approved_by = auth()->id();
            $invoice->save();
            
            // Dodaj powiadomienie
            AdminNotification::createInvoiceNotification(
                'Faktura odrzucona',
                "Faktura nr {$invoice->number} została odrzucona przez " . auth()->user()->name,
                route('admin.invoices.approval.show', $invoice->id),
                [
                    'invoice_id' => $invoice->id, 
                    'approved_by' => auth()->id(),
                    'rejection_reason' => $request->rejection_reason
                ]
            );
            
            DB::commit();
            
            return redirect()->route('admin.invoices.approval.index')
                ->with('success', 'Faktura została odrzucona.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Błąd podczas odrzucania faktury: " . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'exception' => $e
            ]);
            
            return back()->with('error', 'Wystąpił błąd podczas odrzucania faktury.');
        }
    }
    
    /**
     * Oznacza fakturę jako opłaconą
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $request->validate([
            'paid_date' => 'required|date',
            'payment_note' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $invoice->is_paid = true;
            $invoice->status = 'paid';
            $invoice->paid_date = $request->paid_date;
            $invoice->payment_note = $request->payment_note;
            $invoice->save();
            
            // Dodaj powiadomienie
            AdminNotification::createInvoiceNotification(
                'Faktura opłacona',
                "Faktura nr {$invoice->number} została oznaczona jako opłacona przez " . auth()->user()->name,
                route('admin.invoices.approval.show', $invoice->id),
                [
                    'invoice_id' => $invoice->id, 
                    'marked_by' => auth()->id(),
                    'paid_date' => $request->paid_date
                ]
            );
            
            DB::commit();
            
            return redirect()->route('admin.invoices.approval.show', $invoice->id)
                ->with('success', 'Faktura została oznaczona jako opłacona.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Błąd podczas oznaczania faktury jako opłaconej: " . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'exception' => $e
            ]);
            
            return back()->with('error', 'Wystąpił błąd podczas oznaczania faktury jako opłaconej.');
        }
    }
    
    /**
     * Wyświetla raport z fakturami
     */
    public function report(Request $request)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        $query = Invoice::with(['user']);
        
        // Filtrowanie według daty
        if ($request->has('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }
        
        // Filtrowanie według statusu
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filtrowanie według statusu zatwierdzenia
        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }
        
        // Filtrowanie według statusu płatności
        if ($request->has('is_paid')) {
            $query->where('is_paid', $request->is_paid === 'true');
        }
        
        // Grupowanie i podsumowanie danych
        $reportType = $request->report_type ?? 'daily';
        
        switch ($reportType) {
            case 'daily':
                $invoices = $query->selectRaw('
                    DATE(issue_date) as date,
                    COUNT(*) as count,
                    SUM(net_total) as net_total,
                    SUM(tax_total) as tax_total,
                    SUM(gross_total) as gross_total,
                    SUM(CASE WHEN is_paid = 1 THEN gross_total ELSE 0 END) as paid_amount
                ')
                ->groupByRaw('DATE(issue_date)')
                ->orderByRaw('DATE(issue_date) DESC')
                ->paginate(30);
                break;
                
            case 'monthly':
                $invoices = $query->selectRaw('
                    YEAR(issue_date) as year,
                    MONTH(issue_date) as month,
                    COUNT(*) as count,
                    SUM(net_total) as net_total,
                    SUM(tax_total) as tax_total,
                    SUM(gross_total) as gross_total,
                    SUM(CASE WHEN is_paid = 1 THEN gross_total ELSE 0 END) as paid_amount
                ')
                ->groupByRaw('YEAR(issue_date), MONTH(issue_date)')
                ->orderByRaw('YEAR(issue_date) DESC, MONTH(issue_date) DESC')
                ->paginate(24);
                break;
                
            case 'user':
                $invoices = $query->selectRaw('
                    user_id,
                    COUNT(*) as count,
                    SUM(net_total) as net_total,
                    SUM(tax_total) as tax_total,
                    SUM(gross_total) as gross_total,
                    SUM(CASE WHEN is_paid = 1 THEN gross_total ELSE 0 END) as paid_amount
                ')
                ->groupBy('user_id')
                ->orderByRaw('SUM(gross_total) DESC')
                ->paginate(50);
                break;
        }
        
        return view('admin.invoices.report', compact('invoices', 'reportType'));
    }
    
    /**
     * Wysyła ręcznie przypomnienie o płatności
     */
    public function sendReminder(Invoice $invoice)
    {
        if (Gate::denies('access-admin-panel')) {
            abort(403, 'Brak dostępu.');
        }
        
        if ($invoice->is_paid) {
            return back()->with('error', 'Nie można wysłać przypomnienia dla opłaconej faktury.');
        }
        
        DB::beginTransaction();
        
        try {
            $invoice->notification_sent = true;
            $invoice->reminder_sent_at = now();
            $invoice->reminders_count += 1;
            $invoice->save();
            
            // Dodaj powiadomienie dla administratora
            AdminNotification::createInvoiceNotification(
                'Ręcznie wysłano przypomnienie o płatności',
                "Ręcznie wysłano przypomnienie o płatności faktury nr {$invoice->number} przez " . auth()->user()->name,
                route('admin.invoices.approval.show', $invoice->id),
                [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'sent_by' => auth()->id(),
                    'reminder_type' => 'manual'
                ]
            );
            
            // Tutaj kod wysyłający e-mail do klienta
            // ...
            
            DB::commit();
            
            return back()->with('success', 'Przypomnienie zostało wysłane.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Błąd podczas wysyłania przypomnienia: " . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'exception' => $e
            ]);
            
            return back()->with('error', 'Wystąpił błąd podczas wysyłania przypomnienia.');
        }
    }
}

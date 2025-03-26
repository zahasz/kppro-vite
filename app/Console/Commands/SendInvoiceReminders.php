<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use App\Models\AdminNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wysyła przypomnienia o zbliżającym się terminie płatności faktur lub zaległych płatnościach';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczynam wysyłanie przypomnień o płatnościach faktur...');
        $remindersSent = 0;
        
        try {
            // Faktury, których termin płatności zbliża się (5 dni lub mniej)
            $this->info('Wyszukiwanie faktur z bliskim terminem płatności...');
            $upcomingInvoices = Invoice::with('user')
                ->where('status', '!=', 'paid')
                ->where('is_paid', false)
                ->where('approval_status', 'approved')
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(5))
                ->where(function($query) {
                    $query->where('notification_sent', false)
                        ->orWhereNull('reminder_sent_at');
                })
                ->get();
            
            $this->info("Znaleziono {$upcomingInvoices->count()} faktur z bliskim terminem płatności.");
            
            foreach ($upcomingInvoices as $invoice) {
                DB::beginTransaction();
                
                try {
                    $daysLeft = now()->diffInDays($invoice->due_date, false);
                    $user = $invoice->user;
                    
                    if (!$user) {
                        $this->warn("Faktura ID: {$invoice->id} nie ma przypisanego użytkownika. Pomijam...");
                        continue;
                    }
                    
                    $this->info("Wysyłam przypomnienie dla faktury nr {$invoice->number} (pozostało dni: {$daysLeft})");
                    
                    // Aktualizacja faktury
                    $invoice->notification_sent = true;
                    $invoice->reminder_sent_at = now();
                    $invoice->reminders_count += 1;
                    $invoice->save();
                    
                    // Dodaj powiadomienie dla administratora
                    AdminNotification::createInvoiceNotification(
                        'Wysłano przypomnienie o płatności',
                        "Wysłano przypomnienie o płatności faktury nr {$invoice->number} dla użytkownika {$user->name}. Pozostało {$daysLeft} dni do terminu płatności.",
                        route('admin.billing.invoices.show', $invoice->id),
                        [
                            'invoice_id' => $invoice->id,
                            'user_id' => $user->id,
                            'days_left' => $daysLeft,
                            'reminder_type' => 'upcoming'
                        ]
                    );
                    
                    // Tutaj można dodać kod do wysyłania e-maila do użytkownika
                    // np. Mail::to($user->email)->send(new InvoiceReminderMail($invoice));
                    
                    $remindersSent++;
                    DB::commit();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Błąd podczas wysyłania przypomnienia dla faktury ID: {$invoice->id}: " . $e->getMessage());
                    Log::error("Błąd wysyłania przypomnienia: " . $e->getMessage(), [
                        'invoice_id' => $invoice->id,
                        'exception' => $e
                    ]);
                }
            }
            
            // Faktury zaległe (termin płatności minął co najmniej 1 dzień temu)
            $this->info('Wyszukiwanie faktur z przekroczonym terminem płatności...');
            $overdueInvoices = Invoice::with('user')
                ->where('status', '!=', 'paid')
                ->where('is_paid', false)
                ->where('approval_status', 'approved')
                ->where('due_date', '<', now())
                ->where(function($query) {
                    $query->whereNull('reminder_sent_at')
                        ->orWhere('reminder_sent_at', '<', now()->subDays(7));
                })
                ->get();
            
            $this->info("Znaleziono {$overdueInvoices->count()} faktur z przekroczonym terminem płatności.");
            
            foreach ($overdueInvoices as $invoice) {
                DB::beginTransaction();
                
                try {
                    $daysOverdue = now()->diffInDays($invoice->due_date);
                    $user = $invoice->user;
                    
                    if (!$user) {
                        $this->warn("Faktura ID: {$invoice->id} nie ma przypisanego użytkownika. Pomijam...");
                        continue;
                    }
                    
                    $this->info("Wysyłam przypomnienie o zaległej fakturze nr {$invoice->number} (dni po terminie: {$daysOverdue})");
                    
                    // Aktualizacja statusu faktury na overdue, jeśli jeszcze nie jest
                    if ($invoice->status !== 'overdue') {
                        $invoice->status = 'overdue';
                    }
                    
                    $invoice->notification_sent = true;
                    $invoice->reminder_sent_at = now();
                    $invoice->reminders_count += 1;
                    $invoice->save();
                    
                    // Dodaj powiadomienie dla administratora
                    AdminNotification::createInvoiceNotification(
                        'Wysłano przypomnienie o zaległej płatności',
                        "Wysłano przypomnienie o zaległej płatności faktury nr {$invoice->number} dla użytkownika {$user->name}. Faktura jest przeterminowana o {$daysOverdue} dni.",
                        route('admin.billing.invoices.show', $invoice->id),
                        [
                            'invoice_id' => $invoice->id,
                            'user_id' => $user->id,
                            'days_overdue' => $daysOverdue,
                            'reminder_type' => 'overdue'
                        ]
                    );
                    
                    // Tutaj można dodać kod do wysyłania e-maila do użytkownika
                    // np. Mail::to($user->email)->send(new OverdueInvoiceReminderMail($invoice));
                    
                    $remindersSent++;
                    DB::commit();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Błąd podczas wysyłania przypomnienia o zaległej fakturze ID: {$invoice->id}: " . $e->getMessage());
                    Log::error("Błąd wysyłania przypomnienia o zaległej fakturze: " . $e->getMessage(), [
                        'invoice_id' => $invoice->id,
                        'exception' => $e
                    ]);
                }
            }
            
            $this->info("Zakończono. Wysłano {$remindersSent} przypomnień.");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Wystąpił błąd: " . $e->getMessage());
            Log::error("Błąd wykonania komendy SendInvoiceReminders: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}

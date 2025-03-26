<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\AdminPanelController;

class GenerateWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generuje tygodniowy raport ze sprzedaży i subskrypcji';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczęto generowanie tygodniowego raportu...');
        
        try {
            $adminController = app()->make(AdminPanelController::class);
            $response = $adminController->generateWeeklyReport();
            
            if (is_object($response) && method_exists($response, 'getContent')) {
                $data = json_decode($response->getContent(), true);
                
                if ($data['success']) {
                    $this->info('Raport tygodniowy został wygenerowany pomyślnie.');
                    $this->info('ID raportu: ' . $data['report_id']);
                    
                    // Wyświetl skrócone statystyki
                    $this->table(
                        ['Statystyka', 'Wartość'],
                        [
                            ['Nowe subskrypcje', $data['report']['subscriptions']['new']],
                            ['Zakończone subskrypcje', $data['report']['subscriptions']['ended']],
                            ['Odnowione subskrypcje', $data['report']['subscriptions']['renewed']],
                            ['Przychód z nowych subskrypcji', number_format($data['report']['revenue']['new_subscriptions'], 2) . ' PLN'],
                            ['Przychód z odnowień', number_format($data['report']['revenue']['renewals'], 2) . ' PLN'],
                            ['Całkowity przychód', number_format($data['report']['revenue']['total'], 2) . ' PLN'],
                            ['Nieopłacone faktury', $data['report']['unpaid_invoices']['count'] . ' (' . number_format($data['report']['unpaid_invoices']['value'], 2) . ' PLN)'],
                        ]
                    );
                } else {
                    $this->error("Wystąpił błąd: {$data['message']}");
                    if (isset($data['error'])) {
                        $this->error($data['error']);
                    }
                    return 1;
                }
            } else {
                $this->info('Proces generowania raportu zakończony.');
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Wystąpił błąd podczas generowania raportu: ' . $e->getMessage());
            return 1;
        }
    }
}

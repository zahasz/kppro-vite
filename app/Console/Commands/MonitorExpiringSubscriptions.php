<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\AdminPanelController;

class MonitorExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:monitor-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitoruje wygasające subskrypcje i wysyła powiadomienia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczęto monitorowanie wygasających subskrypcji...');
        
        try {
            $adminController = app()->make(AdminPanelController::class);
            $response = $adminController->monitorExpiringSubscriptions();
            
            if (is_object($response) && method_exists($response, 'getContent')) {
                $data = json_decode($response->getContent(), true);
                
                if ($data['success']) {
                    $this->info("Wysłano powiadomienia o {$data['notified_count']} wygasających subskrypcjach.");
                } else {
                    $this->error("Wystąpił błąd: {$data['message']}");
                    if (isset($data['error'])) {
                        $this->error($data['error']);
                    }
                    return 1;
                }
            } else {
                $this->info('Proces monitorowania zakończony.');
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Wystąpił błąd podczas monitorowania wygasających subskrypcji: ' . $e->getMessage());
            return 1;
        }
    }
}

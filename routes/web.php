<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\WarehouseMaterialsController;
use App\Http\Controllers\WarehouseEquipmentController;
use App\Http\Controllers\WarehouseToolsController;
use App\Http\Controllers\WarehouseGarageController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\ModulePermissionController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\BillingSettingsController;
use App\Http\Controllers\Admin\PaymentSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::patch('/company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');
    Route::get('/company-profile/json', [CompanyProfileController::class, 'getJson'])->name('company-profile.json');
    Route::get('/company-profile/create-test', [CompanyProfileController::class, 'createTestProfile'])->name('company-profile.create-test');

    // Zarządzanie kontami bankowymi
    Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');
    Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
    Route::post('/bank-accounts/{bankAccount}/set-default', [BankAccountController::class, 'setDefault'])->name('bank-accounts.set-default');

    // Routing dla kontrahentów
    Route::resource('contractors', ContractorController::class);
    Route::post('contractors/export-pdf', [ContractorController::class, 'exportPDF'])->name('contractors.export-pdf');
    Route::get('contractors/{contractor}/json', [ContractorController::class, 'getJson'])->name('contractors.json');

    // Routing dla modułu finansów
    Route::prefix('finances')->name('finances.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::get('/incomes', [FinanceController::class, 'incomes'])->name('incomes');
        Route::get('/expenses', [FinanceController::class, 'expenses'])->name('expenses');
        
        // Budżet
        Route::prefix('budget')->name('budget.')->group(function () {
            Route::get('/', [BudgetController::class, 'index'])->name('index');
            Route::post('/', [BudgetController::class, 'store'])->name('store');
            Route::get('/{category}', [BudgetController::class, 'get'])->name('get');
            Route::put('/{category}', [BudgetController::class, 'update'])->name('update');
            Route::delete('/{category}', [BudgetController::class, 'destroy'])->name('destroy');
            Route::get('/history', [BudgetController::class, 'history'])->name('history');
            Route::get('/details/{type}', [BudgetController::class, 'details'])->name('details');
        });

        Route::get('/invoices', [FinanceController::class, 'invoices'])->name('invoices');
        Route::get('/reports', [FinanceController::class, 'reports'])->name('reports');
        
        // Faktury
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [FinanceController::class, 'invoices'])->name('index');
            Route::get('/sales', [FinanceController::class, 'salesInvoices'])->name('sales');
            Route::get('/purchases', [FinanceController::class, 'purchaseInvoices'])->name('purchases');
            Route::get('/subscriptions', [FinanceController::class, 'subscriptionInvoices'])->name('subscriptions');
            Route::post('/subscriptions/generate', [FinanceController::class, 'generateSubscriptionInvoices'])->name('subscriptions.generate');
        });
    });

    // Routing dla magazynu
    Route::prefix('warehouse')->name('warehouse.')->middleware('module:warehouse')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        
        // Magazyn materiałów
        Route::get('/materials', [WarehouseMaterialsController::class, 'index'])->name('materials.index');
        Route::post('/materials', [WarehouseMaterialsController::class, 'store'])->name('materials.store');
        Route::get('/materials/create', [WarehouseMaterialsController::class, 'create'])->name('materials.create');
        Route::get('/materials/{material}', [WarehouseMaterialsController::class, 'show'])->name('materials.show');
        Route::put('/materials/{material}', [WarehouseMaterialsController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{material}', [WarehouseMaterialsController::class, 'destroy'])->name('materials.destroy');

        // Magazyn sprzętu
        Route::get('/equipment', [WarehouseEquipmentController::class, 'index'])->name('equipment.index');
        Route::post('/equipment', [WarehouseEquipmentController::class, 'store'])->name('equipment.store');
        Route::get('/equipment/create', [WarehouseEquipmentController::class, 'create'])->name('equipment.create');
        Route::get('/equipment/{equipment}', [WarehouseEquipmentController::class, 'show'])->name('equipment.show');
        Route::put('/equipment/{equipment}', [WarehouseEquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('/equipment/{equipment}', [WarehouseEquipmentController::class, 'destroy'])->name('equipment.destroy');

        // Magazyn narzędzi
        Route::get('/tools', [WarehouseToolsController::class, 'index'])->name('tools.index');
        Route::post('/tools', [WarehouseToolsController::class, 'store'])->name('tools.store');
        Route::get('/tools/create', [WarehouseToolsController::class, 'create'])->name('tools.create');
        Route::get('/tools/{tool}', [WarehouseToolsController::class, 'show'])->name('tools.show');
        Route::put('/tools/{tool}', [WarehouseToolsController::class, 'update'])->name('tools.update');
        Route::delete('/tools/{tool}', [WarehouseToolsController::class, 'destroy'])->name('tools.destroy');

        // Garaż
        Route::get('/garage', [WarehouseGarageController::class, 'index'])->name('garage.index');
        Route::post('/garage', [WarehouseGarageController::class, 'store'])->name('garage.store');
        Route::get('/garage/create', [WarehouseGarageController::class, 'create'])->name('garage.create');
        Route::get('/garage/{vehicle}', [WarehouseGarageController::class, 'show'])->name('garage.show');
        Route::put('/garage/{vehicle}', [WarehouseGarageController::class, 'update'])->name('garage.update');
        Route::delete('/garage/{vehicle}', [WarehouseGarageController::class, 'destroy'])->name('garage.destroy');
    });

    // Routing dla zadań
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
    });

    // Routing dla umów
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');
    });

    // Routing dla kosztorysów
    Route::prefix('estimates')->name('estimates.')->group(function () {
        Route::get('/', [EstimateController::class, 'index'])->name('index');
    });

    // Subskrypcje użytkownika
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');

    // Faktury
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::resource('invoices', InvoiceController::class)->middleware('module:invoices');

    // Produkty
    Route::resource('products', ProductController::class)->middleware('module:products');

    // Te ustawienia zostały usunięte, ponieważ dotyczą one danych prowadzonej działalności
    // i są już dostępne w profilu użytkownika
    // Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    // Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Panel administratora
    Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [AdminPanelController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/details/{type}', [AdminPanelController::class, 'details'])->name('dashboard.details');
        
        // Zarządzanie użytkownikami
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminPanelController::class, 'users'])->name('index');
            Route::get('/online', [AdminPanelController::class, 'onlineUsers'])->name('online');
            Route::get('/create', [AdminPanelController::class, 'createUser'])->name('create');
            Route::post('/', [AdminPanelController::class, 'storeUser'])->name('store');
            Route::get('/{user}/edit', [AdminPanelController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminPanelController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminPanelController::class, 'deleteUser'])->name('delete');
        });
        
        // Zarządzanie rolami
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [AdminPanelController::class, 'roles'])->name('index');
            Route::get('/create', [AdminPanelController::class, 'createRole'])->name('create');
            Route::post('/', [AdminPanelController::class, 'storeRole'])->name('store');
            Route::get('/{role}/edit', [AdminPanelController::class, 'editRole'])->name('edit');
            Route::get('/{role}', [AdminPanelController::class, 'showRole'])->name('show');
            Route::put('/{role}', [AdminPanelController::class, 'updateRole'])->name('update');
            Route::delete('/{role}', [AdminPanelController::class, 'deleteRole'])->name('destroy');
        });
        
        // Zarządzanie uprawnieniami
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [AdminPanelController::class, 'permissions'])->name('index');
            Route::get('/create', [AdminPanelController::class, 'createPermission'])->name('create');
            Route::post('/', [AdminPanelController::class, 'storePermission'])->name('store');
        });
        
        // Strony subskrypcji 
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [AdminPanelController::class, 'subscriptions'])->name('index');
            Route::get('/stats', [AdminPanelController::class, 'subscriptionStats'])->name('stats');
            Route::get('/create', [AdminPanelController::class, 'createSubscription'])->name('create');
            Route::post('/store', [AdminPanelController::class, 'storeSubscription'])->name('store');
            Route::get('/{plan}/edit', [AdminPanelController::class, 'editSubscription'])->name('edit');
            Route::put('/{plan}', [AdminPanelController::class, 'updateSubscription'])->name('update');
            Route::delete('/{plan}', [AdminPanelController::class, 'destroySubscription'])->name('destroy');
            
            // Subskrypcje użytkowników
            Route::get('/users', [AdminPanelController::class, 'userSubscriptions'])->name('users');
            Route::get('/users/create', [AdminPanelController::class, 'createUserSubscription'])->name('create-user-subscription');
            Route::post('/users/store', [AdminPanelController::class, 'storeUserSubscription'])->name('store-user-subscription');
            Route::get('/users/{subscription}/edit', [AdminPanelController::class, 'editUserSubscription'])->name('edit-user-subscription');
            Route::put('/users/{subscription}', [AdminPanelController::class, 'updateUserSubscription'])->name('update-user-subscription');
            Route::delete('/users/{subscription}', [AdminPanelController::class, 'deleteUserSubscription'])->name('delete-user-subscription');
            
            // Ręczna sprzedaż subskrypcji
            Route::match(['get', 'post'], '/manual-sale', [\App\Http\Controllers\Admin\SubscriptionController::class, 'manualSale'])->name('manual-sale');
            
            // Płatności subskrypcji
            Route::get('/payments', [AdminPanelController::class, 'subscriptionPayments'])->name('payments');
            Route::get('/payments/{payment}', [AdminPanelController::class, 'subscriptionPaymentDetails'])->name('payment-details');
        });
        
        // Faktury subskrypcji
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get('/invoices', [AdminPanelController::class, 'invoicesList'])->name('invoices');
            Route::get('/invoices/{invoice}', [AdminPanelController::class, 'invoiceShow'])->name('invoices.show');
            Route::get('/invoices/{invoice}/pdf', [AdminPanelController::class, 'invoicePdf'])->name('invoices.pdf');
            Route::post('/invoices/generate', [AdminPanelController::class, 'generateInvoices'])->name('invoices.generate');
            Route::get('/statistics', [AdminPanelController::class, 'invoiceStatistics'])->name('statistics');
            Route::get('/settings', [AdminPanelController::class, 'invoiceSettings'])->name('settings');
            Route::put('/settings', [AdminPanelController::class, 'updateInvoiceSettings'])->name('settings.update');
            Route::get('/generate', [AdminPanelController::class, 'invoiceGeneratePage'])->name('generate');
            Route::post('/generate/run', [AdminPanelController::class, 'generateInvoices'])->name('generate.run');
        });
        
        // Zarządzanie przychodami
        Route::prefix('revenue')->name('revenue.')->group(function () {
            // Dashboard przychodów
            Route::get('/', [AdminPanelController::class, 'revenueDashboard'])->name('dashboard');
            
            // Raporty miesięczne
            Route::get('/monthly', [AdminPanelController::class, 'revenueMonthly'])->name('monthly');
            
            // Raporty roczne
            Route::get('/annual', [AdminPanelController::class, 'revenueAnnual'])->name('annual');
        });
        
        // System
        Route::prefix('system')->name('system.')->group(function () {
            // Logi systemu
            Route::get('/logs', [AdminPanelController::class, 'systemLogs'])->name('logs');
            Route::post('/logs/clear', [AdminPanelController::class, 'clearSystemLogs'])->name('logs.clear');
            
            // Informacje o systemie
            Route::get('/info', [AdminPanelController::class, 'systemInfo'])->name('info');
            
            // Kopie zapasowe
            Route::get('/backup', [AdminPanelController::class, 'backupSystem'])->name('backup');
            Route::post('/backup', [AdminPanelController::class, 'createBackup'])->name('backup.create');
            
            // Historia logowania
            Route::get('/login-history', [AdminPanelController::class, 'loginHistory'])->name('login-history');
        });
        
        // Zarządzanie uprawnieniami modułów
        Route::prefix('modules')->name('modules.')->group(function () {
            Route::get('/', [ModulePermissionController::class, 'index'])->name('index');
            Route::get('/user/{user}', [ModulePermissionController::class, 'userModules'])->name('user');
            Route::post('/user/{user}/grant', [ModulePermissionController::class, 'grantAccess'])->name('grant');
            Route::post('/user/{user}/deny', [ModulePermissionController::class, 'denyAccess'])->name('deny');
            Route::delete('/user/{user}/module/{module}', [ModulePermissionController::class, 'removeAccess'])->name('remove');
        });

        // Dodatkowa definicja dla trasy czyszczenia logów
        Route::post('/system/logs/clear', [AdminPanelController::class, 'clearSystemLogs'])->name('system.logs.clear');
    });

    // Trasa testowa do dodawania historii logowania
    Route::get('/test/add-login-history', function() {
        $user = \App\Models\User::first();
        
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password')
            ]);
        }
        
        $loginHistory = new \App\Models\LoginHistory();
        $loginHistory->user_id = $user->id;
        $loginHistory->ip_address = request()->ip();
        $loginHistory->user_agent = request()->userAgent();
        $loginHistory->status = 'success';
        $loginHistory->details = 'Testowe logowanie przez stronę diagnostyczną';
        $loginHistory->created_at = now();
        $loginHistory->updated_at = now();
        $loginHistory->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Dodano testowy wpis historii logowania',
            'record' => $loginHistory,
            'user' => $user
        ]);
    });

    // Trasy dla zarządzania planami subskrypcyjnymi
    Route::get('/admin/subscriptions', [AdminPanelController::class, 'subscriptions'])->name('admin.subscriptions.index');
    Route::get('/admin/subscriptions/create', [AdminPanelController::class, 'createSubscription'])->name('admin.subscriptions.create');
    Route::post('/admin/subscriptions/store', [AdminPanelController::class, 'storeSubscription'])->name('admin.subscriptions.store');
    Route::get('/admin/subscriptions/{plan}/edit', [AdminPanelController::class, 'editSubscription'])->name('admin.subscriptions.edit');
    Route::put('/admin/subscriptions/{plan}', [AdminPanelController::class, 'updateSubscription'])->name('admin.subscriptions.update');
    Route::delete('/admin/subscriptions/{plan}', [AdminPanelController::class, 'destroySubscription'])->name('admin.subscriptions.destroy');
    Route::get('/admin/subscriptions/stats', [AdminPanelController::class, 'subscriptionStats'])->name('admin.subscriptions.stats');
    Route::get('/admin/subscriptions/users', [AdminPanelController::class, 'userSubscriptions'])->name('admin.subscriptions.users');
    Route::get('/admin/subscriptions/payments', [AdminPanelController::class, 'subscriptionPayments'])->name('admin.subscriptions.payments');
    Route::get('/admin/subscriptions/payments/{payment}', [AdminPanelController::class, 'subscriptionPaymentDetails'])->name('admin.subscriptions.payment-details');
    Route::get('/admin/subscriptions/permissions', [AdminPanelController::class, 'permissions'])->name('admin.subscriptions.permissions');

    // Trasy dla stron z dolnego menu
    Route::get('/help', function () {
        return view('routes.help');
    })->name('help');
    
    Route::get('/reports', function () {
        return view('routes.reports');
    })->name('reports');
    
    Route::get('/settings', function () {
        return view('routes.settings');
    })->name('settings');
});

// Tymczasowa trasa do ręcznej sprzedaży subskrypcji
Route::get('/manual-subscription-sale', function () {
    try {
        // Znajdź administratora
        $user = \App\Models\User::where('role', 'admin')
            ->orWhere('is_admin', true)
            ->orWhere('email', 'admin@example.com')
            ->first();

        if (!$user) {
            $user = \App\Models\User::first();
            
            if (!$user) {
                return 'Brak użytkowników w systemie. Nie można kontynuować.';
            }
        }

        // Sprawdź, czy profil firmy istnieje, jeśli nie - utwórz
        if (!$user->companyProfile) {
            // Jeśli nie istnieje, utwórz domyślny profil
            $companyProfile = new \App\Models\CompanyProfile();
            $companyProfile->user_id = $user->id;
            $companyProfile->company_name = 'Firma ' . $user->name;
            $companyProfile->tax_number = '1234567890';
            $companyProfile->save();
            
            // Utwórz domyślne konto bankowe
            $bankAccount = new \App\Models\BankAccount();
            $bankAccount->company_profile_id = $companyProfile->id;
            $bankAccount->account_name = 'Główne konto firmowe';
            $bankAccount->account_number = 'PL12 1234 5678 9012 3456 7890 1234';
            $bankAccount->bank_name = 'Polski Bank S.A.';
            $bankAccount->swift = 'POLBPLPW';
            $bankAccount->is_default = true;
            $bankAccount->save();
            
            // Ustaw domyślne konto bankowe
            $companyProfile->default_bank_account_id = $bankAccount->id;
            $companyProfile->save();
            
            // Odśwież użytkownika
            $user->refresh();
        }

        // Znajdź plan subskrypcji
        $plan = \App\Models\SubscriptionPlan::where('code', 'business')->first();
        
        if (!$plan) {
            $plan = \App\Models\SubscriptionPlan::where('is_active', true)->first();
        }

        if (!$plan) {
            return 'Brak aktywnych planów subskrypcji. Nie można kontynuować.';
        }

        // Rozpocznij transakcję
        \Illuminate\Support\Facades\DB::beginTransaction();

        // Utwórz subskrypcję ręcznie
        $subscription = new \App\Models\UserSubscription();
        $subscription->user_id = $user->id;
        $subscription->subscription_plan_id = $plan->id;
        $subscription->status = 'active';
        $subscription->price = $plan->price;
        $subscription->start_date = \Carbon\Carbon::now();
        $subscription->end_date = \Carbon\Carbon::now()->addMonth(); // Miesięczna subskrypcja
        $subscription->subscription_type = 'manual';
        $subscription->renewal_status = null;
        $subscription->payment_method = 'cash';
        $subscription->payment_details = 'Płatność gotówką przyjęta przez administratora';
        $subscription->admin_notes = 'Ręczna sprzedaż subskrypcji przez administratora';
        $subscription->save();

        // Utwórz płatność
        $payment = new \App\Models\SubscriptionPayment();
        $payment->user_id = $user->id;
        $payment->subscription_id = $subscription->id;
        $payment->transaction_id = 'manual-cash-' . time();
        $payment->amount = $plan->price;
        $payment->currency = $plan->currency ?? 'PLN';
        $payment->status = 'completed';
        $payment->payment_method = 'cash';
        $payment->payment_details = 'Płatność gotówką';
        $payment->save();

        // Pobierz serwis subskrypcji
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        
        // Spróbuj wywołać metodę
        $invoice = null;
        $reflection = new \ReflectionClass($subscriptionService);
        
        try {
            $method = $reflection->getMethod('generateInvoiceForPayment');
            $method->setAccessible(true);
            $invoice = $method->invoke($subscriptionService, $payment);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Błąd przy generowaniu faktury: ' . $e->getMessage());
        }

        if ($invoice) {
            // Aktualizuj subskrypcję o identyfikator faktury
            $subscription->last_invoice_id = $invoice->id;
            $subscription->last_invoice_number = $invoice->number;
            $subscription->save();
        }

        // Zatwierdź transakcję
        \Illuminate\Support\Facades\DB::commit();

        // Przygotuj podsumowanie
        $html = '<h1>Proces sprzedaży subskrypcji zakończony pomyślnie!</h1>';
        $html .= '<h2>Podsumowanie:</h2>';
        $html .= '<ul>';
        $html .= '<li>Użytkownik: ' . $user->name . ' (ID: ' . $user->id . ')</li>';
        $html .= '<li>Plan subskrypcji: ' . $plan->name . ' (ID: ' . $plan->id . ')</li>';
        $html .= '<li>Status subskrypcji: ' . $subscription->status . '</li>';
        $html .= '<li>Data rozpoczęcia: ' . $subscription->start_date->format('Y-m-d') . '</li>';
        $html .= '<li>Data zakończenia: ' . $subscription->end_date->format('Y-m-d') . '</li>';
        $html .= '<li>Metoda płatności: ' . $subscription->payment_method . '</li>';
        $html .= '<li>Kwota: ' . $payment->amount . ' ' . $payment->currency . '</li>';
        
        if ($invoice) {
            $html .= '<li>Numer faktury: ' . $invoice->number . '</li>';
            $html .= '<li><a href="' . url("/admin/billing/invoices/{$invoice->id}") . '">Link do faktury</a></li>';
        } else {
            $html .= '<li>Nie udało się wygenerować faktury.</li>';
        }
        
        $html .= '</ul>';
        
        // Linki do panelu administracyjnego
        $html .= '<p><a href="' . url('/admin/subscriptions/users') . '">Przejdź do listy subskrypcji</a></p>';
        
        return $html;
    } catch (\Exception $e) {
        // W przypadku błędu, cofnij transakcję
        \Illuminate\Support\Facades\DB::rollBack();
        
        return 'BŁĄD: ' . $e->getMessage() . "<br>Lokalizacja: " . $e->getFile() . ':' . $e->getLine();
    }
});

// Trasy dla procesu płatności
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/payment/{subscription}', [\App\Http\Controllers\CheckoutController::class, 'selectPaymentMethod'])->name('payment');
    Route::post('/process/{subscription}', [\App\Http\Controllers\CheckoutController::class, 'initiatePayment'])->name('process');
    Route::get('/confirmation/{transactionId}', [\App\Http\Controllers\CheckoutController::class, 'showConfirmation'])->name('confirmation');
    Route::get('/return', [\App\Http\Controllers\CheckoutController::class, 'handleReturn'])->name('return');
});

// Webhooki bramek płatności (bez middleware auth)
Route::prefix('payment-webhooks')->name('payment.webhooks.')->group(function () {
    Route::post('/{gateway}', [\App\Http\Controllers\CheckoutController::class, 'webhook'])->name('process');
});

// Trasy dla modułu płatności i ustawień
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Usunięcie zduplikowanych tras płatności
    // Route::resource('payments', PaymentController::class);
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    
    Route::put('payments/{payment}/toggle-status', [PaymentController::class, 'toggleStatus'])->name('payments.toggle-status');
    Route::get('payments/transactions', [PaymentController::class, 'transactions'])->name('payments.transactions');
    Route::get('payments/transactions/{transaction}', [PaymentController::class, 'transactionDetails'])->name('payments.transaction-details');
    Route::put('payments/transactions/{transaction}/status', [PaymentController::class, 'updateStatus'])->name('payments.update-status');
    Route::post('payments/transactions/{transaction}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    
    // Ustawienia fakturowania
    Route::get('/billing/settings', [BillingSettingsController::class, 'index'])->name('billing.settings');
    Route::put('/billing/settings', [BillingSettingsController::class, 'update'])->name('billing.settings.update');
    
    // Ustawienia płatności
    Route::put('/payment/settings', [PaymentSettingsController::class, 'update'])->name('payment.settings.update');
});

require __DIR__.'/auth.php';

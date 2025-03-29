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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
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
        });
    });

    // Routing dla magazynu
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
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
    Route::resource('invoices', InvoiceController::class);

    // Produkty
    Route::resource('products', ProductController::class);

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
            
            // Płatności subskrypcji
            Route::get('/payments', [AdminPanelController::class, 'subscriptionPayments'])->name('payments');
            Route::get('/payments/{payment}', [AdminPanelController::class, 'subscriptionPaymentDetails'])->name('payment-details');
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

        // Dodatkowa definicja dla trasy czyszczenia logów
        Route::post('/system/logs/clear', [AdminPanelController::class, 'clearSystemLogs'])->name('system.logs.clear');
    });
});

require __DIR__.'/auth.php';

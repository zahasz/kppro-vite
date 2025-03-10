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

    // Routing dla kontrahentów
    Route::resource('contractors', ContractorController::class);
    Route::post('contractors/export-pdf', [ContractorController::class, 'exportPDF'])->name('contractors.export-pdf');

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

    // Panel administratora
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['auth', 'verified'])
        ->group(function () {
            Route::get('/', [AdminPanelController::class, 'index'])->name('index');
            // Alias dla admin.index jako admin.dashboard
            Route::get('/dashboard', [AdminPanelController::class, 'index'])->name('dashboard');
            
            // Zarządzanie rolami
            Route::get('/roles', [AdminPanelController::class, 'roles'])->name('roles');
            Route::post('/roles', [AdminPanelController::class, 'storeRole'])->name('roles.store');
            Route::put('/roles/{role}', [AdminPanelController::class, 'updateRole'])->name('roles.update');
            Route::delete('/roles/{role}', [AdminPanelController::class, 'destroyRole'])->name('roles.destroy');
            
            // Zarządzanie użytkownikami
            Route::get('/users', [AdminPanelController::class, 'users'])->name('users');
            
            // Zarządzanie uprawnieniami
            Route::get('/permissions', [AdminPanelController::class, 'permissions'])->name('permissions');
        });
});

require __DIR__.'/auth.php';

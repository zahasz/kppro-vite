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
        Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
        Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store');
        Route::put('/budget/{category}', [BudgetController::class, 'update'])->name('budget.update');
        Route::delete('/budget/{category}', [BudgetController::class, 'destroy'])->name('budget.destroy');
        Route::get('/budget/history', [BudgetController::class, 'history'])->name('budget.history');
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
});

require __DIR__.'/auth.php';

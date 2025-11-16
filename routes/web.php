<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\ReportController;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Items Management
    Route::get('items-export/pdf', [ItemController::class, 'exportPdf'])->name('items.export-pdf');
    Route::get('items-export/excel', [ItemController::class, 'exportExcel'])->name('items.export-excel');
    Route::put('items/{item}/update-price', [ItemController::class, 'updatePrice'])->name('items.update-price');
    Route::resource('items', ItemController::class);

    // Suppliers Management
    Route::resource('suppliers', SupplierController::class);

    // Transactions Management
    Route::resource('transactions', TransactionController::class);
    Route::get('transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
    Route::get('transactions/{transaction}/pdf', [TransactionController::class, 'downloadPdf'])->name('transactions.pdf');
    Route::post('transactions/{transaction}/send-whatsapp', [TransactionController::class, 'sendToWhatsApp'])->name('transactions.send-whatsapp');
    Route::post('transactions/{transaction}/verify', [TransactionController::class, 'verify'])->name('transactions.verify');

    // Returns Management
    Route::resource('returns', ReturnController::class);
    Route::post('returns/{return}/update-status', [ReturnController::class, 'updateStatus'])->name('returns.update-status');

    // Reports
    Route::get('reports/by-supplier', [ReportController::class, 'bySupplier'])->name('reports.by-supplier');
    Route::get('reports/by-supplier/pdf', [ReportController::class, 'bySupplierPdf'])->name('reports.by-supplier-pdf');
    Route::get('reports/price-history', [ReportController::class, 'priceHistory'])->name('reports.price-history');
    Route::get('reports/price-history/pdf', [ReportController::class, 'priceHistoryPdf'])->name('reports.price-history-pdf');
});

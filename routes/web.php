<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockOpnameController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products / Stock
    Route::resource('products', ProductController::class);
    Route::get('/api/products/search', [ProductController::class, 'searchByCode'])->name('products.search');
    Route::post('/api/products/hitung-harga', [ProductController::class, 'hitungHarga'])->name('products.hitung-harga');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Stock Opname
    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/stock-opname/{stockOpname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opname.approve');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);
    Route::get('/purchases/{purchase}/return', [PurchaseController::class, 'createReturn'])->name('purchases.return');
    Route::post('/purchases/{purchase}/return', [PurchaseController::class, 'storeReturn'])->name('purchases.store-return');

    // POS / Sales
    Route::get('/pos', [SaleController::class, 'pos'])->name('pos');
    Route::post('/pos/transaction', [SaleController::class, 'processTransaction'])->name('pos.transaction')->middleware('throttle:transaction');
    Route::get('/api/pasien/search', [SaleController::class, 'searchPasien'])->name('pasien.search');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
    Route::get('/sales/{sale}/print', [SaleController::class, 'printReceipt'])->name('sales.print');

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/closing-kasir', [ReportController::class, 'closingKasir'])->name('reports.closing-kasir');
    Route::post('/reports/open-shift', [ReportController::class, 'openShift'])->name('reports.open-shift');
    Route::post('/reports/add-expense/{cashRegister}', [ReportController::class, 'addExpense'])->name('reports.add-expense');
    Route::post('/reports/close-shift/{cashRegister}', [ReportController::class, 'closeShift'])->name('reports.close-shift');
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('/reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    // Manual / Buku Panduan
    Route::get('/manual', function () {
        return view('manual');
    })->name('manual');

    // Export PDF


    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/pricing', [\App\Http\Controllers\SettingController::class, 'updatePricing'])->name('settings.update-pricing');
        Route::get('/audit-log', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-log.index');
    });

    // API: pricing config
    Route::get('/api/settings/pricing', [\App\Http\Controllers\SettingController::class, 'getPricingApi'])->name('settings.pricing-api');
});

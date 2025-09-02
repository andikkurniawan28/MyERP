<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\SalesPaymentController;
use App\Http\Controllers\ItemTransactionController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\ServiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login_process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/changePassword', [AuthController::class, 'changePassword'])->name('changePassword');
Route::post('/changePassword', [AuthController::class, 'changePasswordProcess'])->name('changePasswordProcess');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index')->middleware(['auth']);

// Pengguna
Route::resource('roles', RoleController::class)->middleware(['auth']);
Route::resource('users', UserController::class)->middleware(['auth']);

// Keuangan
Route::resource('accounts', AccountController::class)->middleware(['auth']);
Route::resource('journals', JournalController::class)->middleware(['auth']);

// Inventory
Route::resource('warehouses', WarehouseController::class)->middleware(['auth']);
Route::resource('units', UnitController::class)->middleware(['auth']);
Route::resource('item_categories', ItemCategoryController::class)->middleware(['auth']);
Route::resource('items', ItemController::class)->middleware(['auth']);
Route::resource('services', ServiceController::class)->middleware(['auth']);

// Setting
Route::resource('settings', SettingController::class)->middleware(['auth']);

// Kontak
Route::resource('contacts', ContactController::class)->middleware(['auth']);

// Transaksi
Route::resource('item_transactions', ItemTransactionController::class)->middleware(['auth']);
Route::resource('purchases', PurchaseController::class)->middleware(['auth']);
Route::resource('purchasePayments', PurchasePaymentController::class)->middleware(['auth']);
Route::get('purchasePayments/create/{contact_id}', [PurchasePaymentController::class, 'create'])->name('purchasePayments.createByUser')->middleware(['auth']);
Route::resource('sales', SalesController::class)->middleware(['auth']);
Route::resource('salesPayments', SalesPaymentController::class)->middleware(['auth']);
Route::get('salesPayments/create/{contact_id}', [SalesPaymentController::class, 'create'])->name('salesPayments.createByUser')->middleware(['auth']);

// Report
Route::get('report/purchaseReport', [ReportController::class, 'purchaseReport'])->name('report.purchaseReport.index')->middleware(['auth']);
Route::get('report/salesReport', [ReportController::class, 'salesReport'])->name('report.salesReport.index')->middleware(['auth']);
Route::get('report/itemTransactionReport', [ReportController::class, 'itemTransactionReport'])->name('report.itemTransactionReport.index')->middleware(['auth']);
Route::get('report/generalLedger', [ReportController::class, 'generalLedger'])->name('report.generalLedger.index')->middleware(['auth']);
Route::get('report/balanceSheet', [ReportController::class, 'balanceSheet'])->name('report.balanceSheet.index')->middleware(['auth']);
Route::get('report/incomeStatement', [ReportController::class, 'incomeStatement'])->name('report.incomeStatement.index')->middleware(['auth']);
Route::post('report/closeIncomeStatement', [ReportController::class, 'closeIncomeStatement'])->name('report.closeIncomeStatement')->middleware(['auth']);

// Print
Route::get('print/journal/{id}', [PrintController::class, 'journal'])->name('print.journal')->middleware(['auth']);
Route::get('print/itemTransaction/{id}', [PrintController::class, 'itemTransaction'])->name('print.itemTransaction')->middleware(['auth']);
Route::get('print/purchase/{id}', [PrintController::class, 'purchase'])->name('print.purchase')->middleware(['auth']);
Route::get('print/purchasePayment/{id}', [PrintController::class, 'purchasePayment'])->name('print.purchasePayment')->middleware(['auth']);
Route::get('print/sales/{id}', [PrintController::class, 'sales'])->name('print.sales')->middleware(['auth']);
Route::get('print/salesPayment/{id}', [PrintController::class, 'salesPayment'])->name('print.salesPayment')->middleware(['auth']);

// Test
Route::get('test/{id}', TestController::class)->name('test');

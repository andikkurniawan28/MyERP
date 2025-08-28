<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard
Route::get('dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

// Report
Route::post('report/purchaseReportData', [ReportController::class, 'purchaseReportData'])
    ->name('report.purchaseReportData.data');
Route::post('report/salesReportData', [ReportController::class, 'salesReportData'])
    ->name('report.salesReportData.data');
Route::post('report/itemTransactionReportData', [ReportController::class, 'itemTransactionReportData'])
    ->name('report.itemTransactionReportData.data');
Route::post('report/generalLedgerData', [ReportController::class, 'generalLedgerData'])
    ->name('report.generalLedgerData.data');
Route::post('report/balanceSheetData', [ReportController::class, 'balanceSheetData'])
    ->name('report.balanceSheetData.data');
Route::post('report/incomeStatementData', [ReportController::class, 'incomeStatementData'])
    ->name('report.incomeStatementData.data');

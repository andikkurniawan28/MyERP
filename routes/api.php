<?php

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


// Report
Route::post('report/purchaseReportData', [ReportController::class, 'purchaseReportData'])
    ->name('report.purchaseReportData.data');
Route::post('report/salesReportData', [ReportController::class, 'salesReportData'])
    ->name('report.salesReportData.data');

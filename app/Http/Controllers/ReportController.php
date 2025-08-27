<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sales;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function purchaseReport()
    {
        $users = User::all();
        $suppliers = Contact::where('type', 'supplier')->get();
        $warehouses = Warehouse::all();
        return view('reports.purchaseReport', compact('users', 'suppliers','warehouses'));
    }

    public function purchaseReportData(Request $request)
    {
        $query = Purchase::with(['contact', 'warehouse', 'user']);

        // filter bulan
        if ($request->filled('month') && $request->month != "0") {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)
                ->whereMonth('date', $month);
        }

        // filter status
        if ($request->filled('status') && $request->status != "0") {
            $query->where('status', $request->status);
        }

        // filter contact
        if ($request->filled('contact_id') && $request->contact_id != "0") {
            $query->where('contact_id', $request->contact_id);
        }

        // filter user
        if ($request->filled('user_id') && $request->user_id != "0") {
            $query->where('user_id', $request->user_id);
        }

        // filter warehouse
        if ($request->filled('warehouse_id') && $request->warehouse_id != "0") {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $data = $query->orderBy('id', "asc")->get();

        // Hitung sum
        $totals = [
            'grand_total' => $data->sum('grand_total'),
            'paid'        => $data->sum('paid'),
            'remaining'   => $data->sum('remaining'),
        ];

        return response()->json([
            'data'   => $data,
            'totals' => $totals,
        ]);
    }

    public function salesReport()
    {
        $users = User::all();
        $customers = Contact::where('type', '!=', 'supplier')->get();
        $warehouses = Warehouse::all();
        return view('reports.salesReport', compact('users', 'customers','warehouses'));
    }

    public function salesReportData(Request $request)
    {
        $query = Sales::with(['contact', 'warehouse', 'user']);

        // filter bulan
        if ($request->filled('month') && $request->month != "0") {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)
                ->whereMonth('date', $month);
        }

        // filter status
        if ($request->filled('status') && $request->status != "0") {
            $query->where('status', $request->status);
        }

        // filter contact
        if ($request->filled('contact_id') && $request->contact_id != "0") {
            $query->where('contact_id', $request->contact_id);
        }

        // filter user
        if ($request->filled('user_id') && $request->user_id != "0") {
            $query->where('user_id', $request->user_id);
        }

        // filter warehouse
        if ($request->filled('warehouse_id') && $request->warehouse_id != "0") {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $data = $query->orderBy('id', "asc")->get();

        // Hitung sum
        $totals = [
            'grand_total' => $data->sum('grand_total'),
            'paid'        => $data->sum('paid'),
            'remaining'   => $data->sum('remaining'),
        ];

        return response()->json([
            'data'   => $data,
            'totals' => $totals,
        ]);
    }

}

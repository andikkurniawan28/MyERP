<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Sales;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ItemTransaction;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function purchaseReport()
    {
        $users = User::all();
        $suppliers = Contact::where('type', 'supplier')->get();
        $warehouses = Warehouse::all();
        return view('reports.purchaseReport', compact('users', 'suppliers', 'warehouses'));
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
        return view('reports.salesReport', compact('users', 'customers', 'warehouses'));
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

    public function itemTransactionReport()
    {
        $users = User::all();
        $warehouses = Warehouse::all();
        $items = Item::all();

        return view('reports.itemTransactionReport', compact('users', 'warehouses', 'items'));
    }

    public function itemTransactionReportData(Request $request)
    {
        $query = ItemTransaction::with([
            'user',
            'warehouse',
            'details.item.mainUnit'
        ]);

        // --- Hitung saldo awal ---
        $saldoAwalQuery = ItemTransaction::with('details');

        // filter user
        if ($request->filled('user_id') && $request->user_id != "0") {
            $query->where('user_id', $request->user_id);
            $saldoAwalQuery->where('user_id', $request->user_id);
        }

        // filter warehouse
        if ($request->filled('warehouse_id') && $request->warehouse_id != "0") {
            $query->where('warehouse_id', $request->warehouse_id);
            $saldoAwalQuery->where('warehouse_id', $request->warehouse_id);
        }

        // filter item
        if ($request->filled('item_id') && $request->item_id != "0") {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
            $saldoAwalQuery->whereHas('details', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        // filter tipe transaksi
        if ($request->filled('type') && $request->type != "0") {
            if ($request->type == "purchase") {
                $query->whereNotNull('purchase_id');
                $saldoAwalQuery->whereNotNull('purchase_id');
            } elseif ($request->type == "sales") {
                $query->whereNotNull('sales_id');
                $saldoAwalQuery->whereNotNull('sales_id');
            } elseif ($request->type == "manual") {
                $query->whereNull('purchase_id')->whereNull('sales_id');
                $saldoAwalQuery->whereNull('purchase_id')->whereNull('sales_id');
            }
        }

        $saldoAwal = 0;

        if ($request->filled('month') && $request->month != "0") {
            [$year, $month] = explode('-', $request->month);

            // ambil transaksi bulan itu
            $query->whereYear('date', $year)->whereMonth('date', $month);

            // hitung saldo sebelum bulan itu
            $saldoAwalTx = $saldoAwalQuery->where(function ($q) use ($year, $month) {
                $q->whereYear('date', '<', $year)
                    ->orWhere(function ($q2) use ($year, $month) {
                        $q2->whereYear('date', $year)->whereMonth('date', '<', $month);
                    });
            })->get();

            $saldoAwal = $saldoAwalTx->flatMap->details->sum(fn($d) => ($d->in ?? 0) - ($d->out ?? 0));
        }

        $data = $query->orderBy('date', "asc")->orderBy('id', 'asc')->get();

        // Hitung saldo berjalan
        $runningSaldo = $saldoAwal;
        foreach ($data as $tx) {
            foreach ($tx->details as $detail) {
                $in  = $detail->in ?? 0;
                $out = $detail->out ?? 0;
                $runningSaldo += $in - $out;
                $detail->saldo = $runningSaldo; // inject saldo ke setiap detail
            }
        }

        // Hitung total in & out
        $totals = [
            'in'  => $data->flatMap->details->sum('in'),
            'out' => $data->flatMap->details->sum('out'),
            'saldo_awal' => $saldoAwal,
            'saldo_akhir' => $runningSaldo,
        ];

        return response()->json([
            'data'   => $data,
            'totals' => $totals,
        ]);
    }

    public function generalLedger()
    {
        $accounts = Account::all();
        return view('reports.generalLedger', compact('accounts'));
    }

    public function generalLedgerData(Request $request)
    {
        // Ambil input dari request
        $accountId = $request->account_id;
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? now()->endOfMonth()->toDateString();

        // Pastikan account_id ada
        $account = Account::findOrFail($accountId);

        // Hitung saldo awal (sebelum start_date)
        $saldoAwalQuery = JournalDetail::where('account_id', $accountId)
            ->whereHas('journal', function($q) use ($startDate) {
                $q->where('date', '<', $startDate);
            });

        $totalDebitAwal  = $saldoAwalQuery->sum('debit');
        $totalCreditAwal = $saldoAwalQuery->sum('credit');

        // Sesuaikan saldo awal berdasarkan normal balance
        if ($account->normal_balance === 'debit') {
            $saldoAwal = $totalDebitAwal - $totalCreditAwal;
        } else {
            $saldoAwal = $totalCreditAwal - $totalDebitAwal;
        }

        // Ambil transaksi periode berjalan
        $journals = JournalDetail::with('journal')
            ->where('account_id', $accountId)
            ->whereHas('journal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->orderBy(Journal::select('date')->whereColumn('journals.id', 'journal_details.journal_id'))
            ->get();

        // Loop untuk hitung saldo berjalan
        $saldoBerjalan = $saldoAwal;
        $data = [];

        // Tambahkan baris saldo awal
        $data[] = [
            'date'        => $startDate,
            'description' => 'Saldo Awal',
            'debit'       => 0,
            'credit'      => 0,
            'saldo'       => $saldoBerjalan,
            'code'        => '',
        ];

        foreach ($journals as $detail) {
            $debit  = $detail->debit;
            $credit = $detail->credit;

            if ($account->normal_balance === 'debit') {
                $saldoBerjalan += $debit - $credit;
            } else {
                $saldoBerjalan += $credit - $debit;
            }

            $data[] = [
                'date'        => $detail->journal->date,
                'description' => $detail->journal->description,
                'debit'       => $debit,
                'credit'      => $credit,
                'saldo'       => $saldoBerjalan,
                'code'        => $detail->journal->code,
            ];
        }

        return response()->json([
            'account'     => $account->name,
            'normal_balance' => $account->normal_balance,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'saldo_awal'  => $saldoAwal,
            'data'        => $data,
            'saldo_akhir' => $saldoBerjalan,
        ]);
    }

}

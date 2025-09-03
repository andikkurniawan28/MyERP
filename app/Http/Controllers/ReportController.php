<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Sales;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Setting;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ItemTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function purchaseReport()
    {
        if ($response = $this->checkIzin('akses_laporan_pembelian')) {
            return $response;
        }

        $users = User::all();
        $suppliers = Contact::where('type', 'supplier')->get();
        $warehouses = Warehouse::all();
        $branches = Branch::all();
        return view('reports.purchaseReport', compact('users', 'suppliers', 'warehouses', 'branches'));
    }

    public function purchaseReportData(Request $request)
    {
        $query = Purchase::with(['contact', 'warehouse', 'branch']);

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

        // filter branch
        if ($request->filled('branch_id') && $request->branch_id != "0") {
            $query->where('branch_id', $request->branch_id);
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
        if ($response = $this->checkIzin('akses_laporan_penjualan')) {
            return $response;
        }

        $users = User::all();
        $customers = Contact::where('type', '!=', 'supplier')->get();
        $warehouses = Warehouse::all();
        $branches = Branch::all();
        return view('reports.salesReport', compact('users', 'customers', 'warehouses', 'branches'));
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

        // filter branch
        if ($request->filled('branch_id') && $request->branch_id != "0") {
            $query->where('branch_id', $request->branch_id);
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
        if ($response = $this->checkIzin('akses_laporan_mutasi_barang')) {
            return $response;
        }

        $users = User::all();
        $warehouses = Warehouse::all();
        $items = Item::all();
        $categories = ItemCategory::all();

        return view('reports.itemTransactionReport', compact('users', 'warehouses', 'items', 'categories'));
    }

    public function itemTransactionReportData(Request $request)
    {
        $query = ItemTransaction::with([
            'user',
            'warehouse',
            'details.item.mainUnit'
        ]);

        // --- Hitung saldo awal ---
        $saldoAwalQuery = ItemTransaction::with('details.item');

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

        // 🔥 filter item_category
        // if ($request->filled('item_category_id') && $request->item_category_id != "0") {
        //     $query->whereHas('details.item', function ($q) use ($request) {
        //         $q->where('item_category_id', $request->item_category_id);
        //     });
        //     $saldoAwalQuery->whereHas('details.item', function ($q) use ($request) {
        //         $q->where('item_category_id', $request->item_category_id);
        //     });
        // }

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
        if ($response = $this->checkIzin('akses_buku_besar')) {
            return $response;
        }
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
            ->whereHas('journal', function ($q) use ($startDate) {
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
            ->whereHas('journal', function ($q) use ($startDate, $endDate) {
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

    public function balanceSheet()
    {
        if ($response = $this->checkIzin('akses_neraca')) {
            return $response;
        }
        $accounts = Account::all();
        return view('reports.balanceSheet', compact('accounts'));
    }

    public function balanceSheetData(Request $request)
    {
        $query = JournalDetail::with('journal', 'account');

        // filter sampai dengan akhir bulan (cumulative)
        if ($request->filled('month') && $request->month != "0") {
            [$year, $month] = explode('-', $request->month);
            $endOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $query->whereHas('journal', function ($q) use ($endOfMonth) {
                $q->whereDate('date', '<=', $endOfMonth);
            });
        }

        $journalDetails = $query->get();

        $balances = [
            'asset' => [],
            'liability' => [],
            'equity' => [],
        ];

        $totals = [
            'asset' => 0,
            'liability' => 0,
            'equity' => 0,
        ];

        foreach ($journalDetails as $detail) {
            $account = $detail->account;

            if (!$account) continue;

            if (!in_array($account->category, ['asset', 'liability', 'equity'])) {
                continue;
            }

            $category = $account->category;
            $code     = $account->code;
            $name     = $account->name;

            // hitung saldo sesuai normal_balance
            $balance = ($account->normal_balance === 'debit')
                ? $detail->debit - $detail->credit
                : $detail->credit - $detail->debit;

            if (!isset($balances[$category][$code])) {
                $balances[$category][$code] = [
                    'code' => $code,
                    'name' => $name,
                    'balance' => 0,
                ];
            }

            $balances[$category][$code]['balance'] += $balance;
            $totals[$category] += $balance;
        }

        foreach ($balances as $category => $accounts) {
            $balances[$category] = array_values($accounts);
        }

        return response()->json([
            'balances' => $balances,
            'totals' => $totals,
        ]);
    }

    public function incomeStatement()
    {
        if ($response = $this->checkIzin('akses_laporan_laba_rugi')) {
            return $response;
        }
        $accounts = Account::all();
        return view('reports.incomeStatement', compact('accounts'));
    }

    public function incomeStatementData(Request $request)
    {
        $query = JournalDetail::with('journal', 'account');

        // filter per bulan
        if ($request->filled('month') && $request->month != "0") {
            [$year, $month] = explode('-', $request->month);

            $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endOfMonth   = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // $query->whereHas('journal', function ($q) use ($startOfMonth, $endOfMonth) {
            //     $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
            // });

            $query->whereHas('journal', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('is_closing_entry', 0); // hanya jurnal biasa, bukan penutup
            });

        }

        $journalDetails = $query->get();

        $balances = [
            'revenue' => [],
            'cogs' => [],
            'expense' => [],
        ];

        $totals = [
            'revenue' => 0,
            'cogs' => 0,
            'expense' => 0,
            'gross_profit' => 0,
            'net_income' => 0,
        ];

        foreach ($journalDetails as $detail) {
            $account = $detail->account;

            if (!$account) continue;

            // hanya kategori laporan laba rugi
            if (!in_array($account->category, ['revenue', 'cogs', 'expense'])) {
                continue;
            }

            $category = $account->category;
            $code     = $account->code;
            $name     = $account->name;

            // hitung saldo sesuai normal_balance
            $balance = ($account->normal_balance === 'debit')
                ? $detail->debit - $detail->credit
                : $detail->credit - $detail->debit;

            if (!isset($balances[$category][$code])) {
                $balances[$category][$code] = [
                    'code' => $code,
                    'name' => $name,
                    'balance' => 0,
                ];
            }

            $balances[$category][$code]['balance'] += $balance;
            $totals[$category] += $balance;
        }

        // hitung gross profit & net income
        $totals['gross_profit'] = $totals['revenue'] - $totals['cogs'];
        $totals['net_income']   = $totals['gross_profit'] - $totals['expense'];

        foreach ($balances as $category => $accounts) {
            $balances[$category] = array_values($accounts);
        }

        return response()->json([
            'balances' => $balances,
            'totals' => $totals,
        ]);
    }

    public function closeIncomeStatement(Request $request)
    {
        if ($response = $this->checkIzin('akses_tutupan_laporan_laba_rugi')) {
            return $response;
        }

        $setting = Setting::first();
        $retainedEarningsAccountId = $setting->retained_earning_account_id;

        $month = $request->input('month'); // format: YYYY-MM
        $date = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // cek apakah sudah ada jurnal penutup di bulan ini
        $exists = Journal::where('is_closing_entry', 1)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Jurnal penutup bulan ini sudah dibuat.');
        }

        try {
            DB::transaction(function () use ($request, $date, $retainedEarningsAccountId) {

                // Ambil data Income Statement
                $response = $this->incomeStatementData($request);
                $balances = $response->getData(true); // ['balances' => [...], 'totals' => [...]]

                $accountMapping = Account::pluck('id', 'code')->toArray();

                $journal = Journal::create([
                    'code' => Journal::generateCode('JRN'),
                    'date' => $date,
                    'description' => 'Jurnal Penutup Laba Rugi ' . $date->format('F Y'),
                    'debit' => 0,
                    'credit' => 0,
                    'user_id' => Auth::id(),
                    'is_closing_entry' => 1,
                ]);

                $totalDebit = 0;
                $totalCredit = 0;

                // Revenue → didebit
                foreach ($balances['balances']['revenue'] ?? [] as $acc) {
                    $amount = floatval($acc['balance'] ?? 0);
                    $accountId = $accountMapping[$acc['code']] ?? null;
                    if (!$amount || !$accountId) continue;

                    JournalDetail::create([
                        'journal_id' => $journal->id,
                        'account_id' => $accountId,
                        'debit' => $amount,
                        'credit' => 0,
                    ]);
                    $totalDebit += $amount;
                }

                // COGS & Expense → dikredit
                foreach (['cogs','expense'] as $group) {
                    foreach ($balances['balances'][$group] ?? [] as $acc) {
                        $amount = floatval($acc['balance'] ?? 0);
                        $accountId = $accountMapping[$acc['code']] ?? null;
                        if (!$amount || !$accountId) continue;

                        JournalDetail::create([
                            'journal_id' => $journal->id,
                            'account_id' => $accountId,
                            'debit' => 0,
                            'credit' => $amount,
                        ]);
                        $totalCredit += $amount;
                    }
                }

                // Tutup ke Retained Earnings
                $netIncome = $balances['totals']['net_income'] ?? 0;
                if ($netIncome > 0) {
                    // Laba → kredit Retained Earnings
                    JournalDetail::create([
                        'journal_id' => $journal->id,
                        'account_id' => $retainedEarningsAccountId,
                        'debit' => 0,
                        'credit' => $netIncome,
                    ]);
                    $totalCredit += $netIncome;
                } elseif ($netIncome < 0) {
                    // Rugi → debit Retained Earnings
                    JournalDetail::create([
                        'journal_id' => $journal->id,
                        'account_id' => $retainedEarningsAccountId,
                        'debit' => abs($netIncome),
                        'credit' => 0,
                    ]);
                    $totalDebit += abs($netIncome);
                }

                // Update total debit & credit di header jurnal
                $journal->update([
                    'debit' => $totalDebit,
                    'credit' => $totalCredit,
                ]);
            });

            return redirect()->back()->with('success', 'Jurnal penutup berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


}

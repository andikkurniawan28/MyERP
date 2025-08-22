<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Journal;
use App\Models\Setting;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ItemTransaction;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemTransactionDetail;

class ItemTransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_transaksi_barang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = ItemTransaction::query()->with('warehouse', 'user');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->user ? $row->user->name : '-')
                ->addColumn('warehouse', fn($row) => $row->warehouse ? $row->warehouse->name : '-')
                ->editColumn('date', function ($row) {
                    $carbon = Carbon::parse($row->date)->locale('id');
                    return $carbon->translatedFormat('l, d/m/Y');
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_daftar_transaksi_barang) {
                        $showUrl = route('item_transactions.show', $row->id);
                        $buttons .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info">Detail</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_transaksi_barang) {
                        $deleteUrl = route('item_transactions.destroy', $row->id);
                        $buttons .= '
                            <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        ';
                    }
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('item_transactions.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_transaksi_barang')) {
            return $response;
        }
        $code = ItemTransaction::generateCode();
        $items = Item::all();
        $warehouses = Warehouse::all();
        return view('item_transactions.create', compact('items', 'warehouses', 'code'));
    }

    public function storeOld(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_transaksi_barang')) {
            return $response;
        }

        $request->validate([
            'date' => 'required|date',
            'description' => 'required',
            'code' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
            'item_id.*' => 'required|exists:items,id',
            'in.*' => 'nullable|numeric',
            'out.*' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $transaction = ItemTransaction::create([
                'date' => $request->date,
                'description' => $request->description,
                'warehouse_id' => $request->warehouse_id,
                'user_id' => Auth::id(),
                'code' => $request->code,
            ]);

            foreach ($request->item_id as $i => $itemId) {
                ItemTransactionDetail::create([
                    'item_transaction_id' => $transaction->id,
                    'item_id' => $itemId,
                    'in' => $request->in[$i] ?? 0,
                    'out' => $request->out[$i] ?? 0,
                ]);
            }
        });

        return redirect()->route('item_transactions.index')->with('success', 'Transaksi barang berhasil dibuat.');
    }

    public function show(ItemTransaction $itemTransaction)
    {
        if ($response = $this->checkIzin('akses_daftar_transaksi_barang')) {
            return $response;
        }

        $itemTransaction->load(['warehouse', 'details.item']);

        return view('item_transactions.show', compact('itemTransaction'));
    }

    public function destroy(ItemTransaction $itemTransaction)
    {
        if ($response = $this->checkIzin('akses_hapus_transaksi_barang')) {
            return $response;
        }

        $itemTransaction->delete();
        return redirect()->route('item_transactions.index')->with('success', 'Transaksi barang berhasil dihapus.');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_transaksi_barang')) {
            return $response;
        }

        $request->validate([
            'date' => 'required|date',
            'description' => 'required',
            'code' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
            'item_id.*' => 'required|exists:items,id',
            'in.*' => 'nullable|numeric',
            'out.*' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $transaction = $this->createTransaction($request);
            $totalValue = $this->createTransactionDetails($transaction, $request);
            if ($totalValue != 0) {
                $setting = Setting::first();
                $journal = $this->createJournal($request, $totalValue, $transaction);
                $this->createJournalDetails($journal, $totalValue, $setting);
            }
        });

        return redirect()->route('item_transactions.index')->with('success', 'Transaksi barang dan jurnal berhasil dibuat.');
    }

    /**
     * Buat header transaksi
     */
    protected function createTransaction(Request $request)
    {
        return ItemTransaction::create([
            'date' => $request->date,
            'description' => $request->description,
            'warehouse_id' => $request->warehouse_id,
            'user_id' => Auth::id(),
            'code' => $request->code,
        ]);
    }

    /**
     * Buat detail transaksi dan hitung total nilai
     */
    protected function createTransactionDetails(ItemTransaction $transaction, Request $request)
    {
        $totalValue = 0;
        foreach ($request->item_id as $i => $itemId) {
            $inQty = $request->in[$i] ?? 0;
            $outQty = $request->out[$i] ?? 0;
            $item = Item::findOrFail($itemId);
            ItemTransactionDetail::create([
                'item_transaction_id' => $transaction->id,
                'item_id' => $itemId,
                'in' => $inQty,
                'out' => $outQty,
            ]);
            $totalValue += ($inQty - $outQty) * $item->purchase_price_main;
        }
        return $totalValue;
    }

    /**
     * Buat jurnal header
     */
    protected function createJournal(Request $request, float $totalValue, $transaction)
    {
        return Journal::create([
            'code' => Journal::generateCode(),
            'date' => $request->date,
            'description' => $request->description,
            'debit' => $totalValue,
            'credit' => $totalValue,
            'user_id' => Auth::id(),
            'item_transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Buat detail jurnal
     */
    protected function createJournalDetails(Journal $journal, float $totalValue, Setting $setting)
    {
        if ($totalValue > 0) {
            // Barang masuk = debit inventory, kredit stock_in
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $setting->inventory_account_id,
                'debit' => $totalValue,
                'credit' => 0,
            ]);
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $setting->stock_in_account_id,
                'debit' => 0,
                'credit' => $totalValue,
            ]);
        } else {
            // Barang keluar = debit stock_out, kredit inventory
            $totalValue = abs($totalValue);
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $setting->stock_out_account_id,
                'debit' => $totalValue,
                'credit' => 0,
            ]);
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $setting->inventory_account_id,
                'debit' => 0,
                'credit' => $totalValue,
            ]);
        }
    }

}

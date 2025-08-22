<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemTransaction;
use App\Models\ItemTransactionDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
}

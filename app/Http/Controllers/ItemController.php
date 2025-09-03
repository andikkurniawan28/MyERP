<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_barang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Item::query()->with(['category', 'mainUnit']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('category', fn($row) => $row->category->name)
                ->editColumn('mainUnit', fn($row) => $row->mainUnit->name)
                // ->addColumn('secondary_unit', fn($row) => $row->secondaryUnit->name)
                ->editColumn('updated_at', function ($row) {
                    return Carbon::parse($row->updated_at)->locale('id')->translatedFormat('d-m-Y H:i');
                })
                ->addColumn('saldo', function ($row) {
                    $saldo = $row->saldo();
                    return $saldo == 0 ? '-' : number_format($saldo, 0, ',', '.'); // format lokal
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';

                    // Hak akses edit item
                    if (Auth()->user()->role->akses_edit_barang) {
                        $editUrl = route('items.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }

                    // Hak akses hapus item
                    if (Auth()->user()->role->akses_hapus_barang) {
                        $deleteUrl = route('items.destroy', $row->id);
                        $buttons .= '
                            <form action="' . $deleteUrl . '" method="POST"
                                onsubmit="return confirm(\'Hapus data ini?\')"
                                style="display:inline-block;">
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

        return view('items.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_barang')) {
            return $response;
        }

        $categories = ItemCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('items.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_barang')) {
            return $response;
        }

        $request->validate([
            'item_category_id'       => 'required|exists:item_categories,id',
            'barcode'                => 'nullable|unique:items,barcode',
            'code'                   => 'required|unique:items,code',
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'main_unit_id'           => 'required|exists:units,id',
            'purchase_price_main'    => 'required|numeric|min:0',
            'selling_price_main'     => 'nullable|numeric|min:0',
        ]);

        // Handle is_countable (default 0 jika tidak dicentang)
        $data = $request->all();
        $data['is_countable'] = $request->has('is_countable') ? 1 : 0;

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        if ($response = $this->checkIzin('akses_edit_barang')) {
            return $response;
        }

        $categories = ItemCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        if ($response = $this->checkIzin('akses_edit_barang')) {
            return $response;
        }

        $request->validate([
            'item_category_id'       => 'required|exists:item_categories,id',
            'barcode'                => 'nullable|unique:items,barcode,' . $item->id,
            'code'                   => 'required|unique:items,code,' . $item->id,
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'main_unit_id'           => 'required|exists:units,id',
            'purchase_price_main'    => 'required|numeric|min:0',
            'selling_price_main'     => 'nullable|numeric|min:0',
        ]);

        // Handle is_countable
        $data = $request->all();
        $data['is_countable'] = $request->has('is_countable') ? 1 : 0;

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($response = $this->checkIzin('akses_hapus_barang')) {
            return $response;
        }
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}

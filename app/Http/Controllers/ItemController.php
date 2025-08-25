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
        if ($request->ajax()) {
            $data = Item::with(['category', 'mainUnit', 'secondaryUnit'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('category', fn($row) => $row->category->name)
                ->editColumn('main_unit', fn($row) => $row->mainUnit->name)
                // ->addColumn('secondary_unit', fn($row) => $row->secondaryUnit->name)
                ->editColumn('updated_at', function ($row) {
                    return Carbon::parse($row->updated_at)->locale('id')->translatedFormat('d-m-Y H:i');
                })
                ->addColumn('saldo', function ($row) {
                    $saldo = $row->saldo();
                    return $saldo == 0 ? '-' : number_format($saldo, 0, ',', '.'); // format lokal
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('items.edit', $row->id);
                    $deleteUrl = route('items.destroy', $row->id);
                    return '
                        <div class="btn-group" role="group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                            <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('items.index');
    }

    public function create()
    {
        $categories = ItemCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('items.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_category_id'       => 'required|exists:item_categories,id',
            'barcode'                => 'nullable|unique:items,barcode',
            'code'                   => 'required|unique:items,code',
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'main_unit_id'           => 'required|exists:units,id',
            'secondary_unit_id'      => 'required|exists:units,id',
            'conversion_rate'        => 'required|numeric|min:0',
            'purchase_price_secondary'=> 'required|numeric|min:0',
            'selling_price_secondary' => 'nullable|numeric|min:0',
            'purchase_price_main'    => 'required|numeric|min:0',
            'selling_price_main'     => 'nullable|numeric|min:0',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = ItemCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_category_id'       => 'required|exists:item_categories,id',
            'barcode'                => 'nullable|unique:items,barcode,' . $item->id,
            'code'                   => 'required|unique:items,code,' . $item->id,
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'main_unit_id'           => 'required|exists:units,id',
            'secondary_unit_id'      => 'required|exists:units,id',
            'conversion_rate'        => 'required|numeric|min:0',
            'purchase_price_secondary'=> 'required|numeric|min:0',
            'selling_price_secondary' => 'nullable|numeric|min:0',
            'purchase_price_main'    => 'required|numeric|min:0',
            'selling_price_main'     => 'nullable|numeric|min:0',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}

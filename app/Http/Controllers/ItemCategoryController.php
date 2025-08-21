<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_kategori_barang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = ItemCategory::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_edit_kategori_barang) {
                        $editUrl = route('item_categories.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_kategori_barang) {
                        $deleteUrl = route('item_categories.destroy', $row->id);
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

        return view('item_categories.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_kategori_barang')) {
            return $response;
        }

        return view('item_categories.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_kategori_barang')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        ItemCategory::create($request->all());

        return redirect()->route('item_categories.index')->with('success', 'Kategori Barang berhasil ditambahkan.');
    }

    public function edit(ItemCategory $item_category)
    {
        if ($response = $this->checkIzin('akses_edit_kategori_barang')) {
            return $response;
        }

        return view('item_categories.edit', compact('item_category'));
    }

    public function update(Request $request, ItemCategory $item_category)
    {
        if ($response = $this->checkIzin('akses_edit_kategori_barang')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        $item_category->update($request->all());

        return redirect()->route('item_categories.index')->with('success', 'Kategori Barang berhasil diperbarui.');
    }

    public function destroy(ItemCategory $item_category)
    {
        if ($response = $this->checkIzin('akses_hapus_kategori_barang')) {
            return $response;
        }

        $item_category->delete();
        return redirect()->route('item_categories.index')->with('success', 'Kategori Barang berhasil dihapus.');
    }
}

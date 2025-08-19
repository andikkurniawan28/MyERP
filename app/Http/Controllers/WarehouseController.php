<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_gudang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Warehouse::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_edit_gudang) {
                        $editUrl = route('warehouses.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_gudang) {
                        $deleteUrl = route('warehouses.destroy', $row->id);
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

        return view('warehouses.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_gudang')) {
            return $response;
        }

        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_gudang')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        Warehouse::create($request->all());

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Warehouse $warehouse)
    {
        if ($response = $this->checkIzin('akses_edit_gudang')) {
            return $response;
        }

        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        if ($response = $this->checkIzin('akses_edit_gudang')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $warehouse->update($request->all());

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($response = $this->checkIzin('akses_hapus_gudang')) {
            return $response;
        }

        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus.');
    }
}

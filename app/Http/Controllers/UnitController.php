<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_satuan')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Unit::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_edit_satuan) {
                        $editUrl = route('units.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_satuan) {
                        $deleteUrl = route('units.destroy', $row->id);
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

        return view('units.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_satuan')) {
            return $response;
        }

        return view('units.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_satuan')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        if ($response = $this->checkIzin('akses_edit_satuan')) {
            return $response;
        }

        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        if ($response = $this->checkIzin('akses_edit_satuan')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            // 'address' => 'required|string',
        ]);

        $unit->update($request->all());

        return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($response = $this->checkIzin('akses_hapus_satuan')) {
            return $response;
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }
}

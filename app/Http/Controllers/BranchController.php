<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Branch;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_cabang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Branch::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_edit_cabang) {
                        $editUrl = route('branches.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_cabang) {
                        $deleteUrl = route('branches.destroy', $row->id);
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

        return view('branches.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_cabang')) {
            return $response;
        }

        return view('branches.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_cabang')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        Branch::create($request->all());

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch)
    {
        if ($response = $this->checkIzin('akses_edit_cabang')) {
            return $response;
        }

        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        if ($response = $this->checkIzin('akses_edit_cabang')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        $branch->update($request->all());

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($response = $this->checkIzin('akses_hapus_cabang')) {
            return $response;
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}

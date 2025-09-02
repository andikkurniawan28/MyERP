<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Service;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_jasa')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Service::query()->with(['mainUnit']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('mainUnit', fn($row) => $row->mainUnit->name)
                ->editColumn('updated_at', function ($row) {
                    return Carbon::parse($row->updated_at)->locale('id')->translatedFormat('d-m-Y H:i');
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';

                    // Hak akses edit service
                    if (Auth()->user()->role->akses_edit_jasa) {
                        $editUrl = route('services.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }

                    // Hak akses hapus service
                    if (Auth()->user()->role->akses_hapus_jasa) {
                        $deleteUrl = route('services.destroy', $row->id);
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

        return view('services.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_jasa')) {
            return $response;
        }

        $units = Unit::orderBy('name')->get();
        return view('services.create', compact('units'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_jasa')) {
            return $response;
        }

        $request->validate([
            'barcode'                => 'nullable|unique:services,barcode',
            'code'                   => 'required|unique:services,code',
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'main_unit_id'           => 'required|exists:units,id',
            'purchase_price_main'    => 'required|numeric|min:0',
            'selling_price_main'     => 'nullable|numeric|min:0',
        ]);

        Service::create($request->all());

        return redirect()->route('services.index')->with('success', 'Jasa berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        if ($response = $this->checkIzin('akses_edit_jasa')) {
            return $response;
        }

        $units = Unit::orderBy('name')->get();
        return view('services.edit', compact('service', 'units'));
    }

    public function update(Request $request, Service $service)
    {
        if ($response = $this->checkIzin('akses_edit_jasa')) {
            return $response;
        }

        $request->validate([
            'barcode'                => 'nullable|unique:services,barcode,' . $service->id,
            'code'                   => 'required|unique:services,code,' . $service->id,
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'main_unit_id'           => 'required|exists:units,id',
            'purchase_price_main'    => 'required|numeric|min:0',
            'selling_price_main'     => 'nullable|numeric|min:0',
        ]);

        $service->update($request->all());

        return redirect()->route('services.index')->with('success', 'Jasa berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        if ($response = $this->checkIzin('akses_hapus_jasa')) {
            return $response;
        }
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Jasa berhasil dihapus.');
    }
}

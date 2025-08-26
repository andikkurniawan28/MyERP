<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_kontak')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Contact::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('payable', function ($row) {
                    $payable = $row->payable();
                    return $payable == 0 ? '-' : number_format($payable, 0, ',', '.'); // format lokal
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_edit_kontak) {
                        $editUrl = route('contacts.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_kontak) {
                        $deleteUrl = route('contacts.destroy', $row->id);
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

        return view('contacts.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_kontak')) {
            return $response;
        }

        return view('contacts.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_kontak')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'prefix'          => 'required|string|max:255',
        ]);

        Contact::create($request->all());

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function edit(Contact $contact)
    {
        if ($response = $this->checkIzin('akses_edit_kontak')) {
            return $response;
        }

        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        if ($response = $this->checkIzin('akses_edit_kontak')) {
            return $response;
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'prefix'          => 'required|string|max:255',
        ]);

        $contact->update($request->all());

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Contact $contact)
    {
        if ($response = $this->checkIzin('akses_hapus_kontak')) {
            return $response;
        }

        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil dihapus.');
    }
}

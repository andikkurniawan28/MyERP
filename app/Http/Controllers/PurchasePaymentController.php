<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PurchasePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_pelunasan_hutang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = PurchasePayment::query()->with('contact', 'user');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->user ? $row->user->name : '-')
                ->addColumn('contact', fn($row) => $row->contact ? $row->contact->name : '-')
                ->editColumn('grand_total', function ($row) {
                    return number_format($row->grand_total, 0, ',', '.'); // Format lokal Indonesia
                })
                ->editColumn('date', function ($row) {
                    $carbon = Carbon::parse($row->date)->locale('id');
                    return $carbon->translatedFormat('l, d/m/Y');
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';

                    // Hak akses detail purchase
                    if (Auth()->user()->role->akses_daftar_pelunasan_hutang) {
                        $showUrl = route('purchasePayments.show', $row->id);
                        $buttons .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info">Detail</a>';
                    }

                    // Hak akses hapus purchase
                    if (Auth()->user()->role->akses_hapus_pelunasan_hutang) {
                        $deleteUrl = route('purchasePayments.destroy', $row->id);
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

        return view('purchasePayments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchasePayment $purchasePayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchasePayment $purchasePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchasePayment $purchasePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchasePayment $purchasePayment)
    {
        if ($response = $this->checkIzin('akses_hapus_pelunasan_hutang')) {
            return $response;
        }

        $purchasePayment->delete();

        return redirect()->route('purchasePayments.index')
            ->with('success', 'Pelunasan Hutang dihapus.');
    }
}

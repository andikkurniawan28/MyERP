<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Purchase::query()->with('contact');

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
                    $showUrl = route('purchases.show', $row->id);
                    $deleteUrl = route('purchases.destroy', $row->id);
                    return '
                        <div class="btn-group">
                            <a href="'.$showUrl.'" class="btn btn-sm btn-info">Detail</a>
                            <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Hapus data ini?\')" style="display:inline-block;">
                                '.csrf_field().method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('purchases.index');
    }

    public function create()
    {
        $code = Purchase::generateCode();
        $warehouses = Warehouse::all();
        $contacts = Contact::where('type', 'supplier')->get();
        $items = Item::all();
        return view('purchases.create', compact('contacts', 'items', 'warehouses', 'code'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:purchases,code',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'contact_id' => 'required|exists:contacts,id',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'price.*' => 'required|numeric|min:0',
            'discount_percent.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = $this->createPurchase($request);
            $this->createPurchaseDetails($purchase, $request);
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['contact', 'details.item']);
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dihapus.');
    }

    protected function createPurchase(Request $request)
    {
        // Hitung subtotal, discount, tax, grand_total
        $subtotal = 0;
        $discount = 0;
        foreach ($request->item_id as $i => $itemId) {
            $line_total = $request->qty[$i] * $request->price[$i];
            $line_discount = (($request->discount_percent[$i] ?? 0) / 100) * $line_total;
            $subtotal += $line_total;
            $discount += $line_discount;
        }

        $tax = ($request->tax_percent ?? 0) / 100 * ($subtotal - $discount);
        $grand_total = $subtotal - $discount + $tax + ($request->freight ?? 0) + ($request->expense ?? 0);

        return Purchase::create([
            // 'type' => $request->type,
            'code' => $request->code,
            'date' => $request->date,
            'warehouse_id' => $request->warehouse_id,
            'contact_id' => $request->contact_id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_percent' => $request->tax_percent ?? 0,
            'freight' => $request->freight ?? 0,
            'expense' => $request->expense ?? 0,
            'discount_percent' => 0,
            'discount' => $discount,
            'grand_total' => $grand_total,
            'user_id' => Auth::id(),
        ]);
    }

    protected function createPurchaseDetails(Purchase $purchase, Request $request)
    {
        foreach ($request->item_id as $i => $itemId) {
            $line_total = $request->qty[$i] * $request->price[$i];
            $line_discount = (($request->discount_percent[$i] ?? 0) / 100) * $line_total;

            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'item_id' => $itemId,
                'qty' => $request->qty[$i],
                'price' => $request->price[$i],
                'discount_percent' => $request->discount_percent[$i] ?? 0,
                'discount' => $line_discount,
                'total' => $line_total - $line_discount,
            ]);
        }
    }
}

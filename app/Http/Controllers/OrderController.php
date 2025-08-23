<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Contact;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::query()->with('contact');

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
                    $showUrl = route('orders.show', $row->id);
                    $deleteUrl = route('orders.destroy', $row->id);
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

        return view('orders.index');
    }

    public function create()
    {
        // $code = $this->generateCode();
        $contacts = Contact::all();
        $items = Item::all();
        return view('orders.create', compact('contacts', 'items'));
    }

    public function store(Request $request)
    {
        $request->request->add([
            'code' => Order::generateCode($request->type),
            // 'user_id' => Auth::id(),
        ]);

        $request->validate([
            'type' => 'required|in:purchase_quotation,sales_quotation,purchase_order,sales_order,delivery_order',
            'code' => 'required|string|unique:orders,code',
            'date' => 'required|date',
            'contact_id' => 'required|exists:contacts,id',
            // 'currency_code' => 'required|string|size:3',
            // 'status' => 'required|in:draft,confirmed,approved,invoiced,closed,canceled',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'price.*' => 'required|numeric|min:0',
            'discount_percent.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $order = $this->createOrder($request);
            $this->createOrderDetails($order, $request);
        });

        return redirect()->route('orders.index')->with('success', 'Order berhasil disimpan.');
    }

    public function show(Order $order)
    {
        $order->load(['contact', 'details.item']);
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
    }

    protected function createOrder(Request $request)
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

        return Order::create([
            'type' => $request->type,
            'code' => $request->code,
            'date' => $request->date,
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
            // 'currency_code' => $request->currency_code,
            // 'status' => $request->status,
        ]);
    }

    protected function createOrderDetails(Order $order, Request $request)
    {
        foreach ($request->item_id as $i => $itemId) {
            $line_total = $request->qty[$i] * $request->price[$i];
            $line_discount = (($request->discount_percent[$i] ?? 0) / 100) * $line_total;

            OrderDetail::create([
                'order_id' => $order->id,
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

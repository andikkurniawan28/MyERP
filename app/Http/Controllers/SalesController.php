<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ItemTransaction;
use App\Models\ItemTransactionDetail;
use App\Models\Journal;
use App\Models\Setting;
use App\Models\Sales;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\SalesDetail;
use App\Models\SalesPayment;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesPaymentDetail;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_penjualan')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Sales::query()->with('contact', 'user');

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

                    // Hak akses detail sales
                    if (Auth()->user()->role->akses_daftar_penjualan) {
                        $showUrl = route('sales.show', $row->id);
                        $buttons .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info">Detail</a>';
                    }

                    // Hak akses hapus sales
                    if (Auth()->user()->role->akses_hapus_penjualan) {
                        $deleteUrl = route('sales.destroy', $row->id);
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

        return view('sales.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_penjualan')) {
            return $response;
        }

        $code = Sales::generateCode();
        $warehouses = Warehouse::all();
        $contacts = Contact::where('type', '!=', 'supplier')->get();
        $items = Item::all();
        $payment_gateways = Account::where('is_payment_gateway', 1)->get();
        return view('sales.create', compact('contacts', 'items', 'warehouses', 'code', 'payment_gateways'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_penjualan')) {
            return $response;
        }

        $request->validate([
            'code' => 'required|string|unique:sales,code',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'contact_id' => 'required|exists:contacts,id',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'price.*' => 'required|numeric|min:0',
            'discount_percent.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $sales = $this->createSales($request);
            $this->createJournal($sales, $request);
            $itemTransaction = $this->createItemTransaction($sales, $request);
            $this->createSalesDetails($sales, $request, $itemTransaction);
            $this->paySales($sales, $request);
        });

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Sales $sale)
    {
        if ($response = $this->checkIzin('akses_daftar_penjualan')) {
            return $response;
        }
        $sale->load(['contact', 'details.item', 'payments.salesPayment']);
        return view('sales.show', compact('sale'));
    }

    public function destroy(Sales $sale)
    {
        if ($response = $this->checkIzin('akses_hapus_penjualan')) {
            return $response;
        }

        // Cek apakah sales punya payments
        if ($sale->payments()->exists()) {
            return redirect()->route('sales.index')
                ->with('error', 'Penjualan tidak bisa dihapus karena sudah ada pembayaran.');
        }

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil dihapus.');
    }

    protected function createSales(Request $request)
    {
        $subtotal = floatval($request->subtotal ?? 0);
        $discount = floatval($request->discount_header ?? 0);
        $discount_percent = $subtotal > 0 ? ($discount / $subtotal) * 100 : 0;
        $sales = Sales::create([
            'code' => $request->code,
            'date' => $request->date,
            'warehouse_id' => $request->warehouse_id,
            'contact_id' => $request->contact_id,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_percent' => $discount_percent,
            'tax' => $request->tax,
            'tax_percent' => $request->tax_percent ?? 0,
            'freight' => $request->freight ?? 0,
            'expense' => $request->expense ?? 0,
            'grand_total' => $request->grand_total,
            'user_id' => Auth::id(),
        ]);
        return $sales;
    }

    protected function createSalesDetails(Sales $sales, Request $request, ItemTransaction $itemTransaction)
    {
        foreach ($request->item_id as $i => $itemId) {
            SalesDetail::create([
                'sales_id' => $sales->id,
                'item_id' => $itemId,
                'qty' => $request->qty[$i],
                'price' => $request->price[$i],
                'discount_percent' => $request->discount_percent[$i] ?? 0,
                'discount' => $request->discount[$i] ?? 0,
                'total' => $request->total[$i] ?? 0,
            ]);
            ItemTransactionDetail::create([
                'item_transaction_id' => $itemTransaction->id,
                // 'warehouse_id' => $sales->warehouse_id,
                'item_id' => $itemId,
                'out' => $request->qty[$i],
            ]);
            // Item::whereId($itemId)->update(['sales_price_main' => $request->price[$i]]);
        }
    }

    protected function paySales(Sales $sales, Request $request)
    {
        $setting = Setting::get()->first();

        if ($request->payment_amount > 0) {
            $payment = SalesPayment::create([
                'code' => SalesPayment::generateCode(),
                'account_id' => $request->account_id,
                'date' => $request->date,
                'grand_total' => $request->payment_amount,
                'currency' => 'IDR',
                'contact_id' => $request->contact_id,
                'user_id' => Auth::id(),
            ]);

            SalesPaymentDetail::create([
                'sales_payment_id' => $payment->id,
                'sales_id' => $sales->id,
                'total' => $request->payment_amount,
            ]);

            // 🔹 Update kolom paid & remaining di sales
            $sales->paid += $request->payment_amount;
            $sales->remaining = $sales->grand_total - $sales->paid;

            // 🔹 Update status sesuai kondisi pembayaran
            if ($sales->paid == 0) {
                $sales->status = 'Menunggu Pembayaran';
                $sales->remaining = $sales->grand_total;
            } elseif ($sales->paid < $sales->grand_total) {
                $sales->status = 'Belum Tuntas';
            } elseif ($sales->paid >= $sales->grand_total) {
                $sales->status = 'Lunas';
            }

            $sales->save();

            $journal = Journal::create([
                'date' => $request->date,
                'description' => "Pelunasan Piutang oleh {$sales->contact->name}",
                'debit' => $request->payment_amount,
                'credit' => $request->payment_amount,
                'user_id' => Auth::id(),
                'code' => Journal::generateCode(),
                'sales_payment_id' => $payment->id,
            ]);

            $details = [
                ['journal_id' => $journal->id, 'account_id' => $request->account_id, 'debit' => $request->payment_amount, 'credit' => 0],
                ['journal_id' => $journal->id, 'account_id' => $setting->sales_grand_total_account_id, 'debit' => 0, 'credit' => $request->payment_amount],
            ];

            // filter hanya yang ada debit/kredit > 0
            $details = array_filter($details, function ($row) {
                return ($row['debit'] ?? 0) > 0 || ($row['credit'] ?? 0) > 0;
            });

            // insert
            JournalDetail::insert($details);
        } else {
            $sales->remaining = $sales->grand_total;
            $sales->save();
        }

    }

    protected function createJournal(Sales $sales, Request $request)
    {
        $setting = Setting::get()->first();

        $cogs = 0;
        foreach ($request->item_id as $i => $itemId) {
            $item = Item::whereId($itemId)->get()->last();
            $cogs += $request->qty[$i] * $item->purchase_price_main;
        }
        // dd($cogs);

        $journalPenjualan = Journal::create([
            'date' => $request->date,
            'description' => "Penjualan {$sales->code}",
            'debit' => $request->grand_total,
            'credit' => $request->grand_total,
            'user_id' => Auth::id(),
            'code' => Journal::generateCode(),
            'sales_id' => $sales->id,
        ]);

        $journalHPP = Journal::create([
            'date' => $request->date,
            'description' => "HPP {$sales->code}",
            'debit' => $cogs,
            'credit' => $cogs,
            'user_id' => Auth::id(),
            'code' => Journal::generateCode(),
            'sales_id' => $sales->id,
        ]);

        $details = [
            ['journal_id' => $journalPenjualan->id, 'account_id' => $setting->sales_subtotal_account_id, 'credit' => $sales->subtotal - $sales->discount, 'debit' => 0],
            ['journal_id' => $journalPenjualan->id, 'account_id' => $setting->sales_tax_account_id, 'credit' => $sales->tax, 'debit' => 0],
            ['journal_id' => $journalPenjualan->id, 'account_id' => $setting->sales_freight_account_id, 'credit' => $sales->freight, 'debit' => 0],
            ['journal_id' => $journalPenjualan->id, 'account_id' => $setting->sales_expenses_account_id, 'credit' => $sales->expense, 'debit' => 0],
            // ['journal_id' => $journal->id, 'account_id' => $setting->sales_discount_account_id, 'debit' => 0, 'credit' => $sales->discount],
            ['journal_id' => $journalPenjualan->id, 'account_id' => $setting->sales_grand_total_account_id, 'debit' => $sales->grand_total, 'credit' => 0],
            ['journal_id' => $journalHPP->id, 'account_id' => $setting->sales_cogs_account_id, 'debit' => $cogs, 'credit' => 0],
            ['journal_id' => $journalHPP->id, 'account_id' => $setting->inventory_account_id, 'debit' => 0, 'credit' => $cogs],
        ];

        $details2 = [

        ];

        // filter hanya yang ada debit/kredit > 0
        $details = array_filter($details, function ($row) {
            return ($row['debit'] ?? 0) > 0 || ($row['credit'] ?? 0) > 0;
        });

        // insert
        JournalDetail::insert($details);
    }

    protected function createItemTransaction(Sales $sales, Request $request)
    {
        $itemTransaction = ItemTransaction::create([
            'date' => $request->date,
            'description' => "Penjualan {$sales->code}",
            'warehouse_id' => $request->warehouse_id,
            'user_id' => Auth::id(),
            'code' => ItemTransaction::generateCode(),
            'sales_id' => $sales->id,
        ]);
        return $itemTransaction;
    }
}

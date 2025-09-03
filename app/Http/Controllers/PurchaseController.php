<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Setting;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\PurchaseDetail;
use App\Models\ItemTransaction;
use App\Models\PurchasePayment;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemTransactionDetail;
use App\Models\PurchasePaymentDetail;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_pembelian')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Purchase::query()->with('contact', 'branch');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('branch', fn($row) => $row->branch ? $row->branch->name : '-')
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
                    if (Auth()->user()->role->akses_daftar_pembelian) {
                        $showUrl = route('purchases.show', $row->id);
                        $buttons .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info">Detail</a>';
                    }

                    // Hak akses hapus purchase
                    if (Auth()->user()->role->akses_hapus_pembelian) {
                        $deleteUrl = route('purchases.destroy', $row->id);
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

        return view('purchases.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_pembelian')) {
            return $response;
        }

        $code = Purchase::generateCode();
        $branches = Branch::all();
        $warehouses = Warehouse::all();
        $contacts = Contact::where('type', 'supplier')->get();
        $items = Item::all();
        $payment_gateways = Account::where('is_payment_gateway', 1)->get();
        return view('purchases.create', compact('contacts', 'items', 'branches', 'warehouses', 'code', 'payment_gateways'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_pembelian')) {
            return $response;
        }

        $request->validate([
            'code' => 'required|string|unique:purchases,code',
            'date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'contact_id' => 'required|exists:contacts,id',
            'item_id.*' => 'required|exists:items,id',
            'qty.*' => 'required|numeric|min:0.01',
            'price.*' => 'required|numeric|min:0',
            'discount_percent.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = $this->createPurchase($request);
            $this->createJournal($purchase, $request);
            $itemTransaction = $this->createItemTransaction($purchase, $request);
            $this->createPurchaseDetails($purchase, $request, $itemTransaction);
            $this->payPurchase($purchase, $request);
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan.');
    }

    public function show(Purchase $purchase)
    {
        if ($response = $this->checkIzin('akses_daftar_pembelian')) {
            return $response;
        }
        $purchase->load(['contact', 'details.item', 'payments.purchasePayment', 'branch']);
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        if ($response = $this->checkIzin('akses_hapus_pembelian')) {
            return $response;
        }

        // Cek apakah purchase punya payments
        if ($purchase->payments()->exists()) {
            return redirect()->route('purchases.index')
                ->with('error', 'Pembelian tidak bisa dihapus karena sudah ada pembayaran.');
        }

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian berhasil dihapus.');
    }

    protected function createPurchase(Request $request)
    {
        $subtotal = floatval($request->subtotal ?? 0);
        $discount = floatval($request->discount_header ?? 0);
        $discount_percent = $subtotal > 0 ? ($discount / $subtotal) * 100 : 0;
        $purchase = Purchase::create([
            'code' => $request->code,
            'date' => $request->date,
            'branch_id' => $request->branch_id,
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
        return $purchase;
    }

    protected function createPurchaseDetails(Purchase $purchase, Request $request, ItemTransaction $itemTransaction)
    {
        foreach ($request->item_id as $i => $itemId) {
            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'item_id' => $itemId,
                'qty' => $request->qty[$i],
                'price' => $request->price[$i],
                'discount_percent' => $request->discount_percent[$i] ?? 0,
                'discount' => $request->discount[$i] ?? 0,
                'total' => $request->total[$i] ?? 0,
            ]);

            // hanya buat transaksi stok kalau barang countable
            $item = Item::findOrFail($itemId);
            if ($item && $item->is_countable) {
                ItemTransactionDetail::create([
                    'item_transaction_id' => $itemTransaction->id,
                    'item_id' => $itemId,
                    'in' => $request->qty[$i],
                ]);
            }

            Item::whereId($itemId)->update(['purchase_price_main' => $request->price[$i]]);
        }
    }

    protected function payPurchase(Purchase $purchase, Request $request)
    {
        $setting = Setting::get()->first();

        if ($request->payment_amount > 0) {
            $payment = PurchasePayment::create([
                'code' => PurchasePayment::generateCode(),
                'account_id' => $request->account_id,
                'date' => $request->date,
                'grand_total' => $request->payment_amount,
                'currency' => 'IDR',
                'contact_id' => $request->contact_id,
                'user_id' => Auth::id(),
            ]);

            PurchasePaymentDetail::create([
                'purchase_payment_id' => $payment->id,
                'purchase_id' => $purchase->id,
                'total' => $request->payment_amount,
            ]);

            // 🔹 Update kolom paid & remaining di purchase
            $purchase->paid += $request->payment_amount;
            $purchase->remaining = $purchase->grand_total - $purchase->paid;

            // 🔹 Update status sesuai kondisi pembayaran
            if ($purchase->paid == 0) {
                $purchase->status = 'Menunggu Pembayaran';
                $purchase->remaining = $purchase->grand_total;
            } elseif ($purchase->paid < $purchase->grand_total) {
                $purchase->status = 'Belum Tuntas';
            } elseif ($purchase->paid >= $purchase->grand_total) {
                $purchase->status = 'Lunas';
            }

            $purchase->save();

            $journal = Journal::create([
                'date' => $request->date,
                'description' => "Pelunasan Hutang oleh {$purchase->contact->name}",
                'debit' => $request->payment_amount,
                'credit' => $request->payment_amount,
                'user_id' => Auth::id(),
                'code' => Journal::generateCode(),
                'purchase_payment_id' => $payment->id,
            ]);

            $details = [
                ['journal_id' => $journal->id, 'account_id' => $request->account_id, 'credit' => $request->payment_amount, 'debit' => 0],
                ['journal_id' => $journal->id, 'account_id' => $setting->purchase_grand_total_account_id, 'credit' => 0, 'debit' => $request->payment_amount],
            ];

            // filter hanya yang ada debit/kredit > 0
            $details = array_filter($details, function ($row) {
                return ($row['debit'] ?? 0) > 0 || ($row['credit'] ?? 0) > 0;
            });

            // insert
            JournalDetail::insert($details);
        } else {
            $purchase->remaining = $purchase->grand_total;
            $purchase->save();
        }

    }

    protected function createJournal(Purchase $purchase, Request $request)
    {
        $setting = Setting::get()->first();

        $journal = Journal::create([
            'date' => $request->date,
            'description' => "Pembelian {$purchase->code}",
            'debit' => $request->grand_total,
            'credit' => $request->grand_total,
            'user_id' => Auth::id(),
            'code' => Journal::generateCode(),
            'purchase_id' => $purchase->id,
        ]);

        $details = [
            ['journal_id' => $journal->id, 'account_id' => $setting->purchase_subtotal_account_id, 'debit' => $purchase->subtotal - $purchase->discount, 'credit' => 0],
            ['journal_id' => $journal->id, 'account_id' => $setting->purchase_tax_account_id, 'debit' => $purchase->tax, 'credit' => 0],
            ['journal_id' => $journal->id, 'account_id' => $setting->purchase_freight_account_id, 'debit' => $purchase->freight, 'credit' => 0],
            ['journal_id' => $journal->id, 'account_id' => $setting->purchase_expenses_account_id, 'debit' => $purchase->expense, 'credit' => 0],
            // ['journal_id' => $journal->id, 'account_id' => $setting->purchase_discount_account_id, 'debit' => 0, 'credit' => $purchase->discount],
            ['journal_id' => $journal->id, 'account_id' => $setting->purchase_grand_total_account_id, 'debit' => 0, 'credit' => $purchase->grand_total],
        ];

        // filter hanya yang ada debit/kredit > 0
        $details = array_filter($details, function ($row) {
            return ($row['debit'] ?? 0) > 0 || ($row['credit'] ?? 0) > 0;
        });

        // insert
        JournalDetail::insert($details);
    }

    protected function createItemTransaction(Purchase $purchase, Request $request)
    {
        $itemTransaction = ItemTransaction::create([
            'date' => $request->date,
            'description' => "Pembelian {$purchase->code}",
            'warehouse_id' => $request->warehouse_id,
            'user_id' => Auth::id(),
            'code' => ItemTransaction::generateCode(),
            'purchase_id' => $purchase->id,
        ]);
        return $itemTransaction;
    }
}

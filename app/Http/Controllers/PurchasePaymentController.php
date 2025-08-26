<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Setting;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\PurchasePayment;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\PurchasePaymentDetail;

class PurchasePaymentController extends Controller
{

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

    public function create($contact_id)
    {
        if ($response = $this->checkIzin('akses_tambah_pelunasan_hutang')) {
            return $response;
        }

        $code = PurchasePayment::generateCode();
        $payment_gateways = Account::where('is_payment_gateway', 1)->get();
        $purchases = Contact::purchaseNotPaid($contact_id);
        return view('purchasePayments.create', compact('code', 'payment_gateways', 'purchases'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_pelunasan_hutang')) {
            return $response;
        }

        $setting = Setting::get()->first();

        DB::beginTransaction();
        try {
            // 1. Simpan PurchasePayment (HEADER)
            $payment = PurchasePayment::create([
                'code'        => $request->code,
                'date'        => $request->date,
                'contact_id'  => $request->contact_id,
                'account_id'  => $request->account_id,
                'grand_total' => $request->grand_total,
                'user_id'     => auth()->id(),
                'currency'    => 'IDR',
            ]);

            // 2. Simpan Detail per Hutang (BODY)
            foreach ($request->details as $d) {
                PurchasePaymentDetail::create([
                    'purchase_payment_id' => $payment->id,
                    'purchase_id'         => $d['purchase_id'],
                    'total'               => $d['total'],
                ]);

                // update sisa hutang di Purchase
                $purchase = Purchase::find($d['purchase_id']);
                $purchase->paid += $d['total'];
                $purchase->remaining = $purchase->grand_total - $purchase->paid;

                // update status
                if ($purchase->remaining <= 0) {
                    $purchase->status = 'Lunas';
                    $purchase->remaining = 0; // biar tidak minus
                } else {
                    $purchase->status = 'Belum Tuntas';
                }

                $purchase->save();
            }

            // 3. Buat Journal (HEADER)
            $journal = Journal::create([
                'purchase_payment_id' => $payment->id,
                'code'        => Journal::generateCode(),
                'date'        => $request->date,
                'description' => 'Pelunasan hutang pembelian '.$payment->code,
                'debit'       => $payment->grand_total,
                'credit'      => $payment->grand_total,
                'user_id'     => auth()->id(),
            ]);

            // 4. Buat JournalDetail (DEBET KREDIT)
            // Debit → Hutang berkurang
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $setting->purchase_grand_total_account_id, // Hutang Usaha
                'debit'      => $payment->grand_total,
                'credit'     => 0,
            ]);

            // Kredit → Kas/Bank berkurang sesuai metode
            $accountCashBank = $request->account_id;

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $accountCashBank,
                'debit'      => 0,
                'credit'     => $payment->grand_total,
            ]);

            DB::commit();
            return redirect()->route('purchasePayments.index')->with('success', "Pelunasan Hutang berhasil.");

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show(PurchasePayment $purchasePayment)
    {
        if ($response = $this->checkIzin('akses_daftar_pelunasan_hutang')) {
            return $response;
        }

        $purchasePayment->load([
            'contact',
            'details.purchase',
            'details.account',
        ]);

        return view('purchasePayments.show', compact('purchasePayment'));
    }

    public function destroy(PurchasePayment $purchasePayment)
    {
        if ($response = $this->checkIzin('akses_hapus_pelunasan_hutang')) {
            return $response;
        }

        DB::beginTransaction();
        try {
            // Pastikan relasi ter-load: details -> purchase
            $purchasePayment->load('details.purchase');

            foreach ($purchasePayment->details as $detail) {
                $purchase = $detail->purchase;
                if (! $purchase) {
                    continue; // safety
                }

                // Kurangi paid sesuai nilai di detail (kolom 'total' sesuai schema)
                $purchase->paid = max(0, $purchase->paid - $detail->total);

                // Hitung ulang remaining
                $purchase->remaining = $purchase->grand_total - $purchase->paid;
                if ($purchase->remaining < 0) {
                    $purchase->remaining = 0;
                }

                // Update status sesuai schema: Menunggu Pembayaran, Belum Tuntas, Lunas
                if ($purchase->paid <= 0) {
                    $purchase->status = 'Menunggu Pembayaran';
                } elseif ($purchase->remaining <= 0) {
                    $purchase->status = 'Lunas';
                } else {
                    $purchase->status = 'Belum Tuntas';
                }

                $purchase->save();
            }

            // kalau FK journals.purchase_payment_id onDelete cascade sudah ada,
            // maka journal & journal_details akan terhapus otomatis.
            // Jika belum yakin, bisa uncomment baris di bawah untuk hapus manual:
            // \App\Models\Journal::where('purchase_payment_id', $purchasePayment->id)->delete();

            $purchasePayment->delete();

            DB::commit();

            return redirect()->route('purchasePayments.index')
                ->with('success', 'Pelunasan Hutang dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Journal;
use App\Models\Setting;
use App\Models\Sales;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\SalesPayment;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\SalesPaymentDetail;

class SalesPaymentController extends Controller
{

    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_pelunasan_piutang')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = SalesPayment::query()->with('contact', 'user');

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
                    if (Auth()->user()->role->akses_daftar_pelunasan_piutang) {
                        $showUrl = route('salesPayments.show', $row->id);
                        $buttons .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info">Detail</a>';
                    }

                    // Hak akses hapus sales
                    if (Auth()->user()->role->akses_hapus_pelunasan_piutang) {
                        $deleteUrl = route('salesPayments.destroy', $row->id);
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

        return view('salesPayments.index');
    }

    public function create($contact_id)
    {
        if ($response = $this->checkIzin('akses_tambah_pelunasan_piutang')) {
            return $response;
        }

        $code = SalesPayment::generateCode();
        $payment_gateways = Account::where('is_payment_gateway', 1)->get();
        $saless = Contact::salesNotPaid($contact_id);
        return view('salesPayments.create', compact('code', 'payment_gateways', 'saless'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_pelunasan_piutang')) {
            return $response;
        }

        $setting = Setting::get()->first();

        DB::beginTransaction();
        try {
            // 1. Simpan SalesPayment (HEADER)
            $payment = SalesPayment::create([
                'code'        => $request->code,
                'date'        => $request->date,
                'contact_id'  => $request->contact_id,
                'account_id'  => $request->account_id,
                'grand_total' => $request->grand_total,
                'user_id'     => auth()->id(),
                'currency'    => 'IDR',
            ]);

            // 2. Simpan Detail per Piutang (BODY)
            foreach ($request->details as $d) {
                SalesPaymentDetail::create([
                    'sales_payment_id' => $payment->id,
                    'sales_id'         => $d['sales_id'],
                    'total'               => $d['total'],
                ]);

                // update sisa piutang di Sales
                $sales = Sales::find($d['sales_id']);
                $sales->paid += $d['total'];
                $sales->remaining = $sales->grand_total - $sales->paid;

                // update status
                if ($sales->remaining <= 0) {
                    $sales->status = 'Lunas';
                    $sales->remaining = 0; // biar tidak minus
                } else {
                    $sales->status = 'Belum Tuntas';
                }

                $sales->save();
            }

            // 3. Buat Journal (HEADER)
            $journal = Journal::create([
                'sales_payment_id' => $payment->id,
                'code'        => Journal::generateCode(),
                'date'        => $request->date,
                'description' => 'Pelunasan piutang penjualan '.$payment->code,
                'debit'       => $payment->grand_total,
                'credit'      => $payment->grand_total,
                'user_id'     => auth()->id(),
            ]);

            // 4. Buat JournalDetail (DEBET KREDIT)
            // Debit → Piutang berkurang
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $setting->sales_grand_total_account_id, // Piutang Usaha
                'credit'      => $payment->grand_total,
                'debit'     => 0,
            ]);

            // Kredit → Kas/Bank berkurang sesuai metode
            $accountCashBank = $request->account_id;

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $accountCashBank,
                'credit'      => 0,
                'debit'     => $payment->grand_total,
            ]);

            DB::commit();
            return redirect()->route('salesPayments.index')->with('success', "Pelunasan Piutang berhasil.");

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show(SalesPayment $salesPayment)
    {
        if ($response = $this->checkIzin('akses_daftar_pelunasan_piutang')) {
            return $response;
        }

        $salesPayment->load([
            'contact',
            'details.sales',
            'details.account',
        ]);

        return view('salesPayments.show', compact('salesPayment'));
    }

    public function destroy(SalesPayment $salesPayment)
    {
        if ($response = $this->checkIzin('akses_hapus_pelunasan_piutang')) {
            return $response;
        }

        DB::beginTransaction();
        try {
            // Pastikan relasi ter-load: details -> sales
            $salesPayment->load('details.sales');

            foreach ($salesPayment->details as $detail) {
                $sales = $detail->sales;
                if (! $sales) {
                    continue; // safety
                }

                // Kurangi paid sesuai nilai di detail (kolom 'total' sesuai schema)
                $sales->paid = max(0, $sales->paid - $detail->total);

                // Hitung ulang remaining
                $sales->remaining = $sales->grand_total - $sales->paid;
                if ($sales->remaining < 0) {
                    $sales->remaining = 0;
                }

                // Update status sesuai schema: Menunggu Pembayaran, Belum Tuntas, Lunas
                if ($sales->paid <= 0) {
                    $sales->status = 'Menunggu Pembayaran';
                } elseif ($sales->remaining <= 0) {
                    $sales->status = 'Lunas';
                } else {
                    $sales->status = 'Belum Tuntas';
                }

                $sales->save();
            }

            // kalau FK journals.sales_payment_id onDelete cascade sudah ada,
            // maka journal & journal_details akan terhapus otomatis.
            // Jika belum yakin, bisa uncomment baris di bawah untuk hapus manual:
            // \App\Models\Journal::where('sales_payment_id', $salesPayment->id)->delete();

            $salesPayment->delete();

            DB::commit();

            return redirect()->route('salesPayments.index')
                ->with('success', 'Pelunasan Piutang dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


}

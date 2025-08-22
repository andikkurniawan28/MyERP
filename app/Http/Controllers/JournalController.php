<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_jurnal')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Journal::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    return $row->user ? $row->user->name : '-';
                })
                ->editColumn('date', function ($row) {
                    $carbon = Carbon::parse($row->date)->locale('id');
                    $row->date = $carbon->translatedFormat('l, d/m/Y');
                    return $row->date;
                })
                ->editColumn('debit', function ($row) {
                    return number_format($row->debit, 0, ',', '.'); // Format lokal Indonesia
                })
                ->editColumn('credit', function ($row) {
                    return number_format($row->credit, 0, ',', '.'); // Format lokal Indonesia
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_daftar_jurnal) {
                        $editUrl = route('journals.show', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info">Detail</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_jurnal) {
                        $deleteUrl = route('journals.destroy', $row->id);
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

        return view('journals.index');
    }

    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_jurnal')) {
            return $response;
        }
        $code = Journal::generateCode();
        $accounts = Account::all();
        return view('journals.create', compact('accounts', 'code'));
    }

    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_jurnal')) {
            return $response;
        }

        $request->validate([
            'date' => 'required|date',
            'description' => 'required',
            'code' => 'required',
            'account_id.*' => 'required|exists:accounts,id',
            'debit.*' => 'nullable|numeric',
            'credit.*' => 'nullable|numeric',
        ]);

        // Validasi: setiap journal detail hanya boleh satu antara debit/credit
        foreach ($request->debit ?? [] as $i => $debit) {
            $credit = $request->credit[$i] ?? null;

            if (($debit && $credit) || (!$debit && !$credit)) {
                return redirect()->back()
                    ->withInput()
                    ->with('failed', "Baris ke-".($i+1)." harus diisi salah satu antara debit atau credit, tidak boleh keduanya!");
            }
        }

        $totalDebit  = array_sum($request->debit ?? []);
        $totalCredit = array_sum($request->credit ?? []);

        // Validasi balance
        if ($totalDebit != $totalCredit) {
            return redirect()->back()
                ->withInput()
                ->with('failed', 'Debit & Credit tidak balance!');
        }

        DB::transaction(function () use ($request, $totalDebit, $totalCredit) {
            $journal = Journal::create([
                'date' => $request->date,
                'description' => $request->description,
                'debit' => $totalDebit,
                'credit' => $totalCredit,
                'user_id' => Auth::id(),
                'code' => $request->code,
            ]);

            foreach ($request->account_id as $i => $accountId) {
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $accountId,
                    'debit' => $request->debit[$i] ?? 0,
                    'credit' => $request->credit[$i] ?? 0,
                ]);
            }
        });

        return redirect()->route('journals.index')->with('success', 'Jurnal berhasil dibuat.');
    }

    public function show(Journal $journal)
    {
        if ($response = $this->checkIzin('akses_daftar_jurnal')) {
            return $response;
        }

        $journal->load(['details.account']);

        return view('journals.show', compact('journal'));
    }

    public function destroy(Journal $journal)
    {
        if ($response = $this->checkIzin('akses_hapus_jurnal')) {
            return $response;
        }

        $journal->delete();
        return redirect()->route('journals.index')->with('success', 'Jurnal berhasil dihapus.');
    }
}

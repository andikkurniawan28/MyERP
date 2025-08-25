<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($response = $this->checkIzin('akses_daftar_akun')) {
            return $response;
        }

        if ($request->ajax()) {
            $data = Account::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('saldo', function ($row) {
                    $saldo = $row->saldo();
                    return $saldo == 0 ? '-' : number_format($saldo, 0, ',', '.'); // format lokal
                })
                ->addColumn('action', function ($row) {
                    $buttons = '<div class="btn-group" role="group">';
                    if (Auth()->user()->role->akses_edit_akun) {
                        $editUrl = route('accounts.edit', $row->id);
                        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>';
                    }
                    if (Auth()->user()->role->akses_hapus_akun) {
                        $deleteUrl = route('accounts.destroy', $row->id);
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

        return view('accounts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($response = $this->checkIzin('akses_tambah_akun')) {
            return $response;
        }

        $categories = Account::getCategory();
        $normalBalances = Account::getNormalBalance();

        return view('accounts.create', compact('categories', 'normalBalances'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($response = $this->checkIzin('akses_tambah_akun')) {
            return $response;
        }

        $request->validate([
            'code'           => 'required|string|max:255|unique:accounts,code',
            'name'           => 'required|string|max:255|unique:accounts,name',
            'category'       => 'required|in:asset,liability,equity,revenue,expense',
            'description'    => 'required|string',
            'normal_balance' => 'required|in:debit,credit',
            'is_payment_gateway' => 'required',
        ]);

        Account::create($request->only(['code', 'name', 'category', 'description', 'normal_balance', 'is_payment_gateway']));

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        if ($response = $this->checkIzin('akses_edit_akun')) {
            return $response;
        }

        $categories = Account::getCategory();
        $normalBalances = Account::getNormalBalance();

        return view('accounts.edit', compact('account', 'categories', 'normalBalances'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        if ($response = $this->checkIzin('akses_edit_akun')) {
            return $response;
        }

        $request->validate([
            'code'           => 'required|string|max:255|unique:accounts,code,' . $account->id,
            'name'           => 'required|string|max:255|unique:accounts,name,' . $account->id,
            'category'       => 'required|in:asset,liability,equity,revenue,expense',
            'description'    => 'required|string',
            'normal_balance' => 'required|in:debit,credit',
            'is_payment_gateway' => 'required',
        ]);

        $account->update($request->only(['code', 'name', 'category', 'description', 'normal_balance', 'is_payment_gateway']));

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        if ($response = $this->checkIzin('akses_hapus_akun')) {
            return $response;
        }

        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // tampilkan form settings
    public function edit()
    {
        if ($response = $this->checkIzin('akses_edit_setting')) {
            return $response;
        }

        $accounts = Account::all();
        $setting = Setting::first(); // ambil record pertama / satu-satunya
        return view('settings.edit', compact('setting', 'accounts'));
    }

    // update record settings
    public function update(Request $request)
    {
        if ($response = $this->checkIzin('akses_edit_setting')) {
            return $response;
        }

        $request->validate([
            'inventory_account_id' => 'required|exists:accounts,id',
            'stock_in_account_id' => 'required|exists:accounts,id',
            'stock_out_account_id' => 'required|exists:accounts,id',
        ]);

        $setting = Setting::first(); // ambil record pertama

        if (!$setting) {
            abort(404, 'Setting record not found'); // pastikan tidak create baru
        }

        $setting->update([
            'inventory_account_id' => $request->inventory_account_id,
            'stock_in_account_id' => $request->stock_in_account_id,
            'stock_out_account_id' => $request->stock_out_account_id,
        ]);

        return redirect()->back()->with('success', 'Setting berhasil diupdate');
    }
}

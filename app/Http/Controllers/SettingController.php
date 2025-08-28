<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Setting;
use Illuminate\Support\Arr;
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

        $setting = Setting::first(); // ambil record pertama
        if (!$setting) {
            abort(404, 'Setting record not found');
        }

        // update semua field yang ada di fillable
        // $setting->update($request->all()->except(['_token', '_method']));
        $setting->update(Arr::except($request->all(), ['_token', '_method']));

        return redirect()->back()->with('success', 'Setting berhasil diupdate');
    }

}

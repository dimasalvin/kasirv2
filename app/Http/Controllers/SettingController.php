<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $pricing = Setting::getPricingConfig();
        return view('settings.index', compact('pricing'));
    }

    public function updatePricing(Request $request)
    {
        $request->validate([
            'ppn_persen' => 'required|numeric|min:0|max:100',
            'markup_hv_persen' => 'required|numeric|min:0|max:100',
            'markup_resep_persen' => 'required|numeric|min:0|max:100',
        ]);

        Setting::setValue('ppn_persen', $request->ppn_persen);
        Setting::setValue('markup_hv_persen', $request->markup_hv_persen);
        Setting::setValue('markup_resep_persen', $request->markup_resep_persen);

        return back()->with('success', 'Pengaturan harga berhasil disimpan.');
    }

    // API: get pricing config (untuk JavaScript)
    public function getPricingApi()
    {
        return response()->json(Setting::getPricingConfig());
    }
}

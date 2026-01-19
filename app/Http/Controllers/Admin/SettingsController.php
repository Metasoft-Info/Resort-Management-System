<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller {
    public function index() {
        $settings = DB::table('system_settings')->first();
        return view('admin.settings', compact('settings'));
    }
    public function update(Request $request) {
        $validated = $request->validate([
            'resort_name' => 'required|string',
            'resort_phone' => 'required|string',
            'resort_email' => 'required|email',
            'resort_address' => 'required|string',
        ]);
        DB::table('system_settings')->updateOrInsert(['id' => 1], $validated);
        return redirect()->route('admin.settings.index')->with('success', 'Settings updated');
    }
}
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AddonService;
use Illuminate\Http\Request;

class AddonServiceController extends Controller
{
    public function index() {
        $addonServices = AddonService::paginate(15);
        return view('admin.addon-services.index', compact('addonServices'));
    }
    public function create() { return view('admin.addon-services.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
        ]);
        AddonService::create($validated);
        return redirect()->route('admin.addon-services.index')->with('success', 'Created');
    }
    public function edit(AddonService $addonService) {
        return view('admin.addon-services.edit', compact('addonService'));
    }
    public function update(Request $request, AddonService $addonService) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
        ]);
        $addonService->update($validated);
        return redirect()->route('admin.addon-services.index')->with('success', 'Updated');
    }
    public function destroy(AddonService $addonService) {
        $addonService->delete();
        return redirect()->route('admin.addon-services.index')->with('success', 'Deleted');
    }
}

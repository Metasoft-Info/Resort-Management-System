<?php

namespace App\Http\Controllers;

use App\Models\AddonService;
use Illuminate\Http\Request;

class AddonServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = AddonService::orderBy('service_type')->orderBy('category')->orderBy('name');
        
        if ($request->type) {
            $query->where('service_type', $request->type);
        }
        
        $addonServices = $query->paginate(20);
        return view('admin.addon-services.index', compact('addonServices'));
    }

    public function create()
    {
        return view('admin.addon-services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:decoration,sound_system,photography,catering,transport,room_service,laundry,parking,other',
            'service_type' => 'required|in:room,convention,both',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        AddonService::create($validated);

        return redirect()->route('admin.addon-services.index')
            ->with('success', 'Addon service added successfully!');
    }

    public function edit(AddonService $addonService)
    {
        return view('admin.addon-services.edit', compact('addonService'));
    }

    public function update(Request $request, AddonService $addonService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:decoration,sound_system,photography,catering,transport,room_service,laundry,parking,other',
            'service_type' => 'required|in:room,convention,both',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $addonService->update($validated);

        return redirect()->route('admin.addon-services.index')
            ->with('success', 'Addon service updated successfully!');
    }

    public function destroy(AddonService $addonService)
    {
        $addonService->delete();

        return redirect()->route('admin.addon-services.index')
            ->with('success', 'Addon service deleted successfully!');
    }

    // API endpoint for fetching addons by type
    public function getByType(Request $request)
    {
        $type = $request->type ?? 'room';
        
        if ($type === 'room') {
            $addons = AddonService::forRoom()->active()->get();
        } else {
            $addons = AddonService::forConvention()->active()->get();
        }
        
        return response()->json($addons);
    }
}


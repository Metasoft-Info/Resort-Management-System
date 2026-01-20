<?php

namespace App\Http\Controllers;

use App\Models\FoodPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodPackageController extends Controller
{
    public function index()
    {
        $foodPackages = FoodPackage::orderBy('name')->get();
        return view('admin.food-packages.index', compact('foodPackages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_person' => 'required|numeric|min:0',
            'items' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['items'] = $request->input('items', []);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('food-packages', 'public');
        }

        FoodPackage::create($validated);

        return back()->with('success', 'Food package created successfully!');
    }

    public function update(Request $request, FoodPackage $foodPackage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_person' => 'required|numeric|min:0',
            'items' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['items'] = $request->input('items', []);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($foodPackage->image) {
                Storage::disk('public')->delete($foodPackage->image);
            }
            $validated['image'] = $request->file('image')->store('food-packages', 'public');
        }

        $foodPackage->update($validated);

        return back()->with('success', 'Food package updated successfully!');
    }

    public function destroy(FoodPackage $foodPackage)
    {
        if ($foodPackage->image) {
            Storage::disk('public')->delete($foodPackage->image);
        }

        $foodPackage->delete();

        return back()->with('success', 'Food package deleted successfully!');
    }
}


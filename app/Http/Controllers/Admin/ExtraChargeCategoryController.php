<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraChargeCategory;
use Illuminate\Http\Request;

class ExtraChargeCategoryController extends Controller
{
    public function index()
    {
        $categories = ExtraChargeCategory::orderBy('order')->orderBy('name')->paginate(20);
        return view('admin.extra-charge-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.extra-charge-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['order'] = $validated['order'] ?? 0;

        ExtraChargeCategory::create($validated);

        return redirect()->route('admin.extra-charge-categories.index')
            ->with('success', 'অতিরিক্ত চার্জ ক্যাটাগরি তৈরি হয়েছে!');
    }

    public function edit(ExtraChargeCategory $extraChargeCategory)
    {
        return view('admin.extra-charge-categories.edit', compact('extraChargeCategory'));
    }

    public function update(Request $request, ExtraChargeCategory $extraChargeCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $extraChargeCategory->update($validated);

        return redirect()->route('admin.extra-charge-categories.index')
            ->with('success', 'অতিরিক্ত চার্জ ক্যাটাগরি আপডেট হয়েছে!');
    }

    public function destroy(ExtraChargeCategory $extraChargeCategory)
    {
        $extraChargeCategory->delete();
        return redirect()->route('admin.extra-charge-categories.index')
            ->with('success', 'অতিরিক্ত চার্জ ক্যাটাগরি মুছে ফেলা হয়েছে!');
    }

    // API endpoint for getting active categories
    public function getCategories()
    {
        $categories = ExtraChargeCategory::active()->orderBy('order')->orderBy('name')->get();
        return response()->json($categories);
    }
}

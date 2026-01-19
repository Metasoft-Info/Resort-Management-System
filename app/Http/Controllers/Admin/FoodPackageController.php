<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\FoodPackage;
use Illuminate\Http\Request;

class FoodPackageController extends Controller
{
    public function index() {
        $foodPackages = FoodPackage::paginate(15);
        return view('admin.food-packages.index', compact('foodPackages'));
    }
    public function create() { return view('admin.food-packages.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'items' => 'nullable|string',
        ]);
        FoodPackage::create($validated);
        return redirect()->route('admin.food-packages.index')->with('success', 'Created');
    }
    public function edit(FoodPackage $foodPackage) {
        return view('admin.food-packages.edit', compact('foodPackage'));
    }
    public function update(Request $request, FoodPackage $foodPackage) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'items' => 'nullable|string',
        ]);
        $foodPackage->update($validated);
        return redirect()->route('admin.food-packages.index')->with('success', 'Updated');
    }
    public function destroy(FoodPackage $foodPackage) {
        $foodPackage->delete();
        return redirect()->route('admin.food-packages.index')->with('success', 'Deleted');
    }
}

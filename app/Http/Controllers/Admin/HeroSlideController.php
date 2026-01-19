<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HeroSlideController extends Controller {
    public function index() {
        $heroSlides = HeroSlide::orderBy('order')->paginate(15);
        return view('admin.hero-slides.index', compact('heroSlides'));
    }
    public function create() { return view('admin.hero-slides.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|string',
            'order' => 'required|integer',
        ]);
        HeroSlide::create($validated);
        return redirect()->route('admin.hero-slides.index')->with('success', 'Created');
    }
    public function edit(HeroSlide $heroSlide) {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }
    public function update(Request $request, HeroSlide $heroSlide) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|string',
            'order' => 'required|integer',
        ]);
        $heroSlide->update($validated);
        return redirect()->route('admin.hero-slides.index')->with('success', 'Updated');
    }
    public function destroy(HeroSlide $heroSlide) {
        $heroSlide->delete();
        return redirect()->route('admin.hero-slides.index')->with('success', 'Deleted');
    }
}
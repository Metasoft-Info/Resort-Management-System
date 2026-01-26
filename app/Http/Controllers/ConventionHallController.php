<?php

namespace App\Http\Controllers;

use App\Models\ConventionHall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConventionHallController extends Controller
{
    public function index()
    {
        $halls = ConventionHall::paginate(10);
        return view('admin.convention-halls.index', compact('halls'));
    }

    public function create()
    {
        return view('admin.convention-halls.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dimensions' => 'required|numeric',
            'max_capacity' => 'required|integer',
            'price_per_day' => 'required|numeric',
            'amenities' => 'nullable|array',
            'event_types' => 'nullable|array',
            'status' => 'required|in:available,booked,maintenance',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['is_available'] = $request->status === 'available';
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('convention-halls', 'public');
                $images[] = $path;
            }
        }
        $validated['images'] = $images;

        ConventionHall::create($validated);

        return redirect()->route('admin.convention-halls.index')
            ->with('success', 'কনভেনশন হল সফলভাবে যোগ হয়েছে!');
    }

    public function show(ConventionHall $conventionHall)
    {
        return view('admin.convention-halls.show', compact('conventionHall'));
    }

    public function edit(ConventionHall $conventionHall)
    {
        return view('admin.convention-halls.edit', compact('conventionHall'));
    }

    public function update(Request $request, ConventionHall $conventionHall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dimensions' => 'required|numeric',
            'max_capacity' => 'required|integer',
            'price_per_day' => 'required|numeric',
            'amenities' => 'nullable|array',
            'event_types' => 'nullable|array',
            'status' => 'required|in:available,booked,maintenance',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['is_available'] = $request->status === 'available';
        
        // Handle image uploads
        $images = $conventionHall->images ?? [];
        
        // Handle image deletions
        if ($request->delete_images) {
            $deleteImages = json_decode($request->delete_images, true) ?? [];
            foreach ($deleteImages as $deleteImage) {
                if (in_array($deleteImage, $images)) {
                    Storage::disk('public')->delete($deleteImage);
                    $images = array_values(array_filter($images, fn($img) => $img !== $deleteImage));
                }
            }
        }
        
        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('convention-halls', 'public');
                $images[] = $path;
            }
        }
        $validated['images'] = $images;

        $conventionHall->update($validated);

        return redirect()->route('admin.convention-halls.index')
            ->with('success', 'কনভেনশন হল সফলভাবে আপডেট হয়েছে!');
    }

    public function destroy(ConventionHall $conventionHall)
    {
        // Delete associated images
        if ($conventionHall->images) {
            foreach ($conventionHall->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $conventionHall->delete();

        return redirect()->route('admin.convention-halls.index')
            ->with('success', 'কনভেনশন হল সফলভাবে মুছে ফেলা হয়েছে!');
    }
}


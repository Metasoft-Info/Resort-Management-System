<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResortInfo;
use App\Models\NavbarLink;
use App\Models\FooterSection;
use App\Models\FooterLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller {
    public function index() {
        $resortInfo = ResortInfo::first() ?? new ResortInfo();
        $navbarLinks = NavbarLink::orderBy('order')->get();
        $footerSections = FooterSection::with('links')->orderBy('order')->get();
        
        return view('admin.settings', compact('resortInfo', 'navbarLinks', 'footerSections'));
    }

    public function updateResortInfo(Request $request) {
        $validated = $request->validate([
            'resort_name' => 'required|string|max:255',
            'resort_tagline' => 'nullable|string|max:255',
            'about_text' => 'nullable|string',
            'mission_text' => 'nullable|string',
            'footer_description' => 'nullable|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'map_embed_url' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'copyright_text' => 'nullable|string',
            'facilities' => 'nullable|string',
        ]);

        $resortInfo = ResortInfo::first() ?? new ResortInfo();

        // Handle facilities - convert comma-separated string to array
        if (!empty($validated['facilities'])) {
            $facilities = array_map('trim', explode(',', $validated['facilities']));
            $validated['facilities'] = array_filter($facilities);
        } else {
            $validated['facilities'] = [];
        }

        // Handle social links
        $social_links = [];
        if (!empty($validated['facebook_url'])) {
            $social_links['facebook'] = $validated['facebook_url'];
        }
        if (!empty($validated['instagram_url'])) {
            $social_links['instagram'] = $validated['instagram_url'];
        }
        if (!empty($validated['twitter_url'])) {
            $social_links['twitter'] = $validated['twitter_url'];
        }
        $validated['social_links'] = $social_links;

        // Remove individual social link fields
        unset($validated['facebook_url'], $validated['instagram_url'], $validated['twitter_url']);

        if ($resortInfo->exists) {
            $resortInfo->update($validated);
        } else {
            ResortInfo::create($validated);
        }

        return back()->with('success', 'Resort information updated successfully!');
    }

    public function storeNavbarLink(Request $request) {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'display_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        NavbarLink::create($validated);

        return back()->with('success', 'Navbar link added!');
    }

    public function updateNavbarLink(Request $request, NavbarLink $navbarLink) {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'display_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $navbarLink->update($validated);

        return back()->with('success', 'Navbar link updated!');
    }

    public function destroyNavbarLink(NavbarLink $navbarLink) {
        $navbarLink->delete();
        return back()->with('success', 'Navbar link deleted!');
    }

    public function storeFooterSection(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'display_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        FooterSection::create($validated);

        return back()->with('success', 'Footer section added!');
    }

    public function updateFooterSection(Request $request, FooterSection $footerSection) {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'display_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $footerSection->update($validated);

        return back()->with('success', 'Footer section updated!');
    }

    public function destroyFooterSection(FooterSection $footerSection) {
        $footerSection->links()->delete();
        $footerSection->delete();
        return back()->with('success', 'Footer section deleted!');
    }

    public function storeFooterLink(Request $request) {
        $validated = $request->validate([
            'section_id' => 'required|exists:footer_sections,id',
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'display_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        FooterLink::create($validated);

        return back()->with('success', 'Footer link added!');
    }

    public function updateFooterLink(Request $request, FooterLink $footerLink) {
        $validated = $request->validate([
            'section_id' => 'required|exists:footer_sections,id',
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'display_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $footerLink->update($validated);

        return back()->with('success', 'Footer link updated!');
    }

    public function destroyFooterLink(FooterLink $footerLink) {
        $footerLink->delete();
        return back()->with('success', 'Footer link deleted!');
    }
}

<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResortInfo;
use App\Models\NavbarLink;
use App\Models\FooterSection;
use App\Models\FooterLink;
use App\Models\AdminMenuSetting;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\AdditionalGuest;
use App\Models\ConventionBooking;
use App\Models\ConventionPayment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller {
    public function index() {
        $resortInfo = ResortInfo::first() ?? new ResortInfo();
        $navbarLinks = NavbarLink::orderBy('order')->get();
        $footerSections = FooterSection::with('links')->orderBy('order')->get();
        $menuSettings = AdminMenuSetting::orderBy('order')->get()->groupBy('group_name');
        
        return view('admin.settings', compact('resortInfo', 'navbarLinks', 'footerSections', 'menuSettings'));
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
        $validated['order'] = $validated['display_order'];
        unset($validated['display_order']);
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
        $validated['order'] = $validated['display_order'];
        unset($validated['display_order']);
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
            'footer_section_id' => 'required|exists:footer_sections,id',
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') || true;
        $validated['order'] = $validated['display_order'] ?? 1;
        unset($validated['display_order']);
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

    // Logo Management
    public function updateLogos(Request $request) {
        $request->validate([
            'header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,gif,svg|max:512',
            'admin_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $resortInfo = ResortInfo::first() ?? ResortInfo::create([
            'resort_name' => 'Tufan Resort',
            'phone' => '',
            'email' => '',
        ]);

        foreach (['header_logo', 'footer_logo', 'favicon', 'admin_logo'] as $logoType) {
            if ($request->hasFile($logoType)) {
                // Delete old logo if exists
                if ($resortInfo->$logoType) {
                    Storage::disk('public')->delete($resortInfo->$logoType);
                }
                
                $path = $request->file($logoType)->store('logos', 'public');
                $resortInfo->$logoType = $path;
            }
        }

        $resortInfo->save();
        return back()->with('success', 'লোগো সফলভাবে আপডেট হয়েছে!');
    }

    public function deleteLogo(Request $request, $type) {
        $resortInfo = ResortInfo::first();
        
        if ($resortInfo && in_array($type, ['header_logo', 'footer_logo', 'favicon', 'admin_logo'])) {
            if ($resortInfo->$type) {
                Storage::disk('public')->delete($resortInfo->$type);
                $resortInfo->$type = null;
                $resortInfo->save();
            }
        }

        return back()->with('success', 'লোগো মুছে ফেলা হয়েছে!');
    }

    // Admin Menu Settings
    public function updateMenuSettings(Request $request) {
        $activeMenus = $request->input('active_menus', []);
        
        AdminMenuSetting::query()->update(['is_active' => false]);
        AdminMenuSetting::whereIn('menu_key', $activeMenus)->update(['is_active' => true]);
        
        // System menus are always active
        AdminMenuSetting::where('is_system', true)->update(['is_active' => true]);

        return back()->with('success', 'মেনু সেটিংস আপডেট হয়েছে!');
    }

    public function seedMenus() {
        AdminMenuSetting::seedDefaultMenus();
        return back()->with('success', 'ডিফল্ট মেনু লোড হয়েছে!');
    }

    // Reset Room Bookings Only
    public function resetRoomBookings(Request $request) {
        $request->validate(['confirm' => 'required|in:RESET']);
        
        DB::beginTransaction();
        try {
            BookingPayment::truncate();
            AdditionalGuest::truncate();
            Booking::truncate();
            
            ActivityLog::log('Reset room bookings', 'System', null, ['action' => 'room_booking_reset']);
            
            DB::commit();
            return back()->with('success', 'All room bookings have been reset successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reset bookings: ' . $e->getMessage());
        }
    }

    // Reset Convention Bookings Only
    public function resetConventionBookings(Request $request) {
        $request->validate(['confirm' => 'required|in:RESET']);
        
        DB::beginTransaction();
        try {
            ConventionPayment::truncate();
            ConventionBooking::truncate();
            
            ActivityLog::log('Reset convention bookings', 'System', null, ['action' => 'convention_booking_reset']);
            
            DB::commit();
            return back()->with('success', 'All convention bookings have been reset successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reset bookings: ' . $e->getMessage());
        }
    }

    // Reset All Bookings
    public function resetAllBookings(Request $request) {
        $request->validate(['confirm' => 'required|in:RESET']);
        
        DB::beginTransaction();
        try {
            BookingPayment::truncate();
            AdditionalGuest::truncate();
            Booking::truncate();
            ConventionPayment::truncate();
            ConventionBooking::truncate();
            
            ActivityLog::log('Reset all bookings', 'System', null, ['action' => 'all_booking_reset']);
            
            DB::commit();
            return back()->with('success', 'All bookings have been reset successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reset bookings: ' . $e->getMessage());
        }
    }

    // Clear Activity Logs
    public function clearActivityLogs(Request $request) {
        $request->validate(['confirm' => 'required|in:CLEAR']);
        
        ActivityLog::truncate();
        return back()->with('success', 'Activity logs cleared successfully!');
    }
}

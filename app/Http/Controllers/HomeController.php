<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ConventionHall;
use App\Models\HeroSlide;
use App\Models\ResortInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache homepage data for 10 minutes
        $rooms = Cache::remember('homepage_rooms', 600, function () {
            return Room::with('roomType')->take(6)->get();
        });
        
        $halls = Cache::remember('homepage_halls', 600, function () {
            return ConventionHall::take(3)->get();
        });
        
        $heroSlides = Cache::remember('homepage_hero_slides', 600, function () {
            return HeroSlide::where('is_active', true)->orderBy('order')->get();
        });

        return view('home', compact('rooms', 'halls', 'heroSlides'));
    }

    public function rooms()
    {
        $rooms = Room::with('roomType')->paginate(12);
        return view('rooms', compact('rooms'));
    }

    public function about()
    {
        return view('about');
    }

    public function conventionHall()
    {
        $halls = Cache::remember('convention_halls', 600, function () {
            return ConventionHall::all();
        });
        return view('convention-hall', compact('halls'));
    }
}

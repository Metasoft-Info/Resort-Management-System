<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ConventionHall;
use App\Models\HeroSlide;
use App\Models\ResortInfo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = Room::take(6)->get();
        $halls = ConventionHall::take(3)->get();

        return view('home', compact('rooms', 'halls'));
    }

    public function rooms()
    {
        $rooms = Room::paginate(12);
        return view('rooms', compact('rooms'));
    }

    public function about()
    {
        return view('about');
    }

    public function conventionHall()
    {
        $halls = ConventionHall::all();
        return view('convention-hall', compact('halls'));
    }
}

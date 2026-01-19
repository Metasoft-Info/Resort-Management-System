<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Room;
use App\Models\ConventionHall;
use App\Models\HeroSlide;
use App\Models\ResortInfo;
use App\Models\FoodPackage;
use App\Models\AddonService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Resort Owner',
            'email' => 'owner@tufanresort.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'permissions' => ['*'],
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@tufanresort.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        Room::create([
            'room_number' => '101',
            'name' => 'Deluxe Ocean View',
            'type' => 'deluxe',
            'description' => 'Spacious room with stunning lake views, perfect for couples.',
            'price_per_night' => 3500.00,
            'has_ac' => true,
            'ac_price' => 3500.00,
            'non_ac_price' => 2500.00,
            'max_guests' => 2,
            'number_of_beds' => 1,
            'amenities' => ['WiFi', 'AC', 'TV', 'Mini Bar', 'Lake View'],
            'status' => 'available',
        ]);

        Room::create([
            'room_number' => '201',
            'name' => 'Family Suite',
            'type' => 'suite',
            'description' => 'Large family suite with separate bedroom and living area.',
            'price_per_night' => 6000.00,
            'has_ac' => true,
            'ac_price' => 6000.00,
            'non_ac_price' => 4500.00,
            'max_guests' => 6,
            'number_of_beds' => 3,
            'amenities' => ['WiFi', 'AC', 'TV', 'Mini Bar', 'Kitchenette', 'Balcony'],
            'status' => 'available',
        ]);

        Room::create([
            'room_number' => '102',
            'name' => 'Standard Room',
            'type' => 'standard',
            'description' => 'Comfortable standard room with all basic amenities.',
            'price_per_night' => 2000.00,
            'has_ac' => true,
            'ac_price' => 2000.00,
            'non_ac_price' => 1500.00,
            'max_guests' => 2,
            'number_of_beds' => 1,
            'amenities' => ['WiFi', 'AC', 'TV'],
            'status' => 'available',
        ]);

        ConventionHall::create([
            'name' => 'Grand Ballroom',
            'description' => 'State-of-the-art convention hall perfect for weddings, conferences, and corporate events.',
            'dimensions' => 5000.00,
            'max_capacity' => 200,
            'price_per_day' => 25000.00,
            'is_available' => true,
            'amenities' => ['Sound System', 'Projector', 'Stage', 'AC', 'Catering Kitchen', 'Parking'],
            'event_types' => ['Wedding', 'Conference', 'Corporate Event', 'Birthday Party', 'Cultural Event'],
            'time_slots' => ['Morning (8AM-12PM)', 'Afternoon (1PM-5PM)', 'Evening (6PM-10PM)', 'Full Day (8AM-10PM)'],
        ]);

        HeroSlide::create([
            'title' => 'Welcome to Tufan Resort',
            'description' => 'Discover Luxury & Tranquility by the Lake',
            'image' => '/images/hero1.jpg',
            'order' => 1,
            'is_active' => true,
        ]);

        ResortInfo::create([
            'about_text' => 'Welcome to Tufan Resort, where luxury meets nature. Nestled in the heart of pristine landscapes, we offer world-class hospitality and unforgettable experiences.',
            'mission_text' => 'Our mission is to provide guests with exceptional service, comfort, and memorable experiences that exceed expectations.',
            'address' => '123 Lake View, Beautiful City, Bangladesh',
            'phone' => '+880-123-456789',
            'email' => 'info@tufanresort.com',
            'facilities' => ['Swimming Pool', 'Spa & Wellness', 'Restaurant', 'Gym', 'Garden', 'Parking'],
            'social_links' => ['facebook' => 'https://facebook.com/tufanresort', 'instagram' => 'https://instagram.com/tufanresort'],
        ]);

        FoodPackage::create([
            'name' => 'Basic Package',
            'description' => 'Simple and delicious meals',
            'price_per_person' => 500.00,
            'items' => ['Rice', 'Dal', 'Chicken Curry', 'Vegetables', 'Salad', 'Dessert'],
            'is_active' => true,
        ]);

        AddonService::create([
            'name' => 'Extra Bed',
            'description' => 'Additional bed for extra guest',
            'price' => 500.00,
            'category' => 'room',
            'is_active' => true,
        ]);
    }
}

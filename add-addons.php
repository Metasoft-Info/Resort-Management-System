<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// All 78 items (using only valid categories: decoration, sound_system, photography, transport, lighting, stage, other)
$addons = [
    ['name' => 'চেয়ার-প্লাস্টিক', 'price' => 5, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'চেয়ার-প্লাস্টিক (হেভি)', 'price' => 10, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'চেয়ার (ভি.আই.পি কুশন)', 'price' => 200, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'চেয়ার কভার', 'price' => 15, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'হাড়ি বা ডেকচি ২৬ পর্যন্ত', 'price' => 100, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'হাড়ি বা ডেকচি ২৭/৩০', 'price' => 150, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'কড়াই ছোট', 'price' => 100, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'কড়াই গোহার', 'price' => 200, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'লাঙ্গা', 'price' => 50, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'হামান দিস্তা', 'price' => 15, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'বিরা মেশিন', 'price' => 250, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'ওয়েল গীট', 'price' => 75, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গাদলা গদি', 'price' => 15, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গাদলা মালামাইন', 'price' => 15, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'জগ প্লাস্টিক', 'price' => 15, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'জল কাট', 'price' => 20, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'মালামাইন প্লেট', 'price' => 5, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'কড়ির প্লেট', 'price' => 10, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'প্লেট হাফ কড়ি', 'price' => 5, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'গ্লাস মালামাইন', 'price' => 5, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'গ্লাস কাঁচের', 'price' => 5, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'রাইস ডিস মালামাইন', 'price' => 20, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'চামচ স্টিল', 'price' => 5, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'চা অথবা কাটা চামচ', 'price' => 5, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'পানির ড্রাম', 'price' => 100, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'বেসিন ডাবল', 'price' => 500, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'বেসিন সিঙ্গেল', 'price' => 100, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'বরের প্লেট', 'price' => 200, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'সুপ বাটি', 'price' => 10, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'বালতি', 'price' => 50, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'টেবিল লম্বা', 'price' => 50, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'টেবিল গোল', 'price' => 100, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'টেবিল রুশ', 'price' => 20, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'টেবিল কেচি', 'price' => 400, 'unit' => 'জোড়া', 'category' => 'decoration'],
    ['name' => 'টেবিল ফেস সিঙ্গেল', 'price' => 200, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সামিয়ানা ১৫×২০ ফুট', 'price' => 300, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'চেন টু কালার', 'price' => 50, 'unit' => 'সেট', 'category' => 'decoration'],
    ['name' => 'চেন এক কালার', 'price' => 30, 'unit' => 'সেট', 'category' => 'decoration'],
    ['name' => 'বড় পতাকা পাইপ ফিটিং সহ', 'price' => 500, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'শো পর্দা সহ লাইট সাইড ৩০ ফুট', 'price' => 50, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সাইড পানি', 'price' => 100, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গিলাপ ২০×৩০ সুট', 'price' => 400, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গিলাপ ৩০×৩০ সুট', 'price' => 600, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গিলাপ ৩০×৪০ সুট', 'price' => 800, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'জেনারেটর', 'price' => 2500, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'প্যান্ডেল মাইকে স্যাম্পল', 'price' => 5, 'unit' => 'স্কয়ার', 'category' => 'decoration'],
    ['name' => 'গেট অর্ডিনারি', 'price' => 5, 'unit' => 'স্কয়ার', 'category' => 'decoration'],
    ['name' => 'প্যান্ডেল সাধারণ', 'price' => 15, 'unit' => 'স্কয়ার', 'category' => 'decoration'],
    ['name' => 'প্যান্ডেল অর্ডিনারি', 'price' => 15, 'unit' => 'স্কয়ার', 'category' => 'decoration'],
    ['name' => 'বিড়িকান প্যান্ডেল', 'price' => 10000, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গেইট স্ট্যান্ড', 'price' => 200, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'পাটি কান্দেত', 'price' => 50, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'গেইট সিলেড রোড এম হাইলাইট ছোট', 'price' => 10000, 'unit' => 'পিস', 'category' => 'lighting'],
    ['name' => 'গেইট সিলেড রোড এম হাইলাইট বড়', 'price' => 15000, 'unit' => 'পিস', 'category' => 'lighting'],
    ['name' => 'বেস্ট কার্পেট ৩০×৪ ফুট', 'price' => 400, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'রেড কার্পেট ৪/৩০ ফুট', 'price' => 500, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'স্বর্গ হাট তুল আদম', 'price' => 4000, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সাধার ঝালার ডিজাইন', 'price' => 2000, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'ফ্লাস স্ট্যান্ড', 'price' => 50, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'টিউব লাইট', 'price' => 50, 'unit' => 'পিস', 'category' => 'lighting'],
    ['name' => 'হ্যালোজেন লাইট', 'price' => 100, 'unit' => 'পিস', 'category' => 'lighting'],
    ['name' => 'লাইট সেট একস্ট্রা', 'price' => 500, 'unit' => 'সেট', 'category' => 'lighting'],
    ['name' => 'কার বিয়াল ২০×৩০ ফুট', 'price' => 200, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'রোড সিফনি বোট', 'price' => 300, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'পুংকা কাষ্টম', 'price' => 50, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'শীতলের সুন্দর চব', 'price' => 200, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সিড ড্রয়ার ক্যানের', 'price' => 300, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সিড ড্রয়ার স্টিলের', 'price' => 300, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সোফা সেট ১ সিটের', 'price' => 300, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'পার্কিং লাইট', 'price' => 20, 'unit' => 'পিস', 'category' => 'lighting'],
    ['name' => 'স্টেইন ওয়্যার ১.৫ ফুট', 'price' => 50, 'unit' => 'ফেন্স', 'category' => 'stage'],
    ['name' => 'স্টেজ ওয়্যা ২.৫ ফুট', 'price' => 100, 'unit' => 'ফেন্স', 'category' => 'stage'],
    ['name' => 'স্টেজ ওয়্যার ৫ ফুট', 'price' => 300, 'unit' => 'ফেন্স', 'category' => 'stage'],
    ['name' => 'সোফা দুই সিটের তিতাস', 'price' => 1500, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'সোফা ৩ সিটের তিতাস', 'price' => 2000, 'unit' => 'পিস', 'category' => 'decoration'],
    ['name' => 'ওয়েটিং ড্রয়াস ৩ টেবিলের', 'price' => 800, 'unit' => 'সেট', 'category' => 'decoration'],
    ['name' => 'সোফা ৫ টেবিলের তিতাস', 'price' => 1000, 'unit' => 'সেট', 'category' => 'decoration'],
];

$count = 0;
$now = date('Y-m-d H:i:s');
foreach ($addons as $addon) {
    try {
        DB::table('addon_services')->insert([
            'name' => $addon['name'],
            'price' => $addon['price'],
            'unit' => $addon['unit'],
            'category' => $addon['category'],
            'service_type' => 'convention',
            'is_active' => 1,
            'description' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $count++;
    } catch (Exception $e) {
        echo "Error adding {$addon['name']}: " . $e->getMessage() . "\n";
    }
}

echo "Added $count convention addon services successfully!\n";

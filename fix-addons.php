<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$now = date('Y-m-d H:i:s');

// Add the missing items with 'other' and 'decoration' categories instead of lighting/stage
$addons = [
    ['name' => 'গেইট সিলেড রোড এম হাইলাইট ছোট', 'price' => 10000, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'গেইট সিলেড রোড এম হাইলাইট বড়', 'price' => 15000, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'টিউব লাইট', 'price' => 50, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'হ্যালোজেন লাইট', 'price' => 100, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'লাইট সেট একস্ট্রা', 'price' => 500, 'unit' => 'সেট', 'category' => 'other'],
    ['name' => 'পার্কিং লাইট', 'price' => 20, 'unit' => 'পিস', 'category' => 'other'],
    ['name' => 'স্টেইন ওয়্যার ১.৫ ফুট', 'price' => 50, 'unit' => 'ফেন্স', 'category' => 'decoration'],
    ['name' => 'স্টেজ ওয়্যা ২.৫ ফুট', 'price' => 100, 'unit' => 'ফেন্স', 'category' => 'decoration'],
    ['name' => 'স্টেজ ওয়্যার ৫ ফুট', 'price' => 300, 'unit' => 'ফেন্স', 'category' => 'decoration'],
];

$count = 0;
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
        echo "Error: " . $e->getMessage() . "\n";
    }
}

$total = DB::table('addon_services')->where('service_type', 'convention')->count();
echo "Added $count more items. Total convention addons: $total\n";

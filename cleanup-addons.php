<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Delete duplicates, keeping the first one
$duplicates = DB::table('addon_services')
    ->select('name', DB::raw('MIN(id) as min_id, COUNT(*) as count'))
    ->where('service_type', 'convention')
    ->groupBy('name')
    ->having('count', '>', 1)
    ->get();

$deletedCount = 0;
foreach ($duplicates as $dup) {
    $deleted = DB::table('addon_services')
        ->where('service_type', 'convention')
        ->where('name', $dup->name)
        ->where('id', '!=', $dup->min_id)
        ->delete();
    $deletedCount += $deleted;
}

$total = DB::table('addon_services')->where('service_type', 'convention')->count();
echo "Deleted $deletedCount duplicate entries. Total convention addons: $total\n";

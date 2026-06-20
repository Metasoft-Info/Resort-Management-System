<?php
// Production deployment helper - Run migrations and clear caches via browser
// DELETE THIS FILE AFTER RUNNING FOR SECURITY

ini_set('display_errors', 1);
error_reporting(E_ALL);

$laravelRoot = dirname(__DIR__);

require $laravelRoot . '/vendor/autoload.php';

$app = require_once $laravelRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = [];

// Try specific migration first
$specificMigration = 'database/migrations/2026_06_11_070000_add_updated_by_to_bookings.php';
try {
    \Artisan::call('migrate', ['--force' => true, '--path' => $specificMigration]);
    $output[] = 'MIGRATION (specific): ' . trim(\Artisan::output());
} catch (\Exception $e) {
    $output[] = 'Specific migration failed, trying full: ' . $e->getMessage();
    try {
        \Artisan::call('migrate', ['--force' => true]);
        $output[] = 'MIGRATION (full): ' . trim(\Artisan::output());
    } catch (\Exception $e2) {
        $output[] = 'Full migration error (tables may already exist): ' . substr($e2->getMessage(), 0, 200);
    }
}

// Ensure columns exist manually as fallback
$added = [];
if (!\Schema::hasColumn('bookings', 'updated_by_id')) {
    \Schema::table('bookings', function ($table) {
        $table->unsignedBigInteger('updated_by_id')->nullable()->after('created_by_id');
    });
    $added[] = 'bookings.updated_by_id';
}
if (!\Schema::hasColumn('convention_bookings', 'updated_by_id')) {
    \Schema::table('convention_bookings', function ($table) {
        $table->unsignedBigInteger('updated_by_id')->nullable()->after('created_by_id');
    });
    $added[] = 'convention_bookings.updated_by_id';
}
if ($added) {
    $output[] = 'MANUAL COLUMNS ADDED: ' . implode(', ', $added);
} else {
    $output[] = 'Columns already exist: updated_by_id on both tables';
}

// Ensure booking_payments table exists
if (!\Schema::hasTable('booking_payments')) {
    \Schema::create('booking_payments', function ($table) {
        $table->id();
        $table->foreignId('booking_id')->constrained()->onDelete('cascade');
        $table->decimal('amount', 10, 2);
        $table->enum('method', ['cash', 'card', 'mfs'])->default('cash');
        $table->enum('type', ['advance', 'payment', 'refund'])->default('payment');
        $table->text('note')->nullable();
        $table->foreignId('recorded_by_id')->constrained('users');
        $table->timestamps();
    });
    $output[] = 'CREATED TABLE: booking_payments';
} else {
    $output[] = 'Table already exists: booking_payments';
}

try {
    \Artisan::call('config:clear');
    $output[] = 'Config cache cleared';
} catch (\Exception $e) {
    $output[] = 'CONFIG ERROR: ' . $e->getMessage();
}

try {
    \Artisan::call('cache:clear');
    $output[] = 'Application cache cleared';
} catch (\Exception $e) {
    $output[] = 'CACHE ERROR: ' . $e->getMessage();
}

try {
    \Artisan::call('route:clear');
    $output[] = 'Route cache cleared';
} catch (\Exception $e) {
    $output[] = 'ROUTE ERROR: ' . $e->getMessage();
}

try {
    \Artisan::call('view:clear');
    $output[] = 'View cache cleared';
} catch (\Exception $e) {
    $output[] = 'VIEW ERROR: ' . $e->getMessage();
}

// Show last 10 log lines
$logFile = $laravelRoot . '/storage/logs/laravel.log';
$logLines = [];
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $logLines = array_slice(array_filter(explode("\n", $logContent)), -50);
}

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Fix Production</title>';
echo '<style>body{font-family:monospace;padding:20px;background:#1a1a1a;color:#0f0;}pre{background:#000;padding:15px;border-radius:5px;overflow:auto;}h2{color:#fff;border-bottom:1px solid #333;padding-bottom:10px;}</style></head><body>';
echo '<h2>Deployment Fix</h2><pre>' . implode("\n", $output) . '</pre>';
echo '<h2>Last Log Lines</h2><pre>' . implode("\n", $logLines) . '</pre>';
echo '<p style="color:#f00;font-weight:bold;">DELETE THIS FILE (public/run.php) AFTER RUNNING!</p>';
echo '</body></html>';

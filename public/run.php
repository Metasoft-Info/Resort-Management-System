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

try {
    \Artisan::call('migrate', ['--force' => true]);
    $output[] = 'MIGRATION: ' . trim(\Artisan::output());
} catch (\Exception $e) {
    $output[] = 'MIGRATION ERROR: ' . $e->getMessage();
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
    $logLines = array_slice(array_filter(explode("\n", $logContent)), -15);
}

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Fix Production</title>';
echo '<style>body{font-family:monospace;padding:20px;background:#1a1a1a;color:#0f0;}pre{background:#000;padding:15px;border-radius:5px;overflow:auto;}h2{color:#fff;border-bottom:1px solid #333;padding-bottom:10px;}</style></head><body>';
echo '<h2>Deployment Fix</h2><pre>' . implode("\n", $output) . '</pre>';
echo '<h2>Last Log Lines</h2><pre>' . implode("\n", $logLines) . '</pre>';
echo '<p style="color:#f00;font-weight:bold;">DELETE THIS FILE (public/run.php) AFTER RUNNING!</p>';
echo '</body></html>';

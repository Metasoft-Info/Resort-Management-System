<?php
// Temporary migration runner - DELETE AFTER USE!
// Security key required
if (!isset($_GET['key']) || $_GET['key'] !== 'tufan2026migrate') {
    die('Unauthorized');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre>";
echo "Running migrations...\n\n";

try {
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "\n✅ Migrations completed successfully!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "\n\n⚠️ DELETE THIS FILE AFTER RUNNING!";
echo "</pre>";

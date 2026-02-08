<?php
// Add extra charges menu - DELETE AFTER USE!
if (!isset($_GET['key']) || $_GET['key'] !== 'tufan2026menu') {
    die('Unauthorized');
}

$vendorPath = file_exists(__DIR__.'/vendor/autoload.php') 
    ? __DIR__.'/vendor/autoload.php' 
    : __DIR__.'/../vendor/autoload.php';
$bootstrapPath = file_exists(__DIR__.'/bootstrap/app.php') 
    ? __DIR__.'/bootstrap/app.php' 
    : __DIR__.'/../bootstrap/app.php';

require $vendorPath;
$app = require_once $bootstrapPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre style='font-family:monospace; background:#1a1a2e; color:#16db65; padding:20px;'>";

try {
    // Clear caches
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    echo "✅ Caches cleared\n\n";

    // Add extra charges menu
    $pdo = DB::connection()->getPdo();
    
    // Check if menu exists
    $stmt = $pdo->prepare("SELECT * FROM admin_menu_settings WHERE menu_key = 'extra_charge_categories'");
    $stmt->execute();
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $pdo->exec("INSERT INTO admin_menu_settings 
            (menu_key, menu_label, menu_icon, route_name, route_pattern, group_name, `order`, is_active, is_system, created_at, updated_at) 
            VALUES 
            ('extra_charge_categories', 'Extra Charges', 'fas fa-tags', 'admin.extra-charge-categories.index', 'admin.extra-charge-categories.*', 'Services', 42, 1, 0, NOW(), NOW())");
        echo "✅ Extra Charges menu added\n";
    } else {
        echo "⏭️ Extra Charges menu already exists\n";
    }
    
    echo "\n✅ Done! Delete this file now.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";

<?php
// /artisan-web.php - Web-based artisan runner - DELETE AFTER USE!
if (!isset($_GET['key']) || $_GET['key'] !== 'tufan2026migrate') {
    die('Unauthorized');
}

define('LARAVEL_START', microtime(true));

// Paths work for both public/ and public_html/ deployments
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

// Clear caches first
echo "🔄 Clearing caches...\n\n";

try {
    Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";
} catch (Exception $e) {
    echo "⚠️ Config: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
} catch (Exception $e) {
    echo "⚠️ Route: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
} catch (Exception $e) {
    echo "⚠️ View: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";
} catch (Exception $e) {
    echo "⚠️ Cache: " . $e->getMessage() . "\n";
}

echo "\n🗃️ Running migrations...\n\n";

// Run specific migration files only
try {
    // Add columns directly via DB instead of broken migration system
    $pdo = DB::connection()->getPdo();
    
    // Add bookings columns if not exist
    $columns = ['company_name', 'bkash_number', 'bank_name', 'extra_charges_data', 'discount_reference'];
    foreach ($columns as $col) {
        try {
            $pdo->exec("ALTER TABLE bookings ADD COLUMN $col VARCHAR(255) NULL");
            echo "✅ Added bookings.$col\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⏭️ bookings.$col already exists\n";
            } else {
                echo "⚠️ bookings.$col: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Add extra_charges_data as JSON
    try {
        $pdo->exec("ALTER TABLE bookings MODIFY COLUMN extra_charges_data JSON NULL");
        echo "✅ Updated bookings.extra_charges_data to JSON\n";
    } catch (Exception $e) {
        echo "⏭️ extra_charges_data type: " . $e->getMessage() . "\n";
    }
    
    // Add company_name to additional_guests
    try {
        $pdo->exec("ALTER TABLE additional_guests ADD COLUMN company_name VARCHAR(255) NULL AFTER phone");
        echo "✅ Added additional_guests.company_name\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⏭️ additional_guests.company_name already exists\n";
        } else {
            echo "⚠️ additional_guests.company_name: " . $e->getMessage() . "\n";
        }
    }
    
    // Create extra_charge_categories table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS extra_charge_categories (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10,2) DEFAULT 0,
            unit VARCHAR(255) NULL,
            description TEXT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            `order` INT DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");
        echo "✅ Created extra_charge_categories table\n";
    } catch (Exception $e) {
        echo "⚠️ extra_charge_categories: " . $e->getMessage() . "\n";
    }
    
    // Update payment_method enum to include bkash
    try {
        $pdo->exec("ALTER TABLE bookings MODIFY COLUMN payment_method ENUM('cash', 'card', 'mfs', 'bkash') NOT NULL DEFAULT 'cash'");
        echo "✅ Updated payment_method enum\n";
    } catch (Exception $e) {
        echo "⚠️ payment_method enum: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Database updates completed!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n⚠️ DELETE THIS FILE IMMEDIATELY AFTER USE!\n";
echo "</pre>";

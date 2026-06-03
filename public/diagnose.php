<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$secret = $_GET['secret'] ?? '';
if ($secret !== 'diag2026tufan') {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

$baseDir = dirname(__DIR__);

echo "=== Laravel Production Diagnose ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "Base: {$baseDir}\n";
echo "PHP: " . PHP_VERSION . "\n\n";

$checks = [
    '.env' => file_exists($baseDir . '/.env'),
    'vendor/autoload.php' => file_exists($baseDir . '/vendor/autoload.php'),
    'bootstrap/app.php' => file_exists($baseDir . '/bootstrap/app.php'),
    'storage writable' => is_writable($baseDir . '/storage'),
    'bootstrap/cache writable' => is_writable($baseDir . '/bootstrap/cache'),
];

foreach ($checks as $name => $ok) {
    echo sprintf("%-28s : %s\n", $name, $ok ? 'OK' : 'FAIL');
}

echo "\n";

if (!file_exists($baseDir . '/vendor/autoload.php')) {
    echo "ERROR: vendor/autoload.php missing\n";
    exit;
}

try {
    require $baseDir . '/vendor/autoload.php';
    $app = require $baseDir . '/bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "App env                 : " . config('app.env') . "\n";
    echo "App debug               : " . (config('app.debug') ? 'true' : 'false') . "\n";
    echo "DB default              : " . config('database.default') . "\n";
    echo "DB database             : " . (string) config('database.connections.' . config('database.default') . '.database') . "\n";
    echo "Mail mailer             : " . (string) config('mail.default') . "\n";

    try {
        $row = Illuminate\Support\Facades\DB::select('SELECT 1 as ok');
        $ok = isset($row[0]) && ((int) ($row[0]->ok ?? 0) === 1);
        echo "DB connection           : " . ($ok ? 'OK' : 'FAIL') . "\n";
    } catch (Throwable $dbEx) {
        echo "DB connection           : FAIL\n";
        echo "DB error                : " . $dbEx->getMessage() . "\n";
    }

    $action = $_GET['action'] ?? 'status';
    if ($action === 'clear') {
        echo "\nRunning optimize:clear ...\n";
        try {
            $kernel->call('optimize:clear');
            echo $kernel->output() . "\n";
        } catch (Throwable $artisanEx) {
            echo "optimize:clear error    : " . $artisanEx->getMessage() . "\n";
        }
    }
} catch (Throwable $e) {
    echo "\nBOOTSTRAP ERROR\n";
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ':' . $e->getLine() . "\n";
}

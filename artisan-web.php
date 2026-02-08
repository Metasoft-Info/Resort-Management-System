<?php
/**
 * Web-based Artisan Command Runner
 * Use: /artisan-web.php?command=view:clear
 * SQL: /artisan-web.php?sql=SELECT...
 */

// Handle SQL queries first
if (isset($_GET['sql']) || isset($_POST['sql'])) {
    // Load .env file to get database credentials
    $envFile = __DIR__ . '/.env';
    $env = [];
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
    }
    
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $database = $env['DB_DATABASE'] ?? 'tufanconx_resort';
    $username = $env['DB_USERNAME'] ?? 'tufanconx_resort';
    $password = $env['DB_PASSWORD'] ?? '';
    
    $sql = $_GET['sql'] ?? $_POST['sql'] ?? '';
    header('Content-Type: application/json');
    
    try {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (stripos(trim($sql), 'select') === 0 || stripos(trim($sql), 'show') === 0 || stripos(trim($sql), 'describe') === 0) {
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result = $pdo->exec($sql);
        }
        echo json_encode(['success' => true, 'result' => $result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Security check - only allow specific commands
$allowedCommands = [
    'view:clear',
    'route:clear',
    'config:clear',
    'cache:clear',
    'clear-compiled',
    'optimize:clear',
];

$command = $_GET['command'] ?? '';

if (empty($command)) {
    echo "Usage: ?command=view:clear\n";
    echo "Allowed commands: " . implode(', ', $allowedCommands);
    exit;
}

if (!in_array($command, $allowedCommands)) {
    echo "Command not allowed: " . htmlspecialchars($command);
    exit;
}

// Change to Laravel root directory
chdir(__DIR__);

// Run the artisan command
exec("php artisan {$command} 2>&1", $output, $returnCode);

header('Content-Type: text/plain');
echo "Command: php artisan {$command}\n";
echo "Return Code: {$returnCode}\n";
echo "Output:\n";
echo implode("\n", $output);

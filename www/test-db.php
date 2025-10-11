<?php
header('Content-Type: application/json');

// Restrict this diagnostic endpoint to development environment only
if ((getenv('APP_ENV') ?: 'development') !== 'development') {
    http_response_code(403);
    echo json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => 'Access Denied: test-db.php is only available in development environment',
    ], JSON_PRETTY_PRINT);
    exit;
}

// Load environment variables using getenv() - more reliable in Docker
$db_host = getenv('DB_HOST') ?: 'mysql';
$db_name = getenv('DB_NAME') ?: 'my_app_db';
$db_user = getenv('DB_USER') ?: 'app_user';
$db_password = getenv('DB_PASSWORD') ?: '';

// Check if required environment variables are set
if (empty($db_password)) {
    $result = [
        'timestamp' => date('Y-m-d H:i:s'),
        'tests' => [
            'connection' => ['status' => 'error', 'message' => 'Database password not set']
        ],
        'overall_status' => 'error'
    ];
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}

$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

try {
    // Test 1: Connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", 
                   $db_user, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $result['tests']['connection'] = ['status' => 'success', 'message' => 'Database connected'];
    
    // Test 2: Table existence
    $tables = ['users', 'posts'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result['tests']["table_$table"] = $stmt->rowCount() > 0
            ? ['status' => 'success', 'message' => "Table '$table' exists"]
            : ['status' => 'error', 'message' => "Table '$table' not found"];
    }
    
    // Test 3: Data query (only if tables exist)
    if ($result['tests']['table_users']['status'] === 'success') {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $result['tests']['data_query'] = [
            'status' => 'success',
            'message' => "Users in DB: " . $stmt->fetch()['total']
        ];
    } else {
        $result['tests']['data_query'] = ['status' => 'error', 'message' => 'Users table not found'];
    }
    
    // Test 4: Write + delete (only if users table exists)
    if ($result['tests']['table_users']['status'] === 'success') {
        $test_username = 'test_user_' . time();
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$test_username, "$test_username@test.com", password_hash('test', PASSWORD_DEFAULT)]);
        $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
        $stmt->execute([$test_username]);
        $result['tests']['write_delete'] = ['status' => 'success', 'message' => 'Write/delete test passed'];
    } else {
        $result['tests']['write_delete'] = ['status' => 'error', 'message' => 'Users table not found'];
    }
    
    $result['overall_status'] = 'success';
    
} catch (Exception $e) {
    $result['tests']['connection'] = ['status' => 'error', 'message' => $e->getMessage()];
    $result['overall_status'] = 'error';
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
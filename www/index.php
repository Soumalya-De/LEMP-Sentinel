<?php
// Load environment variables using getenv() - more reliable in Docker
$db_host = getenv('DB_HOST') ?: 'mysql';
$db_name = getenv('DB_NAME') ?: 'my_app_db';
$db_user = getenv('DB_USER') ?: 'app_user';
$db_password = getenv('DB_PASSWORD') ?: '';

// Check if required environment variables are set
if (empty($db_password)) {
    $db_status = "Connection Failed - Password Not Set";
    $db_color = "#dc3545";
    $user_count = $post_count = 0;
    $posts = [];
} else {
    // Error reporting: only display errors in development
    $isDev = (getenv('APP_ENV') ?: 'development') === 'development';
    error_reporting(E_ALL);
    ini_set('display_errors', $isDev ? '1' : '0');

    // Function to create database connection
    function getConnection() {
        global $db_host, $db_name, $db_user, $db_password;
        
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", 
                          $db_user, $db_password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Get connection and test database
    $pdo = getConnection();
    $db_status = $pdo ? "Connected" : "Connection Failed";
    $db_color = $pdo ? "#28a745" : "#dc3545";

    // Fetch sample data if connected
    $users = [];
    $posts = [];
    if ($pdo) {
        try {
            // Use prepared statements for better security
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
            $stmt->execute();
            $user_count = $stmt->fetch()['count'];
            
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE status = 'published'");
            $stmt->execute();
            $post_count = $stmt->fetch()['count'];
            
            $stmt = $pdo->prepare("SELECT u.username, p.title, p.created_at 
                                  FROM posts p 
                                  JOIN users u ON p.user_id = u.id 
                                  WHERE p.status = 'published' 
                                  ORDER BY p.created_at DESC 
                                  LIMIT 3");
            $stmt->execute();
            $posts = $stmt->fetchAll();
        } catch (Exception $e) {
            $user_count = $post_count = 0;
            $posts = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEMP Stack Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f4f4;
        }
        .status {
            padding: 10px;
            color: white;
            font-weight: bold;
            background-color: <?= $db_color ?>;
            display: inline-block;
            border-radius: 5px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .posts li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>🐳 Dockerized LEMP Stack Dashboard</h1>
    <div class="card">
        <h2>Database Connection</h2>
        <div class="status"><?= $db_status ?></div>
        <?php if ($pdo): ?>
            <p><strong>Users:</strong> <?= $user_count ?? 0 ?></p>
            <p><strong>Posts:</strong> <?= $post_count ?? 0 ?></p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>System Information</h2>
        <p><strong>PHP Version:</strong> <?= PHP_VERSION ?></p>
        <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Nginx' ?></p>
        <p><strong>Environment:</strong> <?= getenv('APP_ENV') ?: 'development' ?></p>
        <p><strong>Timestamp:</strong> <?= date('Y-m-d H:i:s') ?></p>
    </div>
    
    <div class="card">
        <h2>Recent Posts</h2>
        <ul class="posts">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <li>
                        <strong><?= htmlspecialchars($post['title']) ?></strong> 
                        by <?= htmlspecialchars($post['username']) ?> 
                        (<?= date('M j, Y', strtotime($post['created_at'])) ?>)
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No posts found or DB unavailable.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
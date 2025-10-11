<?php
// Restrict phpinfo() to development mode only
if ((getenv('APP_ENV') ?: 'development') !== 'development') {
    http_response_code(403);
    die('Access Denied: PHP Info only available in development mode');
}

// Set security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

phpinfo();
?>
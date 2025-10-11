#!/usr/bin/env php
<?php
// Simple CLI to generate password_hash() (bcrypt) for use in mysql/init.sql
// Usage:
//   php scripts/bcrypt.php "your_password"
//   echo -n "your_password" | php scripts/bcrypt.php

$password = null;
if (isset($argv[1])) {
    $password = $argv[1];
} else {
    // Read from STDIN if no arg
    $stdin = fopen('php://stdin', 'r');
    $password = trim(stream_get_contents($stdin));
    fclose($stdin);
}

if ($password === null || $password === '') {
    fwrite(STDERR, "Error: password is required.\n");
    fwrite(STDERR, "Usage: php scripts/bcrypt.php 'your_password'\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
if ($hash === false) {
    fwrite(STDERR, "Error: failed to generate hash.\n");
    exit(2);
}

echo $hash . PHP_EOL;

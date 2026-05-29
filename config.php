<?php
// config.php
session_start();
date_default_timezone_set('Australia/Sydney');

// Enable error reporting to troubleshoot online crashes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Dynamic base URL detection
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    if ($host === 'localhost' || $host === '127.0.0.1') {
        define('BASE_URL', $protocol . $host . '/Project 1/moonlight/');
    } else {
        define('BASE_URL', $protocol . $host . '/');
    }
} else {
    define('BASE_URL', '/');
}

// Dynamic database credentials detection
$db_host = '127.0.0.1';
$db_name = 'moonlight_grocery';
$db_user = 'root';
$db_pass = '';

// Load online credentials from a secure ignored file if it exists
if (file_exists(__DIR__ . '/db_credentials.php')) {
    include __DIR__ . '/db_credentials.php';
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch objects by default for easier syntax
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>

<?php
require_once __DIR__ . '/config.php';

try {
    echo "<h1>Starting Enterprise Database Migration...</h1>";

    // 1. Drop old tables in reverse order of dependencies to avoid constraint violations
    $tables = ['order_items', 'orders', 'products', 'categories', 'users'];
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table");
        echo "<p>Teardown: Dropped table '$table' successfully.</p>";
    }

    // 2. Read and parse database.sql
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("database.sql file not found!");
    }

    $sql = file_get_contents($sqlFile);

    // Remove CREATE DATABASE and USE statements as they fail on shared hosting
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS[^;]+;/i', '', $sql);
    $sql = preg_replace('/USE [^;]+;/i', '', $sql);

    // Split SQL queries by semicolon
    $queries = explode(';', $sql);
    
    $successCount = 0;
    $ignoreCount = 0;

    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) {
            continue;
        }

        try {
            $pdo->exec($query);
            $successCount++;
        } catch (PDOException $e) {
            // Ignore duplicate entry or already exists errors
            if ($e->getCode() == '23000' || strpos($e->getMessage(), '1062') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $ignoreCount++;
            } else {
                throw $e;
            }
        }
    }

    echo "<h2>Migration & Seeding Completed Successfully!</h2>";
    echo "<p>Executed $successCount database queries ($ignoreCount duplicates skipped).</p>";
    echo "<p>Admin user created: <strong>admin@moonlight.com</strong> (Password: <strong>password</strong>)</p>";
    echo "<p>Customer user created: <strong>customer@moonlight.com</strong> (Password: <strong>password</strong>)</p>";
    echo "<p><a href='index.php'>Go to Storefront Homepage</a></p>";

    // Self-delete for security
    unlink(__FILE__);
    echo "<p>Security Cleanup: Seeding script has securely self-deleted.</p>";
} catch (Exception $e) {
    die("<h1 style='color:red;'>Migration Failed!</h1><p>" . $e->getMessage() . "</p>");
}
?>

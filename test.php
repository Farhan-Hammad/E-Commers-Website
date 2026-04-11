<?php
// Show all errors during development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config/database.php';

try {
    $db = db();
    echo "✅ Database connected successfully!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

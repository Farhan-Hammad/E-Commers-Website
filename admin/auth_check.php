<?php
// admin/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/User.php';

$user = new User();

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: /E-Commers-Website/login.php');
    exit;
}

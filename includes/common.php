<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function hasRole($role) {
    return getCurrentUserRole() === $role;
}

function requireAuth() {
    if (!isLoggedIn()) {
        redirect('../auth/login.php');
    }
}

function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        redirect('../index.php');
    }
}

?>

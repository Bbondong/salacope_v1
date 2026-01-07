<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier si l'utilisateur est connecté
$isLoggedIn = false;
$userData = null;

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $isLoggedIn = true;
    $userData = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'name' => $_SESSION['name'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'user_type' => 'admin',
        'login_time' => $_SESSION['login_time'] ?? null
    ];
} elseif (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in'] === true) {
    $isLoggedIn = true;
    $userData = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'name' => $_SESSION['name'] ?? null,
        'user_type' => 'client',
        'login_time' => $_SESSION['login_time'] ?? null
    ];
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'is_logged_in' => $isLoggedIn,
    'user' => $userData,
    'session_id' => session_id(),
    'timestamp' => time()
]);
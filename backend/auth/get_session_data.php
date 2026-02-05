<?php
// backend/auth/get_session_data.php

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

// Vérifier si l'utilisateur a une session de vérification
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session de vérification non trouvée',
        'redirect' => './index.php'
    ]);
    exit();
}

// Préparer les données de session
$sessionData = [
    'success' => true,
    'session' => [
        'user_id' => $_SESSION['user_id'] ?? null,
        'client_nom' => $_SESSION['client_nom'] ?? null,
        'client_telephone' => $_SESSION['client_telephone'] ?? null,
        'verification_expires' => $_SESSION['verification_expires'] ?? null,
        'whatsapp_sent' => $_SESSION['whatsapp_sent'] ?? false
    ]
];

echo json_encode($sessionData, JSON_UNESCAPED_UNICODE);
?>
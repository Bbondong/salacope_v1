<?php
// backend/auth/get_session_data.php

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

// Vérifier si l'utilisateur a une session de vérification
if (!isset($_SESSION['client_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session de vérification non trouvée',
        'redirect' => './inscription.html'
    ]);
    exit();
}

try {
    // Connexion à la base de données pour récupérer le vrai numéro
    require_once __DIR__ . '/../config.php';
    
    if (!isset($bd) || !($bd instanceof PDO)) {
        throw new Exception("Connexion à la base de données non établie");
    }
    
    // Récupérer le vrai numéro depuis la table client
    $clientId = $_SESSION['user_id'];
    $query = "SELECT tel FROM client WHERE id_client = :client_id";
    $stmt = $bd->prepare($query);
    $stmt->execute(['client_id' => $clientId]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception("Client non trouvé dans la base de données");
    }
    
    // Récupérer le vrai numéro
    $realPhone = $client['tel'];
    
    // Préparer les données de session
    $sessionData = [
        'success' => true,
        'session' => [
            'client_id' => $_SESSION['client_id'] ?? null,
            'client_nom' => $_SESSION['client_nom'] ?? null,
            'client_telephone' => $realPhone, // VRAI NUMÉRO ICI
            'verification_expires' => $_SESSION['verification_expires'] ?? null,
            'whatsapp_sent' => $_SESSION['whatsapp_sent'] ?? false
        ]
    ];

    echo json_encode($sessionData, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Erreur get_session_data: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de chargement des données',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
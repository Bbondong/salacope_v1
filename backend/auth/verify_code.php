<?php
// backend/auth/verify_code.php

session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Vérifier la session
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expirée. Veuillez vous réinscrire.'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Récupérer les données JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalide'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Vérifier le code
if (!isset($input['verification_code']) || empty($input['verification_code'])) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Code de vérification requis'], JSON_UNESCAPED_UNICODE);
    exit();
}

$enteredCode = trim($input['verification_code']);
$clientId = $_SESSION['client_id'];

try {
    // Connexion à la base de données
    require_once __DIR__ . '/../config.php';
    
    if (!isset($bd) || !($bd instanceof PDO)) {
        throw new Exception("Connexion à la base de données non établie");
    }
    
    // Vérifier si le code est valide
    // Note: Dans votre inscription, vous avez hashé le code avec password_hash()
    $checkCodeQuery = "SELECT vc.*, c.type_client, c.statut 
                      FROM verification_codes vc 
                      INNER JOIN client c ON vc.user_id = c.id_client 
                      WHERE vc.user_id = :client_id 
                      AND vc.statut = 'pending'
                      AND vc.expires_at > NOW()";
    
    $checkCodeStmt = $bd->prepare($checkCodeQuery);
    $checkCodeStmt->execute(['client_id' => $clientId]);
    $codeRecord = $checkCodeStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$codeRecord) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Aucun code valide trouvé. Le code a peut-être expiré.'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // Vérifier le code avec password_verify()
    if (!password_verify($enteredCode, $codeRecord['code'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Code incorrect'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // Démarrer une transaction
    $bd->beginTransaction();
    
    // 1. Mettre à jour le statut du code
    $updateCodeQuery = "UPDATE verification_codes 
                       SET statut = 'verified'
                       WHERE id = :code_id";
    
    $updateCodeStmt = $bd->prepare($updateCodeQuery);
    $updateCodeStmt->execute(['code_id' => $codeRecord['id']]);
    
    // 2. Mettre à jour le statut du client de 'new' à 'actif'
    $updateClientQuery = "UPDATE client 
                         SET statut = 'actif'
                         WHERE id_client = :client_id 
                         AND statut = 'new'";
    
    $updateClientStmt = $bd->prepare($updateClientQuery);
    $clientUpdated = $updateClientStmt->execute(['client_id' => $clientId]);
    
    if (!$clientUpdated) {
        throw new Exception("Échec de l'activation du compte");
    }
    
    // 3. Récupérer le type de client pour la redirection
    $getClientQuery = "SELECT type_client FROM client WHERE id_client = :client_id";
    $getClientStmt = $bd->prepare($getClientQuery);
    $getClientStmt->execute(['client_id' => $clientId]);
    $client = $getClientStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception("Client non trouvé");
    }
    
    $userType = $client['type_client']; // 'client', 'vendeur', ou 'admin'
    
    // 4. Mettre à jour la session
    $_SESSION['client_verified'] = true;
    $_SESSION['client_statut'] = 'actif';
    $_SESSION['client_type'] = $userType;
    
    // 5. Valider la transaction
    $bd->commit();
    
    // 6. Préparer la réponse
    $response = [
        'success' => true,
        'message' => 'Compte vérifié et activé avec succès !',
        'user_type' => $userType,
        'redirect_info' => [
            'client' => '/clients/index.php',
            'vendeur' => '/vendeur/index.php',
            'admin' => '/admin/index.php'
        ]
    ];
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    if (isset($bd) && $bd->inTransaction()) {
        $bd->rollBack();
    }
    
    ob_end_clean();
    error_log("PDOException verify_code: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données lors de la vérification'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    if (isset($bd) && $bd->inTransaction()) {
        $bd->rollBack();
    }
    
    ob_end_clean();
    error_log("Exception verify_code: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

exit();
?>
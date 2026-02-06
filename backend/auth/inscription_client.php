<?php
// backend/auth/inscription_client.php

// ===================== CONFIGURATION =====================
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

// Toujours retourner du JSON
header('Content-Type: application/json; charset=utf-8');

// ===================== FONCTIONS UTILITAIRES =====================
function sendResponse($success, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

function sendError($message, $statusCode = 500, $details = null) {
    error_log("❌ inscription_client.php - $message" . ($details ? " - " . json_encode($details) : ""));
    
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// ===================== VALIDATION =====================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Méthode non autorisée. Utilisez POST.', 405);
}

// Vérifier Content-Type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') === false) {
    sendError('Content-Type doit être application/json', 400);
}

// Lire les données
$inputJSON = file_get_contents('php://input');
if (empty($inputJSON)) {
    sendError('Aucune donnée reçue', 400);
}

$input = json_decode($inputJSON, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendError('JSON invalide: ' . json_last_error_msg(), 400);
}

// ===================== VALIDATION DES DONNÉES =====================
if (!isset($input['accountType']) || $input['accountType'] !== 'acheteur') {
    sendError('Type de compte invalide', 400);
}

if (!isset($input['user']) || !is_array($input['user'])) {
    sendError('Données utilisateur manquantes', 400);
}

$user = $input['user'];
$requiredFields = ['nom', 'postnom', 'prenom', 'telephone'];

foreach ($requiredFields as $field) {
    if (empty($user[$field] ?? '')) {
        sendError("Le champ '$field' est requis", 400);
    }
}

// Valider le téléphone
$telephone = trim($user['telephone']);
if (!preg_match('/^\+243\s?\d{2}\s?\d{3}\s?\d{4}$/', $telephone)) {
    sendError('Format de téléphone invalide. Ex: +243 81 123 4567', 400);
}

// Valider le mot de passe
if (empty($input['password'] ?? '')) {
    sendError('Mot de passe requis', 400);
}

$password = $input['password'];
if (strlen($password) < 8) {
    sendError('Le mot de passe doit avoir au moins 8 caractères', 400);
}

// ===================== CONNEXION BASE DE DONNÉES =====================
try {
    // Inclure la configuration
    require_once __DIR__ . '/../config.php';
    
    // Vérifier si la connexion existe
    if (!isset($bd) || !($bd instanceof PDO)) {
        sendError('Connexion base de données non initialisée', 500);
    }
    
    // ===================== VÉRIFIER SI LE TÉLÉPHONE EXISTE =====================
    $checkQuery = "SELECT id_client FROM client WHERE telephone = :telephone LIMIT 1";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute([':telephone' => $telephone]);
    
    if ($checkStmt->rowCount() > 0) {
        sendError('Ce numéro de téléphone est déjà utilisé', 409);
    }
    
    // ===================== CRÉER L'UTILISATEUR =====================
    // Hasher le mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Générer un ID client unique
    $id_client = 'CLI_' . date('YmdHis') . '_' . substr(md5(uniqid()), 0, 8);
    
    // Insérer dans la base
    $insertQuery = "INSERT INTO client (
        id_client, nom, postnom, prenom, telephone, 
        mot_de_passe, statut, date_creation
    ) VALUES (
        :id_client, :nom, :postnom, :prenom, :telephone,
        :mot_de_passe, 'new', NOW()
    )";
    
    $insertStmt = $bd->prepare($insertQuery);
    $insertData = [
        ':id_client' => $id_client,
        ':nom' => trim($user['nom']),
        ':postnom' => trim($user['postnom']),
        ':prenom' => trim($user['prenom']),
        ':telephone' => $telephone,
        ':mot_de_passe' => $passwordHash
    ];
    
    if (!$insertStmt->execute($insertData)) {
        sendError('Erreur lors de la création du compte', 500);
    }
    
    // ===================== GÉNÉRER LE CODE DE VÉRIFICATION =====================
    $verificationCode = sprintf('%06d', mt_rand(1, 999999));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    
    $codeQuery = "INSERT INTO verification_codes (
        user_id, code, statut, expires_at, created_at
    ) VALUES (
        :user_id, :code, 'pending', :expires_at, NOW()
    )";
    
    $codeStmt = $bd->prepare($codeQuery);
    $codeStmt->execute([
        ':user_id' => $id_client,
        ':code' => $verificationCode,
        ':expires_at' => $expiresAt
    ]);
    
    // ===================== PRÉPARER LE MESSAGE WHATSAPP DE DEMANDE =====================
    $userName = trim($user['prenom']) . ' ' . trim($user['nom']);
    $cleanPhone = preg_replace('/[^0-9]/', '', $telephone);
    
    // Message pour l'utilisateur (demande d'inscription)
    $demandeMessage = "Bonjour {$userName},\n\n";
    $demandeMessage .= "Vous avez demandé à vous inscrire sur l'application Salacoop.\n\n";
    $demandeMessage .= "✅ Votre compte a été créé avec succès.\n";
    $demandeMessage .= "📱 Vous recevrez votre code de vérification dans quelques instants.\n\n";
    $demandeMessage .= "Merci,\nL'équipe Salacoop";
    
    $encodedDemande = urlencode($demandeMessage);
    $whatsappDemandeUrl = "https://wa.me/{$cleanPhone}?text={$encodedDemande}";
    
    // ===================== LANCER L'ENVOI DU CODE APRÈS 10 SECONDES =====================
    // Données pour l'envoi différé du code
    $whatsappData = [
        'client_id' => $id_client,
        'verification_code' => $verificationCode,
        'phone' => $telephone,
        'name' => $userName,
        'delay_seconds' => 10
    ];
    
    // URL du script d'envoi différé
    $delayedUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/backend/sendsms/delayed_sender.php';
    
    // Lancer l'envoi en arrière-plan sans attendre
    $ch = curl_init($delayedUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($whatsappData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_TIMEOUT => 1, // Timeout court pour ne pas bloquer
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_FORBID_REUSE => true,
    ]);
    
    // Exécuter en arrière-plan (ignorer le résultat)
    curl_exec($ch);
    curl_close($ch);
    
    // ===================== PRÉPARER LA RÉPONSE POUR LE FRONTEND =====================
    $redirectUrl = './double_authen.php?client=' . urlencode($id_client);
    
    // Démarrer la session pour stocker les informations
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['client_id'] = $id_client;
    $_SESSION['verification_pending'] = true;
    $_SESSION['verification_started'] = time();
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_phone'] = $telephone;
    $_SESSION['whatsapp_scheduled'] = true;
    $_SESSION['code_send_time'] = time() + 10; // Code envoyé dans 10 secondes
    session_write_close();
    
    // ===================== RÉPONSE DE SUCCÈS =====================
    $responseData = [
        'message' => 'Compte créé avec succès !',
        'client_id' => $id_client,
        'whatsapp' => [
            'demande_url' => $whatsappDemandeUrl,
            'demande_message' => 'Message de confirmation envoyé sur WhatsApp',
            'code_message' => 'Votre code de vérification sera envoyé dans 10 secondes',
            'code' => $verificationCode, // Pour debug seulement
            'expires_at' => $expiresAt
        ],
        'redirect' => $redirectUrl,
        'verification' => [
            'code_generated' => true,
            'expires_in' => '30 minutes',
            'delayed_send' => true,
            'delay_seconds' => 10,
            'status' => 'pending'
        ],
        'user_info' => [
            'name' => $userName,
            'phone' => $telephone
        ],
        'instructions' => [
            'step1' => 'Un message de confirmation a été envoyé sur WhatsApp',
            'step2' => 'Votre code de vérification arrivera dans 10 secondes',
            'step3' => 'Utilisez ce code sur la page suivante'
        ]
    ];
    
    sendResponse(true, $responseData, 201);
    
} catch (PDOException $e) {
    error_log("❌ PDOException inscription_client.php: " . $e->getMessage());
    
    // Détails pour debug
    $details = [
        'error_code' => $e->getCode(),
        'error_message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    
    sendError('Erreur base de données', 500, $details);
    
} catch (Exception $e) {
    error_log("❌ Exception inscription_client.php: " . $e->getMessage());
    
    $details = [
        'error_message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    
    sendError('Erreur inattendue', 500, $details);
}
?>
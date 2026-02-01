<?php
// backend/auth/inscription_client.php

// Désactiver la mise en cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Permettre les requêtes CORS si nécessaire
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Traiter les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée. Utilisez POST.'
    ]);
    exit();
}

// Vérifier le Content-Type
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if ($contentType !== 'application/json') {
    http_response_code(415);
    echo json_encode([
        'success' => false,
        'message' => 'Content-Type doit être application/json'
    ]);
    exit();
}

// Récupérer les données JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Vérifier si le JSON est valide
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'JSON invalide: ' . json_last_error_msg()
    ]);
    exit();
}

// Vérifier les données requises
if (!isset($input['accountType']) || $input['accountType'] !== 'acheteur') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Type de compte invalide'
    ]);
    exit();
}

if (!isset($input['user']) || !is_array($input['user'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Données utilisateur manquantes'
    ]);
    exit();
}

if (!isset($input['password']) || strlen($input['password']) < 6) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Mot de passe invalide (minimum 6 caractères)'
    ]);
    exit();
}

// Récupérer les données utilisateur
$user = $input['user'];
$password = $input['password'];

// Valider les champs obligatoires
$requiredFields = ['nom', 'postnom', 'prenom', 'telephone'];
foreach ($requiredFields as $field) {
    if (empty($user[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Le champ '$field' est obligatoire"
        ]);
        exit();
    }
}

// Nettoyer les données
$nom = htmlspecialchars(trim($user['nom']));
$postnom = htmlspecialchars(trim($user['postnom']));
$prenom = htmlspecialchars(trim($user['prenom']));
$telephone = htmlspecialchars(trim($user['telephone']));

// Valider le format du téléphone (format congolais simplifié)
if (!preg_match('/^\+243\s?\d{2}\s?\d{3}\s?\d{4}$/', $telephone)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Format de téléphone invalide. Utilisez: +243 XX XXX XXXX'
    ]);
    exit();
}

// Connexion à la base de données
require_once __DIR__ . '/../config.php';

try {
    // Commencer une transaction
    $bd->beginTransaction();

    // 1. Vérifier si le téléphone existe déjà
    $checkQuery = "SELECT id_client FROM client WHERE tel = :telephone";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['telephone' => $telephone]);
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Ce numéro de téléphone est déjà utilisé'
        ]);
        $bd->rollBack();
        exit();
    }

    // 2. Générer un code de vérification à 6 chiffres
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Définir la date d'expiration (30 minutes)
    $expirationDate = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // 3. Hacher le mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insérer le client dans la base de données
    $insertQuery = "
        INSERT INTO client
        (tel, password, nom, post_nom, prenom, type_client) 
        VALUES 
        (:telephone, :mot_de_passe, :nom, :postnom, :prenom, 'client')
    ";
    
    $insertStmt = $bd->prepare($insertQuery);
    $insertStmt->execute([
        'telephone' => $telephone,
        'mot_de_passe' => $passwordHash,
        'nom' => $nom,
        'postnom' => $postnom,
        'prenom' => $prenom
    ]);

    $clientId = $bd->lastInsertId();

    // 5. Enregistrer le code de vérification dans une table dédiée pour le suivi
    $codeQuery = "
        INSERT INTO verification_codes 
        (user_id, code, expires_at, status, created_at) 
        VALUES 
        (:client_id, :code, :expiration_date, 'pending', NOW())
    ";
    $codeStmt = $bd->prepare($codeQuery);
    $codeStmt->execute([
        'client_id' => $clientId,
        'code' => $verificationCode,
        'expiration_date' => $expirationDate
    ]);

    // 6. Planifier l'envoi du code après 30 secondes
    $backgroundScript = __DIR__ . '/../sendsms/send_whatsapp_background.php';
    $command = "php " . escapeshellarg($backgroundScript) . " " . 
               escapeshellarg($clientId) . " " . 
               escapeshellarg($verificationCode) . " " . 
               escapeshellarg($telephone) . " " . 
               escapeshellarg($prenom) . " > /dev/null 2>&1 &";
    
    // Exécuter en arrière-plan après 30 secondes
    shell_exec("sleep 30 && " . $command);

    // 7. Préparer le message WhatsApp que l'utilisateur doit envoyer
    $userMessage = "Bonjour, je m'appelle {$prenom} {$nom}. Je souhaite recevoir mon code de double authentification pour mon compte client.";
    
    // Votre numéro WhatsApp (celui qui recevra le message)
    $yourWhatsAppNumber = "243962763130"; // REMPLACEZ PAR VOTRE NUMÉRO
    
    // URL WhatsApp pour envoyer le message à VOTRE numéro
    $whatsappUrl = "https://wa.me/" . $yourWhatsAppNumber . "?text=" . urlencode($userMessage);

    // 8. Préparer la réponse avec redirection WhatsApp
    $responseData = [
        'success' => true,
        'message' => 'Compte créé avec succès. Veuillez envoyer un message WhatsApp pour recevoir votre code.',
        'client' => [
            'id' => $clientId,
            'nom' => $nom,
            'postnom' => $postnom,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'statut' => 'client',
            'verification_pending' => true
        ],
        'verification' => [
            'pending' => true,
            'wait_time' => 30, // secondes avant envoi du code
            'whatsapp_message' => $userMessage,
            'whatsapp_url' => $whatsappUrl
        ],
        'whatsapp_redirect' => [
            'immediate' => true,
            'url' => $whatsappUrl,
            'message' => 'Ouvrez WhatsApp pour envoyer le message',
            'auto_redirect' => true
        ]
    ];

    // 9. Démarrer une session
    session_start();
    $_SESSION['client_id'] = $clientId;
    $_SESSION['client_nom'] = $nom . ' ' . $prenom;
    $_SESSION['client_telephone'] = $telephone;
    $_SESSION['account_type'] = 'client';
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['verification_pending'] = true;
    $_SESSION['verification_expires'] = $expirationDate;

    // Valider la transaction
    $bd->commit();

    // 10. Envoyer la réponse
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($responseData);

} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($bd) && $bd->inTransaction()) {
        $bd->rollBack();
    }
    
    error_log("Erreur lors de l'inscription: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création du compte: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($bd) && $bd->inTransaction()) {
        $bd->rollBack();
    }
    
    error_log("Erreur générale: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue: ' . $e->getMessage()
    ]);
}

// Fermer la connexion
if (isset($bd)) {
    $bd = null;
}
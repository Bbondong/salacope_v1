<?php
// backend/auth/inscription_client.php

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si le fichier config.php existe
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Fichier de configuration manquant',
        'path' => $configPath
    ]);
    exit();
}

// Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalide']);
    exit();
}

// Vérifier les données requises
if (!isset($input['accountType'], $input['password'], $input['user'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

if ($input['accountType'] !== 'acheteur') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Type de compte invalide']);
    exit();
}

$user = $input['user'];
$password = $input['password'];

// Vérifier les champs obligatoires
$required = ['nom', 'postnom', 'prenom', 'telephone'];
foreach ($required as $field) {
    if (empty($user[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Le champ $field est obligatoire"]);
        exit();
    }
}

// Nettoyer les données
$nom = trim($user['nom']);
$postnom = trim($user['postnom']);
$prenom = trim($user['prenom']);
$telephone = trim($user['telephone']);

// Normaliser le téléphone (enlever les espaces)
$telephoneClean = preg_replace('/\s+/', '', $telephone);

// Connexion à la base de données
require_once __DIR__ . '/../config.php';

try {
    // Vérifier la connexion à la base de données
    if (!isset($bd)) {
        throw new Exception("Connexion à la base de données non établie");
    }

    // Démarrer une transaction
    $bd->beginTransaction();

    // 1. Vérifier si le téléphone existe déjà
    $checkQuery = "SELECT id_client FROM client WHERE tel = :telephone";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['telephone' => $telephoneClean]);
    
    if ($checkStmt->rowCount() > 0) {
        $bd->rollBack();
        http_response_code(409);
        echo json_encode([
            'success' => false, 
            'message' => 'Ce numéro de téléphone est déjà utilisé'
        ]);
        exit();
    }

    // 2. Générer un code de vérification
    $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash_code = password_hash($verificationCode, PASSWORD_DEFAULT);
    $expirationDate = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // 3. Hacher le mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insérer dans la table client
    $insertClientQuery = "
        INSERT INTO client (tel, password, nom, post_nom, prenom, type_client, statut) 
        VALUES (:telephone, :mot_de_passe, :nom, :postnom, :prenom, 'client', 'new')
    ";
    
    $insertClientStmt = $bd->prepare($insertClientQuery);
    $insertClientStmt->execute([
        'telephone' => $telephoneClean,
        'mot_de_passe' => $passwordHash,
        'nom' => $nom,
        'postnom' => $postnom,
        'prenom' => $prenom
    ]);

    $clientId = $bd->lastInsertId();

    // 5. Insérer le code dans verification_codes
    $insertCodeQuery = "
        INSERT INTO verification_codes (user_id, code, expires_at, statut) 
        VALUES (:user_id, :code, :expires_at, 'pending')
    ";
    
    $insertCodeStmt = $bd->prepare($insertCodeQuery);
    $codeInserted = $insertCodeStmt->execute([
        'user_id' => $clientId,
        'code' => $hash_code,
        'expires_at' => $expirationDate
    ]);

    if (!$codeInserted) {
        error_log("⚠️ Échec d'insertion dans verification_codes: " . print_r($insertCodeStmt->errorInfo(), true));
    }
    
    // 6. Lancer l'envoi WhatsApp en arrière-plan après 15 secondes
    $name = $nom . ' ' . $prenom;
    
    // Préparer la commande pour exécuter en arrière-plan
    $whatsappScriptPath = __DIR__ . '/../sendsms/send_whatsapp_background.php';
    
    if (file_exists($whatsappScriptPath)) {
        $command = "sleep 15 && php " . escapeshellarg($whatsappScriptPath) . " " . 
                   escapeshellarg($clientId) . " " . 
                   escapeshellarg($verificationCode) . " " . 
                   escapeshellarg($telephoneClean) . " " . 
                   escapeshellarg($name) . " > /dev/null 2>&1 &";
        
        // Exécuter en arrière-plan
        $processId = shell_exec($command);
        error_log("📱 WhatsApp: Envoi programmé pour client {$clientId} dans 15s");
        
        $whatsappStatus = [
            'scheduled' => true,
            'delay_seconds' => 15,
            'process_id' => $processId ? trim($processId) : 'unknown'
        ];
    } else {
        error_log("⚠️ WhatsApp: Fichier non trouvé: {$whatsappScriptPath}");
        $whatsappStatus = [
            'scheduled' => false,
            'error' => 'Fichier WhatsApp non trouvé'
        ];
    }

    // 7. Préparer le lien WhatsApp pour l'utilisateur (logique originale)
    $whatsappMessage = "Bonjour \nL'équipe Salacoop \n\n";
    $whatsappMessage .= "Je souhaite m'inscrire sur l'application Salacoop\n\n";
    $whatsappMessage .= "Veuillez m'envoyer mon code de double authentification.\n";
    $whatsappMessage .= "Ne partagez ce code avec personne.\n\n";
    $whatsappMessage .= "Merci,\nL'équipe Salacoop";
    
    $yourWhatsAppNumber = "243962763130";
    $whatsappUrl = "https://wa.me/" . $yourWhatsAppNumber . "?text=" . urlencode($whatsappMessage);

    // 8. Démarrer la session
    session_start();
    session_regenerate_id(true);
    
    $_SESSION['client_id'] = $clientId;
    $_SESSION['client_nom'] = $name;
    $_SESSION['client_telephone'] = $telephoneClean;
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['verification_expires'] = $expirationDate;

    // 9. Valider la transaction
    $bd->commit();

    // 10. Préparer la réponse
    $response = [
        'success' => true,
        'message' => 'Compte créé avec succès ! Un code de vérification vous sera envoyé par WhatsApp dans 30 secondes.',
        'data' => [
            'client_id' => $clientId,
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephoneClean,
            'verification_code' => $verificationCode, // Pour debug seulement
            'code_inserted' => $codeInserted,
            'whatsapp_scheduled' => $whatsappStatus['scheduled']
        ],
        'whatsapp' => [
            'url' => $whatsappUrl,
            'message' => 'Ouvrez WhatsApp pour contacter le support si nécessaire',
            'auto_send' => $whatsappStatus
        ],
        'redirect' => './double_authen.php'
    ];

    http_response_code(201);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($bd) && $bd->inTransaction()) {
        $bd->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données',
        'error' => $e->getMessage(),
        'error_info' => isset($insertCodeStmt) ? $insertCodeStmt->errorInfo() : null
    ]);
    
} catch (Exception $e) {
    if (isset($bd) && $bd->inTransaction()) {
        $bd->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur générale',
        'error' => $e->getMessage()
    ]);
}

exit();
?>
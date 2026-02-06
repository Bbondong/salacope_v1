<?php
// backend/auth/inscription_client.php

// DÉBUT - Capturer toute sortie pour éviter la corruption JSON
ob_start();

// Activer l'affichage des erreurs MAIS les envoyer aux logs, pas à la sortie
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Vérifier si le fichier config.php existe
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Fichier de configuration manquant'
    ], JSON_UNESCAPED_UNICODE);
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

// Récupérer les données JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalide'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Vérifier les données requises
if (!isset($input['accountType'], $input['password'], $input['user'])) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes'], JSON_UNESCAPED_UNICODE);
    exit();
}

if ($input['accountType'] !== 'acheteur') {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Type de compte invalide'], JSON_UNESCAPED_UNICODE);
    exit();
}

$user = $input['user'];
$password = $input['password'];

// Vérifier les champs obligatoires
$required = ['nom', 'postnom', 'prenom', 'telephone'];
foreach ($required as $field) {
    if (empty($user[$field])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Le champ $field est obligatoire"], JSON_UNESCAPED_UNICODE);
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
try {
    require_once __DIR__ . '/../config.php';
    
    // Vérifier la connexion à la base de données
    if (!isset($bd) || !($bd instanceof PDO)) {
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
        ob_end_clean();
        http_response_code(409);
        echo json_encode([
            'success' => false, 
            'message' => 'Ce numéro de téléphone est déjà utilisé'
        ], JSON_UNESCAPED_UNICODE);
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
    $result = $insertClientStmt->execute([
        'telephone' => $telephoneClean,
        'mot_de_passe' => $passwordHash,
        'nom' => $nom,
        'postnom' => $postnom,
        'prenom' => $prenom
    ]);

    if (!$result) {
        throw new Exception("Échec de l'insertion du client: " . implode(', ', $insertClientStmt->errorInfo()));
    }

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
    
    // 6. ENVOI WHATSAPP IMMÉDIAT (sans shell_exec - compatible InfinityFree)
    $name = $nom . ' ' . $prenom;
    $whatsappStatus = ['sent' => false, 'method' => 'immediate'];
    $whatsappEnabled = false;
    $whatsappMessage = '';
    
    // Vérifier si le fichier sendwhatsapp.php existe
    $sendWhatsappPath = __DIR__ . '/../sendsms/sendwhatsapp.php';
    
    if (file_exists($sendWhatsappPath)) {
        require_once $sendWhatsappPath;
        
        // Vérifier si les variables WhatsApp sont définies
        if (defined('WHATSAPP_TOKEN') && defined('WHATSAPP_PHONE_NUMBER_ID') && 
            !empty(WHATSAPP_TOKEN) && !empty(WHATSAPP_PHONE_NUMBER_ID)) {
            
            $env = [
                'WHATSAPP_TOKEN' => WHATSAPP_TOKEN,
                'WHATSAPP_PHONE_NUMBER_ID' => WHATSAPP_PHONE_NUMBER_ID
            ];
            
            // Préparer le message
            $message = "Votre code de vérification Salacoop est : *{$verificationCode}*\n\n";
            $message .= "Utilisez ce code pour activer votre compte.\n";
            $message .= "⚠️ Ce code expire dans 30 minutes.\n";
            $message .= "🔒 Ne partagez jamais ce code.\n\n";
            $message .= "Merci,\nL'équipe Salacoop";
            
            try {
                // Envoyer immédiatement
                $result = sendWhatsAppMessage($name, $telephoneClean, $message, $env);
                
                if ($result['success'] ?? false) {
                    $whatsappStatus = [
                        'sent' => true,
                        'method' => 'immediate',
                        'message_id' => $result['message_id'] ?? null
                    ];
                    
                    // CORRECTION : Requête UPDATE correcte avec SET
                    $updateQuery = "UPDATE verification_codes 
                                    SET statut = 'sent'
                                    WHERE user_id = :client_id 
                                    AND statut = 'pending'
                                    AND expires_at > NOW()";
                    
                    $updateStmt = $bd->prepare($updateQuery);
                    $updateStmt->execute(['client_id' => $clientId]);
                    
                    error_log("✅ WhatsApp envoyé immédiatement à {$telephoneClean}");
                    $whatsappEnabled = true;
                    $whatsappMessage = 'Un code de vérification vous a été envoyé par WhatsApp.';
                } else {
                    $whatsappStatus = [
                        'sent' => false,
                        'method' => 'immediate',
                        'error' => $result['error'] ?? 'Erreur inconnue'
                    ];
                    error_log("❌ Échec envoi WhatsApp: " . json_encode($result));
                    $whatsappMessage = 'Échec d\'envoi WhatsApp. Utilisez le lien manuel ci-dessous.';
                }
            } catch (Exception $e) {
                error_log("❌ Exception lors de l'envoi WhatsApp: " . $e->getMessage());
                $whatsappStatus['error'] = $e->getMessage();
                $whatsappMessage = 'Erreur technique WhatsApp. Utilisez le lien manuel.';
            }
        } else {
            error_log("⚠️ Configuration WhatsApp non définie dans config.php");
            $whatsappStatus['error'] = 'Configuration WhatsApp manquante';
            $whatsappMessage = 'WhatsApp non configuré. Contactez le support pour votre code.';
        }
    } else {
        error_log("⚠️ Fichier sendwhatsapp.php non trouvé");
        $whatsappStatus['error'] = 'Fichier sendwhatsapp.php non trouvé';
        $whatsappMessage = 'Système WhatsApp indisponible. Contactez le support.';
    }

    // 7. Préparer le lien WhatsApp pour l'utilisateur (backup manuel)
    $whatsappManualMessage = "Bonjour \nL'équipe Salacoop \n\n";
    $whatsappManualMessage .= "Je souhaite m'inscrire sur l'application Salacoop\n\n";
    $whatsappManualMessage .= "Mon numéro: {$telephoneClean}\n";
    $whatsappManualMessage .= "Nom: {$name}\n\n";
    $whatsappManualMessage .= "Veuillez m'envoyer mon code de double authentification.\n";
    $whatsappManualMessage .= "Ne partagez ce code avec personne.\n\n";
    $whatsappManualMessage .= "Merci,\nL'équipe Salacoop";
    
    $yourWhatsAppNumber = "243962763130";
    $whatsappUrl = "https://wa.me/" . $yourWhatsAppNumber . "?text=" . urlencode($whatsappManualMessage);

    // 8. Démarrer la session
    session_start();
    session_regenerate_id(true);
    
    $_SESSION['client_id'] = $clientId;
    $_SESSION['client_nom'] = $name;
    $_SESSION['client_telephone'] = $telephoneClean;
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['verification_expires'] = $expirationDate;
    $_SESSION['whatsapp_sent'] = $whatsappStatus['sent'];

    // 9. Valider la transaction
    $bd->commit();

    // 10. Préparer la réponse
    $successMessage = 'Compte créé avec succès ! ';
    
    if ($whatsappEnabled && $whatsappStatus['sent']) {
        $successMessage .= 'Un code de vérification vous a été envoyé par WhatsApp.';
    } else {
        $successMessage .= 'Veuillez utiliser le lien WhatsApp ci-dessous pour recevoir votre code.';
    }
    
    $response = [
        'success' => true,
        'message' => $successMessage,
        'data' => [
            'client_id' => $clientId,
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephoneClean,
            'verification_code' => $verificationCode, // À NE PAS AFFICHER EN PRODUCTION
            'code_inserted' => $codeInserted,
            'whatsapp_sent' => $whatsappStatus['sent'],
            'whatsapp_enabled' => $whatsappEnabled
        ],
        'whatsapp' => [
            'url' => $whatsappUrl,
            'message' => $whatsappMessage,
            'status' => $whatsappStatus,
            'manual_required' => !$whatsappStatus['sent'],
            'manual_message' => 'Cliquez pour ouvrir WhatsApp et envoyer le message pré-rempli'
        ],
        'redirect' => './double_authen.php'
    ];

    // Vider le buffer et envoyer la réponse JSON
    ob_end_clean();
    http_response_code(201);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($bd) && $bd->inTransaction()) {
        try {
            $bd->rollBack();
        } catch (Exception $rollbackError) {
            error_log("Erreur lors du rollback: " . $rollbackError->getMessage());
        }
    }
    
    ob_end_clean();
    error_log("PDOException inscription: " . $e->getMessage());
    error_log("PDOException trace: " . $e->getTraceAsString());
    
    // Message d'erreur utilisateur-friendly
    $errorMessage = 'Erreur lors de la création du compte';
    if (strpos($e->getMessage(), 'SQL syntax') !== false) {
        $errorMessage = 'Erreur technique SQL. Notre équipe a été notifiée.';
    } elseif (strpos($e->getMessage(), 'Column not found') !== false) {
        $errorMessage = 'Erreur technique. Vérifiez la structure de la base de données.';
    } elseif (strpos($e->getMessage(), 'SQLSTATE') !== false) {
        $errorMessage = 'Erreur de base de données. Notre équipe technique a été notifiée.';
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $errorMessage,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    if (isset($bd) && $bd->inTransaction()) {
        try {
            $bd->rollBack();
        } catch (Exception $rollbackError) {
            error_log("Erreur lors du rollback: " . $rollbackError->getMessage());
        }
    }
    
    ob_end_clean();
    error_log("Exception inscription: " . $e->getMessage());
    error_log("Exception trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création du compte',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

exit();
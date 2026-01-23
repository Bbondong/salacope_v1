<?php
// backend/auth/inscription.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit();
}

// ==================== CHARGEMENT CONFIGURATION ====================
// Chemin vers config.php (un niveau au-dessus de backend)
$configFile = __DIR__ . '/../config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Configuration manquante']);
    exit();
}

// Charger la configuration - cela va créer la connexion $bd automatiquement
require $configFile;

// Vérifier si la connexion $bd existe
if (!isset($bd) || !($bd instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur connexion base de données']);
    exit();
}

// Chemin vers whatsapp_functions.php
$whatsappFunctionsFile = __DIR__ . '/../sendsms/sendwhatsapp.php';
if (!file_exists($whatsappFunctionsFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fonctions WhatsApp manquantes']);
    exit();
}

require_once $whatsappFunctionsFile;

try {
    $errors = [];

    // ==================== VALIDATION DES DONNÉES ====================
    $accountType = $data['accountType'] ?? null;
    if ($accountType !== 'acheteur') {
        $errors[] = 'Seul le compte acheteur est disponible pour le moment';
    }

    $phone = $data['user']['telephone'] ?? '';
    $nom = $data['user']['nom'] ?? '';
    $postnom = $data['user']['postnom'] ?? '';
    $prenom = $data['user']['prenom'] ?? '';
    $password = $data['password'] ?? '';

    // Champs requis
    if (empty($phone)) $errors[] = 'Numéro de téléphone requis';
    if (empty($nom)) $errors[] = 'Nom requis';
    if (empty($postnom)) $errors[] = 'Post-nom requis';
    if (empty($prenom)) $errors[] = 'Prénom requis';
    if (empty($password)) $errors[] = 'Mot de passe requis';

    // Format téléphone RDC
    if (!empty($phone) && !preg_match('/^(\+243|0)[0-9]{9}$/', $phone)) {
        $errors[] = 'Format de téléphone invalide. Ex: +243812345678 ou 0812345678';
    }

    // Normaliser le numéro pour la recherche
    $normalizedPhone = normalizePhone($phone);
    
    // Vérifier si numéro existe déjà
    if (!empty($phone)) {
        $stmt = $bd->prepare("SELECT id_client FROM client WHERE tel = ?");
        $stmt->execute([$normalizedPhone]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Ce numéro de téléphone est déjà utilisé';
        }
    }

    // Validation mot de passe
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
    }

    // Si erreurs, retourner
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Erreurs de validation',
            'errors' => $errors
        ]);
        exit();
    }

    // Hacher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $dbPhone = $normalizedPhone;

    // ==================== CRÉATION UTILISATEUR ====================
    // Commencer une transaction
    $bd->beginTransaction();
    
    try {
        // 1. Insérer l'utilisateur
        $sql = "INSERT INTO client (tel, password, nom, post_nom, prenom, type_client, created_at) 
                VALUES (:tel, :password, :nom, :postnom, :prenom, 'acheteur', NOW())";
        
        $stmt = $bd->prepare($sql);
        $stmt->execute([
            ':tel' => $dbPhone,
            ':password' => $hashedPassword,
            ':nom' => trim($nom),
            ':postnom' => trim($postnom),
            ':prenom' => trim($prenom)
        ]);

        $userId = $bd->lastInsertId();

        // 2. Générer le code de vérification
        $authCode = generateVerificationCode(4); // Code à 4 chiffres
        
        // 3. Créer la table verification_codes si elle n'existe pas
        createVerificationCodesTable($bd);

        // 4. Stocker le code en base
        $sqlCode = "INSERT INTO verification_codes 
                    (user_id, phone, code, expires_at, created_at) 
                    VALUES (:user_id, :phone, :code, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())";
        
        $stmtCode = $bd->prepare($sqlCode);
        $stmtCode->execute([
            ':user_id' => $userId,
            ':phone' => $dbPhone,
            ':code' => $authCode
        ]);

        // 5. Envoyer le code par WhatsApp
        $fullName = trim($prenom) . ' ' . trim($nom);
        $whatsappResult = sendWhatsAppVerificationCode($phone, $authCode, $fullName);
        
        // 6. Journaliser l'envoi WhatsApp
        logWhatsAppToDatabase($bd, $dbPhone, $authCode, $whatsappResult, $userId);
        
        // Valider la transaction
        $bd->commit();
        
        // Message selon le résultat WhatsApp
        if ($whatsappResult['success']) {
            $whatsappMessage = "✅ Code de vérification envoyé par WhatsApp";
        } else {
            $whatsappMessage = "⚠️ Le code n'a pas pu être envoyé par WhatsApp. Contactez le support.";
            error_log("Échec envoi WhatsApp: " . json_encode($whatsappResult));
        }

        // ==================== RÉPONSE SUCCÈS ====================
        session_start();
        $_SESSION['auth_user_id'] = $userId;
        $_SESSION['auth_phone'] = $dbPhone;
        $_SESSION['auth_created_at'] = time();
        $_SESSION['verification_code'] = $authCode; // Temporaire pour vérification

        echo json_encode([
            'success' => true,
            'message' => 'Compte acheteur créé avec succès. ' . $whatsappMessage,
            'user_id' => $userId,
            'account_type' => 'acheteur',
            'phone' => $dbPhone,
            'whatsapp_sent' => $whatsappResult['success'] ?? false,
            'whatsapp_message_id' => $whatsappResult['message_id'] ?? null,
            'redirect' => '../auth/double_authen.php?phone=' . urlencode($dbPhone),
            'debug_info' => ($config['app']['environment'] === 'development') ? [
                'code' => $authCode,
                'whatsapp_result' => $whatsappResult
            ] : null
        ]);

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $bd->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur base de données',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur serveur',
        'error' => $e->getMessage()
    ]);
}

// ==================== FONCTIONS UTILITAIRES ====================

/**
 * Normalise le numéro de téléphone
 */
function normalizePhone($phone) {
    // Supprimer espaces et caractères spéciaux
    $cleaned = preg_replace('/[^0-9+]/', '', $phone);
    
    // Si commence par +243
    if (strpos($cleaned, '+243') === 0) {
        return $cleaned;
    }
    
    // Si commence par 243 (sans +)
    if (strpos($cleaned, '243') === 0) {
        return '+' . $cleaned;
    }
    
    // Si commence par 0 (format local RDC)
    if (strpos($cleaned, '0') === 0 && strlen($cleaned) == 10) {
        return '+243' . substr($cleaned, 1);
    }
    
    // Par défaut, ajouter +243 pour RDC
    return '+243' . $cleaned;
}

/**
 * Crée la table verification_codes si elle n'existe pas
 */
function createVerificationCodesTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS verification_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        code VARCHAR(10) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        verified TINYINT(1) DEFAULT 0,
        verified_at DATETIME NULL,
        attempts INT DEFAULT 0,
        INDEX idx_phone (phone),
        INDEX idx_expires (expires_at),
        INDEX idx_user (user_id),
        FOREIGN KEY (user_id) REFERENCES client(id_client) ON DELETE CASCADE
    )";
    
    try {
        $pdo->exec($sql);
    } catch (Exception $e) {
        error_log("Erreur création table verification_codes: " . $e->getMessage());
    }
}

/**
 * Journalise l'envoi WhatsApp en base de données
 */
function logWhatsAppToDatabase($pdo, $phone, $code, $result, $userId) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS whatsapp_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            phone VARCHAR(20) NOT NULL,
            message_id VARCHAR(100),
            code_sent VARCHAR(10),
            status VARCHAR(50),
            success TINYINT(1) DEFAULT 0,
            error_message TEXT,
            response_data TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_phone (phone),
            INDEX idx_created (created_at)
        )";
        
        $pdo->exec($sql);
        
        $stmt = $pdo->prepare("INSERT INTO whatsapp_logs 
                              (user_id, phone, message_id, code_sent, status, success, error_message, response_data) 
                              VALUES (:user_id, :phone, :message_id, :code, :status, :success, :error, :response)");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':phone' => $phone,
            ':message_id' => $result['message_id'] ?? null,
            ':code' => $code,
            ':status' => $result['success'] ? 'sent' : 'failed',
            ':success' => $result['success'] ? 1 : 0,
            ':error' => $result['error'] ?? null,
            ':response' => json_encode($result)
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur journalisation WhatsApp: " . $e->getMessage());
    }
}
?>
<?php
// DÉBUT - Configuration InfinityFree compatible
error_reporting(0); // Désactiver les erreurs pour InfinityFree
ini_set('display_errors', 0);

// Fonction de log dans htdocs seulement
function logDebug($message) {
    $logFile = __DIR__ . '/../logs/debug.log';
    // Créer le dossier logs s'il n'existe pas (dans htdocs)
    if (!file_exists(dirname($logFile))) {
        @mkdir(dirname($logFile), 0755, true);
    }
    @file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

logDebug("=== NOUVELLE REQUÊTE LOGIN ===");
logDebug("Méthode: " . $_SERVER['REQUEST_METHOD']);

// Vérifier si session déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers CORS pour API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode POST requise',
        'timestamp' => time()
    ]);
    exit();
}

// Récupérer les données JSON
$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Données JSON requises',
        'timestamp' => time()
    ]);
    exit();
}

$data = json_decode($input, true);

// Validation des données
if (!$data || !isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Username et password requis',
        'timestamp' => time()
    ]);
    exit();
}

$username = trim($data['username']);
$password = trim($data['password']);

logDebug("Tentative login pour: " . $username);

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Champs non vides requis',
        'timestamp' => time()
    ]);
    exit();
}

// Connexion à la base de données
require_once __DIR__ . '/../config.php';

try {
    logDebug("Connexion BD OK, recherche utilisateur...");
    
    // 1. Vérifier dans la table admin - CORRIGÉ : un seul paramètre
    $query = "SELECT admin_id, Num, password, admin_name, admin_role 
              FROM admin 
              WHERE Num = :username 
              LIMIT 1";
    
    logDebug("Requête admin: " . $query);
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    logDebug("Résultats admin: " . $stmt->rowCount());
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        logDebug("Admin trouvé: " . $user['Num']);
        
        // Vérifier le mot de passe
        $passwordValid = password_verify($password, $user['password']);
        logDebug("Password verify: " . ($passwordValid ? 'OK' : 'ECHEC'));
        
        if ($passwordValid) {
            // Session admin
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['Num'];
            $_SESSION['name'] = $user['admin_name'];
            $_SESSION['role'] = $user['admin_role'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            logDebug("✅ Connexion admin réussie pour: " . $user['Num']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion admin réussie',
                'data' => [
                    'user_id' => $user['admin_id'],
                    'username' => $user['Num'],
                    'name' => $user['admin_name'],
                    'role' => $user['admin_role'],
                    'user_type' => 'admin'
                ],
                'redirect' => '/admin/index.php',
                'timestamp' => time()
            ]);
            exit();
        }
    }
    
    // 2. Vérifier dans la table client - CORRIGÉ : un seul paramètre
    $query = "SELECT id_client, tel, password, nom, post_nom, prenom, type_client 
              FROM client 
              WHERE tel = :username 
              LIMIT 1";
    
    logDebug("Requête client: " . $query);
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    logDebug("Résultats client: " . $stmt->rowCount());
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        logDebug("Client trouvé: " . $user['tel']);
        
        // Vérifier le mot de passe
        $passwordValid = false;
        $isPlainText = false;
        
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
            logDebug("Password verify hash: OK");
        } elseif ($password === $user['password']) {
            $passwordValid = true;
            $isPlainText = true;
            logDebug("Password en clair: OK");
        }
        
        if ($passwordValid) {
            // Si mot de passe en clair, le hasher
            if ($isPlainText) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE client SET password = :hashedPassword WHERE id_client = :id";
                $updateStmt = $bd->prepare($updateQuery);
                $updateStmt->execute([
                    ':hashedPassword' => $hashedPassword,
                    ':id' => $user['id_client']
                ]);
                logDebug("Mot de passe hashé et mis à jour");
            }
            
            // Session client
            $_SESSION['user_id'] = $user['id_client'];
            $_SESSION['username'] = $user['tel'];
            $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
            $_SESSION['user_type'] = $user['type_client'];
            $_SESSION['client_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            logDebug("✅ Connexion client réussie pour: " . $user['tel']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion client réussie',
                'data' => [
                    'user_id' => $user['id_client'],
                    'username' => $user['tel'],
                    'name' => $user['nom'] . ' ' . $user['post_nom'],
                    'user_type' => $user['type_client']
                ],
                'redirect' => '/clients/index.php',
                'timestamp' => time()
            ]);
            exit();
        }
    }
    
    // Aucun utilisateur trouvé
    logDebug("❌ Aucun utilisateur trouvé pour: " . $username);
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Identifiants incorrects',
        'timestamp' => time()
    ]);
    exit();
    
} catch(PDOException $e) {
    // Log l'erreur
    logDebug("❌ ERREUR PDO: " . $e->getMessage());
    logDebug("❌ Code: " . $e->getCode());
    logDebug("❌ Fichier: " . $e->getFile());
    logDebug("❌ Ligne: " . $e->getLine());
    
    // Réponse d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage(),
        'error_code' => $e->getCode(),
        'debug' => 'Vérifiez les logs',
        'timestamp' => time()
    ]);
    exit();
} catch(Exception $e) {
    logDebug("❌ ERREUR GENERALE: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'timestamp' => time()
    ]);
    exit();
}
?>
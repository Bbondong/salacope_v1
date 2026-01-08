<?php
// DÉBUT - Configuration InfinityFree compatible
error_reporting(0);
ini_set('display_errors', 0);

// Fonction de log
function logDebug($message) {
    $logFile = __DIR__ . '/../logs/login_debug.log';
    // Créer le dossier logs s'il n'existe pas
    if (!file_exists(dirname($logFile))) {
        @mkdir(dirname($logFile), 0755, true);
    }
    @file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

logDebug("=== NOUVELLE TENTATIVE DE CONNEXION ===");

// Vérifier si session déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    logDebug("Session démarrée");
}

// Headers CORS pour API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    logDebug("Requête OPTIONS traitée");
    exit();
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    logDebug("❌ Mauvaise méthode: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode POST requise',
        'timestamp' => time()
    ]);
    exit();
}

logDebug("Méthode POST OK");

// Récupérer les données JSON
$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    logDebug("❌ Données JSON vides");
    echo json_encode([
        'success' => false,
        'message' => 'Données JSON requises',
        'timestamp' => time()
    ]);
    exit();
}

logDebug("Données JSON reçues: " . substr($input, 0, 100) . "...");

$data = json_decode($input, true);

// Validation des données
if (!$data || !isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    logDebug("❌ Champs manquants dans JSON");
    echo json_encode([
        'success' => false,
        'message' => 'Username et password requis',
        'timestamp' => time()
    ]);
    exit();
}

$username = trim($data['username']);
$password = trim($data['password']);

logDebug("Username reçu: '$username'");
logDebug("Password reçu (longueur): " . strlen($password) . " caractères");

if (empty($username) || empty($password)) {
    http_response_code(400);
    logDebug("❌ Username ou password vide");
    echo json_encode([
        'success' => false,
        'message' => 'Champs non vides requis',
        'timestamp' => time()
    ]);
    exit();
}

logDebug("Champs non vides OK");

// Connexion à la base de données
require_once __DIR__ . '/../config.php';

try {
    logDebug("Connexion BDD établie");
    
    // 1. Vérifier dans la table admin - Num seulement (car pas de colonne username)
    $query = "SELECT *
              FROM admin 
              WHERE Num = :username 
              LIMIT 1";
    
    logDebug("Recherche admin avec Num: '$username'");
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    $adminCount = $stmt->rowCount();
    logDebug("Nombre d'admins trouvés: $adminCount");
    
    if ($adminCount > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        logDebug("✅ ADMIN trouvé: ID=" . $user['admin_id'] . ", Nom=" . $user['admin_name'] . ", Num=" . $user['Num']);
        logDebug("Hash password admin: " . substr($user['password'], 0, 20) . "...");
        
        // Vérifier le mot de passe
        if (password_verify($password, $user['password'])) {
            logDebug("✅ Mot de passe ADMIN CORRECT");
            
            // Session admin
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['Num'];
            $_SESSION['name'] = $user['admin_name'];
            $_SESSION['role'] = $user['admin_role'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            logDebug("✅ Session admin créée - Redirection vers /admin/index.php");
            
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
        } else {
            logDebug("❌ Mot de passe ADMIN INCORRECT");
            logDebug("Password fourni: '$password'");
            logDebug("Hash BD: " . $user['password']);
        }
    } else {
        logDebug("ℹ️ Aucun ADMIN trouvé avec Num: '$username'");
    }
    
    // 2. Vérifier dans la table client - tel seulement (car pas de colonne username)
    $query = "SELECT *
              FROM client 
              WHERE tel = :username 
              LIMIT 1";
    
    logDebug("Recherche client avec tel: '$username'");
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    $clientCount = $stmt->rowCount();
    logDebug("Nombre de clients trouvés: $clientCount");
    
    if ($clientCount > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        logDebug("✅ CLIENT trouvé: ID=" . $user['id_client'] . ", Nom=" . $user['nom'] . " " . $user['post_nom'] . ", Tel=" . $user['tel']);
        logDebug("Hash/Password client: " . substr($user['password'], 0, 20) . "...");
        
        // Vérifier le mot de passe
        $passwordValid = false;
        $isPlainText = false;
        
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
            logDebug("✅ Mot de passe CLIENT CORRECT (hashé)");
        } elseif ($password === $user['password']) {
            $passwordValid = true;
            $isPlainText = true;
            logDebug("✅ Mot de passe CLIENT CORRECT (en clair)");
        } else {
            logDebug("❌ Mot de passe CLIENT INCORRECT");
            logDebug("Password fourni: '$password'");
            logDebug("Password BD: " . $user['password']);
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
                logDebug("🔄 Mot de passe client migré vers hash sécurisé");
            }
            
            // Session client
            $_SESSION['user_id'] = $user['id_client'];
            $_SESSION['username'] = $user['tel'];
            $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
            $_SESSION['user_type'] = $user['type_client'];
            $_SESSION['client_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            logDebug("✅ Session client créée - Redirection vers /clients/index.php");
            
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
    } else {
        logDebug("ℹ️ Aucun CLIENT trouvé avec tel: '$username'");
    }
    
    // Aucun utilisateur trouvé
    logDebug("❌ FINAL: Aucun compte trouvé (ni admin ni client)");
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Identifiants incorrects',
        'timestamp' => time()
    ]);
    exit();
    
} catch(PDOException $e) {
    logDebug("❌ ERREUR PDO: " . $e->getMessage());
    logDebug("❌ Fichier: " . $e->getFile());
    logDebug("❌ Ligne: " . $e->getLine());
    logDebug("❌ Code: " . $e->getCode());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage(),
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

logDebug("=== FIN DE LA TENTATIVE ===");
?>
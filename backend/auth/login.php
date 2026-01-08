<?php
// DÉBUT - Configuration InfinityFree compatible
error_reporting(0);
ini_set('display_errors', 0);

// Fonction de log minimal pour déboguer
function logDebug($message) {
    $logFile = __DIR__ . '/../logs/debug_login.log';
    @file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

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
$password = $data['password']; // NE PAS TRIMMER le mot de passe !

logDebug("=== NOUVELLE TENTATIVE ===");
logDebug("Username reçu: '$username'");
logDebug("Password reçu (longueur): " . strlen($password) . " caractères");

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
    // 1. Vérifier dans la table admin
    $query = "SELECT admin_id, Num, password, admin_name, admin_role 
              FROM admin 
              WHERE Num = :username 
              LIMIT 1";
    
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        logDebug("Admin trouvé: " . $user['Num']);
        logDebug("Hash DB complet: " . $user['password']);
        logDebug("Longueur hash: " . strlen($user['password']) . " caractères");
        
        // Vérifier le mot de passe
        $testPassword = "Test manuel avec même mot de passe: ";
        $testPassword .= password_verify('votre_mot_de_passe', $user['password']) ? 'OK' : 'ECHEC';
        logDebug($testPassword);
        
        $passwordValid = password_verify($password, $user['password']);
        logDebug("password_verify avec données reçues: " . ($passwordValid ? 'OK' : 'ECHEC'));
        
        // Test alternative - afficher les caractères du mot de passe
        logDebug("Caractères password (décimal):");
        for ($i = 0; $i < strlen($password); $i++) {
            logDebug("  [$i]: " . ord($password[$i]) . " ('" . $password[$i] . "')");
        }
        
        if ($passwordValid) {
            // Session admin
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['Num'];
            $_SESSION['name'] = $user['admin_name'];
            $_SESSION['role'] = $user['admin_role'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            logDebug("✅ Connexion admin réussie!");
            
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
            logDebug("❌ Échec password_verify");
            
            // Test avec différentes variations du mot de passe
            $variations = [
                'trim' => trim($password),
                'rtrim' => rtrim($password),
                'ltrim' => ltrim($password),
                'no_change' => $password
            ];
            
            foreach ($variations as $name => $variant) {
                $result = password_verify($variant, $user['password']) ? 'OK' : 'ECHEC';
                logDebug("Test '$name': $result (longueur: " . strlen($variant) . ")");
            }
        }
    } else {
        logDebug("Aucun admin trouvé avec username: '$username'");
    }
    
    // 2. Vérifier dans la table client
    $query = "SELECT id_client, tel, password, nom, post_nom, prenom, type_client 
              FROM client 
              WHERE tel = :username 
              LIMIT 1";
    
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        logDebug("Client trouvé: " . $user['tel']);
        logDebug("Hash client DB: " . $user['password']);
        
        // Vérifier le mot de passe
        $passwordValid = false;
        
        // Essayer password_verify d'abord
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
            logDebug("password_verify client: OK");
        } 
        // Essayer sans trim
        elseif (password_verify(trim($password), $user['password'])) {
            $passwordValid = true;
            logDebug("password_verify avec trim: OK");
        }
        // Essayer en clair
        elseif ($password === $user['password']) {
            $passwordValid = true;
            logDebug("Mot de passe en clair: OK");
        }
        
        if ($passwordValid) {
            // Session client
            $_SESSION['user_id'] = $user['id_client'];
            $_SESSION['username'] = $user['tel'];
            $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
            $_SESSION['user_type'] = $user['type_client'];
            $_SESSION['client_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            logDebug("✅ Connexion client réussie!");
            
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
        logDebug("Aucun client trouvé avec username: '$username'");
    }
    
    // Aucun utilisateur trouvé
    logDebug("❌ FINAL: Aucune connexion réussie");
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Identifiants incorrects',
        'timestamp' => time()
    ]);
    exit();
    
} catch(PDOException $e) {
    logDebug("❌ ERREUR PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données',
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
<?php
// DÉBUT - Configuration InfinityFree compatible
error_reporting(0);
ini_set('display_errors', 0);

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
$password = $data['password'];

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Champs non vides requis',
        'timestamp' => time()
    ]);
    exit();
}

// DEBUG: Vérifier si config.php existe
$config_path = __DIR__ . '/../config.php';
if (!file_exists($config_path)) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur configuration',
        'debug' => 'Fichier config.php introuvable à: ' . $config_path,
        'timestamp' => time()
    ]);
    exit();
}

// Connexion à la base de données
try {
    require_once $config_path;
    
    // DEBUG: Vérifier si $bd existe
    if (!isset($bd)) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur configuration BDD',
            'debug' => 'Variable $bd non définie dans config.php',
            'timestamp' => time()
        ]);
        exit();
    }
    
    // DEBUG: Tester la connexion
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur chargement configuration',
        'debug' => 'Erreur config.php: ' . $e->getMessage(),
        'timestamp' => time()
    ]);
    exit();
}

try {
    // DEBUG: Message de début
    $debug_message = "🔍 Recherche pour: '$username'";
    
    // 1. Vérifier dans la table admin - Num OU admin_name
    $query = "SELECT admin_id, Num, password, admin_name, admin_role 
              FROM admin 
              WHERE Num = :username OR admin_name = :username 
              LIMIT 1";
    
    $debug_message .= " | Requête admin préparée";
    
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    $rowCount = $stmt->rowCount();
    $debug_message .= " | Résultat admin: " . ($rowCount > 0 ? "TROUVÉ" : "NON TROUVÉ");
    
    if ($rowCount > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $debug_message .= " | Admin: " . $user['admin_name'] . " (Num: " . $user['Num'] . ")";
        
        // Vérifier le mot de passe
        if (password_verify($password, $user['password'])) {
            $debug_message .= " | ✅ Mot de passe CORRECT";
            
            // Session admin
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['Num'];
            $_SESSION['name'] = $user['admin_name'];
            $_SESSION['role'] = $user['admin_role'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion admin réussie',
                'debug' => $debug_message,
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
            $debug_message .= " | ❌ Mot de passe INCORRECT";
            
            // DEBUG supplémentaire
            $debug_message .= " | Hash BD: " . substr($user['password'], 0, 30) . "...";
            $debug_message .= " | Longueur: " . strlen($user['password']);
            
            echo json_encode([
                'success' => false,
                'message' => 'Mot de passe incorrect',
                'debug' => $debug_message,
                'timestamp' => time()
            ]);
            exit();
        }
    }
    
    // 2. Vérifier dans la table client - tel OU nom
    $query = "SELECT id_client, tel, password, nom, post_nom, prenom, type_client 
              FROM client 
              WHERE tel = :username OR nom = :username 
              LIMIT 1";
    
    $debug_message .= " | Requête client préparée";
    
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    $rowCount = $stmt->rowCount();
    $debug_message .= " | Résultat client: " . ($rowCount > 0 ? "TROUVÉ" : "NON TROUVÉ");
    
    if ($rowCount > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $debug_message .= " | Client: " . $user['nom'] . " " . $user['post_nom'] . " (Tel: " . $user['tel'] . ")";
        
        // Vérifier le mot de passe
        $passwordValid = false;
        $isPlainText = false;
        
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
            $debug_message .= " | ✅ Mot de passe CORRECT (hashé)";
        } elseif ($password === $user['password']) {
            $passwordValid = true;
            $isPlainText = true;
            $debug_message .= " | ✅ Mot de passe CORRECT (en clair)";
        } else {
            $debug_message .= " | ❌ Mot de passe INCORRECT";
            $debug_message .= " | Hash BD: " . substr($user['password'], 0, 30) . "...";
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
                $debug_message .= " | 🔄 Mot de passe migré vers hash";
            }
            
            // Session client
            $_SESSION['user_id'] = $user['id_client'];
            $_SESSION['username'] = $user['tel'];
            $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
            $_SESSION['user_type'] = $user['type_client'];
            $_SESSION['client_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion client réussie',
                'debug' => $debug_message,
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
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Mot de passe incorrect',
                'debug' => $debug_message,
                'timestamp' => time()
            ]);
            exit();
        }
    }
    
    // Aucun utilisateur trouvé
    $debug_message .= " | ❌ AUCUN COMPTE TROUVÉ";
    
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Identifiants incorrects',
        'debug' => $debug_message,
        'timestamp' => time()
    ]);
    exit();
    
} catch(PDOException $e) {
    $debug_message = "❌ ERREUR PDO: " . $e->getMessage();
    $debug_message .= " | Fichier: " . $e->getFile();
    $debug_message .= " | Ligne: " . $e->getLine();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données',
        'debug' => $debug_message,
        'timestamp' => time()
    ]);
    exit();
} catch(Exception $e) {
    $debug_message = "❌ ERREUR GENERALE: " . $e->getMessage();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'debug' => $debug_message,
        'timestamp' => time()
    ]);
    exit();
}
?>
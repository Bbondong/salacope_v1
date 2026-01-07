<?php
session_start();

// Headers CORS pour API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
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
        'message' => 'Méthode non autorisée. Utilisez POST.',
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
        'message' => 'Nom d\'utilisateur et mot de passe requis',
        'timestamp' => time()
    ]);
    exit();
}

$username = trim($data['username']);
$password = trim($data['password']);

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Les champs ne peuvent pas être vides',
        'timestamp' => time()
    ]);
    exit();
}

// Connexion à la base de données
require_once '../config.php';

try {
    // 1. Vérifier dans la table admin
    $query = "SELECT admin_id, Num, password, admin_name, admin_role FROM admin WHERE Num = :username OR admin_name = :username LIMIT 1";
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe (hashé)
        if (password_verify($password, $user['password'])) {
            // Démarrer la session pour admin
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['Num'];
            $_SESSION['name'] = $user['admin_name'];
            $_SESSION['role'] = $user['admin_role'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Réponse API
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Connexion admin réussie',
                'data' => [
                    'user_id' => $user['admin_id'],
                    'username' => $user['Num'],
                    'name' => $user['admin_name'],
                    'role' => $user['admin_role'],
                    'user_type' => 'admin',
                    'session_id' => session_id()
                ],
                'redirect' => '/admin/index.php',
                'timestamp' => time()
            ]);
            exit();
        }
    }
    
    // 2. Vérifier dans la table client
    $query = "SELECT id_client, tel, password, nom, post_nom, prenom , type_client FROM client WHERE tel = :username OR nom = :username LIMIT 1";
    $stmt = $bd->prepare($query);
    $stmt->execute([':username' => $username]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Si mot de passe en clair, le hasher
            if ($password === $user['password']) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE client SET password = :hashedPassword WHERE id_client = :id";
                $updateStmt = $bd->prepare($updateQuery);
                $updateStmt->execute([
                    ':hashedPassword' => $hashedPassword,
                    ':id' => $user['id_client']
                ]);
            }
            
            // Démarrer la session pour client
            $_SESSION['user_id'] = $user['id_client'];
            $_SESSION['username'] = $user['tel'];
            $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
            $_SESSION['full_name'] = [
                'nom' => $user['nom'],
                'post_nom' => $user['post_nom'],
                'prenom' => $user['prenom']
            ];
            $_SESSION['user_type'] = $user['type_client'];
            $_SESSION['client_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Réponse API
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Connexion client réussie',
                'data' => [
                    'user_id' => $user['id_client'],
                    'username' => $user['tel'],
                    'name' => $user['nom'] . ' ' . $user['post_nom'],
                    'full_name' => [
                        'nom' => $user['nom'],
                        'post_nom' => $user['post_nom'],
                        'prenom' => $user['prenom']
                    ],
                    'user_type' => $user['type_client'],
                    'session_id' => session_id()
                ],
                'redirect' => '/client/dashboard.php',
                'timestamp' => time()
            ]);
            exit();
        }
    }
    
    // Aucun utilisateur trouvé ou mot de passe incorrect
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Identifiants incorrects',
        'timestamp' => time()
    ]);
    exit();
    
} catch(PDOException $e) {
    error_log("Erreur login PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur lors de l\'authentification',
        'timestamp' => time()
    ]);
    exit();
}
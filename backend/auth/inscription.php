<?php
// backend/auth/inscription.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Activer les erreurs pour le développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit();
}

// Chemin vers config.php (depuis backend/auth/)
require_once __DIR__ . '/../../config.php';

try {
    // Connexion à la base de données
    $bd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Validation des données
    $errors = [];
    
    // Vérifier le type de compte (seulement acheteur pour l'instant)
    $accountType = $data['accountType'] ?? null;
    if ($accountType !== 'acheteur') {
        $errors[] = 'Seul le compte acheteur est disponible pour le moment';
    }
    
    // Récupérer les données
    $phone = $data['user']['telephone'] ?? '';
    $nom = $data['user']['nom'] ?? '';
    $postnom = $data['user']['postnom'] ?? '';
    $prenom = $data['user']['prenom'] ?? '';
    $password = $data['password'] ?? '';
    
    // Vérifier les champs obligatoires
    if (empty($phone)) $errors[] = 'Numéro de téléphone requis';
    if (empty($nom)) $errors[] = 'Nom requis';
    if (empty($postnom)) $errors[] = 'Post-nom requis';
    if (empty($prenom)) $errors[] = 'Prénom requis';
    if (empty($password)) $errors[] = 'Mot de passe requis';
    
    // Valider le téléphone
    if (!empty($phone) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $phone)) {
        $errors[] = 'Format de téléphone invalide. Ex: +243 81 234 5678';
    }
    
    // Vérifier si le téléphone existe déjà
    if (!empty($phone)) {
        $stmt = $bd->prepare("SELECT id_client FROM client WHERE tel = ?");
        $stmt->execute([$phone]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Ce numéro de téléphone est déjà utilisé';
        }
    }
    
    // Valider la longueur du mot de passe
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit();
    }
    
    // Hacher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // ========== INSCRIPTION ACHETEUR ==========
    $sql = "INSERT INTO client (tel, password, nom, post_nom, prenom, type_client) 
            VALUES (:tel, :password, :nom, :postnom, :prenom, 'acheteur')";
    
    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ':tel' => $phone,
        ':password' => $hashedPassword,
        ':nom' => $nom,
        ':postnom' => $postnom,
        ':prenom' => $prenom
    ]);
    
    $userId = $bd->lastInsertId();
    
    // Créer une session pour l'utilisateur
    session_start();
    
    // Récupérer les informations de l'utilisateur
    $userSql = "SELECT id_client, tel, nom, post_nom, prenom, type_client FROM client WHERE id_client = ?";
    $stmt = $bd->prepare($userSql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Définir les variables de session
    $_SESSION['user_id'] = $user['id_client'];
    $_SESSION['username'] = $user['tel'];
    $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
    $_SESSION['full_name'] = $user['prenom'] . ' ' . $user['nom'] . ' ' . $user['post_nom'];
    $_SESSION['user_type'] = $user['type_client'];
    $_SESSION['client_logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Compte acheteur créé avec succès',
        'user_id' => $userId,
        'account_type' => 'acheteur',
        'username' => $user['tel'],
        'name' => $user['nom'] . ' ' . $user['post_nom'],
        'full_name' => $user['prenom'] . ' ' . $user['nom'] . ' ' . $user['post_nom'],
        'redirect' => '../../clients/index.php'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de connexion à la base de données'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur serveur'
    ]);
}
?>
<?php
require_once '../config.php';

// Gérer les requêtes préflight OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Méthode non autorisée. Utilisez POST.', null, 405);
}

// Récupérer et valider les données JSON
$input = file_get_contents("php://input");
if (empty($input)) {
    sendResponse(false, 'Données JSON requises', null, 400);
}

$data = json_decode($input);

if (!$data || !isset($data->username) || !isset($data->password)) {
    sendResponse(false, 'Nom d\'utilisateur et mot de passe requis', null, 400);
}

// Validation des entrées
$username = trim($data->username);
$password = trim($data->password);

if (empty($username) || empty($password)) {
    sendResponse(false, 'Les champs ne peuvent pas être vides', null, 400);
}

// Limiter la longueur des entrées
if (strlen($username) > 50 || strlen($password) > 255) {
    sendResponse(false, 'Entrées trop longues', null, 400);
}

// Connexion à la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier d'abord dans la table admin
    $query = "SELECT admin_id, Num, password, admin_name, admin_role FROM admin WHERE Num = :username OR admin_name = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe hashé
        if (password_verify($password, $user['password'])) {
            // Préparer la réponse
            $userData = [
                'user_id' => $user['admin_id'],
                'username' => $user['Num'],
                'name' => $user['admin_name'],
                'role' => $user['admin_role'],
                'user_type' => 'admin'
            ];
            
            // Générer un token JWT (optionnel)
            $jwtSecret = getenv('JWT_SECRET');
            if ($jwtSecret) {
                $userData['token'] = generateJWT($userData);
            }
            
            sendResponse(true, 'Connexion admin réussie', $userData, 200);
        }
    }
    
    // Si pas admin, vérifier dans la table client
    $query = "SELECT id_client, tel, password, nom, post_nom, prenom FROM client WHERE tel = :username OR nom = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe
        // Note: Si les mots de passe clients ne sont pas hashés, il faut les hasher d'abord
        if (password_verify($password, $user['password'])) {
            // Mot de passe hashé correct
        } elseif ($password === $user['password']) {
            // Mot de passe en clair (à migrer vers le hashage)
            // Vous devriez mettre à jour le mot de passe hashé ici
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE client SET password = :hashedPassword WHERE id_client = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(":hashedPassword", $hashedPassword);
            $updateStmt->bindParam(":id", $user['id_client']);
            $updateStmt->execute();
        } else {
            // Mot de passe incorrect
            sendResponse(false, 'Identifiants incorrects', null, 401);
        }
        
        // Préparer la réponse
        $userData = [
            'user_id' => $user['id_client'],
            'username' => $user['tel'],
            'name' => $user['nom'] . ' ' . $user['post_nom'] . ' ' . $user['prenom'],
            'full_name' => [
                'nom' => $user['nom'],
                'post_nom' => $user['post_nom'],
                'prenom' => $user['prenom']
            ],
            'user_type' => 'client'
        ];
        
        // Générer un token JWT (optionnel)
        $jwtSecret = getenv('JWT_SECRET');
        if ($jwtSecret) {
            $userData['token'] = generateJWT($userData);
        }
        
        sendResponse(true, 'Connexion client réussie', $userData, 200);
    }
    
    // Aucun utilisateur trouvé
    sendResponse(false, 'Identifiants incorrects', null, 401);
    
} catch(PDOException $exception) {
    // Journalisation de l'erreur
    error_log("Erreur login PDO: " . $exception->getMessage());
    sendResponse(false, 'Erreur serveur lors de l\'authentification', null, 500);
} catch(Exception $exception) {
    error_log("Erreur login: " . $exception->getMessage());
    sendResponse(false, 'Erreur serveur', null, 500);
}
?>
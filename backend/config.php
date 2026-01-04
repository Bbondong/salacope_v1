<?php
// Headers pour API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Charger les variables d'environnement
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception("Le fichier .env n'existe pas: " . $path);
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Séparer le nom et la valeur
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Définir la variable d'environnement
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Chemin du fichier .env
$envPath = __DIR__ . '../.env.exemple';

try {
    loadEnv($envPath);
} catch (Exception $e) {
    // Pour le développement, vous pouvez commenter cette ligne en production
    error_log("Erreur de chargement .env: " . $e->getMessage());
}

class Database {
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Récupérer les variables d'environnement
            $host = getenv('DB_HOST');
            $db_name = getenv('DB_NAME');
            $username = getenv('DB_USER');
            $password = getenv('DB_PASS');
            $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
            
            // Validation des variables d'environnement
            if (!$host || !$db_name || !$username) {
                throw new Exception("Variables d'environnement de base de données manquantes");
            }
            
            // Créer la connexion PDO
            $dsn = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=" . $charset;
            $this->conn = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset"
            ]);
            
        } catch(PDOException $exception) {
            // Journalisation sécurisée pour la production
            error_log("Erreur de connexion DB: " . $exception->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        } catch(Exception $exception) {
            error_log("Erreur configuration: " . $exception->getMessage());
            throw $exception;
        }
        
        return $this->conn;
    }
}

function sendResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Fonction pour valider le token JWT (optionnel pour plus tard)
function validateJWT($token) {
    // Implémentation basique - à améliorer selon vos besoins
    try {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        $payload = json_decode(base64_decode($parts[1]), true);
        return $payload;
    } catch (Exception $e) {
        return false;
    }
}

// Fonction pour générer un token JWT (optionnel)
function generateJWT($data) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode(array_merge($data, [
        'iat' => time(),
        'exp' => time() + (24 * 60 * 60) // 24 heures
    ]));
    
    $base64Header = base64_encode($header);
    $base64Payload = base64_encode($payload);
    
    // Clé secrète - à stocker dans .env
    $secret = getenv('JWT_SECRET') ?: 'votre_secret_jwt';
    
    $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $secret, true);
    $base64Signature = base64_encode($signature);
    
    return "$base64Header.$base64Payload.$base64Signature";
}
?>
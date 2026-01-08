<?php
// config.php - Version simplifiée
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Chemin .env
$envFile = dirname(__DIR__) . '/.env';

if (!file_exists($envFile)) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Fichier .env manquant',
        'timestamp' => time()
    ]));
}

$env = parse_ini_file($envFile);
if (!$env) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Erreur lecture .env',
        'timestamp' => time()
    ]));
}

// Variables
$host = $env['DB_HOST'] ?? '';
$dbname = $env['DB_NAME'] ?? '';
$db_username = $env['DB_USER'] ?? '';
$db_password = $env['DB_PASS'] ?? '';
$charset = $env['DB_CHARSET'] ?? 'utf8mb4';

// Vérification
if (empty($host) || empty($dbname) || empty($username)) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Configuration base de données incomplète',
        'timestamp' => time()
    ]));
}

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $bd = new PDO($dsn, $db_username, $db_password, $options);
    
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Erreur connexion base de données',
        'timestamp' => time()
    ]));
}
?>
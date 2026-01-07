<?php
// ⚠️ AJOUTE CES 4 LIGNES AU TRÈS DÉBUT :
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

// Créer un fichier log
$logFile = __DIR__ . '/../../logs/login_errors.log';
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

function logDebug($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

logDebug("=== NOUVELLE REQUÊTE LOGIN ===");
logDebug("Méthode: " . $_SERVER['REQUEST_METHOD']);
logDebug("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

session_start();


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
$username = $env['DB_USER'] ?? '';
$password = $env['DB_PASS'] ?? '';
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
    
    $bd = new PDO($dsn, $username, $password, $options);
    
}catch(PDOException $e) {
    // ⚠️ LOG L'ERREUR COMPLÈTE
    logDebug("❌ ERREUR PDO: " . $e->getMessage());
    logDebug("❌ Fichier: " . $e->getFile());
    logDebug("❌ Ligne: " . $e->getLine());
    logDebug("❌ Code: " . $e->getCode());
    logDebug("❌ Trace: " . $e->getTraceAsString());
    
    // ⚠️ AFFICHE L'ERREUR DANS LA RÉPONSE
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur PDO: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'code' => $e->getCode(),
        'timestamp' => time()
    ]);
    exit();
}

?>
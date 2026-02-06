<?php
// config.php - Version avec support WhatsApp
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

// Variables de base de données
$host = $env['DB_HOST'] ?? '';
$dbname = $env['DB_NAME'] ?? '';
$db_username = $env['DB_USER'] ?? '';
$db_password = $env['DB_PASS'] ?? '';
$charset = $env['DB_CHARSET'] ?? 'utf8mb4';

// Variables WhatsApp (optionnelles - ajoutez-les à votre .env)
$whatsapp_token = $env['WHATSAPP_TOKEN'] ?? '';
$whatsapp_phone_number_id = $env['WHATSAPP_PHONE_NUMBER_ID'] ?? '';

// Vérification BD
if (empty($host) || empty($dbname) || empty($db_username)) {
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

// Définir les constantes WhatsApp si elles existent
if (!defined('WHATSAPP_TOKEN') && !empty($whatsapp_token)) {
    define('WHATSAPP_TOKEN', $whatsapp_token);
}

if (!defined('WHATSAPP_PHONE_NUMBER_ID') && !empty($whatsapp_phone_number_id)) {
    define('WHATSAPP_PHONE_NUMBER_ID', $whatsapp_phone_number_id);
}

// Optionnel : Définir d'autres constantes utiles
if (!defined('APP_ENV')) {
    define('APP_ENV', $env['APP_ENV'] ?? 'production');
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', ($env['APP_DEBUG'] ?? 'false') === 'true');
}
?>
// Ajoutez à la fin de votre config.php
if (!function_exists('async_http_request')) {
    function async_http_request($url, $data = []) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 1,
            CURLOPT_CONNECTTIMEOUT => 1,
        ]);
        
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        return ['success' => empty($error), 'error' => $error];
    }
}
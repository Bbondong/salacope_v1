<?php
error_reporting(0);
ini_set('display_errors', 0);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ==========================
   PROTECTION BRUTE FORCE
========================== */
$MAX_ATTEMPTS = 20;
$LOCK_TIME = 900; // 15 minutes
$ip = $_SERVER['REMOTE_ADDR'];

if (!isset($_SESSION['bruteforce'][$ip])) {
    $_SESSION['bruteforce'][$ip] = [
        'attempts' => 0,
        'lock_until' => 0
    ];
}

// Vérifier blocage
if ($_SESSION['bruteforce'][$ip]['lock_until'] > time()) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Trop de tentatives. Réessayez plus tard.',
        'retry_after' => $_SESSION['bruteforce'][$ip]['lock_until'] - time(),
        'timestamp' => time()
    ]);
    exit();
}

/* ==========================
   HEADERS API
========================== */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode POST requise',
        'timestamp' => time()
    ]);
    exit();
}

// JSON
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
if (!$data || !isset($data['username'], $data['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Username et password requis',
        'timestamp' => time()
    ]);
    exit();
}

$username = trim($data['username']);
$password = trim($data['password']);

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Champs non vides requis',
        'timestamp' => time()
    ]);
    exit();
}

// BDD
require_once __DIR__ . '/../config.php';

try {
    /* ==========================
       ADMIN
    ========================== */
    $stmt = $bd->prepare("SELECT * FROM admin WHERE Num = :u LIMIT 1");
    $stmt->execute([':u' => $username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            // RESET brute force
            $_SESSION['bruteforce'][$ip] = ['attempts' => 0, 'lock_until' => 0];

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
                'redirect' => '/admin/index.php',
                'timestamp' => time()
            ]);
            exit();
        }
    }

    /* ==========================
       CLIENT
    ========================== */
    $stmt = $bd->prepare("SELECT * FROM client WHERE tel = :u LIMIT 1");
    $stmt->execute([':u' => $username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $passwordValid = password_verify($password, $user['password']) || $password === $user['password'];

        if ($passwordValid) {
            // Migration mot de passe si en clair
            if ($password === $user['password']) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $bd->prepare("UPDATE client SET password = :p WHERE id_client = :id");
                $upd->execute([':p' => $hash, ':id' => $user['id_client']]);
            }

            // RESET brute force
            $_SESSION['bruteforce'][$ip] = ['attempts' => 0, 'lock_until' => 0];

            $_SESSION['user_id'] = $user['id_client'];
            $_SESSION['username'] = $user['tel'];
            $_SESSION['name'] = $user['nom'] . ' ' . $user['post_nom'];
            $_SESSION['user_type'] = $user['type_client'];
            $_SESSION['user'] = $user ;
            $_SESSION['client_logged_in'] = true;
            $_SESSION['login_time'] = time();
            if($user['type_client'] =="client"){
                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion client réussie',
                    'redirect' => '/clients/index.php',
                    'timestamp' => time()
                ]);
                exit();
            }else{
                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion vendeur réussie',
                    'redirect' => '/vendeur/index.php',
                    'timestamp' => time()
                ]);
                exit();
            }
        }
    }

    /* ==========================
       ÉCHEC → BRUTE FORCE
    ========================== */
    $_SESSION['bruteforce'][$ip]['attempts']++;

    if ($_SESSION['bruteforce'][$ip]['attempts'] >= $MAX_ATTEMPTS) {
        $_SESSION['bruteforce'][$ip]['lock_until'] = time() + $LOCK_TIME;
    }

    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Identifiants incorrects',
        'attempts_left' => max(0, $MAX_ATTEMPTS - $_SESSION['bruteforce'][$ip]['attempts']),
        'timestamp' => time()
    ]);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'timestamp' => time()
    ]);
    exit();
}
?>

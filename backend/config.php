<?php
// config.php - Connexion PDO avec fichier .env

// Chemin vers le fichier .env (dans la racine)
$envFile = dirname(__DIR__) . '/.env';

if (!file_exists($envFile)) {
    die("❌ Erreur : Fichier .env non trouvé à l'emplacement : $envFile");
}

$envVariables = parse_ini_file($envFile);

if ($envVariables === false) {
    die("❌ Erreur : Impossible de lire le fichier .env");
}

// Récupérer les variables
$host = $envVariables['DB_HOST'] ?? 'localhost';
$dbname = $envVariables['DB_NAME'] ?? '';
$username = $envVariables['DB_USER'] ?? '';
$password = $envVariables['DB_PASS'] ?? '';
$charset = $envVariables['DB_CHARSET'] ?? 'utf8mb4';

try {
    // Création de la connexion PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "✅ Connexion à la base de données réussie !";
    
} catch (PDOException $e) {
    die("❌ Échec de connexion : " . $e->getMessage());
}

// La variable $pdo est maintenant disponible pour vos requêtes
<?php
/* =========================
   SESSION & SÉCURITÉ
========================= */
require_once __DIR__ . '/../backend/config.php';
session_start();

/* Bloquer tout accès sans authentification */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user']) ||
    !isset($_SESSION['username'])
) {
    header('Location: ../');
    exit;
}

/* Sécurisation des variables session */
$admin         = $_SESSION['user'];
$name          = $_SESSION['name'] ?? '';
$tel           = $_SESSION['username'];
$id_client     = (int) $_SESSION['user_id'];
$date_creation = $admin['created_at'] ?? null;

/* =========================
   GESTION DES PAGES
========================= */
$page = $_GET['page'] ?? 'Home';

/* Whitelist stricte des pages - MIS À JOUR */
function loadPage(string $page): string
{
    $validPages = [
        'Home'    => 'Home.php',
        'Trips'   => 'Trips.php',      // Anciennement Category
        'Favory'  => 'Favory.php',
        'Payment' => 'Payment.php',     // Anciennement Chat
        'Profil'  => 'profil.php'
    ];

    return $validPages[$page] ?? 'Home.php';
}

/* Titres des pages - MIS À JOUR */
$pageTitles = [
    'Home'    => 'Accueil',
    'Trips'   => 'Mes courses',         // Nouveau titre
    'Favory'  => 'Favoris',
    'Payment' => 'Moyens de paiement',  // Nouveau titre
    'Profil'  => 'Profil',
];

$pageTitle = $pageTitles[$page] ?? 'Accueil';

/* CSS par page - MIS À JOUR */
$pageStyles = [
    'Home'    => 'home.css',
    'Trips'   => 'trips.css',      // Nouveau fichier CSS
    'Favory'  => 'favory.css',
    'Payment' => 'payment.css',     // Nouveau fichier CSS
    'Profil'  => 'profil.css',
];

/* JS par page - MIS À JOUR */
$pagejs = [
    'Home'    => 'home.js',
    'Trips'   => 'trips.js',        // Nouveau fichier JS
    'Payment' => 'payment.js',       // Nouveau fichier JS
];

$jspage = $pagejs[$page] ?? '';
$cssFile = $pageStyles[$page] ?? 'home.css';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Sécurité cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Dans le head, après tes autres CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS commun -->
    <link rel="stylesheet" href="./style/navbar-style.css">

    <!-- CSS spécifique à la page -->
    <link rel="stylesheet" href="./style/<?= htmlspecialchars($cssFile, ENT_QUOTES) ?>">

    <title>Salacopp client - <?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
</head>
<body>

    <!-- Contenu sécurisé -->
    <?php
        require loadPage($page);
    ?>

    <!-- Navbar (ton nouveau menu avec Accueil, Courses, Favoris, Paiement, Profil) -->
    <?php require 'includes/navbar.php'; ?>

    <!-- JS -->
    <?php if ($jspage): ?>
        <script src="./js/<?= htmlspecialchars($jspage, ENT_QUOTES) ?>"></script>
    <?php endif; ?>

</body>
</html>
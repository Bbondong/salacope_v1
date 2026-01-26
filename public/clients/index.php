<?php
/* =========================
   SESSION & SÉCURITÉ
========================= */
session_start();

/* Bloquer tout accès sans authentification */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user']) ||
    !isset($_SESSION['username'])
) {
    header('Location: login.php');
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

/* Whitelist stricte des pages */
function loadPage(string $page): string
{
    $validPages = [
        'Home'     => 'Home.php',
        'Category' => 'Category.php',
        'Favory'   => 'Favory.php',
        'Chat'     => 'Chat.php',
        'profil'   => 'profil.php'
    ];

    return $validPages[$page] ?? 'Home.php';
}

/* Titres des pages */
$pageTitles = [
    'Home'     => 'Page accueil',
    'Category' => 'Produits',
    'Favory'   => 'Favoris',
    'Chat'     => 'Messagerie',
    'profil'   => 'Profil',
];

$pageTitle = $pageTitles[$page] ?? 'Page accueil';

/* CSS par page */
$pageStyles = [
    'Home'     => 'home.css',
    'Category' => 'category.css',
    'Favory'   => 'favory.css',
    'Chat'     => 'chat.css',
    'profil'   => 'profil.css',
];

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

    <!-- Navbar -->
    <?php require 'includes/navbar.php'; ?>

    <!-- JS -->
    <script src="./js/home.js"></script>

</body>
</html>

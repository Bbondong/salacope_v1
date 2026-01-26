<?php
// session :
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'Home';

function loadPage($page) {
    $validPages = ['Home', 'Category', 'Favory', 'Chat', 'profil'];
    if (in_array($page, $validPages)) {
        return $page . '.php';
    } else {
        return 'Home.php';
    }
}

// Déterminer le titre de la page
$pageTitles = [
    'Home' => 'Page accueil',
    'Category' => 'Produits',
    'Favory' => 'Favory',
    'Chat' => 'Messagerie',
    'profil' => 'Profil',
];

$pageTitle = isset($pageTitles[$page]) ? $pageTitles[$page] : 'Page accueil';

// Déterminer le CSS à charger selon la page
$pageStyles = [
    'Home' => 'home.css',
    'Category' => 'category.css',
    'Favory' => 'favory.css',
    'Chat' => 'chat.css',
    'Profil' => 'profil.css',
];

$cssFile = isset($pageStyles[$page]) ? $pageStyles[$page] : 'home.css';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="./style/<?php echo $cssFile; ?>">

    <title>Salacopp client - <?php echo $pageTitle; ?></title>
</head>
<body>
    <!-- Contenu de chaque page -->
    <?php include loadPage($page); ?>

    <!-- Navbar format mobile -->
    <?php include 'includes/navbar.php'; ?>

    <!-- JS spécifique -->
    <script src="./js/home.js"></script>
</body>
</html>

<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'Home';
function loadPage($page) {
    $validPages = ['Home', 'Category', 'Favory', 'Chat', 'Profil',];
    
    if (in_array($page, $validPages)) {
        return $page . '.php';
    } else {
        return 'Home.php';
    }
}

// Déterminer le titre de la page
$pageTitles = [
    'Home' => 'Page acceuil',
    'Category' => 'Produits',
    'Favory' => 'Favory',
    'Chat' => 'Messagerie',
    'Profil' => 'Profil',
];

$pageTitle = isset($pageTitles[$page]) ? $pageTitles[$page] : 'Page acceuil';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style/navbar-style.css">
    <link rel="stylesheet" href="./style/profil.css">
    <link rel="stylesheet" href="./style/home.css">
    <link rel="stylesheet" href="./style/chat.css">
    <title>Salacopp client - <?php echo $pageTitle; ?> </title>
</head>
<body>
    <!-- Contenue de chaque page -->
    <?php include loadPage($page); ?>
    <!-- Navbar format mobile -->
    <?php include 'includes/navbar.php'; ?>
    <script src="./js/home.js"></script>
</body>
</html>
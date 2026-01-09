<?php
// admin/index.php

// Démarrer la session
session_start();

function checkAdminSession() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Rediriger vers la page de login
        header('Location: /');
        exit();
    }
    
    // Vérifier le timeout de session (1 heure)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 3600)) {
        session_destroy();
        header('Location: /?session_expired=1');
        exit();
    }
    
    // Renouveler le temps de session
    $_SESSION['login_time'] = time();
}

// Appliquer la vérification
checkAdminSession();

// Page par défaut
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Fonction pour inclure la page correspondante
function loadPage($page) {
    $validPages = ['dashboard', 'products', 'publish', 'chat', 'subscriptions', 'settings'];
    
    if (in_array($page, $validPages)) {
        return $page . '.php';
    } else {
        return 'dashboard.php';
    }
}

// Déterminer le titre de la page
$pageTitles = [
    'dashboard' => 'Tableau de bord',
    'products' => 'Produits',
    'publish' => 'Publier un produit',
    'chat' => 'Messagerie',
    'subscriptions' => 'Abonnements',
    'settings' => 'Paramètres'
];

$pageTitle = isset($pageTitles[$page]) ? $pageTitles[$page] : 'Tableau de bord';

// Récupérer les données de l'administrateur (exemple)
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salacopp Admin - <?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Barre latérale (visible sur desktop) -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Contenu principal -->
        <div class="main-content">
            <!-- En-tête -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Navbar mobile -->
            <?php include 'includes/navbar.php'; ?>
            
            <!-- Contenu de la page -->
            <div class="page-content">
                <?php include loadPage($page); ?>
            </div>
            
            <!-- Pied de page -->
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
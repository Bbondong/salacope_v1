<?php
// admin/index.php

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Récupérer les informations de l'admin
$admin_id = $_SESSION['admin_id'] ?? 1;
$admin_name = $_SESSION['admin_name'] ?? 'Benjamin Tech';
$admin_role = $_SESSION['admin_role'] ?? 'Propriétaire';
$admin_avatar = $_SESSION['admin_avatar'] ?? '../assets/admin_avatar.jpg';

// Déterminer la page à afficher
$page = $_GET['page'] ?? 'dashboard';
$allowed_pages = ['dashboard', 'profile', 'publications', 'articles', 'sellers', 'buyers', 'subscriptions', 'settings', 'users', 'ads', 'reports', 'support', 'logs'];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Titre de la page selon la section
$page_titles = [
    'dashboard' => 'Tableau de bord',
    'profile' => 'Mon profil',
    'publications' => 'Publications',
    'articles' => 'Articles',
    'sellers' => 'Vendeurs',
    'buyers' => 'Acheteurs',
    'subscriptions' => 'Abonnements',
    'settings' => 'Paramètres',
    'users' => 'Utilisateurs',
    'ads' => 'Publicités',
    'reports' => 'Rapports',
    'support' => 'Support',
    'logs' => 'Journaux'
];

$page_title = $page_titles[$page] ?? 'Tableau de bord';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin Salacope</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <?php
        // Inclure le contenu de la page demandée
        $include_file = "includes/{$page}.php";
        
        if (file_exists($include_file)) {
            include $include_file;
        } else {
            include 'includes/dashboard.php';
        }
        ?>
    </main>
    
    <?php include 'navbar-mobile.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="assets/js/admin.js"></script>
    
    <?php if ($page === 'dashboard'): ?>
    <script src="assets/js/dashboard-charts.js"></script>
    <?php endif; ?>
    
    <?php if ($page === 'profile'): ?>
    <script src="assets/js/profile.js"></script>
    <?php endif; ?>
    
    <?php if ($page === 'publications'): ?>
    <script src="assets/js/publications.js"></script>
    <?php endif; ?>
</body>
</html>
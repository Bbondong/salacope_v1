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



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    $nom = $_SESSION['name'] ;
    echo "bonjours admin ".$nom ;
    
    ?>
    <a href="../backend/auth/logout.php">deconnecte toi se pas encore fini cette page</a>
</body>
</html>
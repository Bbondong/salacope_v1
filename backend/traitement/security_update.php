<?php
session_start();

/* =============================
   Anti-accès direct par URL
   (bloque GET / accès navigateur)
============================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

/* =============================
   Connexion DB
============================= */
require_once __DIR__ . '/../config.php';

/* =============================
   Vérification session
============================= */
if (
    !isset($_SESSION['admin_id']) ||
    !isset($_SESSION['admin_role'])
) {
    http_response_code(403);
    exit('Accès non autorisé');
}

/* =============================
   Vérification rôle
============================= */
if ($_SESSION['admin_role'] !== 'fondateur') {
    http_response_code(403);
    exit('Action réservée au fondateur');
}

$admin_id = (int) $_SESSION['admin_id'];

/* =============================
   Vérification POST
============================= */
if (
    empty($_POST['current_password']) ||
    empty($_POST['new_password']) ||
    empty($_POST['confirm_password'])
) {
    exit('Champs requis manquants');
}

/* =============================
   Récupération admin
============================= */
$stmt = $bd->prepare("
    SELECT password 
    FROM admin 
    WHERE admin_id = ?
    LIMIT 1
");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    exit('Administrateur introuvable');
}

/* =============================
   Vérification mot de passe actuel
============================= */
if (!password_verify($_POST['current_password'], $admin['password'])) {
    exit('Mot de passe actuel incorrect');
}

/* =============================
   Validation nouveau mot de passe
============================= */
if ($_POST['new_password'] !== $_POST['confirm_password']) {
    exit('Les mots de passe ne correspondent pas');
}

if (strlen($_POST['new_password']) < 8) {
    exit('Mot de passe trop court (8 caractères minimum)');
}

/* =============================
   Mise à jour mot de passe
============================= */
$new_hash = password_hash($_POST['new_password'], PASSWORD_ARGON2ID);

$update = $bd->prepare("
    UPDATE admin 
    SET password = ?
    WHERE admin_id = ?
");
$update->execute([$new_hash, $admin_id]);

/* =============================
   Sécurité post-changement
   (forçage reconnexion)
============================= */
session_regenerate_id(true);

echo 'Mot de passe mis à jour (fondateur uniquement)';

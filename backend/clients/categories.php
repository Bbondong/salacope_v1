<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
    $stmt = $bd->query("SELECT category_id AS id, name, slug FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();

    echo json_encode($categories);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur récupération catégories',
        'error'   => $e->getMessage(),
        'timestamp' => time()
    ]);
}
?>

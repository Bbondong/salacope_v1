<?php
header('Content-Type: application/json');
require_once '../config.php';

$categoryId = $_GET['category'] ?? null;
$offset     = (int) ($_GET['offset'] ?? 0);
$limit      = 5;

// Requête SQL pour récupérer les produits avec leur image principale
$sql = "
SELECT 
    p.product_id,
    p.title,
    p.price,
    p.slug,
    pi.image_path
FROM products p
JOIN product_images pi 
    ON pi.product_id = p.product_id AND pi.is_primary = 1
WHERE p.status = 'published'
";

$params = [];

if ($categoryId) {
    $sql .= " AND p.category_id = ?";
    $params[] = $categoryId;
}

$sql .= "
ORDER BY p.created_at DESC
LIMIT $limit OFFSET $offset
";

try {
    $stmt = $bd->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Ajustement du chemin image pour le web
    $products = array_map(function($p) {
        $p['image_path'] = '/' . $p['image_path']; 
        return $p;
    }, $products);

    echo json_encode($products);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des produits',
        'error'   => $e->getMessage(),
        'timestamp' => time()
    ]);
}
?>

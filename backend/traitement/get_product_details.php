<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID produit invalide']);
    exit();
}

try {
    // Récupérer le produit
    $stmt = $bd->prepare("
        SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ? AND p.status = 'published'
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit();
    }
    
    // Récupérer les images
    $imgStmt = $bd->prepare("
        SELECT image_path, image_name, is_primary 
        FROM product_images 
        WHERE product_id = ?
        ORDER BY image_order
    ");
    $imgStmt->execute([$product_id]);
    $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Nettoyer les données pour sécurité
    $product['title'] = htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8');
    $product['description'] = htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8');
    $product['city'] = htmlspecialchars($product['city'], ENT_QUOTES, 'UTF-8');
    $product['address'] = htmlspecialchars($product['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $product['brand'] = htmlspecialchars($product['brand'] ?? '', ENT_QUOTES, 'UTF-8');
    $product['model'] = htmlspecialchars($product['model'] ?? '', ENT_QUOTES, 'UTF-8');
    $product['category_name'] = htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
    
    echo json_encode([
        'success' => true,
        'product' => $product,
        'images' => $images
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur get_product_details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des détails'
    ]);
}
?>
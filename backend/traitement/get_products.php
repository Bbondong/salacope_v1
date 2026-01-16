<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Récupérer les paramètres
$page = intval($_POST['page'] ?? 1);
$perPage = intval($_POST['per_page'] ?? 12);
$offset = ($page - 1) * $perPage;

// Récupérer les filtres
$search = trim($_POST['search'] ?? '');
$category = intval($_POST['category'] ?? 0);
$condition = $_POST['condition'] ?? '';
$city = trim($_POST['city'] ?? '');

try {
    // Construire la requête avec filtres
    $sql = "
        SELECT p.*, 
               c.name as category_name,
               pi.image_path as main_image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        WHERE p.status = 'published'
    ";
    
    $params = [];
    
    // Ajouter les filtres
    if (!empty($search)) {
        $sql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if ($category > 0) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category;
    }
    
    if (!empty($condition)) {
        $sql .= " AND p.product_condition = ?";
        $params[] = $condition;
    }
    
    if (!empty($city)) {
        $sql .= " AND p.city LIKE ?";
        $params[] = "%$city%";
    }
    
    // Ajouter l'ordre et la limite
    $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    // Exécuter la requête
    $stmt = $bd->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Vérifier s'il y a plus de produits
    $countSql = "
        SELECT COUNT(*) as total 
        FROM products p 
        WHERE p.status = 'published'
    ";
    
    $countParams = [];
    
    if (!empty($search)) {
        $countSql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
    }
    
    if ($category > 0) {
        $countSql .= " AND p.category_id = ?";
        $countParams[] = $category;
    }
    
    if (!empty($condition)) {
        $countSql .= " AND p.product_condition = ?";
        $countParams[] = $condition;
    }
    
    if (!empty($city)) {
        $countSql .= " AND p.city LIKE ?";
        $countParams[] = "%$city%";
    }
    
    $countStmt = $bd->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    // Formater les produits
    foreach ($products as &$product) {
        // Nettoyer les données pour sécurité
        $product['title'] = htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8');
        $product['description'] = htmlspecialchars(substr($product['description'], 0, 200), ENT_QUOTES, 'UTF-8');
        $product['city'] = htmlspecialchars($product['city'], ENT_QUOTES, 'UTF-8');
        $product['category_name'] = htmlspecialchars($product['category_name'] ?? 'Non catégorisé', ENT_QUOTES, 'UTF-8');
        
        // Image par défaut si vide
        if (empty($product['main_image'])) {
            $product['main_image'] = null;
        }
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'has_more' => ($offset + count($products)) < $total,
        'total' => $total,
        'page' => $page
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur get_products: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des produits: ' . $e->getMessage(),
        'products' => []
    ]);
}
?>
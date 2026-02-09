<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$seller_id = intval($data['seller_id'] ?? 0);
$client_id = intval($data['client_id'] ?? 0);
$product_id = intval($data['product_id'] ?? 0);
$seller_type = isset($data['seller_type']) ? $data['seller_type'] : 'client';

// Vérifier l'authentification
if ($client_id != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    // 1. Vérifier que le produit existe et récupérer le seller_type réel
    $productSql = "SELECT seller_id, seller_type FROM products WHERE product_id = ?";
    $productStmt = $bd->prepare($productSql);
    $productStmt->execute([$product_id]);
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit;
    }
    
    // S'assurer que le seller_id correspond
    if ($product['seller_id'] != $seller_id) {
        echo json_encode(['success' => false, 'message' => 'Vendeur incorrect pour ce produit']);
        exit;
    }
    
    // Utiliser le seller_type de la base de données (plus fiable)
    $seller_type = $product['seller_type'];
    
    // 2. Vérifier si le vendeur existe dans la bonne table
    $seller_exists = false;
    
    if ($seller_type == 'admin') {
        $checkSql = "SELECT admin_id FROM admin WHERE admin_id = ?";
    } else {
        $checkSql = "SELECT id_client FROM client WHERE id_client = ?";
    }
    
    $checkStmt = $bd->prepare($checkSql);
    $checkStmt->execute([$seller_id]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Vendeur non trouvé']);
        exit;
    }
    
    // 3. Vérifier si une conversation existe déjà
    $sql = "SELECT conversation_id FROM conversations 
            WHERE product_id = ? 
            AND buyer_id = ? 
            AND seller_id = ? 
            AND seller_type = ?
            LIMIT 1";
    
    $stmt = $bd->prepare($sql);
    $stmt->execute([$product_id, $client_id, $seller_id, $seller_type]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        $conversation_id = $existing['conversation_id'];
    } else {
        $sql = "INSERT INTO conversations (product_id, buyer_id, seller_id, seller_type, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $bd->prepare($sql);
        $stmt->execute([$product_id, $client_id, $seller_id, $seller_type]);
        $conversation_id = $pdo->lastInsertId();
    }
    
    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id,
        'product_id' => $product_id,
        'buyer_id' => $client_id,
        'seller_id' => $seller_id,
        'seller_type' => $seller_type
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>
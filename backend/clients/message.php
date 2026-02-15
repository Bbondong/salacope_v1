<?php
// Désactiver l'affichage des erreurs en production, mais les capturer
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Buffer de sortie pour capturer toute sortie inattendue
ob_start();

session_start();

// S'assurer qu'aucun contenu n'a été envoyé avant
if (headers_sent($filename, $linenum)) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => "Headers déjà envoyés dans $filename à la ligne $linenum"]);
    exit;
}

header('Content-Type: application/json');

try {
    require_once '../config.php';
    
    // Vérifier que $bd est défini
    if (!isset($bd)) {
        throw new Exception('Variable $bd non définie dans config.php');
    }
    
    // Lire et valider les données d'entrée
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('Aucune donnée reçue');
    }
    
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON invalide: ' . json_last_error_msg());
    }
    
    $seller_id = intval($data['seller_id'] ?? 0);
    $client_id = intval($data['client_id'] ?? 0);
    $product_id = intval($data['product_id'] ?? 0);
    $seller_type = isset($data['seller_type']) ? $data['seller_type'] : 'client';
    
    // Vérifier les paramètres requis
    if ($seller_id <= 0 || $client_id <= 0 || $product_id <= 0) {
        throw new Exception('Paramètres invalides');
    }
    
    // Vérifier l'authentification
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Utilisateur non connecté');
    }
    
    if ($client_id != $_SESSION['user_id']) {
        throw new Exception('Non autorisé');
    }
    
    // 1. Vérifier que le produit existe
    $productSql = "SELECT seller_id, seller_type FROM products WHERE product_id = ?";
    $productStmt = $bd->prepare($productSql);
    $productStmt->execute([$product_id]);
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception('Produit non trouvé');
    }
    
    if ($product['seller_id'] != $seller_id) {
        throw new Exception('Vendeur incorrect pour ce produit');
    }
    
    $seller_type = $product['seller_type'];
    
    // 2. Vérifier si le vendeur existe
    if ($seller_type == 'admin') {
        $checkSql = "SELECT admin_id FROM admin WHERE admin_id = ?";
    } else {
        $checkSql = "SELECT id_client FROM client WHERE id_client = ?";
    }
    
    $checkStmt = $bd->prepare($checkSql);
    $checkStmt->execute([$seller_id]);
    
    if (!$checkStmt->fetch()) {
        throw new Exception('Vendeur non trouvé dans la table ' . ($seller_type == 'admin' ? 'admin' : 'client'));
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
        $conversation_id = $bd->lastInsertId();
    }
    
    // Vider le buffer et envoyer la réponse
    ob_clean();
    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id,
        'product_id' => $product_id,
        'buyer_id' => $client_id,
        'seller_id' => $seller_id,
        'seller_type' => $seller_type
    ]);
    
} catch (Exception $e) {
    // Logger l'erreur
    error_log('Erreur dans create_chat.php: ' . $e->getMessage());
    
    // Vider le buffer et envoyer l'erreur en JSON
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Terminer le buffer
ob_end_flush();
?>
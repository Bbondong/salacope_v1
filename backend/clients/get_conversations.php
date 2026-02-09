<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

try {
    // Récupérer toutes les conversations de l'utilisateur
    $sql = "SELECT 
                c.conversation_id,
                c.product_id,
                c.buyer_id,
                c.seller_id,
                c.seller_type,
                c.created_at,
                -- Dernier message
                (SELECT message FROM messages 
                 WHERE conversation_id = c.conversation_id 
                 ORDER BY created_at DESC LIMIT 1) as last_message,
                -- Heure du dernier message
                (SELECT created_at FROM messages 
                 WHERE conversation_id = c.conversation_id 
                 ORDER BY created_at DESC LIMIT 1) as last_message_time,
                -- Messages non lus
                (SELECT COUNT(*) FROM messages 
                 WHERE conversation_id = c.conversation_id 
                 AND sender_id != ? 
                 AND is_read = 0) as unread_count,
                -- Infos de l'autre utilisateur
                CASE 
                    WHEN c.buyer_id = ? THEN 
                        CASE 
                            WHEN c.seller_type = 'admin' THEN
                                (SELECT admin_name FROM admin WHERE admin_id = c.seller_id)
                            ELSE
                                (SELECT CONCAT(nom, ' ', post_nom) FROM client WHERE id_client = c.seller_id)
                        END
                    ELSE 
                        (SELECT CONCAT(nom, ' ', post_nom) FROM client WHERE id_client = c.buyer_id)
                END as other_user_name,
                -- Infos du produit
                p.title as product_title,
                p.image_path as product_image,
                p.price as product_price
            FROM conversations c
            LEFT JOIN products p ON c.product_id = p.product_id
            WHERE c.buyer_id = ? OR c.seller_id = ?
            ORDER BY c.created_at DESC";
    
    $stmt = $bd->prepare($sql);
    $stmt->execute([$user_id, $user_id, $user_id, $user_id]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'conversations' => $conversations,
        'count' => count($conversations)
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
}
?>
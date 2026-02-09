<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;
$user_id = $_SESSION['user_id'] ?? 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;

if (!$conversation_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

try {
    // Vérifier l'accès à la conversation
    $checkSql = "SELECT conversation_id, buyer_id, seller_id, seller_type 
                 FROM conversations 
                 WHERE conversation_id = ? 
                 AND (buyer_id = ? OR seller_id = ?)";
    
    $checkStmt = $bd->prepare($checkSql);
    $checkStmt->execute([$conversation_id, $user_id, $user_id]);
    $conversation = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        echo json_encode(['success' => false, 'message' => 'Accès interdit']);
        exit;
    }
    
    // Déterminer si l'utilisateur est le buyer ou le seller
    $is_buyer = ($conversation['buyer_id'] == $user_id);
    $is_seller = ($conversation['seller_id'] == $user_id);
    
    // Marquer les messages de l'autre personne comme lus
    $other_user_id = $is_buyer ? $conversation['seller_id'] : $conversation['buyer_id'];
    
    $updateSql = "UPDATE messages 
                  SET is_read = 1 
                  WHERE conversation_id = ? 
                  AND sender_id = ? 
                  AND is_read = 0";
    
    $updateStmt = $bd->prepare($updateSql);
    $updateStmt->execute([$conversation_id, $other_user_id]);
    
    // Récupérer les messages
    $sql = "SELECT 
                m.message_id,
                m.conversation_id,
                m.sender_id,
                m.sender_type,
                m.message,
                m.is_read,
                DATE_FORMAT(m.created_at, '%H:%i') as time_only,
                DATE_FORMAT(m.created_at, '%d/%m/%Y %H:%i') as full_date,
                m.created_at,
                CASE 
                    WHEN m.sender_type = 'admin' THEN 
                        (SELECT admin_name FROM admin WHERE admin_id = m.sender_id)
                    ELSE 
                        (SELECT CONCAT(nom, ' ', post_nom) FROM client WHERE id_client = m.sender_id)
                END as sender_name
            FROM messages m
            WHERE m.conversation_id = ?
            ORDER BY m.created_at DESC
            LIMIT ?";
    
    $stmt = $bd->prepare($sql);
    $stmt->execute([$conversation_id, $limit]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Inverser pour avoir les plus anciens en premier
    $messages = array_reverse($messages);
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'conversation_info' => $conversation,
        'current_user_id' => $user_id,
        'is_buyer' => $is_buyer,
        'is_seller' => $is_seller,
        'count' => count($messages)
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>
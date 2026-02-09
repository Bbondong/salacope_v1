<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$conversation_id = intval($data['conversation_id'] ?? 0);
$sender_id = intval($data['sender_id'] ?? 0);
$sender_type = $data['sender_type'] ?? 'client'; // 'client' ou 'admin'
$message = trim($data['message'] ?? '');
$product_id = intval($data['product_id'] ?? 0);

// Validation
if (!$conversation_id || !$sender_id || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Vérifier que l'expéditeur est bien connecté
if ($sender_id != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    // Vérifier que l'utilisateur a accès à cette conversation
    $checkSql = "SELECT conversation_id, seller_type FROM conversations 
                 WHERE conversation_id = ? 
                 AND (buyer_id = ? OR seller_id = ?)";
    
    $checkStmt = $bd->prepare($checkSql);
    $checkStmt->execute([$conversation_id, $sender_id, $sender_id]);
    $conversation = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$conversation) {
        echo json_encode(['success' => false, 'message' => 'Accès à la conversation interdit']);
        exit;
    }
    
    // Déterminer le sender_type réel (client ou admin)
    // Si l'expéditeur est le vendeur et que c'est un admin
    if ($conversation['seller_type'] == 'admin' && $sender_id == $conversation['seller_id']) {
        $sender_type = 'admin';
    } else {
        $sender_type = 'client';
    }
    
    // Insérer le message
    $sql = "INSERT INTO messages 
            (conversation_id, sender_id, sender_type, message, is_read, created_at) 
            VALUES (?, ?, ?, ?, 0, NOW())";
    
    $stmt = $bd->prepare($sql);
    $stmt->execute([$conversation_id, $sender_id, $sender_type, $message]);
    $message_id = $pdo->lastInsertId();
    
    // Mettre à jour la conversation (date de dernière activité)
    $updateSql = "UPDATE conversations SET updated_at = NOW() WHERE conversation_id = ?";
    $updateStmt = $bd->prepare($updateSql);
    $updateStmt->execute([$conversation_id]);
    
    echo json_encode([
        'success' => true,
        'message_id' => $message_id,
        'sender_type' => $sender_type,
        'message' => 'Message envoyé'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>
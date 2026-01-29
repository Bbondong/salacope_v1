<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config.php'; // adapte si besoin

$userId = (int)$_SESSION['user_id'];
$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if ($conversationId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid conversation']);
    exit;
}

/* 🔒 Vérifier que l’utilisateur fait partie de la conversation */
$stmt = $pdo->prepare("
    SELECT 1 FROM conversations
    WHERE conversation_id = ?
    AND (buyer_id = ? OR seller_id = ?)
");
$stmt->execute([$conversationId, $userId, $userId]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Forbidden']);
    exit;
}

/* 📩 Récupérer les nouveaux messages */
$stmt = $pdo->prepare("
    SELECT 
        m.message_id AS id,
        m.sender_id,
        m.sender_type,
        m.message,
        DATE_FORMAT(m.created_at, '%H:%i') AS time
    FROM messages m
    WHERE m.conversation_id = ?
    AND m.message_id > ?
    ORDER BY m.message_id ASC
");
$stmt->execute([$conversationId, $lastId]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* 👁️ Marquer comme lus les messages reçus */
$pdo->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE conversation_id = ?
    AND sender_id != ?
")->execute([$conversationId, $userId]);

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

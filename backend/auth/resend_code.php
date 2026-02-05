<?php
// backend/auth/resend_code.php

session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Vérifier la session
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expirée'], JSON_UNESCAPED_UNICODE);
    exit();
}

$clientId = $_SESSION['user_id'];

try {
    require_once __DIR__ . '/../config.php';
    
    if (!isset($bd) || !($bd instanceof PDO)) {
        throw new Exception("Connexion à la base de données non établie");
    }
    
    // 1. Vérifier si le client existe et n'est pas encore vérifié
    $checkClientQuery = "SELECT id_client, tel, nom, prenom, type_client, statut 
                        FROM client 
                        WHERE id_client = :client_id 
                        AND statut = 'new'";
    
    $checkClientStmt = $bd->prepare($checkClientQuery);
    $checkClientStmt->execute(['client_id' => $clientId]);
    $client = $checkClientStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Compte déjà vérifié ou non trouvé'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 2. Générer un nouveau code
    $newVerificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash_code = password_hash($newVerificationCode, PASSWORD_DEFAULT);
    $expirationDate = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    
    // 3. Désactiver les anciens codes
    $disableOldCodesQuery = "UPDATE verification_codes 
                            SET statut = 'expired'
                            WHERE user_id = :client_id 
                            AND statut = 'pending'";
    
    $disableOldCodesStmt = $bd->prepare($disableOldCodesQuery);
    $disableOldCodesStmt->execute(['client_id' => $clientId]);
    
    // 4. Insérer le nouveau code
    $insertCodeQuery = "INSERT INTO verification_codes (user_id, code, expires_at, statut) 
                       VALUES (:user_id, :code, :expires_at, 'pending')";
    
    $insertCodeStmt = $bd->prepare($insertCodeQuery);
    $codeInserted = $insertCodeStmt->execute([
        'user_id' => $clientId,
        'code' => $hash_code,
        'expires_at' => $expirationDate
    ]);
    
    if (!$codeInserted) {
        throw new Exception("Échec de création du nouveau code");
    }
    
    // 5. Mettre à jour la session
    $_SESSION['verification_code'] = $newVerificationCode;
    $_SESSION['verification_expires'] = $expirationDate;
    
    // 6. Envoyer le code par WhatsApp (si configuré)
    $whatsappSent = false;
    $name = $client['nom'] . ' ' . $client['prenom'];
    $phone = $client['tel'];
    
    $sendWhatsappPath = __DIR__ . '/../sendsms/sendwhatsapp.php';
    if (file_exists($sendWhatsappPath)) {
        require_once $sendWhatsappPath;
        
        if (defined('WHATSAPP_TOKEN') && defined('WHATSAPP_PHONE_NUMBER_ID') && 
            !empty(WHATSAPP_TOKEN) && !empty(WHATSAPP_PHONE_NUMBER_ID)) {
            
            $env = [
                'WHATSAPP_TOKEN' => WHATSAPP_TOKEN,
                'WHATSAPP_PHONE_NUMBER_ID' => WHATSAPP_PHONE_NUMBER_ID
            ];
            
            $message = "Votre nouveau code de vérification Salacoop est : *{$newVerificationCode}*\n\n";
            $message .= "Utilisez ce code pour activer votre compte.\n";
            $message .= "⚠️ Ce code expire dans 30 minutes.\n";
            $message .= "🔒 Ne partagez jamais ce code.\n\n";
            $message .= "Merci,\nL'équipe Salacoop";
            
            $result = sendWhatsAppMessage($name, $phone, $message, $env);
            
            if ($result['success'] ?? false) {
                $whatsappSent = true;
                $_SESSION['whatsapp_sent'] = true;
                
                // Mettre à jour le statut du code
                $updateCodeQuery = "UPDATE verification_codes 
                                   SET statut = 'sent'
                                   WHERE user_id = :client_id 
                                   AND code = :code";
                
                $updateCodeStmt = $bd->prepare($updateCodeQuery);
                $updateCodeStmt->execute([
                    'client_id' => $clientId,
                    'code' => $hash_code
                ]);
            }
        }
    }
    
    // 7. Préparer la réponse
    $response = [
        'success' => true,
        'message' => 'Nouveau code généré' . ($whatsappSent ? ' et envoyé par WhatsApp' : ''),
        'new_expiry' => $expirationDate,
        'whatsapp_sent' => $whatsappSent
    ];
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    ob_end_clean();
    error_log("PDOException resend_code: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    ob_end_clean();
    error_log("Exception resend_code: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du renvoi du code'
    ], JSON_UNESCAPED_UNICODE);
}

exit();
?>
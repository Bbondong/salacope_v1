<?php
// backend/sendsms/delayed_sender.php

// Attendre le délai
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$delaySeconds = $input['delay_seconds'] ?? 10;
error_log("⏰ delayed_sender: Attente de {$delaySeconds} secondes avant l'envoi du code");

sleep($delaySeconds);

// Démarrer l'envoi réel
require_once __DIR__ . '/sendwhatsapp.php';
require_once __DIR__ . '/../config.php';

if (!$input) {
    error_log("❌ delayed_sender: Données JSON invalides");
    exit();
}

$clientId = $input['client_id'] ?? null;
$verificationCode = $input['verification_code'] ?? null;
$phone = $input['phone'] ?? null;
$name = $input['name'] ?? null;

if (!$clientId || !$verificationCode || !$phone || !$name) {
    error_log("❌ delayed_sender: Paramètres manquants");
    exit();
}

try {
    // Vérifier la connexion à la base de données
    if (!isset($bd) || !($bd instanceof PDO)) {
        throw new Exception("Connexion à la base de données non établie");
    }
    
    // Vérifier si le client existe
    $checkQuery = "SELECT id_client FROM client WHERE id = :client_id LIMIT 1";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['client_id' => $clientId]);
    
    if ($checkStmt->rowCount() === 0) {
        error_log("⚠️ delayed_sender: Client {$clientId} non trouvé");
        exit();
    }
    
    // Vérifier si WhatsApp est configuré
    if (!defined('WHATSAPP_TOKEN') || !defined('WHATSAPP_PHONE_NUMBER_ID') ||
        empty(WHATSAPP_TOKEN) || empty(WHATSAPP_PHONE_NUMBER_ID)) {
        error_log("⚠️ delayed_sender: Configuration WhatsApp manquante");
        exit();
    }
    
    // Configuration WhatsApp
    $env = [
        'WHATSAPP_TOKEN' => WHATSAPP_TOKEN,
        'WHATSAPP_PHONE_NUMBER_ID' => WHATSAPP_PHONE_NUMBER_ID
    ];
    
    // Message du code (envoyé par VOUS à l'utilisateur)
    $message = "Bonjour {$name},\n\n";
    $message .= "Merci pour votre demande d'inscription sur Salacoop.\n\n";
    $message .= "Votre code de vérification est :\n\n";
    $message .= "🎯 *{$verificationCode}*\n\n";
    $message .= "Utilisez ce code pour compléter votre inscription.\n";
    $message .= "⏰ Ce code expire dans 30 minutes.\n";
    $message .= "🔒 Ne partagez jamais ce code.\n\n";
    $message .= "Bienvenue dans la communauté Salacoop !\n\n";
    $message .= "L'équipe Salacoop";
    
    error_log("📤 delayed_sender: Envoi du code après {$delaySeconds}s à {$phone}");
    
    // Envoyer le message WhatsApp
    $result = sendWhatsAppMessage($name, $phone, $message, $env);
    
    if ($result['success'] ?? false) {
        // Mettre à jour le statut
        $updateQuery = "UPDATE verification_codes 
                        SET statut = 'sent',
                            sent_at = NOW()
                        WHERE user_id = :client_id 
                        AND statut = 'pending'";
        
        $updateStmt = $bd->prepare($updateQuery);
        $updateResult = $updateStmt->execute(['client_id' => $clientId]);
        
        if ($updateResult) {
            error_log("✅ delayed_sender: Code envoyé avec succès à {$phone}");
        } else {
            error_log("⚠️ delayed_sender: Code envoyé mais erreur de mise à jour BD");
        }
        
    } else {
        error_log("❌ delayed_sender: Échec envoi du code à {$phone}: " . json_encode($result));
    }
    
} catch (Exception $e) {
    error_log("💥 delayed_sender: Erreur: " . $e->getMessage());
}

// Répondre
http_response_code(200);
echo json_encode([
    'success' => true, 
    'processed_at' => date('Y-m-d H:i:s'),
    'message' => 'Code de vérification envoyé'
]);
?>
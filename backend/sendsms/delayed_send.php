<?php
// backend/sendsms/delayed_send.php

// Ce script s'exécute en différé pour envoyer le code WhatsApp

// Attendre 10 secondes (simuler le délai)
sleep(10);

// Charger la configuration
require_once __DIR__ . '/../config.php';

// Vérifier si le fichier sendwhatsapp.php existe
$sendWhatsappPath = __DIR__ . '/sendwhatsapp.php';
if (!file_exists($sendWhatsappPath)) {
    error_log("❌ Fichier sendwhatsapp.php non trouvé");
    exit(1);
}

require_once $sendWhatsappPath;

// Lire les paramètres POST
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    error_log("❌ Données invalides pour delayed_send");
    exit(1);
}

$clientId = $input['client_id'] ?? null;
$verificationCode = $input['verification_code'] ?? null;
$phone = $input['phone'] ?? null;
$name = $input['name'] ?? null;

if (!$clientId || !$verificationCode || !$phone || !$name) {
    error_log("❌ Paramètres manquants pour delayed_send");
    exit(1);
}

try {
    // Vérifier la connexion à la base de données
    if (!isset($bd) || !($bd instanceof PDO)) {
        throw new Exception("Connexion à la base de données non établie");
    }

    // Vérifier si le client existe et n'est pas encore vérifié
    $checkQuery = "SELECT id_client, statut FROM client WHERE id_client = :client_id AND statut = 'new'";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['client_id' => $clientId]);
    
    if ($checkStmt->rowCount() === 0) {
        error_log("⚠️ Client {$clientId} déjà vérifié ou non trouvé");
        exit(0);
    }
    
    // Vérifier si le code est toujours valide
    $codeCheckQuery = "SELECT id FROM verification_codes 
                       WHERE user_id = :client_id 
                       AND statut = 'pending'
                       AND expires_at > NOW()";
    
    $codeCheckStmt = $bd->prepare($codeCheckQuery);
    $hashCode = password_hash($verificationCode, PASSWORD_DEFAULT);
    $codeCheckStmt->execute(['client_id' => $clientId]);
    
    if ($codeCheckStmt->rowCount() === 0) {
        error_log("❌ Code non trouvé ou expiré pour client {$clientId}");
        exit(0);
    }
    
    // Vérifier si WhatsApp est configuré
    if (!defined('WHATSAPP_TOKEN') || !defined('WHATSAPP_PHONE_NUMBER_ID') ||
        empty(WHATSAPP_TOKEN) || empty(WHATSAPP_PHONE_NUMBER_ID)) {
        error_log("⚠️ Configuration WhatsApp manquante");
        exit(1);
    }
    
    // Configuration WhatsApp
    $env = [
        'WHATSAPP_TOKEN' => WHATSAPP_TOKEN,
        'WHATSAPP_PHONE_NUMBER_ID' => WHATSAPP_PHONE_NUMBER_ID
    ];
    
    // Préparer le message
    $message = "Votre code de vérification Salacoop est : *{$verificationCode}*\n\n";
    $message .= "Utilisez ce code pour activer votre compte.\n";
    $message .= "⚠️ Ce code expire dans 30 minutes.\n";
    $message .= "🔒 Ne partagez jamais ce code.\n\n";
    $message .= "Merci,\nL'équipe Salacoop";
    
    error_log("📤 Envoi WhatsApp différé pour client {$clientId} ({$phone})");
    
    // Envoyer le message WhatsApp
    $result = sendWhatsAppMessage($name, $phone, $message, $env);
    
    if ($result['success']) {
        // Mettre à jour le statut dans verification_codes
        $updateQuery = "UPDATE verification_codes 
                        SET statut = 'sent'
                        WHERE user_id = :client_id 
                        AND statut = 'pending'
                        AND expires_at > NOW()";
        
        $updateStmt = $bd->prepare($updateQuery);
        $updateResult = $updateStmt->execute(['client_id' => $clientId]);
        
        error_log("✅ WhatsApp envoyé avec succès à {$phone} (après délai)");
        
        // Envoyer notification au support (optionnel)
        if (defined('WHATSAPP_SUPPORT_PHONE') && !empty(WHATSAPP_SUPPORT_PHONE)) {
            try {
                $supportMessage = "📱 Code envoyé après délai\n\n";
                $supportMessage .= "Client: {$name}\n";
                $supportMessage .= "Téléphone: {$phone}\n";
                $supportMessage .= "Code: {$verificationCode}\n";
                $supportMessage .= "Heure: " . date('H:i:s');
                
                $supportResult = sendWhatsAppMessage(
                    "Support Salacoop", 
                    WHATSAPP_SUPPORT_PHONE, 
                    $supportMessage, 
                    $env
                );
            } catch (Exception $e) {
                error_log("⚠️ Erreur envoi support: " . $e->getMessage());
            }
        }
        
    } else {
        error_log("❌ Échec envoi WhatsApp différé à {$phone}: " . json_encode($result));
    }
    
} catch (Exception $e) {
    error_log("💥 Erreur dans delayed_send.php: " . $e->getMessage());
    exit(1);
}

exit(0);
?>
<?php
// backend/auth/send_code_after_delay.php
// Ce script s'exécute en arrière-plan après 30 secondes pour envoyer le code

if ($argc < 5) {
    exit(1);
}

$clientId = $argv[1];
$verificationCode = $argv[2];
$phone = $argv[3];
$name = $argv[4];

// Charger la configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../sendsms/sendwhatsapp.php';

try {
    // Vérifier si le client existe et n'est pas encore vérifié
    $checkQuery = "SELECT id_client FROM client WHERE id_client = :id_client AND is_verified = 0";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['id_client' => $clientId]);
    
    if ($checkStmt->rowCount() === 0) {
        // Le client a déjà vérifié ou n'existe plus
        exit(0);
    }
    
    // Vérifier si l'utilisateur a déjà reçu le code
    $codeCheckQuery = "SELECT id FROM verification_codes WHERE user_id = :client_id AND code = :code AND status = 'sent'";
    $codeCheckStmt = $bd->prepare($codeCheckQuery);
    $codeCheckStmt->execute(['client_id' => $clientId, 'code' => $verificationCode]);
    
    if ($codeCheckStmt->rowCount() > 0) {
        // Code déjà envoyé
        exit(0);
    }
    
    // Configuration WhatsApp
    $env = [
        'WHATSAPP_TOKEN' => 'VOTRE_TOKEN_WHATSAPP',
        'WHATSAPP_PHONE_NUMBER_ID' => 'VOTRE_ID_NUMERO_WHATSAPP'
    ];
    
    // Message à envoyer à l'utilisateur
    $message = "✅ Code de vérification Salacope\n\n";
    $message .= "Bonjour {$name},\n\n";
    $message .= "Votre code de vérification est : *{$verificationCode}*\n\n";
    $message .= "Utilisez ce code pour activer votre compte.\n";
    $message .= "⚠️ Ce code expire dans 30 minutes.\n";
    $message .= "🔒 Ne partagez jamais ce code.\n\n";
    $message .= "Merci,\nL'équipe S'alacoop";
    
    // Envoyer le message WhatsApp
    $result = sendWhatsAppMessage($name, $phone, $message, $env);
    
    if ($result['success']) {
        // Mettre à jour le statut dans la base de données
        $updateQuery = "UPDATE verification_codes SET status = 'sent', sent_at = NOW() WHERE user_id = :client_id AND code = :code";
        $updateStmt = $bd->prepare($updateQuery);
        $updateStmt->execute(['client_id' => $clientId, 'code' => $verificationCode]);
        
        // Envoyer une notification au support
        $supportMessage = "📱 Code envoyé à un client\n\n";
        $supportMessage .= "Nom: {$name}\n";
        $supportMessage .= "Téléphone: {$phone}\n";
        $supportMessage .= "Code envoyé: {$verificationCode}\n";
        $supportMessage .= "Heure: " . date('H:i:s');
        
        $supportResult = sendWhatsAppMessage("Support", "VOTRE_NUMERO_SUPPORT", $supportMessage, $env);
        
        error_log("Code WhatsApp envoyé à {$phone} avec succès");
        

        
    } else {
        
        error_log("Échec d'envoi du code WhatsApp à {$phone}: " . json_encode($result));
        
        // Réessayer après 60 secondes (optionnel)
        $retryCommand = "sleep 60 && php " . escapeshellarg(__FILE__) . " " . 
                       escapeshellarg($clientId) . " " . 
                       escapeshellarg($verificationCode) . " " . 
                       escapeshellarg($phone) . " " . 
                       escapeshellarg($name) . " > /dev/null 2>&1 &";
        shell_exec($retryCommand);
    }
    
} catch (Exception $e) {
    error_log("Erreur dans send_code_after_delay.php: " . $e->getMessage());
    exit(1);
}
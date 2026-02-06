<?php
// backend/sendsms/delayed_sender.php

// Ce fichier reçoit la demande et lance l'envoi différé du code

// Attendre le délai spécifié (défaut 10 secondes)
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
    
    // Vérifier si le client existe et n'est pas encore vérifié
    $checkQuery = "SELECT id_client, statut FROM client WHERE id_client = :client_id AND statut = 'new'";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['client_id' => $clientId]);
    
    if ($checkStmt->rowCount() === 0) {
        error_log("⚠️ delayed_sender: Client {$clientId} déjà vérifié ou non trouvé");
        exit();
    }
    
    // Vérifier si le code est toujours valide
    $codeCheckQuery = "SELECT id FROM verification_codes 
                       WHERE user_id = :client_id 
                       AND statut = 'pending'
                       AND expires_at > NOW()";
    
    $codeCheckStmt = $bd->prepare($codeCheckQuery);
    $codeCheckStmt->execute(['client_id' => $clientId]);
    
    if ($codeCheckStmt->rowCount() === 0) {
        error_log("❌ delayed_sender: Code non trouvé ou expiré pour client {$clientId}");
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
    
    // Préparer le message du code
    $message = "Bonjour {$name},\n\n";
    $message .= "Votre code de vérification pour l'application Salacoop est :\n\n";
    $message .= "🎯 *{$verificationCode}*\n\n";
    $message .= "Utilisez ce code pour compléter votre inscription.\n";
    $message .= "⏰ Ce code expire dans 30 minutes.\n";
    $message .= "🔒 Ne partagez jamais ce code avec personne.\n\n";
    $message .= "Si vous n'avez pas fait cette demande, ignorez ce message.\n\n";
    $message .= "Merci,\nL'équipe Salacoop";
    
    error_log("📤 delayed_sender: Envoi du code après {$delaySeconds}s pour client {$clientId} ({$phone})");
    
    // Envoyer le message WhatsApp
    $result = sendWhatsAppMessage($name, $phone, $message, $env);
    
    if ($result['success'] ?? false) {
        // Mettre à jour le statut dans verification_codes
        $updateQuery = "UPDATE verification_codes 
                        SET statut = 'sent',
                            sent_at = NOW()
                        WHERE user_id = :client_id 
                        AND statut = 'pending'
                        AND expires_at > NOW()";
        
        $updateStmt = $bd->prepare($updateQuery);
        $updateResult = $updateStmt->execute(['client_id' => $clientId]);
        
        if ($updateResult) {
            error_log("✅ delayed_sender: Code envoyé avec succès à {$phone}");
            
            // Mettre à jour la session si elle existe
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (isset($_SESSION['client_id']) && $_SESSION['client_id'] == $clientId) {
                $_SESSION['whatsapp_sent'] = true;
                $_SESSION['whatsapp_sent_time'] = time();
                $_SESSION['verification_code_sent'] = true;
            }
            session_write_close();
            
        } else {
            error_log("⚠️ delayed_sender: Code envoyé mais erreur de mise à jour BD");
        }
        
    } else {
        error_log("❌ delayed_sender: Échec envoi du code à {$phone}: " . json_encode($result));
    }
    
} catch (Exception $e) {
    error_log("💥 delayed_sender: Erreur: " . $e->getMessage());
}

// Répondre au serveur appelant (optionnel)
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'success' => true, 
    'processed_at' => date('Y-m-d H:i:s'),
    'client_id' => $clientId ?? null,
    'message' => 'Code de vérification envoyé avec délai'
]);
?>
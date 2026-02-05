<?php
// backend/sendsms/send_whatsapp_background.php

if ($argc < 5) {
    error_log("❌ Arguments insuffisants pour send_whatsapp_background.php");
    exit(1);
}

$clientId = $argv[1];
$verificationCode = $argv[2];
$phone = $argv[3];
$name = $argv[4];

error_log("🚀 WhatsApp: Début de l'envoi différé pour client {$clientId} ({$phone})");

// Charger la configuration
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    error_log("❌ WhatsApp: Fichier de configuration non trouvé: {$configPath}");
    exit(1);
}

require_once $configPath;

// Maintenant $env est disponible depuis config.php
// Mais si vous voulez y accéder directement, vous pouvez refaire parse_ini_file
$envFile = dirname(__DIR__) . '/../../.env'; // Ajustez le chemin selon votre structure
if (!file_exists($envFile)) {
    error_log("❌ WhatsApp: Fichier .env non trouvé: {$envFile}");
    // Essayez un autre chemin
    $envFile = dirname(__DIR__, 2) . '/.env';
}

if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
} else {
    error_log("⚠️ WhatsApp: Impossible de charger .env, utilisation des constantes");
    $envVars = [];
}

// Vérifier si le fichier sendwhatsapp.php existe
$sendWhatsappPath = __DIR__ . '/sendwhatsapp.php';
if (!file_exists($sendWhatsappPath)) {
    error_log("❌ WhatsApp: Fichier sendwhatsapp.php non trouvé");
    exit(1);
}

require_once $sendWhatsappPath;

try {
    // Vérifier la connexion à la base de données
    if (!isset($bd)) {
        throw new Exception("Connexion à la base de données non établie");
    }

    // 1. Vérifier si le client existe
    $checkQuery = "SELECT id_client, statut, tel, nom, prenom FROM client WHERE id_client = :id_client";
    $checkStmt = $bd->prepare($checkQuery);
    $checkStmt->execute(['id_client' => $clientId]);
    $client = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        error_log("❌ WhatsApp: Client {$clientId} non trouvé dans la base de données");
        exit(0);
    }
    
    // 2. Vérifier le statut du client
    if ($client['statut'] !== 'new') {
        error_log("⚠️ WhatsApp: Client {$clientId} déjà activé (statut: {$client['statut']})");
        exit(0);
    }
    
    // 3. Vérifier si le numéro de téléphone correspond
    if ($client['tel'] !== $phone) {
        error_log("⚠️ WhatsApp: Numéro de téléphone ne correspond pas pour client {$clientId}");
        exit(0);
    }
    
    // 4. Vérifier si le code est toujours valide
    $codeCheckQuery = "SELECT id FROM verification_codes 
                       WHERE user_id = :client_id 
                       AND statut = 'pending'
                       AND expires_at > NOW()";
    
    $codeCheckStmt = $bd->prepare($codeCheckQuery);
    $codeCheckStmt->execute(['client_id' => $clientId]);
    
    if ($codeCheckStmt->rowCount() === 0) {
        error_log("❌ WhatsApp: Aucun code valide trouvé pour client {$clientId}");
        exit(0);
    }
    
    // 5. Configuration WhatsApp depuis .env
    // Méthode 1: Utiliser les constantes définies dans config.php
    $env = [
        'WHATSAPP_TOKEN' => defined('WHATSAPP_TOKEN') ? WHATSAPP_TOKEN : '',
        'WHATSAPP_PHONE_NUMBER_ID' => defined('WHATSAPP_PHONE_NUMBER_ID') ? WHATSAPP_PHONE_NUMBER_ID : '',
        'WHATSAPP_SUPPORT_PHONE' => defined('WHATSAPP_SUPPORT_PHONE') ? WHATSAPP_SUPPORT_PHONE : '243962763130'
    ];
    
    // Méthode 2: Utiliser les variables $envVars si les constantes ne sont pas définies
    if (empty($env['WHATSAPP_TOKEN']) && isset($envVars['WHATSAPP_TOKEN'])) {
        $env['WHATSAPP_TOKEN'] = $envVars['WHATSAPP_TOKEN'];
    }
    if (empty($env['WHATSAPP_PHONE_NUMBER_ID']) && isset($envVars['WHATSAPP_PHONE_NUMBER_ID'])) {
        $env['WHATSAPP_PHONE_NUMBER_ID'] = $envVars['WHATSAPP_PHONE_NUMBER_ID'];
    }
    
    // Vérifier que les clés WhatsApp sont configurées
    if (empty($env['WHATSAPP_TOKEN']) || empty($env['WHATSAPP_PHONE_NUMBER_ID'])) {
        error_log("❌ WhatsApp: Configuration WhatsApp manquante dans .env");
        error_log("❌ WhatsApp: Token: " . (empty($env['WHATSAPP_TOKEN']) ? 'MANQUANT' : 'PRÉSENT'));
        error_log("❌ WhatsApp: Phone ID: " . (empty($env['WHATSAPP_PHONE_NUMBER_ID']) ? 'MANQUANT' : 'PRÉSENT'));
        exit(1);
    }
    
    error_log("✅ WhatsApp: Configuration chargée - Token: " . substr($env['WHATSAPP_TOKEN'], 0, 10) . "...");
    error_log("✅ WhatsApp: Phone Number ID: " . $env['WHATSAPP_PHONE_NUMBER_ID']);
    
    // 6. Préparer le message
    $message = "Votre code de vérification Salacoop est : *{$verificationCode}*\n\n";
    $message .= "Utilisez ce code pour activer votre compte.\n";
    $message .= "⚠️ Ce code expire dans 30 minutes.\n";
    $message .= "🔒 Ne partagez jamais ce code.\n\n";
    $message .= "Merci,\nL'équipe Salacoop";
    
    error_log("📤 WhatsApp: Tentative d'envoi à {$phone}");
    
    // 7. Envoyer le message WhatsApp
    $result = sendWhatsAppMessage($name, $phone, $message, $env);
    
    if ($result['success']) {
        // 8. Mettre à jour le statut dans verification_codes
        $updateQuery = "UPDATE verification_codes 
                        SET statut = 'sent', 
                            sent_at = NOW() 
                        WHERE user_id = :client_id 
                        AND statut = 'pending'
                        AND expires_at > NOW()";
        
        $updateStmt = $bd->prepare($updateQuery);
        $updateResult = $updateStmt->execute(['client_id' => $clientId]);
        
        if ($updateResult) {
            error_log("✅ WhatsApp: Code envoyé avec succès à {$phone}");
            error_log("✅ WhatsApp: Statut mis à jour dans la base de données");
        } else {
            error_log("⚠️ WhatsApp: Code envoyé mais erreur de mise à jour BD");
        }
        
        // 9. Envoyer notification au support (optionnel)
        if (!empty($env['WHATSAPP_SUPPORT_PHONE'])) {
            try {
                $supportMessage = "Code de vérification envoyé à un client\n\n";
                $supportMessage .= "Client: {$name}\n";
                $supportMessage .= "Téléphone: {$phone}\n";
                $supportMessage .= "Code: {$verificationCode}\n";
                $supportMessage .= "Heure: " . date('H:i:s');
                
                $supportResult = sendWhatsAppMessage(
                    "Support Salacoop", 
                    $env['WHATSAPP_SUPPORT_PHONE'], 
                    $supportMessage, 
                    $env
                );
                
                if ($supportResult['success']) {
                    error_log("📨 WhatsApp: Notification envoyée au support");
                } else {
                    error_log("⚠️ WhatsApp: Échec envoi notification support");
                }
            } catch (Exception $e) {
                error_log("⚠️ WhatsApp: Erreur lors de l'envoi au support: " . $e->getMessage());
            }
        }
        
    } else {
        error_log("❌ WhatsApp: Échec d'envoi à {$phone}");
        error_log("❌ WhatsApp: Détails: " . json_encode($result));
        
        // 10. Réessayer après 60 secondes (optionnel)
        $retryCommand = "sleep 60 && php " . escapeshellarg(__FILE__) . " " . 
                       escapeshellarg($clientId) . " " . 
                       escapeshellarg($verificationCode) . " " . 
                       escapeshellarg($phone) . " " . 
                       escapeshellarg($name) . " > /dev/null 2>&1 &";
        
        $retryPid = shell_exec($retryCommand);
        if ($retryPid) {
            error_log("🔄 WhatsApp: Réessai programmé dans 60s");
        }
    }
    
} catch (Exception $e) {
    error_log("💥 WhatsApp: Erreur dans send_whatsapp_background.php: " . $e->getMessage());
    exit(1);
}

error_log("✅ WhatsApp: Processus terminé pour client {$clientId}");
exit(0);
?>
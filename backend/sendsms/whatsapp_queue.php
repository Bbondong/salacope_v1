<?php


function addToWhatsAppQueue($clientId, $verificationCode, $phone, $name, $delaySeconds = 15) {
    $queueFile = __DIR__ . '/whatsapp_queue.json';
    
    // Lire la file d'attente existante
    $queue = [];
    if (file_exists($queueFile)) {
        $queue = json_decode(file_get_contents($queueFile), true) ?: [];
    }
    
    // Ajouter la tâche
    $task = [
        'client_id' => $clientId,
        'verification_code' => $verificationCode,
        'phone' => $phone,
        'name' => $name,
        'added_at' => time(),
        'execute_after' => time() + $delaySeconds,
        'status' => 'pending'
    ];
    
    $queue[] = $task;
    
    // Sauvegarder
    file_put_contents($queueFile, json_encode($queue, JSON_PRETTY_PRINT));
    
    return true;
}

function processWhatsAppQueue() {
    $queueFile = __DIR__ . '/whatsapp_queue.json';
    
    if (!file_exists($queueFile)) {
        return 0;
    }
    
    $queue = json_decode(file_get_contents($queueFile), true) ?: [];
    $now = time();
    $processed = 0;
    
    foreach ($queue as $key => $task) {
        if ($task['status'] === 'pending' && $task['execute_after'] <= $now) {
            // Exécuter la tâche
            require_once __DIR__ . '/sendwhatsapp.php';
            
            // Configuration
            $env = [
                'WHATSAPP_TOKEN' => defined('WHATSAPP_TOKEN') ? WHATSAPP_TOKEN : '',
                'WHATSAPP_PHONE_NUMBER_ID' => defined('WHATSAPP_PHONE_NUMBER_ID') ? WHATSAPP_PHONE_NUMBER_ID : ''
            ];
            
            $message = "Votre code de vérification Salacoop est : *{$task['verification_code']}*\n\n";
            $message .= "Utilisez ce code pour activer votre compte.\n";
            $message .= "⚠️ Ce code expire dans 30 minutes.\n";
            $message .= "🔒 Ne partagez jamais ce code.\n\n";
            $message .= "Merci,\nL'équipe Salacoop";
            
            $result = sendWhatsAppMessage($task['name'], $task['phone'], $message, $env);
            
            if ($result['success']) {
                $queue[$key]['status'] = 'sent';
                $queue[$key]['sent_at'] = $now;
                $processed++;
                
                // Mettre à jour la base de données
                require_once __DIR__ . '/../config.php';
                $updateQuery = "UPDATE verification_codes 
                                SET statut = 'sent', 
                                    sent_at = NOW() 
                                WHERE user_id = :client_id 
                                AND statut = 'pending'";
                $updateStmt = $bd->prepare($updateQuery);
                $updateStmt->execute(['client_id' => $task['client_id']]);
            } else {
                $queue[$key]['status'] = 'failed';
                $queue[$key]['error'] = $result['error'] ?? 'Erreur inconnue';
            }
        }
    }
    
    // Sauvegarder la file mise à jour
    file_put_contents($queueFile, json_encode($queue, JSON_PRETTY_PRINT));
    
    return $processed;
}
?>
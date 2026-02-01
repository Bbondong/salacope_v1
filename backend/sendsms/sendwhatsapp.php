<?php

function sendWhatsAppMessage(string $name, string $phone, string $message, array $env): array
{
    $token = $env['WHATSAPP_TOKEN'] ?? null;
    $phoneNumberId = $env['WHATSAPP_PHONE_NUMBER_ID'] ?? null;

    if (!$token || !$phoneNumberId) {
        return [
            'success' => false,
            'message' => 'Configuration WhatsApp manquante'
        ];
    }

    // Nettoyage du numéro (ex: +243812345678 → 243812345678)
    $phone = preg_replace('/[^0-9]/', '', $phone);

    if (strlen($phone) < 10) {
        return [
            'success' => false,
            'message' => 'Numéro WhatsApp invalide'
        ];
    }

    $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => $phone,
        'type' => 'text',
        'text' => [
            'body' => "👋 Bonjour {$name},\n\n{$message}"
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return [
            'success' => false,
            'message' => 'Erreur CURL',
            'error' => $curlError
        ];
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return [
            'success' => true,
            'response' => json_decode($response, true)
        ];
    }

    return [
        'success' => false,
        'http_code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

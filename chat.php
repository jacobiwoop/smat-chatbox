<?php


// Vérifie la présence du cookie automatiquement envoyé
if (!isset($_COOKIE['smat_token'])) {
  http_response_code(401);
  die("Token manquant");
}

// Traitement du message reçu en POST
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('messages.log', $data['text'] . "\n", FILE_APPEND);
//echo "Message reçu avec token: " . $_COOKIE['smat_token'];




function sendWebhookMessage($message, $token) {
    // URL du webhook
    $webhookUrl = 'http://13.48.149.255:5678/webhook/chating';
    
    // Préparation des données à envoyer
    $data = [
        'message' => $message,
        'token' => $token,
        'timestamp' => date('Y-m-d H:i:s'),
        'source' => 'webhook_gateway' // Optionnel: identifiant de source
    ];
    
    // Initialisation de cURL
    $ch = curl_init($webhookUrl);
    
    // Configuration des options cURL
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-Request-Signature: ' . hash_hmac('sha256', json_encode($data), 'your_secret_key') // Optionnel: signature de sécurité
        ],
        CURLOPT_TIMEOUT => 10 // Timeout de 10 secondes
    ];
    
    curl_setopt_array($ch, $options);
    
    // Exécution de la requête
    $response = curl_exec($ch);
    
    // Gestion des erreurs
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Erreur cURL: $error");
    }
    
    // Fermeture de la session
    curl_close($ch);
    
    // Retourne la réponse (pour traitement ultérieur)
    return $response;
}



// Exemple d'utilisation
try {
    $result = sendWebhookMessage($data['text'],  $_COOKIE['smat_token']);
    echo   $result;
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
//print_r($_SERVER) ;

?>




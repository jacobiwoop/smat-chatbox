<?php
function checkAndSetSmatToken() {
    $cookieName = "smat_token";
    $expiryTime = time() + (86400 * 30); // 30 jours (en secondes)

    // 1. Vérifie si le cookie existe et n'est pas expiré
    if (!isset($_COOKIE[$cookieName])) {
        // 2. Génère un nouvel ID unique
        $newToken = generateCustomId();
        
        // 3. Définit le nouveau cookie
        setcookie(
            $cookieName,
            $newToken,
            $expiryTime,
            "/",        // Accessible sur tout le domaine
            "",         // Domaine (optionnel)
            false,      // Secure (HTTPS uniquement ? false en dev, true en prod)
            true       // HttpOnly (empêche l'accès via JavaScript)
        );
        
        // Stocke aussi en session pour usage immédiat (optionnel)
        $_COOKIE[$cookieName] = $newToken;
        
        return $newToken;
    }
    
    // Si le cookie existe, retourne sa valeur
    return $_COOKIE[$cookieName];
}

// Fonction pour générer un ID unique (définie précédemment)
function generateCustomId() {
    $uniqid = uniqid();
    $timestamp = round(microtime(true) * 1000);
    $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5);
    return $uniqid . '-' . $timestamp . '-' . $randomString;
}

/**
 * Envoie un message et un token à un webhook
 * 
 * @param string $message Le contenu du message à envoyer
 * @param string $token Le token d'authentification
 * @return string La réponse du serveur distant
 */
// function sendWebhookMessage($message, $token) {
//     // URL du webhook
//     $webhookUrl = 'http://13.48.149.255:5678/webhook-test/chating';
    
//     // Préparation des données à envoyer
//     $data = [
//         'message' => $message,
//         'token' => $token,
//         'timestamp' => date('Y-m-d H:i:s'),
//         'source' => 'webhook_gateway' // Optionnel: identifiant de source
//     ];
    
//     // Initialisation de cURL
//     $ch = curl_init($webhookUrl);
    
//     // Configuration des options cURL
//     $options = [
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_POST => true,
//         CURLOPT_POSTFIELDS => json_encode($data),
//         CURLOPT_HTTPHEADER => [
//             'Content-Type: application/json',
//             'X-Request-Signature: ' . hash_hmac('sha256', json_encode($data), 'your_secret_key') // Optionnel: signature de sécurité
//         ],
//         CURLOPT_TIMEOUT => 10 // Timeout de 10 secondes
//     ];
    
//     curl_setopt_array($ch, $options);
    
//     // Exécution de la requête
//     $response = curl_exec($ch);
    
//     // Gestion des erreurs
//     if (curl_errno($ch)) {
//         $error = curl_error($ch);
//         curl_close($ch);
//         throw new Exception("Erreur cURL: $error");
//     }
    
//     // Fermeture de la session
//     curl_close($ch);
    
//     // Retourne la réponse (pour traitement ultérieur)
//     return $response;
// }



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
// $smatToken = checkAndSetSmatToken();
// echo "Token actuel : " . $smatToken;
?>

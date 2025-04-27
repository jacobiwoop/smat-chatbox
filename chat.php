<?php

require_once "./fonction.php";
require_once "./parsedown.php";


// Vérifie la présence du cookie automatiquement envoyé
if (!isset($_COOKIE['smat_token'])) {
  http_response_code(401);
  die("Token manquant");
}

// Traitement du message reçu en POST
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('messages.log', $data['text'] . "\n", FILE_APPEND);
//echo "Message reçu avec token: " . $_COOKIE['smat_token'];



$Parsedown = new Parsedown();




// Exemple d'utilisation
try {
    $result = sendWebhookMessage($data['text'],  $_COOKIE['smat_token']);
    echo $Parsedown->text($result);
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
//print_r($_SERVER) ;

?>




<?php
require 'vendor/autoload.php';

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$data = json_decode(file_get_contents('php://input'));
$text = $data->text; // Obtener el texto del mensaje

$client = new Client("AlzaSyDsCPE1euarfCZKYjfcf77j7BLeVHDXmos0"); // Usa tu API Key

$response = $client->geminiPro()->generateContent(
    new TextPart($text)
);

echo $response->text();
?>

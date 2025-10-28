<?php
// chatbot.php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

// Llamar a la API de Gemini con el mensaje
$message = $data['message'] ?? '';

// Configura tu API de Gemini aquí y procesa la respuesta
$response = getGeminiResponse($message); // Debes definir esta función

echo json_encode(['response' => $response]);

function getGeminiResponse($message) {
    // Implementa la lógica para obtener una respuesta de Gemini
    return 'Respuesta a: ' . $message;
}

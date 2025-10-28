<?php
declare(strict_types=1);
require_once __DIR__.'/../config/secrets.php';

/**
 * Envía email por Brevo (API v3).
 * Retorna [true, 'messageId'] o [false, 'error'].
 */
function send_brevo_mail(string $toEmail, string $toName, string $subject, string $html): array {
  $payload = [
    'sender'      => ['name' => MAIL_SENDER_NAME, 'email' => MAIL_SENDER_EMAIL],
    'to'          => [['email' => $toEmail, 'name' => $toName ?: $toEmail]],
    'subject'     => $subject,
    'htmlContent' => $html
  ];

  $ch = curl_init('https://api.brevo.com/v3/smtp/email');
  curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
      'accept: application/json',
      'content-type: application/json',
      'api-key: '.BREVO_API_KEY
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE)
  ]);

  $response = curl_exec($ch);
  $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err      = curl_error($ch);
  curl_close($ch);

  if ($err) return [false, 'cURL error: '.$err];
  if ($http < 200 || $http >= 300) return [false, 'HTTP '.$http.' → '.$response];

  $data  = json_decode($response, true);
  $msgId = $data['messageId'] ?? 'sent';
  return [true, $msgId];
}

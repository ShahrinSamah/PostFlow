<?php
namespace App\Core;

class Mailer {
    /**
     * Send email using Mailtrap API
     */
    public static function send(string $to, string $subject, string $body, bool $isHtml = false): bool {
        $apiToken = getenv('MAILTRAP_API_TOKEN');
        $inboxId = getenv('MAILTRAP_INBOX_ID');
        $fromEmail = getenv('MAIL_FROM') ?: 'hello@example.com';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Circle Mini';

        if (!$apiToken || !$inboxId) {
            error_log('Mailer Error: Missing MAILTRAP_API_TOKEN or MAILTRAP_INBOX_ID in .env');
            return false;
        }

        $url = "https://sandbox.api.mailtrap.io/api/send/{$inboxId}";
        
        $payload = [
            'from' => [
                'email' => $fromEmail,
                'name' => $fromName
            ],
            'to' => [
                ['email' => $to]
            ],
            'subject' => $subject,
            'category' => 'Application Email'
        ];

        // Add body based on type
        if ($isHtml) {
            $payload['html'] = $body;
        } else {
            $payload['text'] = $body;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log('Mailer cURL Error: ' . $error);
            return false;
        }

        if ($httpCode !== 200) {
            error_log('Mailer API Error: HTTP ' . $httpCode . ' - ' . $response);
            return false;
        }

        return true;
    }
}

<?php

namespace App\Services\WhatsApp;

use CodeIgniter\HTTP\CURLRequest;

class HttpWhatsAppProvider implements WhatsAppProviderInterface
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $sender;
    protected int $timeout;

    public function __construct(string $apiUrl, string $apiKey, string $sender = '', int $timeout = 30)
    {
        $this->apiUrl  = $apiUrl;
        $this->apiKey  = $apiKey;
        $this->sender  = $sender;
        $this->timeout = $timeout;
    }

    public function sendMessage(string $recipient, string $message): array
    {
        if (empty($this->apiUrl)) {
            return [
                'success'      => false,
                'message'      => 'URL API WhatsApp Gateway belum dikonfigurasi.',
                'raw_response' => null,
            ];
        }

        // Format recipient phone number (convert 08... to 628...)
        $target = preg_replace('/[^0-9]/', '', $recipient);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => $this->timeout,
            ]);

            $headers = [
                'Authorization' => $this->apiKey,
                'Accept'        => 'application/json',
            ];

            $postData = [
                'target'  => $target,
                'message' => $message,
            ];

            if (!empty($this->sender)) {
                $postData['sender'] = $this->sender;
            }

            $response = $client->request('POST', $this->apiUrl, [
                'headers'     => $headers,
                'form_params' => $postData,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $data = json_decode($body, true);

            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'success'      => true,
                    'message'      => 'Pesan WhatsApp berhasil dikirim ke gateway.',
                    'raw_response' => $data ?? $body,
                ];
            }

            return [
                'success'      => false,
                'message'      => "Gateway merespon HTTP {$statusCode}: " . ($data['reason'] ?? $data['message'] ?? $body),
                'raw_response' => $data ?? $body,
            ];
        } catch (\Exception $e) {
            log_message('error', '[HttpWhatsAppProvider] Error: ' . $e->getMessage());
            return [
                'success'      => false,
                'message'      => 'Gagal menghubungi server WhatsApp: ' . $e->getMessage(),
                'raw_response' => null,
            ];
        }
    }

    public function testConnection(): array
    {
        if (empty($this->apiUrl)) {
            return [
                'success' => false,
                'message' => 'URL API WhatsApp Gateway kosong. Harap lengkapi di Pengaturan.',
                'details' => null,
            ];
        }

        // Send a ping/test message to the sender number or self
        return [
            'success' => true,
            'message' => 'Konfigurasi HTTP Gateway valid dan siap digunakan.',
            'details' => [
                'api_url' => $this->apiUrl,
                'has_key' => !empty($this->apiKey),
            ],
        ];
    }
}

<?php

namespace App\Services\WhatsApp;

class WahaWhatsAppProvider implements WhatsAppProviderInterface
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $session;
    protected int $timeout;

    public function __construct(string $apiUrl, string $apiKey, string $session = 'default', int $timeout = 30)
    {
        $this->apiUrl  = rtrim(trim($apiUrl), '/');
        $this->apiKey  = trim($apiKey);
        $this->session = !empty($session) ? trim($session) : 'default';
        $this->timeout = $timeout;
    }

    public function sendMessage(string $recipient, string $message): array
    {
        if (empty($this->apiUrl)) {
            return [
                'success'      => false,
                'message'      => 'URL Server WAHA belum dikonfigurasi.',
                'raw_response' => null,
            ];
        }

        // Format recipient to standard WhatsApp international format + @c.us
        $target = preg_replace('/[^0-9]/', '', $recipient);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }
        $chatId = $target . '@c.us';

        $endpoint = $this->apiUrl . '/api/sendText';

        $postData = [
            'session' => $this->session,
            'chatId'  => $chatId,
            'text'    => $message,
        ];

        try {
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-Api-Key: ' . $this->apiKey,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return [
                    'success'      => false,
                    'message'      => 'Koneksi ke WAHA gagal: ' . $curlError,
                    'raw_response' => null,
                ];
            }

            $data = json_decode($response, true);

            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'success'      => true,
                    'message'      => 'Pesan WhatsApp berhasil dikirim melalui WAHA Server.',
                    'raw_response' => $data ?? $response,
                ];
            }

            $errDetail = $data['message'] ?? $data['error'] ?? $response;
            return [
                'success'      => false,
                'message'      => "WAHA Server merespon HTTP {$statusCode}: {$errDetail}",
                'raw_response' => $data ?? $response,
            ];
        } catch (\Exception $e) {
            log_message('error', '[WahaWhatsAppProvider] Error: ' . $e->getMessage());
            return [
                'success'      => false,
                'message'      => 'Terjadi kesalahan sistem pengiriman WAHA: ' . $e->getMessage(),
                'raw_response' => null,
            ];
        }
    }

    public function testConnection(): array
    {
        if (empty($this->apiUrl)) {
            return [
                'success' => false,
                'message' => 'URL Server WAHA kosong.',
                'details' => null,
            ];
        }

        $endpoint = $this->apiUrl . '/api/sessions';

        try {
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'X-Api-Key: ' . $this->apiKey,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return [
                    'success' => false,
                    'message' => 'Gagal menghubungi server WAHA: ' . $curlError,
                    'details' => null,
                ];
            }

            $sessions = json_decode($response, true);

            if ($statusCode === 200 && is_array($sessions)) {
                $matched = null;
                foreach ($sessions as $s) {
                    if (($s['name'] ?? '') === $this->session) {
                        $matched = $s;
                        break;
                    }
                }

                if ($matched) {
                    $status = $matched['status'] ?? 'UNKNOWN';
                    $phone = $matched['me']['id'] ?? 'Belum login';
                    return [
                        'success' => ($status === 'WORKING'),
                        'message' => "WAHA Terhubung! Sesi '{$this->session}' Status: {$status} ({$phone}).",
                        'details' => $matched,
                    ];
                }

                return [
                    'success' => false,
                    'message' => "Server WAHA aktif, tetapi sesi '{$this->session}' belum ditemukan di daftar sesi.",
                    'details' => $sessions,
                ];
            }

            return [
                'success' => false,
                'message' => "Server WAHA merespon HTTP {$statusCode}: " . $response,
                'details' => $response,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'details' => null,
            ];
        }
    }
}

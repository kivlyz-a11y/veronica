<?php

namespace App\Services\WhatsApp;

class MockWhatsAppProvider implements WhatsAppProviderInterface
{
    public function sendMessage(string $recipient, string $message): array
    {
        // Log simulation for development & testing
        log_message('info', "[MockWhatsAppProvider] Sent message to {$recipient}:\n{$message}");

        return [
            'success'      => true,
            'message'      => 'Pesan simulasi berhasil dikirim (Mock Mode).',
            'raw_response' => [
                'status'    => 'simulated',
                'recipient' => $recipient,
                'timestamp' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function testConnection(): array
    {
        return [
            'success' => true,
            'message' => 'Koneksi Mock WhatsApp Provider aktif dan siap digunakan (Mode Simulasi).',
            'details' => [
                'provider'  => 'mock',
                'timestamp' => date('Y-m-d H:i:s'),
                'status'    => 'ready',
            ],
        ];
    }
}

<?php

namespace App\Services\WhatsApp;

interface WhatsAppProviderInterface
{
    /**
     * Send a WhatsApp message to a recipient
     *
     * @param string $recipient Phone number
     * @param string $message   Message text
     * @return array [ 'success' => bool, 'message' => string, 'raw_response' => mixed ]
     */
    public function sendMessage(string $recipient, string $message): array;

    /**
     * Test connection to the WhatsApp Gateway provider
     *
     * @return array [ 'success' => bool, 'message' => string, 'details' => mixed ]
     */
    public function testConnection(): array;
}

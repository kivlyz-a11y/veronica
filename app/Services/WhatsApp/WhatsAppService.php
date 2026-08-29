<?php

namespace App\Services\WhatsApp;

use App\Models\NotificationModel;
use App\Models\SystemSettingModel;

class WhatsAppService
{
    protected WhatsAppProviderInterface $provider;
    protected NotificationModel $notificationModel;
    protected SystemSettingModel $settingModel;

    public function __construct(?WhatsAppProviderInterface $provider = null)
    {
        $this->notificationModel = new NotificationModel();
        $this->settingModel      = new SystemSettingModel();

        if ($provider !== null) {
            $this->provider = $provider;
        } else {
            $this->provider = $this->resolveProvider();
        }
    }

    /**
     * Resolve configured WhatsApp provider
     */
    protected function resolveProvider(): WhatsAppProviderInterface
    {
        $providerType = $this->settingModel->getVal('wa_provider', env('WHATSAPP_PROVIDER', 'mock'));

        if ($providerType === 'waha' || ($providerType === 'http' && strpos($this->settingModel->getVal('wa_api_url', ''), '3000') !== false)) {
            $apiUrl  = $this->settingModel->getVal('wa_api_url', env('WHATSAPP_API_URL', 'http://36.95.108.50:3000'));
            $apiKey  = $this->settingModel->getVal('wa_api_key', env('WHATSAPP_API_KEY', ''));
            $session = $this->settingModel->getVal('wa_session_name', env('WHATSAPP_SESSION', 'test'));
            $timeout = (int)$this->settingModel->getVal('wa_timeout', env('WHATSAPP_TIMEOUT', 30));

            return new WahaWhatsAppProvider($apiUrl, $apiKey, $session, $timeout);
        }

        if ($providerType === 'http') {
            $apiUrl  = $this->settingModel->getVal('wa_api_url', env('WHATSAPP_API_URL', ''));
            $apiKey  = $this->settingModel->getVal('wa_api_key', env('WHATSAPP_API_KEY', ''));
            $sender  = $this->settingModel->getVal('wa_sender_number', env('WHATSAPP_SENDER', ''));
            $timeout = (int)$this->settingModel->getVal('wa_timeout', env('WHATSAPP_TIMEOUT', 30));

            return new HttpWhatsAppProvider($apiUrl, $apiKey, $sender, $timeout);
        }

        return new MockWhatsAppProvider();
    }

    /**
     * Replace template variables in a text string
     */
    public function renderTemplate(string $template, array $data): string
    {
        $defaults = [
            'nama_instansi' => $this->settingModel->getVal('institution_name', 'Pengadilan Agama Penajam'),
            'alamat_instansi' => $this->settingModel->getVal('institution_address', ''),
            'telepon_instansi' => $this->settingModel->getVal('institution_phone', ''),
            'url_cek_status' => site_url('cek-status'),
        ];

        $merged = array_merge($defaults, $data);

        foreach ($merged as $key => $val) {
            $template = str_replace('{{' . $key . '}}', (string)$val, $template);
            $template = str_replace('{{ ' . $key . ' }}', (string)$val, $template);
        }

        return $template;
    }

    /**
     * Send notification with idempotency check and database logging
     */
    public function send(
        string $recipient,
        string $message,
        string $type,
        ?int $applicationId = null,
        ?string $eventKey = null
    ): array {
        // Idempotency check: if eventKey provided and already sent, return existing
        if (!empty($eventKey)) {
            $existing = $this->notificationModel->where('event_key', $eventKey)->first();
            if ($existing && $existing['status'] === 'sent') {
                return [
                    'success'         => true,
                    'message'         => 'Pesan telah terkirim sebelumnya (Idempotent).',
                    'notification_id' => $existing['id'],
                    'idempotent'      => true,
                ];
            }
        }

        // Create or get notification record
        $notificationId = null;
        if (!empty($eventKey) && isset($existing)) {
            $notificationId = $existing['id'];
            $this->notificationModel->update($notificationId, [
                'attempts' => $existing['attempts'] + 1,
            ]);
        } else {
            $notificationId = $this->notificationModel->insert([
                'application_id' => $applicationId,
                'type'           => $type,
                'event_key'      => $eventKey,
                'recipient'      => $recipient,
                'message'        => $message,
                'status'         => 'pending',
                'attempts'       => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        // Send via provider
        $result = $this->provider->sendMessage($recipient, $message);

        if ($result['success']) {
            $this->notificationModel->update($notificationId, [
                'status'        => 'sent',
                'sent_at'       => date('Y-m-d H:i:s'),
                'error_message' => null,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->notificationModel->update($notificationId, [
                'status'        => 'failed',
                'error_message' => $result['message'],
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return array_merge($result, ['notification_id' => $notificationId]);
    }

    /**
     * Test provider connection
     */
    public function testConnection(): array
    {
        return $this->provider->testConnection();
    }
}

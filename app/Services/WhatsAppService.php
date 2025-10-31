<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $phoneNumberId;
    protected string $accessToken;
    protected string $baseUrl = 'https://graph.facebook.com/v18.0';

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->accessToken = config('services.whatsapp.access_token');
    }

    public function sendMessage(string $to, string $message): array
    {
        $to = $this->formatPhoneNumber($to);

        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json()['messages'][0]['id'] ?? null,
                ];
            }

            Log::error('WhatsApp message failed', [
                'to' => $to,
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => $response->json()['error']['message'] ?? 'Failed to send message',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendTemplate(string $to, string $templateName, array $parameters = []): array
    {
        $to = $this->formatPhoneNumber($to);

        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'id',
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => $parameters,
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json()['messages'][0]['id'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['error']['message'] ?? 'Failed to send template',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp template error', [
                'to' => $to,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendOTP(string $to, string $code): array
    {
        $message = "Kode OTP SobriZIS Anda adalah: *{$code}*\n\nKode berlaku selama 5 menit.\nJangan bagikan kode ini kepada siapapun.";
        
        return $this->sendMessage($to, $message);
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    public function verifyWebhook(array $data, string $verifyToken): bool
    {
        return ($data['hub_verify_token'] ?? '') === $verifyToken;
    }
}

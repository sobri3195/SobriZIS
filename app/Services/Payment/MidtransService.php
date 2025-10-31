<?php

namespace App\Services\Payment;

use App\Models\Donation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->baseUrl = $this->isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    public function createTransaction(Donation $donation, array $options = []): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $donation->order_id,
                'gross_amount' => (int) $donation->total_amount,
            ],
            'customer_details' => [
                'first_name' => $donation->donor_name ?? 'Anonymous',
                'email' => $donation->donor_email,
                'phone' => $donation->donor_phone,
            ],
            'item_details' => [
                [
                    'id' => $donation->program_id,
                    'name' => $donation->program->title,
                    'price' => (int) $donation->total_amount,
                    'quantity' => 1,
                ],
            ],
            'enabled_payments' => $options['payment_methods'] ?? [
                'qris', 'gopay', 'shopeepay', 'other_qris',
                'bca_va', 'bni_va', 'bri_va', 'permata_va',
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post("{$this->baseUrl}/v2/charge", $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                $donation->update([
                    'gateway_transaction_id' => $data['transaction_id'] ?? null,
                    'gateway_response' => json_encode($data),
                    'status' => $this->mapStatus($data['transaction_status'] ?? 'pending'),
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $data['transaction_id'],
                    'redirect_url' => $data['redirect_url'] ?? null,
                    'qr_code' => $data['actions'][0]['url'] ?? null,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['error_messages'][0] ?? 'Payment creation failed',
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans payment creation failed', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        if (!$this->verifySignature($payload)) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $orderId = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        $donation = Donation::where('order_id', $orderId)->first();
        
        if (!$donation) {
            return ['success' => false, 'message' => 'Donation not found'];
        }

        $status = $this->mapStatus($transactionStatus, $fraudStatus);
        
        $donation->update([
            'status' => $status,
            'gateway_transaction_id' => $payload['transaction_id'],
            'gateway_response' => json_encode($payload),
            'paid_at' => in_array($status, ['success']) ? now() : null,
        ]);

        return ['success' => true, 'donation' => $donation];
    }

    protected function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        
        return $signature === ($payload['signature_key'] ?? '');
    }

    protected function mapStatus(string $transactionStatus, string $fraudStatus = 'accept'): string
    {
        if ($fraudStatus === 'challenge' || $fraudStatus === 'deny') {
            return 'failed';
        }

        return match ($transactionStatus) {
            'capture', 'settlement' => 'success',
            'pending' => 'waiting_payment',
            'deny', 'cancel' => 'failed',
            'expire' => 'expired',
            'refund' => 'refunded',
            default => 'pending',
        };
    }

    public function getTransactionStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get("{$this->baseUrl}/v2/{$orderId}/status");

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to get Midtrans transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}

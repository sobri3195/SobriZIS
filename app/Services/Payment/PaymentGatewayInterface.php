<?php

namespace App\Services\Payment;

use App\Models\Donation;

interface PaymentGatewayInterface
{
    public function createTransaction(Donation $donation, array $options = []): array;
    
    public function handleWebhook(array $payload): array;
    
    public function getTransactionStatus(string $orderId): array;
}

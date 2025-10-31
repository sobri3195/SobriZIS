# Payment Gateway Integrations

## Overview

SobriZIS supports multiple payment gateways to provide flexibility for donors. The system is designed with an abstraction layer that makes it easy to add new payment providers.

## Supported Payment Gateways

### 1. Midtrans (Default)

Midtrans is the default payment gateway, offering comprehensive payment methods popular in Indonesia.

#### Supported Payment Methods
- **QRIS**: Universal QR code for all e-wallets and banks
- **GoPay**: Direct integration
- **ShopeePay**: Direct integration  
- **OVO**: Via QRIS
- **DANA**: Via QRIS
- **Virtual Accounts**: BCA, BNI, BRI, Mandiri, Permata
- **Credit/Debit Cards**: Visa, Mastercard, JCB
- **Bank Transfer**: Manual confirmation

#### Setup Midtrans

**1. Register Account**
- Sandbox: [https://dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com)
- Production: [https://dashboard.midtrans.com](https://dashboard.midtrans.com)

**2. Get API Keys**
1. Log in to Midtrans Dashboard
2. Go to Settings > Access Keys
3. Copy Server Key and Client Key

**3. Configure in SobriZIS**

Edit `.env`:
```env
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx  # Sandbox
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx  # Sandbox
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

For production, replace with production keys and set `MIDTRANS_IS_PRODUCTION=true`.

**4. Configure Webhook**

1. In Midtrans Dashboard, go to Settings > Configuration
2. Set Payment Notification URL:
   ```
   https://yourdomain.com/api/webhooks/midtrans
   ```
3. Enable HTTP notification
4. Save settings

**5. Test Integration**

Use Midtrans test cards:
- Success: `4811 1111 1111 1114`
- Challenge: `4411 1111 1111 1118`
- Failure: `4911 1111 1111 1113`

CVV: `123`, Expiry: Any future date

### 2. Xendit (Alternative)

Xendit is an alternative payment gateway with similar features.

#### Setup Xendit

**1. Register**
- Visit [https://dashboard.xendit.co](https://dashboard.xendit.co)
- Create account and verify business

**2. Get API Key**
1. Go to Settings > API Keys
2. Copy Secret Key
3. Generate Webhook Token

**3. Configure in SobriZIS**

```env
XENDIT_SECRET_KEY=xnd_development_xxxxx
XENDIT_WEBHOOK_TOKEN=your_webhook_token
XENDIT_IS_PRODUCTION=false
```

**4. Set Webhook URL**
```
https://yourdomain.com/api/webhooks/xendit
```

### 3. DOKU (Optional)

DOKU provides payment solutions with local Indonesian bank support.

#### Setup DOKU

```env
DOKU_CLIENT_ID=your_client_id
DOKU_SECRET_KEY=your_secret_key
DOKU_IS_PRODUCTION=false
```

Contact DOKU for merchant account setup and API credentials.

## Payment Flow

### Standard Donation Flow

1. **Donor initiates donation**
   - Selects program
   - Enters amount and personal details
   - Chooses payment method

2. **System creates donation record**
   - Generates unique order ID
   - Adds unique code (1-999) for easy identification
   - Sets status to "pending"
   - Sets expiry time (default: 24 hours)

3. **Payment gateway integration**
   - System calls payment gateway API
   - Receives payment instructions (VA number, QR code, redirect URL)
   - Returns payment details to donor

4. **Donor completes payment**
   - Scans QR code or enters VA number
   - Completes payment via chosen method
   - Payment gateway processes transaction

5. **Webhook notification**
   - Payment gateway sends notification to webhook URL
   - System verifies signature
   - Updates donation status
   - Triggers post-payment actions

6. **Post-payment processing**
   - Updates program collected amount
   - Generates receipt (PDF)
   - Sends confirmation email/WhatsApp
   - Creates accounting journal entry
   - Logs audit trail

## Webhook Security

### Signature Verification

All webhooks must be verified before processing:

#### Midtrans Signature Verification

```php
$orderId = $payload['order_id'];
$statusCode = $payload['status_code'];
$grossAmount = $payload['gross_amount'];
$serverKey = config('services.midtrans.server_key');

$signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

if ($signature !== $payload['signature_key']) {
    // Invalid signature
    abort(403);
}
```

#### Xendit Callback Token

```php
$callbackToken = $request->header('x-callback-token');
$expectedToken = config('services.xendit.webhook_token');

if ($callbackToken !== $expectedToken) {
    // Invalid token
    abort(403);
}
```

## Payment Status Mapping

### Midtrans Status

| Midtrans Status | SobriZIS Status | Description |
|----------------|-----------------|-------------|
| pending | waiting_payment | Waiting for donor payment |
| capture | success | Card payment captured |
| settlement | success | Payment settled |
| deny | failed | Payment denied |
| cancel | failed | Payment cancelled |
| expire | expired | Payment expired |
| refund | refunded | Payment refunded |

### Handling Payment Updates

The system handles these scenarios:

1. **Success Payment**
   - Update donation status to "success"
   - Set paid_at timestamp
   - Increment program collected_amount
   - Generate receipt
   - Send notification

2. **Failed Payment**
   - Update status to "failed"
   - Log reason
   - Allow retry

3. **Expired Payment**
   - Update status to "expired"
   - Notify donor
   - Allow new donation

4. **Pending Payment**
   - Maintain "waiting_payment" status
   - Send reminder (optional)

## Testing Payment Integration

### Test in Sandbox Mode

**1. Create Test Donation**
```bash
curl -X POST https://your-domain.com/api/v1/donations \
  -H "Content-Type: application/json" \
  -d '{
    "program_id": 1,
    "amount": 100000,
    "donor_name": "Test User",
    "donor_email": "test@example.com",
    "donor_phone": "081234567890",
    "payment_method": "qris"
  }'
```

**2. Simulate Webhook (Midtrans)**
```bash
curl -X POST https://your-domain.com/api/webhooks/midtrans \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": "INV-20240115-ABC123",
    "status_code": "200",
    "gross_amount": "100000",
    "transaction_status": "settlement",
    "signature_key": "computed_signature"
  }'
```

**3. Check Logs**
```bash
tail -f storage/logs/laravel.log
```

## Common Issues & Solutions

### Issue: Webhook Not Received

**Symptoms:**
- Donation stays in "pending" status
- No webhook logs in laravel.log

**Solutions:**
1. Verify webhook URL is publicly accessible (not localhost)
2. Check firewall/security groups allow incoming requests
3. Ensure HTTPS is enabled (required in production)
4. Test webhook URL manually:
   ```bash
   curl -X POST https://your-domain.com/api/webhooks/midtrans
   ```
5. Check Midtrans Dashboard > Settings > Webhook Logs

### Issue: Invalid Signature Error

**Symptoms:**
- Webhook received but returns 403 error
- Log shows "Invalid signature"

**Solutions:**
1. Verify `MIDTRANS_SERVER_KEY` in `.env` matches dashboard
2. Ensure no extra spaces or line breaks in `.env`
3. Check gross_amount includes unique code
4. Verify signature calculation algorithm

### Issue: Payment Stuck in Processing

**Symptoms:**
- Donor paid but status doesn't update
- Manual check shows payment successful

**Solutions:**
1. Manually trigger status check:
   ```php
   php artisan donation:check-status {order_id}
   ```
2. Check queue is running:
   ```bash
   php artisan queue:work
   ```
3. Review payment gateway dashboard
4. Contact gateway support if persistent

### Issue: Double Payment Prevention

**Solution:** System uses idempotency with order_id:
- Same order_id won't create duplicate
- Unique constraint on order_id column
- Webhook handler checks existing status

## Custom Payment Gateway

To add a new payment gateway:

**1. Create Service Class**

```php
// app/Services/Payment/CustomGatewayService.php
namespace App\Services\Payment;

use App\Models\Donation;

class CustomGatewayService implements PaymentGatewayInterface
{
    public function createTransaction(Donation $donation, array $options = []): array
    {
        // Implement payment creation
    }
    
    public function handleWebhook(array $payload): array
    {
        // Implement webhook handling
    }
    
    public function getTransactionStatus(string $orderId): array
    {
        // Implement status check
    }
}
```

**2. Register in Service Provider**

```php
$this->app->bind('payment.custom', function() {
    return new CustomGatewayService();
});
```

**3. Add Configuration**

```php
// config/services.php
'custom_gateway' => [
    'api_key' => env('CUSTOM_GATEWAY_API_KEY'),
    'secret' => env('CUSTOM_GATEWAY_SECRET'),
],
```

**4. Add Webhook Route**

```php
// routes/api.php
Route::post('/webhooks/custom', [WebhookController::class, 'custom']);
```

## Best Practices

1. **Always use HTTPS in production**
2. **Validate webhook signatures**
3. **Log all payment transactions**
4. **Handle idempotency**
5. **Set appropriate timeouts**
6. **Implement retry logic for failed API calls**
7. **Monitor payment success rates**
8. **Keep API keys secure (never commit to git)**
9. **Test thoroughly in sandbox before production**
10. **Have rollback plan for payment issues**

## Production Checklist

- [ ] API keys changed from sandbox to production
- [ ] Webhook URL uses HTTPS
- [ ] Webhook signature verification enabled
- [ ] Payment methods tested with real transactions
- [ ] Error handling implemented
- [ ] Logging configured
- [ ] Monitoring/alerts set up
- [ ] Queue worker running with supervisor
- [ ] Database backups enabled
- [ ] Reconciliation process documented

## Support

For payment gateway issues:
- **Midtrans**: [https://support.midtrans.com](https://support.midtrans.com)
- **Xendit**: [https://help.xendit.co](https://help.xendit.co)
- **DOKU**: support@doku.com

For SobriZIS integration issues:
- Email: support@sobrizis.com
- Documentation: https://docs.sobrizis.com

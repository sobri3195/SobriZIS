# SobriZIS - Zakat, Infaq, Sedekah Management System

**Version:** 1.0.0  
**License:** Regular License (Single Organization) / Extended License (Multi-Tenant)  
**Author:** SobriZIS Team

## Overview

SobriZIS is a comprehensive Laravel + Vue.js application designed for Islamic charitable organizations to manage Zakat, Infaq, and Sedekah (ZIS) donations with complete transparency and accountability. The system supports multiple payment gateways (QRIS, Virtual Account, E-Wallet), WhatsApp notifications, and provides robust audit trails.

## Key Features

### For Donors
- **Registration & Authentication**: Email/WhatsApp OTP login, 2FA support
- **Zakat Calculator**: Calculate zakat for Maal, Profession, Gold, Agriculture
- **Multiple Payment Methods**: QRIS, Virtual Accounts (BCA, BNI, BRI, Mandiri), E-Wallets (GoPay, OVO, DANA, ShopeePay)
- **Donation History**: View all donations with downloadable PDF receipts
- **Privacy Controls**: Donate anonymously or publicly
- **Reminders**: Set up recurring donation reminders
- **Transparency**: Real-time program progress, distribution proofs

### For Administrators
- **KPI Dashboard**: Real-time statistics (today/month/year), cash flow, program balances
- **Program Management**: Create and manage donation programs by category and asnaf
- **Mustahik Management**: Verify and manage beneficiaries
- **Distribution Management**: Record disbursements with proof uploads, multi-level approval
- **Accounting**: Simple journal entries, automatic amil calculation (≤12.5%)
- **Reports**: Generate comprehensive reports by period, program, asnaf
- **Audit Trail**: Complete activity logs with actor, action, IP address
- **Communications**: WhatsApp/Email broadcasting with segmentation
- **Multi-Tenant** (Optional): Manage multiple organizations with data isolation

### Public Features
- **Transparency Page**: Real-time donation counter, program listings
- **Program Details**: Progress bars, updates, photo galleries
- **Embeddable Widgets**: Iframe/script for external websites

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Vue 3 + Inertia.js, Tailwind CSS
- **Database**: MySQL 8+ / PostgreSQL 14+
- **Cache/Queue**: Redis
- **Payment**: Midtrans (default), Xendit, DOKU support
- **Notifications**: WhatsApp Cloud API, Email (SMTP)
- **Storage**: Local / AWS S3 compatible

## System Requirements

- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18 LTS or higher
- **NPM**: 9.x or higher
- **MySQL**: 8.0+ or PostgreSQL: 14+
- **Redis**: 6.x+ (optional, recommended for queue)
- **Web Server**: Nginx or Apache with mod_rewrite

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/sobrizis.git
cd sobrizis
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure:

- **Database**: Set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- **Mail**: Configure `MAIL_*` settings
- **Midtrans**: Add `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`
- **WhatsApp**: Add `WHATSAPP_PHONE_NUMBER_ID`, `WHATSAPP_ACCESS_TOKEN`
- **Storage**: Configure AWS S3 or use local storage

### 4. Database Setup

```bash
php artisan migrate --seed
```

This will create tables and seed demo data:
- 5 programs (Beasiswa, Santunan Yatim, UMKM, Kesehatan, Ramadhan)
- 50 sample donations
- 10 mustahik records
- 6 blog posts
- 12 gallery images

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Build Assets

```bash
npm run build
# or for development
npm run dev
```

### 7. Queue Worker (Optional but Recommended)

```bash
php artisan queue:work --tries=3
```

### 8. Schedule (Cron)

Add to your crontab:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Webhook Configuration

#### Midtrans Webhook

1. Log in to Midtrans Dashboard
2. Go to Settings > Configuration > Payment Notification URL
3. Set to: `https://yourdomain.com/api/webhooks/midtrans`

#### WhatsApp Webhook

1. Log in to Meta for Developers
2. Configure webhook URL: `https://yourdomain.com/api/webhooks/whatsapp`
3. Verify token: Use value from `WHATSAPP_WEBHOOK_VERIFY_TOKEN` in `.env`

## Default Demo Accounts

After seeding, you can log in with:

**Admin Account:**
- Email: `admin@demo.test`
- Password: `admin123`

**Donor Account:**
- Email: `donor@demo.test`
- Password: `donor123`

## Directory Structure

```
├── app/
│   ├── Http/Controllers/       # Web & API controllers
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic services
│   │   ├── Payment/           # Payment gateway integrations
│   │   └── WhatsAppService.php
│   └── Traits/                # Reusable traits
├── config/
│   ├── services.php           # Payment & API configurations
│   └── sobrizis.php           # Application-specific settings
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Data seeders
├── resources/
│   ├── js/                    # Vue.js components
│   ├── views/                 # Blade templates (Inertia shell)
│   └── lang/                  # Translations (id, en)
├── routes/
│   ├── web.php                # Web routes
│   └── api.php                # API routes
├── public/                    # Public assets
├── storage/                   # File uploads, logs
└── tests/                     # Unit & Feature tests
```

## Configuration

### Amil Percentage

Default: 12.5% (compliant with Indonesian regulations). Adjust in `.env`:

```
ORG_AMIL_PERCENTAGE=12.5
```

### Approval Threshold

Payouts above this amount require multi-level approval:

```
ORG_APPROVAL_THRESHOLD=10000000
```

### Multi-Tenant Mode

Enable for SaaS deployment:

```
MULTI_TENANT_ENABLED=true
TENANT_CENTRAL_DOMAIN=sobrizis.com
```

## Payment Gateway Setup

### Midtrans

1. Register at [https://dashboard.midtrans.com](https://dashboard.midtrans.com)
2. Get Server Key and Client Key from Settings
3. Add to `.env`:

```
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

### Xendit (Alternative)

```
XENDIT_SECRET_KEY=your_secret_key
XENDIT_IS_PRODUCTION=false
```

## WhatsApp Cloud API Setup

1. Register at [Meta for Developers](https://developers.facebook.com/)
2. Create WhatsApp Business App
3. Get Phone Number ID and Access Token
4. Add to `.env`:

```
WHATSAPP_PHONE_NUMBER_ID=your_phone_id
WHATSAPP_ACCESS_TOKEN=your_token
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_verify_token
```

## API Documentation

### Public API Endpoints

#### Get Programs

```
GET /api/v1/programs?status=active&category=zakat_maal
```

#### Get Program Progress

```
GET /api/v1/programs/{slug}/progress
```

#### Create Donation

```
POST /api/v1/donations
{
  "program_id": 1,
  "amount": 100000,
  "donor_name": "John Doe",
  "donor_email": "john@example.com",
  "donor_phone": "081234567890",
  "payment_method": "qris"
}
```

#### Zakat Calculator

```
POST /api/v1/zakat-calculator/calculate
{
  "type": "maal",
  "assets": {
    "cash": 50000000,
    "gold": 100,
    "investment": 20000000
  }
}
```

### Widget Embed

#### Donation Counter

```html
<iframe src="https://yourdomain.com/api/v1/widget/counter" width="300" height="150"></iframe>
```

#### Program Widget

```html
<script src="https://yourdomain.com/widget.js"></script>
<div data-sobrizis-program="program-slug"></div>
```

## Security Features

- **CSRF Protection**: All forms protected
- **SQL Injection**: Eloquent ORM prevents injection
- **XSS Protection**: Output escaping enabled
- **File Upload**: MIME type validation, size limits
- **Password**: Argon2id hashing
- **2FA**: TOTP support
- **Rate Limiting**: Login, OTP, broadcast limits
- **Webhook Verification**: Signature validation
- **Audit Trail**: Complete activity logging

## Privacy & Data Protection (PDP)

- **Data Minimization**: Only necessary data collected
- **Consent Logging**: Checkbox + timestamp
- **Export Data**: Donors can export their data
- **Delete Data**: Right to be forgotten
- **Anonymization**: Anonymous donation option

## Performance Optimization

- **Config Caching**: `php artisan config:cache`
- **Route Caching**: `php artisan route:cache`
- **View Caching**: `php artisan view:cache`
- **Query Optimization**: Eager loading, indexes
- **Asset Optimization**: Minified CSS/JS
- **Image Optimization**: WebP support, lazy loading
- **CDN Ready**: S3/CloudFront compatible

## Testing

Run PHPUnit tests:

```bash
php artisan test
```

Run specific test:

```bash
php artisan test --filter DonationTest
```

## Troubleshooting

### Payment Gateway Errors

**Problem**: "Invalid signature" on webhook

**Solution**: 
- Verify `MIDTRANS_SERVER_KEY` is correct
- Check webhook URL is publicly accessible
- Review logs: `storage/logs/laravel.log`

### WhatsApp Not Sending

**Problem**: Messages not delivered

**Solution**:
- Verify `WHATSAPP_ACCESS_TOKEN` is valid
- Check phone number format (62xxx)
- Ensure business account is approved
- Review rate limits (80 messages/second)

### Queue Not Processing

**Problem**: Jobs stuck in queue

**Solution**:
```bash
php artisan queue:restart
php artisan queue:work --tries=3 --timeout=90
```

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Use strong `APP_KEY` (auto-generated)
- [ ] Configure production database
- [ ] Set up Redis for cache/queue
- [ ] Enable HTTPS/SSL
- [ ] Configure S3 for file storage
- [ ] Set up supervisor for queue worker
- [ ] Configure cron for scheduler
- [ ] Set up backups (database + files)
- [ ] Configure monitoring (Sentry, etc.)
- [ ] Review security headers
- [ ] Test payment webhooks

### Supervisor Configuration

Create `/etc/supervisor/conf.d/sobrizis-worker.conf`:

```ini
[program:sobrizis-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-project/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasflags=TERM
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-project/storage/logs/worker.log
stopwaitsecs=3600
```

## Support

### Included Support
- Bug fixes
- Minor compatibility updates
- One-time installation assistance

### Not Included
- Custom feature development
- Multiple server installations
- New payment gateway integrations

For paid custom services, contact: support@sobrizis.com

## Changelog

### Version 1.0.0 (Initial Release)
- Complete donation management (QRIS, VA, E-Wallet)
- Program & mustahik management
- Distribution with multi-approval
- Accounting & journal entries
- WhatsApp/Email notifications
- Audit trail system
- Transparency widgets
- Zakat calculator
- Multi-tenant support (optional)
- Admin dashboard with KPI
- PDF receipt generation
- Comprehensive reports

## License

This is a proprietary commercial software. Purchase includes:

- **Regular License**: Single organization deployment, all core features
- **Extended License**: Multi-tenant/agency use, white-label option

Redistribution, resale, or unauthorized use is prohibited.

## Credits

- **Payment**: Midtrans, Xendit
- **Icons**: Heroicons
- **UI**: Tailwind CSS
- **Charts**: Chart.js, ApexCharts

---

**Website**: [https://sobrizis.com](https://sobrizis.com)  
**Documentation**: [https://docs.sobrizis.com](https://docs.sobrizis.com)  
**Support**: support@sobrizis.com

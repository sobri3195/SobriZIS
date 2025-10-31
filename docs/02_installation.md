# Installation Guide

## System Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18 LTS
- **NPM**: 9.x
- **MySQL**: 8.0+ or PostgreSQL: 14+
- **Web Server**: Nginx or Apache
- **RAM**: 2GB minimum (4GB recommended)
- **Storage**: 10GB minimum

### Recommended for Production
- **Redis**: For cache and queue
- **Supervisor**: For queue workers
- **SSL Certificate**: For HTTPS
- **CDN**: For static assets
- **S3 or equivalent**: For file storage

### PHP Extensions Required
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD or Imagick

## Installation Steps

### 1. Download & Extract

If you purchased from CodeCanyon:
```bash
unzip sobrizis-laravel-vue.zip
cd sobrizis-laravel-vue
```

Or clone from repository:
```bash
git clone https://your-repo-url.git sobrizis
cd sobrizis
```

### 2. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

For development environment:
```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

Or with Yarn:
```bash
yarn install
```

### 4. Environment Configuration

Copy the example environment file:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 5. Configure Database

Edit `.env` and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sobrizis
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Create Database:**
```bash
mysql -u root -p
CREATE DATABASE sobrizis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

This will:
- Create all database tables
- Seed demo programs (5 programs)
- Create sample donations (50 transactions)
- Add mustahik records (10 beneficiaries)
- Create blog posts (6 articles)
- Add gallery images (12 photos)
- Create admin and donor test accounts

### 7. Configure Storage

Create symbolic link for file uploads:
```bash
php artisan storage:link
```

Set proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Build Frontend Assets

**For Production:**
```bash
npm run build
```

**For Development:**
```bash
npm run dev
```

### 9. Configure Web Server

#### Nginx Configuration

Create `/etc/nginx/sites-available/sobrizis.conf`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name sobrizis.local;
    root /var/www/sobrizis/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/sobrizis.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache Configuration

Create `/etc/apache2/sites-available/sobrizis.conf`:

```apache
<VirtualHost *:80>
    ServerName sobrizis.local
    DocumentRoot /var/www/sobrizis/public

    <Directory /var/www/sobrizis/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/sobrizis_error.log
    CustomLog ${APACHE_LOG_DIR}/sobrizis_access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite sobrizis.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### 10. Configure Queue Worker (Recommended)

Install Supervisor:
```bash
sudo apt-get install supervisor
```

Create `/etc/supervisor/conf.d/sobrizis-worker.conf`:

```ini
[program:sobrizis-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sobrizis/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasflags=TERM
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sobrizis/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sobrizis-worker:*
```

### 11. Configure Scheduler (Cron)

Add to crontab (`crontab -e`):

```bash
* * * * * cd /var/www/sobrizis && php artisan schedule:run >> /dev/null 2>&1
```

### 12. Configure Mail Settings

Edit `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sobrizis.com"
MAIL_FROM_NAME="SobriZIS"
```

For production, use services like:
- **Amazon SES**
- **SendGrid**
- **Mailgun**
- **Postmark**

### 13. Configure Payment Gateway

See [05_payment_integrations.md](05_payment_integrations.md) for detailed setup.

**Quick Setup (Midtrans Sandbox):**

1. Register at [https://dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com)
2. Get Server Key and Client Key from Settings
3. Add to `.env`:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

4. Configure webhook URL in Midtrans Dashboard:
   ```
   https://yourdomain.com/api/webhooks/midtrans
   ```

### 14. Configure WhatsApp (Optional)

See [06_notifications.md](06_notifications.md) for detailed setup.

```env
WHATSAPP_PHONE_NUMBER_ID=your_phone_id
WHATSAPP_ACCESS_TOKEN=your_token
WHATSAPP_WEBHOOK_VERIFY_TOKEN=random_string_123
```

### 15. Organization Settings

Configure your organization details in `.env`:

```env
ORG_NAME="Lembaga Zakat Indonesia"
ORG_NPWP=01.234.567.8-901.000
ORG_ADDRESS="Jl. Sudirman No. 123, Jakarta"
ORG_PHONE="+62 21 1234567"
ORG_EMAIL="info@lembagaku.org"
ORG_AMIL_PERCENTAGE=12.5
ORG_APPROVAL_THRESHOLD=10000000
```

### 16. Test Installation

**Check System:**
```bash
php artisan about
```

**Access Website:**
- Public: `http://sobrizis.local`
- Admin: `http://sobrizis.local/admin/dashboard`

**Test Accounts:**
- Admin: `admin@demo.test` / `admin123`
- Donor: `donor@demo.test` / `donor123`

### 17. Security Hardening (Production)

**1. Disable Debug Mode:**
```env
APP_ENV=production
APP_DEBUG=false
```

**2. Optimize Application:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader
```

**3. Set Proper Permissions:**
```bash
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

**4. Install SSL Certificate:**
```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d sobrizis.com -d www.sobrizis.com
```

**5. Configure Firewall:**
```bash
sudo ufw allow 'Nginx Full'
sudo ufw delete allow 'Nginx HTTP'
sudo ufw enable
```

## Troubleshooting

### Permission Errors

```bash
sudo chown -R www-data:www-data /var/www/sobrizis
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed

- Check MySQL service: `sudo systemctl status mysql`
- Verify credentials in `.env`
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

### Queue Not Working

- Check Redis: `redis-cli ping` (should return PONG)
- Restart queue: `php artisan queue:restart`
- Check supervisor: `sudo supervisorctl status sobrizis-worker:*`

### Assets Not Loading

- Check storage link: `php artisan storage:link`
- Verify permissions: `ls -la public/storage`
- Clear cache: `php artisan cache:clear`

### Payment Webhook Failed

- Verify webhook URL is publicly accessible
- Check SSL certificate (webhooks require HTTPS in production)
- Review logs: `tail -f storage/logs/laravel.log`

## Update Guide

To update to a new version:

1. **Backup database and files**
2. Download new version
3. Extract and replace files (keep `.env` and `storage/` folder)
4. Run migrations:
   ```bash
   php artisan migrate
   ```
5. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
6. Install dependencies:
   ```bash
   composer install --no-dev
   npm install && npm run build
   ```
7. Test functionality

## Next Steps

After installation:

1. Read [03_admin_guide.md](03_admin_guide.md) to learn admin features
2. Configure payment gateways: [05_payment_integrations.md](05_payment_integrations.md)
3. Set up notifications: [06_notifications.md](06_notifications.md)
4. Customize appearance: [07_customization.md](07_customization.md)

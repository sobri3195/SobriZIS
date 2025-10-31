# Quick Installation Guide - SobriZIS

## For CodeCanyon Buyers

This is a quick start guide. For detailed documentation, see `/docs/` folder.

## Prerequisites

Ensure your server meets these requirements:
- PHP 8.2 or higher
- MySQL 8.0+ or PostgreSQL 14+
- Composer 2.x
- Node.js 18 LTS
- Redis (optional but recommended)

## Installation Steps

### 1. Extract Files

Extract the downloaded zip file to your web server directory:
```bash
unzip sobrizis-laravel-vue.zip
cd sobrizis
```

### 2. Install Dependencies

**PHP Dependencies:**
```bash
composer install
```

**JavaScript Dependencies:**
```bash
npm install
```

### 3. Environment Configuration

Copy the example environment file:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Edit `.env` file with your settings:
```env
# Database
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Mail
MAIL_HOST=smtp.yourhost.com
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_password

# Midtrans (Payment Gateway)
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
```

### 4. Database Setup

Create your database, then run:
```bash
php artisan migrate --seed
```

This creates all tables and demo data including:
- Admin account: admin@demo.test / admin123
- Donor account: donor@demo.test / donor123
- 5 sample programs
- 50 demo donations
- 10 mustahik records

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Build Assets

**For Production:**
```bash
npm run build
```

**For Development:**
```bash
npm run dev
```

### 7. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

If on Linux/Mac with www-data:
```bash
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Configure Web Server

**Nginx:**
Point your site root to the `/public` directory and ensure `.htaccess` rules work.

**Apache:**
Enable `mod_rewrite` and ensure `AllowOverride All` is set.

### 9. Access Your Site

- Public Site: `http://yourdomain.com`
- Admin Panel: `http://yourdomain.com/admin/dashboard`

**Demo Accounts:**
- Admin: admin@demo.test / admin123
- Donor: donor@demo.test / donor123

### 10. Configure Payment Gateway

1. Register at [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Get your API keys
3. Set webhook URL to: `https://yourdomain.com/api/webhooks/midtrans`
4. Update `.env` with your keys

### 11. Configure WhatsApp (Optional)

1. Get WhatsApp Business API access
2. Add credentials to `.env`:
```env
WHATSAPP_PHONE_NUMBER_ID=your_id
WHATSAPP_ACCESS_TOKEN=your_token
```

### 12. Setup Queue Worker

For background processing (emails, WhatsApp):

**Using Supervisor (Recommended):**
Create `/etc/supervisor/conf.d/sobrizis.conf`:
```ini
[program:sobrizis-worker]
command=php /path/to/sobrizis/artisan queue:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/sobrizis/storage/logs/worker.log
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sobrizis-worker:*
```

### 13. Setup Cron

Add to crontab (`crontab -e`):
```bash
* * * * * cd /path/to/sobrizis && php artisan schedule:run >> /dev/null 2>&1
```

## Customization

### Organization Details

Edit `.env`:
```env
ORG_NAME="Your Organization Name"
ORG_EMAIL="info@yourorg.com"
ORG_PHONE="+62 21 1234567"
ORG_NPWP="Your Tax ID"
```

### Logo & Branding

Replace files in `/public/images/`:
- `logo.png` (for header)
- `logo-dark.png` (for footer)
- `favicon.ico`

### Colors

Edit `/tailwind.config.js` to change primary colors.

## Production Checklist

Before going live:

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Use real payment gateway keys (not sandbox)
- [ ] Configure proper mail settings
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set up automatic backups
- [ ] Configure firewall
- [ ] Set strong passwords for admin accounts
- [ ] Test payment flow end-to-end
- [ ] Verify webhook URLs are accessible
- [ ] Review privacy policy and terms
- [ ] Set up monitoring/error tracking

## Troubleshooting

### Issue: Blank page after installation

**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
chmod -R 775 storage bootstrap/cache
```

### Issue: CSS/JS not loading

**Solution:**
```bash
npm run build
php artisan storage:link
```

### Issue: Database connection error

**Solution:**
- Verify database credentials in `.env`
- Ensure database exists
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

### Issue: Payment webhook not working

**Solution:**
- Ensure URL is publicly accessible (not localhost)
- Verify HTTPS is enabled
- Check webhook URL in payment gateway dashboard
- Review logs: `tail -f storage/logs/laravel.log`

## Getting Help

- **Documentation**: See `/docs/` folder
- **Email Support**: support@sobrizis.com
- **FAQ**: See docs/09_faq_troubleshooting.md

## License

This software is licensed per your purchase:
- **Regular License**: Single organization use
- **Extended License**: Multi-tenant/agency use

See LICENSE.md for full terms.

## Next Steps

1. Review documentation in `/docs/` folder
2. Customize branding and colors
3. Configure payment gateways
4. Set up WhatsApp notifications
5. Create your first program
6. Test donation flow
7. Review transparency page
8. Invite your team

Welcome to SobriZIS! 🎉

# Changelog

All notable changes to SobriZIS will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-15

### Added

#### Donor Features
- User registration and authentication with email/WhatsApp OTP
- Two-factor authentication (2FA) support using TOTP
- Zakat calculator for Maal, Profession, Gold, and Agriculture
- Asset profile management for annual zakat reminders
- Multiple payment methods: QRIS, Virtual Accounts (BCA, BNI, BRI, Mandiri), E-Wallets (GoPay, OVO, DANA, ShopeePay)
- Unique code generation for easy payment confirmation
- Real-time donation status tracking
- Donation history with filtering and search
- PDF receipt generation and download
- Anonymous donation option
- Personal message support on donations
- Email and WhatsApp notifications for donation confirmations
- Recurring donation reminders (weekly/monthly/yearly)
- Profile management with privacy controls

#### Admin Features
- Comprehensive KPI dashboard with real-time statistics
- Date range filtering (today/this week/this month/this year)
- Cash inflow and outflow tracking
- Program balance monitoring
- Program management with categories (Zakat Maal, Zakat Profession, Infaq, Sedekah, Wakaf, etc.)
- Asnaf classification (8 categories according to Islamic law)
- Program progress tracking with visual indicators
- Mustahik (beneficiary) management with verification workflow
- NIK masking for privacy protection
- Distribution (payout) management with proof upload
- Multi-level approval workflow for large disbursements (configurable threshold)
- Automatic amil percentage calculation (max 12.5%)
- Simple accounting with journal entries
- Account chart management
- Budget allocation per program
- Comprehensive reporting (by period, program, asnaf)
- Export reports to CSV and PDF
- Complete audit trail with actor, action, timestamp, IP address, and user agent
- Broadcast messaging to donor segments (active, inactive, high-value)
- WhatsApp and email template management
- Blog/news management for updates
- Photo gallery management for activities
- Program update posting for transparency
- Campaign tracking with UTM parameters
- Multi-currency support (default IDR)
- Multi-language support (Indonesian, English)

#### Public Features
- Transparent homepage with real-time donation counter
- Program listing with filters (region, asnaf, category)
- Program detail pages with progress bars
- Distribution proof display (with privacy blur option)
- Photo galleries of activities
- Blog/news listing
- SEO-optimized pages
- Embeddable widgets (iframe/script) for external websites
- Widget types: counter, program progress, recent donors
- Mobile-responsive design
- Dark mode support

#### Multi-Tenant Features (Optional SaaS Mode)
- Subdomain-based tenant isolation
- Separate database per tenant with data isolation
- Tenant management dashboard
- Billing and subscription tracking
- Trial period management
- Custom branding per tenant
- Tenant-specific settings and configurations

#### Technical Features
- Laravel 11 with PHP 8.2+ support
- Vue 3 + Inertia.js for SPA experience
- Tailwind CSS for modern UI
- Redis for caching and queue management
- Midtrans payment gateway integration (default)
- Xendit and DOKU support (configurable)
- WhatsApp Cloud API integration for notifications
- Email notifications via SMTP
- AWS S3 compatible file storage
- Image optimization and lazy loading
- Queue system for background processing
- Rate limiting for API and broadcasts
- CSRF protection
- Input validation and sanitization
- XSS protection
- SQL injection prevention
- Webhook signature verification
- Password hashing with Argon2id
- Session security with HttpOnly cookies
- Database query optimization with eager loading
- Full-text search support
- Pagination for large datasets
- Export functionality (CSV, PDF, Excel)
- Automated testing with PHPUnit
- Code quality with PHPStan (level 5)
- PSR-12 coding standards
- API documentation
- Comprehensive documentation

#### Compliance & Security
- Indonesian Zakat Law compliance (UU No. 23/2011)
- OWASP security best practices
- Personal Data Protection (PDP) ready
- Consent logging
- Data export functionality for users
- Right to be forgotten (data deletion)
- Audit trail for all sensitive actions
- File upload security (MIME type validation, size limits)
- Rate limiting for login attempts
- Automatic session timeout
- Secure webhook handling
- Environment-based configuration
- Secret key management

### Security
- Implemented CSRF protection on all forms
- Added rate limiting for authentication endpoints
- Enabled XSS protection with output escaping
- Implemented webhook signature verification
- Added audit logging for sensitive actions
- Secure file upload with MIME validation
- Password hashing with Argon2id

### Performance
- Implemented config, route, and view caching
- Added database query optimization with indexes
- Enabled eager loading to prevent N+1 queries
- Implemented pagination for large datasets
- Added Redis caching for frequently accessed data
- Optimized assets with Vite bundling
- Implemented lazy loading for images

## [Unreleased]

### Planned Features
- Mobile app (Flutter) for donors
- Blockchain audit trail (optional add-on)
- Advanced analytics dashboard
- AI-powered fraud detection
- Recurring donation subscriptions
- Peer-to-peer zakat distribution
- Integration with more payment gateways
- SMS notifications
- Push notifications
- Multi-factor authentication with biometrics
- Advanced reporting with charts
- Data visualization dashboard
- Integration with accounting software
- API for third-party integrations
- Webhook for external systems
- Advanced donor segmentation
- Marketing automation
- Loyalty program for donors
- Gamification features
- Social media integration
- Video content support
- Live streaming for events
- Crowdfunding campaigns
- Donation matching programs

---

For support or questions about updates, please contact support@sobrizis.com

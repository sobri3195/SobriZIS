# Introduction to SobriZIS

## What is SobriZIS?

SobriZIS is a comprehensive web-based management system designed specifically for Islamic charitable organizations managing Zakat, Infaq, and Sedekah (ZIS) in Indonesia. Built with modern technologies (Laravel 11 + Vue 3), it provides a complete solution for donation management, distribution tracking, and financial transparency.

## Why SobriZIS?

### Problems It Solves

1. **Manual Donation Processing**: Automates payment collection through QRIS, Virtual Accounts, and E-Wallets
2. **Lack of Transparency**: Provides real-time progress tracking and distribution proof
3. **Complex Accounting**: Automates journal entries and amil calculation (max 12.5%)
4. **Communication Gaps**: Integrated WhatsApp and email notifications
5. **Audit Challenges**: Complete audit trail with actor, action, timestamp, and IP logging
6. **Multiple Organizations**: Optional multi-tenant mode for agencies managing multiple institutions

## Key Architecture Components

### Backend (Laravel 11)
- **MVC Pattern**: Controllers, Models, Services separation
- **Queue System**: Background job processing for emails, WhatsApp, and webhooks
- **Event-Driven**: Donation success triggers receipt generation, accounting entries
- **Repository Pattern**: Payment gateway abstraction for easy switching

### Frontend (Vue 3 + Inertia)
- **SPA Experience**: Fast navigation without page reloads
- **Component-Based**: Reusable UI components
- **Real-Time Updates**: WebSocket ready for live counters
- **Responsive Design**: Mobile-first with Tailwind CSS

### Database Schema
- **Users & Donors**: Authentication and donor profiles
- **Programs**: Donation campaigns with categories and asnaf
- **Donations**: Transaction records with payment gateway integration
- **Mustahik**: Verified beneficiaries
- **Payouts**: Distribution records with multi-approval workflow
- **Journals**: Double-entry accounting
- **Audit Logs**: Complete activity tracking

## User Roles

1. **Donor**: Register, donate, view history, download receipts
2. **Amil** (Staff): Verify mustahik, record distributions
3. **Admin**: Full access to dashboard, reports, settings
4. **Super Admin**: Multi-tenant management (optional)

## Compliance & Standards

- **Indonesian Zakat Law**: Complies with UU No. 23/2011
- **Amil Percentage**: Maximum 12.5% as per regulation
- **Asnaf Categories**: 8 categories as per Islamic jurisprudence
- **PDP Ready**: Privacy controls, consent logging, data export/delete
- **Security**: OWASP best practices, CSRF protection, input validation

## Technology Stack Summary

| Component | Technology |
|-----------|-----------|
| Backend Framework | Laravel 11 |
| PHP Version | 8.2+ |
| Frontend Framework | Vue 3 + Inertia.js |
| CSS Framework | Tailwind CSS |
| Database | MySQL 8+ / PostgreSQL 14+ |
| Cache/Queue | Redis |
| Payment Gateway | Midtrans (Xendit, DOKU supported) |
| Notification | WhatsApp Cloud API, Email |
| File Storage | Local / AWS S3 |
| PDF Generation | DomPDF |
| Export | Maatwebsite Excel |

## Use Cases

### For Mosques
- Accept online donations via QRIS
- Manage construction projects
- Distribute zakat fitrah
- Send Ramadan reminders

### For Foundations
- Scholarship programs with progress tracking
- Orphan sponsorship
- Medical assistance distribution
- Transparency reporting for donors

### For National Organizations
- Multi-branch operations (multi-tenant)
- Centralized reporting
- Campaign management
- Large-scale distributions

## Getting Started

1. **Installation**: Follow [02_installation.md](02_installation.md)
2. **Admin Guide**: Read [03_admin_guide.md](03_admin_guide.md)
3. **Donor Guide**: See [04_donor_guide.md](04_donor_guide.md)
4. **Payment Setup**: Configure gateways in [05_payment_integrations.md](05_payment_integrations.md)

## Support & Community

- **Documentation**: This docs folder
- **Email Support**: support@sobrizis.com
- **Updates**: Check CHANGELOG.md for version history
- **License**: See LICENSE.md for terms

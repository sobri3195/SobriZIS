<?php

return [
    'organization' => [
        'name' => env('ORG_NAME', 'Lembaga Zakat Indonesia'),
        'npwp' => env('ORG_NPWP'),
        'address' => env('ORG_ADDRESS'),
        'phone' => env('ORG_PHONE'),
        'email' => env('ORG_EMAIL', 'info@sobrizis.com'),
    ],

    'amil_percentage' => (float) env('ORG_AMIL_PERCENTAGE', 12.5),
    
    'approval_threshold' => (float) env('ORG_APPROVAL_THRESHOLD', 10000000),

    'multi_tenant' => [
        'enabled' => env('MULTI_TENANT_ENABLED', false),
        'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'sobrizis.com'),
    ],

    'features' => [
        '2fa_enabled' => env('FEATURE_2FA_ENABLED', true),
        'otp_login' => env('FEATURE_OTP_LOGIN', true),
        'blockchain_audit' => env('FEATURE_BLOCKCHAIN_AUDIT', false),
        'pwa' => env('FEATURE_PWA', true),
        'widget_embed' => env('FEATURE_WIDGET_EMBED', true),
    ],

    'zakat' => [
        'nisab' => [
            'gold' => 85,
            'silver' => 595,
        ],
        'rate' => [
            'maal' => 2.5,
            'profesi' => 2.5,
            'pertanian' => [
                'irigasi' => 5,
                'tadah_hujan' => 10,
            ],
        ],
    ],

    'donation' => [
        'min_amount' => 10000,
        'unique_code' => [
            'enabled' => true,
            'min' => 1,
            'max' => 999,
        ],
        'payment_expiry_hours' => 24,
    ],

    'receipt' => [
        'prefix' => 'RCP',
        'logo' => public_path('images/logo.png'),
    ],

    'notification' => [
        'channels' => ['email', 'whatsapp'],
        'rate_limit' => [
            'whatsapp' => 80,
            'email' => 1000,
        ],
    ],
];

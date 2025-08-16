<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
    'csrf'          => \CodeIgniter\Filters\CSRF::class,
    'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
    'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
    'invalidchars'  => \CodeIgniter\Filters\InvalidChars::class,
    'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,
    
    // Custom filters - TAMBAHKAN INI
    'auth'          => \App\Filters\AuthFilter::class,
    'apiauth'       => \App\Filters\ApiAuthFilter::class,  // ⭐ INI YANG PENTING
];

    

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            'honeypot',
            'csrf' => ['except' => [
                'api/*',  // Disable CSRF for API routes
                'admin/api/*',  // Disable CSRF for admin API routes
                'auth/check-email',  // AJAX route
                'register/check-email',  // AJAX route
                'auth/verify-code',  // Temporary disable for testing
                'auth/resend-code'  // Temporary disable for testing
            ]],
            'invalidchars',
        ],
        'after' => [
            'toolbar',
            'honeypot',
            'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that work on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
    'auth' => [
        'before' => [
            'admin/*',
            'user/*', 
            'presenter/*',
            'reviewer/*',
            'dashboard',
        ]
    ],
        
        'apiauth' => [
        'before' => [
            'api/v1/registrations',
            'api/v1/registrations/*',
            'api/v1/payments',
            'api/v1/payments/*',
            'api/v1/auth/profile',
        ]
        ]
        ];
}
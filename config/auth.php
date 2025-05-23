<?php
// filepath: /Users/tylormayfield/Documents/GitHub/lorphp/config/auth.php
return [
    // General settings
    'app_name' => 'LorPHP',
    // Default role for new users (by name)
    'default_role' => 'user',
    
    // Routes
    'routes' => [
        'login_redirect' => '/dashboard',
        'logout_redirect' => '/',
        'register_redirect' => '/dashboard',
    ],
    
    // View templates
    'views' => [
        'login' => 'login',
        'register' => 'register',
    ],
    
    // Validation
    'validation' => [
        'registration' => [
            'name' => [
                'required' => true,
                'message' => 'Name is required'
            ],
            'email' => [
                'required' => true,
                'validate_email' => true,
                'message' => 'Invalid email format'
            ],
            'password' => [
                'required' => true,
                'min_length' => 6,
                'message' => 'Password is required and must be at least 6 characters'
            ],
            'password_confirm' => [
                'required' => true,
                'match' => 'password',
                'message' => 'Passwords do not match'
            ]
        ],
        'login' => [
            'email' => [
                'required' => true,
                'message' => 'Email is required'
            ],
            'password' => [
                'required' => true,
                'message' => 'Password is required'
            ]
        ]
    ],

    // JWT Configuration
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'your-256-bit-secret',
        'expiration' => 72, // hours
        'algorithm' => 'HS256'
    ],
    
    // Messages
    'messages' => [
        'login' => [
            'invalid_credentials' => 'Invalid email or password'
        ],
        'register' => [
            'registration_failed' => 'Registration failed. Please try again.',
        ]
    ]
];

/**
 * To add more roles:
 * 1. Add a new entry to the RoleSeeder in database/seeders/RoleSeeder.php.
 * 2. Optionally, set 'default_role' above to the new role name for new users.
 */

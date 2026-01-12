<?php
/**
 * Chronos V3 - Sample Configuration File
 * Copy this to config.php and adjust settings for your environment
 */

return [
    // Database Configuration (MariaDB 11 compatible)
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'chronos_stats',
        'user' => 'chronos_user',
        'pass' => 'your_password_here',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    
    // Site Configuration
    'site' => [
        'title' => 'BAB.Stats Neos Chronos V3',
        'url' => 'http://localhost',
        'timezone' => 'UTC',
        'items_per_page' => 50,
    ],
    
    // Statistics Configuration
    'stats' => [
        'min_playtime' => 60, // Minimum seconds to be included in stats
        'round_decimals' => 2,
    ],
    
    // Security Settings
    'security' => [
        'enable_csrf' => true,
        'session_lifetime' => 3600,
    ],
];

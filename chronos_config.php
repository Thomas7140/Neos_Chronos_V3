<?php
/**
 * chronos Secure configuration
 * This file loads configuration from .env file
 * DO NOT commit .env to version control
 * 
 * SECURITY WARNING: This file should be moved outside the web root
 * in production environments.
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }
            
            // Set environment variable and make available via getenv()
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    return true;
}

// Try to load .env file from multiple locations
$envPaths = [
    '/home/devilishservices/connections/.env',  // External .env location
    __DIR__ . '/.env'                            // Local .env location
];

$envLoaded = false;
foreach ($envPaths as $envPath) {
    if (file_exists($envPath)) {
        loadEnv($envPath);
        $envLoaded = true;
        break;
    }
}

// Helper function to get environment variable with fallback
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

// Only use .env values if they're not placeholders
$dbhost     = env('DB_HOST', 'localhost');
$dbname     = env('DB_NAME', 'devilishservices_stats');
$dbuname    = env('DB_USERNAME', '');
$dbpass     = env('DB_PASSWORD', '');

$tablepre   = env('DB_TABLE_PREFIX', 'chronos');
$tablepre2  = env('DB_TABLE_PREFIX_MONTHLY', 'chronos_m');

// Check if we have placeholder values - if so, return without setting variables
// This allows config.php to fallback to external config
$hasPlaceholders = (
    $dbuname === 'your_database_user' || 
    $dbuname === '' || 
    $dbpass === 'your_database_password' ||
    $dbpass === ''
);

if ($hasPlaceholders && !$envLoaded) {
    // No valid .env file - don't set any variables, let config.php use external config
    return;
}

// ----------------- Admin login -----------------
$admin_name = env('ADMIN_USERNAME', 'admin');
$admin_pass = env('ADMIN_PASSWORD', 'changeme');

// Validate admin password is not default
if ($admin_pass === 'changeme' || $admin_pass === 'change_this_password') {
    error_log("WARNING: Using default admin password. Please change it in .env file!");
}

// ----------------- FTP details -----------------
$ftpInfoArr = [
    "fUser"   => env('FTP_USERNAME', ''),
    "fPass"   => env('FTP_PASSWORD', ''),
    "fRoot"   => env('FTP_ROOT', 'upload'),
    "fServer" => env('FTP_SERVER', 'localhost'),
];
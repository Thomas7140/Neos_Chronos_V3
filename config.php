<?php
/**
 * Chronos V3 - Configuration File
 * 
 * Main configuration for the Chronos statistics system.
 * This file loads environment variables and sets up core constants.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

// Prevent direct access
defined('CHRONOS_INIT') or define('CHRONOS_INIT', true);

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv(sprintf('%s=%s', $name, $value));
            }
        }
    }
    return true;
}

// Load .env file
$envPath = __DIR__ . '/.env';
if (!loadEnv($envPath)) {
    // Fall back to .env.example for initial setup
    loadEnv(__DIR__ . '/.env.example');
}

// Helper function to get environment variables with default values
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    
    // Convert string representations of boolean values
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
    }
    
    return $value;
}

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'chronos_stats'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_PREFIX', env('DB_PREFIX', 'chronos_'));

// Application Settings
define('APP_NAME', env('APP_NAME', 'Chronos V3'));
define('APP_URL', env('APP_URL', 'http://localhost'));
define('APP_DEBUG', env('APP_DEBUG', false));
define('APP_TIMEZONE', env('APP_TIMEZONE', 'UTC'));
define('APP_VERSION', '3.0.0');

// Admin Configuration
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'admin@example.com'));
define('ADMIN_DEFAULT_PASSWORD', env('ADMIN_DEFAULT_PASSWORD', 'change_this_password'));

// Security Settings
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 7200));
define('HASH_ALGORITHM', env('HASH_ALGORITHM', 'sha256'));
define('CSRF_TOKEN_EXPIRY', env('CSRF_TOKEN_EXPIRY', 3600));

// Stats Configuration
define('STATS_PER_PAGE', env('STATS_PER_PAGE', 30));
define('RECORDS_PER_PAGE', env('RECORDS_PER_PAGE', 50));
define('ENABLE_MONTHLY_STATS', env('ENABLE_MONTHLY_STATS', true));
define('ENABLE_HALL_OF_FAME', env('ENABLE_HALL_OF_FAME', true));

// Server Configuration
define('SERVER_NAME', env('SERVER_NAME', 'My BHD Server'));
define('SERVER_IP', env('SERVER_IP', '127.0.0.1'));
define('SERVER_PORT', env('SERVER_PORT', '3000'));

// File Upload Settings
define('MAX_UPLOAD_SIZE', env('MAX_UPLOAD_SIZE', 10485760)); // 10MB
define('ALLOWED_EXTENSIONS', env('ALLOWED_EXTENSIONS', 'log,txt'));

// Cache Settings
define('ENABLE_CACHE', env('ENABLE_CACHE', true));
define('CACHE_LIFETIME', env('CACHE_LIFETIME', 3600));

// Paths
define('BASE_PATH', __DIR__);
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('MODULES_PATH', BASE_PATH . '/modules');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('CACHE_PATH', BASE_PATH . '/cache');
define('LOGS_PATH', BASE_PATH . '/logs');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting based on debug mode
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Database table names
define('TABLE_PLAYERS', DB_PREFIX . 'players');
define('TABLE_WEAPONS', DB_PREFIX . 'weapons');
define('TABLE_MAPS', DB_PREFIX . 'maps');
define('TABLE_AWARDS', DB_PREFIX . 'awards');
define('TABLE_RANKS', DB_PREFIX . 'ranks');
define('TABLE_SERVERS', DB_PREFIX . 'servers');
define('TABLE_STATS', DB_PREFIX . 'stats');
define('TABLE_MONTHLY', DB_PREFIX . 'monthly_stats');
define('TABLE_SESSIONS', DB_PREFIX . 'sessions');
define('TABLE_ADMIN', DB_PREFIX . 'admin');

// Rating calculation constants
define('RATING_KILL_POINTS', 1);
define('RATING_DEATH_POINTS', -1);
define('RATING_HEADSHOT_BONUS', 2);
define('RATING_TEAMKILL_PENALTY', -5);

// Game types
define('GAME_TYPE_DM', 'Deathmatch');
define('GAME_TYPE_TDM', 'Team Deathmatch');
define('GAME_TYPE_CTF', 'Capture the Flag');
define('GAME_TYPE_KOTH', 'King of the Hill');
define('GAME_TYPE_SC', 'Search & Destroy');

return true;

<?php
/**
 * Chronos V3 - Bootstrap/Initialization
 * PHP 8+ compatible
 */

// Error reporting for development (adjust for production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
$configFile = __DIR__ . '/../config/config.php';
if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/../config/config.sample.php';
}

if (!file_exists($configFile)) {
    die('Configuration file not found. Please create config/config.php from config.sample.php');
}

$config = require $configFile;

// Set timezone
date_default_timezone_set($config['site']['timezone'] ?? 'UTC');

// Autoload Chronos classes
spl_autoload_register(function ($class) {
    $prefix = 'Chronos\\';
    $baseDir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize database connection
try {
    $db = \Chronos\Database::getInstance($config['database']);
} catch (Exception $e) {
    die('Database connection error. Please check your configuration.');
}

/**
 * Helper function to escape HTML output
 */
function html(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function to format time duration
 */
function formatTime(int $seconds): string
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    if ($hours > 0) {
        return sprintf('%dh %dm', $hours, $minutes);
    }
    return sprintf('%dm', $minutes);
}

/**
 * Helper function to format large numbers
 */
function formatNumber(int|float $number): string
{
    return number_format($number, 0, '.', ',');
}

return $config;

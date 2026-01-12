<?php
/**
 * Chronos V3 - Admin Logout
 * 
 * Logs out the admin user.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

// Initialize application
define('CHRONOS_INIT', true);
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/functions.php';

// Load functions
require_once INCLUDES_PATH . '/functions.php';

// Start session
session_start();

// Log the logout
if (isset($_SESSION['admin_username'])) {
    logMessage("Admin logout: {$_SESSION['admin_username']} from " . getClientIp(), 'INFO');
}

// Destroy session
$_SESSION = [];
session_destroy();

// Redirect to login
redirect('login.php');

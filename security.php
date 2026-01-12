<?php
/**
 * Security Helper Functions
 * Provides input validation, sanitization, and secure database operations
 */

// Global database connection
$GLOBALS['db_connection'] = null;

/**
 * Get or create database connection
 */
function getDBConnection() {
    global $dbhost, $dbuname, $dbpass, $dbname, $dbusername, $dbuserpw;
    
    // Support legacy variable names
    if (!isset($dbuname) && isset($dbusername)) {
        $dbuname = $dbusername;
    }
    if (!isset($dbpass) && isset($dbuserpw)) {
        $dbpass = $dbuserpw;
    }
    
    if ($GLOBALS['db_connection'] === null || !($GLOBALS['db_connection'] instanceof mysqli)) {
        $GLOBALS['db_connection'] = new mysqli($dbhost, $dbuname, $dbpass, $dbname);
        
        if ($GLOBALS['db_connection']->connect_error) {
            error_log("DB connection failed: " . $GLOBALS['db_connection']->connect_error . " (Host=$dbhost, DB=$dbname, User=$dbuname)");
            die("Database connection failed. Please contact administrator.");
        }
        
        $GLOBALS['db_connection']->set_charset("utf8mb4");
        $GLOBALS['db_connection']->query("SET SQL_BIG_SELECTS=1");
    }
    
    return $GLOBALS['db_connection'];
}

/**
 * Escape string for SQL (legacy support)
 */
function escapeSQL($value) {
    $conn = getDBConnection();
    if (is_array($value)) {
        return array_map('escapeSQL', $value);
    }
    return $conn->real_escape_string($value);
}

/**
 * Sanitize input - remove dangerous characters
 */
function sanitizeInput($data, $type = 'string') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $data);
    }
    
    // Remove null bytes
    $data = str_replace(chr(0), '', $data);
    
    switch ($type) {
        case 'int':
            return (int) $data;
        case 'float':
            return (float) $data;
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        case 'string':
        default:
            return trim($data);
    }
}

/**
 * Validate input
 */
function validateInput($data, $type = 'string', $options = []) {
    switch ($type) {
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT) !== false;
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL) !== false;
        case 'ip':
            return filter_var($data, FILTER_VALIDATE_IP) !== false;
        case 'alpha':
            return preg_match('/^[a-zA-Z]+$/', $data);
        case 'alphanumeric':
            return preg_match('/^[a-zA-Z0-9]+$/', $data);
        case 'username':
            return preg_match('/^[a-zA-Z0-9_-]{3,32}$/', $data);
        default:
            return is_string($data);
    }
}

/**
 * Safe GET parameter retrieval
 */
function getParam($key, $default = null, $type = 'string') {
    if (!isset($_GET[$key])) {
        return $default;
    }
    return sanitizeInput($_GET[$key], $type);
}

/**
 * Safe POST parameter retrieval
 */
function postParam($key, $default = null, $type = 'string') {
    if (!isset($_POST[$key])) {
        return $default;
    }
    return sanitizeInput($_POST[$key], $type);
}

/**
 * Safe REQUEST parameter retrieval
 */
function requestParam($key, $default = null, $type = 'string') {
    if (!isset($_REQUEST[$key])) {
        return $default;
    }
    return sanitizeInput($_REQUEST[$key], $type);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    // Support legacy plain text comparison during migration
    if ($password === $hash) {
        error_log("WARNING: Plain text password comparison detected. Please update password hashing.");
        return true;
    }
    return password_verify($password, $hash);
}

/**
 * Execute prepared statement safely
 */
function executePrepared($query, $params = [], $types = '') {
    $conn = getDBConnection();
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-detect types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    return $stmt;
}

/**
 * Sanitize array of values for IN clause
 */
function sanitizeArrayForIN($array, $type = 'int') {
    return array_map(function($item) use ($type) {
        return sanitizeInput($item, $type);
    }, $array);
}

/**
 * Log security event
 */
function logSecurityEvent($event, $details = []) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = $_SESSION['admin_username'] ?? 'anonymous';
    $timestamp = date('Y-m-d H:i:s');
    
    $message = sprintf(
        "[%s] Security Event: %s | User: %s | IP: %s | Details: %s",
        $timestamp,
        $event,
        $user,
        $ip,
        json_encode($details)
    );
    
    error_log($message);
}

<?php
/**
 * Chronos V3 - Common Functions
 * 
 * Shared utility functions used throughout the application.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

/**
 * Sanitize input to prevent XSS attacks
 */
function sanitize($input, $type = 'string') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return sanitize($item, $type);
        }, $input);
    }
    
    switch ($type) {
        case 'int':
            return (int) $input;
        case 'float':
            return (float) $input;
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        case 'string':
        default:
            return htmlspecialchars(strip_tags($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

/**
 * Validate input
 */
function validate($input, $type = 'string', $options = []) {
    switch ($type) {
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return filter_var($input, FILTER_VALIDATE_URL) !== false;
        case 'ip':
            return filter_var($input, FILTER_VALIDATE_IP) !== false;
        case 'string':
            $min = $options['min'] ?? 0;
            $max = $options['max'] ?? PHP_INT_MAX;
            $len = strlen($input);
            return $len >= $min && $len <= $max;
        default:
            return !empty($input);
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRY) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Format number with thousands separator
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, '.', ',');
}

/**
 * Format K/D ratio
 */
function formatKD($kills, $deaths) {
    if ($deaths == 0) {
        return $kills > 0 ? formatNumber($kills, 2) : '0.00';
    }
    return formatNumber($kills / $deaths, 2);
}

/**
 * Format date/time
 */
function formatDateTime($timestamp, $format = 'Y-m-d H:i:s') {
    if (is_numeric($timestamp)) {
        return date($format, $timestamp);
    }
    return date($format, strtotime($timestamp));
}

/**
 * Calculate player rating
 */
function calculateRating($stats) {
    $kills = $stats['kills'] ?? 0;
    $deaths = $stats['deaths'] ?? 0;
    $headshots = $stats['headshots'] ?? 0;
    $teamKills = $stats['team_kills'] ?? 0;
    
    $rating = ($kills * RATING_KILL_POINTS) + 
              ($deaths * RATING_DEATH_POINTS) + 
              ($headshots * RATING_HEADSHOT_BONUS) + 
              ($teamKills * RATING_TEAMKILL_PENALTY);
    
    return max(0, $rating);
}

/**
 * Get rank by rating
 */
function getRankByRating($rating) {
    $db = Database::getInstance();
    $rank = $db->fetchOne(
        "SELECT * FROM " . TABLE_RANKS . " 
         WHERE min_rating <= ? 
         ORDER BY min_rating DESC 
         LIMIT 1",
        [$rating]
    );
    
    return $rank ?: ['name' => 'Recruit', 'icon' => 'rank_0.png'];
}

/**
 * Redirect to URL
 */
function redirect($url, $statusCode = 302) {
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('admin/login.php');
    }
}

/**
 * Log message to file
 */
function logMessage($message, $level = 'INFO') {
    $logFile = LOGS_PATH . '/chronos_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Get client IP address
 */
function getClientIp() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
               'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
            return $_SERVER[$key];
        }
    }
    
    return '0.0.0.0';
}

/**
 * Pagination helper
 */
function paginate($total, $perPage, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Load template
 */
function loadTemplate($name, $data = []) {
    extract($data);
    $templatePath = TEMPLATES_PATH . '/' . $name . '.php';
    
    if (!file_exists($templatePath)) {
        throw new Exception("Template not found: {$name}");
    }
    
    ob_start();
    include $templatePath;
    return ob_get_clean();
}

/**
 * Get game type name
 */
function getGameTypeName($type) {
    $gameTypes = [
        'dm' => GAME_TYPE_DM,
        'tdm' => GAME_TYPE_TDM,
        'ctf' => GAME_TYPE_CTF,
        'koth' => GAME_TYPE_KOTH,
        'sc' => GAME_TYPE_SC
    ];
    
    return $gameTypes[strtolower($type)] ?? 'Unknown';
}

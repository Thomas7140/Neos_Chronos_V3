<?php
/**
 * Admin Authentication Functions
 * Database-backed admin user management with secure password hashing using Argon2id
 */

/**
 * Authenticate admin user against database
 * Falls back to config file if database authentication is not available
 * 
 * @param string $username Username to authenticate
 * @param string $password Plain text password
 * @return array|false User data array on success, false on failure
 */
function authenticateAdmin($username, $password) {
    global $tablepre, $admin_name, $admin_pass;
    
    // Sanitize username
    $username = DBEscape(trim($username));
    
    // Try database authentication first
    $sql = "SELECT * FROM {$tablepre}_admin_users 
            WHERE username = '$username' AND is_active = 1 LIMIT 1";
    
    try {
        $result = DBQuery($sql);
        
        if ($result && DBNumRows($result) > 0) {
            $user = DBFetchArray($result);
            
            // Check if account is locked
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                return false;
            }
            
            // Verify password using Argon2id
            if (password_verify($password, $user['password_hash'])) {
                // Check if password needs rehashing (algorithm updated)
                if (password_needs_rehash($user['password_hash'], PASSWORD_ARGON2ID)) {
                    $new_hash = password_hash($password, PASSWORD_ARGON2ID);
                    $new_hash = DBEscape($new_hash);
                    DBQuery("UPDATE {$tablepre}_admin_users 
                            SET password_hash = '$new_hash' 
                            WHERE id = {$user['id']}");
                }
                
                // Reset failed attempts on successful login
                DBQuery("UPDATE {$tablepre}_admin_users 
                        SET failed_login_attempts = 0, locked_until = NULL 
                        WHERE id = {$user['id']}");
                
                return $user;
            } else {
                // Record failed attempt
                $failed_attempts = intval($user['failed_login_attempts']) + 1;
                $locked_until = 'NULL';
                
                // Lock account after 5 failed attempts for 30 minutes
                if ($failed_attempts >= 5) {
                    $locked_until = "'" . date('Y-m-d H:i:s', time() + 1800) . "'";
                }
                
                DBQuery("UPDATE {$tablepre}_admin_users 
                        SET failed_login_attempts = $failed_attempts, 
                            locked_until = $locked_until 
                        WHERE id = {$user['id']}");
                
                return false;
            }
        }
    } catch (Exception $e) {
        // Database authentication failed, log error
        error_log("Database authentication error: " . $e->getMessage());
    }
    
    // Fallback to config file authentication (legacy)
    if (isset($admin_name) && isset($admin_pass) && 
        $username === $admin_name && $password === $admin_pass) {
        return [
            'id' => 0,
            'username' => $admin_name,
            'email' => '',
            'full_name' => 'Legacy Admin',
            'is_legacy' => true
        ];
    }
    
    return false;
}

/**
 * Update last login timestamp for admin user
 * 
 * @param int $admin_id Admin user ID
 */
function updateAdminLastLogin($admin_id) {
    global $tablepre;
    
    if ($admin_id > 0) {
        $admin_id = intval($admin_id);
        DBQuery("UPDATE {$tablepre}_admin_users 
                SET last_login = NOW() 
                WHERE id = $admin_id");
    }
}

/**
 * Record failed login attempt
 * 
 * @param string $username Username that failed
 */
function recordFailedLogin($username) {
    global $tablepre;
    
    $username = DBEscape(trim($username));
    $ip_address = DBEscape($_SERVER['REMOTE_ADDR'] ?? '');
    
    logAdminActivity(null, $username, 'login_failure', 'Failed login attempt from ' . $ip_address);
}

/**
 * Log admin activity to database
 * 
 * @param int|null $admin_id Admin user ID (null for failed logins)
 * @param string $username Username
 * @param string $action Action performed
 * @param string $details Additional details
 */
function logAdminActivity($admin_id, $username, $action, $details = '') {
    global $tablepre;
    
    $admin_id_sql = $admin_id ? intval($admin_id) : 'NULL';
    $username = DBEscape($username);
    $action = DBEscape($action);
    $details = DBEscape($details);
    $ip_address = DBEscape($_SERVER['REMOTE_ADDR'] ?? '');
    $user_agent = DBEscape($_SERVER['HTTP_USER_AGENT'] ?? '');
    
    try {
        DBQuery("INSERT INTO {$tablepre}_admin_log 
                (admin_id, username, action, details, ip_address, user_agent, created_at) 
                VALUES 
                ($admin_id_sql, '$username', '$action', '$details', '$ip_address', '$user_agent', NOW())");
    } catch (Exception $e) {
        // Silently fail if logging table doesn't exist yet
        error_log("Admin activity logging error: " . $e->getMessage());
    }
}

/**
 * Create new admin user
 * 
 * @param string $username Username
 * @param string $password Plain text password (will be hashed)
 * @param string $email Email address
 * @param string $full_name Full name
 * @return int|false New user ID on success, false on failure
 */
function createAdminUser($username, $password, $email = '', $full_name = '') {
    global $tablepre;
    
    $username = trim($username);
    if (empty($username) || empty($password)) {
        return false;
    }
    
    // Check if username already exists
    $check_username = DBEscape($username);
    $result = DBQuery("SELECT id FROM {$tablepre}_admin_users WHERE username = '$check_username'");
    if (DBNumRows($result) > 0) {
        return false; // Username already exists
    }
    
    $password_hash = password_hash($password, PASSWORD_ARGON2ID);
    
    $username = DBEscape($username);
    $password_hash = DBEscape($password_hash);
    $email = DBEscape($email);
    $full_name = DBEscape($full_name);
    
    DBQuery("INSERT INTO {$tablepre}_admin_users 
            (username, password_hash, email, full_name, is_active, created_at) 
            VALUES 
            ('$username', '$password_hash', '$email', '$full_name', 1, NOW())");
    
    return DBInsertId();
}

/**
 * Update admin user password
 * 
 * @param int $admin_id Admin user ID
 * @param string $new_password New plain text password (will be hashed)
 * @return bool Success
 */
function updateAdminPassword($admin_id, $new_password) {
    global $tablepre;
    
    if (empty($new_password)) {
        return false;
    }
    
    $admin_id = intval($admin_id);
    $password_hash = password_hash($new_password, PASSWORD_ARGON2ID);
    $password_hash = DBEscape($password_hash);
    
    DBQuery("UPDATE {$tablepre}_admin_users 
            SET password_hash = '$password_hash', 
                updated_at = NOW() 
            WHERE id = $admin_id");
    
    return true;
}

/**
 * Get admin user by ID
 * 
 * @param int $admin_id Admin user ID
 * @return array|false User data or false
 */
function getAdminUser($admin_id) {
    global $tablepre;
    
    $admin_id = intval($admin_id);
    $result = DBQuery("SELECT * FROM {$tablepre}_admin_users WHERE id = $admin_id");
    
    if (DBNumRows($result) > 0) {
        return DBFetchArray($result);
    }
    
    return false;
}

/**
 * Get all admin users
 * 
 * @return array Array of admin users
 */
function getAllAdminUsers() {
    global $tablepre;
    
    $users = [];
    $result = DBQuery("SELECT * FROM {$tablepre}_admin_users ORDER BY username");
    
    while ($row = DBFetchArray($result)) {
        $users[] = $row;
    }
    
    return $users;
}

/**
 * Deactivate admin user
 * 
 * @param int $admin_id Admin user ID
 * @return bool Success
 */
function deactivateAdminUser($admin_id) {
    global $tablepre;
    
    $admin_id = intval($admin_id);
    DBQuery("UPDATE {$tablepre}_admin_users 
            SET is_active = 0, 
                updated_at = NOW() 
            WHERE id = $admin_id");
    
    return true;
}

/**
 * Activate admin user
 * 
 * @param int $admin_id Admin user ID
 * @return bool Success
 */
function activateAdminUser($admin_id) {
    global $tablepre;
    
    $admin_id = intval($admin_id);
    DBQuery("UPDATE {$tablepre}_admin_users 
            SET is_active = 1, 
                failed_login_attempts = 0,
                locked_until = NULL,
                updated_at = NOW() 
            WHERE id = $admin_id");
    
    return true;
}

/**
 * Check if admin users table exists
 * 
 * @return bool True if table exists
 */
function adminUsersTableExists() {
    global $tablepre, $dbname;
    
    // Check if database functions are available
    if (!function_exists('DBQuery') || !function_exists('DBFetchArray')) {
        return false;
    }
    
    try {
        $table_name = $tablepre . '_admin_users';
        $result = @DBQuery("SELECT COUNT(*) as count FROM information_schema.tables 
                          WHERE table_schema = '$dbname' 
                          AND table_name = '$table_name'");
        if (!$result) {
            return false;
        }
        $row = @DBFetchArray($result);
        return $row && isset($row['count']) && $row['count'] > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Create admin users tables automatically
 * 
 * @return bool True on success, false on failure
 */
function createAdminUsersTables() {
    global $tablepre, $base_folder;
    
    // Try multiple paths to find the migration file
    $possible_paths = [
        $base_folder . 'migrations/001_create_admin_users_table.sql',
        __DIR__ . '/migrations/001_create_admin_users_table.sql',
        dirname(__FILE__) . '/migrations/001_create_admin_users_table.sql',
        './migrations/001_create_admin_users_table.sql'
    ];
    
    $migration_file = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $migration_file = $path;
            break;
        }
    }
    
    if (!$migration_file) {
        error_log("Migration file not found. Tried: " . implode(', ', $possible_paths));
        return false;
    }
    
    $sql = file_get_contents($migration_file);
    if (!$sql) {
        error_log("Failed to read migration file: $migration_file");
        return false;
    }
    
    error_log("Migration file loaded from: $migration_file, size: " . strlen($sql) . " bytes");
    
    // Replace table prefix placeholders
    $sql = str_replace('chronos_admin_users', $tablepre . '_admin_users', $sql);
    $sql = str_replace('chronos_admin_log', $tablepre . '_admin_log', $sql);
    
    // Remove comments (lines starting with --)
    $lines = explode("\n", $sql);
    $cleaned_lines = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        // Skip empty lines and comment lines
        if (empty($trimmed) || strpos($trimmed, '--') === 0) {
            continue;
        }
        $cleaned_lines[] = $line;
    }
    $sql = implode("\n", $cleaned_lines);
    
    // Split by semicolon to get individual statements
    $statements = explode(';', $sql);
    
    error_log("Found " . count($statements) . " SQL statements after splitting");
    
    $success_count = 0;
    $statement_num = 0;
    foreach ($statements as $statement) {
        $statement_num++;
        $statement = trim($statement);
        
        if (empty($statement)) {
            error_log("Statement $statement_num: Empty, skipping");
            continue;
        }
        
        error_log("Statement $statement_num: Executing - " . substr($statement, 0, 100) . "...");
        
        try {
            $result = @DBQuery($statement);
            if ($result !== false) {
                $success_count++;
                error_log("Statement $statement_num: SUCCESS");
            } else {
                error_log("Statement $statement_num: FAILED (DBQuery returned false)");
            }
        } catch (Exception $e) {
            error_log("Statement $statement_num: ERROR - " . $e->getMessage());
            error_log("Failed SQL: " . substr($statement, 0, 200));
            return false;
        }
    }
    
    // Log success
    error_log("Successfully executed $success_count SQL statements for admin tables");
    
    return $success_count > 0;
}

?>

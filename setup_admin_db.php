<?php
/**
 * Admin Database Setup Script
 * This script creates the admin users table and migrates existing credentials
 * Run this once to migrate from file-based to database-based authentication
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_folder = "";
require $base_folder."config.php";
require $base_folder."common.php";

// Prevent running from web if already set up
$check_existing = "SELECT COUNT(*) as count FROM information_schema.tables 
                   WHERE table_schema = '$dbname' 
                   AND table_name = '{$tablepre}_admin_users'";

DBConnect();

echo "<html><head><title>Admin Database Setup</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
.success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; margin: 10px 0; }
.error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; margin: 10px 0; }
.warning { color: #856404; padding: 10px; background: #fff3cd; border: 1px solid #ffeeba; margin: 10px 0; }
.info { color: #004085; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; margin: 10px 0; }
code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style></head><body>";

echo "<h1>Admin Database Setup</h1>";

// Check if tables already exist
$result = DBQuery($check_existing);
$row = DBFetchArray($result);

if ($row['count'] > 0) {
    echo "<div class='warning'><strong>Warning:</strong> Admin users table already exists.</div>";
    echo "<p>If you want to re-run this setup, you must first drop the existing tables:</p>";
    echo "<pre>DROP TABLE IF EXISTS {$tablepre}_admin_log;\nDROP TABLE IF EXISTS {$tablepre}_admin_users;</pre>";
    echo "<p>Or use the admin management interface to manage users.</p>";
    echo "</body></html>";
    exit;
}

echo "<div class='info'>Creating admin authentication tables...</div>";

// Read and execute migration SQL
$migration_file = __DIR__ . '/migrations/001_create_admin_users_table.sql';
if (!file_exists($migration_file)) {
    echo "<div class='error'>Migration file not found: $migration_file</div>";
    exit;
}

$sql = file_get_contents($migration_file);

// Replace table prefix placeholders
$sql = str_replace('chronos_admin_users', $tablepre . '_admin_users', $sql);
$sql = str_replace('chronos_admin_log', $tablepre . '_admin_log', $sql);

// Split and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    try {
        DBQuery($statement);
        echo "<div class='success'>✓ Executed statement</div>";
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<pre>" . htmlspecialchars($statement) . "</pre>";
    }
}

echo "<h2>Creating Initial Admin User</h2>";

// Check if we should create an admin from .env or use the modal
$create_from_env = !empty($admin_name) && !empty($admin_pass);

if (!$create_from_env) {
    echo "<div class='info'><strong>No admin credentials found in .env</strong></div>";
    echo "<p>That's okay! You have two options to create your first admin:</p>";
    echo "<ol>";
    echo "<li><strong>Web Setup (Recommended):</strong> Visit <a href='admin.php'><strong>admin.php</strong></a> and use the first-time setup modal</li>";
    echo "<li><strong>CLI Setup:</strong> Run <code>php cli_create_admin.php</code> from the command line</li>";
    echo "</ol>";
    echo "<div class='warning'><strong>Note:</strong> The first-time setup modal will appear automatically when you visit admin.php since no admin users exist yet.</div>";
    echo "</body></html>";
    exit;
}

echo "<div class='info'>Admin credentials found in .env. Creating initial admin user...</div>";

// Get current admin credentials from config
if (empty($admin_name) || empty($admin_pass)) {
    echo "<div class='error'>Admin credentials not found in configuration!</div>";
    echo "<p>Please ensure ADMIN_USERNAME and ADMIN_PASSWORD are set in your .env file.</p>";
    exit;
}

// Hash the password using Argon2id
$password_hash = password_hash($admin_pass, PASSWORD_ARGON2ID);

// Insert the admin user
$username = DBEscape($admin_name);
$email = DBEscape("admin@" . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

$insert_sql = "INSERT INTO {$tablepre}_admin_users 
               (username, password_hash, email, full_name, is_active, created_at) 
               VALUES 
               ('$username', '$password_hash', '$email', 'System Administrator', 1, NOW())";

try {
    DBQuery($insert_sql);
    echo "<div class='success'><strong>✓ Successfully created admin user:</strong> $admin_name</div>";
    echo "<div class='warning'><strong>Important Security Notice:</strong></div>";
    echo "<ul>";
    echo "<li>Your admin credentials have been migrated to the database</li>";
    echo "<li>Passwords are now securely hashed using Argon2id</li>";
    echo "<li>Please log in and change your password immediately</li>";
    echo "<li>You can now remove ADMIN_USERNAME and ADMIN_PASSWORD from your .env file (but keep them as backup until you verify login works)</li>";
    echo "</ul>";
    
    echo "<h2>Next Steps</h2>";
    echo "<ol>";
    echo "<li><a href='admin.php'>Log in to the admin panel</a> with your existing credentials</li>";
    echo "<li>Go to Admin &gt; Manage Users to change your password</li>";
    echo "<li>Create additional admin users if needed</li>";
    echo "<li>Once verified, consider removing ADMIN_PASSWORD from .env for security</li>";
    echo "</ol>";
    
    echo "<div class='info'><strong>Database Authentication Enabled!</strong><br>";
    echo "The system will now use database authentication. If database authentication fails, it will fall back to .env credentials.</div>";
    
} catch (Exception $e) {
    echo "<div class='error'><strong>✗ Error creating admin user:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>

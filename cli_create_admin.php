#!/usr/bin/env php
<?php
/**
 * CLI Admin User Creation Tool
 * Creates the first admin user securely from command line
 * 
 * Usage: php cli_create_admin.php
 */

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

$base_folder = "";
require $base_folder."config.php";
require $base_folder."common.php";
require $base_folder."admin_auth.php";

echo "============================================\n";
echo "  Admin User Creation Tool\n";
echo "============================================\n\n";

DBConnect();

// Check if admin users table exists
if (!adminUsersTableExists()) {
    echo "ERROR: Admin users table does not exist.\n";
    echo "Please run setup_admin_db.php first to create the database tables.\n";
    exit(1);
}

// Check if any admin users already exist
$result = DBQuery("SELECT COUNT(*) as count FROM {$tablepre}_admin_users");
$row = DBFetchArray($result);
$existing_count = $row['count'];

if ($existing_count > 0) {
    echo "WARNING: {$existing_count} admin user(s) already exist in the database.\n";
    echo "Do you want to create an additional admin user? (yes/no): ";
    $confirm = trim(fgets(STDIN));
    if (strtolower($confirm) !== 'yes') {
        echo "Cancelled.\n";
        exit(0);
    }
    echo "\n";
}

// Collect username
echo "Enter username: ";
$username = trim(fgets(STDIN));

if (empty($username)) {
    echo "ERROR: Username cannot be empty.\n";
    exit(1);
}

// Check if username already exists
$check_username = DBEscape($username);
$result = DBQuery("SELECT id FROM {$tablepre}_admin_users WHERE username = '$check_username'");
if (DBNumRows($result) > 0) {
    echo "ERROR: Username '$username' already exists.\n";
    exit(1);
}

// Collect password (hidden input)
echo "Enter password (min 8 characters): ";
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
echo "\n";

if (strlen($password) < 8) {
    echo "ERROR: Password must be at least 8 characters long.\n";
    exit(1);
}

// Confirm password
echo "Confirm password: ";
system('stty -echo');
$password_confirm = trim(fgets(STDIN));
system('stty echo');
echo "\n";

if ($password !== $password_confirm) {
    echo "ERROR: Passwords do not match.\n";
    exit(1);
}

// Collect email (optional)
echo "Enter email address (optional, press Enter to skip): ";
$email = trim(fgets(STDIN));

// Collect full name (optional)
echo "Enter full name (optional, press Enter to skip): ";
$full_name = trim(fgets(STDIN));

// Confirm creation
echo "\n";
echo "============================================\n";
echo "  Confirm Admin User Creation\n";
echo "============================================\n";
echo "Username:  $username\n";
echo "Email:     " . ($email ?: '(not set)') . "\n";
echo "Full Name: " . ($full_name ?: '(not set)') . "\n";
echo "\nCreate this admin user? (yes/no): ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) !== 'yes') {
    echo "Cancelled.\n";
    exit(0);
}

// Create the user
echo "\nCreating admin user...\n";

$user_id = createAdminUser($username, $password, $email, $full_name);

if ($user_id) {
    echo "✓ SUCCESS: Admin user '$username' created with ID: $user_id\n";
    echo "\nYou can now log in at: " . (isset($_SERVER['HTTP_HOST']) ? "https://{$_SERVER['HTTP_HOST']}/admin.php" : "your-domain/admin.php") . "\n";
    echo "\nSecurity recommendations:\n";
    echo "- Change your password regularly\n";
    echo "- Use a strong password with mixed case, numbers, and symbols\n";
    echo "- Enable two-factor authentication if available\n";
    echo "- Monitor the admin activity log regularly\n";
    exit(0);
} else {
    echo "✗ ERROR: Failed to create admin user.\n";
    exit(1);
}

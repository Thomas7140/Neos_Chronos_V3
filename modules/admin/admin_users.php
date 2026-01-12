<?php
/**
 * Admin User Management Module
 * Manage admin users, passwords, and permissions
 */

if(!isset($authenticated) || !$authenticated) {
    die("Access denied");
}

$message = "";
$error = "";

// Handle form submissions
$submit_action = postParam('submit_action', '');

if ($submit_action === 'create_user') {
    $new_username = postParam('new_username', '');
    $new_password = postParam('new_password', '');
    $new_email = postParam('new_email', '');
    $new_full_name = postParam('new_full_name', '');
    
    if (empty($new_username) || empty($new_password)) {
        $error = "Username and password are required.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        $user_id = createAdminUser($new_username, $new_password, $new_email, $new_full_name);
        if ($user_id) {
            $message = "Admin user '$new_username' created successfully.";
            logAdminActivity($_SESSION['admin_id'], $_SESSION['admin_username'], 'create_user', "Created admin user: $new_username");
        } else {
            $error = "Failed to create user. Username may already exist.";
        }
    }
}

if ($submit_action === 'change_password') {
    $change_user_id = postParam('change_user_id', 0, 'int');
    $new_password = postParam('new_password', '');
    $confirm_password = postParam('confirm_password', '');
    
    if (empty($new_password)) {
        $error = "Password cannot be empty.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        if (updateAdminPassword($change_user_id, $new_password)) {
            $user = getAdminUser($change_user_id);
            $message = "Password updated successfully for '{$user['username']}'.";
            logAdminActivity($_SESSION['admin_id'], $_SESSION['admin_username'], 'change_password', "Changed password for user ID: $change_user_id");
        } else {
            $error = "Failed to update password.";
        }
    }
}

if ($submit_action === 'deactivate_user') {
    $deactivate_id = postParam('user_id', 0, 'int');
    if ($deactivate_id > 0 && $deactivate_id != $_SESSION['admin_id']) {
        deactivateAdminUser($deactivate_id);
        $message = "User deactivated successfully.";
        logAdminActivity($_SESSION['admin_id'], $_SESSION['admin_username'], 'deactivate_user', "Deactivated user ID: $deactivate_id");
    } else {
        $error = "Cannot deactivate your own account or invalid user.";
    }
}

if ($submit_action === 'activate_user') {
    $activate_id = postParam('user_id', 0, 'int');
    if ($activate_id > 0) {
        activateAdminUser($activate_id);
        $message = "User activated successfully.";
        logAdminActivity($_SESSION['admin_id'], $_SESSION['admin_username'], 'activate_user', "Activated user ID: $activate_id");
    }
}

// Get all admin users
$admin_users = getAllAdminUsers();

// Check if this is a database setup notice
$show_setup_notice = !adminUsersTableExists();

$content .= "<div class='admin-content'>";

if ($show_setup_notice) {
    $content .= "<div class='admin-warning'>";
    $content .= "<h2>⚠️ Database Authentication Not Set Up</h2>";
    $content .= "<p>You are currently using file-based authentication from the .env file.</p>";
    $content .= "<p>To enable secure database authentication with encrypted passwords:</p>";
    $content .= "<ol>";
    $content .= "<li>Run the setup script: <a href='setup_admin_db.php' target='_blank'><strong>setup_admin_db.php</strong></a></li>";
    $content .= "<li>This will create the database tables and migrate your current admin credentials</li>";
    $content .= "<li>Passwords will be securely hashed using Argon2id</li>";
    $content .= "<li>You'll be able to manage multiple admin users</li>";
    $content .= "</ol>";
    $content .= "</div>";
} else {
    $content .= "<h1>Admin User Management</h1>";
    
    if ($message) {
        $content .= "<div class='success-message'>$message</div>";
    }
    if ($error) {
        $content .= "<div class='error-message'>$error</div>";
    }
    
    // Current admin users table
    $content .= "<h2>Current Admin Users</h2>";
    $content .= "<table class='admin-table'>";
    $content .= "<thead>";
    $content .= "<tr>";
    $content .= "<th>ID</th>";
    $content .= "<th>Username</th>";
    $content .= "<th>Email</th>";
    $content .= "<th>Full Name</th>";
    $content .= "<th>Status</th>";
    $content .= "<th>Last Login</th>";
    $content .= "<th>Failed Attempts</th>";
    $content .= "<th>Actions</th>";
    $content .= "</tr>";
    $content .= "</thead>";
    $content .= "<tbody>";
    
    foreach ($admin_users as $user) {
        $status_class = $user['is_active'] ? 'status-active' : 'status-inactive';
        $status_text = $user['is_active'] ? 'Active' : 'Inactive';
        
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $status_text = 'Locked';
            $status_class = 'status-locked';
        }
        
        $last_login = $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never';
        
        $content .= "<tr>";
        $content .= "<td>{$user['id']}</td>";
        $content .= "<td><strong>{$user['username']}</strong></td>";
        $content .= "<td>{$user['email']}</td>";
        $content .= "<td>{$user['full_name']}</td>";
        $content .= "<td><span class='$status_class'>$status_text</span></td>";
        $content .= "<td>$last_login</td>";
        $content .= "<td>{$user['failed_login_attempts']}</td>";
        $content .= "<td>";
        
        // Change password button
        $content .= "<button onclick=\"showPasswordForm({$user['id']}, '{$user['username']}')\">Change Password</button> ";
        
        // Activate/Deactivate button (can't deactivate yourself)
        if ($user['id'] != $_SESSION['admin_id']) {
            if ($user['is_active']) {
                $content .= "<form method='post' style='display:inline;' onsubmit='return confirm(\"Deactivate this user?\");'>";
                $content .= "<input type='hidden' name='submit_action' value='deactivate_user'>";
                $content .= "<input type='hidden' name='user_id' value='{$user['id']}'>";
                $content .= "<button type='submit' class='btn-warning'>Deactivate</button>";
                $content .= "</form>";
            } else {
                $content .= "<form method='post' style='display:inline;'>";
                $content .= "<input type='hidden' name='submit_action' value='activate_user'>";
                $content .= "<input type='hidden' name='user_id' value='{$user['id']}'>";
                $content .= "<button type='submit' class='btn-success'>Activate</button>";
                $content .= "</form>";
            }
        }
        
        $content .= "</td>";
        $content .= "</tr>";
    }
    
    $content .= "</tbody>";
    $content .= "</table>";
    
    // Password change form (hidden by default)
    $content .= "<div id='password-form' style='display:none; margin-top: 20px; padding: 15px; border: 2px solid #007bff; background: #f8f9fa;'>";
    $content .= "<h3>Change Password for: <span id='password-username'></span></h3>";
    $content .= "<form method='post'>";
    $content .= "<input type='hidden' name='submit_action' value='change_password'>";
    $content .= "<input type='hidden' name='change_user_id' id='change_user_id' value=''>";
    $content .= "<div class='form-group'>";
    $content .= "<label>New Password:</label>";
    $content .= "<input type='password' name='new_password' required minlength='8' style='width: 300px;'>";
    $content .= "<small>Minimum 8 characters</small>";
    $content .= "</div>";
    $content .= "<div class='form-group'>";
    $content .= "<label>Confirm Password:</label>";
    $content .= "<input type='password' name='confirm_password' required minlength='8' style='width: 300px;'>";
    $content .= "</div>";
    $content .= "<button type='submit' class='btn-primary'>Update Password</button> ";
    $content .= "<button type='button' onclick=\"document.getElementById('password-form').style.display='none'\">Cancel</button>";
    $content .= "</form>";
    $content .= "</div>";
    
    // Create new admin user form
    $content .= "<h2 style='margin-top: 30px;'>Create New Admin User</h2>";
    $content .= "<form method='post' class='admin-form'>";
    $content .= "<input type='hidden' name='submit_action' value='create_user'>";
    $content .= "<div class='form-group'>";
    $content .= "<label>Username:</label>";
    $content .= "<input type='text' name='new_username' required maxlength='50' placeholder='admin_username'>";
    $content .= "</div>";
    $content .= "<div class='form-group'>";
    $content .= "<label>Password:</label>";
    $content .= "<input type='password' name='new_password' required minlength='8' placeholder='Minimum 8 characters'>";
    $content .= "</div>";
    $content .= "<div class='form-group'>";
    $content .= "<label>Email:</label>";
    $content .= "<input type='email' name='new_email' maxlength='100' placeholder='admin@example.com'>";
    $content .= "</div>";
    $content .= "<div class='form-group'>";
    $content .= "<label>Full Name:</label>";
    $content .= "<input type='text' name='new_full_name' maxlength='100' placeholder='John Doe'>";
    $content .= "</div>";
    $content .= "<button type='submit' class='btn-primary'>Create Admin User</button>";
    $content .= "</form>";
    
    // JavaScript for password form
    $content .= "<script>";
    $content .= "function showPasswordForm(userId, username) {";
    $content .= "  document.getElementById('password-form').style.display = 'block';";
    $content .= "  document.getElementById('password-username').textContent = username;";
    $content .= "  document.getElementById('change_user_id').value = userId;";
    $content .= "  document.getElementById('password-form').scrollIntoView({behavior: 'smooth'});";
    $content .= "}";
    $content .= "</script>";
    
    // CSS styles
    $content .= "<style>";
    $content .= ".admin-content { padding: 20px; }";
    $content .= ".admin-table { width: 100%; border-collapse: collapse; margin: 20px 0; }";
    $content .= ".admin-table th, .admin-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }";
    $content .= ".admin-table th { background: #007bff; color: white; }";
    $content .= ".admin-table tr:nth-child(even) { background: #f8f9fa; }";
    $content .= ".status-active { color: green; font-weight: bold; }";
    $content .= ".status-inactive { color: gray; }";
    $content .= ".status-locked { color: red; font-weight: bold; }";
    $content .= ".success-message { padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin: 10px 0; }";
    $content .= ".error-message { padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin: 10px 0; }";
    $content .= ".admin-warning { padding: 15px; background: #fff3cd; color: #856404; border: 2px solid #ffeeba; margin: 20px 0; }";
    $content .= ".admin-form { max-width: 500px; }";
    $content .= ".form-group { margin: 15px 0; }";
    $content .= ".form-group label { display: block; font-weight: bold; margin-bottom: 5px; }";
    $content .= ".form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }";
    $content .= ".form-group small { display: block; color: #666; margin-top: 3px; }";
    $content .= "button { padding: 8px 15px; margin: 5px; cursor: pointer; border: none; border-radius: 4px; }";
    $content .= ".btn-primary { background: #007bff; color: white; }";
    $content .= ".btn-warning { background: #ffc107; color: black; }";
    $content .= ".btn-success { background: #28a745; color: white; }";
    $content .= "button:hover { opacity: 0.9; }";
    $content .= "</style>";
}

$content .= "</div>";

?>

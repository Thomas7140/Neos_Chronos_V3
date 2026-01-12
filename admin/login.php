<?php
/**
 * Chronos V3 - Admin Login
 * 
 * Admin authentication interface.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

// Initialize application
define('CHRONOS_INIT', true);
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

// Start session
session_start();

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $db = Database::getInstance();
            $admin = $db->fetchOne(
                "SELECT * FROM " . TABLE_ADMIN . " WHERE username = ? AND is_active = 1",
                [$username]
            );
            
            if ($admin && verifyPassword($password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];
                
                // Update last login
                $db->update(
                    "UPDATE " . TABLE_ADMIN . " SET last_login = NOW() WHERE id = ?",
                    [$admin['id']]
                );
                
                // Log the login
                logMessage("Admin login: {$username} from " . getClientIp(), 'INFO');
                
                redirect('index.php');
            } else {
                $error = 'Invalid username or password.';
                logMessage("Failed admin login attempt: {$username} from " . getClientIp(), 'WARNING');
            }
        }
    }
}

$pageTitle = 'Admin Login - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle, 'html'); ?></title>
    <link rel="stylesheet" href="../templates/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .login-form {
            margin-top: 20px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: var(--accent-color);
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><?php echo sanitize(APP_NAME, 'html'); ?></h1>
            <p>Admin Panel Login</p>
        </div>
        
        <?php if ($error): ?>
            <div class="message message-error"><?php echo sanitize($error, 'html'); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message message-success"><?php echo sanitize($success, 'html'); ?></div>
        <?php endif; ?>
        
        <form method="post" class="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </div>
        </form>
        
        <div class="back-link">
            <a href="../index.php">← Back to Statistics</a>
        </div>
    </div>
</body>
</html>

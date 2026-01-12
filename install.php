<?php
/**
 * Chronos V3 - Installation Script
 * 
 * Web-based installer for setting up Chronos V3.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

define('CHRONOS_INIT', true);

// Load configuration
require_once __DIR__ . '/config.php';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];
$success = [];

// Check if already installed
$lockFile = __DIR__ . '/install.lock';
if (file_exists($lockFile)) {
    die('Chronos V3 is already installed. Delete install.lock to reinstall.');
}

// Step 1: System Requirements Check
if ($step === 1) {
    $checks = [
        'PHP Version >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'Writable cache directory' => is_writable(CACHE_PATH),
        'Writable logs directory' => is_writable(LOGS_PATH),
        'Writable uploads directory' => is_writable(UPLOADS_PATH),
        '.env file exists' => file_exists(__DIR__ . '/.env')
    ];
}

// Step 2: Database Setup
if ($step === 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once INCLUDES_PATH . '/database.php';
        $db = Database::getInstance();
        
        // Read and execute schema
        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $db->query($statement);
            }
        }
        
        $success[] = 'Database schema installed successfully!';
        $step = 3;
    } catch (Exception $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
}

// Step 3: Admin Account
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once INCLUDES_PATH . '/database.php';
        require_once INCLUDES_PATH . '/functions.php';
        
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '', 'email');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            $errors[] = 'All fields are required.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        } else {
            $db = Database::getInstance();
            
            // Update default admin account
            $hashedPassword = hashPassword($password);
            $db->update(
                "UPDATE " . TABLE_ADMIN . " SET username = ?, email = ?, password = ? WHERE id = 1",
                [$username, $email, $hashedPassword]
            );
            
            $success[] = 'Admin account created successfully!';
            $step = 4;
        }
    } catch (Exception $e) {
        $errors[] = 'Error creating admin account: ' . $e->getMessage();
    }
}

// Step 4: Complete Installation
if ($step === 4 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create lock file
    file_put_contents($lockFile, date('Y-m-d H:i:s'));
    $success[] = 'Installation completed successfully!';
    $step = 5;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Chronos V3</title>
    <link rel="stylesheet" href="templates/style.css">
    <style>
        .install-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .check-list {
            list-style: none;
            margin: 20px 0;
        }
        .check-list li {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .check-pass {
            background: #d4edda;
            color: #155724;
        }
        .check-fail {
            background: #f8d7da;
            color: #721c24;
        }
        .install-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .install-step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #ecf0f1;
            margin: 0 5px;
            border-radius: 5px;
        }
        .install-step.active {
            background: var(--accent-color);
            color: white;
        }
        .install-step.completed {
            background: var(--success-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <header style="text-align: center; margin-bottom: 30px;">
            <h1>Chronos V3 Installation</h1>
            <p>Follow the steps to install your statistics system</p>
        </header>

        <div class="install-steps">
            <div class="install-step <?php echo $step === 1 ? 'active' : ($step > 1 ? 'completed' : ''); ?>">
                1. Requirements
            </div>
            <div class="install-step <?php echo $step === 2 ? 'active' : ($step > 2 ? 'completed' : ''); ?>">
                2. Database
            </div>
            <div class="install-step <?php echo $step === 3 ? 'active' : ($step > 3 ? 'completed' : ''); ?>">
                3. Admin
            </div>
            <div class="install-step <?php echo $step === 4 ? 'active' : ($step > 4 ? 'completed' : ''); ?>">
                4. Complete
            </div>
        </div>

        <?php foreach ($errors as $error): ?>
            <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>

        <?php foreach ($success as $msg): ?>
            <div class="message message-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endforeach; ?>

        <?php if ($step === 1): ?>
            <h2>System Requirements</h2>
            <ul class="check-list">
                <?php foreach ($checks as $label => $passed): ?>
                    <li class="<?php echo $passed ? 'check-pass' : 'check-fail'; ?>">
                        <?php echo $passed ? '✓' : '✗'; ?> <?php echo $label; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <?php if (!in_array(false, $checks)): ?>
                <p>All requirements met! Click Next to continue.</p>
                <a href="?step=2" class="btn btn-primary">Next: Database Setup</a>
            <?php else: ?>
                <p class="message message-error">Please fix the above issues before continuing.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($step === 2): ?>
            <h2>Database Setup</h2>
            <p>Make sure you have created a MySQL/MariaDB database and configured .env file with correct credentials.</p>
            <p><strong>Database Name:</strong> <?php echo DB_NAME; ?></p>
            <p><strong>Database Host:</strong> <?php echo DB_HOST; ?></p>
            
            <form method="post">
                <p>Click the button below to create database tables.</p>
                <button type="submit" class="btn btn-primary">Install Database Schema</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 3): ?>
            <h2>Create Admin Account</h2>
            <p>Set up your administrator account to manage the system.</p>
            
            <form method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary">Create Admin Account</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 4): ?>
            <h2>Finalize Installation</h2>
            <p>Click the button below to complete the installation.</p>
            
            <form method="post">
                <button type="submit" class="btn btn-success">Complete Installation</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 5): ?>
            <h2>Installation Complete!</h2>
            <div class="message message-success">
                <p>Chronos V3 has been successfully installed!</p>
            </div>
            
            <h3>Next Steps:</h3>
            <ul>
                <li>Delete or rename <code>install.php</code> for security</li>
                <li><a href="admin/login.php">Login to Admin Panel</a></li>
                <li><a href="index.php">View Statistics Page</a></li>
                <li>Configure your game server to upload stats</li>
            </ul>
            
            <p>
                <a href="admin/login.php" class="btn btn-primary">Go to Admin Panel</a>
                <a href="index.php" class="btn btn-success">View Statistics</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>

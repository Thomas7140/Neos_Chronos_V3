<?php ob_start() ?>
<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$mtime1    = explode(" ", microtime());
$starttime = $mtime1[1] + $mtime1[0];

$base_folder  = "";
$mainscript   = $_SERVER['PHP_SELF']."?";
$content      = "";
$rowclass1    = "tablerow1";
$rowclass2    = "tablerow2";
$selectedasc  = "selectedasc";
$selecteddesc = "selecteddesc";
$unselected   = "unselected";

require $base_folder."config.php";
require $base_folder."common.php";
require $base_folder."weapons.php";
require $base_folder."gametypes.php";
require $base_folder."rating.php";
require $base_folder."functions.php";
require $base_folder."admin_auth.php";

// SECURITY FIX: Explicit parameter handling
$action = getParam('action', '');
$id = getParam('id', 0, 'int');
$frame = getParam('frame', '');
$username = postParam('username', '');
$password = postParam('password', '');

$is_frame = isset($frame) && (string)$frame === "1";
if ($is_frame) {
  $mainscript = $_SERVER['PHP_SELF']."?frame=1&";
} else {
  $mainscript = $_SERVER['PHP_SELF']."?";
}
$frame_script = $_SERVER['PHP_SELF']."?frame=1&";
$adminaction = $mainscript."action=admin_actions";

$default_action = "admin_players";
if(!isset($action) || $action == "") $action = $default_action;
$iframe_src = $frame_script . "action=" . $action;

DBConnect();
MakeTableNames();

// Start secure session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
  // Regenerate session ID periodically for security
  if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
  } elseif (time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
  }
}

$login_error = "";
$authenticated = false;

// Check for logout
$logoutsubmit = postParam('logoutsubmit', '');
if($logoutsubmit) {
  $_SESSION = array();
  session_destroy();
  setcookie("auth_string", "", time() - 3600, "/");
  if (!$is_frame) {
    header("Location: index.php");
    exit;
  }
}

// Check for login
$loginsubmit = postParam('loginsubmit', '');
if($loginsubmit) {
  $username = postParam('username', '');
  $password1 = postParam('password', '');
  
  // Rate limiting
  if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
  }
  
  // Reset attempts after 15 minutes
  if (time() - $_SESSION['last_attempt_time'] > 900) {
    $_SESSION['login_attempts'] = 0;
  }
  
  if ($_SESSION['login_attempts'] >= 5) {
    $login_error = "Too many login attempts. Please try again in 15 minutes.";
    logSecurityEvent('excessive_login_attempts', ['username' => $username]);
  } else {
    $authenticated_user = authenticateAdmin($username, $password1);
    
    if($authenticated_user) {
      // Successful login
      $_SESSION['authenticated'] = true;
      $_SESSION['admin_username'] = $username;
      $_SESSION['admin_id'] = $authenticated_user['id'];
      $_SESSION['login_time'] = time();
      $_SESSION['login_attempts'] = 0;
      
      // Update last login time in database
      updateAdminLastLogin($authenticated_user['id']);
      
      // Legacy cookie support
      $auth_str = md5($username)."|".md5($password1);
      setcookie("auth_string", $auth_str, time() + (86400*3), "/", "", false, true);
      
      logAdminActivity($authenticated_user['id'], $username, 'login_success', 'Successful admin login');
      logSecurityEvent('admin_login_success', ['username' => $username]);
    } else {
      $_SESSION['login_attempts']++;
      $_SESSION['last_attempt_time'] = time();
      $login_error = "Invalid username or password.";
      recordFailedLogin($username);
      logSecurityEvent('admin_login_failure', ['username' => $username]);
    }
  }
}

// Check authentication
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
  // Check session timeout (30 minutes)
  if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
    $_SESSION = array();
    session_destroy();
    $login_error = "Session expired. Please log in again.";
  } else {
    $_SESSION['login_time'] = time(); // Refresh login time
    $authenticated = true;
  }
} elseif (isset($_COOKIE['auth_string'])) {
  // Legacy cookie authentication (less secure, maintain for compatibility)
  $auth_str = $_COOKIE['auth_string'];
  $auth_parts = explode("|", $auth_str);
  if(count($auth_parts) == 2 && $auth_parts[0] == md5($admin_name) && $auth_parts[1] == md5($admin_pass)) {
    $authenticated = true;
    // Upgrade to session
    $_SESSION['authenticated'] = true;
    $_SESSION['admin_username'] = $admin_name;
    $_SESSION['login_time'] = time();
  }
}

require $base_folder."phemplate_class.php";

if($authenticated) {
  if ($is_frame) {
    if(file_exists($base_folder."modules/admin/$action.php")) {
      include $base_folder."modules/admin/$action.php";
    } else {
      $content = "<P class='error'>Error: Module '$action' does not exist</P>";    
    }
  } else {
    $content = "";
  }
} else {
  if ($is_frame) {
    $content = "<div class='admin-frame-message'>Please log in to access the admin panel.</div>";
  } else {
    $content = "";
  }
}

$login_overlay = "";
$page_blur_class = "";

// Check if this is first-time setup (admin table exists but no users)
$needs_setup = false;
$setup_message = "";
$tables_exist = false;

if(!$authenticated && !$is_frame) {
  // Auto-create tables if they don't exist
  if(!adminUsersTableExists()) {
    error_log("Admin users table does not exist, attempting to create...");
    if(createAdminUsersTables()) {
      $setup_message = "<div class='admin-modal-success'>✓ Database tables created successfully!</div>";
      $tables_exist = true;
      error_log("Admin users tables created successfully");
    } else {
      $setup_message = "<div class='admin-modal-error'>Failed to create database tables. Check that migrations/001_create_admin_users_table.sql exists and database has CREATE TABLE permissions.</div>";
      $tables_exist = false;
      error_log("Failed to create admin users tables");
    }
  } else {
    $tables_exist = true;
    error_log("Admin users table already exists");
  }
  
  // Now check if we need first-time setup
  if($tables_exist) {
    try {
      $user_count_result = DBQuery("SELECT COUNT(*) as count FROM {$tablepre}_admin_users");
      $user_count_row = DBFetchArray($user_count_result);
      $user_count = $user_count_row['count'];
      error_log("Admin users count: " . $user_count);
      
      if($user_count == 0) {
        $needs_setup = true;
        error_log("No admin users found, showing setup modal");
      } else {
        error_log("Found " . $user_count . " admin users, showing login modal");
      }
    } catch (Exception $e) {
      error_log("Error counting admin users: " . $e->getMessage());
      $needs_setup = false;
    }
    
    if($needs_setup) {
      
      // Handle first admin creation
      $setup_submit = postParam('setupsubmit', '');
      if($setup_submit) {
        $setup_username = postParam('setup_username', '');
        $setup_password = postParam('setup_password', '');
        $setup_confirm = postParam('setup_confirm', '');
        $setup_email = postParam('setup_email', '');
        $setup_fullname = postParam('setup_fullname', '');
        
        if(empty($setup_username) || empty($setup_password)) {
          $setup_message = "<div class='admin-modal-error'>Username and password are required.</div>";
        } elseif(strlen($setup_password) < 8) {
          $setup_message = "<div class='admin-modal-error'>Password must be at least 8 characters long.</div>";
        } elseif($setup_password !== $setup_confirm) {
          $setup_message = "<div class='admin-modal-error'>Passwords do not match.</div>";
        } else {
          $user_id = createAdminUser($setup_username, $setup_password, $setup_email, $setup_fullname);
          if($user_id) {
            // Auto-login the newly created user
            $_SESSION['authenticated'] = true;
            $_SESSION['admin_username'] = $setup_username;
            $_SESSION['admin_id'] = $user_id;
            $_SESSION['login_time'] = time();
            logAdminActivity($user_id, $setup_username, 'first_admin_created', 'First admin user created via setup modal');
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
          } else {
            $setup_message = "<div class='admin-modal-error'>Failed to create admin user. Please try again.</div>";
          }
        }
      }
    }
  }
}

if(!$authenticated && !$is_frame) {
  $page_blur_class = " is-blurred";
  
  if($needs_setup) {
    // Show first-time setup modal
    $login_overlay = <<<HTML
<div class="admin-overlay">
  <div class="admin-modal admin-modal-large">
    <div class="admin-modal-title">🎉 Welcome to Chronos Stats Admin</div>
    <div class="admin-modal-subtitle">Create Your First Administrator Account</div>
    $setup_message
    <form method="post" action="$mainscript" class="setup-form">
      <div class="form-row">
        <label for="setup-username">Username <span class="required">*</span></label>
        <input type="text" id="setup-username" name="setup_username" required maxlength="50" placeholder="admin" autocomplete="off">
        <small>Choose a unique username for the administrator account</small>
      </div>
      
      <div class="form-row">
        <label for="setup-password">Password <span class="required">*</span></label>
        <input type="password" id="setup-password" name="setup_password" required minlength="8" placeholder="Minimum 8 characters">
        <small>Use a strong password with mixed case, numbers, and symbols</small>
      </div>
      
      <div class="form-row">
        <label for="setup-confirm">Confirm Password <span class="required">*</span></label>
        <input type="password" id="setup-confirm" name="setup_confirm" required minlength="8" placeholder="Re-enter password">
      </div>
      
      <div class="form-row">
        <label for="setup-email">Email Address</label>
        <input type="email" id="setup-email" name="setup_email" maxlength="100" placeholder="admin@example.com">
        <small>Optional - for password recovery notifications</small>
      </div>
      
      <div class="form-row">
        <label for="setup-fullname">Full Name</label>
        <input type="text" id="setup-fullname" name="setup_fullname" maxlength="100" placeholder="John Doe">
        <small>Optional - display name for the admin panel</small>
      </div>
      
      <div class="admin-modal-actions">
        <input type="submit" name="setupsubmit" value="Create Administrator Account" class="btn-primary">
      </div>
    </form>
  </div>
</div>
<style>
.admin-modal-large {
  max-width: 550px;
  max-height: 90vh;
  overflow-y: auto;
}
.admin-modal-subtitle {
  font-size: 14px;
  color: #666;
  margin: -10px 0 20px 0;
  text-align: center;
}
.setup-form .form-row {
  margin-bottom: 20px;
}
.setup-form label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
  color: #333;
}
.setup-form .required {
  color: #dc3545;
}
.setup-form input[type="text"],
.setup-form input[type="password"],
.setup-form input[type="email"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}
.setup-form input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}
.setup-form small {
  display: block;
  color: #6c757d;
  font-size: 12px;
  margin-top: 4px;
}
.btn-primary {
  background: #007bff;
  color: white;
  font-weight: bold;
  padding: 12px 24px;
  font-size: 15px;
}
.btn-primary:hover {
  background: #0056b3;
}
</style>
HTML;
  } else {
    // Show normal login modal
    $error_html = "";
    if($login_error != "") {
      $error_html = "<div class='admin-modal-error'>$login_error</div>";
    }
    $login_overlay = <<<HTML
<div class="admin-overlay">
  <div class="admin-modal">
    <div class="admin-modal-title">Admin Login</div>
    $error_html
    <form method="post" action="$mainscript">
      <label for="admin-username">Username</label>
      <input type="text" id="admin-username" name="username" size="25" maxlength="40">
      <label for="admin-password">Password</label>
      <input type="password" id="admin-password" name="password" size="25" maxlength="40">
      <div class="admin-modal-actions">
        <input type="submit" name="loginsubmit" value="Login">
      </div>
    </form>
  </div>
</div>
HTML;
  }
}

$logout_form = "";
if($authenticated && !$is_frame) {
  $logout_form = <<<HTML
<form method="post" action="$mainscript">
               <input type="submit" name="logoutsubmit" value="Logout">
               </form>
HTML;
}

$tpl = new phemplate();
if ($is_frame) {
  $tpl->set_file("main", $base_folder."templates/admin/admin_frame.htm");
} else {
  $tpl->set_file("main", $base_folder."templates/admin/admin_main.htm");
}
$tpl->set_var("website_title", $website_title);
$tpl->set_var("charset",       $charset);
$tpl->set_var("mainscript",    $mainscript);
$tpl->set_var("version",       $version);
$tpl->set_var("base",          $base_folder);
$tpl->set_var("content", $content);
$tpl->set_var("frame_script", $frame_script);
$tpl->set_var("iframe_src", $iframe_src);
$tpl->set_var("logout_form", $logout_form);
$tpl->set_var("login_overlay", $login_overlay);
$tpl->set_var("page_blur_class", $page_blur_class);
echo $tpl->process("", "main", 1);

$mtime2 = explode(" ", microtime());
$endtime = $mtime2[1] + $mtime2[0];
$totaltime = ($endtime - $starttime);
$totaltime = number_format($totaltime, 3);

?>

<div style="visibility:hidden; font-size:xx-small;">admin.php 1.0.0</div>

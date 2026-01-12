<?php

if(isset($_GET["version"])) {
	echo 'config.php 1.0.0';
	die();
}


require_once __DIR__ . '/compat.php';

// Database/admin/FTP settings are defined in /home/devilishservices/connections/chronos_config.php

$website_title = "Devilish Services Stats";       // Website title
$charset       = "windows-1252";   // Character encoding (windows-1252 for english)

$playerstoshow = 25;  // How many players/maps to show per page
$averagelimit  = 0;   // Player must play this number of seconds before his stats show up
$percenttowin  = 60;  // Percent of game time player must play in order the win was recorded
$timefactor    = 60;  // Do not edit - number of seconds in the minute.

$date = mktime(0, 0, 0, date("m")  , date("d"), date("Y")); // Do not edit - todays date.
$today = date("Y-m-d", $date) ; // Do not edit - formatted date.
$date2 = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")); // Do not edit - yesterdays date.
$yesterday = date("Y-m-d", $date2) ; // Do not edit - formatted date.
$dow = date("l"); // Do not edit - current day.
$cur_mon = date("m"); // Do not edit - current month.
$cur_year = date("Y"); // Do not edit - current year.


// Load configuration - check for .env in multiple locations
$env_locations = [
    '/home/devilishservices/connections/.env',
    __DIR__ . '/.env'
];

$env_exists = false;
foreach ($env_locations as $env_path) {
    if (file_exists($env_path)) {
        $env_exists = true;
        break;
    }
}

$local_config = __DIR__ . '/chronos_config.php';
$secure_config = '/home/devilishservices/connections/chronos_config.php';

if ($env_exists) {
    // Use local config which will load .env from either location
    require_once $local_config;
} elseif (file_exists($secure_config) && is_readable($secure_config)) {
    // Fallback to external secure config (legacy)
    require_once $secure_config;
} else {
    die("Configuration file not found. Please create .env file or ensure external config exists.");
}

// Backward compatibility for variable names
if (isset($dbuname)) {
    $dbusername = $dbuname;
}
if (isset($dbpass)) {
    $dbuserpw = $dbpass;
}

?>

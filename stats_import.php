<?php
/*
 * Copyright (c) 2003, Tomas Stucinskas a.k.a Baboon
 * All rights reserved.
 *
 * Redistribution and use with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * Redistributions must retain the above copyright notice.
 * File licence.txt must not be removed from the package.
 *
 * Author        : Tomas Stucinskas a.k.a Baboon
 * E-mail        : baboon@ai-hq.com
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(isset($_GET["version"])) {
	echo 'stats_import.php 1.0.0';
	die();
}

global $client;
$client = "uploader";

require "config.php";
require "common.php";
require "weapons.php";
require "gametypes.php";
require "rating.php";
require "functions.php";

// SECURITY FIX: Explicit parameter handling
$data = postParam('data', '');
$serverid = postParam('serverid', '');

// Debug logging (disable after troubleshooting)
$debug_log_enabled = true;
$debug_log_path = __DIR__ . '/stats_import_debug.log';
$debug_log_limit = 2000;

DBConnect();
MakeTableNames();

if($data === '' || $serverid === '') {
  echo "No data sent";
  exit;
}

$raw_data = $data;
$decoded_data = base64_decode(str_replace(" ", "+", $raw_data), true);
if ($decoded_data === false) {
  $decoded_data = '';
}

if ($debug_log_enabled) {
  $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
  $raw_len = strlen($raw_data);
  $decoded_len = strlen($decoded_data);
  $raw_snip = substr($raw_data, 0, $debug_log_limit);
  $raw_snip = preg_replace('/[^\x20-\x7E]/', '.', $raw_snip);
  $decoded_snip = substr($decoded_data, 0, $debug_log_limit);
  $decoded_snip = preg_replace('/[^\x20-\x7E]/', '.', $decoded_snip);
  $log_entry = "[" . date("Y-m-d H:i:s") . "] method=$method ip=$client_ip serverid=$serverid raw_len=$raw_len decoded_len=$decoded_len raw=\"$raw_snip\" decoded=\"$decoded_snip\"\n";
  @file_put_contents($debug_log_path, $log_entry, FILE_APPEND | LOCK_EX);
}

$data = $decoded_data;

$query = DBQuery("SELECT * FROM $servers_table WHERE serverid='$serverid'");
if(DBNumRows($query) == 0) {
  echo "Invalid server id code ($serverid)\n";
  exit; 
}

echo ImportStats($data);
?>

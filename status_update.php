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
	echo 'status_update.php 1.0.0';
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

// Secure input handling (accept GET or POST)
$data = requestParam('data', '');
$serverid = requestParam('serverid', '');
$serverid = escapeSQL($serverid);

DBConnect();
MakeTableNames();

if($data === '' || $serverid === '') {
  echo "No data sent";
  exit;
}

$data =	base64_decode(str_replace(" ", "+", $data));

echo StatusUpdate($data, $serverid);

?>

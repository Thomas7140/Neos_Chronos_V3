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
 *
 * Finalized     : 29th April 2005
 * Modified by   : Peter Jones a.k.a Azrael
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(isset($_GET["data"]) && $_GET["data"] == "requestVer") {
	echo "2.0.0 BMTv3 (Neos-Standalone)";
	exit();
}

if(isset($_GET["version"])) {
	echo 'common.php 1.0.0';
	die();
}

$version    = "3.0.0 V3 (Standalone)";     // Release version - Standalone Editon, do not edit

// Load security helpers
require_once __DIR__ . '/security.php';

function DBConnect() { 
  global $dbhost, $dbuname, $dbpass, $dbname, $client, $dbusername, $dbuserpw;
  
  // Support legacy variable names
  if (!isset($dbuname) && isset($dbusername)) {
    $dbuname = $dbusername;
  }
  if (!isset($dbpass) && isset($dbuserpw)) {
    $dbpass = $dbuserpw;
  }
  
  if(!isset($client) || $client == "") $client = "browser";
  
  // Use new mysqli connection
  getDBConnection();
} 

function DBQuery($query) {
  global $db_connection;
  
  // Use mysqli connection
  $conn = getDBConnection();
  $result = $conn->query($query);
  
  if (!$result) {
    error_log("Query failed: " . $conn->error . " | Query: " . $query);
    if (defined('DEBUG_MODE') && constant('DEBUG_MODE')) {
      die("Query failed: " . $conn->error);
    } else {
      die("Database error occurred. Please contact administrator.");
    }
  }
  return $result;
}
 
function DBNumRows($query) {
  return $query->num_rows;
}
 
function DBFetchArray($query) {
  return $query->fetch_array(MYSQLI_ASSOC);
}

function DBEscape($string) {
  $conn = getDBConnection();
  return $conn->real_escape_string($string);
}

function DBInsertId() {
  $conn = getDBConnection();
  return $conn->insert_id;
}
 
function MakeTableNames() {
  global $tablepre;
  global $tablepre2;
  $tables = array("aliases", "awards", "games", "hof", "log", "maps", "mapstats", "monthawards", "playerawards", "players", 
                  "playergames", "playerips", "ranks", "squads", "servers", "serverhistory", "serverstats", "stats", 
				  "weapons", "weaponstats");
  $mtables = array("mapstats", "stats", "weaponstats");
  foreach($tables as $name) {
    global ${$name."_table"};
    ${$name."_table"} = $tablepre."_".$name;
  }
  foreach($mtables as $m_name) {
    global ${$m_name."_m_table"};
    ${$m_name."_m_table"} = $tablepre2."_".$m_name;
  }
}

function FormatTime($seconds) {
  $hours    = floor($seconds/3600);
  $seconds -= $hours * 3600; 
  $minutes  = floor($seconds/60);
  $seconds -= $minutes * 60; 
  return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
}

function GetIP() {
  $ip = '';
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // Handle proxy IPs - get first IP in chain
    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $ip = trim($ips[0]);
  } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  // Validate IP address
  return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

function GetRanking($id) {
  global $players_table;
  $id = (int) $id; // Sanitize input
  $i = 0; // Initialize counter
  $list = DBquery("SELECT $players_table.id FROM $players_table WHERE $players_table.rating > 0 GROUP BY $players_table.id ORDER BY rating DESC, name ASC");
  $ni = null;
  foreach ($list as $line) {
    $i++;
    foreach ($line as $col_value) {
      if ($col_value == $id) {
        $ni = $i;
        break 2;
      }
    }
  }
  if ($ni === null) {
    // If not found, return null or a default value
    return null;
  }
  $st = array('','2','3','4','5','6','7','8','9','10');
  $nd = array('','2','3','4','5','6','7','8','9','10');
  $rd = array('','2','3','4','5','6','7','8','9','10');
  $th = array('','1','2','3','4','5','6','7','8','9','10');
  $nni = $ni;
  foreach($st as $value){
    switch ($ni) {
      case $value.'1': $nni = $ni."st"; break;
    }
  }
  foreach($nd as $value){
    switch ($ni) {
      case $value.'2': $nni = $ni."nd"; break;
    }
  }
  foreach($rd as $value){
    switch ($ni) {
      case $value.'3': $nni = $ni."rd"; break;
    }
  }
  foreach($th as $value){
    switch ($ni) {
      case $value.'0': $nni = $ni."th"; break;
      case $value.'4': $nni = $ni."th"; break;
      case $value.'5': $nni = $ni."th"; break;
      case $value.'6': $nni = $ni."th"; break;
      case $value.'7': $nni = $ni."th"; break;
      case $value.'8': $nni = $ni."th"; break;
      case $value.'9': $nni = $ni."th"; break;
      case '1'.$value: $nni = $ni."th"; break;
    }
  }
  switch ($ni) {
    case 0 : $nni = "1st"; break;
  }
  return $nni;
}

function GetMonthlyRanking($id) {
  global $players_table;
  $id = (int) $id; // Sanitize input
  $i = 0;
  $list = DBQuery("SELECT $players_table.id FROM $players_table WHERE $players_table.m_rating > 0 GROUP BY $players_table.id ORDER BY m_rating DESC, name ASC");
  while ($line = DBFetchArray($list)) {
	$i++;
    foreach ($line as $col_value) {
      if($col_value != $id){
	    $ni = $i+1;
	  } else {
	    $st = array('','2','3','4','5','6','7','8','9','10');
	    $nd = array('','2','3','4','5','6','7','8','9','10');
	    $rd = array('','2','3','4','5','6','7','8','9','10');
		$th = array('','1','2','3','4','5','6','7','8','9','10');
		foreach($st as $value){
	      switch ($ni) {
            case $value.'1': $nni = $ni."st"; break;
		  }
	    }
	    foreach($nd as $value){
	      switch ($ni) {
            case $value.'2': $nni = $ni."nd"; break;
		  }
	    }
		foreach($rd as $value){
	      switch ($ni) {
            case $value.'3': $nni = $ni."rd"; break;
		  }
	    }
		foreach($th as $value){
	      switch ($ni) {
            case $value.'0': $nni = $ni."th"; break;
			case $value.'4': $nni = $ni."th"; break;
			case $value.'5': $nni = $ni."th"; break;
			case $value.'6': $nni = $ni."th"; break;
			case $value.'7': $nni = $ni."th"; break;
			case $value.'8': $nni = $ni."th"; break;
			case $value.'9': $nni = $ni."th"; break;
			case '1'.$value: $nni = $ni."th"; break;
		  }
	    }
		switch ($ni) {
           case 0 : $nni = "1st"; break;
		  }
	    return $nni;
	  }
    }
  }
}

function GetLogins($value, $serverid) {
  global $serverhistory_table, $dow, $today, $yesterday;
  
  switch($dow) {
    case "Monday"    : $tws = date("d")     ; $twe = date("d") + 6 ; $lws = date("d") - 7 ; $lwe = date("d") - 1 ;break;
	case "Tuesday"   : $tws = date("d") - 1 ; $twe = date("d") + 5 ; $lws = date("d") - 8 ; $lwe = date("d") - 2 ;break;
	case "Wednesday" : $tws = date("d") - 2 ; $twe = date("d") + 4 ; $lws = date("d") - 9 ; $lwe = date("d") - 3 ;break;
    case "Thursday"  : $tws = date("d") - 3 ; $twe = date("d") + 3 ; $lws = date("d") -10 ; $lwe = date("d") - 4 ;break;
	case "Friday"    : $tws = date("d") - 4 ; $twe = date("d") + 2 ; $lws = date("d") -11 ; $lwe = date("d") - 5 ;break;
	case "Saturday"  : $tws = date("d") - 5 ; $twe = date("d") + 1 ; $lws = date("d") -12 ; $lwe = date("d") - 6 ;break;
	case "Sunday"    : $tws = date("d") - 6 ; $twe = date("d")     ; $lws = date("d") -13 ; $lwe = date("d") - 7 ;break;
  }
  
  $start_date = mktime(0, 0, 0, date("m")  , $tws, date("Y"));
  $this_start = date("Y-m-d", $start_date) ;
  $end_date = mktime(0, 0, 0, date("m")  , $twe, date("Y"));
  $this_end = date("Y-m-d", $end_date) ;
  $last_start_date =  mktime(0, 0, 0, date("m")  , $lws, date("Y"));
  $last_start = date("Y-m-d", $last_start_date) ;
  $last_end_date = mktime(0, 0, 0, date("m")  , $lwe, date("Y"));
  $last_end = date("Y-m-d", $last_end_date) ;
  $month_s_date = mktime(0, 0, 0, date("m")  , date("d") , date("Y"));
  $month_start = date("Y-m-01", $month_s_date) ;
  $month_e_date = mktime(0, 0, 0, date("m")  , date("d") , date("Y"));
  $month_end = date("Y-m-t", $month_e_date) ;
  $lmonth_s_date = mktime(0, 0, 0, date("m") - 1  , date("d") , date("Y"));
  $lmonth_start = date("Y-m-01", $lmonth_s_date) ;
  $lmonth_e_date = mktime(0, 0, 0, date("m") - 1  , date("d") , date("Y"));
  $lmonth_end = date("Y-m-t", $lmonth_e_date) ;
  $year_s_date = mktime(0, 0, 0, date("m")  , date("d") , date("Y"));
  $year_start = date("Y-01-01", $year_s_date) ;
  $year_e_date = mktime(0, 0, 0, date("m")  , date("d") , date("Y"));
  $year_end = date("Y-12-t", $year_e_date) ;
  $lyear_s_date = mktime(0, 0, 0, date("m") , date("d") , date("Y") - 1);
  $lyear_start = date("Y-01-01", $lyear_s_date) ;
  $lyear_e_date = mktime(0, 0, 0, date("m")  , date("d") , date("Y") - 1);
  $lyear_end = date("Y-12-t", $lyear_e_date) ;

  
  switch($value) {
    case "todays"      : $criteria = 'COUNT(name) as num' ; $conditions = "date = '$today'"; break;
    case "yesterdays"  : $criteria = 'players as num'     ; $conditions = "date = '$yesterday'"; break;
    case "this_week"   : $criteria = 'SUM(players) as num'; $conditions = "date >= '$this_start' AND date <= '$this_end'"; break;
    case "last_week"   : $criteria = 'SUM(players) as num'; $conditions = "date >= '$last_start' AND date <= '$last_end'"; break;
    case "this_month"  : $criteria = 'SUM(players) as num'; $conditions = "date >= '$month_start' AND date <= '$month_end'"; break;
	case "last_month"  : $criteria = 'SUM(players) as num'; $conditions = "date >= '$lmonth_start' AND date <= '$lmonth_end'"; break;
    case "this_year"   : $criteria = 'SUM(players) as num'; $conditions = "date >= '$year_start' AND date <= '$year_end'"; break;
    case "last_year"   : $criteria = 'SUM(players) as num'; $conditions = "date >= '$lyear_start' AND date <= '$lyear_end'"; break;
  }
  
  $sql = DBQuery("SELECT $criteria FROM $serverhistory_table WHERE serverid = '$serverid' AND $conditions");
  $row = DBFetchArray($sql);
  return $row["num"] ?? 0;
}


function GetWeaponLevels($plid, $wpnid) {
  global $stats_table, $weapons_table, $weaponstats_table;
  
  if($wpnid == "CAR15/M203 - Grenade" || $wpnid == "M16/M203 - Grenade") $type = "weapontype1";
	
  if($wpnid == "CAR15" || $wpnid == "CAR15/M203" || $wpnid == "M16" || $wpnid == "M249 SAW" || 
    $wpnid == "M16/M203" || $wpnid == "G3" || $wpnid == "G36" || $wpnid == "M240" || $wpnid == "M60") $type = "weapontype2";
  
  if($wpnid == "M24" || $wpnid == "Barrett .50 Cal" || $wpnid == "PSG1" || 
    $wpnid == "MCRT .300 Tactical" || $wpnid == "M21" || $wpnid == "MP5" || $wpnid == "Knife") $type = "weapontype3";
  
  if($wpnid == "Colt .45" || $wpnid == "M9 Beretta" || $wpnid == "AT4" || $wpnid == "Remington Shotgun") $type = "weapontype4";
  
  if($wpnid == "50 Cal Humvee" || $wpnid == "MiniGun" || $wpnid == "Grenade Launcher" || $wpnid == "50 Cal Emplacement" || 
    $wpnid == "E M Canon" || $wpnid == "50 Cal Truck" || $wpnid == "Frag Grenade" || $wpnid == "Claymore") $type = "weapontype5";


  switch($type) {
    case "weapontype1"   : $div = '50'; break;
    case "weapontype2"   : $div = '40'; break;
    case "weapontype3"   : $div = '30'; break;
    case "weapontype4"   : $div = '20'; break;
    case "weapontype5"   : $div = '10'; break;
  }

  $query = DBQuery("SELECT $weapons_table.id, $weapons_table.name, $weaponstats_table.record, $stats_table.id, 
                      sum($weaponstats_table.kills) as kills, $weaponstats_table.weapon, $stats_table.player 
				    FROM $weapons_table, $weaponstats_table, $stats_table 
				    WHERE $weaponstats_table.weapon = $weapons_table.id AND $stats_table.id = $weaponstats_table.record 
				    AND $weapons_table.name LIKE '$wpnid' AND $stats_table.player = '$plid' GROUP BY $stats_table.player");
					
  $row = DBFetchArray($query);
  if($div < '1') {
  	$row["exp"] = '0';
  } else {
    $kills = $row["kills"]/$div;
    if(floor($kills) > 49) {
      $row["exp"] = 50;
    } else {
      $row["exp"] = floor($kills);
    }
  }
  return $row["exp"];
}

function GetGameLevels($id, $type, $group, $section) {
  global $players_table, $stats_table, $stats_m_table;
    
	if($section == "overall") {
	  $table = $stats_table;
	} elseif($section == "month") {
	  $table = $stats_m_table;
	}
	
    if($group == "player") {
	  $conditions = " AND  game_type LIKE '$type' AND $players_table.id = '$id' GROUP BY $players_table.id";
	} elseif($group == "squad") {
	  $conditions = " AND  game_type LIKE '$type' AND $players_table.squad = '$id' GROUP BY $players_table.squad";
	}
					 
	$data = DBQuery("SELECT $players_table.id, $players_table.squad, $table.player, SUM($table.kills) AS kills, 
	                   SUM($table.psptakeovers) AS psptakeovers, SUM($table.score_1) AS score_1, 
	                   SUM($table.score_2) AS score_2, SUM($table.score_3) AS score_3, SUM($table.games) AS games, SUM($table.time) AS time 
					 FROM $players_table, $table 
					 WHERE $table.player = $players_table.id 
					 $conditions");
    
	$row = DBFetchArray($data);
	
	if($row["kills"] === '0') $row["kills"] = 1;
	if($row["score_1"] === '0') $row["score_1"] = 1;
	if($row["score_2"] === '0') $row["score_2"] = 1;
	if($row["score_3"] === '0') $row["score_3"] = 1;
	if($row["games"] === '0') $row["games"] = 1;
	if($row["time"] === '0') $row["time"] = 1;
	if(!($row["games"])) $div = '1';
	
	switch($type) {
	  case "Deathmatch"           : $crit3 =  $row["kills"]/100       AND $div = '3'                                                                 ; break;
	  case "Team Deathmatch"      : $crit3 =  $row["kills"]/100       /*AND $crit4 = $row["psptakeovers"]-10*/ AND $div = '2'                            ; break;
	  case "Team King of the Hill": $crit3 =  ($row["score_1"]/60)/60 AND $crit4 = $row["score_2"]/15 AND $crit5 = $row["score_3"]/15 AND $div = '5' ; break;
	  case "Search and Destroy"   : $crit3 =  $row["score_1"]/25      AND $crit4 = $row["score_2"]/8  AND $crit5 = $row["score_3"]/8  AND $div = '5' ; break;
	  case "Attack and Defend"    : $crit3 =  $row["score_1"]/12      AND $crit4 = $row["score_2"]/12 AND $crit5 = $row["score_3"]/12 AND $div = '5' ; break;
	  case "Capture the Flag"     : $crit3 =  $row["score_1"]/10      AND $crit4 = $row["score_2"]/10 AND $crit5 = $row["score_3"]/10 AND $div = '5' ; break;
	  case "Flagball"             : $crit3 =  $row["score_1"]/6       AND $crit5 = $row["score_3"]/6  AND $div = '4'                                 ; break;
	}
	
	if($group == "squad") {
	  $squad = DBQuery("SELECT COUNT(id) AS num FROM $players_table WHERE squad = '$id'");
	  $srow = DBFetchArray($squad);
	  $div = $div*$srow["num"];
	}
	$crit1 = $row["games"]/10;
	$crit2 = ($row["time"]/60)/60;
	$crit = $crit1 + $crit2 + $crit3 + $crit4 + $crit5;
	if($crit < 1){
	  $row[$type] = "Level 0";
	} elseif(!($crit)) {
	  $row[$type] = "Level 0";
	} else {
	  $level = $crit/$div;
	  if(floor($level) > 49) {$row[$type] = "Level 50";} else {$row[$type] = "Level ".floor($level)."";}
    }
  return $row[$type];
}

function GetWeaponUsage($value, $id, $type, $server, $gametype) {
  global $players_table, $stats_table, $weaponstats_table, $weapons_table;

  $conditions = "";
  if($server   != -1)    $conditions .= " AND $stats_table.server='$server' ";
  if($gametype != "All") $conditions .= " AND $stats_table.game_type='$gametype' ";

  switch($type) {
    case "squad"     : $gettype = "GROUP BY $players_table.squad HAVING $players_table.squad = '$id'"   ; break;
	case "player"    : $gettype = "GROUP BY $players_table.id HAVING $players_table.id = '$id'"         ; break;
  }
  
  switch($value) {
    case "assault"   : $wpntype = "'CAR15', 'CAR15/M203', 'M16', 'M16/M203', 'G3', 'G36', 'MP5'"                                      ; break;
    case "support"   : $wpntype = "'M60', 'M240', 'M249 SAW'"                                                                         ; break;
    case "sniper"    : $wpntype = "'PSG1', 'MCRT .300 Tactical', 'M21', 'M24', 'Barrett .50 Cal'"                                     ; break;
    case "secondary" : $wpntype = "'Colt .45', 'M9 Beretta', 'Remington Shotgun'"                                                     ; break;
    case "other"     : $wpntype = "'Knife', 'CAR15/M203 - Grenade', 'M16/M203 - Grenade', 'Frag Grenade', 'Claymore', 'AT4'"          ; break;
    case "emplace"   : $wpntype = "'50 Cal Humvee', 'MiniGun', 'Grenade Launcher', '50 Cal Emplacement', 'E M Canon', '50 Cal Truck'" ; break;
  }
	
  $query = DBQuery("SELECT $players_table.id, $players_table.squad, $stats_table.id, $stats_table.player, 
                      $weapons_table.id, $weapons_table.name, $weaponstats_table.record, $weaponstats_table.weapon, SUM( $weaponstats_table.kills ) AS kills  
                    FROM $players_table, $stats_table, $weapons_table, $weaponstats_table WHERE $weapons_table.id = $weaponstats_table.weapon 
					AND $weaponstats_table.record = $stats_table.id AND $stats_table.player = $players_table.id
                    AND $weapons_table.name IN ($wpntype) 
					$conditions 
                    $gettype");
  while ($row = DBFetchArray($query)) {
    $kills = $row["kills"];
  }
  $ttlkills = DBQuery("SELECT $players_table.id, $players_table.squad, $stats_table.player, 
                         SUM($stats_table.kills) AS kills 
					   FROM $players_table, $stats_table WHERE $stats_table.player = $players_table.id 
					   AND $players_table.squad = '$id' $conditions GROUP BY $players_table.squad");
  while ($ttl_row = DBFetchArray($ttlkills)) {
    $tkills = $ttl_row["kills"];
    $div = ($tkills+1);
    $result = round ($kills/$div, 1);
  }
  return $result;
}

function GetMapWeaponUsage($value, $id, $server) {
  global $stats_table, $weaponstats_table, $weapons_table, $mapstats_table, $maps_table;

  $conditions = "";
  if($server   != -1)    $conditions .= " AND $stats_table.server='$server' ";
  
  switch($value) {
    case "assault"   : $wpntype = "'CAR15', 'CAR15/M203', 'M16', 'M16/M203', 'G3', 'G36', 'MP5'"                                      ; break;
    case "support"   : $wpntype = "'M60', 'M240', 'M249 SAW'"                                                                         ; break;
    case "sniper"    : $wpntype = "'PSG1', 'MCRT .300 Tactical', 'M21', 'M24', 'Barrett .50 Cal'"                                     ; break;
    case "secondary" : $wpntype = "'Colt .45', 'M9 Beretta', 'Remington Shotgun'"                                                     ; break;
    case "other"     : $wpntype = "'Knife', 'CAR15/M203 - Grenade', 'M16/M203 - Grenade', 'Frag Grenade', 'Claymore', 'AT4'"          ; break;
    case "emplace"   : $wpntype = "'50 Cal Humvee', 'MiniGun', 'Grenade Launcher', '50 Cal Emplacement', 'E M Canon', '50 Cal Truck'" ; break;
  }
	
  $query = DBQuery("SELECT $maps_table.id, $mapstats_table.map, $mapstats_table.record, $weaponstats_table.record, 
                      $weaponstats_table.weapon, SUM( $weaponstats_table.kills ) AS kills, 
					  $weapons_table.*, $stats_table.id, $stats_table.server
                    FROM $maps_table, $mapstats_table, $weaponstats_table, $weapons_table, $stats_table
                    WHERE $maps_table.id = '$id' AND $mapstats_table.map = $maps_table.id
                    AND $mapstats_table.record = $weaponstats_table.record AND $weaponstats_table.record = $stats_table.id 
                    AND $weaponstats_table.weapon = $weapons_table.id AND $weapons_table.name IN ($wpntype)
                    $conditions GROUP BY $maps_table.id");
  while ($row = DBFetchArray($query)) {
    if(!($row["kills"])){$kills = 0;} else {$kills = $row["kills"];}
  }
  $ttlkills = DBQuery("SELECT $maps_table.id, $mapstats_table.map, $mapstats_table.record, $weaponstats_table.record, 
                        $weaponstats_table.weapon, SUM( $weaponstats_table.kills ) AS kills, 
					    $stats_table.id, $stats_table.server
                      FROM $maps_table, $mapstats_table, $weaponstats_table, $stats_table
                      WHERE $maps_table.id = '$id' AND $mapstats_table.map = $maps_table.id
                      AND $mapstats_table.record = $weaponstats_table.record AND $weaponstats_table.record = $stats_table.id 
                      $conditions GROUP BY $maps_table.id");
  while ($ttl_row = DBFetchArray($ttlkills)) {
    if(!($ttl_row["kills"])){
	  $result = 0;
	} else {
      $tkills = $ttl_row["kills"];
      $div = ($tkills/100);
      $result = round ($kills/$div, 0);
	}
  }
  return $result;
}

function GetRatioColor($ratio) {
  if($ratio < '1.00' AND $ratio > '0.00') {
    $class = "red";
  } elseif($ratio > '1.00') {
    $class = "green";
  } elseif($ratio === '0.00') {
    $class = "blue";
  } else {
    $class = "blue";
  }
  return $class;
}
   
function GetPlayers1List($current, $all, $query_str) {
  $query           = DBQuery($query_str);
  $players1         = array();
  $row             = array();
  $count           = 0;
  if($all != 0) {
    $row["name"]     = "Select Player";
    $row["id"]       = -1;
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $players1[$count] = $row;
    $count++;
  }
  while($row = DBFetchArray($query)) {
  $row["name"] = htmlspecialchars(base64_decode(str_replace(" ", "+", $row["name"])));
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $players1[$count] = $row;
    $count++;
  }
  return $players1;
}

function GetPlayers2List($current, $all, $query_str) {
  $query           = DBQuery($query_str);
  $players2         = array();
  $row             = array();
  $count           = 0;
  if($all != 0) {
    $row["name"]     = "Select Player";
    $row["id"]       = -1;
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $players2[$count] = $row;
    $count++;
  }
  while($row = DBFetchArray($query)) {
  $row["name"] = htmlspecialchars(base64_decode(str_replace(" ", "+", $row["name"])));
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $players2[$count] = $row;
    $count++;
  }
  return $players2;
}

function GetSquadsList($current, $all) {
  global $squads_table;
  $query          = DBQuery("SELECT * FROM $squads_table ORDER BY name");
  $squads         = array();
  $row            = array();
  $count          = 0;
  if($all != 0) {
    $row["name"]    = "All";
    $row["id"]      = -1;
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $squads[$count] = $row;
    $count++;
  }
  while($row = DBFetchArray($query)) {
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $squads[$count] = $row;
    $count++;
  }
  return $squads;
}

function GetServersList($current, $all, $query_str) {
  $query           = DBQuery($query_str);
  $servers         = array();
  $row             = array();
  $count           = 0;
  if($all != 0) {
    $row["name"]     = "All";
    $row["id"]       = -1;
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $servers[$count] = $row;
    $count++;
  }
  while($row = DBFetchArray($query)) {
    if($current == $row["id"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $servers[$count] = $row;
    $count++;
  }
  return $servers;
}

function GetGameTypesList($current, $all, $query_str) {
  $query             = DBQuery($query_str);
  $gametypes         = array();
  $row               = array();
  $count             = 0;
  if($all != 0) {
    $row["name"]       = "All";
    $row["game_type"]  = "All";
    if($current == $row["game_type"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $gametypes[$count] = $row;
    $count++;
  }
  while($row = DBFetchArray($query)) {
    if($current == $row["game_type"]) {
      $row["selected"] = "selected=\"selected\"";
    } else {
      $row["selected"] = "";
    }
    $row["name"] = $row["game_type"];
    $gametypes[$count] = $row;
    $count++;
  }
  return $gametypes;
}

function GetRank($rating) {
  global $players_table, $ranks_table;
  if($rating < 0) $rating = 0;
  $max_rank_query   = DBQuery("SELECT * FROM $ranks_table ORDER BY rating DESC LIMIT 1");
  $max_rank         = DBFetchArray($max_rank_query);
  $max_rank         = $max_rank["id"];
  $rank_query       = DBQuery("SELECT * FROM $ranks_table WHERE rating<='$rating' ORDER BY rating DESC");
  $rank             = DBFetchArray($rank_query);
  if($max_rank == $rank["id"]) {
    $max_rating_query = DBQuery("SELECT * FROM $players_table ORDER BY rating DESC LIMIT 1");
    $max_rating       = DBFetchArray($max_rating_query);
    $max_rating       = $max_rating["rating"];
    if($max_rating > $rating) {
      $rank = DBFetchArray($rank_query);
    }
  }
  return $rank;
}

function ShortenGameType($gametype) {
  switch($gametype) {
    case "Cooperative"           : $gametype = "COOP";   break;
    case "Deathmatch"            : $gametype = "DM";     break;
    case "Team Deathmatch"       : $gametype = "TDM";    break;
    case "King of the Hill"      : $gametype = "KOTH";   break;
    case "Team King of the Hill" : $gametype = "TKOTH";  break;
    case "Capture the Flag"      : $gametype = "CTF";    break;
    case "Flagball"              : $gametype = "FB";     break;
    case "Attack and Defend"     : $gametype = "A&D";    break;
    case "Search and Destroy"    : $gametype = "S&D";    break;
    default                      : $gametype = "unknown";
  }
  return $gametype;  
}

function ShortenGameName($gamename) {
  $gamename = str_replace("Delta Force Land Warrior",      "DFLW",  $gamename);
  $gamename = str_replace("Delta Force Task Force Dagger", "DFTFD", $gamename);
  $gamename = str_replace("Delta Force Black Hawk Down",   "DFBHD", $gamename);
  $gamename = str_replace("Delta Force 2",                 "DF2",   $gamename);
  $gamename = str_replace("Delta Force",                   "DF",    $gamename);
  return $gamename;
}

function ParseMessage($msg, $tpl, $t, $s, &$target, &$source) {
  $tpl   = "-".$tpl."-";
  $msg   = "-".$msg."-";
  $parts = explode("$", $tpl);
  $num   = count($parts);
  $part  = $msg;
  $found = array();
  $index = 1;
  for($i = 0; $i < $num; $i++) {
    $old_part = $part;
    $part = strstr($part, $parts[$i]);
    if(!$part) return FALSE;
    $parti = substr($part, 0, strlen($parts[$i]));
    if($i != 0) {
      $ply = substr($old_part, strlen($parts[$i-1]), strlen($old_part) - strlen($part) - strlen($parts[$i-1]));
      $found[$index] = $ply;
      $index++;     
    }
  }
  $target = $found[$t];
  $source = $found[$s];
  return TRUE;
}

function Multipage($url, $total, $perpage, $page) {
  $multipage = "";
  if($total > $perpage) {
	$pages = $total  / $perpage;
	$pages = ceil($pages);
	if ($page == $pages) {
	  $to = $pages;
	} elseif ($page == $pages-1) {
	  $to = $page+1;
	} elseif ($page == $pages-2) {
	  $to = $page+2;
	} else {
	  $to = $page+3;
	}

	if($page == 1 || $page == 2 || $page == 3) {
	  $from = 1;
	} else {
	  $from = $page-3;
	}

	$fwd_back = "<a href=\"$url&page=1\">&laquo;</a>&nbsp;";

	for($i = $from; $i <= $to; $i++) {
	  if($i != $page) {
	    $fwd_back .= "<a href=\"$url&page=$i\">$i</a>&nbsp;";
	  } else {
	    $fwd_back .= "<b>$i</b>&nbsp;";
	  }
	}

	$fwd_back .= "<a href=\"$url&page=$pages\">&raquo;</a>";
	$multipage = $fwd_back;
  }
  return $multipage;
}

function formatRow($id, $table) {

  $query = mysql_query("select * from $table WHERE id = '$id'");
  $num_fields = @mysql_num_fields($query);

  $row = @mysql_fetch_row($query);
  $string = "INSERT INTO $table VALUES(";
  for($i = 0; $i < $num_fields; $i++) {
    if($i < ($num_fields-1)) {
	  $string .= '"'.$row[$i].'", ';
	} else {
	  $string .= '"'.$row[$i].'"';
    }
  }
  $string .= ");\r\n";
  return $string;
}

function folderPerm($ftpInfoArr, $folderPath, $perm) {
	$ftpConn = ftp_connect($ftpInfoArr["fServer"]);
	$ftpLogin = ftp_login($ftpConn, $ftpInfoArr["fUser"], $ftpInfoArr["fPass"]);

	if (ftp_site($ftpConn, 'CHMOD '.$perm.' '.$ftpInfoArr["fRoot"].$folderPath) !== false) {
		ftp_close($ftpConn);
		return true;
	} else {
	ftp_close($ftpConn);
		die("Unable to CHMOD Folder!");
		return false;
	}
}
?>

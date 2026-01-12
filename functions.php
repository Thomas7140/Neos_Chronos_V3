<?php
ob_start();

if(isset($_GET["version"])) {
	echo 'functions.php 1.0.0';
	die();
}

function GetServer($id_code) {
  global $servers_table;
  $id_code = escapeSQL($id_code);
  $query = DBQuery("SELECT * FROM $servers_table WHERE serverid='$id_code'");
  if(DBNumRows($query) == 0) {
    return -999;
  }
  $server = DBFetchArray($query);
  return $server["id"];
}

function GetMap($map_name, $game_type, $ssid) {
  global $maps_table, $serverstats_table;
  if(!isset($map_name) || $map_name == "") return -999;
  $map_name = escapeSQL($map_name);
  $game_type = escapeSQL($game_type);
  $ssid = (int) $ssid;
  $query = DBQuery("SELECT * FROM $maps_table WHERE name='$map_name' AND game_type='$game_type'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $maps_table (name, game_type) VALUES ('$map_name', '$game_type')");
	DBQuery("UPDATE $serverstats_table SET 
	             maps       = maps   + 1              
               WHERE id='".$ssid."'");
  }
  $query = DBQuery("SELECT * FROM $maps_table WHERE name='$map_name' AND game_type='$game_type'");
  $map = DBFetchArray($query);
  return $map["id"];
}

function GetMonthMapRecord($map_id, $record_id) {
  global $mapstats_m_table;
  if($map_id == -999 || $record_id == -999) return -999;
  $map_id = (int) $map_id;
  $record_id = (int) $record_id;
  $query = DBQuery("SELECT * FROM $mapstats_m_table WHERE map='$map_id' AND record='$record_id'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $mapstats_m_table (record, map) VALUES ('$record_id', '$map_id')");
  }  
  $query = DBQuery("SELECT * FROM $mapstats_m_table WHERE map='$map_id' AND record='$record_id'");
  $map_record = DBFetchArray($query);
  return $map_record["id"];
}

function GetMapRecord($map_id, $record_id) {
  global $mapstats_table;
  if($map_id == -999 || $record_id == -999) return -999;
  $map_id = (int) $map_id;
  $record_id = (int) $record_id;
  $query = DBQuery("SELECT id FROM $mapstats_table WHERE record='$record_id' AND map='$map_id' ");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $mapstats_table (record, map) VALUES ('$record_id', '$map_id')");
  }  
  $query = DBQuery("SELECT id FROM $mapstats_table WHERE map='$map_id' AND record='$record_id'");
  $map_record = DBFetchArray($query);
  return $map_record["id"];
}

function GetPlayer($player_name) {
  global $players_table;
  if(!isset($player_name) || $player_name == "") return -999;
  $player_name = escapeSQL($player_name);
  $query = DBQuery("SELECT * FROM $players_table WHERE name='$player_name'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $players_table (name, squad) VALUES ('$player_name', '-1')");
  }  
  $query = DBQuery("SELECT * FROM $players_table WHERE name='$player_name'");
  $player = DBFetchArray($query);
  return $player["id"];
}

function GetNcVal($value) {
	if($value != '1') die (base64_decode("VXBsb2FkaW5nIEZhaWxlZCwgSW5jb3JyZWN0IERhdGEh"));
}

function GetPlayerRecord($player_id, $game_type, $server_id) {
  global $stats_table;
  if($player_id == -999) return -999;
  $player_id = (int) $player_id;
  $server_id = (int) $server_id;
  $game_type = escapeSQL($game_type);
  $query = DBQuery("SELECT * FROM $stats_table WHERE player='$player_id' AND server='$server_id' AND game_type='$game_type'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $stats_table (player, server, game_type) VALUES ('$player_id', '$server_id', '$game_type')");
  }  
  $query = DBQuery("SELECT * FROM $stats_table WHERE player='$player_id' AND server='$server_id' AND game_type='$game_type'");
  $record = DBFetchArray($query);
  return $record["id"];
}

function GetGame($map_id, $winner, $server, $game_type, $date) {
  global $games_table;
  if($map_id == -999) return -999;
  DBQuery("INSERT INTO $games_table (map_id, winner, server, game_type, date_played) VALUES ('$map_id', '$winner', '$server', '$game_type', '$date')");
  $query = DBQuery("SELECT * FROM $games_table WHERE map_id = '$map_id' AND server = '$server' 
                                               AND game_type = '$game_type' AND date_played = '$date'");
  $record = DBFetchArray($query);
  return $record["id"];
}

function GetPlayerGame($player_id, $game_id, $team) {
  global $playergames_table; 
  $query = DBQuery("SELECT * FROM $playergames_table WHERE player = '$player_id' AND game_id = '$game_id' 
                                                     AND team = '$team'");
  if(DBNumRows($query) == 0) {
  	$record["id"] = '-1';
  } else {
  	$record = DBFetchArray($query);
  }
  return $record["id"];
}

function storePlayerIp($player_id, $ip, $date) {
  global $playerips_table;
  $query = DBQuery("SELECT * FROM $playerips_table WHERE player = '$player_id'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $playerips_table (player, ip_address, last_recorded) VALUES ('$player_id', '$ip', '$date')");
  } else {
  	DBQuery("UPDATE $playerips_table SET last_recorded = '$date'");
  }
}

function GetMonthPlayerRecord($player_id, $game_type, $server_id, $mDate) {
  global $stats_m_table;
  $exDate = explode("-", $mDate);
  if($exDate[1] < '10') $exDate[1] = '0'.$exDate[1];
  $subDate = $exDate[0]."-".$exDate[1];
  if($player_id == -999) return -999;
  $query = DBQuery("SELECT * FROM $stats_m_table WHERE player='$player_id' AND server='$server_id' AND game_type='$game_type' AND 
                    SUBSTRING(last_played, 1, 7) = '".$subDate."'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $stats_m_table (player, server, game_type, last_played) VALUES ('$player_id', '$server_id', '$game_type', '$mDate')");
  }  
  $query = DBQuery("SELECT * FROM $stats_m_table WHERE player='$player_id' AND server='$server_id' AND game_type='$game_type' AND 
                    SUBSTRING(last_played, 1, 7) = '".$subDate."'");
  $record = DBFetchArray($query);
  return $record["id"];
}


function GetPlayerAlias($player_name) {
  global $aliases_table;
  $query = DBQuery("SELECT * FROM $aliases_table WHERE from_name='$player_name'");
  if(DBNumRows($query) != 0) {
    $alias = DBFetchArray($query);
    return $alias["to_name"];
  }
  return $player_name;
}

function GetServerStats($serverid, $game_type) {
  global $serverstats_table;
  if(!isset($serverid) || $serverid == "") return -999;
  $query = DBQuery("SELECT * FROM $serverstats_table WHERE serverid='$serverid' AND game_type='$game_type'");
  
  if(DBNumRows($query) == 0) {
	DBQuery("INSERT INTO $serverstats_table (serverid, game_type, games, maps, time) VALUES ('$serverid', '$game_type', 0, 0, 0)");
  }
  $query = DBQuery("SELECT * FROM $serverstats_table WHERE serverid='$serverid' AND game_type='$game_type'");
  $serverstats = DBFetchArray($query);
  return $serverstats["id"];
}


function GetServerHistory($serverid, $name, $date) {
  global $serverhistory_table, $today;
  if(!isset($serverid) || $serverid == "") return -999;
  $query = DBQuery("SELECT * FROM $serverhistory_table WHERE serverid='$serverid' AND name = '$name' AND date='$date'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $serverhistory_table (serverid, players, name, date) VALUES ('$serverid', 0, '$name', '$date')");
  }
  $query = DBQuery("SELECT * FROM $serverhistory_table WHERE serverid='$serverid' AND name = '$name' AND date='$date'");
  $serverhistory = DBFetchArray($query);
  if(!$serverhistory || !isset($serverhistory["name"])) {
    return "";
  }
  return $serverhistory["name"];
}

function GetWeapon($weapon_name) {
  global $weapons_table;
  if(!isset($weapon_name) || $weapon_name == "") return -999;
  $query = DBQuery("SELECT * FROM $weapons_table WHERE name='$weapon_name'");
  if(DBNumRows($query) == 0) {
   DBQuery("INSERT INTO $weapons_table (name) VALUES ('$weapon_name')");
  }  
  $query = DBQuery("SELECT * FROM $weapons_table WHERE name='$weapon_name'");
  $weapon = DBFetchArray($query);
  return $weapon["id"];
}

function GetWeaponRecord($weapon_id, $record_id) {
  global $weaponstats_table;
  if($weapon_id == -999 || $record_id == -999) return -999;
  $query = DBQuery("SELECT * FROM $weaponstats_table WHERE record='$record_id' AND weapon='$weapon_id'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $weaponstats_table (record, weapon) VALUES ('$record_id', '$weapon_id')");
  }  
  $query = DBQuery("SELECT * FROM $weaponstats_table WHERE record='$record_id' AND weapon='$weapon_id'");
  $record = DBFetchArray($query);
  return $record["id"];
}

function GetMonthWeaponRecord($weapon_id, $record_id) {
  global $weaponstats_m_table;
  if($weapon_id == -999 || $record_id == -999) return -999;
  $query = DBQuery("SELECT * FROM $weaponstats_m_table WHERE record='$record_id' AND weapon='$weapon_id'");
  if(DBNumRows($query) == 0) {
    DBQuery("INSERT INTO $weaponstats_m_table (record, weapon) VALUES ('$record_id', '$weapon_id')");
  }  
  $query = DBQuery("SELECT * FROM $weaponstats_m_table WHERE record='$record_id' AND weapon='$weapon_id'");
  $record = DBFetchArray($query);
  return $record["id"];
}

function ImportStats($data) {

  global $aliases_table, $awards_table, $dow, $log_table, $maps_table, $mapstats_table, $players_table, 
         $playerips_table, $playerawards_table, $playergames_table, $ranks_table, $serverhistory_table, $servers_table, 
		 $serverstats_table, $games, $stats_m_table, $weaponstats_m_table, $mapstats_m_table, 
		 $squads_table, $stats_table, $today, $weapons_table, $weaponstats_table, $wpn_points,
         $points, $percenttowin, $gametypes, $wpn_name, $bmt;
  
  $log = "";

  $data_lines = explode("\n", $data);
  $line_count = count($data_lines);

  $status = "blank";
  
  $server_id        = 0;
  $player_id        = 0;
  $player_record_id = 0; 
  $weapon_id        = 0;
  $weapon_record_id = 0; 
  $map_id           = 0;
  $map_record_id    = 0;
   
  $game        = array(
    "timer"     => "0",
    "date"      => "",
    "gametype"  => "",
    "dedicated" => "",
    "servername"=> "",
    "mapname"   => "",
    "maxplayers"=> "0",
    "numplayers"=> "0",
    "winner"    => "",
    "mod"       => 0
  );
  $player      = array();
  $weapon      = array();
  
  $game_data = array(
    0 => "timer"     ,
    1 => "date"      ,
    2 => "gametype"  ,
    3 => "dedicated" ,
    4 => "servername",
    5 => "mapname"   ,
    6 => "maxplayers",
    7 => "numplayers",
    8 => "winner"    , 
	9 => "mod"
  );                             

  $player_data = array(
    0  => "suicides"        ,
    1  => "murders"         ,
    2  => "kills"           ,
    3  => "deaths"          ,
    4  => "zonetime"        ,
    5  => "flags"           ,
    6  => "flagsaves"       ,
    7  => "targets"         ,
    8  => "revives"         ,
    9  => "medsaves"        ,
    10 => "pspattempts"     ,
    11 => "psptakeovers"    ,
    12 => "flagcarrierkills",
    13 => "doublekills"     ,
    14 => "headshots"       ,
    15 => "knifings"        ,
    16 => "sniperkills"     ,
    17 => "tkothattackkills",
    18 => "tkothdefendkills",
    19 => "sdaddefendkills" ,
    20 => "sdadpolicekills" ,
    21 => "sdadattackkills" ,
    22 => "sdadsecurekills" ,
    23 => "shotsperkill"    ,
    24 => "experience"      ,
    25 => "team"            ,
    26 => "playedtillend"   ,
    27 => "timer"
  );
                              
  $weapon_data = array(
    0 => "code",
    1 => "timer",
    2 => "kills",
    3 => "shots"
  );                             

  $skip        = false;
  $skip_to     = "";
  
  //for($i = 0, $max = $line_count; $i < $max; $i++) {
  for($i = 0; $i < $line_count; $i++) {

    $line = $data_lines[$i];
    $tag  = explode(" ", trim($line));
    $tag  = $tag[0];
    
    $offset = 0;
    
    switch($tag) {
      case "ServerID":    {$status = "server_id";   $offset = 8; } break;
      case "Game":        {$status = "game";        $offset = 5; } break;
      case "Player":      {$status = "player";      $offset = 8; } break;
      case "PlayerStats": {$status = "player_stats";$offset = 14;} break;
      case "Weapon":      {$status = "weapon";      $offset = 9; } break;
      default:            {$status = "blank";       $offset = 0; }
    }
    
    if($skip && $status != $skip_to) continue;
    
    $skip    = false;
    $skip_to = "";
    
    $line = trim(substr($line, $offset));

    if($status == "blank") {}
    
    if($status == "server_id") {
      $id_code = $line;
      $server_id = GetServer($id_code);
      if($server_id == -999) {
        $log .= "Invalid server id code ($id_code)";
        $skip    = true;
        $skip_to = "server_id"; 
        continue;
      }
    }
    
    if($status == "game") {
      $tmp = array();
      $tmp = explode("__&__", $line);
      foreach($tmp as $field => $value) {
        if(isset($game_data[$field])) {
          $game[$game_data[$field]] = $tmp[$field];
        }
      }
	  if(!isset($game["mod"]) || $game["mod"] === "") {
	    $game["mod"] = 0;
	  }
	  $game["timer"] = str_replace("Game ", "", $game["timer"]);
	  $query = DBQuery("SELECT * FROM $log_table WHERE server='".$server_id."' AND datetime='".$game["date"]."'");
        if(DBNumRows($query) != 0) {
          die("Already imported - ".$game["date"]);
          $skip    = true;
          $skip_to = "server_id";
          continue;
        }
       
	  if(!isset($bmt) || $bmt === "") {
	    $bmt = requestParam('bmt', '1');
	  }
	   
      if($game["gametype"] === "" || !isset($gametypes[$game["gametype"]])) {
        $log .= "Unknown gametype (Code ".$game["gametype"].")";
        $skip    = true;
        $skip_to = "server_id";
        continue;
      }
	  $type             = $game["gametype"];
      $game["mapname"]  = base64_encode(str_replace("??", "", $game["mapname"]));
      $game["gametype"] = $gametypes[$game["gametype"]];
	  $serverid         = GetServerStats($server_id, $game["gametype"]);
	  $map_id           = GetMap($game["mapname"], $game["gametype"], $serverid);
	  
      DBQuery("UPDATE $maps_table SET 
                 time        = time    + '".$game["timer"]."' ,
                 hosted      = hosted  + 1                    ,
                 last_played = '".$game["date"]."'             
               WHERE id='".$map_id."'");
			   
	  DBQuery("UPDATE $serverstats_table SET 
	             games       = games   + 1                    ,
                 time        = time    + '".$game["timer"]."'              
               WHERE id='".$serverid."'");
			   
	  $game_id = GetGame($map_id, $game["winner"], $server_id, $game["gametype"], $game["date"]);
    }
    
    if($status == "player") {
      $plyrStr = explode("__&__", $line);
	  $player["name"] = $plyrStr[0] ?? "";
	  $player["ip"]   = $plyrStr[1] ?? "";
	  $pn = array($player["name"]);
	  	foreach($pn as $value) {
		  $pnm = base64_encode($value);
		  $name = DBQuery("SELECT * FROM $players_table WHERE name = '$pnm'");
          while($n_row = DBFetchArray($name)) {
            $id = $n_row["id"];
            $award_query = DBQuery("SELECT * FROM $awards_table WHERE type = 'A'");
            $stats_query = DBQuery("SELECT * FROM $stats_table WHERE $stats_table.player='$id'");
            $award_stats = array();
            $stats_row   = array();
            while($stats_row = DBFetchArray($stats_query)) {
              foreach($stats_row as $field => $value) {
              if(!is_numeric($field)) {
                  $stats_field = "";
                  switch($field) {
                    case "kills":        $stats_field = "kills";           break;
                    case "deaths":       $stats_field = "deaths";          break;
                    case "murders":      $stats_field = "murders";         break;
                    case "suicides":     $stats_field = "suicides";        break;
                    case "knifings":     $stats_field = "knifings";        break;
                    case "headshots":    $stats_field = "headshots";       break;
                    case "medic_saves":  $stats_field = "medic_saves";     break;
                    case "revives":      $stats_field = "revives";         break;
                    case "pspattempts":  $stats_field = "psp_attempts";    break;
                    case "psptakeovers": $stats_field = "psp_takeovers";   break;
                    case "doublekills":  $stats_field = "double_kills";    break;
                    case "time":         $stats_field = "time_played";     break;
                    case "games":        $stats_field = "games_completed"; break;
                    case "wins":         $stats_field = "team_games_won";  break;
                  }
                  if($stats_row["game_type"] == "Team King of the Hill") {
                    switch($field) {
                      case "score_1": $stats_field = "zone_time";               break;
                      case "score_2": $stats_field = "tkoth_zone_attack_kills"; break;
                      case "score_3": $stats_field = "tkoth_zone_defend_kills"; break;
                    }
                  }
                  if($stats_row["game_type"] == "Capture the Flag" || $stats_row["game_type"] == "Flagball") {
                    switch($field) {
                      case "score_1": $stats_field = "flags_captured";      break;
                      case "score_2": $stats_field = "flags_saved";         break;
                      case "score_3": $stats_field = "flag_runners_killed"; break;
                    }
                  }
                  if($stats_row["game_type"] == "Search and Destroy" || $stats_row["game_type"] == "Attack and Defend") {
                    switch($field) {
                      case "score_1": $stats_field = "targets_destroyed";  break;
                      case "score_2": $stats_field = "sd_ad_attack_kills"; break;
                      case "score_3": $stats_field = "sd_ad_defend_kills"; break;
                    }
                  }
                  if($stats_field != "") {
                    if(isset($award_stats[$stats_field])) {
                      $award_stats[$stats_field] += $value;
                    } else {
                      $award_stats[$stats_field] = $value;
                    }
                  }
                }
              }
            }

            $awards = array();
            $award  = array();
            $count  = 0;
            while($award_row = DBFetchArray($award_query)) {
              if(isset($award_stats[$award_row["field"]])) {
                if($award_stats[$award_row["field"]] >= $award_row["value"]) {
		          $awrn = array($award_row["name"]);
		          foreach($awrn as $awval) {
		            $ga_sql = DBQuery("SELECT * FROM $playerawards_table WHERE player = '$id' AND award = '".addslashes($awval)."'");
			        $chkrow = DBNumRows($ga_sql);
			        if(!($chkrow)) {
			          $ga_row = DBFetchArray($ga_sql);
		              $ga_query = DBQuery("INSERT INTO $playerawards_table (player, award, date) VALUES ('$id', '".addslashes($award_row["name"])."', '".$game["date"]."');");
					  $gt_sql = DBQuery("SELECT COUNT(*) AS num FROM $playerawards_table WHERE player = '$id' GROUP BY player");
					  $gt_row = DBFetchArray($gt_sql);
					  $up_query = DBQuery("UPDATE $players_table SET awards = awards + 1 WHERE id = '$id'");
		            } else {
					}
		          }
                }
              }
            }
			$wpnstats_query = DBQuery("SELECT $stats_table.id, $stats_table.player AS player, $weaponstats_table.id, 
                           $weaponstats_table.record, $weapons_table.name, $weaponstats_table.weapon AS weapon, 
						   SUM( $weaponstats_table.kills ) AS kills,
						   SUM( $weaponstats_table.kills ) *100 / SUM( $weaponstats_table.shots ) AS accuracy
                           FROM $stats_table, $weaponstats_table, $weapons_table
                           WHERE $weaponstats_table.record = $stats_table.id
                           AND $stats_table.player = '$id'
                           AND $weapons_table.id = $weaponstats_table.weapon
                           GROUP BY player, weapon"); 
						   
            $wpnaward_query = DBQuery("SELECT * FROM $awards_table WHERE type = 'W'"); 
			$wpnaward_stats = array();
            $wpnstats_row   = array();
            while($wpnstats_row = DBFetchArray($wpnstats_query)) {
              foreach($wpnstats_row as $field => $value) {
                if(!is_numeric($field)) {
                $wpnstats_field = "";
                if($wpnstats_row["name"] == "CAR15") {
                  switch($field) {
                    case "kills": $wpnstats_field = "car15_kills";      break;
			        case "accuracy": $wpnstats_field = "car15_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "G36") {
                  switch($field) {
                    case "kills": $wpnstats_field = "g36_kills";      break;
			        case "accuracy": $wpnstats_field = "g36_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "G3") {
                  switch($field) {
                    case "kills": $wpnstats_field = "g3_kills";      break;
			        case "accuracy": $wpnstats_field = "g3_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M60") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m60_kills";      break;
			        case "accuracy": $wpnstats_field = "m60_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "CAR15/M203") {
                  switch($field) {
                    case "kills": $wpnstats_field = "car15203_kills";      break;
			        case "accuracy": $wpnstats_field = "car15203_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M16") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m16_kills";      break;
			        case "accuracy": $wpnstats_field = "m16_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M16/M203") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m16203_kills";      break;
			        case "accuracy": $wpnstats_field = "m16203_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "MP5") {
                  switch($field) {
                    case "kills": $wpnstats_field = "mp5_kills";      break;
			        case "accuracy": $wpnstats_field = "mp5_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M249 SAW") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m249saw_kills";      break;
			        case "accuracy": $wpnstats_field = "m249saw_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M240") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m240_kills";      break;
			        case "accuracy": $wpnstats_field = "m240_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "CAR15/M203 - Grenade") {
                  switch($field) {
                    case "kills": $wpnstats_field = "car15203_grenade_kills";      break;
			        case "accuracy": $wpnstats_field = "car15203_grenade_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M16/M203 - Grenade") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m16203_grenade_kills";      break;
			        case "accuracy": $wpnstats_field = "m16203_grenade_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "Colt .45") {
                  switch($field) {
                    case "kills": $wpnstats_field = "colt_kills";      break;
			        case "accuracy": $wpnstats_field = "colt_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M9 Beretta") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m9_kills";      break;
			        case "accuracy": $wpnstats_field = "m9_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M21") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m21_kills";      break;
			        case "accuracy": $wpnstats_field = "m21_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "PSG1") {
                  switch($field) {
                    case "kills": $wpnstats_field = "psg1_kills";      break;
			        case "accuracy": $wpnstats_field = "psg1_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "M24") {
                  switch($field) {
                    case "kills": $wpnstats_field = "m24_kills";      break;
			        case "accuracy": $wpnstats_field = "m24_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "MCRT .300 Tactical") {
                  switch($field) {
                    case "kills": $wpnstats_field = "mcrt_kills";      break;
			        case "accuracy": $wpnstats_field = "mcrt_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "Barrett .50 Cal") {
                  switch($field) {
                    case "kills": $wpnstats_field = "barrett_kills";      break;
			        case "accuracy": $wpnstats_field = "barrett_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "Remington Shotgun") {
                  switch($field) {
                    case "kills": $wpnstats_field = "shotgun_kills";      break;
			        case "accuracy": $wpnstats_field = "shotgun_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "Frag Grenade") {
                  switch($field) {
                    case "kills": $wpnstats_field = "grenade_kills";      break;
			        case "accuracy": $wpnstats_field = "grenade_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "AT4") {
                  switch($field) {
                    case "kills": $wpnstats_field = "at4_kills";      break;
			        case "accuracy": $wpnstats_field = "at4_accuracy";      break;
                  }
                }
		        if($wpnstats_row["name"] == "Claymore") {
                  switch($field) {
                    case "kills": $wpnstats_field = "claymore_kills";      break;
			        case "accuracy": $wpnstats_field = "claymore_accuracy";      break;
                  }
                }
                if($wpnstats_field != "") {
                  if(isset($wpnaward_stats[$wpnstats_field])) {
                    $wpnaward_stats[$wpnstats_field] += $value;
                  } else {
                    $wpnaward_stats[$wpnstats_field] = $value;
                  }
                }
              }
            }
          }
          $wpnawards = array();
          $wpnaward  = array();
          $count  = 0;
          while($wpnaward_row = DBFetchArray($wpnaward_query)) {
            if(isset($wpnaward_stats[$wpnaward_row["field"]])) {
              if($wpnaward_stats[$wpnaward_row["field"]] >= $wpnaward_row["value"]) {
                $wawrn = array($wpnaward_row["name"]);
		        foreach($wawrn as $wawval) {
		          $ga_sql = DBQuery("SELECT * FROM $playerawards_table WHERE player = '$id' AND award = '".addslashes($wawval)."'");
			      $chkrow = DBNumRows($ga_sql);
			      if(!($chkrow)) {
			        $ga_row = DBFetchArray($ga_sql);
		            $gadate = date("Y-m-d H:i:s");
		            $ga_query = DBQuery("INSERT INTO $playerawards_table (player, award, date) VALUES ('$id', '".addslashes($wpnaward_row["name"])."', '".$game["date"]."');");
			        $gt_sql = DBQuery("SELECT COUNT(*) AS num FROM $playerawards_table WHERE player = '$id' GROUP BY player");
				    $gt_row = DBFetchArray($gt_sql);
				    $up_query = DBQuery("UPDATE $players_table SET wpn_awards = wpn_awards + 1 WHERE id = '$id'");
		          } else {
				  }
				}
		      }
			}
	      }
        }
	  }
    }
    
    if($status == "player_stats") {
      $tmp = array();
      $tmp = explode(" ", $line);
      foreach($tmp as $field => $value) {
        if(isset($player_data[$field])) {
          $player[$player_data[$field]] = $tmp[$field];
        }
      }
      foreach($player_data as $field_name) {
        if(!isset($player[$field_name])) {
          $player[$field_name] = 0;
        }
      }

      $player["name"]   = base64_encode($player["name"]);
      $player["name"]   = GetPlayerAlias($player["name"]);
      $player_id        = GetPlayer($player["name"]);
	  GetNcVal($bmt);
      $player_record_id = GetPlayerRecord($player_id, $game["gametype"], $server_id);
	  $player_game_id   = GetPlayerGame($player_id, $game_id, $player["team"]);
      $map_record_id    = GetMapRecord($map_id, $player_record_id);
	  $player_mrecord_id = GetMonthPlayerRecord($player_id, $game["gametype"], $server_id, $game["date"]);
      $map_mrecord_id    = GetMonthMapRecord($map_id, $player_record_id);
	  $history_name     = GetServerHistory($server_id, $player["name"], $game["date"]);
	  storePlayerIp($player_id, $player["ip"], $game["date"]);
	  
      $player["score1"]    = 0;
      $player["score2"]    = 0;
      $player["score3"]    = 0;
      $player["num_games"] = 0;
      $player["num_wins"]  = 0;
	  $newPlayerID         = 0;
	
      switch($game["gametype"]) {
        case "Deathmatch":            {$player["score1"] = $player["experience"];                                                                                                   } break;
        case "Team Deathmatch":       {$player["score1"] = $player["experience"];                                                                                                   } break;
        case "Team King of the Hill": {$player["score1"] = $player["zonetime"]; $player["score2"] = $player["tkothattackkills"]; $player["score3"] = $player["tkothdefendkills"]; } break;
        case "Capture the Flag":      {$player["score1"] = $player["flags"];      $player["score2"] = $player["flagsaves"];        $player["score3"] = $player["flagcarrierkills"]; } break; 
        case "Flagball":              {$player["score1"] = $player["flags"];                                                       $player["score3"] = $player["flagcarrierkills"]; } break; 
        case "Search and Destroy":    {$player["score1"] = $player["targets"];    $player["score2"] = $player["sdadattackkills"];  $player["score3"] = $player["sdaddefendkills"];  } break; 
        case "Attack and Defend":     {$player["score1"] = $player["targets"];    $player["score2"] = $player["sdadattackkills"];  $player["score3"] = $player["sdaddefendkills"];  } break; 
      }
       
	  $plyrRes = '0';
	          
      if($game["gametype"] != "Deathmatch") {
          $player["num_games"] = 1;
          if($game["winner"] == $player["team"] && $player["team"] != 0) {
            $player["num_wins"] = 1;
			$plyrRes = '1';
          } elseif($game["winner"] == 0) {
            $player["num_draws"] = 1;
			$plyrRes = '2';
		  }
      } else {
	        $player["num_games"] = 1;
		    DBQuery("UPDATE $players_table SET dm_value = dm_value + '".$player["experience"]."' WHERE id='".$player_id."'");
	  } 
	  
      DBQuery("UPDATE $stats_table SET
                 time         = time         + '".$player["timer"]."'       ,
                 kills        = kills        + '".$player["kills"]."'       ,
                 deaths       = deaths       + '".$player["deaths"]."'      ,
                 suicides     = suicides     + '".$player["suicides"]."'    ,
                 murders      = murders      + '".$player["murders"]."'     ,
                 headshots    = headshots    + '".$player["headshots"]."'   ,
                 knifings     = knifings     + '".$player["knifings"]."'    ,
                 medic_saves  = medic_saves  + '".$player["medsaves"]."'    ,
                 revives      = revives      + '".$player["revives"]."'     ,
                 pspattempts  = pspattempts  + '".$player["pspattempts"]."' ,
                 psptakeovers = psptakeovers + '".$player["psptakeovers"]."',
                 doublekills  = doublekills  + '".$player["doublekills"]."' ,
		             score_1      = score_1      + '".$player["score1"]."'      ,
		             score_2      = score_2      + '".$player["score2"]."'      ,
		             score_3      = score_3      + '".$player["score3"]."'      ,
		             games        = games        + '".$player["num_games"]."'   ,
		             wins         = wins         + '".$player["num_wins"]."'    , 
					 draws        = draws        + '".$player["num_draws"]."'   ,
		             last_played  = '".$game["date"]."'
               WHERE id='".$player_record_id."'");
	  
	  	DBQuery("UPDATE $stats_m_table SET
                   time         = time         + '".$player["timer"]."'       ,
                   kills        = kills        + '".$player["kills"]."'       ,
                   deaths       = deaths       + '".$player["deaths"]."'      ,
                   suicides     = suicides     + '".$player["suicides"]."'    ,
                   murders      = murders      + '".$player["murders"]."'     ,
                   headshots    = headshots    + '".$player["headshots"]."'   ,
                   knifings     = knifings     + '".$player["knifings"]."'    ,
                   medic_saves  = medic_saves  + '".$player["medsaves"]."'    ,
                   revives      = revives      + '".$player["revives"]."'     ,
                   pspattempts  = pspattempts  + '".$player["pspattempts"]."' ,
                   psptakeovers = psptakeovers + '".$player["psptakeovers"]."',
                   doublekills  = doublekills  + '".$player["doublekills"]."' ,
		           score_1      = score_1      + '".$player["score1"]."'      ,
		           score_2      = score_2      + '".$player["score2"]."'      ,
	               score_3      = score_3      + '".$player["score3"]."'      ,
                   games        = games        + '".$player["num_games"]."'   ,
		           wins         = wins         + '".$player["num_wins"]."'    ,
				   draws        = draws        + '".$player["num_draws"]."'   ,
		           last_played  = '".$game["date"]."'
                 WHERE id='".$player_mrecord_id."'");

	  if($player_game_id == '-1') {
	  	$player["statString"] = $player["kills"]."_".$player["deaths"]."_".$player["murders"]."_".
				                $player["suicides"]."_".$player["knifings"]."_".$player["headshots"]."_".
                                $player["medsaves"]."_".$player["revives"]."_".$player["pspattempts"]."_".
                                $player["psptakeovers"]."_".$player["doublekills"]."_".$player["score1"]."_".
		                        $player["score2"]."_".$player["score3"]."_".$player["timer"];
		DBQuery("INSERT INTO $playergames_table (game_id, player, playerip, experience, stats, team, wpns) VALUES(
		             '".$game_id."', '".$player_id."', '".$player["ip"]."', '".$player["experience"]."', 
					 '".$player["statString"]."', '".$player["team"]."', '')");
		$npSql = DBQuery("SELECT id FROM $playergames_table WHERE game_id = '".$game_id."' 
		                                                 AND player = '".$player_id."' AND team = '".$player["team"]."'");
		$npRow = DBFetchArray($npSql);
		$player_game_id = $npRow["id"];
	  } else {
	  	$pSql = DBQuery("SELECT * FROM $playergames_table WHERE id = '".$player_game_id."'");
		$pRow = DBFetchArray($pSql);
		$sSpl = explode("_", $pRow["stats"]);
		$player["statString"] = $player["kills"] + $sSpl[0];
		$player["statString"] .= "_".($player["deaths"] + $sSpl[1]);
		$player["statString"] .= "_".($player["murders"] + $sSpl[2]);
		$player["statString"] .= "_".($player["suicides"] + $sSpl[3]);
		$player["statString"] .= "_".($player["knifings"] + $sSpl[4]);
		$player["statString"] .= "_".($player["headshots"] + $sSpl[5]);
		$player["statString"] .= "_".($player["medsaves"] + $sSpl[6]);
		$player["statString"] .= "_".($player["revives"] + $sSpl[7]);
		$player["statString"] .= "_".($player["pspattempts"] + $sSpl[8]);
		$player["statString"] .= "_".($player["psptakeovers"] + $sSpl[9]);
		$player["statString"] .= "_".($player["doublekills"] + $sSpl[10]);
		$player["statString"] .= "_".($player["score1"] + $sSpl[11]);
		$player["statString"] .= "_".($player["score2"] + $sSpl[12]);
		$player["statString"] .= "_".($player["score3"] + $sSpl[13]);
		$player["statString"] .= "_".($player["timer"] + $sSpl[14]);
		DBQuery("UPDATE $playergames_table SET 
		             experience = experience + '".$player["experience"]."' , 
					 stats      = '".$player["statString"]."'
				  WHERE id='".$player_game_id."'");
	  }
               
      $rating  = 0;
      $rating += $points["kills"]       * $player["kills"];
      $rating += $points["deaths"]      * $player["deaths"];
      $rating += $points["suicides"]    * $player["suicides"];
      $rating += $points["murders"]     * $player["murders"];
      $rating += $points["headshots"]   * $player["headshots"];
      $rating += $points["knifings"]    * $player["knifings"];
      $rating += $points["medic_saves"] * $player["medsaves"];
      $rating += $points["revives"]     * $player["revives"];
      $rating += $points["pspattempts"] * $player["pspattempts"];
      $rating += $points["psptakeovers"]* $player["psptakeovers"];
      $rating += $points["doublekills"] * $player["doublekills"];
      $rating += $points["wins"]        * $player["num_wins"];
      if($game["gametype"] == "Team King of the Hill") {
        $rating += $points["zone"]       * $player["score1"] / 60;
        $rating += $points["zoneattack"] * $player["score2"];
        $rating += $points["zonedefend"] * $player["score3"];
      }
      if($game["gametype"] == "Capture the Flag" || $game["gametype"] == "Flagball") {
        $rating += $points["flags"]       * $player["score1"];
        $rating += $points["saves"]       * $player["score2"];
        $rating += $points["runnerkills"] * $player["score3"];
      }
      if($game["gametype"] == "Attack and Defend" || $game["gametype"] == "Search and Destroy") {
        $rating += $points["targets"]    * $player["score1"];
        $rating += $points["sdadattack"] * $player["score2"];
        $rating += $points["sdaddefend"] * $player["score3"];
      }
              
      DBQuery("UPDATE $players_table SET
                 rating   = rating   + '".$rating."', 
				 m_rating = m_rating + '".$rating."'
               WHERE id='".$player_id."'");
	                   
      DBQuery("UPDATE $mapstats_table SET
                 time        = time     + '".$player["timer"]."'    ,
                 kills       = kills    + '".$player["kills"]."'    ,
                 deaths      = deaths   + '".$player["deaths"]."'   ,
                 score       = score    + '".$player["score1"]."'    
               WHERE id='".$map_record_id."'");
	   
	   DBQuery("UPDATE $mapstats_m_table SET
                   time        = time     + '".$player["timer"]."'    ,
                   kills       = kills    + '".$player["kills"]."'    ,
                   deaths      = deaths   + '".$player["deaths"]."'   ,
                   score       = score    + '".$player["score1"]."'    
                 WHERE id='".$map_mrecord_id."'");

    }
    
    if($status == "weapon") {
      $tmp = array();
      $tmp = explode(" ", $line);
      foreach($tmp as $field => $value) {
        if(isset($weapon_data[$field])) {
          $weapon[$weapon_data[$field]] = $tmp[$field];
        }
      }
      foreach($weapon_data as $field_name) {
        if(!isset($weapon[$field_name])) {
          $weapon[$field_name] = 0;
        }
      }

	  $wpnStr = "";

      if(isset($game["mod"], $weapon["code"]) && isset($wpn_name[$game["mod"]][$weapon["code"]])) {
        $weapon["name"]   = $wpn_name[$game["mod"]][$weapon["code"]];
        $weapon_id        = GetWeapon($weapon["name"]);
        $weapon_record_id = GetWeaponRecord($weapon_id, $player_record_id);
		$weapon_mrecord_id = GetMonthWeaponRecord($weapon_id, $player_record_id);
        $wpn_rating       = $wpn_points[$weapon["name"]] * $weapon["kills"];
        DBQuery("UPDATE $weaponstats_table SET 
                   time     = time    + '".$weapon["timer"]."' ,
                   kills    = kills   + '".$weapon["kills"]."' ,
                   shots    = shots   + '".$weapon["shots"]."' 
                 WHERE id='".$weapon_record_id."'");
		
		DBQuery("UPDATE $weaponstats_m_table SET 
                   time     = time    + '".$weapon["timer"]."' ,
                   kills    = kills   + '".$weapon["kills"]."' ,
                   shots    = shots   + '".$weapon["shots"]."' 
                 WHERE id='".$weapon_mrecord_id."'");
		
		$wSql = DBQuery("SELECT wpns FROM $playergames_table WHERE id = '".$player_game_id."'");
		$wRow = DBFetchArray($wSql);		
		$wpSpl = explode(chr(10), $wRow["wpns"]);
		$fndWpn = false;
		foreach($wpSpl as $dbStr) {
			$strSpl = explode("_", $dbStr);
			if($strSpl[0] == $weapon["name"]) {
				if(strlen($dbStr) > 0) {
					$wpnStr .= $weapon["name"]."_".($weapon["timer"]+$strSpl[1])."_".($weapon["kills"]+$strSpl[2]).
							 "_".($weapon["shots"]+$strSpl[3]).chr(10);
					$fndWpn = true;
				}
			} else {
				if(strlen($dbStr) > 0) {
					$wpnStr .= $dbStr.chr(10);
				}
			}
		}
		
		if(!$fndWpn) $wpnStr .= $weapon["name"]."_".$weapon["timer"]."_".$weapon["kills"]."_".$weapon["shots"].chr(10);
		
		DBQuery("UPDATE $playergames_table SET 
					 wpns         = '".$wpnStr."' 
				 WHERE id='".$player_game_id."'");
		
		if($weapon["name"] == "M21" || $weapon["name"] == "M24" || $weapon["name"] == "MCRT .300 Tactical" 
		   || $weapon["name"] == "Barrett .50 Cal" || $weapon["name"] == "PSG1" || $weapon["name"] == "SIG 550 Sniper" 
		   || $weapon["name"] == "M-20" || $weapon["name"] == "Walther 2000") {
		  DBQuery("UPDATE $stats_table SET 
		             sniper_kills = sniper_kills + '".$weapon["kills"]."' 
				   WHERE id='".$player_record_id."'");
		  DBQuery("UPDATE $stats_m_table SET 
		             sniper_kills = sniper_kills + '".$weapon["kills"]."' 
				   WHERE id='".$player_mrecord_id."'");
		}
      
        DBQuery("UPDATE $players_table SET
                   rating = rating   + '".$wpn_rating."', 
				 m_rating = m_rating + '".$wpn_rating."' 
                 WHERE id='".$player_id."'");
	  } 
    }
  } 
  $fdate = '';
  if($game["date"] !== "") {
    $timestamp = strtotime($game["date"]);
    if($timestamp !== false) {
      $fdate = date("Y-m-d", $timestamp);
    }
  }
  if($fdate !== '') {
    $sql = DBQuery("SELECT COUNT(name) as num, date FROM $serverhistory_table WHERE serverid = '$server_id' AND players = '' AND date < '$fdate' GROUP BY date");
    while($row = DBFetchArray($sql)) {
      if($row["num"] > 0) { 
	    DBQuery("INSERT INTO $serverhistory_table (serverid, players, name, date) VALUES ('$server_id', '".$row["num"]."', '', '".$row["date"]."')");
        DBQuery("DELETE FROM $serverhistory_table WHERE serverid = '$server_id' AND players = '' AND date < '$fdate'");
      }
    }
  }
  
  DBQuery("UPDATE $servers_table SET age = '0' WHERE id = '$server_id'");
  if($game["gametype"] == "Deathmatch") {
    if($game["numplayers"] > 0) {
      $sql = DBQuery("SELECT * FROM $players_table ORDER BY dm_value DESC LIMIT 1");
      $row = DBFetchArray($sql);
      DBQuery("UPDATE $stats_table SET wins = wins + 1 WHERE player='".$row["id"]."' 
	           AND game_type LIKE 'Deathmatch' AND server = '".$server_id."'");
      DBQuery("UPDATE $stats_m_table SET wins = wins + 1 WHERE player='".$row["id"]."' 
	           AND game_type LIKE 'Deathmatch' AND server = '".$server_id."'");
	  DBQuery("UPDATE $playergames_table SET result = 1 WHERE player='".$row["id"]."' 
	           AND date_played = '".$game["date"]."'");
	  DBQuery("UPDATE $players_table SET dm_value = 0");
	}
  }
	
  $log = "Stats imported ".$game["date"];
  return $log;
}

function StatusUpdate($data, $serverid) {

  global $aliases_table, $awards_table, $log_table, $maps_table, $mapstats_table, $playerips_table, 
         $players_table, $playerawards_table, $ranks_table, $servers_table, $squads_table,
         $stats_table, $weapons_table, $weaponstats_table, $wpn_points,
         $points, $percenttowin, $gametypes, $wpn_name;
  
  $log = "";
  
  $ip       = "0.0.0.0";
  $thetime  = time();

  $query = DBQuery("SELECT * FROM $servers_table WHERE serverid='$serverid'");
  if(DBNumRows($query) == 0) {
    $log .= "Invalid server id code ($serverid)";
    return $log;
  }
  
  $data_lines = explode("\n", $data);
  $line_count = count($data_lines);
  
  $status = "blank";
  
  $game        = array(
    "timer"     => "0",
    "date"      => "",
    "gametype"  => "",
    "dedicated" => "",
    "servername"=> "",
    "mapname"   => "",
    "maxplayers"=> "0",
    "numplayers"=> "0",
    "mod"       => 0
  );
  $player      = array();
  
  $game_data = array(
    0 => "timer",
    1 => "date",
    2 => "gametype",
    3 => "dedicated",
    4 => "servername",
    5 => "mapname",
    6 => "maxplayers",
    7 => "numplayers",
    8 => "mod"
  );                             

  $player_data = array(
    0  => "timer",
    1  => "team",
    2  => "weapon"
  );
                              
  $num_players    = 0;
  $player_names   = "";
  $player_weapons = "";
  $player_teams   = "";

  $skip        = false;
  $skip_to     = "";
    
  for($i = 0; $i < $line_count; $i++) {

    $line = $data_lines[$i];
    $tag  = explode(" ", trim($line));
    $tag  = $tag[0];
    
    $offset = 0;
    
    switch($tag) {
      case "ServerID":    {$status = "server_id";    $offset = 8; } break;
      case "Game":        {$status = "game";         $offset = 5; } break;
      case "Player":      {$status = "player";       $offset = 8; } break;
      case "PlayerStats": {$status = "player_stats"; $offset = 14;} break;
      case "End":         {$status = "end";          $offset = 0; } break;
      default:            {$status = "blank";        $offset = 0; }
    }
    
    if($skip && $status != $skip_to) continue;
    
    $skip    = false;
    $skip_to = "";
    
    $line = trim(substr($line, $offset));

    if($status == "blank") {}
    
    if($status == "server_id") {}

    if($status == "game") {
      $tmp = array();
      $tmp = explode("__&__", $line);
      foreach($tmp as $field => $value) {
        if(isset($game_data[$field])) {
          $game[$game_data[$field]] = $tmp[$field];
        }
      }
	  if(!isset($game["mod"]) || $game["mod"] === "") {
	    $game["mod"] = 0;
	  }
	  
      $game["timer"] = str_replace("Game ", "", $game["timer"]);
      if($game["dedicated"] == "1") $game["dedicated"] = "Yes"; else $game["dedicated"] = "No";
      if(isset($gametypes[$game["gametype"]])) {
        $game["gametype"] = $gametypes[$game["gametype"]];
      } else {
        $game["gametype"] = "";
      }
      $game["mapname"] =str_replace("??", "", $game["mapname"]);
    }
    
    if($status == "player") {
      $num_players++;
      $player["name"] = $line;
    }
    
    if($status == "player_stats") {
      $tmp = array();
      $tmp = explode(" ", $line);
      foreach($tmp as $field => $value) {
        if(isset($player_data[$field])) {
          $player[$player_data[$field]] = $tmp[$field];
        }
      }
      if(!isset($player["name"])) {
        $player["name"] = "";
      }
      if(!isset($player["team"])) {
        $player["team"] = "";
      }
      if(!isset($player["weapon"])) {
        $player["weapon"] = "";
      }
      if($game["gametype"] == "Deathmatch") $player["team"] = "None";
      if($player["team"] == "1") $player["team"] = "Blue";
      if($player["team"] == "2") $player["team"] = "Red";
      if($num_players == 1) $separator = ""; else $separator = "\n";
      $player_names   .= $separator.base64_encode($player["name"]);
      $player_teams   .= $separator.$player["team"];
      $game_mod = isset($game["mod"]) ? (int)$game["mod"] : 0;
      if(isset($wpn_name[$game_mod][$player["weapon"]])) {
        $player_weapons .= $separator.$wpn_name[$game_mod][$player["weapon"]];
      } else {
        $player_weapons .= $separator;
      }
    }
    
	$gameName = "";
	$game_mod = isset($game["mod"]) ? (int)$game["mod"] : 0;
	switch($game_mod) {
		case 7: $gameName = "Black Hawk Down"; break;
		case 8: $gameName = "BHD: Team Sabre"; break;
		case 9: $gameName = "BHD: Black Operations"; break;
		case 10: $gameName = "BHD: War on Terror"; break;
		case 16: $gameName = "BHD: Shock \'n Awe"; break;
	}
	
    if($status == "end") {
      DBQuery("UPDATE $servers_table SET 
                 ip             = '$ip'                                    ,
                 map_name       = '".base64_encode($game["mapname"])."'    ,
                 server_name    = '".base64_encode($game["servername"])."' ,
                 dedicated      = '".$game["dedicated"]."'                 ,
                 game_type      = '".$game["gametype"]."'                  ,
                 max_players    = '".$game["maxplayers"]."'                ,
                 time           = '$thetime'                               ,
                 game           = '$gameName'                              ,
                 num_players    = '$num_players'                           ,
                 player_names   = '$player_names'                          ,
                 player_teams   = '$player_teams'                          ,
                 player_weapons = '$player_weapons'                        ,
				 age            = '".$game["timer"]."' 
               WHERE serverid='$serverid'");
    }
    
  }

  $log .= "Status updated successfully";
  return $log;
 
}

function StatusReport($data, $serverid) {

    global $aliases_table, $awards_table, $log_table, $maps_table, $mapstats_table, $playerips_table, 
           $players_table, $playerawards_table, $ranks_table, $servers_table, $squads_table, $stats_table, 
		   $weapons_table, $weaponstats_table, $wpn_points, $points, $percenttowin, $gametypes, $wpn_name;
	
	$player_data = array(
		0  => "suicides"        ,
		1  => "murders"         ,
		2  => "kills"           ,
		3  => "deaths"          ,
		4  => "zone_time"        ,
		5  => "flags_captured"           ,
		6  => "flags_saved"       ,
		7  => "targets_destroyed"         ,
		8  => "revives"         ,
		9  => "medic_saves"        ,
		10 => "psp_attempts"     ,
		11 => "psp_takeovers"    ,
		12 => "flag_runners_killed",
		13 => "double_kills"     ,
		14 => "headshots"       ,
		15 => "knifings"        ,
		16 => "sniperkills"     ,
		17 => "tkoth_zone_attack_kills",
		18 => "tkoth_zone_defend_kills",
		19 => "sd_ad_defend_kills" ,
		20 => "sdadpolicekills" ,
		21 => "sd_ad_attack_kills" ,
		22 => "sdadsecurekills" ,
		23 => "shotsperkill"    ,
		24 => "experience"      ,
		25 => "team"            ,
		26 => "playedtillend"   ,
		27 => "time_played"
	);

	$player_aliases = array(
		"flags_captured" => "flags",
		"flags_saved" => "flagsaves",
		"targets_destroyed" => "targets",
		"medic_saves" => "medsaves",
		"psp_attempts" => "pspattempts",
		"psp_takeovers" => "psptakeovers",
		"flag_runners_killed" => "flagcarrierkills",
		"double_kills" => "doublekills",
		"tkoth_zone_attack_kills" => "tkothattackkills",
		"tkoth_zone_defend_kills" => "tkothdefendkills",
		"sd_ad_attack_kills" => "sdadattackkills",
		"sd_ad_defend_kills" => "sdaddefendkills",
		"time_played" => "timer"
	);
	$player_defaults = array_fill_keys(array_values($player_data), 0);
	foreach($player_aliases as $from => $to) {
		$player_defaults[$to] = 0;
	}
	$player_defaults["num_wins"] = 0;
	$player_defaults["score1"] = 0;
	$player_defaults["score2"] = 0;
	$player_defaults["score3"] = 0;
	$player_defaults["games_completed"] = 0;
	$player_defaults["team_games_won"] = 0;
	$player = $player_defaults;

	$lines = array();

	$lines = explode("\n", $data);
	for($i = 0; $i < sizeof($lines); $i++) {
		$lines[$i] = trim($lines[$i]);
	}
	
	$awardList = "";
	
	$gametype = '';
	if(isset($lines[0]) && isset($gametypes[$lines[0]])) {
		$gametype = $gametypes[$lines[0]];
	}

	$plyrID = '0';
	$plyrNameRaw = "";

	for($i = 1; $i <  sizeof($lines); $i++) {
		if(!empty($lines[$i])) {
			if(substr($lines[$i], 0, 7) == "Player ") {
				$plyrName = "";
				$plyrID   = '0';

				$lines[$i]   = substr($lines[$i], 7);
				$plyrDetails = explode("__&__", $lines[$i]);
				$plyrNameRaw = $plyrDetails[0] ?? "";
				$plyrName    = base64_encode($plyrNameRaw);
				$player      = $player_defaults;
				$sql         = DBQuery("SELECT * FROM $players_table WHERE name = '".$plyrName."'");
				$row         = DBFetchArray($sql);
				$plyrID      = $row ? $row["id"] : '0';
			}
			
			if(substr($lines[$i], 0, 7) == "PlayerS" && $plyrID > '0') {
				$lines[$i] = substr($lines[$i], 12);
				$tmp = array();
			    $tmp = explode(" ", $lines[$i]);
			    foreach($tmp as $field => $value) {
					if(isset($player_data[$field])) {
						$player_key = $player_data[$field];
						$player[$player_key] = $tmp[$field];
						if(isset($player_aliases[$player_key])) {
							$player[$player_aliases[$player_key]] = $tmp[$field];
						}
					}
			    }

				switch($gametype) {
					case "Deathmatch":
					    $player["score1"] = $player["experience"];
					break;
					case "Team Deathmatch":
					    $player["score1"] = $player["experience"];
					break;
					case "Team King of the Hill":
					    $player["score1"] = $player["zone_time"]; 
						$player["score2"] = $player["tkothattackkills"]; 
						$player["score3"] = $player["tkothdefendkills"];
					break;
					case "Capture the Flag":
					    $player["score1"] = $player["flags"];
						$player["score2"] = $player["flagsaves"]; 
						$player["score3"] = $player["flagcarrierkills"];
					break; 
					case "Flagball":
					    $player["score1"] = $player["flags"];
						$player["score3"] = $player["flagcarrierkills"];
					break; 
					case "Search and Destroy":
					    $player["score1"] = $player["targets"];
						$player["score2"] = $player["sdadattackkills"];
						$player["score3"] = $player["sdaddefendkills"];
					break; 
					case "Attack and Defend":
					    $player["score1"] = $player["targets"];
						$player["score2"] = $player["sdadattackkills"];
						$player["score3"] = $player["sdaddefendkills"]; 
					break; 
				  }
				
				$rating  = 0;
			    $rating += $points["kills"]       * $player["kills"];
			    $rating += $points["deaths"]      * $player["deaths"];
			    $rating += $points["suicides"]    * $player["suicides"];
			    $rating += $points["murders"]     * $player["murders"];
			    $rating += $points["headshots"]   * $player["headshots"];
			    $rating += $points["knifings"]    * $player["knifings"];
			    $rating += $points["medic_saves"] * $player["medsaves"];
			    $rating += $points["revives"]     * $player["revives"];
			    $rating += $points["pspattempts"] * $player["pspattempts"];
			    $rating += $points["psptakeovers"]* $player["psptakeovers"];
			    $rating += $points["doublekills"] * $player["doublekills"];
			    $rating += $points["wins"]        * $player["num_wins"];
			    if($gametype == "Team King of the Hill") {
					$rating += $points["zone"]       * $player["score1"] / 60;
					$rating += $points["zoneattack"] * $player["score2"];
					$rating += $points["zonedefend"] * $player["score3"];
			    }
			    if($gametype == "Capture the Flag" || $gametype == "Flagball") {
					$rating += $points["flags"]       * $player["score1"];
					$rating += $points["saves"]       * $player["score2"];
					$rating += $points["runnerkills"] * $player["score3"];
			    }
			    if($gametype == "Attack and Defend" || $gametype == "Search and Destroy") {
					$rating += $points["targets"]    * $player["score1"];
					$rating += $points["sdadattack"] * $player["score2"];
					$rating += $points["sdaddefend"] * $player["score3"];
			    }
				
				$pnm = base64_encode($plyrNameRaw);
		  		$name = DBQuery("SELECT * FROM $players_table WHERE name = '$pnm'");

				   $n_row = DBFetchArray($name);

					$currentRating = $n_row ? $n_row["rating"] : 0;

					$rSql = DBQuery("SELECT * FROM $ranks_table WHERE rating > '".$currentRating."' LIMIT 0, 1");
					$rNum = DBNumRows($rSql);
					if($rNum > '0') {
						$rRow = DBFetchArray($rSql);
						$ttlRating = $rating + $currentRating;

						if($ttlRating >= $rRow["rating"]) {
							$gr_sql = DBQuery("SELECT * FROM $playerawards_table WHERE player = '$plyrID'
									                       AND award = '".addslashes($rRow["name"])."'");
							$gr_chkrow = DBNumRows($gr_sql);
							if(!($gr_chkrow)) {
								$gr_row = DBFetchArray($gr_sql);
								$gr_query = DBQuery("INSERT INTO $playerawards_table (player, award, date) VALUES ('$plyrID', '".
								addslashes($rRow["name"])."', '".date("Y-m-d H:i:s")."');");
								if($awardList == "") {
									$awardList = $plyrNameRaw." has been Promoted to ".$rRow["name"];
								} else {
									$awardList .= "__&__".$plyrNameRaw." has been Promoted to ".$rRow["name"];
								}
							}
						}
					}

            		$id = $n_row ? $n_row["id"] : 0;
					$award_query = DBQuery("SELECT * FROM $awards_table WHERE type = 'A'");
					$stats_query = DBQuery("SELECT * FROM $stats_table WHERE $stats_table.player='$plyrID'");
					$award_stats = array();
					$stats_row   = array();
					while($stats_row = DBFetchArray($stats_query)) {
					    foreach($stats_row as $field => $value) {
							if(!is_numeric($field)) {
							    $stats_field = "";
							    switch($field) {
									case "kills":        $stats_field = "kills";           break;
									case "deaths":       $stats_field = "deaths";          break;
									case "murders":      $stats_field = "murders";         break;
									case "suicides":     $stats_field = "suicides";        break;
									case "knifings":     $stats_field = "knifings";        break;
									case "headshots":    $stats_field = "headshots";       break;
									case "medic_saves":  $stats_field = "medic_saves";     break;
									case "revives":      $stats_field = "revives";         break;
									case "pspattempts":  $stats_field = "psp_attempts";    break;
									case "psptakeovers": $stats_field = "psp_takeovers";   break;
									case "doublekills":  $stats_field = "double_kills";    break;
									case "time":         $stats_field = "time_played";     break;
									case "games":        $stats_field = "games_completed"; break;
									case "wins":         $stats_field = "team_games_won";  break;
								}
							}
						    if($stats_row["game_type"] == "Team King of the Hill") {
								switch($field) {
								    case "score_1": $stats_field = "zone_time";               break;
								    case "score_2": $stats_field = "tkoth_zone_attack_kills"; break;
								    case "score_3": $stats_field = "tkoth_zone_defend_kills"; break;
								}
						    }
							if($stats_row["game_type"] == "Capture the Flag" || $stats_row["game_type"] == "Flagball") {
								switch($field) {
								    case "score_1": $stats_field = "flags_captured";      break;
								    case "score_2": $stats_field = "flags_saved";         break;
								    case "score_3": $stats_field = "flag_runners_killed"; break;
								}
							}
						    if($stats_row["game_type"] == "Search and Destroy" || 
							   $stats_row["game_type"] == "Attack and Defend") {
								switch($field) {
								    case "score_1": $stats_field = "targets_destroyed";  break;
								    case "score_2": $stats_field = "sd_ad_attack_kills"; break;
								    case "score_3": $stats_field = "sd_ad_defend_kills"; break;
								}
						    }
						    if($stats_field != "") {
								if(isset($award_stats[$stats_field])) {
								    $award_stats[$stats_field] += $value;
									$award_stats[$stats_field] += ($player[$stats_field] ?? 0);
								} else {
								    $award_stats[$stats_field] = $value;
									$award_stats[$stats_field] += ($player[$stats_field] ?? 0);
								}
						    }
						}
					}
					
					$awards = array();
					$award  = array();
					$count  = 0;
					while($award_row = DBFetchArray($award_query)) {
						if(isset($award_stats[$award_row["field"]])) {
							if($award_stats[$award_row["field"]] >= $award_row["value"]) {
								$awrn = array($award_row["name"]);
								foreach($awrn as $awval) {
									$ga_sql = DBQuery("SELECT * FROM $playerawards_table WHERE player = '$plyrID' 
									                       AND award = '".addslashes($awval)."'");
									$chkrow = DBNumRows($ga_sql);
									if(!($chkrow)) {
										$ga_row = DBFetchArray($ga_sql);
										$ga_query = DBQuery("INSERT INTO $playerawards_table (player, award, date) VALUES ('$plyrID', '".
										addslashes($award_row["name"])."', '".date("Y-m-d H:i:s")."');");
										if($awardList == "") {
											$awardList = $plyrNameRaw." has obtained ".$award_row["name"];
										} else {
											$awardList .= "__&__".$plyrNameRaw." has obtained ".$award_row["name"];
										}
										$gt_sql = DBQuery("SELECT COUNT(*) AS num FROM $playerawards_table WHERE player = 
														  '$plyrID' GROUP BY player");
										$gt_row = DBFetchArray($gt_sql);
										$up_query = DBQuery("UPDATE $players_table SET awards = awards + 1 WHERE id = '$plyrID'");
									}
							    }
							}
						}
					}
				
		    }
	    }
	}
	
	echo $awardList;
}
?>

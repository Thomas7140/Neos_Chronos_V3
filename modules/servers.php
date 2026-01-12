
<?php

class ServerUtils {
  public static function convertTilde(string $str) : string {
    $arr = array(
      '~0' => '<FONT COLOR="#FFFFFF">',
      '~1' => '<FONT COLOR="#00FF00">',
      '~2' => '<FONT COLOR="#FF0000">',
      '~3' => '<FONT COLOR="#008000">',
      '~4' => '<FONT COLOR="#FFFF00">',
      '~5' => '<FONT COLOR="#00FFFF">',
      '~6' => '<FONT COLOR="#808080">',
      '~7' => '<FONT COLOR="#008080">',
      '~8' => '<FONT COLOR="#800000">',
      '~9' => '<FONT COLOR="#80FFFF">', // #87CEEB
    );
    return str_ireplace(array_values($arr), array_keys($arr), $str);
  }

  public static function colorTilde(string $str) : string {
    $arr = array(
      '~0' => '<FONT COLOR="#FFFFFF">',
      '~1' => '<FONT COLOR="#00FF00">',
      '~2' => '<FONT COLOR="#FF0000">',
      '~3' => '<FONT COLOR="#008000">',
      '~4' => '<FONT COLOR="#FFFF00">',
      '~5' => '<FONT COLOR="#00FFFF">',
      '~6' => '<FONT COLOR="#808080">',
      '~7' => '<FONT COLOR="#008080">',
      '~8' => '<FONT COLOR="#800000">',
      '~9' => '<FONT COLOR="#80FFFF">', // #87CEEB
    );
    $str = str_replace(array_keys($arr), array_values($arr), $str);
    return $str . (str_repeat("</FONT>", substr_count($str, "<FONT ")));
  }

  public static function removeTilde(string $str) : string {
    return preg_replace('/~([0-9])/', '', $str);
  }
}

$conditions = "";
if(isset($id) && $id != "") $conditions = "WHERE id='$id'";

$query = DBQuery("SELECT * FROM $servers_table $conditions ORDER BY name ASC");
$stats = DBQuery("SELECT COUNT(player) AS num, SUM(kills) AS kills, MAX(last_played) AS last_active FROM $stats_table WHERE server = '$id' GROUP BY server");
$mstats = DBQuery("SELECT SUM(games) AS games, SUM(time) AS time 
                   FROM $serverstats_table WHERE serverid = '$id'");
$pstats = DBQuery("SELECT COUNT(hosted) AS maps 
                   FROM $maps_table WHERE hosted > '0'");
$gstats = DBQuery("SELECT * FROM $serverstats_table WHERE serverid = '$id'");

if(file_exists($base_folder."templates/servers.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/servers.htm", 1);
  
  $has_server_info = isset($tpl->vars["server_info"]);
  $thetime = time();

  if(!isset($id) || $id == "" || !$has_server_info) {
	if(!$has_server_info && isset($id) && $id != "") {
	  $id = "";
	  $conditions = "";
	  $query = DBQuery("SELECT * FROM $servers_table ORDER BY name ASC");
	}
	
    $servers = array();
    $row     = array();
    $count   = 0;
  
    while($row = DBFetchArray($query)) {
      if($count % 2 == 0){
        $row["class"] = $rowclass1;
      } else {
        $row["class"] = $rowclass2;
      }
	
      $row["name"]        = htmlspecialchars($row["name"]);

      if($thetime - $row["time"] > 120) {
        $row["status"]      = "offline"; 
        $row["server_name"] = "";
        $row["map_name"]    = "";
        $row["dedicated"]   = "";
        $row["players"]     = "";
        $row["game_type"]   = "";
        $row["game"]        = "";
		$row["sstatus"]     = "";
        $row["map_thumbnail"]   = "<p class=\"main\">Image N/A</p>";
      } else {
        $map_query = DBQuery("SELECT * FROM $maps_table WHERE name='".$row["map_name"]."'");
        if(DBNumRows($map_query) != 0) {
          $map_row = DBFetchArray($map_query);
          if($map_row["thumbnail"] != "") {
            $row["map_thumbnail"] = "<img src=\"".$base_folder.$map_row["thumbnail"]."\" border=\"0\">";
          } else {
            $row["map_thumbnail"] = "<p class=\"main\">Image N/A</p>";
          }
        } else {
          $row["map_thumbnail"] = "<p class=\"main\">Image N/A</p>";
        }
        $row["status"]      = "online";

        $row["server_name"] = htmlspecialchars(base64_decode($row["server_name"]));
        $row["map_name"]    = htmlspecialchars(base64_decode($row["map_name"]));
        $row["players"]     = $row["num_players"]."/".$row["max_players"];
        $row["game_type"]   = ShortenGameType($row["game_type"]);
        $row["game"]        = ShortenGameName($row["game"]);
      }
	  
      $row["num"]      = $count + 1;
      $servers[$count] = $row;
      $count++;
    }

    $tpl->set_var("mainscript", $mainscript);
    $tpl->set_var("base",       $base_folder);
    $tpl->set_loop("servers",   $servers);
    $tpl->process("template", "main");
    $content = $tpl->process("", "template", 1);
    
  } else {
  
    $row = DBFetchArray($query);
	$srow = DBFetchArray($stats);
	$msrow = DBFetchArray($mstats);
	$psrow = DBFetchArray($pstats);
	foreach($gametypes as $type) {
	  $row["$type"] = '0';
	}
	
	while($grow = DBFetchArray($gstats)){
	  $games = array($grow["games"] => $grow["game_type"]);
	  foreach($games as $index => $value) {
	    $row["$value"] = $index;
	  }
	}

	switch($row["age"] ?? null) {
		case -1:
		  $row["sstatus"] = "Loading Map";
	      $row["age"] = 0;
		  break;
		case -2:
		  $row["sstatus"] = "Waiting for Players";
		  $row["age"] = 0;
		  break;
		case -4:
		  $row["sstatus"] = "Scoring Map";
		  $row["age"] = 0;
		  break;
	    default:
		  $row["sstatus"] = "Hosting Map";
	 }
	
	if(empty($row["age"])){$row["age"] = "00:00:00";} else {$row["age"] = FormatTime($row["age"]);}
	
	$field = array("todays", "yesterdays", "this_week", "last_week", "this_month", "last_month", "this_year", "last_year");
    foreach($field as $value) {
      $row["$value"] = GetLogins($value, $id);
	  if(!($row["$value"])) $row["$value"] = 0;
    }
	
    if(empty($srow["kills"])){$row["kills"] = 0;} else {$row["kills"] = $srow["kills"];}
    if(empty($srow["num"])){$row["total_players"] = 0;} else {$row["total_players"] = $srow["num"];}
    if(empty($srow["last_active"])){$row["last_active"] = 0;} else {$row["last_active"] = $srow["last_active"];}
	if(empty($msrow["games"])){$row["games"] = 0;} else {$row["games"] = $msrow["games"];}
	if(empty($msrow["uptime"])){$row["uptime"] = '00:00:00';} else {$row["uptime"] = FormatTime($msrow["time"]);}
	if(empty($psrow["maps"])){$row["maps"] = 0;} else {$row["maps"] = $psrow["maps"];}
	if(empty($crow["num"])){$row["today"] = 0;} else {$row["today"] = $crow["num"];}
	if(empty($yrow["players"])){$row["yesterday"] = 0;} else {$row["yesterday"] = $yrow["players"];}
	
    $row["name"] = htmlspecialchars($row["name"] ?? '');

    if(($thetime - ($row["time"] ?? 0)) > 120) {
      $row["status"]      = "offline"; 
      $row["server_name"] = "";
      $row["map_name"]    = "";
      $row["dedicated"]   = "";
      $row["players"]     = "";
      $row["game_type"]   = "";
      $row["game"]        = "";
	  $row["sstatus"] = "Not Hosting";
      $row["map_image"]   = "<p class=\"main\">Image N/A</p>";
    } else {
      $map_query = DBQuery("SELECT * FROM $maps_table WHERE name='".$row["map_name"]."'");
      if(DBNumRows($map_query) != 0) {
        $map_row = DBFetchArray($map_query);
        if($map_row["image"] != "") {
          $row["map_image"] = "<img src=\"".$base_folder.$map_row["image"]."\" border=\"0\">";
        } else {
          $row["map_image"] = "<p class=\"main\">Image N/A</p>";
        }
      } else {
        $row["map_image"] = "<p class=\"main\">Image N/A</p>";
      }
      $row["status"]      = "online";

      $row["server_name"] = ServerUtils::colorTilde(htmlspecialchars(base64_decode($row["server_name"])));
      $row["map_name"]    = ServerUtils::colorTilde(htmlspecialchars(base64_decode($row["map_name"])));
      $row["players"]     = $row["num_players"]."/".$row["max_players"];
      $row["game_type"]   = ServerUtils::colorTilde(ShortenGameType($row["game_type"]));
      $row["game"]        = ServerUtils::colorTilde(ShortenGameName($row["game"]));
    }
    
    foreach($row as $field=>$value) {
      $tpl->set_var($field, $value);
    }
    
    $players = array();
    $player  = array();
    
    if($row["status"] == "online") {
      $player_names   = explode("\n", $row["player_names"]);
      $player_teams   = explode("\n", $row["player_teams"]);
      $player_weapons = explode("\n", $row["player_weapons"]);
    
      for($i = 0; $i < count($player_names); $i++) {
        if($i % 2 == 0){
          $player["class"] = $rowclass1;
        } else {
          $player["class"] = $rowclass2;
        }
        $player["name"]         = htmlspecialchars(base64_decode($player_names[$i]));
        $player["weapon"]       = $player_weapons[$i];
        $player["team"]         = $player_teams[$i];
        if(isset($wpn_img[trim($player["weapon"])])) {
          $player["weapon_image"] = "<img src=\"".$base_folder.$wpn_img[trim($player["weapon"])]."\" border=\"0\" alt=\"".$player["weapon"]."\">";
        } else {
          $player["weapon_image"] = "";
        }
        $player["num"] = $i + 1;
        $players[$i]   = $player;
      }
    }

    $tpl->set_var("mainscript", $mainscript);
    $tpl->set_var("base",       $base_folder);
    $tpl->set_loop("players",   $players);
    $tpl->process("template", "server_info");
    $content = $tpl->process("", "server_info", 1);
  }
} else {
  $content = "Error: template 'servers' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/servers.php 1.0.0</div>

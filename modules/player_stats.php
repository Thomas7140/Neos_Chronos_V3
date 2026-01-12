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
 * Finalized     : 13th May 2005
 * Modified by   : Peter Jones a.k.a Azrael
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

// Sanitize inputs
$server = (int) $server;
$gametype = escapeSQL($gametype);
$id = (int) $id;

$formurl = $mainscript."action=$action&id=$id";

$type = $gametypes;

$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_table WHERE $stats_table.player='$id' AND $servers_table.id=$stats_table.server GROUP BY $stats_table.server ORDER BY $servers_table.name");
$gametypes = GetGametypesList($gametype, 1, "SELECT game_type FROM $stats_table WHERE $stats_table.player='$id' GROUP BY game_type ORDER BY game_type");

switch($gametype) {
  case "Cooperative"           : $action2 = "coop_stats";  break;
  case "Deathmatch"            : $action2 = "dm_stats";    break;
  case "Team Deathmatch"       : $action2 = "tdm_stats";   break;
  case "King of the Hill"      : $action2 = "koth_stats";  break;
  case "Team King of the Hill" : $action2 = "tkoth_stats"; break;
  case "Capture the Flag"      : $action2 = "ctf_stats";   break;
  case "Flagball"              : $action2 = "fb_stats";    break;
  case "Attack and Defend"     : $action2 = "ad_stats";    break;
  case "Search and Destroy"    : $action2 = "sd_stats";    break;
  case "All"                   : $action2 = "all_stats";   break;
  default                      : $action2 = "unknown";
}

$conditions  = "WHERE $stats_table.player='$id'"; 
$conditions .= "AND $players_table.id=$stats_table.player ";

if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";
if($server   != -1)    $conditions2 = "AND $stats_m_table.server='$server' ";
else $conditions2 = '';
if($gametype != "All") $conditions2 .= "AND $stats_m_table.game_type='$gametype' ";

$query = DBQuery("SELECT 
                    $players_table.id                                AS id               ,
                    $players_table.name                              AS name             ,
                    $players_table.squad                             AS squad            ,
                    $players_table.rating                            AS rating           ,
					$players_table.motm                              AS motm             ,
					$players_table.awards                            AS awards           ,
					$players_table.wpn_awards                        AS wpn_awards       ,
                    SUM($stats_table.kills)                          AS kills            ,
                    SUM($stats_table.deaths)                         AS deaths           ,
                    SUM($stats_table.murders)                        AS murders          ,
                    SUM($stats_table.suicides)                       AS suicides         ,
                    SUM($stats_table.headshots)                      AS headshots        ,
                    SUM($stats_table.knifings)                       AS knifings         ,
					SUM($stats_table.sniper_kills)                   AS sniper_kills     ,
                    SUM($stats_table.medic_saves)                    AS medic_saves      ,
                    SUM($stats_table.revives)                        AS revives          ,
                    SUM($stats_table.doublekills)                    AS doublekills      ,
                    SUM($stats_table.time)                           AS time             ,
                    SUM($stats_table.score_1)                        AS score_1          ,
                    SUM($stats_table.score_2)                        AS score_2          ,
                    SUM($stats_table.score_3)                        AS score_3          ,
                    SUM($stats_table.pspattempts)                    AS pspattempts      ,
                    SUM($stats_table.psptakeovers)                   AS psptakeovers     ,
                    SUM($stats_table.games)                          AS games            ,
                    SUM($stats_table.wins)                           AS wins             ,
					SUM($stats_table.draws)                          AS draws            , 
                    TO_DAYS( NOW( ) ) - TO_DAYS( MAX( last_played )) AS days             ,
					MAX(UNIX_TIMESTAMP(last_played))                 AS last_played      , 
                    SUM($stats_table.wins)*100/IF(SUM($stats_table.games)=0, 1, SUM($stats_table.games)) AS percent_won      ,
					SUM($stats_table.psptakeovers)*100/IF(SUM($stats_table.pspattempts)=0, 1, SUM($stats_table.pspattempts)) AS percent_psp      ,
                    SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio            ,
                    SUM($stats_table.kills)*$timefactor/SUM($stats_table.time)                           AS avg_kills        ,
                    SUM($stats_table.deaths)*$timefactor/SUM($stats_table.time)                          AS avg_deaths       ,
                    SUM($stats_table.murders)*$timefactor/SUM($stats_table.time)                         AS avg_murders      ,
                    SUM($stats_table.suicides)*$timefactor/SUM($stats_table.time)                        AS avg_suicides     ,
                    SUM($stats_table.headshots)*$timefactor/SUM($stats_table.time)                       AS avg_headshots    ,
                    SUM($stats_table.knifings)*$timefactor/SUM($stats_table.time)                        AS avg_knifings     ,
					SUM($stats_table.sniper_kills)*$timefactor/SUM($stats_table.time)                    AS avg_sniper_kills ,
                    SUM($stats_table.medic_saves)*$timefactor/SUM($stats_table.time)                     AS avg_medic_saves  ,
                    SUM($stats_table.revives)*$timefactor/SUM($stats_table.time)                         AS avg_revives      ,
                    SUM($stats_table.doublekills)*$timefactor/SUM($stats_table.time)                     AS avg_doublekills  ,
                    SUM($stats_table.pspattempts)*$timefactor/SUM($stats_table.time)                     AS avg_pspattempts  ,
                    SUM($stats_table.psptakeovers)*$timefactor/SUM($stats_table.time)                    AS avg_psptakeovers ,
                    SUM($stats_table.score_1)*$timefactor/SUM($stats_table.time)                         AS avg_score_1      ,
                    SUM($stats_table.score_2)*$timefactor/SUM($stats_table.time)                         AS avg_score_2      ,
                    SUM($stats_table.score_3)*$timefactor/SUM($stats_table.time)                         AS avg_score_3      ,
                    SUM($stats_table.pspattempts)*$timefactor/SUM($stats_table.time)                     AS avg_pspattempts  ,
                    SUM($stats_table.psptakeovers)*$timefactor/SUM($stats_table.time)                    AS avg_psptakeovers  
                  FROM $players_table, $stats_table
                  $conditions
                  GROUP BY $stats_table.player");
				  
$mquery = DBQuery("SELECT 
                    SUM($stats_m_table.kills)/IF(SUM($stats_m_table.deaths)=0, 1, SUM($stats_m_table.deaths))  AS mratio             
                  FROM $stats_m_table 
				  WHERE $stats_m_table.player='$id'
                  $conditions2
                  GROUP BY $stats_m_table.player");

$stats_query = DBQuery("SELECT * FROM $stats_table WHERE $stats_table.player='$id'");
$award_query = DBQuery("SELECT * FROM $awards_table");

$wpnstats_query = DBQuery("SELECT $stats_table.id, $stats_table.player AS player, $weaponstats_table.id, 
                           $weaponstats_table.record, $weapons_table.name, $weaponstats_table.weapon AS weapon, 
						   SUM( $weaponstats_table.kills ) AS kills,
						   SUM( $weaponstats_table.kills ) *100 / SUM( $weaponstats_table.shots ) AS accuracy
                           FROM $stats_table, $weaponstats_table, $weapons_table
                           WHERE $weaponstats_table.record = $stats_table.id
                           AND $stats_table.player = '$id'
                           AND $weapons_table.id = $weaponstats_table.weapon
                           GROUP BY player, weapon"); 

$other_awards = DBQuery("SELECT $players_table.*, COUNT($players_table.name) AS num, $monthawards_table.* FROM $players_table, $monthawards_table 
                  WHERE $monthawards_table.player = $players_table.name 
				  AND $players_table.id = '$id' AND $monthawards_table.monthaward NOT IN ('Most MOTM Awards Gained', 'Member of the Month (criteria gained -->)') 
				  GROUP BY $players_table.id");
						   
$wpnaward_query = DBQuery("SELECT * FROM $awards_table");

if($gametype != "All") $typecheck .= "AND $stats_table.game_type='$gametype' ";

$conditions  = "WHERE $stats_table.player='$id' "; 
$conditions .= "AND $stats_table.id=$weaponstats_table.record ";
$conditions .= "AND $weapons_table.id=$weaponstats_table.weapon ";
if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";

$weapon_query = DBQuery("SELECT
                           $weapons_table.id                                  AS id     ,
                           $weapons_table.name                                AS weapon ,
                           SUM($weaponstats_table.kills)                      AS kills  ,
                           SUM($weaponstats_table.shots)                      AS shots  ,
                           SUM($weaponstats_table.time)                       AS time   ,
                           SUM($weaponstats_table.kills)*$timefactor/SUM($weaponstats_table.time) AS avg_kills ,
                           SUM($weaponstats_table.shots)*$timefactor/SUM($weaponstats_table.time) AS avg_shots ,
                           SUM($weaponstats_table.kills)*100/if(SUM($weaponstats_table.shots)=0, 1, SUM($weaponstats_table.shots)) AS accuracy  
                         FROM $stats_table, $weapons_table, $weaponstats_table
                         $conditions
                         GROUP BY $weaponstats_table.weapon
                         ORDER BY time DESC");

$conditions  = "WHERE $stats_table.player='$id' "; 
$conditions .= "AND $stats_table.id=$mapstats_table.record ";
$conditions .= "AND $maps_table.id=$mapstats_table.map ";
if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $maps_table.game_type='$gametype' ";

$map_query = DBQuery("SELECT
                        $maps_table.id                                                    AS id         ,
                        $maps_table.name                                                  AS name       ,
                        $maps_table.image                                                 AS image      ,
                        $maps_table.thumbnail                                             AS thumbnail  ,
                        SUM($mapstats_table.kills)                                        AS kills      ,
                        SUM($mapstats_table.deaths)                                       AS deaths     ,
						SUM($mapstats_table.kills)/IF(SUM($mapstats_table.deaths)=0, 1, SUM($mapstats_table.deaths))  AS mp_ratio            ,
                        SUM($mapstats_table.time)                                         AS time       ,
                        SUM($mapstats_table.kills)*$timefactor/SUM($mapstats_table.time)  AS avg_kills  ,
                        SUM($mapstats_table.deaths)*$timefactor/SUM($mapstats_table.time) AS avg_deaths  
                      FROM $maps_table, $mapstats_table, $stats_table
                      $conditions
                      GROUP BY $mapstats_table.map
                      ORDER BY time DESC
                      LIMIT 50");

$ga_sql = DBQuery("select * FROM $playerawards_table where player = '$id' ORDER BY date DESC LIMIT 0, 10;");

$check  = DBQuery("SELECT * FROM $stats_m_table WHERE player = '$id'");
	                  
if(file_exists($base_folder."templates/player_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/player_stats.htm", 1);
  
  $row = DBFetchArray($query);
  $crow = DBFetchArray($check);
  $mrow = DBFetchArray($mquery);
  $orow = DBFetchArray($other_awards);
  
  if(!($crow["player"])) {$row["month_stats"] = "";} else {
    $row["month_stats"] = "<a href=\"{monthlyscript}action=player_stats&id={id}\">Click for Monthly stats</a>";
  }

  if(!($row["ratio"])){$row["ratio"] = '0.00';} else {$row["ratio"] = $row["ratio"];}
  if(!($mrow["mratio"])){$row["mratio"] = '0.00';} else {$row["mratio"] = $mrow["mratio"];}
  
  $row["r_class"] = GetRatioColor($row["ratio"]);
  $row["mr_class"] = GetRatioColor($row["mratio"]);
  
  $max_value = max($row["kills"], $row["deaths"], $row["headshots"], $row["knifings"], $row["murders"], $row["suicides"], $row["medic_saves"], $row["doublekills"], $row["revives"]);
  if($max_value == 0) $max_value = 1;
  $ratio = 150/$max_value;
  $row["len_kills"]         = round($row["kills"]*$ratio,         0);
  $row["len_deaths"]        = round($row["deaths"]*$ratio,        0);
  $row["len_headshots"]     = round($row["headshots"]*$ratio,     0);
  $row["len_knifings"]      = round($row["knifings"]*$ratio,      0);
  $row["len_sniper_kills"]  = round($row["sniper_kills"]*$ratio,  0);
  $row["len_murders"]       = round($row["murders"]*$ratio,       0);
  $row["len_suicides"]      = round($row["suicides"]*$ratio,      0);
  $row["len_medic_saves"]   = round($row["medic_saves"]*$ratio,   0);
  $row["len_doublekills"]   = round($row["doublekills"]*$ratio,   0);
  $row["len_revives"]       = round($row["revives"]*$ratio,       0);
  
  if($row["kills"]  == 0) $kills_divide  = 1; else $kills_divide  = $row["kills"];
  if($row["deaths"] == 0) $deaths_divide = 1; else $deaths_divide = $row["deaths"];
  if($row["days"] < 1) {$row["days"] = "0 days ago";} else {$row["days"] = $row["days"]." days ago";}

  $row["percent_headshots"]    = round($row["headshots"]*100/$kills_divide, 0);
  $row["percent_knifings"]     = round($row["knifings"]*100/$kills_divide, 0);
  $row["percent_sniper_kills"] = round($row["sniper_kills"]*100/$kills_divide, 0);
  $row["percent_doublekills"]  = round($row["doublekills"]*100/$kills_divide, 0);
  $row["percent_suicides"]     = round($row["suicides"]*100/$deaths_divide, 0);
  
  $row["time"] = FormatTime($row["time"]);
    
  $row["last_played"] = date("Y-d-m H:i:s", $row["last_played"]);
	
  $row["name"] = htmlspecialchars(base64_decode($row["name"]));
  
  if(!($orow["num"])){$row["other"] = 0;} else {$row["other"] = $orow["num"];}
  
  if($gametype == "King of the Hill" || $gametype == "Team King of the Hill") {
    $row["score_1"]     = FormatTime($row["score_1"]);
    $row["avg_score_1"] = FormatTime(round($row["avg_score_1"], 0));
  }
  
  $row["losses"] = $row["games"] - ($row["wins"] + $row["draws"]);
  
  $rank = GetRank($row["rating"]);
  $row["rank_name"]      = $rank["name"];
  $row["rank_image"]     = $rank["image"];
  $row["rank_thumbnail"] = $rank["thumbnail"];
  
  $row["ranking"] = GetRanking($id);
  if(!($row["ranking"])){$row["ranking"] = "Not Ranked";}
  $row["mrank"] = GetMonthlyRanking($id);
  if(!($row["mrank"])){$row["mrank"] = "Not Ranked";}

  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }
  
  $goal_stats = "";
  if($gametype == "All"){

	$tpl2 = new phemplate();
    $tpl2->set_file("template", $base_folder."templates/player_stats.htm", 1);

    foreach($type as $value) {
      $row3["$value"] = GetGameLevels($id, $value, "player", "overall");
    }

	foreach($row3 as $field => $value) {
      $tpl2->set_var($field, $value);
    }
	$tpl->set_var("base", $base_folder);
    $tpl2->process("template", $action2);
    $goal_stats = $tpl2->process("", "template", 1);
	}
  if($gametype != "All") {
    $tpl2 = new phemplate();
    $tpl2->set_file("template", $base_folder."templates/player_stats.htm", 1);
    foreach($row as $field => $value) {
      $tpl2->set_var($field, $value);
    }
    $tpl->set_var("base", $base_folder);
    $tpl2->process("template", $action2);
    $goal_stats = $tpl2->process("", "template", 1);
  }

  $squadblock = "";
  if($row["squad"] != -1) {
    $squad_query = DBQuery("SELECT * FROM $squads_table WHERE id='".$row["squad"]."'");
    $squad_row = DBFetchArray($squad_query);
	$squad_row["name"] = $squad_row["name"];
	$squad_row["tag"] = $squad_row["tag"];
    $tpl3 = new phemplate();
    $tpl3->set_file("template", $base_folder."templates/player_stats.htm", 1);
        if(is_array($squad_row)) {
      foreach($squad_row as $field => $value) {
        $tpl3->set_var($field, $value);
      }
    }
    $tpl3->set_var("base", $base_folder);
    $tpl3->process("template", "squad_block");
    $squadblock = $tpl3->process("", "template", 1);
  }
  
  $newawards = array();
  $newaward  = array();
  $count   = 0;
  
  while($newaward = DBFetchArray($ga_sql)) {
      if($count % 2 == 0){
      $newaward["class"] = $rowclass2;
    } else {
      $newaward["class"] = $rowclass1;
    }
	
	$newaward["date"] = substr($newaward["date"], 0, 10);
	
	$newaward["num"]   = $count + 1;
    $newawards[$count] = $newaward;
    $count++;
  }
  
  $award_stats = array();
  $stats_row   = array();
  while($stats_row = DBFetchArray($stats_query)) {
    foreach($stats_row as $field => $value) {
      if($field * 1 == 0 && $field != "0") {
        $stats_field = "";
        switch($field) {
          case "kills":        $stats_field = "kills";           break;
          case "deaths":       $stats_field = "deaths";          break;
          case "murders":      $stats_field = "murders";         break;
          case "suicides":     $stats_field = "suicides";        break;
          case "knifings":     $stats_field = "knifings";        break;
		  case "sniper_kills": $stats_field = "sniper_kills";    break;
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
		$awards[$count]["award"] = "<td align=\"center\" width=\"60\"><img src=\"".$base_folder.$award_row["image"]."\" border=\"0\" alt=\"".$award_row["name"]."\n".$award_row["description"]."\"></td>";
		if(($count+1) % 9 == 0) $awards[$count]["award"] .= "</tr><tr>";
        $count++;
      }
    }
  } 
  
  $wpnaward_stats = array();
  $wpnstats_row   = array();
  while($wpnstats_row = DBFetchArray($wpnstats_query)) {
    foreach($wpnstats_row as $field => $value) {
      if($field * 1 == 0 && $field != "0") {
        $wpnstats_field = "";
		if($wpnstats_row["name"] == "Knife") {
          switch($field) {
            case "kills": $wpnstats_field = "knife_kills";      break;
			case "accuracy": $wpnstats_field = "knife_accuracy";      break;
          }
        }
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
        $wpnawards[$count]["wpnaward"] = "<td align=\"center\" width=\"60\"><img src=\"".$base_folder.$wpnaward_row["image"]."\" border=\"0\" alt=\"".$wpnaward_row["name"]."\n".$wpnaward_row["description"]."\"></td>";
        if(($count+1) % 9 == 0) $wpnawards[$count]["wpnaward"] .= "</tr><tr>";
        $count++;
      }
    }
  }

  $weapons = array();
  $weapon  = array();
  $count   = 0;

  $max_wpn_time = 0;
  
  while($weapon = DBFetchArray($weapon_query)) {
    if($count % 2 == 0){
      $weapon["class"] = $rowclass1;
    } else {
      $weapon["class"] = $rowclass2;
    }
  
    $max_wpn_time += $weapon["time"];
    
    $weapon["time_sec"]    = $weapon["time"];
    $weapon["time"]        = FormatTime($weapon["time"]);
    $weapon["image"]       = $wpn_img[$weapon["weapon"]];
    $weapon["weapon"]      = htmlspecialchars($weapon["weapon"]);
	$weapon["explvl"] = GetWeaponLevels($id, $weapon["weapon"]);
	$weapon["exp"] = "Level <span class=\"{weapons.lvl_clr}\">".$weapon["explvl"]."</span>/<span class=\"green\">50</span>";
	if($weapon["explvl"] < 10) $weapon["lvl_clr"] = "red";
	if($weapon["explvl"] > 9 AND $weapon["explvl"] < 20) $weapon["lvl_clr"] = "orange";
	if($weapon["explvl"] > 19 AND $weapon["explvl"] < 30) $weapon["lvl_clr"] = "orange2";
	if($weapon["explvl"] > 29 AND $weapon["explvl"] < 40) $weapon["lvl_clr"] = "yellow";
	if($weapon["explvl"] > 39 AND $weapon["explvl"] < 50) $weapon["lvl_clr"] = "green";
	if($weapon["explvl"] === 50) $weapon["exp"] = "<div align=\"center\"><img src=\"{base}weapons/mastered.gif\" border=\"0\" width=\"60\" height=\"15\"></div>";
    
    $weapon["num"]   = $count + 1;
    $weapons[$count] = $weapon;
    $count++;
  }
  
  if($max_wpn_time == 0) $max_wpn_time = 1;
  $wpn_time_ratio = 100/$max_wpn_time;
  for($i = 0; $i < $count; $i++) {
    $weapons[$i]["usage_bar_len"] = round($weapons[$i]["time_sec"]*$wpn_time_ratio, 0);
    $weapons[$i]["usage_percent"] = round($weapons[$i]["time_sec"]*$wpn_time_ratio, 0);
  }

  $maps  = array();
  $map   = array();
  $count = 0;

  while($map = DBFetchArray($map_query)) {
    if($count % 2 == 0){
      $map["class"] = $rowclass1;
    } else {
      $map["class"] = $rowclass2;
    }
    
	$map["mpr_class"] = GetRatioColor($map["mp_ratio"]);
	
    $map["time"] = FormatTime($map["time"]);
    $map["name"] = htmlspecialchars(base64_decode($map["name"]));
    
    if($map["image"] != "") {
      $map["image"] = "<img src=\"".$base_folder.$map["image"]."\" border=\"1\">";
    } else {
      $map["image"] = "<img src=\"".$base_folder."maps\l_none.jpg\" border=\"1\">";
    }
    
    if($map["thumbnail"] != "") {
      $map["thumbnail"] = "<img src=\"".$base_folder.$map["thumbnail"]."\" border=\"1\">";
    } else {
      $map["thumbnail"] = "<img src=\"".$base_folder."maps\s_none.jpg\" border=\"1\">";
    }
    
    $map["num"]   = $count + 1;
    $maps[$count] = $map;
    $count++;
  }

  $tpl->set_var("mainscript",    $mainscript);
  $tpl->set_var("monthlyscript", $monthlyscript);
  $tpl->set_var("formurl",       $formurl);
  $tpl->set_var("base",          $base_folder);
  $tpl->set_var("squadblock",    $squadblock);
  $tpl->set_var("goal_stats",    $goal_stats);
  $tpl->set_loop("servers",      $servers);
  $tpl->set_loop("gametypes",    $gametypes);
  $tpl->set_loop("awards",       $awards);
  $tpl->set_loop("wpnawards",    $wpnawards);
  $tpl->set_loop("weapons",      $weapons);
  $tpl->set_loop("newawards",    $newawards);
  $tpl->set_loop("maps",         $maps);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'player_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/player_stats.php 1.0.0</div>
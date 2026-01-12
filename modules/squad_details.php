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
 * Finalized     : 4th May 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

$pageurl = $mainscript."action=$action&server=$server&gametype=$gametype&id=$id";
$formurl = $mainscript."action=$action&id=$id";

$types = $gametypes;

$gametypes = GetGameTypesList($gametype, 1, "SELECT $stats_table.game_type FROM $stats_table, $players_table WHERE $stats_table.player=$players_table.id AND $players_table.squad='$id' GROUP BY $stats_table.game_type ORDER BY $stats_table.game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_table, $players_table WHERE $servers_table.id=$stats_table.server AND $players_table.id=$stats_table.player AND $players_table.squad='$id' GROUP BY $servers_table.id ORDER BY $servers_table.name");

$conditions  = "WHERE $players_table.squad='$id'"; 
$conditions .= " AND $stats_table.player=$players_table.id ";
$conditions .= " AND $players_table.squad=$squads_table.id ";

if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";

$query = DBQuery("SELECT 
                    $squads_table.id                                 AS id          ,
                    $squads_table.name                               AS name        ,
                    $squads_table.tag                                AS tag         ,
                    $squads_table.url                                AS url         ,
                    $stats_table.server                              AS server      ,
                    $stats_table.game_type                           AS game_type   ,
                    SUM($stats_table.kills)                          AS kills       ,
                    SUM($stats_table.deaths)                         AS deaths      ,
                    SUM($stats_table.murders)                        AS murders     ,
                    SUM($stats_table.suicides)                       AS suicides    ,
                    SUM($stats_table.headshots)                      AS headshots   ,
                    SUM($stats_table.knifings)                       AS knifings    ,
                    SUM($stats_table.medic_saves)                    AS medic_saves ,
                    SUM($stats_table.revives)                        AS revives     ,
                    SUM($stats_table.doublekills)                    AS doublekills ,
                    SUM($stats_table.time)                           AS time        ,
                    SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio 
                  FROM $players_table, $stats_table, $squads_table
                  $conditions
                  GROUP BY $players_table.squad");

$conditions  = "WHERE $players_table.id=$stats_table.player "; 
$conditions .= "AND $players_table.squad='$id' ";
 
if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";
if($server   != -1)    $conditions2 .= "AND $stats_m_table.server='$server' ";
if($gametype != "All") $conditions2 .= "AND $stats_m_table.game_type='$gametype' ";
if($server   != -1)    $conditions3 .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions3 .= "AND $stats_table.game_type='$gametype' ";

$player_query = DBQuery("SELECT
                           $players_table.id              AS id      ,
                           $players_table.name            AS name    ,
                           $players_table.rating          AS rating  ,
                           SUM($stats_table.kills)        AS kills   ,
                           SUM($stats_table.deaths)       AS deaths  ,
                           SUM($stats_table.time)         AS time    ,
                           SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio  
                         FROM $players_table, $stats_table
                         $conditions
                         GROUP BY $stats_table.player
                         ORDER BY rating DESC");

$sniper_sql = DBQuery("SELECT $players_table.id, $players_table.squad, $stats_table.player, $stats_table.id, 
                         $weaponstats_table.id, SUM($weaponstats_table.kills) AS sniper_kills, 
					     SUM($weaponstats_table.kills)*$timefactor/SUM($weaponstats_table.time) AS avg_sniper_kills 
                       FROM $players_table, $stats_table, $weaponstats_table, $weapons_table
                       WHERE $weaponstats_table.record = $stats_table.id 
					   AND $stats_table.player = $players_table.id AND $players_table.squad = '$id'  
					   AND $weapons_table.id = $weaponstats_table.weapon 
                       AND $weapons_table.name IN ('PSG1','MCRT .300 Tactical','M21','M24','Barrett .50 Cal') 
					   $conditions3 
                       GROUP BY squad");
                      
if(file_exists($base_folder."templates/squad_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/squad_stats.htm", 1);
  
  $row = DBFetchArray($query);
  $rowsn = DBFetchArray($sniper_sql);
  
  //$row["name"] = htmlspecialchars(base64_decode($row["name"]));
  //$row["tag"] = htmlspecialchars(base64_decode($row["tag"]));
    
  $max_value = max($row["kills"], $row["deaths"], $row["headshots"], $row["knifings"], $row["murders"], $row["suicides"], $row["medic_saves"], $row["doublekills"], $row["revives"]);
  if($max_value == 0) $max_value = 1;
  $ratio = 150/$max_value;
  $row["len_kills"]       = round($row["kills"]*$ratio,       0);
  $row["len_deaths"]      = round($row["deaths"]*$ratio,      0);
  $row["len_headshots"]   = round($row["headshots"]*$ratio,   0);
  $row["len_knifings"]    = round($row["knifings"]*$ratio,    0);
  $row["len_murders"]     = round($row["murders"]*$ratio,     0);
  $row["len_suicides"]    = round($row["suicides"]*$ratio,    0);
  $row["len_medic_saves"] = round($row["medic_saves"]*$ratio, 0);
  $row["len_doublekills"] = round($row["doublekills"]*$ratio, 0);
  $row["len_revives"]     = round($row["revives"]*$ratio, 0);
  
  if($row["kills"]  == 0) $kills_divide  = 1; else $kills_divide  = $row["kills"];
  if($row["deaths"] == 0) $deaths_divide = 1; else $deaths_divide = $row["deaths"];

  $row["percent_headshots"]    = round($row["headshots"]*100/$kills_divide, 0);
  $row["percent_knifings"]     = round($row["knifings"]*100/$kills_divide, 0);
  $row["percent_doublekills"]  = round($row["doublekills"]*100/$kills_divide, 0);
  $row["percent_suicides"]     = round($row["suicides"]*100/$deaths_divide, 0);
  
  if(!($rowsn)) {$row["sniper_kills"] = 0;} else {$row["sniper_kills"] = $rowsn["sniper_kills"];}
  $row["len_sniper_kills"] = round($row["sniper_kills"]*$ratio, 0);
  $row["percent_sniper_kills"]  = round($row["sniper_kills"]*100/$deaths_divide, 0);

  $row["name"] = htmlspecialchars($row["name"]);
  $row["tag"]  = htmlspecialchars($row["tag"]);
  
  $row["time"]        = FormatTime($row["time"]);
  
  foreach($types as $value) {
      $row["$value"] = GetGameLevels($id, $value, "squad", "overall");
  }

  
  $type = "squad";
  $wpns = array('assault', 'support', 'sniper', 'secondary', 'other', 'emplace');
  foreach($wpns as $value) {
      $row["$value"] = GetWeaponUsage($value, $id, $type, $server, $gametype);
  }
    
  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }
  
  $players = array();
  $player  = array();
  $count   = 0;

  while($player = DBFetchArray($player_query)) {
    if($count % 2 == 0){
      $player["class"] = $rowclass1;
    } else {
      $player["class"] = $rowclass2;
    }
	
    $plyr = array($player["id"]);
	foreach($plyr as $pvalue) {
	  $plyrs_m_query = DBQuery("SELECT 
                            SUM($stats_m_table.kills)                          AS kills           ,
                            SUM($stats_m_table.deaths)                         AS deaths          ,
							SUM($stats_m_table.time)                           AS time            ,
                            SUM($stats_m_table.kills)/IF(SUM($stats_m_table.deaths)=0, 1, SUM($stats_m_table.deaths))  AS ratio
                          FROM $stats_m_table 
                          WHERE $stats_m_table.player = '$pvalue'
						  $conditions2
                          GROUP BY $stats_m_table.player");
	  
	  $num_rows = mysql_num_rows($plyrs_m_query);
	  if(!($num_rows)) {
	    $player["mkills"] = '0'; $player["mdeaths"] = '0'; $player["mratio"] = '0.00'; $player["mtime"] = '00:00:00';
	  } else {
	    while($prow = DBFetchArray($plyrs_m_query)) {
	      $player["mkills"] = $prow["kills"];
		  $player["mdeaths"] = $prow["deaths"];
		  $player["mratio"] = $prow["ratio"];
		  $player["mtime"] = FormatTime($prow["time"]);
	    }
      }
	}
  
    $player["time"] = FormatTime($player["time"]);
    $player["name"] = htmlspecialchars(base64_decode($player["name"]));

    $rank = GetRank($player["rating"]);
    $player["rank_name"]      = $rank["name"];
    $player["rank_image"]     = $rank["image"];
    $player["rank_thumbnail"] = $rank["thumbnail"];
	
    $player["r_class"] = GetRatioColor($player["ratio"]);
    $player["mr_class"] = GetRatioColor($player["mratio"]);

    $player["num"]   = $count + 1;
    $players[$count] = $player;
    $count++;
  }
  
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("players",   $players);
  $tpl->set_loop("servers",   $servers);
  $tpl->set_loop("gametypes", $gametypes);
  $tpl->process("template", "squad_details");
  $content = $tpl->process("", "template", 1);
  
} else {
  $content = "Error: template 'squad_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/squad_details.php 1.0.0</div>
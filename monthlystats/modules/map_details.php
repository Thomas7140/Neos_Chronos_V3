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
 * Module        : Monthly
 * Finalized     : 6th May 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(!isset($server) || $server   == "") $server   = -1;

$pageurl = $mainscript."action=$action&server=$server&id=$id";
$formurl = $mainscript."action=$action&id=$id";

$servers = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $maps_table, $stats_m_table, $mapstats_m_table WHERE $maps_table.id='$id' AND $servers_table.id=$stats_m_table.server AND $maps_table.id=$mapstats_m_table.map AND $stats_m_table.id=$mapstats_m_table.record GROUP BY $stats_m_table.server ORDER BY $servers_table.name");

$conditions  = "WHERE $maps_table.id='$id' "; 
$conditions .= "AND $maps_table.id=$mapstats_m_table.map ";
$conditions .= "AND $stats_m_table.id=$mapstats_m_table.record ";
if($server != -1) $conditions .= "AND $stats_m_table.server='$server' ";

$query = DBQuery("SELECT 
                    $maps_table.id                                            AS id            ,
                    $maps_table.name                                          AS name          ,
                    $maps_table.thumbnail                                     AS thumbnail     ,
                    $maps_table.image                                         AS image         ,
                    $maps_table.file                                          AS file          ,
                    $stats_m_table.server                                       AS server        ,
                    $maps_table.game_type                                     AS game_type     ,
                    $maps_table.hosted                                        AS hosted        ,
                    $maps_table.time                                          AS time          ,
                    SUM($mapstats_m_table.kills)                                AS kills         ,
                    SUM($mapstats_m_table.score)                                AS score         ,
                    SUM($mapstats_m_table.kills)*$timefactor/$maps_table.time   AS avg_kills     ,
                    SUM($mapstats_m_table.score)*$timefactor/$maps_table.time   AS avg_score     ,
					TO_DAYS( NOW( ) ) - TO_DAYS( MAX( $maps_table.last_played ))  AS days      ,
                    $maps_table.last_played                                       AS last_played    
                  FROM $maps_table, $mapstats_m_table, $stats_m_table
                  $conditions
                  GROUP BY $mapstats_m_table.map");

$conditions  = "WHERE $mapstats_m_table.map='$id' "; 
$conditions .= "AND $players_table.id=$stats_m_table.player "; 
$conditions .= "AND $stats_m_table.id=$mapstats_m_table.record "; 
$conditions .= "AND $maps_table.id=$mapstats_m_table.map "; 
if($server != -1) $conditions .= "AND $stats_m_table.server='$server' ";

$player_query = DBQuery("SELECT
                           $players_table.id                                                 AS id         ,
                           $players_table.name                                               AS name       ,
                           $players_table.rating                                             AS rating     ,
                           SUM($mapstats_m_table.kills)                                        AS kills      ,
                           SUM($mapstats_m_table.deaths)                                       AS deaths     ,
                           SUM($mapstats_m_table.score)                                        AS score      ,
                           SUM($mapstats_m_table.time)                                         AS time       ,
						   SUM($mapstats_m_table.kills)/IF(SUM($mapstats_m_table.deaths)=0, 1, SUM($mapstats_m_table.deaths))  AS mp_ratio            ,
                           SUM($mapstats_m_table.kills)*$timefactor/SUM($mapstats_m_table.time)  AS avg_kills  ,
                           SUM($mapstats_m_table.deaths)*$timefactor/SUM($mapstats_m_table.time) AS avg_deaths ,
                           SUM($mapstats_m_table.score)*$timefactor/SUM($mapstats_m_table.time)  AS avg_score   
                         FROM $maps_table, $mapstats_m_table, $players_table, $stats_m_table
                         $conditions
                         GROUP BY $mapstats_m_table.record
                         ORDER BY time DESC
                         LIMIT 25");

                      
if(file_exists($base_folder."templates/map_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/map_stats.htm", 1);
  
  $row = DBFetchArray($query);
    
  $row["name"] = htmlspecialchars(base64_decode($row["name"]));
  
  $wpns = array('assault', 'support', 'sniper', 'secondary', 'other', 'emplace');
  foreach($wpns as $value) {
      $row["$value"] = GetMapWeaponUsage($value, $id, $server);
  }
  
  if($row["image"] != "") {
    $row["image"] = "<img src=\"".$base_folder.$row["image"]."\" border=\"1\">";
  } else {
    $row["image"] = "<img src=\"".$base_folder."maps/l_none.jpg\" border=\"1\">";
  }
  
  if($row["thumbnail"] != "") {
    $row["thumbnail"] = "<img src=\"".$base_folder.$row["thumbnail"]."\" border=\"1\">";
  } else {
    $row["thumbnail"] = "<img src=\"".$base_folder."maps/s_none.jpg\" border=\"1\">";
  }
  
  $row["time"]        = FormatTime($row["time"]);
    
  if($row["game_type"] == "King of the Hill" || $row["game_type"] == "Team King of the Hill") {
    $row["score"]     = FormatTime($row["score"]);
    $row["avg_score"] = FormatTime(round($row["avg_score"], 0));
  }
  
  if($row["days"] < 1) {$row["days"] = "0 days ago";} else {$row["days"] = $row["days"]." days ago";}
  
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
  
    $player["mpr_class"] = GetRatioColor($player["mp_ratio"]);
  
    $player["time"] = FormatTime($player["time"]);
    $player["name"] = htmlspecialchars(base64_decode($player["name"]));

    if($row["game_type"] == "King of the Hill" || $row["game_type"] == "Team King of the Hill") {
      $player["score"]     = FormatTime($player["score"]);
      $player["avg_score"] = FormatTime(round($player["avg_score"], 0));
    }
  
    $rank = GetRank($player["rating"]);
    $player["rank_name"]      = $rank["name"];
    $player["rank_image"]     = $rank["image"];
    $player["rank_thumbnail"] = $rank["thumbnail"];

    $player["num"]   = $count + 1;
    $players[$count] = $player;
    $count++;
  }
  
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("players",   $players);
  $tpl->set_loop("servers",   $servers);
  $tpl->process("template", "map_details");
  $content = $tpl->process("", "template", 1);
  
} else {
  $content = "Error: template 'map_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">monthlystats/modules/map_details.php 1.0.0</div>
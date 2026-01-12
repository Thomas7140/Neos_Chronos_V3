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
 * Module        : Monthly
 */

if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

$pageurl = $mainscript."action=$action&server=$server&gametype=$gametype&id=$id";
$formurl = $mainscript."action=$action&id=$id";

$gametypes = GetGameTypesList($gametype, 1, "SELECT $stats_m_table.game_type FROM $stats_m_table, $weaponstats_m_table WHERE $stats_m_table.id=$weaponstats_m_table.record AND $weaponstats_m_table.weapon='$id' GROUP BY $stats_m_table.game_type ORDER BY $stats_m_table.game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_m_table, $weaponstats_m_table WHERE $servers_table.id=$stats_m_table.server AND $weaponstats_m_table.record=$stats_m_table.id AND $weaponstats_m_table.weapon='$id' GROUP BY $servers_table.id ORDER BY $servers_table.name");

$conditions = "WHERE $weapons_table.id='$id' "; 
$conditions .= "AND $stats_m_table.id=$weaponstats_m_table.record ";
$conditions .= "AND $weaponstats_m_table.weapon=$weapons_table.id ";
if($server   != -1)    $conditions .= "AND $stats_m_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_m_table.game_type='$gametype' ";

$query = DBQuery("SELECT 
                    $weapons_table.id                                                      AS id          ,
                    $weapons_table.name                                                    AS name        ,
                    $stats_m_table.server                                                    AS server      ,
                    $stats_m_table.game_type                                                 AS game_type   ,
                    SUM($weaponstats_m_table.kills)                                          AS kills       ,
                    SUM($weaponstats_m_table.time)                                           AS time        ,
                    SUM($weaponstats_m_table.kills)*$timefactor/SUM($weaponstats_m_table.time) AS avg_kills   ,
                    SUM($weaponstats_m_table.kills)*100/if(SUM($weaponstats_m_table.shots)=0, 1, SUM($weaponstats_m_table.shots)) AS accuracy 
                  FROM $stats_m_table, $weapons_table, $weaponstats_m_table
                  $conditions
                  GROUP BY $weapons_table.name");

$conditions  = "WHERE $weaponstats_m_table.weapon='$id' "; 
$conditions .= "AND $players_table.id=$stats_m_table.player "; 
$conditions .= "AND $stats_m_table.id=$weaponstats_m_table.record "; 
$conditions .= "AND $weapons_table.id=$weaponstats_m_table.weapon "; 
if($server   != -1)    $conditions .= "AND $stats_m_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_m_table.game_type='$gametype' ";

$player_query = DBQuery("SELECT
                           $players_table.id                                                      AS id         ,
                           $players_table.name                                                    AS name       ,
                           $players_table.rating                                                  AS rating     ,
                           SUM($weaponstats_m_table.kills)                                          AS kills      ,
                           SUM($weaponstats_m_table.time)                                           AS time       ,
                           SUM($weaponstats_m_table.kills)*$timefactor/SUM($weaponstats_m_table.time) AS avg_kills  ,
                           SUM($weaponstats_m_table.kills)*100/if(SUM($weaponstats_m_table.shots)=0, 1, SUM($weaponstats_m_table.shots)) AS accuracy 
                         FROM $weapons_table, $weaponstats_m_table, $players_table, $stats_m_table
                         $conditions
                         GROUP BY $stats_m_table.player
                         ORDER BY time DESC
                         LIMIT 25");

                      
if(file_exists($base_folder."templates/weapon_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/weapon_stats.htm", 1);
  
  $row = DBFetchArray($query);
    
  $row["name"] = htmlspecialchars($row["name"]);
  
  $row["image"] = $wpn_img[$row["name"]];
  
  $row["time"] = FormatTime($row["time"]);
  
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
  $tpl->set_loop("gametypes", $gametypes);
  $tpl->process("template", "weapon_details");
  $content = $tpl->process("", "template", 1);
  
} else {
  $content = "Error: template 'weapon_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">monthlystats/modules/weapon_details.php 1.0.0</div>
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

$tableheader = array(
  0 => "id",
  1 => "name",
  2 => "squad",
  3 => "kills",
  4 => "rating",
  5 => "time",
  6 => "last_played",
  7 => "score_1",
  8 => "score_2",
  9 => "score_3",
  10 => "avg_score_1",
  11 => "avg_score_2",
  12 => "avg_score_3",
  13 => "games",
  14 => "wins",
  15 => "draws",
  16 => "percent_won",
  17 => "pspattempts",
  18 => "psptakeovers",
  19 => "psppercent",
  20 => "avg_pspattempts",
  21 => "avg_psptakeovers",
  22 => "ratio",
  23 => "level"
);                          

if(!isset($sort)     || $sort     == "") $sort     = "score_1";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($page)     || $page     == "") $page     = 1;
if(!isset($squad)    || $squad    == "") $squad    = -1;
if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") {
  $tmp      = GetGameTypesList("", 0, "SELECT game_type FROM $stats_m_table GROUP BY game_type ORDER BY game_type");
  $tmp      = $tmp[0];
  $gametype = $tmp["name"];
}

$type = $gametypes;

switch($gametype) {
  case "Cooperative"           : $action2 = "coop_stats";  break;
  case "Deathmatch"            : $action2 = "dm_stats";    $conditions2 = "SUM(($stats_m_table.kills)/100) + SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) AS level, ";  break;
  case "Team Deathmatch"       : $action2 = "tdm_stats";   $conditions2 = "SUM($stats_m_table.kills)/100 + SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM($stats_m_table.psptakeovers)/10 AS level, ";  break;
  case "King of the Hill"      : $action2 = "koth_stats";  $conditions2 = "SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM($stats_m_table.score_1)/3600 + SUM($stats_m_table.score_2)/15 + SUM($stats_m_table.score_3)/15 AS level, "; break;
  case "Team King of the Hill" : $action2 = "tkoth_stats"; $conditions2 = "SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM($stats_m_table.score_1)/3600 + SUM($stats_m_table.score_2)/15 + SUM($stats_m_table.score_3)/15 AS level, "; break;
  case "Capture the Flag"      : $action2 = "ctf_stats";   $conditions2 = "SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM($stats_m_table.score_1)/10 + SUM($stats_m_table.score_2)/10 + SUM($stats_m_table.score_3)/10 AS level, "; break;
  case "Flagball"              : $action2 = "fb_stats";    $conditions2 = "SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM($stats_m_table.score_1)/6 + SUM($stats_m_table.score_3)/6 AS level, ";     break;
  case "Attack and Defend"     : $action2 = "ad_stats";    $conditions2 = "SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM(($stats_m_table.score_1)/12) + SUM(($stats_m_table.score_2)/12) + SUM(($stats_m_table.score_3)/12) AS level, ";  break;
  case "Search and Destroy"    : $action2 = "sd_stats";    $conditions2 = "SUM($stats_m_table.games) + SUM(($stats_m_table.time)/3600) + SUM($stats_m_table.score_1)/25 + SUM($stats_m_table.score_2)/8 + SUM($stats_m_table.score_3)/8 AS level, ";  break;
  default                      : $action2 = "unknown";
}

$pageurl = $mainscript."action=$action&sort=$sort&order=$order&server=$server&gametype=$gametype&squad=$squad";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$squads    = GetSquadsList($squad, 1);
$gametypes = GetGameTypesList($gametype, 0, "SELECT game_type FROM $stats_m_table GROUP BY game_type ORDER BY game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_m_table GROUP BY $servers_table.id ORDER BY $servers_table.name");

$conditions = "WHERE $players_table.id=$stats_m_table.player ";

if($squad != -1)  $conditions .= "AND $players_table.squad='$squad' ";
if($server != -1) $conditions .= "AND $stats_m_table.server='$server' ";

$conditions .= "AND $stats_m_table.game_type='$gametype' ";

$query = DBQuery("SELECT $players_table.id FROM $stats_m_table, $players_table $conditions GROUP BY $stats_m_table.player");
$totalplayers = DBNumRows($query);

$offset = ($page - 1) * $playerstoshow;

$multipage = Multipage($pageurl, $totalplayers, $playerstoshow, $page);

$query = DBQuery("SELECT 
                    $players_table.id                               AS id               ,
                    $players_table.name                             AS name             ,
                    $players_table.squad                            AS squad            ,
                    $players_table.rating                           AS rating           ,
					SUM($stats_m_table.kills)                         AS kills            ,
                    SUM($stats_m_table.score_1)                       AS score_1          ,
                    SUM($stats_m_table.score_2)                       AS score_2          ,
                    SUM($stats_m_table.score_3)                       AS score_3          ,
                    SUM($stats_m_table.time)                          AS time             ,
                    SUM($stats_m_table.games)                         AS games            ,
                    SUM($stats_m_table.wins)                          AS wins             ,
					SUM($stats_m_table.draws)                         AS draws            ,
                    SUM($stats_m_table.pspattempts)                   AS pspattempts      ,
                    SUM($stats_m_table.psptakeovers)                  AS psptakeovers     ,
                    SUM($stats_m_table.score_1)*$timefactor/time      AS avg_score_1      ,
                    SUM($stats_m_table.score_2)*$timefactor/time      AS avg_score_2      ,
                    SUM($stats_m_table.score_3)*$timefactor/time      AS avg_score_3      ,
                    SUM($stats_m_table.pspattempts)*$timefactor/time  AS avg_pspattempts  ,
                    SUM($stats_m_table.psptakeovers)*$timefactor/time   AS avg_psptakeovers , 
					$conditions2 
                    MAX($stats_m_table.last_played)                   AS last_played      ,
                    SUM($stats_m_table.wins)*100/IF(SUM($stats_m_table.games)=0, 1, SUM($stats_m_table.games)) AS percent_won , 
					SUM($stats_m_table.psptakeovers)*100/IF(SUM($stats_m_table.pspattempts)=0, 1, SUM($stats_m_table.pspattempts)) AS psppercent ,
					SUM($stats_m_table.kills)/IF(SUM($stats_m_table.deaths)=0, 1, SUM($stats_m_table.deaths))  AS ratio 
                  FROM $players_table, $stats_m_table
                  $conditions AND time>$averagelimit
                  GROUP BY $stats_m_table.player
                  ORDER BY $sort $order, rating DESC
                  LIMIT $offset, $playerstoshow");

if(file_exists($base_folder."templates/goal_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/goal_stats.htm", 1);
  $tpl2 = new phemplate();
  $tpl2->set_file("template", $base_folder."templates/goal_stats.htm", 1);

  foreach($tableheader as $field => $value) {
    $value_sort = $value . "_sort";
    if($value == $sort) {
      if($order == "ASC") {
        $tpl2->set_var($value, $selectedasc);
        $tpl2->set_var($value_sort, $mainscript."action=$action&sort=$value&order=DESC&server=$server&gametype=$gametype&squad=$squad");
      } else {
        $tpl2->set_var($value, $selecteddesc);
        $tpl2->set_var($value_sort, $mainscript."action=$action&sort=$value&order=ASC&server=$server&gametype=$gametype&squad=$squad");
      }
    } else {
      $tpl2->set_var($value, $unselected);
      $tpl2->set_var($value_sort, $mainscript."action=$action&sort=$value&order=DESC&server=$server&gametype=$gametype&squad=$squad");
    }
  }

  $players = array();
  $row     = array();
  $count   = $offset;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
  
    $rank = GetRank($row["rating"]);
    $row["rank_name"]      = $rank["name"];
    $row["rank_image"]     = $rank["image"];
    $row["rank_thumbnail"] = $rank["thumbnail"];
	
	$row["r_class"] = GetRatioColor($row["ratio"]);

    $row["time"] = FormatTime($row["time"]);
    
    if($gametype == "King of the Hill" || $gametype == "Team King of the Hill") {
      $row["score_1"]     = FormatTime($row["score_1"]);
      $row["avg_score_1"] = FormatTime(round($row["avg_score_1"], 0));
    }
    
    $row["name"] = htmlspecialchars(base64_decode($row["name"]));
	
	foreach($type as $value) {
      $row["$value"] = GetGameLevels($row["id"], $value, "player", "month");
    }
    
    $row["num"]      = $count + 1;
    $players[$count] = $row;
    $count++;
  }

  $tpl2->set_loop("players", $players);
  $tpl2->set_var("base", $base_folder);
  $tpl2->process("template", $action2);
  $stats_m_table = $tpl2->process("", "template", 1);

  $tpl->set_var("multipage",   $multipage);
  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("formurl",     $formurl);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_var("gametype",    $gametype);
  $tpl->set_var("stats_table", $stats_m_table);
  $tpl->set_loop("gametypes",  $gametypes);
  $tpl->set_loop("squads",     $squads);
  $tpl->set_loop("servers",    $servers);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'goal_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">monthlystats/modules/goal_stats.php 1.0.0</div>
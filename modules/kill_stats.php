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
 * Finalized     : 2nd May 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

$check = DBQuery("SELECT * FROM $maps_table");
$chk = mysql_field_name($check, 8); 
if($chk == "server") {
  $content = "ERROR: You need to update your database.<br> 
              Click <a href=\"".$_SERVER['HTTP_REFFERER']."update.php\">Here</a> to update!";
} else {

$tableheader = array(
  0 => "id",
  1 => "name",
  2 => "squad",
  3 => "rating",
  4 => "time",
  5 => "last_played",
  6 => "kills",
  7 => "deaths",
  8 => "murders",
  9 => "suicides",
  10 => "headshots",
  11 => "knifings",
  12 => "sniper_kills",
  13 => "medic_saves",
  14 => "revives",
  15 => "doublekills",
  16 => "awards",
  17 => "avg_kills",
  18 => "avg_deaths",
  19 => "avg_murders",
  20 => "avg_suicides",
  21 => "avg_headshots",
  22 => "avg_knifings",
  23 => "avg_sniper_kills",
  24 => "avg_medic_saves",
  25 => "avg_revives",
  26 => "avg_doublekills",
  27 => "ratio",
  28 => "games",
  29 => "wins",
  30 => "percent_won"
);                             

if(!isset($action2)  || $action2  == "") $action2  = "kill_stats_totals";
if($action2  == "medic_save_stats" AND (!isset($sort)     || $sort     == ""))      $sort     = "medic_saves";
if($action2  == "kill_stats_averages" AND (!isset($sort)     || $sort     == ""))   $sort     = "avg_kills";
if(!isset($sort)     || $sort     == "") $sort     = "rating";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($page)     || $page     == "") $page     = 1;
if(!isset($squad)    || $squad    == "") $squad    = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";
if(!isset($server)   || $server   == "") $server   = -1;

$pageurl = $mainscript."action=$action&action2=$action2&sort=$sort&order=$order&server=$server&gametype=$gametype&squad=$squad";
$formurl = $mainscript."action=$action&action2=$action2&sort=$sort&order=$order";

$squads    = GetSquadsList($squad, 1);
$gametypes = GetGameTypesList($gametype, 1, "SELECT game_type FROM $stats_table GROUP BY game_type ORDER BY game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_table WHERE $servers_table.id=$stats_table.server GROUP BY $servers_table.id ORDER BY $servers_table.name");

$conditions = "WHERE $players_table.id=$stats_table.player AND time>$averagelimit ";

if($squad != -1)       $conditions .= "AND $players_table.squad='$squad' ";
if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";
if($server != -1)      $conditions .= "AND $stats_table.server='$server' ";

$query = DBQuery("SELECT $players_table.id FROM $stats_table, $players_table $conditions GROUP BY $stats_table.player");
$totalplayers = DBNumRows($query);

$offset = ($page - 1) * $playerstoshow;

$multipage = Multipage($pageurl, $totalplayers, $playerstoshow, $page);

$query = DBQuery("SELECT 
                    $players_table.id                                 AS id              ,
                    $players_table.name                               AS name            ,
                    $players_table.squad                              AS squad           ,
                    $players_table.rating                             AS rating          ,
					$players_table.awards + $players_table.wpn_awards AS awards          ,
                    SUM($stats_table.kills)                           AS kills           ,
                    SUM($stats_table.deaths)                          AS deaths          ,
                    SUM($stats_table.murders)                         AS murders         ,
                    SUM($stats_table.suicides)                        AS suicides        ,
                    SUM($stats_table.headshots)                       AS headshots       ,
                    SUM($stats_table.knifings)                        AS knifings        ,
					SUM($stats_table.sniper_kills)                    AS sniper_kills    ,
                    SUM($stats_table.medic_saves)                     AS medic_saves     ,
                    SUM($stats_table.revives)                         AS revives         ,
                    SUM($stats_table.doublekills)                     AS doublekills     ,
                    SUM($stats_table.time)                            AS time            ,
                    SUM($stats_table.games)                           AS games           ,
                    SUM($stats_table.wins)                            AS wins            ,
                    SUM($stats_table.kills)*$timefactor/time          AS avg_kills       ,
                    SUM($stats_table.deaths)*$timefactor/time         AS avg_deaths      ,
                    SUM($stats_table.murders)*$timefactor/time        AS avg_murders     ,
                    SUM($stats_table.suicides)*$timefactor/time       AS avg_suicides    ,
                    SUM($stats_table.headshots)*$timefactor/time      AS avg_headshots   ,
                    SUM($stats_table.knifings)*$timefactor/time       AS avg_knifings    ,
					SUM($stats_table.sniper_kills)*$timefactor/time   AS avg_sniper_kills,
                    SUM($stats_table.medic_saves)*$timefactor/time    AS avg_medic_saves ,
                    SUM($stats_table.revives)*$timefactor/time        AS avg_revives     ,
                    SUM($stats_table.doublekills)*$timefactor/time    AS avg_doublekills ,
                    MAX($stats_table.last_played)                     AS last_played     ,
                    SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio       ,
                    SUM($stats_table.wins)*100/IF(SUM($stats_table.games)=0, 1, SUM($stats_table.games)) AS percent_won  
                  FROM $players_table, $stats_table
                  $conditions 
                  GROUP BY $stats_table.player
                  ORDER BY $sort $order, name ASC
                  LIMIT $offset, $playerstoshow");

if(file_exists($base_folder."templates/kill_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/kill_stats.htm", 1);

  foreach($tableheader as $field => $value) {
    $value_sort = $value . "_sort";
    if($value == $sort) {
      if($order == "ASC") {
        $tpl->set_var($value, $selectedasc);
        $tpl->set_var($value_sort, $mainscript."action=$action&action2=$action2&sort=$value&order=DESC&server=$server&gametype=$gametype&squad=$squad");
      } else {
        $tpl->set_var($value, $selecteddesc);
        $tpl->set_var($value_sort, $mainscript."action=$action&action2=$action2&sort=$value&order=ASC&server=$server&gametype=$gametype&squad=$squad");
      }
    } else {
      $tpl->set_var($value, $unselected);
      $tpl->set_var($value_sort, $mainscript."action=$action&action2=$action2&sort=$value&order=DESC&server=$server&gametype=$gametype&squad=$squad");
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

    $row["time"] = FormatTime($row["time"]);
    
    $row["name"] = htmlspecialchars(base64_decode($row["name"]));
	
	$row["r_class"] = GetRatioColor($row["ratio"]);
	
    $row["num"]      = $count + 1;
    $players[$count] = $row;
    $count++;
  }

  $tpl->set_var("multipage",  $multipage);
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("players",   $players);
  $tpl->set_loop("squads",    $squads);
  $tpl->set_loop("servers",   $servers);
  $tpl->set_loop("gametypes", $gametypes);
  $tpl->process("template",   $action2);
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'kill_stats' does not exist";
}
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/kill_stats.php 1.0.0</div>
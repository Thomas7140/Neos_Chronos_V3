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

$tableheader = array(
  0 => "id",
  1 => "name",
  2 => "time",
  3 => "kills",
  4 => "deaths",
  5 => "murders",
  6 => "suicides",
  7 => "headshots",
  8 => "knifings",
  9 => "medic_saves",
  10 => "revives",
  11 => "doublekills",
  12 => "ratio",
);                             

if(!isset($sort)     || $sort     == "") $sort     = "time";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($page)     || $page     == "") $page     = 1;
if(!isset($gametype) || $gametype == "") $gametype = "All";
if(!isset($server)   || $server   == "") $server   = -1;

$pageurl = $mainscript."action=$action&sort=$sort&order=$order&server=$server&gametype=$gametype";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$gametypes = GetGameTypesList($gametype, 1, "SELECT game_type FROM $stats_table GROUP BY game_type ORDER BY game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_table WHERE $servers_table.id=$stats_table.server GROUP BY $servers_table.id ORDER BY $servers_table.name");

$conditions  = "WHERE $players_table.id=$stats_table.player";
$conditions .= " AND $players_table.squad=$squads_table.id ";

if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";
if($server != -1)      $conditions .= "AND $stats_table.server='$server' ";

$query = DBQuery("SELECT $players_table.id FROM $stats_table, $players_table, $squads_table $conditions GROUP BY $players_table.squad");
$totalsquads = DBNumRows($query);

$offset = ($page - 1) * $playerstoshow;

$multipage = Multipage($pageurl, $totalsquads, $playerstoshow, $page);

$query = DBQuery("SELECT 
                    $squads_table.id                                 AS id              ,
                    $squads_table.name                               AS name            ,
                    $squads_table.tag                                AS tag             ,
                    $squads_table.url                                AS url             ,
                    SUM($stats_table.kills)                          AS kills           ,
                    SUM($stats_table.deaths)                         AS deaths          ,
                    SUM($stats_table.murders)                        AS murders         ,
                    SUM($stats_table.suicides)                       AS suicides        ,
                    SUM($stats_table.headshots)                      AS headshots       ,
                    SUM($stats_table.knifings)                       AS knifings        ,
                    SUM($stats_table.medic_saves)                    AS medic_saves     ,
                    SUM($stats_table.revives)                        AS revives         ,
                    SUM($stats_table.doublekills)                    AS doublekills     ,
                    SUM($stats_table.time)                           AS time            ,
                    SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio 
                  FROM $players_table, $stats_table, $squads_table
                  $conditions
                  GROUP BY $players_table.squad
                  ORDER BY $sort $order
                  LIMIT $offset, $playerstoshow");

if(file_exists($base_folder."templates/squad_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/squad_stats.htm", 1);

  foreach($tableheader as $field => $value) {
    $value_sort = $value . "_sort";
    if($value == $sort) {
      if($order == "ASC") {
        $tpl->set_var($value, $selectedasc);
        $tpl->set_var($value_sort, $mainscript."action=$action&sort=$value&order=DESC&server=$server&gametype=$gametype");
      } else {
        $tpl->set_var($value, $selecteddesc);
        $tpl->set_var($value_sort, $mainscript."action=$action&sort=$value&order=ASC&server=$server&gametype=$gametype");
      }
    } else {
      $tpl->set_var($value, $unselected);
      $tpl->set_var($value_sort, $mainscript."action=$action&sort=$value&order=DESC&server=$server&gametype=$gametype");
    }
  }
  
  $squads  = array();
  $row     = array();
  $count   = $offset;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
	
	
  
    $members = DBQuery("SELECT count(name) as mem, squad FROM $players_table WHERE squad = '".$row["id"]."' GROUP BY squad");
    $memrow = DBFetchArray($members);
    if(!($memrow["mem"])){$row["mem"] = '0';} else {$row["mem"] = $memrow["mem"];}
  
    $row["time"] = FormatTime($row["time"]);
    
    $row["name"] = htmlspecialchars($row["name"]);
    $row["tag"]  = htmlspecialchars($row["tag"]);
	
    $row["r_class"] = GetRatioColor($row["ratio"]);
    
    $row["num"]     = $count + 1;
    $squads[$count] = $row;
    $count++;
  }

  $tpl->set_var("multipage",  $multipage);
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("squads",    $squads);
  $tpl->set_loop("servers",   $servers);
  $tpl->set_loop("gametypes", $gametypes);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'squad_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/squad_stats.php 1.0.0</div>
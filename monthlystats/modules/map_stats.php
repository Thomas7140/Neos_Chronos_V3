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
  2 => "time",
  3 => "last_played",
  4 => "kills",
  5 => "score",
  6 => "hosted",
  7 => "logins",
  8 => "avg_kills",
  9 => "avg_score",
);                             

if(!isset($sort)     || $sort     == "") $sort     = "time";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($page)     || $page     == "") $page     = 1;
if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

$pageurl = $mainscript."action=$action&sort=$sort&order=$order&server=$server&gametype=$gametype";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$gametypes = GetGameTypesList($gametype, 0, "SELECT game_type FROM $maps_table GROUP BY game_type ORDER BY game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $maps_table, $stats_m_table, $mapstats_m_table WHERE $servers_table.id=$stats_m_table.server AND $maps_table.id=$mapstats_m_table.map AND $stats_m_table.id=$mapstats_m_table.record GROUP BY $stats_m_table.server ORDER BY $servers_table.name");

if($gametype == "All") $gametype = $gametypes["0"]["name"];

$conditions  = "WHERE $maps_table.time>$averagelimit ";
$conditions .= "AND $maps_table.id=$mapstats_m_table.map ";
$conditions .= "AND $stats_m_table.id=$mapstats_m_table.record ";

if($server   != -1)    $conditions .= "AND $stats_m_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $maps_table.game_type='$gametype' ";

$query = DBQuery("SELECT $maps_table.id FROM $maps_table, $mapstats_m_table, $stats_m_table $conditions GROUP BY $maps_table.name");
$totalmaps = DBNumRows($query);

$offset = ($page - 1) * $playerstoshow;

$multipage = Multipage($pageurl, $totalmaps, $playerstoshow, $page);

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
                    $maps_table.last_played                                   AS last_played    
                  FROM $maps_table, $mapstats_m_table, $stats_m_table
                  $conditions
                  GROUP BY $mapstats_m_table.map
                  ORDER BY $sort $order, $maps_table.name DESC
                  LIMIT $offset, $playerstoshow");

if(file_exists($base_folder."templates/map_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/map_stats.htm", 1);

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

  $maps  = array();
  $row   = array();
  $count = $offset;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
  
    $row["time"]        = FormatTime($row["time"]);
    
    $row["name"] = htmlspecialchars(base64_decode($row["name"]));
	
    if($row["game_type"] == "King of the Hill" || $row["game_type"] == "Team King of the Hill") {
      $row["score"]     = FormatTime($row["score"]);
      $row["avg_score"] = FormatTime(round($row["avg_score"], 0));
    }
    
    if($row["image"] != "") {
      $row["image"] = "<img src=\""."../".$row["image"]."\" border=\"1\">";
    } else {
      $row["image"] = "<img src=\""."../"."maps/l_none.jpg\" border=\"1\">";
    }
    
    if($row["thumbnail"] != "") {
      $row["thumbnail"] = "<img src=\""."../".$row["thumbnail"]."\" border=\"1\">";
    } else {
      $row["thumbnail"] = "<img src=\""."../"."maps/s_none.jpg\" border=\"1\">";
    }
    
    $row["num"]   = $count + 1;
    $maps[$count] = $row;
    $count++;
  }

  $tpl->set_loop("maps",       $maps);
  $tpl->set_var("multipage",   $multipage);
  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("formurl",     $formurl);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_var("gametype",    $gametype);
  $tpl->set_loop("gametypes",  $gametypes);
  $tpl->set_loop("servers",    $servers);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'map_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">monthlystats/modules/map_stats.php 1.0.0</div>
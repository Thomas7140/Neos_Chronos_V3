<?php
/*
 * Copyright (c) 2005, Peter Jones a.k.a »TÐÖ« Ãzràél
 * All rights reserved.
 *
 * Redistribution and use with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * Redistributions must retain the above copyright notice.
 * File licence.txt must not be removed from the package.
 *
 * Finalized     : 30th April 2005
 * Author        : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

$tableheader = array(
  0 => "id",
  1 => "name",
  2 => "hosted",
  3 => "kills",
  4 => "last_played",
);                             

if(!isset($sort)     || $sort     == "") $sort     = "hosted";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($page)     || $page     == "") $page     = 1;
if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

$pageurl = $mainscript."action=$action&sort=$sort&order=$order&server=$server&gametype=$gametype";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$gametypes = GetGameTypesList($gametype, 0, "SELECT game_type FROM $maps_table GROUP BY game_type ORDER BY game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $maps_table, $stats_table, $mapstats_table WHERE $servers_table.id=$stats_table.server AND $maps_table.id=$mapstats_table.map AND $stats_table.id=$mapstats_table.record GROUP BY $stats_table.server ORDER BY $servers_table.name");

if($gametype == "All") $gametype = $gametypes["0"]["name"];

$conditions  = "WHERE $maps_table.time>$averagelimit ";
$conditions .= "AND $maps_table.id=$mapstats_table.map ";
$conditions .= "AND $stats_table.id=$mapstats_table.record ";

if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $maps_table.game_type='$gametype' ";

$query = DBQuery("SELECT $maps_table.id FROM $maps_table, $mapstats_table, $stats_table $conditions GROUP BY $maps_table.name");
$totalmaps = DBNumRows($query);

$offset = ($page - 1) * $playerstoshow;

$multipage = Multipage($pageurl, $totalmaps, $playerstoshow, $page);

$query = DBQuery("SELECT 
                    $maps_table.id                                            AS id            ,
                    $maps_table.name                                          AS name          ,
                    $maps_table.thumbnail                                     AS thumbnail     ,
                    $maps_table.image                                         AS image         ,
                    $stats_table.server                                       AS server        ,
                    $maps_table.game_type                                     AS game_type     ,
                    $maps_table.hosted                                        AS hosted        ,
                    $maps_table.last_played                                   AS last_played    
                  FROM $maps_table, $mapstats_table, $stats_table
                  $conditions
                  GROUP BY $mapstats_table.map
                  ORDER BY $sort $order, $maps_table.name DESC
                  LIMIT $offset, $playerstoshow");

if(file_exists($base_folder."templates/admin/admin_maps.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_maps.htm", 1);

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
	
	$row["last_played"] = substr($row["last_played"], 0, 10);
    
    $row["name"] = htmlspecialchars(base64_decode($row["name"]));
	
	if(!($row["image"])){$row["lpic"] = "no" AND $row["lp_class"] = "red";} else {$row["lpic"] = "yes" AND $row["lp_class"] = "green";}
	if(!($row["thumbnail"])){$row["thumb"] = "no" AND $row["tn_class"] = "red";} else {$row["thumb"] = "yes" AND $row["tn_class"] = "green";}
    
    $row["num"]   = $count + 1;
    $maps[$count] = $row;
    $count++;
  }

  $tpl->set_loop("maps",       $maps);
  $tpl->set_var("multipage",   $multipage);
  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("formurl",     $formurl);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_var("gametype",    $gametype);
  $tpl->set_loop("gametypes",  $gametypes);
  $tpl->set_loop("servers",    $servers);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_maps' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_maps.php 1.0.0</div>
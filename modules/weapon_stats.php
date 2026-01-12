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
 * Finalized     : 3rd May 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

$tableheader = array(
  0 => "id",
  1 => "name",
  2 => "time",
  3 => "kills",
  4 => "avg_kills",
  5 => "accuracy"
);                             

if(!isset($sort)     || $sort     == "") $sort     = "time";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

$pageurl = $mainscript."action=$action&sort=$sort&order=$order&server=$server&gametype=$gametype";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$gametypes = GetGameTypesList($gametype, 1, "SELECT $stats_table.game_type FROM $stats_table, $weaponstats_table WHERE $stats_table.id=$weaponstats_table.record GROUP BY $stats_table.game_type ORDER BY $stats_table.game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $stats_table, $weaponstats_table WHERE $servers_table.id=$stats_table.server AND $weaponstats_table.record=$stats_table.id GROUP BY $servers_table.id ORDER BY $servers_table.name");

$conditions = "WHERE ";

$conditions .= "$stats_table.id=$weaponstats_table.record ";
$conditions .= "AND $weaponstats_table.weapon=$weapons_table.id ";
if($server   != -1)    $conditions .= "AND $stats_table.server='$server' ";
if($gametype != "All") $conditions .= "AND $stats_table.game_type='$gametype' ";

$query = DBQuery("SELECT 
                    $weapons_table.id                                                      AS id          ,
                    $weapons_table.name                                                    AS name        ,
                    $stats_table.server                                                    AS server      ,
                    $stats_table.game_type                                                 AS game_type   ,
					$weaponstats_table.weapon                                              AS weapon      ,
                    SUM($weaponstats_table.kills)                                          AS kills       ,
                    SUM($weaponstats_table.time)                                           AS time        ,
                    SUM($weaponstats_table.kills)*$timefactor/SUM($weaponstats_table.time) AS avg_kills   ,
                    SUM($weaponstats_table.kills)*100/if(SUM($weaponstats_table.shots)=0, 1, SUM($weaponstats_table.shots)) AS accuracy 
                  FROM $stats_table, $weapons_table, $weaponstats_table
                  $conditions
                  GROUP BY $weapons_table.name
                  ORDER BY $sort $order, name DESC");
				   

if(file_exists($base_folder."templates/weapon_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/weapon_stats.htm", 1);

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

  $weapons = array();
  $row     = array();
  $count   = 0;

  $max_wpn_time = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
	
	$weapon = array($row["weapon"]);

	foreach($weapon as $wvalue) {
	  $sql = DBQuery("SELECT $stats_table.player AS player, 
	                      $stats_table.id AS id, $weaponstats_table.record AS record, 
						  $weaponstats_table.weapon AS weapon, SUM( $weaponstats_table.kills ) AS kills
                        FROM $weaponstats_table, $stats_table
                        WHERE $stats_table.id = $weaponstats_table.record 
					    AND $weaponstats_table.weapon = '$wvalue' 
                        GROUP BY $weaponstats_table.weapon, $stats_table.player
					    HAVING kills > '499'");
	  $num_rows = mysql_num_rows($sql);
	  if(!($num_rows)) {$row["mastered"] = "None";} else {
	    while($wrow = DBFetchArray($sql)) {
	     $row["mastered"] = $num_rows." Soldier(s)";
	    }
      }
	}  
  
    $max_wpn_time += $row["time"];

    $row["time_sec"] = $row["time"];
    $row["time"]     = FormatTime($row["time"]);
    $row["image"]    = $wpn_img[$row["name"]];
    $row["name"]     = htmlspecialchars($row["name"]);
    
    $row["num"]      = $count + 1;
    $weapons[$count] = $row;
    $count++;
  }

  if($max_wpn_time == 0) $max_wpn_time = 1;
  $wpn_time_ratio = 100/$max_wpn_time;
  for($i = 0; $i < $count; $i++) {
    $weapons[$i]["usage_bar_len"] = round($weapons[$i]["time_sec"]*$wpn_time_ratio, 0);
    $weapons[$i]["usage_percent"] = round($weapons[$i]["time_sec"]*$wpn_time_ratio, 0);
  }

  $tpl->set_loop("weapons",   $weapons);
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("gametypes", $gametypes);
  $tpl->set_loop("servers",   $servers);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'weapon_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/weapon_stats.php 1.0.0</div>
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
  0 => "date_played",
  1 => "game_type",
  2 => "mapname",
  3 => "players",
  4 => "winner",
);                             

if(!isset($page)     || $page     == "") $page     = 1;
if(!isset($sort)     || $sort     == "") $sort     = "date_played";
if(!isset($order)    || $order    == "") $order    = "DESC";
if(!isset($server)   || $server   == "") $server   = -1;
if(!isset($gametype) || $gametype == "") $gametype = "All";

$pageurl = $mainscript."action=$action&sort=$sort&order=$order&server=$server&gametype=$gametype";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$gametypes = GetGameTypesList($gametype, 1, "SELECT $games_table.game_type FROM $games_table GROUP BY $games_table.game_type ORDER BY $games_table.game_type");
$servers   = GetServersList($server, 1, "SELECT $servers_table.id, $servers_table.name FROM $servers_table, $games_table WHERE $servers_table.id=$games_table.server GROUP BY $servers_table.id ORDER BY $servers_table.name");


$conditions = "";
if($gametype != "All") $conditions = "AND $games_table.game_type='$gametype' ";
if($server != -1) $conditions .= "AND $games_table.server='$server' ";


$query = DBQuery("SELECT * FROM $games_table WHERE 1=1 $conditions");
$totalGames = DBNumRows($query);

$offset = ($page - 1) * 25;

$Multipage = Multipage($pageurl, $totalGames, 25, $page);

$query = DBQuery("SELECT 
                    $games_table.id            AS id          , 
					$maps_table.name          AS mapname        , 
					$games_table.date_played   AS date_played , 
					COUNT($playergames_table.player) AS players     , 
					$games_table.game_type     AS game_type   , 
					$games_table.winner         AS winner       
                  FROM $games_table, $playergames_table, $maps_table 
                  WHERE $playergames_table.game_id = $games_table.id 
				  AND $maps_table.id = $games_table.map_id 
				  $conditions
                  GROUP BY $playergames_table.game_id
                  ORDER BY $sort $order, date_played DESC 
				  LIMIT $offset, 25");
				   

if(file_exists($base_folder."templates/game_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/game_stats.htm", 1);

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

  $games = array();
  $row   = array();
  $count = 0;

  $max_wpn_time = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }

	$row["mapname"] = base64_decode($row["mapname"]);
	
	if($row["winner"] == '0') $row["winner"] = "Draw";
	if($row["winner"] == '1') $row["winner"] = "Blue Team";
	if($row["winner"] == '2') $row["winner"] = "Red Team";
	
    $row["num"]      = $count + 1;
    $games[$count] = $row;
    $count++;
  }

  $tpl->set_loop("games",   $games);
  $tpl->set_var("Multipage",  $Multipage);
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("gametypes", $gametypes);
  $tpl->set_loop("servers",   $servers);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'game_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/game_stats.php 1.0.2</div>
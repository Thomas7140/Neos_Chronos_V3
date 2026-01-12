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
 * Author        : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 * Finalized     : 1st May 2005
 */           

$ids1 = DBQuery("SELECT $players_table.id, $players_table.name FROM $players_table LIMIT 0, 1");
$row3 = DBFetchArray($ids1);
$id1 = $row3 ? $row3["id"] : 0;
$ids2 = DBQuery("SELECT $players_table.id, $players_table.name FROM $players_table LIMIT 1, 1");
$row4 = DBFetchArray($ids2);
$id2 = $row4 ? $row4["id"] : $id1;
if(!isset($player1)) $player1 = $id1;
if(!isset($player2)) $player2 = $id2;

$formurl = $mainscript."action=$action&first=$player1&second=$player2";

$players1 = GetPlayers1List($player1, 1, "SELECT $players_table.id, $players_table.name FROM $players_table");
$players2 = GetPlayers2List($player1, 1, "SELECT $players_table.id, $players_table.name FROM $players_table");


$conditions = "";
if($player1 != -1) $conditions .= "WHERE $players_table.id='$player1' ";
if($player2 != -1) $conditions .= "WHERE $players_table.id='$player2' ";

$query_p1 = DBQuery("SELECT 
                    $players_table.id                                AS id               ,
                    $players_table.name                              AS name             ,
                    $players_table.rating                            AS rating           ,
                    SUM($stats_table.kills)                          AS kills            ,
                    SUM($stats_table.deaths)                         AS deaths           ,
                    SUM($stats_table.murders)                        AS murders          ,
                    SUM($stats_table.suicides)                       AS suicides         ,
                    SUM($stats_table.headshots)                      AS headshots        ,
                    SUM($stats_table.knifings)                       AS knifings         ,
                    SUM($stats_table.medic_saves)                    AS medic_saves      ,
                    SUM($stats_table.revives)                        AS revives          ,
					SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio            ,
                    SUM($stats_table.games)                          AS games            ,
                    SUM($stats_table.wins)                           AS wins             
                  FROM $players_table, $stats_table
                  WHERE $stats_table.player = $players_table.id 
				  AND $players_table.id = '$player1'
                  GROUP BY $stats_table.player");
				  
$query_p2 = DBQuery("SELECT 
                    $players_table.id                                AS id2               ,
                    $players_table.name                              AS name2             ,
                    $players_table.rating                            AS rating2          ,
                    SUM($stats_table.kills)                          AS kills2            ,
                    SUM($stats_table.deaths)                         AS deaths2           ,
                    SUM($stats_table.murders)                        AS murders2          ,
                    SUM($stats_table.suicides)                       AS suicides2         ,
                    SUM($stats_table.headshots)                      AS headshots2       ,
                    SUM($stats_table.knifings)                       AS knifings2         ,
                    SUM($stats_table.medic_saves)                    AS medic_saves2      ,
                    SUM($stats_table.revives)                        AS revives2          ,
					SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio2            ,
                    SUM($stats_table.games)                          AS games2            ,
                    SUM($stats_table.wins)                           AS wins2             
                  FROM $players_table, $stats_table
                  WHERE $stats_table.player = $players_table.id 
				  AND $players_table.id = '$player2'
                  GROUP BY $stats_table.player");

if(file_exists($base_folder."templates/compare_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/compare_stats.htm", 1);

  $row = DBFetchArray($query_p1);
  $row2 = DBFetchArray($query_p2);
  
  $row_defaults = array(
    "id" => $player1,
    "name" => "",
    "rating" => 0,
    "kills" => 0,
    "deaths" => 0,
    "murders" => 0,
    "suicides" => 0,
    "headshots" => 0,
    "knifings" => 0,
    "medic_saves" => 0,
    "revives" => 0,
    "ratio" => 0,
    "games" => 0,
    "wins" => 0
  );
  $row2_defaults = array(
    "id2" => $player2,
    "name2" => "",
    "rating2" => 0,
    "kills2" => 0,
    "deaths2" => 0,
    "murders2" => 0,
    "suicides2" => 0,
    "headshots2" => 0,
    "knifings2" => 0,
    "medic_saves2" => 0,
    "revives2" => 0,
    "ratio2" => 0,
    "games2" => 0,
    "wins2" => 0
  );
  $row = is_array($row) ? array_merge($row_defaults, $row) : $row_defaults;
  $row2 = is_array($row2) ? array_merge($row2_defaults, $row2) : $row2_defaults;
  
  $row["name"] = $row["name"] !== "" ? htmlspecialchars(base64_decode($row["name"])) : "";
  
  $row2["name2"] = $row2["name2"] !== "" ? htmlspecialchars(base64_decode($row2["name2"])) : "";
  
  $kills_total = $row["kills"] + $row2["kills2"];
  if ($kills_total == 0) $kills_total = 1;
  $row["per_kills"] = ($row["kills"]*180)/$kills_total;
  $row2["per_kills2"] = ($row2["kills2"])*180/$kills_total;
  if($row["per_kills"] > $row2["per_kills2"]) {$row["colork"] = "green_bar";}
  else {$row["colork"] = "red_bar";}
  if($row2["per_kills2"] > $row["per_kills"]) {$row["colork2"] = "green_bar";}
  else {$row["colork2"] = "red_bar";}
  $deaths_total = $row["deaths"] + $row2["deaths2"];
  if ($deaths_total == 0) $deaths_total = 1;
  $row["per_deaths"] = ($row["deaths"]*180)/$deaths_total;
  $row2["per_deaths2"] = ($row2["deaths2"])*180/$deaths_total;
  if($row["per_deaths"] < $row2["per_deaths2"]) {$row["colord"] = "green_bar";}
  else {$row["colord"] = "red_bar";}
  if($row2["per_deaths2"] < $row["per_deaths"]) {$row["colord2"] = "green_bar";}
  else {$row["colord2"] = "red_bar";}
  $knifings_total = $row["knifings"] + $row2["knifings2"];
  if ($knifings_total == 0) $knifings_total = 1;
  $row["per_knifings"] = ($row["knifings"]*180)/$knifings_total;
  $row2["per_knifings2"] = ($row2["knifings2"])*180/$knifings_total;
  if($row["per_knifings"] > $row2["per_knifings2"]) {$row["colorkn"] = "green_bar";}
  else {$row["colorkn"] = "red_bar";}
  if($row2["per_knifings2"] > $row["per_knifings"]) {$row["colorkn2"] = "green_bar";}
  else {$row["colorkn2"] = "red_bar";}
  $headshots_total = $row["headshots"] + $row2["headshots2"];
  if ($headshots_total == 0) $headshots_total = 1;
  $row["per_headshots"] = ($row["headshots"]*180)/$headshots_total;
  $row2["per_headshots2"] = ($row2["headshots2"])*180/$headshots_total;
  if($row["per_headshots"] > $row2["per_headshots2"]) {$row["colorh"] = "green_bar";}
  else {$row["colorh"] = "red_bar";}
  if($row2["per_headshots2"] > $row["per_headshots"]) {$row["colorh2"] = "green_bar";}
  else {$row["colorh2"] = "red_bar";}
  $suicides_total = $row["suicides"] + $row2["suicides2"];
  if ($suicides_total == 0) $suicides_total = 1;
  $row["per_suicides"] = ($row["suicides"]*180)/$suicides_total;
  $row2["per_suicides2"] = ($row2["suicides2"])*180/$suicides_total;
  if($row["per_suicides"] < $row2["per_suicides2"]) {$row["colors"] = "green_bar";}
  else {$row["colors"] = "red_bar";}
  if($row2["per_suicides2"] < $row["per_suicides"]) {$row["colors2"] = "green_bar";}
  else {$row["colors2"] = "red_bar";}
  $murders_total = $row["murders"] + $row2["murders2"];
  if ($murders_total == 0) $murders_total = 1;
  $row["per_murders"] = ($row["murders"]*180)/$murders_total;
  $row2["per_murders2"] = ($row2["murders2"])*180/$murders_total;
  if($row["per_murders"] < $row2["per_murders2"]) {$row["colorm"] = "green_bar";}
  else {$row["colorm"] = "red_bar";}
  if($row2["per_murders2"] < $row["per_murders"]) {$row["colorm2"] = "green_bar";}
  else {$row["colorm2"] = "red_bar";}
  $medic_saves_total = $row["medic_saves"] + $row2["medic_saves2"];
  if ($medic_saves_total == 0) $medic_saves_total = 1;
  $row["per_medic_saves"] = ($row["medic_saves"]*180)/$medic_saves_total;
  $row2["per_medic_saves2"] = ($row2["medic_saves2"])*180/$medic_saves_total;
  if($row["per_medic_saves"] > $row2["per_medic_saves2"]) {$row["colorms"] = "green_bar";}
  else {$row["colorms"] = "red_bar";}
  if($row2["per_medic_saves2"] > $row["per_medic_saves"]) {$row["colorms2"] = "green_bar";}
  else {$row["colorms2"] = "red_bar";}
  $revives_total = $row["revives"] + $row2["revives2"];
  if ($revives_total == 0) $revives_total = 1;
  $row["per_revives"] = ($row["revives"]*180)/$revives_total;
  $row2["per_revives2"] = ($row2["revives2"])*180/$revives_total;
  if($row["per_revives"] > $row2["per_revives2"]) {$row["colorr"] = "green_bar";}
  else {$row["colorr"] = "red_bar";}
  if($row2["per_revives2"] > $row["per_revives"]) {$row["colorr2"] = "green_bar";}
  else {$row["colorr2"] = "red_bar";}
  $ratio_total = $row["ratio"] + $row2["ratio2"];
  if ($ratio_total == 0) $ratio_total = 1;
  $row["per_ratio"] = ($row["ratio"]*180)/$ratio_total;
  $row2["per_ratio2"] = ($row2["ratio2"])*180/$ratio_total;
  if($row["per_ratio"] > $row2["per_ratio2"]) {$row["colorkr"] = "green_bar";}
  else {$row["colorkr"] = "red_bar";}
  if($row2["per_ratio2"] > $row["per_ratio"]) {$row["colorkr2"] = "green_bar";}
  else {$row["colorkr2"] = "red_bar";}
  $wins_total = $row["wins"] + $row2["wins2"];
  if ($wins_total < 1) $wins_total = 1;
  $row["per_wins"] = ($row["wins"]*180)/$wins_total;
  $row2["per_wins2"] = ($row2["wins2"])*180/$wins_total;
  if($row["per_wins"] > $row2["per_wins2"]) {$row["colorw"] = "green_bar";}
  else {$row["colorw"] = "red_bar";}
  if($row2["per_wins2"] > $row["per_wins"]) {$row["colorw2"] = "green_bar";}
  else {$row["colorw2"] = "red_bar";}
  $rating_total = $row["rating"] + $row2["rating2"];
  if ($rating_total == 0) $rating_total = 1;
  $row["per_rating"] = ($row["rating"]*180)/$rating_total;
  $row2["per_rating2"] = ($row2["rating2"])*180/$rating_total;
  if($row["per_rating"] > $row2["per_rating2"]) {$row["colorra"] = "green_bar";}
  else {$row["colorra"] = "red_bar";}
  if($row2["per_rating2"] > $row["per_rating"]) {$row["colorra2"] = "green_bar";}
  else {$row["colorra2"] = "red_bar";}
  
  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }
  foreach($row2 as $field => $value) {
    $tpl->set_var($field, $value);
  }

  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("player1",   $players1);
  $tpl->set_loop("player2",   $players2);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'compare_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/compare_stats.php 1.0.0</div>
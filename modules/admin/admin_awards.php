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
 * Finalized     : 28th April 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(isset($opt)) $opt   = " checked";

$fields = array(
  0 => "kills",
  1 => "deaths",
  2 => "murders",
  3 => "suicides",
  4 => "knifings",
  5 => "sniper_kills",
  6 => "headshots",
  7 => "medic_saves",
  8 => "revives",
  9 => "psp_attempts",
  10 => "psp_takeovers",
  11 => "double_kills",
  12 => "time_played",
  13 => "games_completed",
  14 => "zone_time",
  15 => "tkoth_zone_attack_kills",
  16 => "tkoth_zone_defend_kills",
  17 => "flags_captured",
  18 => "flags_saved",
  19 => "flag_runners_killed",
  20 => "targets_destroyed",
  21 => "sd_ad_attack_kills",
  22 => "sd_ad_defend_kills",
  23 => "team_games_won",
);

$formurl = $mainscript."action=$action";

$query = DBQuery("SELECT * FROM $awards_table WHERE type = 'A' ORDER BY field, value ASC");
$num = DBQuery("SELECT count(type) as num FROM $awards_table WHERE type = 'A' GROUP BY type");

if(file_exists($base_folder."templates/admin/admin_awards.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_awards.htm", 1);
  
  $row = DBFetchArray($num);
  if(!($row)) {$row["num"] = 0;}

  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }

$awards = array();
$row    = array();
$count  = 0;

while($row = DBFetchArray($query)) {
  if($count % 2 == 0){
    $row["class"] = $rowclass1;
  } else {
    $row["class"] = $rowclass2;
  }

  $row["opt"] = $opt;
  
  $field_select = "<SELECT name=\"aw".$row["id"]."[]\" class=\"cinput\">";
  foreach($fields as $field => $value) {
    if($row["field"] == $value) {
      $field_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $field_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $field_select .= "</SELECT>";
  $row["field_select"] = $field_select;
  $awards[$count] = $row;
  $count++;
}

$field_select = "<SELECT name=\"new_field\" class=\"cinput\">";
foreach($fields as $field => $value) {
  $field_select .= "<OPTION value=\"$value\">$value</OPTION>";
}

$field_select .= "</SELECT>";


  $tpl->set_var("mainscript",   $mainscript);
  $tpl->set_var("adminaction",  $adminaction);
  $tpl->set_var("formurl",      $formurl);
  $tpl->set_var("field_select", $field_select);
  $tpl->set_loop("awards",      $awards);
  $tpl->set_var("base",         $base_folder);
  $tpl->process("template", "main");
  $content = $tpl->process("", "main", 1);
} else {
  $content .= "Error: template 'admin/admin_awards' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_awards.php 1.0.0</div>
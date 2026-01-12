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
 * Finalized     : 28th April 2005
 * Author        : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(isset($opt)) $opt   = " checked";

$fields = array(
   1 => "car15_kills",
   2 => "g36_kills",
   3 => "g3_kills",
   4 => "m60_kills",
   5 => "car15203_kills",
   6 => "m16_kills",
   7 => "m16203_kills",
   8 => "mp5_kills",
   9 => "m249saw_kills",
  10 => "m240_kills",
  11 => "car15203_grenade_kills",
  12 => "m16203_grenade_kills",
  13 => "colt_kills",
  14 => "m9_kills",
  15 => "m21_kills",
  16 => "psg1_kills",
  17 => "m24_kills",
  18 => "mcrt_kills",
  19 => "barrett_kills",
  20 => "shotgun_kills",
  21 => "grenade_kills",
  22 => "at4_kills",
  23 => "claymore_kills",
  24 => "car15_accuracy",
  25 => "g36_accuracy",
  26 => "g3_accuracy",
  27 => "m60_accuracy",
  28 => "car15203_accuracy",
  29 => "m16_accuracy",
  30 => "m16203_accuracy",
  31 => "mp5_accuracy",
  32 => "m249saw_accuracy",
  33 => "m240_accuracy",
  34 => "car15203_grenade_accuracy",
  35 => "m16203_grenade_accuracy",
  36 => "colt_accuracy",
  37 => "m9_accuracy",
  38 => "m21_accuracy",
  39 => "psg1_accuracy",
  40 => "m24_accuracy",
  41 => "mcrt_accuracy",
  42 => "barrett_accuracy",
  43 => "shotgun_accuracy",
  44 => "grenade_accuracy",
  45 => "at4_accuracy",
  46 => "claymore_accuracy",
  47 => "knife_kills",
  48 => "knife_accuracy"
);

$formurl = $mainscript."action=$action";

$query = DBQuery("SELECT * FROM $awards_table WHERE type = 'W' ORDER BY value ASC");
$num = DBQuery("SELECT count(type) as num FROM $awards_table WHERE type = 'W' GROUP BY type");

if(file_exists($base_folder."templates/admin/admin_weapon_awards.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_weapon_awards.htm", 1);

  $row = DBFetchArray($num);
  if(!($row)) {$row["num"] = 0;}

  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }

$wpnawards = array();
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
  $wpnawards[$count] = $row;
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
  $tpl->set_loop("wpnawards",   $wpnawards);
  $tpl->set_var("base",         $base_folder);
  $tpl->process("template", "main");
  $content = $tpl->process("", "main", 1);
} else {
  $content .= "Error: template 'admin/admin_weapon_awards' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_weapon_awards.php 1.0.0</div>
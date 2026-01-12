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

$query = DBQuery("SELECT * FROM $ranks_table ORDER BY rating ASC");

if(file_exists($base_folder."templates/ranks.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/ranks.htm");

  $ranks = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
  
    $row["num"]    = $count + 1;
    $ranks[$count] = $row;
    $count++;
  }
  
  foreach($points as $field => $value) {
    $tpl->set_var("points_".$field, $points[$field]);
  }

  $weapon_points = array();
  $row           = array();
  $count         = 0;
  
  foreach($wpn_points as $field => $value) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
    $row["weapon"] = htmlspecialchars($field);
    $row["points"] = $wpn_points[$field];
    $weapon_points[$count] = $row;
    $count++;
  }
  
  $tpl->set_var("mainscript",     $mainscript);
  $tpl->set_var("base",           $base_folder);
  $tpl->set_loop("ranks",         $ranks);
  $tpl->set_loop("weapon_points", $weapon_points);
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'ranks' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/ranks.php 1.0.0</div>
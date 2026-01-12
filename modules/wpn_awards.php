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
 * Finalized     : 10th May 2005
 */

$query = DBQuery("SELECT * FROM $awards_table WHERE type = 'W' ORDER BY name ASC");

if(file_exists($base_folder."templates/awards.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/awards.htm");

  $awards = array();
  $row    = array();
  $count  = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
  
    $row["num"]     = $count + 1;
    $awards[$count] = $row;
    $count++;
  }
  
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_loop("awards",    $awards);
  $tpl->set_var("base",       $base_folder);
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'awards' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/wpn_awards.php 1.0.0</div>
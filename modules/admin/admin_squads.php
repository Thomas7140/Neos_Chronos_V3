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
 * Finalized     : 29th April 2005
 * Author        : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */
 
$query = DBQuery("SELECT * FROM $squads_table");

if(file_exists($base_folder."templates/admin/admin_squads.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_squads.htm", 1);

  $squads  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
	
	$members = DBQuery("SELECT count(name) as mem, squad FROM $players_table WHERE squad = '".$row["id"]."' GROUP BY squad");
	$memrow = DBFetchArray($members);
	if(!$memrow || !isset($memrow["mem"])) {$row["mem"] = '0';} else {$row["mem"] = $memrow["mem"];}
	
    if(empty($row["tag"])) {$row["tag"] = "no"; $row["tg_class"] = "red";} else {$row["tag"] = "yes"; $row["tg_class"] = "green";}
	if(empty($row["url"])) {$row["url"] = "no"; $row["ur_class"] = "red";} else {$row["url"] = "yes"; $row["ur_class"] = "green";}
	
    
    $row["num"]   = $count + 1;
    $squads[$count] = $row;
    $count++;
  }

  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_loop("squads",     $squads);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_squads' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_squads.php 1.0.0</div>
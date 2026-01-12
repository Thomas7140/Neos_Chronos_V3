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
 * Finalized     : 1st MAy 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(isset($opt)) $opt   = " checked";

$formurl = $mainscript."action=$action";

$query = DBQuery("SELECT * FROM $ranks_table ORDER BY rating ASC");
$ranks = array();
$row    = array();
$count  = 0;

while($row = DBFetchArray($query)) {
  if($count % 2 == 0){
    $row["class"] = $rowclass1;
  } else {
    $row["class"] = $rowclass2;
  }
  
  $row["opt"] = $opt;
   
  $ranks[$count] = $row;
  $count++;
}


if(file_exists($base_folder."templates/admin/admin_ranks.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template",    $base_folder."templates/admin/admin_ranks.htm", 1);
  $tpl->set_var("mainscript",   $mainscript);
  $tpl->set_var("adminaction",  $adminaction);
  $tpl->set_var("formurl",      $formurl);
  $tpl->set_loop("ranks",       $ranks);
  $tpl->set_var("base",         $base_folder);
  $tpl->process("template", "main");
  $content = $tpl->process("", "main", 1);
} else {
  $content .= "Error: template 'admin/admin_ranks' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_ranks.php 1.0.0</div>
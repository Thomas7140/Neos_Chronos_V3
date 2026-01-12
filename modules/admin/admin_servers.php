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

$query = DBQuery("SELECT * FROM $servers_table ORDER BY id ASC");
$server = array();
$row   = array();
$count = 0;

while($row = DBFetchArray($query)) {
  if($count % 2 == 0){
    $row["class"] = $rowclass2;
  } else {
    $row["class"] = $rowclass1;
  }
  
  $server[$count] = $row;
  $count++;
}
  
if(file_exists($base_folder."templates/admin/admin_servers.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template",   $base_folder."templates/admin/admin_servers.htm", 1);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_loop("server",     $server);
  $tpl->set_var("base",        $base_folder);
  $tpl->process("template", "main");
  $content = $tpl->process("", "main", 1);

} else {
  $content .= "Error: template 'admin/admin_servers' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_servers.php 1.0.0</div>
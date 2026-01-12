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

$mtime1     = explode(" ", microtime());
$starttime  = $mtime1[1] + $mtime1[0];
$importtime = 0;
$data       = "";
/*$handle=opendir($base_folder."upload");
while(false!==($filename = readdir($handle))) {
  if($filename != "." && $filename != "..") {
    $statsfilename = $base_folder."upload/".$filename;
    if(file_exists($statsfilename) && $importtime < 10) {
      $statslines = file($statsfilename);
      $numlines   = count($statslines);
      for($i = 0; $i < $numlines; $i++) {
        $data .= $statslines[$i];
      }
      ImportStats($data);
      $mtime2     = explode(" ", microtime());
      $endtime    = $mtime2[1] + $mtime2[0];
      $importtime = number_format($endtime - $starttime, 0);
      @unlink($statsfilename);
    }
  }
}
closedir($handle);*/

if(file_exists($base_folder."templates/admin/admin_start.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_start.htm");
  $tpl->set_var("base", $base_folder);
  $content = $tpl->process("", "template", 1);
} else {
  $content = "Error: template 'start' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_start.php 1.0.0</div>
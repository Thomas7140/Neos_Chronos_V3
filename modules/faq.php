<?php
/*
 * Copyright (c) 2005, Peter Jones a.k.a �T�֫ �zr��l
 * All rights reserved.
 *
 * Redistribution and use with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * Redistributions must retain the above copyright notice.
 * File licence.txt must not be removed from the package.
 *
 * Author        : Peter Jones a.k.a �T�֫ �zr��l
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 * Finalized     : 13th June 2005
 */  

if(file_exists($base_folder."templates/faq.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template",  $base_folder."templates/faq.htm", 1);
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl ?? '');
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("players",   $players ?? []);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'faq' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/faq.php 1.0.0</div>
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
 * Finalized     : 13th June 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

if(file_exists($base_folder."templates/about.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/about.htm");
  $tpl->set_var("base", $base_folder);
  $content = $tpl->process("", "template", 1);
} else {
  $content = "Error: template 'about' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">monthlystats/modules/about.php 1.0.0</div>
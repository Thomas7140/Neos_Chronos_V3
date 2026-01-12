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
 * Finalized     : 1st May 2005
 * Author        : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */


if(file_exists($base_folder."templates/admin/admin_hof.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_hof.htm", 1);
  
  if($_POST['games'] != "Ignore"){
    if($row["games"] = "Played"){$type = "P";} else {$type = "W";}
    $row["games"] = "<input type=\"hidden\" name=\"cri[games_type]\" value=\"$type\">";
    $row["crit1"] = "<td class=\"tablerow2\">Team Games ".$_POST['games'].":</td>
					   <td class=\"tablerow1\"><input type=\"text\" size=\"10\" name=\"cri[team_games]\"></td>";
  } else {
    $row["games"] = "<td colspan=\"2\"></td>";
    $row["crit1"] = "<td></td>";
  }
  if($_POST['ratingpts'] != "Ignore"){
    $row["crit2"] = "<td class=\"tablerow2\">Rating Pts:</td>
					   <td class=\"tablerow1\"><input type=\"text\" size=\"10\" name=\"cri[rating_pts]\"></td>";
  } else {
    $row["crit2"] = "<td></td>";
  }
  if($_POST['ptime'] != "Ignore"){
    $row["crit3"] = "<td class=\"tablerow2\">Time Played:</td>
					   <td class=\"tablerow1\"><input type=\"text\" size=\"10\" name=\"cri[time_played]\"></td>";
  } else {
    $row["crit3"] = "<td></td>";
  }
  if($_POST['awards'] != "Ignore"){
    $row["crit4"] = "<td class=\"tablerow2\">Awards Gained:</td>
					   <td class=\"tablerow1\"><input type=\"text\" size=\"10\" name=\"cri[awards]\"></td>";
  } else {
    $row["crit4"] = "<td></td>";
  }
  if($_POST['wpnawards'] != "Ignore"){
    $row["crit5"] = "<td class=\"tablerow2\">Weapon Awards Gained:</td>
					   <td class=\"tablerow1\"><input type=\"text\" size=\"10\" name=\"cri[wpn_awards]\"></td>";
  } else {
    $row["crit5"] = "<td></td>";
  }
  if($_POST['kills'] != "Ignore"){
    $row["crit6"] = "<td class=\"tablerow2\">Kills Gained:</td>
					   <td class=\"tablerow1\"><input type=\"text\" size=\"10\" name=\"cri[kills]\"></td>";
  } else {
    $row["crit6"] = "<td></td>";
  }
  
  foreach($row as $field => $value) {
      $tpl->set_var($field, $value);
    }

  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("base",        $base_folder);
  $tpl->process("template", "set_criteria");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_hof' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_set_hof.php 1.0.0</div>
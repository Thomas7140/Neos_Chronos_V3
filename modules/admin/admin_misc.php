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

if($act == "")    $act = "";
if($act === '0')  $act = "<span class=\"red\">$act Players have been pruned!!!</span>";
if($act > 0)      $act = "<span class=\"green\">$act Players have been pruned.</span>";
if($act === '-1') $act = "<span class=\"green\">All Players and Player stats deleted.</span>";
if($act === '-2') $act = "<span class=\"green\">Your Babstats.Chronos has been reset.</span>";
if($act === '-3') $act = "<span class=\"green\">Chronos database repaired.</span>";
if($act === '-4') $act = "<span class=\"green\">Monthly stats have been reset.</span>";
if($act === '-5') $act = "<span class=\"red\">Monthly stats have already been reset.</span>";
if($act === '-6') $act = "<span class=\"green\">Players Merged Successfully</span>";
if($act === '-7') $act = "<span class=\"red\">Invalid Player!</span>";
if($act === '-8') $act = "<span class=\"green\">Database Backup Successful!</span>";
if($act === '-9') $act = "<span class=\"red\">Database Backup Failed!</span>";

if(file_exists($base_folder."templates/admin/admin_misc.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_misc.htm", 1);

  $query = DBQuery("SELECT SUBSTRING(last_played, 1, 7) AS month FROM $stats_m_table GROUP BY month");
	
  $months  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
    
	$exDate = explode("-", $row["month"]);
	switch($exDate[1]) {
		case '01': $row["fMonth"] = "Jan ".$exDate[0]; break;
		case '02': $row["fMonth"] = "Feb ".$exDate[0]; break;
		case '03': $row["fMonth"] = "Mar ".$exDate[0]; break;
		case '04': $row["fMonth"] = "Apr ".$exDate[0]; break;
		case '05': $row["fMonth"] = "May ".$exDate[0]; break;
		case '06': $row["fMonth"] = "Jun ".$exDate[0]; break;
		case '07': $row["fMonth"] = "Jul ".$exDate[0]; break;
		case '08': $row["fMonth"] = "Aug ".$exDate[0]; break;
		case '09': $row["fMonth"] = "Sep ".$exDate[0]; break;
		case '10': $row["fMonth"] = "Oct ".$exDate[0]; break;
		case '11': $row["fMonth"] = "Nov ".$exDate[0]; break;
		case '12': $row["fMonth"] = "Dec ".$exDate[0]; break;
	}
	
    $row["num"]   = $count + 1;
    $months[$count] = $row;
    $count++;
  }

  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_loop("months",    $months);
  $tpl->set_var("act",         $act);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_misc' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_misc.php 1.0.0</div>
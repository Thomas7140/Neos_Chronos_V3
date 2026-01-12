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
 
$tfields = array(
  0 => "Ignore",
  1 => "less than 10 Hours",
  2 => "more than 10 Hours",
  3 => "more than 50 Hours",
  4 => "more than 100 Hours",
  5 => "more than 150 Hours",
  6 => "more than 200 Hours",
  7 => "more than 250 Hours",
  8 => "more than 300 Hours",
  9 => "more than 350 Hours",
 10 => "more than 400 Hours",
 11 => "more than 450 Hours",
 12 => "more than 500 Hours",
);

$kfields = array(
  0 => "Ignore",
  1 => "0 - 250",
  2 => "250 - 500",
  3 => "500 - 750",
  4 => "750 - 1000",
  5 => "1000 - 3000",
  6 => "3000 - 5000",
  7 => "5000 - 10000",
  8 => "10000 - 15000",
  9 => "15000 - 20000",
 10 => "20000 - 30000",
 11 => "30000 - 40000",
 12 => "40000 - 50000",
);

if(!isset($squadid)) $squadid   = "Ignore";
if(!isset($time))    $time      = "Ignore";
if(!isset($kill))    $kill      = "Ignore";
if(!isset($pname))   $pname      = "Ignore";

if($squadid != "Ignore") {
  $condition = "AND $players_table.squad = '$squadid' ";
} else {
  $condition = "";
}

if($time != "Ignore") {
  $dec = trim(substr($time, 0, 9));
  if($dec == "more_than"){
	$condition2 = "HAVING ptime > ((60*".ereg_replace("_", "", trim(substr($time, 10, 3))).")*60) ";
  } else {
    $condition2 = "HAVING ptime < ((60*".ereg_replace("_", "", trim(substr($time, 10, 3))).")*60) ";
  }
}

if(isset($kill)){
  if($kill != "Ignore") {
    if($time != "Ignore"){
      switch($kill) {
	    case '0-250'       : $condition2 .= "AND (kills < 251)"                    ;  break;
	    case '250-500'     : $condition2 .= "AND (kills > 249 AND kills < 501)"    ;  break;
	    case '500-750'     : $condition2 .= "AND (kills > 499 AND kills < 751)"    ;  break;
	    case '750-1000'    : $condition2 .= "AND (kills > 749 AND kills < 1001)"   ;  break;
	    case '1000-3000'   : $condition2 .= "AND (kills > 999 AND kills < 3001)"   ;  break;
	    case '3000-5000'   : $condition2 .= "AND (kills > 2999 AND kills < 5001)"  ;  break;
	    case '5000-10000'  : $condition2 .= "AND (kills > 4999 AND kills < 10001)" ;  break;
	    case '10000-15000' : $condition2 .= "AND (kills > 9999 AND kills < 15001)" ;  break;
	    case '15000-20000' : $condition2 .= "AND (kills > 14999 AND kills < 20001)";  break;
	    case '20000-30000' : $condition2 .= "AND (kills > 19999 AND kills < 30001)";  break;
	    case '30000-40000' : $condition2 .= "AND (kills > 29999 AND kills < 40001)";  break;
	    case '40000-50000' : $condition2 .= "AND (kills > 39999 AND kills < 50001)";  break;
	  }
	} else {
	  switch($kill) {
	    case '0-250'       : $condition2 = "HAVING (kills < 251)"                    ;  break;
	    case '250-500'     : $condition2 = "HAVING (kills > 249 AND kills < 501)"    ;  break;
	    case '500-750'     : $condition2 = "HAVING (kills > 499 AND kills < 751)"    ;  break;
	    case '750-1000'    : $condition2 = "HAVING (kills > 749 AND kills < 1001)"   ;  break;
	    case '1000-3000'   : $condition2 = "HAVING (kills > 999 AND kills < 3001)"   ;  break;
	    case '3000-5000'   : $condition2 = "HAVING (kills > 2999 AND kills < 5001)"  ;  break;
	    case '5000-10000'  : $condition2 = "HAVING (kills > 4999 AND kills < 10001)" ;  break;
	    case '10000-15000' : $condition2 = "HAVING (kills > 9999 AND kills < 15001)" ;  break;
	    case '15000-20000' : $condition2 = "HAVING (kills > 14999 AND kills < 20001)";  break;
	    case '20000-30000' : $condition2 = "HAVING (kills > 19999 AND kills < 30001)";  break;
	    case '30000-40000' : $condition2 = "HAVING (kills > 29999 AND kills < 40001)";  break;
	    case '40000-50000' : $condition2 = "HAVING (kills > 39999 AND kills < 50001)";  break;
	  }
	}
  }
}

$query = DBQuery("SELECT $players_table.*, $stats_table.player, SUM($stats_table.kills) as kills, SUM($stats_table.time) as ptime 
                    FROM $players_table, $stats_table 
				  WHERE $stats_table.player = $players_table.id
			      $condition
				  GROUP BY $players_table.id 
				  $condition2");
				  
$squad = DBQuery("SELECT * FROM $squads_table");

if(file_exists($base_folder."templates/admin/admin_players.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_players.htm", 1);

  $players  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($query)) {
    
    $pname  = htmlspecialchars($pname);
    $row["name"] = htmlspecialchars(base64_decode($row["name"]));
    
    if($pname == "") {
      $row["num"]      = $count + 1;
      $players[$count] = $row;
      $count++;
    } elseif(strstr($row["name"], $pname)) {
      $row["num"]      = $count + 1;
      $players[$count] = $row;
      $count++;
    }
  }
  
  $squads  = array();
  $row     = array();
  $count   = 0;

  while($row = DBFetchArray($squad)) {
	    
    $row["num"]   = $count + 1;
    $squads[$count] = $row;
    $count++;
  }

  $time_select = "<SELECT name=\"time\" class=\"cinput\">";
  foreach($tfields as $field => $value) {
    if($row["field"] == $value) {
      $time_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $time_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $kill_select = "<SELECT name=\"kill\" class=\"cinput\">";
  foreach($kfields as $field => $value) {
    if($row["field"] == $value) {
      $kill_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $kill_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }


  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_var("time_select", $time_select);
  $tpl->set_var("kill_select", $kill_select);
  $tpl->set_loop("players",    $players);
  $tpl->set_loop("squads",     $squads);
  $tpl->process("template", "advanced_search");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_players' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_players_search.php 1.0.0</div>
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
 * Finalized     : 12th May 2005
 */                            

if(!isset($month)   || $month   == "") $month   = "_Alltime";

if($month != "_Alltime") {
  $mth        = substr($month, 0, 2); 
  $yr         = substr($month, 3, 4);
  $row["date"] = $mth." ".$yr;
  $conditions = "WHERE month_gained = '$mth' AND year_gained = '$yr'";
} else {
  $al         = substr($month, 1, 7);
  $row["date"] = $al;
  $conditions = "WHERE year_gained = '$al'";
}

$months_sql = DBQuery("SELECT * FROM $monthawards_table GROUP BY month_gained ORDER BY year_gained DESC, month_gained DESC");
$query      = DBQuery("SELECT * FROM $monthawards_table $conditions ORDER BY id ASC");

if(file_exists($base_folder."templates/month_awards.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/month_awards.htm", 1);
  
  if($month != "_Alltime") {
    $row["date"] = $mth." ".$yr;
  } else {
    $row["date"] = $al;
  }
 
  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }
  
  $months  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($months_sql)) {
    
	$row["id"] = $row["month_gained"]."_".$row["year_gained"];
	
	switch($row["month_gained"]) {
	  case '01' : $row["type"] = "Jan"; break;
	  case '02' : $row["type"] = "Feb"; break;
	  case '03' : $row["type"] = "Mar"; break;
	  case '04' : $row["type"] = "Apr"; break;
	  case '05' : $row["type"] = "May"; break;
	  case '06' : $row["type"] = "Jun"; break;
	  case '07' : $row["type"] = "Jul"; break;
	  case '08' : $row["type"] = "Aug"; break;
	  case '09' : $row["type"] = "Sep"; break;
	  case '10' : $row["type"] = "Oct"; break;
	  case '11' : $row["type"] = "Nov"; break;
	  case '12' : $row["type"] = "Dec"; break;
	  case '' : $row["type"] = ""   ; break;
    }
		  
	$row["year_gained"] = $row["type"]." ".$row["year_gained"];
	    
    $row["num"]   = $count + 1;
    $months[$count] = $row;
    $count++;
  }
  
  $awards = array();
  $row  = array();
  $count   = 0;
  
  while($row = DBFetchArray($query)) {
      if($count % 2 == 0){
      $row["class"] = $rowclass2;
    } else {
      $row["class"] = $rowclass1;
    }
	
	$row["player"] = htmlspecialchars(base64_decode($row["player"]));
	
	$arr = array('Highest Rating Pts', 'Highest No. of Kills', 'Highest No. of Headshots', 'Most Time Played', 
	             'Most Games Played', 'Most Games Won', 'Member of the Month (criteria gained -->)', 'Most MOTM Awards Gained');
	foreach($arr as $value) {
	  if($value == $row["monthaward"]) {
	    $row["value"] = sprintf("%d", $row["value"]);
      }
	}	
	
	if($row["monthaward"] == 	"Member of the Month (criteria gained -->)") {
	  $row["value"] = $row["value"]."/12";
	}
	
	if($row["monthaward"] == "Most Time Played") {
	  $row["value"] = FormatTime($row["value"]);
	}
	
	$string = $row["monthaward"];
    $check = strstr($string, '%');
	if($check == true) {
	 $row["value"] = $row["value"]." %";
	}
  
    $row["num"]   = $count + 1;
    $awards[$count] = $row;
    $count++;
  }
  
  
  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_var("month",       $month);
  $tpl->set_loop("months",     $months);
  $tpl->set_loop("awards",     $awards);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'month_awards' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/month_awards.php 1.0.0</div>
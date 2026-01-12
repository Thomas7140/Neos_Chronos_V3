
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

$squad = DBQuery("SELECT * FROM $squads_table ORDER BY id ASC");

$player = DBQuery("SELECT * FROM $players_table LEFT JOIN $squads_table ON $players_table.squad=$squads_table.id 
                      WHERE $players_table.id = '$id'");
$alias = DBQuery("SELECT * FROM $players_table LEFT JOIN $aliases_table ON $players_table.name=$aliases_table.to_name 
                      WHERE $players_table.id = '$id'");
$stats = DBQuery("SELECT * FROM $stats_table WHERE player = '$id'");

$ipSql = DBQuery("SELECT * FROM $playerips_table WHERE player = '$id'");

if(file_exists($base_folder."templates/admin/admin_players.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_players.htm", 1);

  $row = mysql_fetch_row($player);
  $arow = DBFetchArray($stats);
  $wpnstats = DBQuery("SELECT * FROM $weaponstats_table WHERE record = '".$arow["id"]."'");
  $mapstats = DBQuery("SELECT * FROM $mapstats_table WHERE record = '".$arow["id"]."'");
  $wrow = DBFetchArray($wpnstats);
  $mrow = DBFetchArray($mapstats);
  
  if(!($arow)){$row["stats"] = "None";} elseif ($arow["time"] ==='0') {$row["stats"] = "None";} else {$row["stats"] = "Available";}
  if(!($wrow)){$row["wpnstats"] = "None";} else {$row["wpnstats"] = "Available";}
  if(!($mrow)){$row["mapstats"] = "None";} else {$row["mapstats"] = "Available";}
  
    $row["ranking"] = GetRanking($id);
	if(!($row["ranking"])){$row["ranking"] = "Not Ranked";}
    $row["name"] = htmlspecialchars(base64_decode($row[1]));
	$row["id"] = $row[0];
    if(!($row[10])){$row["squad"] = "none";} else {$row["squad"] = $row[10];}

    if(!isset($id)   || $id   == "")
      {
        $fnames = array('name', 'squad', 'ratio', 'medals', 'aliases');
        foreach($fnames as $value)
         {
	       $row["$value"] = "";
	     }
      }
  
      foreach($row as $field => $value) {
      $tpl->set_var($field, $value);
    }
  
  $aliases  = array();
  $row   = array();
  $count = 0;

  while($row = mysql_fetch_row($alias)) {
      if($count % 2 == 0){
      $row["class"] = $rowclass2;
    } else {
      $row["class"] = $rowclass1;
    }
  
    if(!($row[6])){$row["name"] = "none";} else {$row["name"] = htmlspecialchars(base64_decode($row[6]));}
	
	$row["id"] = $row[9];
	$row["name"] = htmlspecialchars(base64_decode($row[10]));
	    
    $row["num"]   = $count + 1;
    $aliases[$count] = $row;
    $count++;
  }
  
  $squads  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($squad)) {
 
    $row["num"]   = $count + 1;
    $squads[$count] = $row;
    $count++;
  }
  
  $ips  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($ipSql)) {
 
    $row["num"]   = $count + 1;
    $ips[$count] = $row;
    $count++;
  }

  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_loop("aliases",    $aliases);
  $tpl->set_loop("squads",     $squads);
  $tpl->set_loop("ips",        $ips);
  $tpl->process("template", "more_player_options");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_players' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_player_options.php 1.0.0</div>
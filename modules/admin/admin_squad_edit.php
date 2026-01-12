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
 
$query = DBQuery("SELECT * FROM $squads_table WHERE id = '$id'");
$plyrs_query = DBQuery("SELECT 
                    $players_table.id                                AS id               ,
                    $players_table.name                              AS name             ,
                    $players_table.squad                             AS squad            ,
                    $players_table.rating                            AS rating           ,
                    SUM($stats_table.kills)                          AS kills            ,
                    SUM($stats_table.deaths)                         AS deaths           ,
                    SUM($stats_table.kills)/IF(SUM($stats_table.deaths)=0, 1, SUM($stats_table.deaths))  AS ratio 
                  FROM $players_table, $stats_table 
                  WHERE $players_table.squad = '$id' 
				  AND $stats_table.player = $players_table.id 
                  GROUP BY $stats_table.player ORDER BY kills DESC");

if(file_exists($base_folder."templates/admin/admin_squads.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_squads.htm", 1);
  
  $row = DBFetchArray($query);
  $members = DBQuery("SELECT count(name) as mem, squad FROM $players_table WHERE squad = '".$row["id"]."' GROUP BY squad");
  $memrow = DBFetchArray($members);
  if(!($memrow["mem"])){$row["mem"] = '0';} else {$row["mem"] = $memrow["mem"];}
  
  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }

  $players  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($plyrs_query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
	
	$plyr = array($row["id"]);
	foreach($plyr as $pvalue) {
	  $plyrs_m_query = DBQuery("SELECT 
                            SUM($stats_m_table.kills)                          AS kills           ,
                            SUM($stats_m_table.deaths)                         AS deaths          ,
                            SUM($stats_m_table.kills)/IF(SUM($stats_m_table.deaths)=0, 1, SUM($stats_m_table.deaths))  AS ratio
                          FROM $stats_m_table 
                          WHERE $stats_m_table.player = '$pvalue'  
                          GROUP BY $stats_m_table.player");
						  
	  $num_rows = mysql_num_rows($plyrs_m_query);
	  if(!($num_rows)) {
	    $row["mkills"] = '0'; $row["mdeaths"] = '0'; $row["mratio"] = '0.00';
	  } else {
	    while($prow = DBFetchArray($plyrs_m_query)) {
	      $row["mkills"] = $prow["kills"];
		  $row["mdeaths"] = $prow["deaths"];
		  $row["mratio"] = $prow["ratio"];
	    }
      }
	} 
	
	$row["name"] = htmlspecialchars(base64_decode($row["name"]));
	
	$row["r_class"] = GetRatioColor($row["ratio"]);
    $row["mr_class"] = GetRatioColor($row["mratio"]);
	  
    $row["num"]   = $count + 1;
    $players[$count] = $row;
    $count++;
  }

  $tpl->set_var("mainscript",  $mainscript);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("base",        $base_folder);
  $tpl->set_loop("players",    $players);
  $tpl->process("template", "squad_details");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_squads' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_squad_edit.php 1.0.0</div>
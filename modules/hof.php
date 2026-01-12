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
 * Finalized     : 1st May 2005
 */  
				  
$query = DBQuery("SELECT * FROM $hof_table");
$row = DBFetchArray($query);

if(file_exists($base_folder."templates/hof.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/hof.htm", 1);

  if(!($row["games_type"])) $row["entries"] = "<span class=\"tableheader\"><span class=\"red\"><big>HOF Criterias have not been set
                                               </big></span></span>";
  $arr = array("team_games" => $row["team_games"], "rating_pts" => $row["rating_pts"], 
               "time_played" => $row["time_played"], "awards" => $row["awards"], 
			   "wpn_awards" => $row["wpn_awards"], "kills" => $row["kills"]);

  foreach($arr as $index => $criteria){
    if($criteria > 0 AND $index == "team_games"){
	  if($row["games_type"] == 'P'){
	    $conditions2 .= " HAVING games >= '$criteria'";
	  } else {
	    $conditions2 .= " HAVING wins >= '$criteria'";
      }
	}
	if($criteria > 0 AND $index == "rating_pts"){
	  $conditions .= " AND $players_table.rating >= '$criteria'";
	}
	if($criteria > 0 AND $index == "time_played"){
	  $criteria = ($criteria*60)*60;
	  if(!($conditions2)){
	    $conditions2 .= " HAVING time >= '$criteria'";
      } else {
	    $conditions2 .= " AND time >= '$criteria'";
	  }
	}
	if($criteria > 0 AND $index == "awards"){
	  $conditions .= " AND $players_table.awards >= '$criteria'";
	}
	if($criteria > 0 AND $index == "wpn_awards"){
	  $conditions .= " AND $players_table.wpn_awards >= '$criteria'";
	}
	if($criteria > 0 AND $index == "kills"){
	  if(!($conditions2)){
	    $conditions2 .= " HAVING kills >= '$criteria'";
	  } else {
	    $conditions2 .= " AND kills >= '$criteria'";
	  }
	}
	
  }
  
  
  if(!($conditions OR $conditions2)) {
    $row["entries"] = "<span class=\"tableheader\"><span class=\"green\"><big>No HoF Members</big></span></span>";
	$members = DBQuery("SELECT $players_table.id                 , 
	                           $players_table.name               , 
							   $players_table.rating             , 
							   $players_table.awards             , 
							   $players_table.wpn_awards         , 
							   $stats_table.player               , 
							   SUM($stats_table.kills)  AS kills , 
							   SUM($stats_table.games)  AS games , 
							   SUM($stats_table.wins)   AS wins  , 
							   SUM($stats_table.time)   AS time  
                        FROM $players_table, $stats_table 
					    WHERE $stats_table.player = $players_table.id
                        AND $stats_table.kills > '99999999' 
						GROUP BY $stats_table.player");
  } else {
    $members = DBQuery("SELECT $players_table.id                 , 
	                           $players_table.name               , 
							   $players_table.rating             , 
							   $players_table.awards             , 
							   $players_table.wpn_awards         , 
							   $stats_table.player               , 
							   SUM($stats_table.kills)  AS kills , 
							   SUM($stats_table.games)  AS games , 
							   SUM($stats_table.wins)   AS wins  , 
							   SUM($stats_table.time)   AS time  
                        FROM $players_table, $stats_table 
					    WHERE $stats_table.player = $players_table.id 
                        $conditions 
						GROUP BY $stats_table.player 
						$conditions2 ");
	$num_rows = mysql_num_rows($members);
	$row["entries"] = "<span class=\"tableheader\"><span class=\"green\"><big>There are $num_rows players in the HoF</big></span></span>";
  }
						
  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }
					  
  $players  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($members)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
	
	$row["name"] = htmlspecialchars(base64_decode($row["name"]));
	
	$rank = GetRank($row["rating"]);
    $row["rank_name"]      = $rank["name"];
    $row["rank_image"]     = $rank["image"];
    $row["rank_thumbnail"] = $rank["thumbnail"];

    $row["num"]   = $count + 1;
    $players[$count] = $row;
    $count++;
  }


  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("players",   $players);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'hof' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/hof.php 1.0.0</div>
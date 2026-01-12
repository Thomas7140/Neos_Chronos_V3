<?php
/*
 * Copyright (c) 2005, Peter Jones a.k.a ?T?? ?zr??l
 * All rights reserved.
 *
 * Redistribution and use with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * Redistributions must retain the above copyright notice.
 * File licence.txt must not be removed from the package.
 *
 * Finalized     : 1st May 2005
 * Author        : Peter Jones a.k.a ?T?? ?zr??l
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */
 
// Use security parameter functions
$select_player = postParam('select_player', '');
$reset_plyr_stats = postParam('reset_plyr_stats', '');
$delete_aliases = postParam('delete_aliases', '');
$unassign_squad = postParam('unassign_squad', '');
$delete_player = postParam('delete_player', '');
$reset_plyrgame_stats = postParam('reset_plyrgame_stats', '');
$reset_plyrwpn_stats = postParam('reset_plyrwpn_stats', '');
$reset_plyrmap_stats = postParam('reset_plyrmap_stats', '');
$set_plyr_alias = postParam('set_plyr_alias', '');
$set_plyr_squad = postParam('set_plyr_squad', '');
$change_ply_name = postParam('change_ply_name', '');
$select_search = postParam('select_search', '');
$search_player = postParam('search_player', '');
$delete_squads = postParam('delete_squads', '');
$reset_squads = postParam('reset_squads', '');
$add_squad = postParam('add_squad', '');
$edit_squad = postParam('edit_squad', '');
$delete_squ_players = postParam('delete_squ_players', '');
$reset_weapons = postParam('reset_weapons', '');
$delete_selected_maps = postParam('delete_selected_maps', '');
$delete_map = postParam('delete_map', '');
$delete_map_image = postParam('delete_map_image', '');
$delete_map_thumbnail = postParam('delete_map_thumbnail', '');
$reset_map = postParam('reset_map', '');
$upload_image = postParam('upload_image', '');
$upload_thumbnail = postParam('upload_thumbnail', '');
$delete_selected_servers = postParam('delete_selected_servers', '');
$edit_selected_servers = postParam('edit_selected_servers', '');
$add_server = postParam('add_server', '');
$select_awards = postParam('select_awards', '');
$deselect_awards = postParam('deselect_awards', '');
$delete_selected_awards = postParam('delete_selected_awards', '');
$update_awards = postParam('update_awards', '');
$add_award = postParam('add_award', '');
$select_wpnawards = postParam('select_wpnawards', '');
$deselect_wpnawards = postParam('deselect_wpnawards', '');
$delete_selected_wpnawards = postParam('delete_selected_wpnawards', '');
$update_wpnawards = postParam('update_wpnawards', '');
$add_wpnaward = postParam('add_wpnaward', '');
$add_rank = postParam('add_rank', '');
$select_ranks = postParam('select_ranks', '');
$deselect_ranks = postParam('deselect_ranks', '');
$update_ranks = postParam('update_ranks', '');
$delete_selected_ranks = postParam('delete_selected_ranks', '');
$set_criteria = postParam('set_criteria', '');
$prune = postParam('prune', '');
$merge = postParam('merge', '');
$backup_db = postParam('backup_db', '');
$reset = postParam('reset', '');
$reset_all = postParam('reset_all', '');
$repair = postParam('repair', '');
$reset_monthly = postParam('reset_monthly', '');

// Additional parameters used in the actions
$id = postParam('id', '', 'int');
$new_alias = postParam('new_alias', '');
$squad_list = postParam('squad_list', '');
$new_name = postParam('new_name', '');
$search_list = postParam('search_list', '');
$squadid = postParam('squadid', '');
$playername = postParam('playername', '');
$time = postParam('time', '');
$kill = postParam('kill', '');
$sqname = postParam('sqname', '');
$tag = postParam('tag', '');
$url = postParam('url', '');
$add_name = postParam('add_name', '');
$add_serverid = postParam('add_serverid', '');
$new_image = postParam('new_image', '');
$new_field = postParam('new_field', '');
$new_value = postParam('new_value', '');
$new_description = postParam('new_description', '');
$add_image = postParam('add_image', '');
$add_thumbnail = postParam('add_thumbnail', '');
$add_rating = postParam('add_rating', '');
$cri = postParam('cri', array());
$player1 = postParam('player1', '');
$player2 = postParam('player2', '');
$days = postParam('days', '');
$kills = postParam('kills', '');
$radiobutton = postParam('radiobutton', '');
$player_list = postParam('player_list', 0, 'int');
$checkbox = postParam('checkbox', array());


/* START OF PLAYER ADMIN FUNCTIONS */
if($select_player) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = preg_replace("/&id=.*/", "", $link);
  header("location: ".$new_link."&id=".$player_list);
  exit;
}

if($reset_plyr_stats) {
  $sql1 = DBQuery("SELECT $stats_m_table.* FROM $stats_m_table WHERE $stats_m_table.player ='$id'");
  while($row = DBFetchArray($sql1)) {
    $sql2 = DBQuery("DELETE FROM $weaponstats_m_table WHERE record ='".$row["id"]."'");
	$sql3 = DBQuery("DELETE FROM $mapstats_m_table WHERE record ='".$row["id"]."'");
    $sql4 = DBQuery("DELETE FROM $stats_m_table WHERE player ='$id'");
  }
  $sql5 = DBQuery("SELECT $stats_table.* FROM $stats_table WHERE $stats_table.player ='$id'");
  while($row = DBFetchArray($sql5)) {
    $sql6 = DBQuery("DELETE FROM $weaponstats_table WHERE record ='".$row["id"]."'");
	$sql7 = DBQuery("DELETE FROM $mapstats_table WHERE record ='".$row["id"]."'");
	$sql8 = DBQuery("DELETE FROM $playerawards_table WHERE player ='".$row["player"]."'");
	$sql9 = DBQuery("UPDATE $players_table SET rating = '0', m_rating = '0', awards = '0', wpn_awards = '0', motm = '0' WHERE id ='".$row["player"]."'");
    $sql10 = DBQuery("DELETE FROM $stats_table WHERE player ='$id'");
  }
}

if($delete_aliases) {
  foreach($checkbox as $value) {
    $sql = DBQuery("DELETE FROM $aliases_table WHERE id ='$value'");
  }
}

if($unassign_squad) {
  $sql = DBQuery("UPDATE $players_table SET squad='-1' WHERE id='$id'");
}

if($delete_player) {
  $sql1 = DBQuery("SELECT $stats_m_table.* FROM $stats_m_table WHERE $stats_m_table.player ='$id'");
  while($row = DBFetchArray($sql1)) {
    $sql2 = DBQuery("DELETE FROM $weaponstats_m_table WHERE record ='".$row["id"]."'");
	$sql3 = DBQuery("DELETE FROM $mapstats_m_table WHERE record ='".$row["id"]."'");
    $sql4 = DBQuery("DELETE FROM $stats_m_table WHERE player ='$id'");
  }
  $sql5 = DBQuery("SELECT $stats_table.* FROM $stats_table WHERE $stats_table.player ='$id'");
  while($row = DBFetchArray($sql5)) {
    $sql6 = DBQuery("DELETE FROM $weaponstats_table WHERE record ='".$row["id"]."'");
	$sql7 = DBQuery("DELETE FROM $mapstats_table WHERE record ='".$row["id"]."'");
	$sql8 = DBQuery("DELETE FROM $playerawards_table WHERE player ='".$row["player"]."'");
	$sql9 = DBQuery("DELETE FROM $stats_table WHERE player ='$id'");
  }
  $sql10 = DBQuery("DELETE FROM $players_table WHERE id ='$id'");
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("admin_player_options&id=.*", "admin_players", $link);
  header("location: ".$new_link);
  exit;
}

if($reset_plyrgame_stats) {
  $sql = DBQuery("UPDATE $stats_table SET
                    kills         =  0, deaths        =  0, murders       =  0, 
					suicides      =  0, knifings      =  0, headshots     =  0, 
                    medic_saves   =  0, revives       =  0, pspattempts   =  0, 
                    psptakeovers  =  0, score_1       =  0, score_2       =  0, 
                    score_3       =  0, time          =  0, games         =  0, 
					wins          =  0, draws         =  0, sniper_kills  =  0       
				  WHERE player ='$id'");
  $sql2 = DBQuery("UPDATE $stats_m_table SET
                     kills         =  0, deaths        =  0, murders       =  0, 
					 suicides      =  0, knifings      =  0, headshots     =  0, 
                     medic_saves   =  0, revives       =  0, pspattempts   =  0, 
                     psptakeovers  =  0, score_1       =  0, score_2       =  0, 
                     score_3       =  0, time          =  0, games         =  0, 
					 wins          =  0, draws         =  0, sniper_kills  =  0         
				   WHERE player ='$id'");
}

if($reset_plyrwpn_stats) {
  $sql = DBQuery("SELECT * FROM $stats_table WHERE player ='$id'");
  $sql2 = DBQuery("SELECT * FROM $stats_m_table WHERE player ='$id'");
  while($row = DBFetchArray($sql)) {
	$sql3 = DBQuery("DELETE FROM $weaponstats_table WHERE record ='".$row["id"]."'");
  }
  while($row2 = DBFetchArray($sql2)) {
	$sql4 = DBQuery("DELETE FROM $weaponstats_m_table WHERE record ='".$row["id"]."'");
  }
}

if($reset_plyrmap_stats) {
  $sql = DBQuery("SELECT * FROM $stats_table WHERE player ='$id'");
  $sql2 = DBQuery("SELECT * FROM $stats_m_table WHERE player ='$id'");
  while($row = DBFetchArray($sql)) {
	$sql3 = DBQuery("DELETE FROM $mapstats_table WHERE record ='".$row["id"]."'");
  }
  while($row2 = DBFetchArray($sql2)) {
	$sql4 = DBQuery("DELETE FROM $mapstats_m_table WHERE record ='".$row["id"]."'");
  }
}

if($set_plyr_alias) {
  $sql = DBQuery("SELECT * FROM $players_table WHERE id = '$id'");
  $row = DBFetchArray($sql);
  $sql2 = DBQuery("INSERT INTO $aliases_table VALUES (NULL, '".htmlspecialchars(base64_encode($new_alias))."', '".$row["name"]."')");
}

if($set_plyr_squad) {
  $sql = DBQuery("UPDATE $players_table SET squad='".$squad_list."' WHERE id='$id'");
}

if($change_ply_name) {
  $sql = DBQuery("UPDATE $players_table SET name='".htmlspecialchars(base64_encode($new_name))."' WHERE id='$id'");
}

if($select_search) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("admin_players_search", "admin_players", $link);
  header("location: ".$new_link."&id=".$search_list);
  exit;
}

if($search_player) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&squadid=.*&time=.*&kill=.*", "", $link);
  $new_time = ereg_replace(" ", "_", $time);
  $new_kill = ereg_replace(" ", "", $kill);
  header("location: ".$new_link."&squadid=".$squadid."&time=".$new_time."&kill=".$new_kill."&pname=".$playername);
  exit;
}
/* END OF PLAYER ADMIN FUNCTIONS*/


/* START OF SQUADS ADMIN FUNCTIONS */  
if($delete_squads) {
  foreach($checkbox as $value) {
    $sql = DBQuery("DELETE FROM $squads_table WHERE id = $value");
	$sql2 = DBQuery("UPDATE $players_table SET squad='-1' WHERE squad='$value'");
  }
}

if($reset_squads) {
  foreach($checkbox as $value) {
	$sql = DBQuery("UPDATE $players_table SET squad='-1' WHERE squad='$value'");
  }
}

if($add_squad) {
  
  $sql = DBQuery("INSERT INTO `$squads_table` (`id`, `name`, `tag`, `url`) VALUES (NULL, '$sqname', '$tag', '$url')");
}

if($edit_squad) {
  
  $sql = DBQuery("UPDATE $squads_table SET name='$sqname', tag='$tag', url='$url' WHERE id='$id'");
}

if($delete_squ_players) {
  foreach($checkbox as $value) {
    $sql = DBQuery("UPDATE $players_table SET squad='-1' WHERE id='$value'");
  }
}
/* END OF SQUADS ADMIN FUNCTIONS*/


/* START OF WEAPONS ADMIN FUNCTIONS */
if($reset_weapons) {
  foreach($checkbox as $value) {
    $sql = DBQuery("SELECT count(weapon) as num FROM $weaponstats_table WHERE weapon = '$value'");
	$row = DBFetchArray($sql);
	$offset = $row["num"] - 1;
	$sql2 = DBQuery("DELETE FROM $weaponstats_table WHERE weapon = '$value' LIMIT $offset");
    $sql = DBQuery("UPDATE $weaponstats_table SET 
	                  id        =   0,
					  record    =   0,
					  kills     =   0,
					  shots     =   0,
					  time      =   0
					WHERE weapon ='$value'");
  }
}
/* END OF WEAPONS ADMIN FUNCTIONS*/


/* START OF MAPS ADMIN FUNCTIONS */
if($delete_selected_maps) {
  foreach($checkbox as $value) {
	$sql = DBQuery("DELETE FROM $maps_table WHERE id = '$value'");
	$sql2 = DBQuery("DELETE FROM $mapstats_table WHERE map = '$value'");
  }
}

if($delete_map) {
  $sql = DBQuery("DELETE FROM $maps_table WHERE id = '$id'");
  $sql2 = DBQuery("DELETE FROM $mapstats_table WHERE map = '$id'");
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("admin_maps_stats&id=.*", "admin_maps", $link);
  header("location: ".$new_link);
  exit;
}

if($delete_map_image) {
  $sql = DBQuery("UPDATE $maps_table SET 
	                image    =   ''
				  WHERE id ='$id'");
}

if($delete_map_thumbnail) {
  $sql = DBQuery("UPDATE $maps_table SET 
	                thumbnail    =   ''
				  WHERE id ='$id'");

}

if($reset_map) {
  $sql = DBQuery("SELECT count(map) as num FROM $mapstats_table WHERE map = '$id'");
  $row = DBFetchArray($sql);
  $offset = $row["num"] - 1;
  $sql2 = DBQuery("DELETE FROM $mapstats_table WHERE map = '$id' LIMIT $offset");
  $sql3 = DBQuery("UPDATE $mapstats_table SET 
	                  id        =   0,
					  record    =   0,
					  kills     =   0,
					  deaths    =   0,
					  score     =   0,
					  time      =   0
					WHERE map ='$id'");
  $sql4 = DBQuery("UPDATE $maps_table SET 
	                  hosted          =   0,
					  time            =   0,
					  last_played     =   ''
					WHERE id ='$id'");
}

if($upload_image) {
  if($_FILES["uploadedimagefile"]["name"]) {
    $_FILES["uploadedimagefile"]["name"] = str_replace("%","",$_FILES["uploadedimagefile"]["name"]);
    if($_FILES["uploadedimagefile"]["size"] > 0) {
      $filename = "maps/".$_FILES["uploadedimagefile"]["name"];
      move_uploaded_file($_FILES["uploadedimagefile"]["tmp_name"], $base_folder.$filename);
      DBQuery("UPDATE $maps_table SET image='$filename' WHERE id='$id'");
    }
  }
}

if($upload_thumbnail) {
  if($_FILES["uploadedthumbfile"]["name"]) {
    $_FILES["uploadedthumbfile"]["name"] = str_replace("%","",$_FILES["uploadedthumbfile"]["name"]);
    if($_FILES["uploadedthumbfile"]["size"] > 0) {
      $filename = "maps/".$_FILES["uploadedthumbfile"]["name"];
      move_uploaded_file($_FILES["uploadedthumbfile"]["tmp_name"], $base_folder.$filename);
      DBQuery("UPDATE $maps_table SET thumbnail='$filename' WHERE id='$id'");
    }
  }
}
/* END OF MAPS ADMIN FUNCTIONS*/


/* START OF SERVERS ADMIN FUNCTIONS */
if($delete_selected_servers) {
  foreach($checkbox as $value) {
	$sql = DBQuery("DELETE FROM $servers_table WHERE id = '$value'");
	$sql2 = DBQuery("DELETE FROM $serverstats_table WHERE serverid = '$value'");
  }
}

if($edit_selected_servers) {
  foreach($checkbox as $value) {
    $a = "sv"."$value";
	$arr = ${$a};
    $sql = DBQuery("UPDATE $servers_table SET 
	                  name        = '".$arr[1]."', 
	                  serverid    = '".$arr[2]."'
					WHERE id    = '".$arr[0]."'");
  }
}

if($add_server) {
  $sql = DBQuery("INSERT INTO $servers_table VALUES (NULL, '$add_name', '$add_serverid', '', '', '', '', '', 0, '', '', '', '', '', 0, 0, 0)");
}
/* END OF SERVERS ADMIN FUNCTIONS*/


/* START OF AWARD ADMIN FUNCTIONS */
if($select_awards) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&opt=.*", "", $link);
  header("location: ".$new_link."&opt=1");
  exit;
}

if($deselect_awards) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&opt=.*", "", $link);
  header("location: ".$new_link);
  exit;
}

if($delete_selected_awards) {
  foreach($checkbox as $value) {
    $sql = DBQuery("DELETE FROM $awards_table WHERE id ='$value'");
  }
}

if($update_awards) {
  foreach($checkbox as $value) {
    $a = "aw"."$value";
	$arr = ${$a};
    $sql = DBQuery("UPDATE $awards_table SET name        = '".$arr[1]."', 
	                                         image       = '".$arr[2]."',
											 field       = '".$arr[3]."',
											 value       = '".$arr[4]."',
											 description = '".$arr[5]."'
											 WHERE id    = '".$arr[0]."'");
  }
}

if($add_award) {
  $sql = DBQuery("INSERT INTO $awards_table VALUES (NULL, '$new_name', '$new_image', '$new_field', '$new_value', '$new_description', 'A')");
}
/* END OF AWARD ADMIN FUNCTIONS*/


/* START OF WEAPONS ADMIN FUNCTIONS */
if($reset_weapons) {
  foreach($checkbox as $value) {
    $sql = DBQuery("SELECT count(weapon) as num FROM $weaponstats_table WHERE weapon = '$value'");
	$row = DBFetchArray($sql);
	$offset = $row["num"] - 1;
	$sql2 = DBQuery("DELETE FROM $weaponstats_table WHERE weapon = '$value' LIMIT $offset");
    $sql = DBQuery("UPDATE $weaponstats_table SET 
	                  id        =   0,
					  record    =   0,
					  kills     =   0,
					  shots     =   0,
					  time      =   0
					WHERE weapon ='$value'");
  }
}
/* END OF WEAPONS ADMIN FUNCTIONS*/


/* START OF WEAPON AWARD ADMIN FUNCTIONS */
if($select_wpnawards) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&opt=.*", "", $link);
  header("location: ".$new_link."&opt=1");
  exit;
}

if($deselect_wpnawards) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&opt=.*", "", $link);
  header("location: ".$new_link);
  exit;
}

if($delete_selected_wpnawards) {
  foreach($checkbox as $value) {
    $sql = DBQuery("DELETE FROM $awards_table WHERE id ='$value'");
  }
}

if($update_wpnawards) {
  foreach($checkbox as $value) {
    $a = "aw"."$value";
	$arr = ${$a};
    $sql = DBQuery("UPDATE $awards_table SET name        = '".$arr[1]."', 
	                                         image       = '".$arr[2]."',
											 field       = '".$arr[3]."',
											 value       = '".$arr[4]."',
											 description = '".$arr[5]."'
											 WHERE id    = '".$arr[0]."'");
  }
}

if($add_wpnaward) {
  $sql = DBQuery("INSERT INTO $awards_table VALUES (NULL, '$new_name', '$new_image', '$new_field', '$new_value', '$new_description', 'W')");
}
/* END OF WEAPON AWARD ADMIN FUNCTIONS*/


/* START OF RANKS ADMIN FUNCTIONS */
if($add_rank) {
  $sql = DBQuery("INSERT INTO $ranks_table VALUES (NULL, '$add_name', '$add_image', '$add_thumbnail', '$add_rating')");
}

if($select_ranks) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&opt=.*", "", $link);
  header("location: ".$new_link."&opt=1");
  exit;
}

if($deselect_ranks) {
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&opt=.*", "", $link);
  header("location: ".$new_link);
  exit;
}

if($update_ranks) {
  foreach($checkbox as $value) {
    $a = "rk"."$value";
	$arr = ${$a};
    $sql = DBQuery("UPDATE $ranks_table SET name        = '".$arr[1]."', 
	                                         rating       = '".$arr[2]."',
											 image       = '".$arr[3]."',
											 thumbnail       = '".$arr[4]."'
											 WHERE id    = '".$arr[0]."'");
  }
}
if($delete_selected_ranks) {
  foreach($checkbox as $value) {
	$sql = DBQuery("DELETE FROM $ranks_table WHERE id = '$value'");
  }
}
/* END OF RANKS ADMIN FUNCTIONS*/


/* START OF HOF ADMIN FUNCTIONS */
if($set_criteria) {
  $query = DBQuery("SELECT * FROM $hof_table");
  $row = DBFetchArray($query);
  if(!($row["games_type"])) {
    DBQuery("INSERT INTO $hof_table VALUES ('P', '0', '0', '0', '0', '0', '0')");
    foreach($cri as $index => $value){
      $sql = DBQuery("UPDATE $hof_table SET $index = '$value';");
    }
    $link = $_SERVER['HTTP_REFERER'];
    $new_link = ereg_replace("_set", "", $link);
    header("location: ".$new_link);
    exit;
  } else {
    $sql = DBQuery("UPDATE $hof_table SET 
	                  team_games  = 0,
				      rating_pts  = 0,
				      time_played = 0,
				      awards      = 0,
			          wpn_awards  = 0,
				      kills       = 0");
					  
    foreach($cri as $index => $value){
      $sql = DBQuery("UPDATE $hof_table SET $index = '$value';");
    }
    $link = $_SERVER['HTTP_REFERER'];
    $new_link = ereg_replace("_set", "", $link);
    header("location: ".$new_link);
    exit;
  }
}

/* END OF HOF ADMIN FUNCTIONS*/


/* START OF MISC ADMIN FUNCTIONS */
if($prune) {
  $sql = DBQuery("SELECT $stats_table.player, sum( $stats_table.kills ) , MAX( $stats_table.last_played ) AS Played, 
                    $players_table.id, $players_table.squad, $stats_table.id 
                  FROM $stats_table, $players_table 
                  WHERE $players_table.id = $stats_table.player 
                  GROUP BY player 
                  HAVING MAX( last_played ) < ( CURDATE( ) - INTERVAL '".$days."' DAY ) 
				  AND $players_table.squad = '-1' AND SUM($stats_table.kills) < '".$kills."' 
                  ORDER BY `$stats_table`.`player`");

  $num_rows = mysql_num_rows($sql);
  while($row = DBFetchArray($sql)){                                    
    $lastplayed = $row["Played"];
    $id = $row["player"];
    $rec = $row["id"];
    $counter++;

    if ($num_rows > 0){
	  $sql1 = DBQuery("DELETE FROM $weaponstats_table WHERE record='$rec'");
      $sql2 = DBQuery("DELETE FROM $mapstats_table WHERE record='$rec'");
      $sql3 = DBQuery("DELETE FROM $players_table WHERE id='$id'");
      $sql4 = DBQuery("DELETE FROM $stats_table WHERE player='$id'");
	}
  }
  $act =  $num_rows;
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
}

if($merge) {

  $plyr1 = nl2br(htmlentities(htmlspecialchars(base64_encode($player1))));
  $plyr2 = nl2br(htmlentities(htmlspecialchars(base64_encode($player2))));
 
  $plyr1_sql = mysql_query("SELECT * FROM $players_table WHERE name = '$plyr1'");
  $plyr2_sql = mysql_query("SELECT * FROM $players_table WHERE name = '$plyr2'");
  $plyr_fields = array('rating', 'm_rating', 'motm');
  $stats_fields = array('kills', 'deaths', 'murders', 'suicides', 'knifings', 'sniper_kills', 'headshots', 
                        'medic_saves', 'revives', 'pspattempts', 'psptakeovers', 'doublekills', 'score_1', 
					    'score_2', 'score_3', 'time', 'games', 'wins', 'draws');
  $match_row = mysql_fetch_assoc($plyr1_sql);
  $match2_row = mysql_fetch_assoc($plyr2_sql);
  
  
  mysql_query("UPDATE $players_table SET rating = rating + '".$match2_row['rating']."', m_rating = m_rating + '".$match2_row['m_rating']."', motm = motm + '".$match2_row['motm']."' WHERE id = '".$match_row['id']."'");
  
  $match_sql = mysql_query("SELECT * FROM $stats_table WHERE player = '".$match_row['id']."' ORDER BY server, game_type");
  while($match = mysql_fetch_assoc($match_sql)) {
    $match_array[] = array('id' => $match['id'], 'server' => $match['server'], 'game_type' => $match['game_type']);
  }
  
  foreach($match_array as $index) {
    $match2_sql = mysql_query("SELECT * FROM $stats_table WHERE player = '".$match2_row['id']."' AND server = '".$index['server']."' 
	                           AND game_type = '".$index['game_type']."' ORDER BY server, game_type");
    while($match2 = mysql_fetch_assoc($match2_sql)) {
      $match3_sql = mysql_query("SELECT * FROM $stats_table WHERE player = '".$match_row['id']."' AND game_type = '".$match2['game_type']."' 
	                             AND server = '".$match2['server']."' ORDER BY server, game_type");
      $match3 = mysql_fetch_assoc($match3_sql);   
      $match2_array[] = array('id' => $match2['id'], 'server' => $match2['server'], 'game_type' => $match2['game_type']);
	  $match3_array[] = array('id1' => $match3['id'], 'id2' => $match2['id']);
    }
  }
  
  //create array of stats for both players
  foreach($match_array as $match_up) {
    $p1_m = mysql_query("SELECT * FROM $stats_table WHERE player = '".$match_row['id']."' AND server = '".$match_up['server']."' 
	                     AND game_type = '".$match_up['game_type']."' ORDER BY game_type, server");
    $row1[] = mysql_fetch_assoc($p1_m);
	$p2_m = mysql_query("SELECT * FROM $stats_table WHERE player = '".$match2_row['id']."' AND server = '".$match_up['server']."' 
	                     AND game_type = '".$match_up['game_type']."' ORDER BY game_type, server");
    $row2[] = mysql_fetch_assoc($p2_m);
  }
  
  //create new array with combined stats
  $size = sizeof($row1);
  for($i = 0; $i < $size; $i++) {
    foreach($row1[$i] as $new => $value) {
	  if($new == 'id' || $new == 'player' || $new == 'server' || $new == 'game_type' || $new == 'last_played') {
        $update_stats[$i][$new] = $value;
	  } else {
	    $update_stats[$i][$new] = $value + $row2[$i][$new];
	  }
	}
  }
  
  //input new stats
  for($i = 0; $i < $size; $i++) {
    foreach($stats_fields as $field) {
      mysql_query("UPDATE $stats_table SET ".$field." = '".$update_stats[$i][$field]."' WHERE id = '".$update_stats[$i]['id']."'");
	}
  }
  
  //Alter player 2 weaponstats and mapstats record id's to associate with player 1's current records
  //Delete player 2's combined stats
  foreach($match3_array as $field) {
	mysql_query("UPDATE $weaponstats_table SET record = '".$field['id1']."' WHERE record = '".$field['id2']."'");
	mysql_query("UPDATE $mapstats_table SET record = '".$field['id1']."' WHERE record = '".$field['id2']."'");
	mysql_query("DELETE FROM $stats_table WHERE id = '".$field['id2']."'");
  }
  
  mysql_query("UPDATE $stats_table SET player = '".$match_row['id']."' WHERE player = '".$match2_row['id']."'");
  
  $match_sql = mysql_query("SELECT * FROM $stats_m_table WHERE player = '".$match_row['id']."' ORDER BY server, game_type");
  while($match = mysql_fetch_assoc($match_sql)) {
    $match_array[] = array('id' => $match['id'], 'server' => $match['server'], 'game_type' => $match['game_type']);
  }
  
  foreach($match_array as $index) {
    $match2_sql = mysql_query("SELECT * FROM $stats_m_table WHERE player = '".$match2_row['id']."' AND server = '".$index['server']."' 
	                           AND game_type = '".$index['game_type']."' ORDER BY server, game_type");
    while($match2 = mysql_fetch_assoc($match2_sql)) {
      $match3_sql = mysql_query("SELECT * FROM $stats_m_table WHERE player = '".$match_row['id']."' AND game_type = '".$match2['game_type']."' 
	                             AND server = '".$match2['server']."' ORDER BY server, game_type");
      $match3 = mysql_fetch_assoc($match3_sql);   
      $match2_array[] = array('id' => $match2['id'], 'server' => $match2['server'], 'game_type' => $match2['game_type']);
	  $match3_array[] = array('id1' => $match3['id'], 'id2' => $match2['id']);
    }
  }
  
  //create array of stats for both players
  foreach($match_array as $match_up) {
    $p1_m = mysql_query("SELECT * FROM $stats_m_table WHERE player = '".$match_row['id']."' AND server = '".$match_up['server']."' 
	                     AND game_type = '".$match_up['game_type']."' ORDER BY game_type, server");
    $row1[] = mysql_fetch_assoc($p1_m);
	$p2_m = mysql_query("SELECT * FROM $stats_m_table WHERE player = '".$match2_row['id']."' AND server = '".$match_up['server']."' 
	                     AND game_type = '".$match_up['game_type']."' ORDER BY game_type, server");
    $row2[] = mysql_fetch_assoc($p2_m);
  }
  
  //create new array with combined stats
  $size = sizeof($row1);
  for($i = 0; $i < $size; $i++) {
    foreach($row1[$i] as $new => $value) {
	  if($new == 'id' || $new == 'player' || $new == 'server' || $new == 'game_type' || $new == 'last_played') {
        $update_stats[$i][$new] = $value;
	  } else {
	    $update_stats[$i][$new] = $value + $row2[$i][$new];
	  }
	}
  }
  
  //input new stats
  for($i = 0; $i < $size; $i++) {
    foreach($stats_fields as $field) {
      mysql_query("UPDATE $stats_m_table SET ".$field." = '".$update_stats[$i][$field]."' WHERE id = '".$update_stats[$i]['id']."'");
	}
  }
  
  //Alter player 2 weaponstats and mapstats record id's to associate with player 1's current records
  //Delete player 2's combined stats
  foreach($match3_array as $field) {
	mysql_query("UPDATE $weaponstats_m_table SET record = '".$field['id1']."' WHERE record = '".$field['id2']."'");
	mysql_query("UPDATE $mapstats_m_table SET record = '".$field['id1']."' WHERE record = '".$field['id2']."'");
	mysql_query("DELETE FROM $stats_m_table WHERE id = '".$field['id2']."'");
  }
  
  mysql_query("UPDATE $stats_m_table SET player = '".$match_row['id']."' WHERE player = '".$match2_row['id']."'");
  mysql_query("DELETE FROM $players_table WHERE id = '".$match2_row['id']."'");
  
  $act = '-6';
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
}

if($backup_db) {
  $file_name = "BU_Neos_Chronos_".date("d_M_Y-H_i_s").".sql";
  $ver   = phpversion();

  $backup = fopen("backups/$file_name", "w+"); 
$string = 
"-- Babstats SQL Dump\r
-- version 1.0.0\r
--\r
-- Host: localhost\r
-- Generation Time: ".date("d_M_Y-H_i_s")."\r
-- PHP Version: ".$ver."\r
--\r
-- Database: ".$dbname."\r
--\r";

  $array = array($aliases_table, $awards_table, $hof_table, $log_table, $mapstats_m_table, $stats_m_table, $weaponstats_m_table, 
                 $maps_table, $mapstats_table, $monthawards_table, $playerawards_table, $players_table, $ranks_table, 
				 $serverhistory_table, $servers_table, $serverstats_table, $squads_table, $stats_table, $weapons_table, 
				 $weaponstats_table);

  foreach($array as $table) {
$string .= "
--\r
-- Dumping data for table `$table`\r
--\r\n\r\n";
    unset($row_id);
    $query = mysql_query("SELECT * from $table");
    $num_fields = @mysql_num_fields($query);
    while($row = mysql_fetch_assoc($query)) {
      $row_id[] = $row['id'];
    }
    if(!empty($row_id)) {
      foreach($row_id as $id) {
        $string .= formatRow($id, $table);
      }
    } else {
      $string .="\r\n";
    }
  }

  $string .= "\r\n";
  fwrite($backup, $string);
  if(file_exists("backups/".$file_name)) {
    $act = '-8';
    $link = $_SERVER['HTTP_REFERER'];
    $new_link = ereg_replace("&act=.*", "", $link);
    header("location: ".$new_link."&act=$act");
    exit;
  } else {
    $act = '-9';
    $link = $_SERVER['HTTP_REFERER'];
    $new_link = ereg_replace("&act=.*", "", $link);
    header("location: ".$new_link."&act=$act");
    exit;
  }
}

if($reset) {
  $sql1 = DBQuery("DELETE FROM $mapstats_table;");
  $sql2 = DBQuery("DELETE FROM $players_table;");
  $sql3 = DBQuery("DELETE FROM $stats_table;");
  $sql4 = DBQuery("DELETE FROM $weaponstats_table;");
  $sql5 = DBQuery("DELETE FROM $playerawards_table;");
  $sql6 = DBQuery("DELETE FROM $mapstats_m_table;");
  $sql7 = DBQuery("DELETE FROM $stats_m_table;");
  $sql8 = DBQuery("DELETE FROM $weaponstats_m_table;");
  $act = '-1';
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
}

if($reset_all) {
  $sql1 = DBQuery("DELETE FROM $aliases_table;");
  $sql2 = DBQuery("DELETE FROM $hof_table;");
  $sql3 = DBQuery("DELETE FROM $log_table;");
  $sql4 = DBQuery("DELETE FROM $mapstats_m_table;");
  $sql5 = DBQuery("DELETE FROM $stats_m_table;");
  $sql6 = DBQuery("DELETE FROM $weaponstats_m_table;");
  $sql7 = DBQuery("DELETE FROM $maps_table;");
  $sql8 = DBQuery("DELETE FROM $mapstats_table;");
  $sql9 = DBQuery("DELETE FROM $playerawards_table;");
  $sql10 = DBQuery("DELETE FROM $players_table;");
  $sql11 = DBQuery("DELETE FROM $servers_table;");
  $sql12 = DBQuery("DELETE FROM $serverstats_table;");
  $sql13 = DBQuery("DELETE FROM $serverhistory_table;");
  $sql14 = DBQuery("DELETE FROM $squads_table;");
  $sql15 = DBQuery("DELETE FROM $stats_table;");
  $sql16 = DBQuery("DELETE FROM $weapons_table;");
  $sql17 = DBQuery("DELETE FROM $weaponstats_table;");
  $act = '-2';
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
}

if($repair) {
  $query1 = DBQuery("REPAIR TABLE $aliases_table, $awards_table, $hof_table, $log_table, 
                       $mapstats_m_table, $stats_m_table, $weaponstats_m_table, $maps_table, 
					   $mapstats_table, $playerawards_table, $players_table, $ranks_table, 
					   $servers_table, $serverstats_table, $serverhistory_table, $squads_table, 
					   $stats_table, $weapons_table, $weaponstats_table;");
  $act = '-3';
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
}

if($reset_monthly) { 
$useDate = $radiobutton;
$check = DBQuery("SELECT * FROM $stats_m_table WHERE player > 0");
$crow = DBFetchArray($check);
if(!($crow["player"]) || !($useDate)){
  $act = '-5';
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
} else {
  $exDate = explode("-", $useDate);
  $cur_year = $exDate[0];
  $cur_mon = $exDate[1];
  
  $qualifier = DBQuery("SELECT player, SUM(time) as time FROM $stats_m_table 
                        WHERE SUBSTRING(last_played, 1, 7) = '$useDate' 
						GROUP BY player ORDER BY time DESC LIMIT 1");
  $qrow = DBFetchArray($qualifier);
  $qualify = $qrow["time"]/4;
  
  $assault_qual = DBQuery("SELECT $stats_m_table.player, $stats_m_table.id, $weaponstats_m_table.record, 
                            SUM( $weaponstats_m_table.kills ) AS kills, $weapons_table.name
                          FROM $stats_m_table, $weaponstats_m_table, $weapons_table
                          WHERE $weaponstats_m_table.record = $stats_m_table.id
                          AND $weaponstats_m_table.weapon = $weapons_table.id
                          AND $weapons_table.name IN ('CAR15', 'CAR15/M203', 'M16', 'M16/M203', 'G3', 'G36', 'MP5') 
						  AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' 
					      GROUP BY $stats_m_table.player ORDER BY kills DESC ");
  $assrow = DBFetchArray($assault_qual);
  $assa_qual = $assrow["kills"]/4;
  
  $support_qual = DBQuery("SELECT $stats_m_table.player, $stats_m_table.id, $weaponstats_m_table.record, 
                             SUM( $weaponstats_m_table.kills ) AS kills, $weapons_table.name
                           FROM $stats_m_table, $weaponstats_m_table, $weapons_table
                           WHERE $weaponstats_m_table.record = $stats_m_table.id
                           AND $weaponstats_m_table.weapon = $weapons_table.id
                           AND $weapons_table.name IN ('M60', 'M240', 'M249 SAW') 
						   AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' 
					       GROUP BY $stats_m_table.player ORDER BY kills DESC ");
  $suprow = DBFetchArray($support_qual);
  $supp_qual = $suprow["kills"]/4;
  
  $sniper_qual = DBQuery("SELECT $stats_m_table.player, $stats_m_table.id, $weaponstats_m_table.record, 
                            SUM( $weaponstats_m_table.kills ) AS kills, $weapons_table.name
                          FROM $stats_m_table, $weaponstats_m_table, $weapons_table
                          WHERE $weaponstats_m_table.record = $stats_m_table.id
                          AND $weaponstats_m_table.weapon = $weapons_table.id
                          AND $weapons_table.name IN ('M21', 'M24', 'PSG1', 'MCRT .300 Tactical', 'Barrett .50 Cal') 
						  AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' 
					      GROUP BY $stats_m_table.player ORDER BY kills DESC ");
  $snprow = DBFetchArray($sniper_qual);
  $snip_qual = $snprow["kills"]/4;
  
  $dsql = DBQuery("SELECT player, SUM(time) as time FROM $stats_m_table 
                   WHERE SUBSTRING(last_played, 1, 7) = '$useDate' 
				   GROUP BY player HAVING time < '$qualify'");
  while($drow = DBFetchArray($dsql)) {
    DBQuery("DELETE FROM $stats_m_table WHERE player = '".$drow["player"]."' AND SUBSTRING(last_played, 1, 7) = '$useDate' ");
  }
  
  $sql1 = DBQuery("SELECT $players_table.name, $players_table.m_rating FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate'  GROUP BY player ORDER BY m_rating DESC LIMIT 1");
  $sql1_row = DBFetchArray($sql1);
  $iql1 = DBQuery("INSERT INTO $monthawards_table VALUES ('2', 'Highest Rating Pts', '".$sql1_row["name"]."', '".$sql1_row["m_rating"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql1b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Rating Pts' AND year_gained = 'Alltime'");
  $sql1b_row = DBFetchArray($sql1b);
  if($sql1_row["m_rating"] > $sql1b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql1_row["name"]."', value = '".$sql1_row["m_rating"]."' WHERE monthaward = 'Highest Rating Pts' AND year_gained = 'Alltime'");}
  
  $sql2 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.kills) as kills FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY kills DESC LIMIT 1");
  $sql2_row = DBFetchArray($sql2);
  $iql2 = DBQuery("INSERT INTO $monthawards_table VALUES ('3', 'Highest No. of Kills', '".$sql2_row["name"]."', '".$sql2_row["kills"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql2b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest No. of Kills' AND year_gained = 'Alltime'");
  $sql2b_row = DBFetchArray($sql2b);
  if($sql2_row["kills"] > $sql2b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql2_row["name"]."', value = '".$sql2_row["kills"]."' WHERE monthaward = 'Highest No. of Kills' AND year_gained = 'Alltime'");}
  
  $sql3 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.headshots) as headshots FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY headshots DESC LIMIT 1");
  $sql3_row = DBFetchArray($sql3);
  $iql3 = DBQuery("INSERT INTO $monthawards_table VALUES ('4', 'Highest No. of Headshots', '".$sql3_row["name"]."', '".$sql3_row["headshots"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql3b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest No. of Headshots' AND year_gained = 'Alltime'");
  $sql3b_row = DBFetchArray($sql3b);
  if($sql3_row["headshots"] > $sql3b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql3_row["name"]."', value = '".$sql3_row["headshots"]."' WHERE monthaward = 'Highest No. of Headshots' AND year_gained = 'Alltime'");}
  
  $sql4 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.headshots)/IF(SUM($stats_m_table.kills)=0, 1, SUM($stats_m_table.kills)) AS per, SUM($stats_m_table.kills) as kills, SUM($stats_m_table.headshots) as headshots FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY per DESC LIMIT 1");
  $sql4_row = DBFetchArray($sql4);
  if($sql4_row["kills"]  == 0) $kills_divide  = 1; else $kills_divide  = $sql4_row["kills"];
  $sql4_row["percent_headshots"] = round($sql4_row["headshots"]*100/$kills_divide, 0);
  $iql4 = DBQuery("INSERT INTO $monthawards_table VALUES ('5', 'Highest Headshot Kills %', '".$sql4_row["name"]."', '".$sql4_row["percent_headshots"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql4b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Headshot Kills %' AND year_gained = 'Alltime'");
  $sql4b_row = DBFetchArray($sql4b);
  if($sql4_row["percent_headshots"] > $sql4b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql4_row["name"]."', value = '".$sql4_row["percent_headshots"]."' WHERE monthaward = 'Highest Headshot Kills %' AND year_gained = 'Alltime'");}
  
  $sql5 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.kills)/IF(SUM($stats_m_table.deaths)=0, 1, SUM($stats_m_table.deaths)) AS ratio FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY ratio DESC LIMIT 1");
  $sql5_row = DBFetchArray($sql5);
  $iql5 = DBQuery("INSERT INTO $monthawards_table VALUES ('6', 'Highest Kill/Death Ratio', '".$sql5_row["name"]."', '".$sql5_row["ratio"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql5b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Kill/Death Ratio' AND year_gained = 'Alltime'");
  $sql5b_row = DBFetchArray($sql5b);
  if($sql5_row["ratio"] > $sql5b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql5_row["name"]."', value = '".$sql5_row["ratio"]."' WHERE monthaward = 'Highest Kill/Death Ratio' AND year_gained = 'Alltime'");}
  
  $sql6 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.time) as time FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY time DESC LIMIT 1");
  $sql6_row = DBFetchArray($sql6);
  $iql6 = DBQuery("INSERT INTO $monthawards_table VALUES ('7', 'Most Time Played', '".$sql6_row["name"]."', '".$sql6_row["time"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql6b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Most Time Played' AND year_gained = 'Alltime'");
  $sql6b_row = DBFetchArray($sql6b);
  if($sql6_row["time"] > $sql6b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql6_row["name"]."', value = '".$sql6_row["time"]."' WHERE monthaward = 'Most Time Played' AND year_gained = 'Alltime'");}
  
  $sql7 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.games) as games FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY games DESC LIMIT 1");
  $sql7_row = DBFetchArray($sql7);
  $iql7 = DBQuery("INSERT INTO $monthawards_table VALUES ('8', 'Most Games Played', '".$sql7_row["name"]."', '".$sql7_row["games"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql7b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Most Games Played' AND year_gained = 'Alltime'");
  $sql7b_row = DBFetchArray($sql7b);
  if($sql7_row["games"] > $sql7b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql7_row["name"]."', value = '".$sql7_row["games"]."' WHERE monthaward = 'Most Games Played' AND year_gained = 'Alltime'");}
  
  $sql8 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.wins) as wins FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY wins DESC LIMIT 1");
  $sql8_row = DBFetchArray($sql8);
  $iql8 = DBQuery("INSERT INTO $monthawards_table VALUES ('9', 'Most Games Won', '".$sql8_row["name"]."', '".$sql8_row["wins"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql8b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Most Games Won' AND year_gained = 'Alltime'");
  $sql8b_row = DBFetchArray($sql8b);
  if($sql8_row["wins"] > $sql8b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql8_row["name"]."', value = '".$sql8_row["wins"]."' WHERE monthaward = 'Most Games Won' AND year_gained = 'Alltime'");}
  
  $sql9 = DBQuery("SELECT $players_table.name, SUM($stats_m_table.wins)*100/IF(SUM($stats_m_table.games)=0, 1, SUM($stats_m_table.games)) AS percent_won FROM $players_table, $stats_m_table WHERE $players_table.id = $stats_m_table.player AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' GROUP BY player ORDER BY percent_won DESC LIMIT 1");
  $sql9_row = DBFetchArray($sql9);
  $iql9 = DBQuery("INSERT INTO $monthawards_table VALUES ('10', 'Highest Game Win %', '".$sql9_row["name"]."', '".$sql9_row["percent_won"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql9b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Game Win %' AND year_gained = 'Alltime'");
  $sql9b_row = DBFetchArray($sql9b);
  if($sql9_row["percent_won"] > $sql9b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql9_row["name"]."', value = '".$sql9_row["percent_won"]."' WHERE monthaward = 'Highest Game Win %' AND year_gained = 'Alltime'");}
  
  $sql10 = DBQuery("SELECT $weapons_table.name, $players_table.name, $stats_m_table.id, $stats_m_table.player, $weaponstats_m_table.record, $weaponstats_m_table.weapon, 
					  SUM($weaponstats_m_table.kills)*100/IF(SUM($weaponstats_m_table.shots)=0, 1, SUM($weaponstats_m_table.shots)) as percent, 
					  SUM($weaponstats_m_table.kills)  as kills
					FROM $players_table, $stats_m_table, $weaponstats_m_table, $weapons_table WHERE $weaponstats_m_table.record=$stats_m_table.id 
					AND $weaponstats_m_table.weapon = $weapons_table.id AND $stats_m_table.player = $players_table.id 
					AND $weapons_table.name IN ('CAR15', 'CAR15/M203', 'M16', 'M16/M203', 'G3', 'G36', 'MP5') 
					AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' 
					GROUP BY $stats_m_table.player HAVING kills > '$assa_qual' ORDER by percent DESC LIMIT 1");
 
  $sql10_row = DBFetchArray($sql10);
  $iql10 = DBQuery("INSERT INTO $monthawards_table VALUES ('11', 'Highest Assault Rifle Accuracy (%)', '".$sql10_row["name"]."', '".$sql10_row["percent"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql10b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Assault Rifle Accuracy (%)' AND year_gained = 'Alltime'");
  $sql10b_row = DBFetchArray($sql10b);
  if($sql10_row["percent"] > $sql10b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql10_row["name"]."', value = '".$sql10_row["percent"]."' WHERE monthaward = 'Highest Assault Rifle Accuracy (%)' AND year_gained = 'Alltime'");}
  
  $sql11 = DBQuery("SELECT $weapons_table.name, $players_table.name, $stats_m_table.id, $stats_m_table.player, $weaponstats_table.record, $weaponstats_table.weapon, 
					  SUM($weaponstats_table.kills)*100/IF(SUM($weaponstats_table.shots)=0, 1, SUM($weaponstats_table.shots)) as percent, 
					  SUM($weaponstats_table.kills)  as kills 
					FROM $players_table, $stats_m_table, $weaponstats_table, $weapons_table WHERE $weaponstats_table.record=$stats_m_table.id 
					AND $weaponstats_table.weapon = $weapons_table.id AND $stats_m_table.player = $players_table.id 
					AND $weapons_table.name IN ('M21', 'M24', 'PSG1', 'MCRT .300 Tactical', 'Barrett .50 Cal') 
					AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' 
					GROUP BY $stats_m_table.player HAVING kills > '$snip_qual' ORDER by percent DESC LIMIT 1");
 
  $sql11_row = DBFetchArray($sql11);
  $iql11 = DBQuery("INSERT INTO $monthawards_table VALUES ('12', 'Highest Sniper Rifle Accuracy (%)', '".$sql11_row["name"]."', '".$sql11_row["percent"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql11b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Sniper Rifle Accuracy (%)' AND year_gained = 'Alltime'");
  $sql11b_row = DBFetchArray($sql11b);
  if($sql11_row["percent"] > $sql11b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql11_row["name"]."', value = '".$sql11_row["percent"]."' WHERE monthaward = 'Highest Sniper Rifle Accuracy (%)' AND year_gained = 'Alltime'");}
  
  $sql12 = DBQuery("SELECT $weapons_table.name, $players_table.name, $stats_m_table.id, $stats_m_table.player, $weaponstats_table.record, $weaponstats_table.weapon, 
					  SUM($weaponstats_table.kills)*100/IF(SUM($weaponstats_table.shots)=0, 1, SUM($weaponstats_table.shots)) as percent, 
					  SUM($weaponstats_table.kills)  as kills 
					FROM $players_table, $stats_m_table, $weaponstats_table, $weapons_table WHERE $weaponstats_table.record=$stats_m_table.id 
					AND $weaponstats_table.weapon = $weapons_table.id AND $stats_m_table.player = $players_table.id 
					AND $weapons_table.name IN ('M60', 'M240', 'M249 SAW') 
					AND SUBSTRING($stats_m_table.last_played, 1, 7) = '$useDate' 
					GROUP BY $stats_m_table.player HAVING kills > '$supp_qual' ORDER by percent DESC LIMIT 1");
 
  $sql12_row = DBFetchArray($sql12);
  $iql12 = DBQuery("INSERT INTO $monthawards_table VALUES ('13', 'Highest Support Gun Accuracy (%)', '".$sql12_row["name"]."', '".$sql12_row["percent"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  $sql12b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Highest Support Gun Accuracy (%)' AND year_gained = 'Alltime'");
  $sql12b_row = DBFetchArray($sql12b);
  if($sql12_row["percent"] > $sql12b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql12_row["name"]."', value = '".$sql12_row["percent"]."' WHERE monthaward = 'Highest Support Gun Accuracy (%)' AND year_gained = 'Alltime'");}
  
  $sql13 = DBQuery("SELECT COUNT(player) as num, player, month_gained, year_gained 
                    FROM $monthawards_table WHERE month_gained = '$cur_mon' AND year_gained = '$cur_year' 
					AND monthaward != 'Most MOTM Awards Gained' 
					GROUP BY player, month_gained, year_gained ORDER BY num DESC LIMIT 1");
  $sql13_row = DBFetchArray($sql13);
  $iql13 = DBQuery("INSERT INTO $monthawards_table VALUES ('0', 'Member of the Month (criteria gained -->)', '".$sql13_row["player"]."', '".$sql13_row["num"]."', '$cur_mon', '$cur_year', '".date("Y-m-d H:i:s")."')");
  
  
  $sql14 = DBQuery("SELECT COUNT(player) as num, player FROM `$monthawards_table` WHERE monthaward = 'Member of the Month (criteria gained -->)' GROUP BY player ORDER BY num DESC LIMIT 1");
  $sql14_row = DBFetchArray($sql14);
  $sql14b = DBQuery("SELECT monthaward, value FROM $monthawards_table WHERE monthaward = 'Most MOTM Awards Gained' AND year_gained = 'Alltime'");
  $sql14b_row = DBFetchArray($sql14b);
  if($sql14_row["num"] > $sql14b_row["value"]){DBQuery("UPDATE $monthawards_table SET date = '".date("Y-m-d H:i:s")."', player = '".$sql14_row["player"]."', value = '".$sql14_row["num"]."' WHERE monthaward = 'Most MOTM Awards Gained' AND year_gained = 'Alltime'");}
  
  $sql15 = DBQuery("SELECT * FROm $monthawards_table WHERE monthaward = 'Member of the Month (criteria gained -->)' AND month_gained = '$cur_mon' AND year_gained ='$cur_year'");
  $sql15_row = DBFetchArray($sql15);
  $uql15 = DBQuery("UPDATE $players_table SET motm = motm + 1 WHERE name = '".$sql15_row["player"]."'");
  
  $recordsIDs = array();
  $sqlGRIDs = DBQuery("SELECT * FROM $stats_m_table WHERE SUBSTRING(last_played, 1, 7) = '$useDate'");
  while($GRIDsRow = mysql_fetch_assoc($sqlGRIDs)) {
      $recordsIDs[] = $GRIDsRow["id"];
  }
  
  DBQuery("DELETE FROM $stats_m_table WHERE SUBSTRING(last_played, 1, 7) = '$useDate' ");
  foreach($recordsIDs AS $record) {
	  DBQuery("DELETE FROM $mapstats_m_table WHERE record = '$record'");
	  DBQuery("DELETE FROM $weaponstats_m_table WHERE record = '$record'");
  }
  DBQuery("UPDATE $players_table SET m_rating = 0");
  
  $act = '-4';
  $link = $_SERVER['HTTP_REFERER'];
  $new_link = ereg_replace("&act=.*", "", $link);
  header("location: ".$new_link."&act=$act");
  exit;
}
}
/* END OF MISC ADMIN FUNCTIONS*/
header("location: ".$_SERVER['HTTP_REFERER']);
exit;
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_actions.php 1.0.0</div>

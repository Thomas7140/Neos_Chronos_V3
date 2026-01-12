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
 */

$gameHeaders = "";
$pidVal = $_POST['pidVal'];

$query = DBQuery("SELECT $games_table.*, $maps_table.* FROM $games_table, $maps_table 
                  WHERE $maps_table.id = $games_table.map_id AND $games_table.id = '".$id."'");

if(file_exists($base_folder."templates/game_stats.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/game_stats.htm", 1);
  
  $row = DBFetchArray($query);
  
  if(!isset($team) || $team == "") $team = $row["winner"];
  if($team == '0') $team = '1';
  
  if($team == '1') {
      $teamName = "Blue Team";
	  $blueSel = 'selected="selected"';
	  $formurl = $mainscript."action=$action&id=$id&team=2";
  } elseif($team == '2') {
  	  $teamName = "Red Team";
	  $redSel = 'selected="selected"';
	  $formurl = $mainscript."action=$action&id=$id&team=1";
  }
  
  $blue_query = DBQuery("SELECT $playergames_table.*, $players_table.name,  $players_table.id AS pID
						 FROM $playergames_table, $players_table 
						 WHERE $players_table.id = $playergames_table.player 
						 AND $playergames_table.team = '$team' 
						 AND game_id = '".$id."' ORDER BY experience DESC");
  
  $row["name"] = base64_decode($row["name"]);
  
  if(!$row["image"]) $row["image"] = "maps/l_none.jpg";
  
  if($row["winner"] == '1') {
  	$row["winner"] = "Blue Team";
  } elseif($row["winner"] == '2') {
    $row["winner"] = "Red Team";
  } else {
	$row["winner"] = "Draw";
  }
  
  foreach($row as $field => $value) {
	$tpl->set_var($field, $value);
  }
  
  $blueTeam = array();
  $player  = array();
  $count   = 0;

  while($player = DBFetchArray($blue_query)) {
    if($count % 2 == 0){
      $player["class"] = $rowclass1;
    } else {
      $player["class"] = $rowclass2;
    }
  
    $player["name"] = base64_decode($player["name"]);

	$statSpl = explode("_", $player["stats"]);
	
	$player["kills"]        = $statSpl[0];
	$player["deaths"]       = $statSpl[1];
	$player["murders"]      = $statSpl[2];
	$player["suicides"]     = $statSpl[3];
	$player["knifings"]     = $statSpl[4];
	$player["headshots"]    = $statSpl[5];
    $player["medsaves"]     = $statSpl[6];
	$player["revives"]      = $statSpl[7];
	$player["pspattempts"]  = $statSpl[8];
    $player["psptakeovers"] = $statSpl[9];
	$player["doublekills"]  = $statSpl[10];
	$player["score1"]       = $statSpl[11];
	$player["score2"]       = $statSpl[12];
	$player["score3"]       = $statSpl[13];
	$player["time"]         = FormatTime($statSpl[14]);
	$player["kr"]           = $player["kills"];
	if($player["kr"] == '0') $player["kr"] = '1';
	$player["dr"]           = $player["deaths"];
	if($player["dr"] == '0') $player["dr"] = '1';
	$player["ratio"]        = round($player["kr"]/$player["dr"], 2);
	if($player["ratio"] == '1') $player["ratio"] = '1.00';

	$player["r_class"]      = GetRatioColor($player["ratio"]);

	$player["pspa"] = $player["pspattempts"];
	if($player["pspa"] == '0') $player["pspa"] = '1';

	$player["pspPer"]  = round(($player["psptakeovers"]*100)/$player["pspa"], 2);
	
	$player["onlickPID"] = 'resubmit('.$player["player"].');';
	
	if($pid == $player["player"]) {
		$player["anchor"] = '<a id="'.$player["pID"].'" title="Weapon Stats" />^';
	} else {
		$player["anchor"] = '<a id="'.$player["pID"].'" title="Weapon Stats" />>>';
	}
	
	switch($row["game_type"]) {
		case "Deathmatch":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2">Headshots:</td>
									<td align="right" class="tablerow1">'.$player["headshots"].'</td>
									<td class="tablerow2">Knifings:</td>
									<td align="right" class="tablerow1">'.$player["knifings"].'</td>
									<td class="tablerow2">Suicides:</td>
									<td class="tablerow1">'.$player["suicides"].'</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td>
									<td class="tablerow2">Score:</td>
									<td align="right" class="tablerow1">'.$player["score1"].'</td></tr>';
			$colCnt = '15';
		break;
		case "Team Deathmatch":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2">Headshots:</td>
									<td align="right" class="tablerow1">'.$player["headshots"].'</td>
									<td class="tablerow2">Knifings:</td>
									<td align="right" class="tablerow1">'.$player["knifings"].'</td>
									<td class="tablerow2">Med Saves:</td>
									<td align="right" class="tablerow1">'.$player["medsaves"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '15';
		break;
		case "Koth":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2" title="Zone Time">Ztime:</td>
									<td align="right" class="tablerow1" title="Zone Time">'.$player["score1"].'</td>
									<td class="tablerow2" title="Zone Attack Kills">ZAttKls:</td>
									<td align="right" class="tablerow1" title="Zone Attack Kills">'.$player["score2"].'</td>
									<td class="tablerow2" title="Zone Defend Kills">ZDefKls:</td>
									<td align="right" class="tablerow1" title="Zone Defend Kills">'.$player["score3"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '15';
		break;
		case "Team King of the Hill":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2" title="Zone Time">Ztime:</td>
									<td align="right" class="tablerow1" title="Zone Time">'.$player["score1"].'</td>
									<td class="tablerow2" title="Zone Attack Kills">ZAttKls:</td>
									<td align="right" class="tablerow1" title="Zone Attack Kills">'.$player["score2"].'</td>
									<td class="tablerow2" title="Zone Defend Kills">ZDefKls:</td>
									<td align="right" class="tablerow1" title="Zone Defend Kills">'.$player["score3"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '15';
		break;
		case "Attack and Defend":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2" title="Targets">Targets:</td>
									<td align="right" class="tablerow1" title="Targets">'.$player["score1"].'</td>
									<td class="tablerow2" title="Zone Attack Kills">ZAttKls:</td>
									<td align="right" class="tablerow1" title="Zone Attack Kills">'.$player["score2"].'</td>
									<td class="tablerow2" title="Zone Defend Kills">ZDefKls:</td>
									<td align="right" class="tablerow1" title="Zone Defend Kills">'.$player["score3"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '15';
		break;
		case "Search and Destroy":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2" title="Targets">Targets:</td>
									<td align="right" class="tablerow1" title="Targets">'.$player["score1"].'</td>
									<td class="tablerow2" title="Zone Attack Kills">ZAttKls:</td>
									<td align="right" class="tablerow1" title="Zone Attack Kills">'.$player["score2"].'</td>
									<td class="tablerow2" title="Zone Defend Kills">ZDefKls:</td>
									<td align="right" class="tablerow1" title="Zone Defend Kills">'.$player["score3"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '15';
		break;
		case "Capture the Flag":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
			                        <td class="tablerow1">&nbsp;</td>
									<td class="tablerow2" title="Flags Captured">F Capt:</td>
									<td align="right" class="tablerow1" title="Flags Captured">'.$player["score1"].'</td>
									<td class="tablerow2" title="Flags Saved">F Saved:</td>
									<td align="right" class="tablerow1" title="Flags Saved">'.$player["score2"].'</td>
									<td class="tablerow2" title="Flag Carriers Killed">C Kills:</td>
									<td align="right" class="tablerow1" title="Flag Carriers Killed">'.$player["score3"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '13';
		break;
		case "Flagball":
			$player["gameStats"] = '<tr>
			                            <td class="tablerow1" title="Weapon Stats" onclick="'.$player["onlickPID"].'">
										    '.$player["anchor"].'
										</td>
									<td class="tablerow1">&nbsp;</td>
									<td class="tablerow2" title="Flags Captured">F Capt:</td>
									<td align="right" class="tablerow1" title="Flags Captured">'.$player["score1"].'</td>
									<td class="tablerow2" title="Flag Carriers Killed">C Kills:</td>
									<td align="right" class="tablerow1" title="Flag Carriers Killed">'.$player["score3"].'</td>
									<td class="tablerow2">Med Saves:</td>
									<td align="right" class="tablerow1">'.$player["medsaves"].'</td>
									<td class="tablerow2">PSP Takeover %:</td>
									<td class="tablerow1">'.$player["pspPer"].'% ('.$player["pspattempts"].')</td>
									<td align="right" class="tablerow2">Double Kills:</td>
									<td align="right" class="tablerow2">'.$player["doublekills"].'</td></tr>';
			$colCnt = '15';
		break;
	}
	
	if($pid == $player["player"]) {						
			$player["gameStats"] .= '<tr>
				                         <td colspan="2"></td>
										 <td colspan="10">
										     <table class="tablerow2" width="100%">
											     <tr>
												     <td align="left">Weapon</td>
													 <td align="center">Shots Fired</td>
													 <td align="center">Kills</td>
													 <td align="center">Accuracy</td>
													 <td align="center">Shots/min</td>
													 <td align="center">Kills/min</td>
													 <td align="center">Time Used</td>
												 </tr>';
									$wpSpl = explode("\n", $player["wpns"]);
									foreach($wpSpl as $wpnData) {
										if($wpnData != "") {
											$wpdSpl = explode("_", $wpnData);
											$accuracy = round(($wpdSpl['2']*100)/$wpdSpl['3'], 2);
											$shotsMin = round(($wpdSpl['3']*60)/$wpdSpl['1'], 2);
											$killsMin = round(($wpdSpl['2']*60)/$wpdSpl['1'], 2);
											if(!strstr($accuracy, ".")) $accuracy .= ".00";
											if(!strstr($shotsMin, ".")) $shotsMin .= ".00";
											if(!strstr($killsMin, ".")) $killsMin .= ".00";
											if(substr($accuracy, -2) == ".") $accuracy .= "0";
											if(substr($shotsMin, -2) == ".") $shotsMin .= "0";
											if(substr($killsMin, -2) == ".") $killsMin .= "0";
						$player["gameStats"] .= '<tr class="tablerow1">
												     <td align="left">'.$wpdSpl['0'].'</td>
													 <td align="center">'.$wpdSpl['3'].'</td>
													 <td align="center">'.$wpdSpl['2'].'</td>
													 <td align="center">'.$accuracy.' %</td>
													 <td align="center">'.$shotsMin.'</td>
													 <td align="center">'.$killsMin.'</td>
													 <td align="center">'.FormatTime($wpdSpl['1']).'</td>
												</tr>';
										}
									}
			$player["gameStats"] .= '		 </table>
											 </td>
									</tr>';
		}

		$player["gameStats"] .= '<tr class="tableheader"><td colspan="12">&nbsp;</td></tr>';

    $player["num"]   = $count + 1;
    $blueTeam[$count] = $player;
    $count++;
  }
  
  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_var("pidVal", $pidVal);
  $tpl->set_var("teamName", $teamName);
  $tpl->set_var("linkSet", $linkSet);
  $tpl->set_var("blueSel", $blueSel);
  $tpl->set_var("redSel", $redSel);
  $tpl->set_var("colCnt", $colCnt);
  $tpl->set_var("gameHeaders", $gameHeaders);
  $tpl->set_loop("blueTeam",   $blueTeam);
  $tpl->set_loop("redTeam",   $redTeam);
  $tpl->process("template", "game_details");
  $content = $tpl->process("", "template", 1);
  
} else {
  $content = "Error: template 'weapon_stats' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/game_details.php 1.0.5</div>
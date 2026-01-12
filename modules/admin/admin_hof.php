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
 
$team_games = array(
  0 => "Ignore",
  1 => "Played",
  2 => "Won",
);

$rating_pts = array(
  0 => "Ignore",
  1 => "Use",
);

$time_played = array(
  0 => "Ignore",
  1 => "Use",
);

$awards = array(
  0 => "Ignore",
  1 => "Use",
);

$wpnawards = array(
  0 => "Ignore",
  1 => "Use",
);

$kills = array(
  0 => "Ignore",
  1 => "Use",
);

$query = DBQuery("SELECT * FROM $hof_table");

if(file_exists($base_folder."templates/admin/admin_hof.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_hof.htm", 1);
  
  $row = DBFetchArray($query);
  if($row["team_games"] === '0' AND $row["rating_pts"] === '0' AND $row["time_played"] ==='0' AND 
      $row["awards"] === '0' AND $row["wpn_awards"] === '0' AND $row["kills"] === '0'){
    $row["games"] = "<td align=\"center\">No criteria set.</td>";
	$row["crit1"] = ""; $row["crit2"] = ""; $row["crit3"] = "";
	$row["crit4"] = ""; $row["crit5"] = ""; $row["crit6"] = "";
  } else {
    $row["games"] = "<td colspan=\"2\"></td>";
    if($row["team_games"] > 0) {
	  if($row["games_type"] == "P"){$row["type"] = "played";} else {$row["type"] = "won";}
	  $row["crit1"] = "<td class=\"tablerow2\">Team Games {type}:</td>
					   <td class=\"tablerow1\">{team_games}</td>";
	} else {
	  $row["crit1"] = "<td></td>";
	}
    if($row["rating_pts"] > 0) {
    $row["crit2"] = "<td class=\"tablerow2\">Rating Pts:</td>
					 <td class=\"tablerow1\">{rating_pts}</td>";
	} else {
	  $row["crit2"] = "<td></td>";
	}
	if($row["time_played"] > 0) {
    $row["crit3"] = "<td class=\"tablerow2\">Time Played:</td>
					 <td class=\"tablerow1\">{time_played}</td>";
	} else {
	  $row["crit3"] = "<td></td>";
	}
	if($row["awards"] > 0) {
    $row["crit4"] = "<td class=\"tablerow2\">Awards Gained:</td>
					 <td class=\"tablerow1\">{awards}</td>";
	} else {
	  $row["crit4"] = "<td></td>";
	}
	if($row["wpn_awards"] > 0) {
    $row["crit5"] = "<td class=\"tablerow2\">Weapon Awards Gained:</td>
					 <td class=\"tablerow1\">{time_played}</td>";
	} else {
	  $row["crit5"] = "<td></td>";
	}
	if($row["kills"] > 0) {
    $row["crit6"] = "<td class=\"tablerow2\">Kills Gained:</td>
					 <td class=\"tablerow1\">{kills}</td>";
	} else {
	  $row["crit6"] = "<td></td>";
	}
  }
  
        foreach($row as $field => $value) {
      $tpl->set_var($field, $value);
    }

  $games_select = "<SELECT name=\"games\" class=\"cinput\">";
  foreach($team_games as $field => $value) {
    if($row["field"] == $value) {
      $games_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $games_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $rating_select = "<SELECT name=\"ratingpts\" class=\"cinput\">";
  foreach($rating_pts as $field => $value) {
    if($row["field"] == $value) {
      $rating_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $rating_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $ptime_select = "<SELECT name=\"ptime\" class=\"cinput\">";
  foreach($time_played as $field => $value) {
    if($row["field"] == $value) {
      $ptime_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $ptime_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $awards_select = "<SELECT name=\"awards\" class=\"cinput\">";
  foreach($awards as $field => $value) {
    if($row["field"] == $value) {
      $awards_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $awards_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $wpn_awards_select = "<SELECT name=\"wpnawards\" class=\"cinput\">";
  foreach($wpnawards as $field => $value) {
    if($row["field"] == $value) {
      $wpn_awards_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $wpn_awards_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }
  
  $kills_select = "<SELECT name=\"kills\" class=\"cinput\">";
  foreach($kills as $field => $value) {
    if($row["field"] == $value) {
      $kills_select .= "<OPTION value=\"$value\" selected=\"selected\">$value</OPTION>";
    } else {
      $kills_select .= "<OPTION value=\"$value\">$value</OPTION>";
    }
  }


  $tpl->set_var("mainscript",        $mainscript);
  $tpl->set_var("base",              $base_folder);
  $tpl->set_var("games_select",      $games_select);
  $tpl->set_var("rating_select",     $rating_select);
  $tpl->set_var("ptime_select",      $ptime_select);
  $tpl->set_var("awards_select",     $awards_select);
  $tpl->set_var("wpn_awards_select", $wpn_awards_select);
  $tpl->set_var("kills_select",      $kills_select);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_hof' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_hof.php 1.0.0</div>
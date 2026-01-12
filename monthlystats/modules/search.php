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

$pageurl = $mainscript."action=$action";
$formurl = $mainscript."action=$action";

$query = DBQuery("SELECT 
                    id     AS id     ,
                    name   AS name   ,
                    rating AS rating  
                  FROM $players_table
                  ORDER BY rating DESC");

if(file_exists($base_folder."templates/search.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/search.htm");

  $players = array();
  $row     = array();
  $count   = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
  
    $rank = GetRank($row["rating"]);
    $row["rank_name"]      = $rank["name"];
    $row["rank_image"]     = $rank["image"];
    $row["rank_thumbnail"] = $rank["thumbnail"];

    $playername  = htmlspecialchars($playername);
    $row["name"] = htmlspecialchars(base64_decode($row["name"]));
    
    if($playername == "") {
      $row["num"]      = $count + 1;
      $players[$count] = $row;
      $count++;
    } elseif(stristr($row["name"], $playername)) {
      $row["num"]      = $count + 1;
      $players[$count] = $row;
      $count++;
    }
  }

  $tpl->set_var("mainscript", $mainscript);
  $tpl->set_var("formurl",    $formurl);
  $tpl->set_var("base",       $base_folder);
  $tpl->set_loop("players",   $players);
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'search' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">monthlystats/modules/search.php 1.0.0</div>
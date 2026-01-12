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

$tableheader = array(
  0 => "id",
  1 => "name",
  2 => "kills",
  3 => "shots_fired",
  4 => "time_used",
  5 => "accuracy",
);                             

if(!isset($sort)     || $sort     == "") $sort     = "id";
if(!isset($order)    || $order    == "") $order    = "ASC";

$pageurl = $mainscript."action=$action&sort=$sort&order=$order";
$formurl = $mainscript."action=$action&sort=$sort&order=$order";

$query = DBQuery("SELECT 
                    $weapons_table.id                                            AS id           ,
                    $weapons_table.name                                          AS name         ,
					$weaponstats_table.weapon                                    AS weapon       ,
                    SUM($weaponstats_table.kills)                                AS kills        ,
                    SUM($weaponstats_table.shots)                                AS shots_fired  ,
                    SUM($weaponstats_table.time)                                 AS time_used    ,
                    SUM($weaponstats_table.kills)*100/if(SUM($weaponstats_table.shots)=0, 1, SUM($weaponstats_table.shots)) AS accuracy       
                  FROM $weapons_table, $weaponstats_table
                  WHERE $weaponstats_table.weapon = $weapons_table.id
                  GROUP BY $weaponstats_table.weapon
                  ORDER BY $sort $order, $weapons_table.name DESC");

if(file_exists($base_folder."templates/admin/admin_weapons.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_weapons.htm", 1);

  foreach($tableheader as $field => $value) {
    $value_sort = $value . "_sort";
    if($value == $sort) {
      if($order == "ASC") {
        $tpl->set_var($value, $selectedasc);
        $tpl->set_var($value_sort, $mainscript."action=$action&sort=$value&order=DESC");
      } else {
        $tpl->set_var($value, $selecteddesc);
        $tpl->set_var($value_sort, $mainscript."action=$action&sort=$value&order=ASC");
      }
    } else {
      $tpl->set_var($value, $unselected);
      $tpl->set_var($value_sort, $mainscript."action=$action&sort=$value&order=DESC");
    }
  }

  $weapons  = array();
  $row   = array();
  $count = 0;

  while($row = DBFetchArray($query)) {
    if($count % 2 == 0){
      $row["class"] = $rowclass1;
    } else {
      $row["class"] = $rowclass2;
    }
  
    $row["image"] = isset($wpn_img[$row["name"]]) ? $wpn_img[$row["name"]] : "";
    $row["time"]  = FormatTime($row["time_used"]);

    if($row["image"] != "") {
      $safe_name = htmlspecialchars($row["name"], ENT_QUOTES);
      $row["pic"] = '<img src="' . $base_folder . $row["image"] . '" alt="' . $safe_name . '" style="height:24px;">';
      $row["wp_class"] = "";
    } else {
      $row["pic"] = "N/A";
      $row["wp_class"] = "red";
    }
    
    $row["num"]   = $count + 1;
    $weapons[$count] = $row;
    $count++;
  }

  $tpl->set_loop("weapons",    $weapons);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("formurl",     $formurl);
  $tpl->set_var("base",        $base_folder);
  $tpl->process("template", "main");
  $content = $tpl->process("", "template", 1);

} else {
  $content = "Error: template 'admin_weapons' does not exist";
}

?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_weapons.php 1.0.0</div>
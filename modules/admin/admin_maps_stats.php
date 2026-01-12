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
 * Finalized     : 30th April 2005
 * Author        : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

$query = DBQuery("SELECT 
                    $maps_table.id                                            AS id            ,
                    $maps_table.name                                          AS name          ,
                    $maps_table.thumbnail                                     AS thumbnail     ,
                    $maps_table.image                                         AS image         ,
                    $maps_table.hosted                                        AS hosted        ,
                    SUM($mapstats_table.kills)                                AS kills         ,
					SUM($mapstats_table.deaths)                               AS deaths        ,
                    $maps_table.last_played                                   AS last_played    
                  FROM $maps_table, $mapstats_table
                  WHERE $mapstats_table.map = $maps_table.id 
				  AND $maps_table.id = '$id' 
                  GROUP BY $mapstats_table.map");

if(file_exists($base_folder."templates/admin/admin_maps.htm")) {
  $tpl = new phemplate();
  $tpl->set_file("template", $base_folder."templates/admin/admin_maps.htm", 1);
  
  $row = DBFetchArray($query);

  $row["name"] = htmlspecialchars(base64_decode($row["name"]));
  $row["time"] = FormatTime($row["time"]);
  if(!($row["image"])) {$row["image"] = "maps\l_none.jpg";}
  if(!($row["thumbnail"])) {$row["thumbnail"] = "maps\s_none.jpg";}
  
  foreach($row as $field => $value) {
    $tpl->set_var($field, $value);
  }

  $tpl->set_var("id",          $id);
  $tpl->set_var("adminaction", $adminaction);
  $tpl->set_var("module_name", $module_name);
  $tpl->set_var("base",        $base_folder);
  $tpl->process("template", "maps_stats");
  $content = $tpl->process("", "template", 1);
 
} else {
  $content .= "Error: template 'admin/admin_maps' does not exist";
}
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/admin_maps_stats.php 1.0.0</div>
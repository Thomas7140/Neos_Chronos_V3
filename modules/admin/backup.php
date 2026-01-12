<?php

$dbhost     = "localhost";	// Database host
$dbname     = "xxxxxxxxxx";	// Database name
$dbusername = "xxxxxxxxxxx";		// Database user name
$dbuserpw   = "xxxxxxxx";			// Database password
mysql_connect($dbhost,$dbusername,$dbuserpw);
mysql_select_db($dbname) or die("Unable to select database.");


function datadump ($table) {

    $result .= "# Dump of $table \n";
    $result .= "# Dump DATE : " . date("d-M-Y") ."\n\n";

    $query = mysql_query("select * from $table");
    $num_fields = mysql_num_fields($query);
    $numrow = mysql_num_rows($query);

    for ($i =0; $i<$numrow; $i++) {
  $result .= "INSERT INTO ".$table." VALUES(";
    for($j=0; $j<$num_fields; $j++) {
    $row[$j] = addslashes($row[$j]);
    $row[$j] = ereg_replace("\n","\\n",$row[$j]);
    if (isset($row[$j])) $result .= "\"$row[$j]\"" ; else $result .= "\"\"";
    if ($j<($num_fields-1)) $result .= ",";
   }    
      $result .= ");\n";
     }
     return $result . "\n\n\n";
  }

$array = datadump ("array");


$array = array($aliases_table, $awards_table, $hof_table, $log_table, $mapstats_m_table, $stats_m_table, $weaponstats_m_table, 
                 $maps_table, $mapstats_table, $monthawards_table, $playerawards_table, $players_table, $ranks_table, 
				 $serverhistory_table, $servers_table, $serverstats_table, $squads_table, $stats_table, $weapons_table, 
				 $weaponstats_table);

$content = $array;

$file_name = "BU_Neos_Chronos_".date("d_M_Y-H_i_s").".sql";
//Header("Content-type: application/octet-stream"); 
//Header("Content-Disposition: attachment; filename=$file_name");
echo $content; 
exit;
?>

<div style="visibility:hidden; font-size:xx-small;">modules/admin/backup.php 1.0.0</div>
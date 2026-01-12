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
 *
 * Finalized     : 15th May 2005
 * Modified by   : Peter Jones a.k.a »TÐÖ« Ãzràél
 * E-mail        : azrael@tdosquad.co.uk
 * Website       : http://www.tdosquad.co.uk
 */

require "config.php";
require "common.php";
require "weapons.php";
require "gametypes.php";
require "rating.php";
require "functions.php";

if($_POST)   foreach($_POST   as $Key=>$Value) $$Key = $Value;
if($_COOKIE) foreach($_COOKIE as $Key=>$Value) $$Key = $Value;
if($_GET)    foreach($_GET    as $Key=>$Value) $$Key = $Value;
if($_SERVER) foreach($_SERVER as $Key=>$Value) $$Key = $Value;
if($_ENV)    foreach($_ENV    as $Key=>$Value) $$Key = $Value;

$db_link = mysqli_connect($dbhost, $dbusername, $dbuserpw, $dbname);
if (!$db_link) {
  die('Database connection failed: ' . mysqli_connect_error());
}

function install_query($db_link, $query) {
  $result = mysqli_query($db_link, $query);
  if (!$result) {
    die(mysqli_error($db_link));
  }
  return $result;
}

MakeTableNames();

echo "Neos.Chronos 2.0.0 Installation<br><br>\n";

install_query($db_link, "CREATE TABLE $aliases_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  from_name varchar(64) NOT NULL default '',
  to_name varchar(64) NOT NULL default '',
  KEY id (id)
);");

echo "Table '$aliases_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $awards_table (
  id bigint(20) NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  image varchar(64) NOT NULL default '',
  field varchar(64) NOT NULL default '0',
  value bigint(20) NOT NULL default '0',
  description text NOT NULL,
  type enum('A','W') NOT NULL default 'A',
  KEY id (id)
);");

install_query($db_link, "INSERT INTO $awards_table VALUES (1, 'Army Commendation Medal', 'awards/armycom1.gif', 'time_played', 600, 'Given for 10 minutes of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (2, 'Army Commendation Medal with 1 bronze Oak leaf Cluster', 'awards/armycom2.gif', 'time_played', 3600, 'Given for 1 hour of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (3, 'Army Commendation Medal with 2 bronze Oak leaf Clusters', 'awards/armycom3.gif', 'time_played', 36000, 'Given for 10 hours of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (4, 'Army Commendation Medal with 3 bronze Oak leaf Clusters', 'awards/armycom4.gif', 'time_played', 180000, 'Given for 50 hours of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (5, 'Army Commendation Medal with 4 bronze Oak leaf Clusters', 'awards/armycom5.gif', 'time_played', 360000, 'Given for 100 hours of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (6, 'Army Commendation Medal with 1 silver Oak leaf Cluster', 'awards/armycom6.gif', 'time_played', 1080000, 'Given for 300 hours of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (7, 'Marksman Badge', 'awards/markman1.gif', 'kills', 100, 'Given for 100 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (8, 'SharpShooter Badge', 'awards/markman2.gif', 'kills', 200, 'Given for 200 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (9, 'Expert Marksman Badge', 'awards/markman3.gif', 'kills', 500, 'Given for 500 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (10, 'Bronze Star', 'awards/bstar1.gif', 'kills', 1000, 'Given for 1000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (11, 'Bronze Star with 1 Bronze Oak Leaf Cluster', 'awards/bstar2.gif', 'kills', 1500, 'Given for  1500 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (12, 'Bronze Star with 2 Bronze Oak Leaf Clusters', 'awards/bstar3.gif', 'kills', 2000, 'Given for  2000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (13, 'Bronze Star with 3 Bronze Oak Leaf Clusters', 'awards/bstar4.gif', 'kills', 3000, 'Given for  3000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (14, 'Bronze Star with 4 Bronze Oak Leaf Clusters', 'awards/bstar5.gif', 'kills', 5000, 'Given for  5000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (15, 'Bronze Star with 1 Silver Oak Leaf Cluster', 'awards/bstar6.gif', 'kills', 7000, 'Given for  7000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (16, 'Bronze Star with 2 Silver Oak Leaf Clusters', 'awards/bstar7.gif', 'kills', 9000, 'Given for  9000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (17, 'Bronze Star with 2 Silver Oak Leaf Clusters', 'awards/bstar8.gif', 'kills', 12000, 'Given for  12000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (18, 'Bronze Star with 2 Silver Oak Leaf Clusters', 'awards/bstar9.gif', 'kills', 15000, 'Given for  15000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (19, 'Silver Star', 'awards/sstar.gif', 'kills', 20000, 'Given for  20000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (20, 'Combat Infantryman Badge 1st Award', 'awards/infant1.gif', 'team_games_won', 10, 'Given for 10 team games won', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (21, 'Combat Infantryman Badge 2nd Award', 'awards/infant2.gif', 'team_games_won', 50, 'Given for 50 team games won', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (22, 'Combat Infantryman Badge 3rd Award', 'awards/infant3.gif', 'team_games_won', 200, 'Given for 200 team games won', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (23, 'Hill Giant Medal 1st Award', 'awards/giant1.gif', 'zone_time', 7200, 'Given for 2 hours in the zone', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (24, 'Hill Giant Medal 2nd Award', 'awards/giant2.gif', 'zone_time', 36000, 'Given for 10 hours in the zone', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (25, 'Hill Giant Medal 3rd Award', 'awards/giant3.gif', 'zone_time', 180000, 'Given for 50 hours in the zone', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (26, 'Hill Giant Medal 4th Award', 'awards/giant4.gif', 'zone_time', 360000, 'Given for 100 hours in the zone', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (27, 'Headhunter''s Medal 1st Award', 'awards/head1.gif', 'headshots', 100, 'Given for 100 headshots', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (28, 'Headhunter''s Medal 2nd Award', 'awards/head2.gif', 'headshots', 500, 'Given for 500 headshots', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (29, 'Headhunter''s Medal 3rd Award', 'awards/head3.gif', 'headshots', 1000, 'Given for 1000 headshots', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (30, 'Headhunter''s Medal 4th Award', 'awards/head4.gif', 'headshots', 2000, 'Given for 2000 headshots', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (31, 'Glory Badge 1st Award', 'awards/glory1.gif', 'flags_captured', 100, 'Given for 100 flags captured', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (32, 'Glory Badge 2nd Award', 'awards/glory2.gif', 'flags_captured', 200, 'Given for 200 flags captured', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (33, 'Glory Badge 3rd Award', 'awards/glory3.gif', 'flags_captured', 500, 'Given for 500 flags captured', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (34, 'CQB Badge 1st Award', 'awards/cqb1.gif', 'knifings', 50, 'Given for 50 knifings', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (35, 'CQB Badge 2nd Award', 'awards/cqb2.gif', 'knifings', 100, 'Given for 100 knifings', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (36, 'CQB Badge 3rd Award', 'awards/cqb3.gif', 'knifings', 200, 'Given for 200 knifings', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (37, 'Silver Ka-Bar Award', 'awards/kabar_s.gif', 'knifings', 500, 'Given for 500 knifings', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (38, 'Gold Ka-Bar Award', 'awards/kabar_g.gif', 'knifings', 1000, 'Given for 1000 knifings', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (39, 'Recovery medal 1st award', 'awards/recover1.gif', 'flags_saved', 50, 'Given for 50 flags saved', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (40, 'Recovery medal 2nd award', 'awards/recover2.gif', 'flags_saved', 100, 'Given for 100 flags saved', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (41, 'Recovery medal 3rd award', 'awards/recover3.gif', 'flags_saved', 200, 'Given for 200 flags saved', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (42, 'Recovery medal 4th award', 'awards/recover4.gif', 'flags_saved', 500, 'Given for 500 flags saved', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (43, 'Combat Medical Badge 1st Award', 'awards/medical1.gif', 'medic_saves', 10, 'Given for 10 medic saves', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (44, 'Combat Medical Badge 2nd Award', 'awards/medical2.gif', 'medic_saves', 50, 'Given for 50 medic saves', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (45, 'Combat Medical Badge 3rd Award', 'awards/medical3.gif', 'medic_saves', 150, 'Given for 150 medic saves', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (46, 'Combat Medical Badge 4th Award', 'awards/medical4.gif', 'medic_saves', 500, 'Given for 500 medic saves', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (47, 'Sapper''s Badge 1st Award', 'awards/sapper1.gif', 'targets_destroyed', 100, 'Given for 100 targets destroyed', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (48, 'Sapper''s Badge 2nd Award', 'awards/sapper2.gif', 'targets_destroyed', 200, 'Given for 200 targets destroyed', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (49, 'Sapper''s Badge 5th Award', 'awards/sapper3.gif', 'targets_destroyed', 500, 'Given for 500 targets destroyed', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (50, 'Distinguished Service Cross', 'awards/dscross.gif', 'time_played', 1800000, 'Given for 500 hours of playing', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (51, 'Medal of Honor', 'awards/moh.gif', 'kills', 40000, 'Given for 40000 kills', 'A');");
install_query($db_link, "INSERT INTO $awards_table VALUES (52, 'Car15 Award', 'awards/car15.gif', 'car15_kills', 1000, 'Given for 1000 car15 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (53, 'G36 Award', 'awards/g36.gif', 'g36_kills', 1000, 'Given for 1000 g36 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (54, 'G3 Award', 'awards/g3.gif', 'g3_kills', 1000, 'Given for 1000 g3 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (55, 'M60 Award', 'awards/m60.gif', 'm60_kills', 1000, 'Given for 1000 m60 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (56, 'Car15/203 Award', 'awards/car15203.gif', 'car15203_kills', 500, 'Given for 500 car15/203 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (57, 'M16 Award', 'awards/m16.gif', 'm16_kills', 1000, 'Given for 1000 m16 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (58, 'M16/203 Award', 'awards/m16203.gif', 'm16203_kills', 1000, 'Given for 1000 m16/203 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (59, 'MP5 Award', 'awards/mp5.gif', 'mp5_kills', 500, 'Given for  500 mp5 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (60, 'M249 saw Award', 'awards/m249saw.gif', 'm249saw_kills', 1000, 'Given for 1000 m249 saw kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (61, 'M240 Award', 'awards/m240.gif', 'm240_kills', 1000, 'Given for 1000 m240 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (62, 'Car15/203 - Grenade Award', 'awards/car15203g.gif', 'car15203_grenade_kills', 1000, 'Given for 1000 car15/203 - grenade kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (63, 'M16/203 - Grenade Award', 'awards/m16203g.gif', 'm16203_grenade_kills', 1000, 'Given for 1000 m16/203 - grenade kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (64, 'Colt .45 Award', 'awards/colt.gif', 'colt_kills', 250, 'Given for  250 colt .45 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (65, 'M9 Beretta Award', 'awards/m9.gif', 'm9_kills', 250, 'Given for  250 m9 beretta kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (66, 'M21 Award', 'awards/m21.gif', 'm21_kills', 500, 'Given for  500 m21 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (67, 'PSG1 Award', 'awards/psg1.gif', 'psg1_kills', 500, 'Given for  500 psg1 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (68, 'M24 Award', 'awards/m24.gif', 'm24_kills', 500, 'Given for  500 m24 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (69, 'MCRT .300 Tactical Award', 'awards/mcrt.gif', 'mcrt_kills', 500, 'Given for  500 mcrt .300 tactical kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (70, 'Barrett .50 Cal Award', 'awards/barrett.gif', 'barrett_kills', 500, 'Given for  500 barrett .50 cal kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (71, 'Remington Shotgun Award', 'awards/remin.gif', 'shotgun_kills', 250, 'Given for  250 remington shotgun kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (72, 'Frag Grenade Award', 'awards/gren.gif', 'grenade_kills', 250, 'Given for  250 frag grenade killd', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (73, 'AT4 Award', 'awards/at4.gif', 'at4_kills', 250, 'Given for  250 at4 kills', 'W');");
install_query($db_link, "INSERT INTO $awards_table VALUES (74, 'Claymore Award', 'awards/clay.gif', 'claymore_kills', 250, 'Given for  250 claymore kills', 'W');");
 
echo "Table '$awards_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE `$hof_table` (
  games_type enum('P','W') NOT NULL default 'P',
  team_games bigint(20) NOT NULL default '0',
  rating_pts bigint(20) NOT NULL default '0',
  time_played bigint(20) NOT NULL default '0',
  awards bigint(20) NOT NULL default '0',
  wpn_awards bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0'
);");

echo "Table '$hof_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $log_table (
  server bigint(20) NOT NULL default '0',
  datetime datetime NOT NULL default '0000-00-00 00:00:00'
);");

echo "Table '$log_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $mapstats_m_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  record bigint(20) NOT NULL default '0',
  map bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0',
  deaths bigint(20) NOT NULL default '0',
  score bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  KEY id (id)
);");

echo "Table '$mapstats_m_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $stats_m_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  player bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0',
  deaths bigint(20) NOT NULL default '0',
  murders bigint(20) NOT NULL default '0',
  suicides bigint(20) NOT NULL default '0',
  knifings bigint(20) NOT NULL default '0',
  sniper_kills bigint(20) NOT NULL default '0',
  headshots bigint(20) NOT NULL default '0',
  medic_saves bigint(20) NOT NULL default '0',
  revives bigint(20) NOT NULL default '0',
  pspattempts bigint(20) NOT NULL default '0',
  psptakeovers bigint(20) NOT NULL default '0',
  doublekills bigint(20) NOT NULL default '0',
  score_1 bigint(20) NOT NULL default '0',
  score_2 bigint(20) NOT NULL default '0',
  score_3 bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  games bigint(20) NOT NULL default '0',
  wins bigint(20) NOT NULL default '0',
  draws bigint(20) NOT NULL default '0',
  server bigint(20) NOT NULL default '0',
  game_type varchar(64) NOT NULL default '',
  last_played datetime NOT NULL default '0000-00-00 00:00:00',
  KEY id (id)
);");

echo "Table '$stats_m_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $weaponstats_m_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  record bigint(20) NOT NULL default '0',
  weapon bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0',
  shots bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  KEY id (id)
);");

echo "Table '$weaponstats_m_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $maps_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  name varchar(128) NOT NULL default '',
  image varchar(64) NOT NULL default '',
  thumbnail varchar(64) NOT NULL default '',
  file varchar(128) NOT NULL default '',
  hosted bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  game_type varchar(64) NOT NULL default '',
  last_played datetime NOT NULL default '0000-00-00 00:00:00',
  KEY id (id)
);");

echo "Table '$maps_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $mapstats_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  record bigint(20) NOT NULL default '0',
  map bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0',
  deaths bigint(20) NOT NULL default '0',
  score bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  KEY id (id)
);");

install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Road Rage")."',      'maps/l_stock_dm1.jpg',     'maps/s_stock_dm1.jpg',     '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("City Madness")."',   'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Cracked")."',        'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Walled In")."',      'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Mines Eye")."',      'maps/l_stock_dm5.jpg',     'maps/s_stock_dm5.jpg',     '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Stadium Riot")."',   'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Funeral")."', 'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Devil Dogs")."',     'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squirrels Nest")."', 'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Deathmatch', '');");

install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict A")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict B")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset A")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset B")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run A")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run B")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island A")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island B")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island A")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island B")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Culture Clash A")."', 'maps/l_stock_tdm1-2.jpg',  'maps/s_stock_tdm1-2.jpg',  '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Culture Clash B")."', 'maps/l_stock_tdm1-2.jpg',  'maps/s_stock_tdm1-2.jpg',  '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Mean Streets A")."',  'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Mean Streets B")."',  'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Panic Attack A")."',  'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Panic Attack B")."',  'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Rampage A")."',       'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Rampage B")."',       'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Double Barrel A")."', 'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Double Barrel B")."', 'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Hornets Nest A")."',  'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Hornets Nest B")."',  'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Sidewinder A")."',    'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Sidewinder B")."',    'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Clue Map A")."',      'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Team Deathmatch', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Clue Map B")."',      'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Team Deathmatch', '');");

install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict A")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict B")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset A")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset B")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run A")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run B")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island A")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island B")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island A")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island B")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Meat Grinder A")."',     'maps/l_stock_tkoth1-2.jpg', 'maps/s_stock_tkoth1-2.jpg', '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Meat Grinder B")."',     'maps/l_stock_tkoth1-2.jpg', 'maps/s_stock_tkoth1-2.jpg', '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("House of Pain A")."',    'maps/l_stock_mog.jpg',      'maps/s_stock_mog.jpg',      '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("House of Pain B")."',    'maps/l_stock_mog.jpg',      'maps/s_stock_mog.jpg',      '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Eye of the Dead A")."',  'maps/l_stock_walls.jpg',    'maps/s_stock_walls.jpg',    '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Eye of the Dead B")."',  'maps/l_stock_walls.jpg',    'maps/s_stock_walls.jpg',    '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Dust Devil A")."',       'maps/l_stock_trap.jpg',     'maps/s_stock_trap.jpg',     '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Dust Devil B")."',       'maps/l_stock_trap.jpg',     'maps/s_stock_trap.jpg',     '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Spider Web A")."',       'maps/l_stock_stadium.jpg',  'maps/s_stock_stadium.jpg',  '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Spider Web B")."',       'maps/l_stock_stadium.jpg',  'maps/s_stock_stadium.jpg',  '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Insertion A")."', 'maps/l_stock_desert.jpg',   'maps/s_stock_desert.jpg',   '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Insertion B")."', 'maps/l_stock_desert.jpg',   'maps/s_stock_desert.jpg',   '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Fox A")."',       'maps/l_stock_frontal.jpg',  'maps/s_stock_frontal.jpg',  '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Fox B")."',       'maps/l_stock_frontal.jpg',  'maps/s_stock_frontal.jpg',  '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tunnel Trouble A")."',   'maps/l_stock_weird.jpg',    'maps/s_stock_weird.jpg',    '', '', '', 'Team King of the Hill', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tunnel Trouble B")."',   'maps/l_stock_weird.jpg',    'maps/s_stock_weird.jpg',    '', '', '', 'Team King of the Hill', '');");

install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict A")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict B")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset A")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset B")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run A")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run B")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island A")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island B")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island A")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island B")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Mog Mayhem A")."',    'maps/l_stock_sd1-2.jpg',   'maps/s_stock_sd1-2.jpg',   '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Mog Mayhem B")."',    'maps/l_stock_sd1-2.jpg',   'maps/s_stock_sd1-2.jpg',   '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Firefighter A")."',   'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Firefighter B")."',   'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Roof Stalker A")."',  'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Roof Stalker B")."',  'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Savannah Town A")."', 'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Savannah Town B")."', 'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Stadium Feud A")."',  'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Stadium Feud B")."',  'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Sky Riders A")."',    'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Sky Riders B")."',    'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Trail Blazer A")."',  'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Trail Blazer B")."',  'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("The Hidden A")."',    'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Search and Destroy', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("The Hidden B")."',    'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Search and Destroy', '');");

install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict A")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict B")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset A")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset B")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run A")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run B")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island A")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island B")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island A")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island B")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crossfire A")."',      'maps/l_stock_ad1-2.jpg',   'maps/s_stock_ad1-2.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crossfire B")."',      'maps/l_stock_ad1-2.jpg',   'maps/s_stock_ad1-2.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Urban Raid A")."',     'maps/l_stock_ad3-4.jpg',   'maps/s_stock_ad3-4.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Urban Raid B")."',     'maps/l_stock_ad3-4.jpg',   'maps/s_stock_ad3-4.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Rapid Fire A")."',     'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Rapid Fire B")."',     'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Ground Fire A")."',    'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Ground Fire B")."',    'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Dust and Bones A")."', 'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Dust and Bones B")."', 'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Valkyrie A")."',       'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Valkyrie B")."',       'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Snake Pit A")."',      'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Snake Pit B")."',      'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Lost and Dead A")."',  'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Attack and Defend', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Lost and Dead B")."',  'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Attack and Defend', '');");


install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict A")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict B")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset A")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset B")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run A")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run B")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island A")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island B")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island A")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island B")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("The Barrens A")."',    'maps/l_stock_ctf1-2.jpg',   'maps/s_stock_ctf1-2.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("The Barrens B")."',    'maps/l_stock_ctf1-2.jpg',   'maps/s_stock_ctf1-2.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tug O' War A")."',     'maps/l_stock_ctf3-4.jpg',   'maps/s_stock_ctf3-4.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tug O' War B")."',     'maps/l_stock_ctf3-4.jpg',   'maps/s_stock_ctf3-4.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Groundhog Day A")."',  'maps/l_stock_ctf5-6.jpg',   'maps/s_stock_ctf5-6.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Groundhog Day B")."',  'maps/l_stock_ctf5-6.jpg',   'maps/s_stock_ctf5-6.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Block Mayhem A")."',   'maps/l_stock_mog.jpg',      'maps/s_stock_mog.jpg',      '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Block Mayhem B")."',   'maps/l_stock_mog.jpg',      'maps/s_stock_mog.jpg',      '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Burned Asphalt A")."', 'maps/l_stock_walls.jpg',    'maps/s_stock_walls.jpg',    '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Burned Asphalt B")."', 'maps/l_stock_walls.jpg',    'maps/s_stock_walls.jpg',    '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Trap A")."',    'maps/l_stock_trap.jpg',     'maps/s_stock_trap.jpg',     '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Trap B")."',    'maps/l_stock_trap.jpg',     'maps/s_stock_trap.jpg',     '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Snake Eyes A")."',     'maps/l_stock_ctf13-14.jpg', 'maps/s_stock_ctf13-14.jpg', '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Snake Eyes B")."',     'maps/l_stock_ctf13-14.jpg', 'maps/s_stock_ctf13-14.jpg', '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Storage Space A")."',  'maps/l_stock_stadium.jpg',  'maps/s_stock_stadium.jpg',  '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Storage Space B")."',  'maps/l_stock_stadium.jpg',  'maps/s_stock_stadium.jpg',  '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Dead Zone A")."',      'maps/l_stock_desert.jpg',   'maps/s_stock_desert.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Dead Zone B")."',      'maps/l_stock_desert.jpg',   'maps/s_stock_desert.jpg',   '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Hit and Run A")."',    'maps/l_stock_frontal.jpg',  'maps/s_stock_frontal.jpg',  '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Hit and Run B")."',    'maps/l_stock_frontal.jpg',  'maps/s_stock_frontal.jpg',  '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Bad Juju A")."',       'maps/l_stock_ctf21-22.jpg', 'maps/s_stock_ctf13-14.jpg', '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Bad Juju B")."',       'maps/l_stock_ctf21-22.jpg', 'maps/s_stock_ctf13-14.jpg', '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Ribcage A")."',        'maps/l_stock_weird.jpg',    'maps/s_stock_weird.jpg',    '', '', '', 'Capture the Flag', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Ribcage B")."',        'maps/l_stock_weird.jpg',    'maps/s_stock_weird.jpg',    '', '', '', 'Capture the Flag', '');");

install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict A")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Crude City Conflict B")."', 'maps/crudecityconflict_big.jpg', 'maps/crudecityconflict_thumb.jpg', '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset A")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Tequila Sunset B")."',      'maps/tequilasunset_big.jpg',     'maps/tequilasunset_thumb.jpg',     '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run A")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Smuggler's Run B")."',      'maps/smugglersrun_big.jpg',      'maps/smugglersrun_thumb.jpg',      '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island A")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Oil Island B")."',          'maps/oilisland_big.jpg',         'maps/oilisland_thumb.jpg',         '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island A")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Squid Island B")."',        'maps/squidisland_big.jpg',       'maps/squidisland_thumb.jpg',       '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Scrimmage Line A")."',  'maps/l_stock_fb1-2.jpg',   'maps/s_stock_fb1-2.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Scrimmage Line B")."',  'maps/l_stock_fb1-2.jpg',   'maps/s_stock_fb1-2.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Blitzkrieg A")."',      'maps/l_stock_fb3-4.jpg',   'maps/s_stock_fb3-4.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Blitzkrieg B")."',      'maps/l_stock_fb3-4.jpg',   'maps/s_stock_fb3-4.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Pigskin A")."',         'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Pigskin B")."',         'maps/l_stock_mog.jpg',     'maps/s_stock_mog.jpg',     '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Wall Jumper A")."',     'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Wall Jumper B")."',     'maps/l_stock_walls.jpg',   'maps/s_stock_walls.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Scarab A")."',   'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Desert Scarab B")."',   'maps/l_stock_trap.jpg',    'maps/s_stock_trap.jpg',    '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Bloodball A")."',       'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Bloodball B")."',       'maps/l_stock_stadium.jpg', 'maps/s_stock_stadium.jpg', '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Sky Fire A")."',        'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Sky Fire B")."',        'maps/l_stock_desert.jpg',  'maps/s_stock_desert.jpg',  '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Frontal Assault A")."', 'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Frontal Assault B")."', 'maps/l_stock_frontal.jpg', 'maps/s_stock_frontal.jpg', '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Frantic Traffic A")."', 'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Flagball', '');");
install_query($db_link, "INSERT INTO $maps_table VALUES ('', '".base64_encode("Frantic Traffic B")."', 'maps/l_stock_weird.jpg',   'maps/s_stock_weird.jpg',   '', '', '', 'Flagball', '');");

echo "Table '$mapstats_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $monthawards_table (
  id bigint(20) NOT NULL default '0',
  monthaward varchar(64) NOT NULL default '',
  player varchar(64) NOT NULL default '',
  value decimal(20,2) NOT NULL default '0.00',
  month_gained char(3) NOT NULL default '',
  year_gained varchar(64) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  KEY id (id)
);");

install_query($db_link, "INSERT INTO $monthawards_table VALUES (1, 'Most MOTM Awards Gained', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (2, 'Highest Rating Pts', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (3, 'Highest No. of Kills', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (4, 'Highest No. of Headshots', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (5, 'Highest Headshot Kills %', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (6, 'Highest Kill/Death Ratio', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (7, 'Most Time Played', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (8, 'Most Games Played', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (9, 'Most Games Won', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (10, 'Highest Game Win %', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (11, 'Highest Assault Rifle Accuracy (%)', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (12, 'Highest Sniper Rifle Accuracy (%)', '', '', '', 'Alltime', '0000-00-00 00:00:00');");
install_query($db_link, "INSERT INTO $monthawards_table VALUES (13, 'Highest Support Gun Accuracy (%)', '', '', '', 'Alltime', '0000-00-00 00:00:00');");


echo "Table '$monthawards_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $playerawards_table (
  id bigint(20) NOT NULL auto_increment,
  player bigint(20) NOT NULL default '0',
  award varchar(64) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY award (award,player),
  KEY id (id)
);");

echo "Table '$playerawards_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $players_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  squad bigint(20) NOT NULL default '0',
  rating bigint(20) NOT NULL default '0',
  m_rating bigint(20) NOT NULL default '0',
  awards bigint(20) NOT NULL default '0',
  wpn_awards bigint(20) NOT NULL default '0',
  motm bigint(20) NOT NULL default '0',
  dm_value bigint(20) NOT NULL default '0',
  KEY id (id)
);");

echo "Table '$players_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $ranks_table (
  id bigint(20) NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  image varchar(64) NOT NULL default '',
  thumbnail varchar(64) NOT NULL default '0',
  rating bigint(20) NOT NULL default '0',
  KEY id (id)
);");

install_query($db_link, "INSERT INTO $ranks_table VALUES (1, 'Private 4th', 'ranks/1.gif', 'ranks/thumbs/1.gif', 0);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (2, 'Private 3rd', 'ranks/2.gif', 'ranks/thumbs/2.gif', 40);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (3, 'Private 2nd', 'ranks/3.gif', 'ranks/thumbs/3.gif', 100);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (4, 'Private 1st', 'ranks/4.gif', 'ranks/thumbs/4.gif', 200);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (23, 'Lance Corporal 4th', 'ranks/5.gif', 'ranks/thumbs/5.gif', 350);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (24, 'Lance Corporal 3rd', 'ranks/6.gif', 'ranks/thumbs/6.gif', 500);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (25, 'Lance Corporal 2nd', 'ranks/7.gif', 'ranks/thumbs/7.gif', 700);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (26, 'Lance Corporal 1st', 'ranks/8.gif', 'ranks/thumbs/8.gif', 900);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (27, 'Corporal 4th', 'ranks/9.gif', 'ranks/thumbs/9.gif', 1200);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (28, 'Corporal 3rd', 'ranks/10.gif', 'ranks/thumbs/10.gif', 1500);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (29, 'Corporal 2nd', 'ranks/11.gif', 'ranks/thumbs/11.gif', 1900);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (30, 'Corporal 1st', 'ranks/12.gif', 'ranks/thumbs/12.gif', 2300);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (31, 'Master Corporal', 'ranks/13.gif', 'ranks/thumbs/13.gif', 2900);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (32, 'Sergeant', 'ranks/14.gif', 'ranks/thumbs/14.gif', 3600);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (33, 'Master Sergeant', 'ranks/15.gif', 'ranks/thumbs/15.gif', 4400);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (34, 'Sub Lieutenant', 'ranks/16.gif', 'ranks/thumbs/16.gif', 5500);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (35, 'Lieutenant', 'ranks/17.gif', 'ranks/thumbs/17.gif', 7000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (36, 'Major', 'ranks/18.gif', 'ranks/thumbs/18.gif', 9000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (37, 'Colonel', 'ranks/19.gif', 'ranks/thumbs/19.gif', 12000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (38, 'Brigadier General, 1 Star', 'ranks/20.gif', 'ranks/thumbs/20.gif', 16000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (39, 'Major General, 2 Stars', 'ranks/21.gif', 'ranks/thumbs/21.gif', 20000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (40, 'Lt. General, 3 Stars', 'ranks/22.gif', 'ranks/thumbs/22.gif', 25000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (41, 'General, 4 Stars', 'ranks/23.gif', 'ranks/thumbs/23.gif', 30000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (42, 'General, 5 Stars', 'ranks/24.gif', 'ranks/thumbs/24.gif', 35000);");
install_query($db_link, "INSERT INTO $ranks_table VALUES (43, 'Chief of State', 'ranks/25.gif', 'ranks/thumbs/25.gif', 40000);");

echo "Table '$ranks_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $playerips_table (
  id bigint(20) NOT NULL auto_increment,
  player bigint(20) NOT NULL,
  ip_address varchar(16) NOT NULL,
  last_recorded datetime NOT NULL,
  KEY id (id)
)");

echo "Table '$playerips_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $serverhistory_table (
  id bigint(20) NOT NULL auto_increment,
  serverid bigint(20) NOT NULL default '0',
  players bigint(20) NOT NULL default '0',
  name varchar(64) NOT NULL default '',
  date date NOT NULL default '0000-00-00',
  KEY id (id)
);");

echo "Table '$serverhistory_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $servers_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  serverid varchar(64) NOT NULL default '',
  server_name varchar(128) NOT NULL default '',
  map_name varchar(128) NOT NULL default '',
  game_type varchar(64) NOT NULL default '',
  game varchar(64) NOT NULL default '',
  dedicated varchar(32) NOT NULL default '',
  time bigint(20) NOT NULL default '0',
  player_names text NOT NULL,
  player_teams text NOT NULL,
  player_weapons text NOT NULL,
  info text NOT NULL,
  ip varchar(64) NOT NULL default '',
  max_players bigint(20) NOT NULL default '0',
  num_players bigint(20) NOT NULL default '0',
  age bigint(20) NOT NULL default '0',
  KEY id (id,name)
);");

echo "Table '$servers_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $serverstats_table (
  id bigint(20) NOT NULL auto_increment,
  serverid bigint(20) NOT NULL default '0',
  game_type varchar(64) NOT NULL default '0',
  games bigint(20) NOT NULL default '0',
  maps bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  KEY id (id)
);");

echo "Table '$serverstats_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $squads_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  name varchar(128) NOT NULL default '',
  tag varchar(64) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  KEY id (id)
);");

echo "Table '$squads_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $stats_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  player bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0',
  deaths bigint(20) NOT NULL default '0',
  murders bigint(20) NOT NULL default '0',
  suicides bigint(20) NOT NULL default '0',
  knifings bigint(20) NOT NULL default '0',
  sniper_kills bigint(20) NOT NULL default '0',
  headshots bigint(20) NOT NULL default '0',
  medic_saves bigint(20) NOT NULL default '0',
  revives bigint(20) NOT NULL default '0',
  pspattempts bigint(20) NOT NULL default '0',
  psptakeovers bigint(20) NOT NULL default '0',
  doublekills bigint(20) NOT NULL default '0',
  score_1 bigint(20) NOT NULL default '0',
  score_2 bigint(20) NOT NULL default '0',
  score_3 bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  games bigint(20) NOT NULL default '0',
  wins bigint(20) NOT NULL default '0',
  draws bigint(20) NOT NULL default '0',
  server bigint(20) NOT NULL default '0',
  game_type varchar(64) NOT NULL default '',
  last_played datetime NOT NULL default '0000-00-00 00:00:00',
  KEY id (id)
);");

echo "Table '$stats_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $games_table (
  id bigint(20) NOT NULL auto_increment,
  map_id bigint(20) NOT NULL,
  winner tinyint(1) NOT NULL,
  server bigint(20) NOT NULL,
  game_type varchar(64) NOT NULL,
  date_played datetime NOT NULL,
  KEY id (id)
)");


echo "Table '$games_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $playergames_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  game_id bigint(20) NOT NULL,
  player bigint(20) NOT NULL default '0',
  playerip varchar(15) NOT NULL,
  experience bigint(20) NOT NULL,
  stats varchar(128) NOT NULL default '0',
  team tinyint(1) NOT NULL,
  wpns text NOT NULL,
  KEY id (id)
)");

echo "Table '$playergames_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $weapons_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  KEY id (id)
);");

echo "Table '$weapons_table' created successfully<br>\n";

install_query($db_link, "CREATE TABLE $weaponstats_table (
  id bigint(20) unsigned NOT NULL auto_increment,
  record bigint(20) NOT NULL default '0',
  weapon bigint(20) NOT NULL default '0',
  kills bigint(20) NOT NULL default '0',
  shots bigint(20) NOT NULL default '0',
  time bigint(20) NOT NULL default '0',
  KEY id (id)
);");

echo "Table '$weaponstats_table' created successfully<br>\n";

@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_players DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_stats DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_m_stats DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_maps DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_mapstats DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_m_mapstats DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_weapons DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_weaponstats DROP PRIMARY KEY;");
@mysqli_query($db_link, "ALTER TABLE ".$tablepre."_m_weaponstats DROP PRIMARY KEY;");

install_query($db_link, "ALTER TABLE ".$tablepre."_players ADD PRIMARY KEY (id);");
install_query($db_link, "ALTER TABLE ".$tablepre."_stats ADD PRIMARY KEY (player, server, game_type);");
//install_query($db_link, "ALTER TABLE ".$tablepre."_m_stats ADD PRIMARY KEY (player, server, game_type);");
install_query($db_link, "ALTER TABLE ".$tablepre."_maps ADD PRIMARY KEY (id);");
install_query($db_link, "ALTER TABLE ".$tablepre."_mapstats ADD PRIMARY KEY (record, map);");
install_query($db_link, "ALTER TABLE ".$tablepre."_m_mapstats ADD PRIMARY KEY (record, map);");
install_query($db_link, "ALTER TABLE ".$tablepre."_weapons ADD PRIMARY KEY (id);");
install_query($db_link, "ALTER TABLE ".$tablepre."_weaponstats ADD PRIMARY KEY (record, weapon);");
install_query($db_link, "ALTER TABLE ".$tablepre."_m_weaponstats ADD PRIMARY KEY (record, weapon);");

echo "Database optimized successfully<br />\n";

@mkdir("upload", 0777);
@mkdir("maps",   0777);

if(@chmod("upload", 0777) == false) {
  echo "Directory 'upload' CHMOD to 777 failed. You will need to do this manually with your FTP client<br>\n";
} else {
  echo "Directory 'upload' CHMOD to 777 was successful<br>\n";
}

if(@chmod("maps", 0777) == false) {
  echo "Directory 'maps' CHMOD to 777 failed. You will need to do this manually with your FTP client<br><br>\n";
} else {
  echo "Directory 'maps' CHMOD to 777 was successful<br><br>\n";
}

echo "Neos.Chronos 2.0.0 installed successfully! Don't forget to delete install.php<br>\n<a href=\"".$_SERVER['HTTP_REFERER']."\">Go to Neos.Chronos home page</a><br>\n";
?>
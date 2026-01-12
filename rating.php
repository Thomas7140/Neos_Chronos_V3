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
* Author : Tomas Stucinskas a.k.a Baboon
* E-mail : baboon@ai-hq.com
*/

/* ----------------------- READ --------------------------- *
* *
* For each kill, deaths, or any other event the player *
* is given so-called rating points. The rating points *
* are used to determine player's rank. *
* *
* Example 1: *
* Player kills someone with a headshot, using CAR-15. *
* kill: 1.0 points *
* headshot: 1.0 points *
* CAR-15 kill: 0.4 points *
* We add these all together and get 2.4 rating points. *
* *
* Example 2: *
* Player dies by blowing himself. *
* death: -0.2 points *
* suicide: -0.2 points *
* In total we get -0.4 rating points *
* *
* NOTE: rating is calculated and stored into database *
* during stats import, so it is not possible to change *
* player ratings for already imported games by only *
* changing this file. You must edit this file before you *
* start stats tracking! An option to change player ratings *
* will be added at a later date. *
* *
* -------------------------------------------------------- */

if(isset($_GET["version"])) {
	echo 'rating.php 1.0.0';
	die();
}

// Rating points for player stats
$points["kills"] = 1.0; // For each kill 
$points["deaths"] = -0.2; // For each death
$points["suicides"] = -0.2; // For each suicide
$points["murders"] = -2.0; // For each team kill 
$points["knifings"] = 2.0; // Bonus for each knife kill 
$points["headshots"] = 1.0; // Bonus for each headshot kill 
$points["medic_saves"] = 4.0; // For each medic save 
$points["revives"] = 1.0; // For each revive 
$points["pspattempts"] = 1.0; // For each PSP attempt 
$points["psptakeovers"] = 4.0; // For each PSP takeover 
$points["doublekills"] = 2.0; // For each double kill
$points["revives"] = 1.0; // For each revive 
$points["zone"] = 5.0; // For each minute in the zone 
$points["flags"] = 10.0; // For each flag captured 
$points["saves"] = 5.0; // For each flag saved 
$points["targets"] = 10.0; // For each target destroyed 
$points["runnerkills"] = 2.0; // For each flagrunner killed 
$points["zoneattack"] = 2.0; // For each zoner killed 
$points["zonedefend"] = 2.0; // For each kill from inside the zone 
$points["sdadattack"] = 2.0; // For each SD/AD attack kill 
$points["sdaddefend"] = 2.0; // For each SD/AD defend kill 
$points["wins"] = 20.0; // For each win in team based game 

// Bonus rating points for each weapon kill
$wpn_points["Knife"]                = 0.6;
$wpn_points["Colt .45"]             = 0.5;
$wpn_points["M9 Beretta"]           = 0.5;
$wpn_points["Remington Shotgun"]    = 0.5;
$wpn_points["CAR15"]                = 0.4;               
$wpn_points["CAR15/M203"]           = 0.4;           
$wpn_points["CAR15/M203 - Grenade"] = 0.2;
$wpn_points["M16"]                  = 0.4;                  
$wpn_points["M16/M203"]             = 0.4;            
$wpn_points["M16/M203 - Grenade"]   = 0.2;
$wpn_points["M21"]                  = 0.4;
$wpn_points["M24"]                  = 0.4;
$wpn_points["MCRT .300 Tactical"]   = 0.4;
$wpn_points["Barrett .50 Cal"]      = 0.4;
$wpn_points["M249 SAW"]             = 0.2;
$wpn_points["M60"]                  = 0.2;
$wpn_points["M240"]                 = 0.2;
$wpn_points["MP5"]                  = 0.5;
$wpn_points["Frag Grenade"]         = 0.5;
$wpn_points["AT4"]                  = 0.2;
$wpn_points["G3"]                   = 0.4;
$wpn_points["G36"]                  = 0.4;
$wpn_points["PSG1"]                 = 0.4;
$wpn_points["Claymore"]             = 0.5;
$wpn_points["Satchel Charge"]       = 0.5;
$wpn_points["50 Cal Humvee"]        = 0.3;
$wpn_points["MiniGun"]              = 0.3;
$wpn_points["50 Cal Emplacement"]   = 0.3;
$wpn_points["E M Canon"]            = 0.2;
$wpn_points["50 Cal Truck"]         = 0.3;
$wpn_points["Grenade Launcher"]     = 0.2;
$wpn_points["Spas 12"]              = 0.5;
$wpn_points["MP5 Silenced"]         = 0.5;
$wpn_points["P90"]                  = 0.4;
$wpn_points["AUG"]                  = 0.4;
$wpn_points["SOPMOD M4"]            = 0.4;
$wpn_points["SR-25"]                = 0.4;
$wpn_points["OICW"]                 = 0.4;
$wpn_points["M4-SD"]                = 0.4;
$wpn_points["551"]                  = 0.4;
$wpn_points["G37"]                  = 0.4;
$wpn_points["G36c"]                 = 0.4;
$wpn_points["FNMAG"]                = 0.2;
$wpn_points["AK-47/203"]            = 0.4;
$wpn_points["AK-47/203 - Grenade"]  = 0.2;
$wpn_points["AK-47"]                = 0.4;
$wpn_points["AK-74"]                = 0.4;
$wpn_points["M22"]                  = 0.4;
$wpn_points["HR-LAW"]               = 0.2;
$wpn_points["L96"]                  = 0.4;
$wpn_points["Glock 18-C"]           = 0.5;
$wpn_points["MP7"]                  = 0.5;
$wpn_points["G11"]                  = 0.4;
$wpn_points["M1 Rifle"]             = 0.4;
$wpn_points["M60 Silenced"]         = 0.2;
$wpn_points["PSG1 Silenced"]        = 0.4;
$wpn_points["SKS Rifle"]            = 0.4;
$wpn_points["M4 Auto"]              = 0.4;
$wpn_points["LG55"]                 = 0.4;
$wpn_points["BR55 Rifle"]           = 0.4;
$wpn_points["SIG SG552"]            = 0.4;
$wpn_points["BR55.6 Rifle"]         = 0.4;
$wpn_points["AR-10"]                = 0.4;
$wpn_points["SOPMOD"]               = 0.4;

?>
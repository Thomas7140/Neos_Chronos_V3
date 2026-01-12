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

if(isset($_GET["version"])) {
	echo 'gametypes.php 1.0.0';
	die();
}

$gametypes["0"] = "Deathmatch";
$gametypes["1"] = "Team Deathmatch";
$gametypes["3"] = "Team King of the Hill";
$gametypes["5"] = "Search and Destroy";
$gametypes["6"] = "Attack and Defend";
$gametypes["7"] = "Capture the Flag";
$gametypes["8"] = "Flagball";

?>
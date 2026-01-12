<?php

/*/////////////////////////////////////////////////////////////
//                                                           //
// Copyright (c) 2006 - 2007, Peter Jones (AKA Azrael)       //
// All rights reserved.                                      //
//                                                           //
// Author        : Peter Jones (AKA Azrael)                  //
// E-mail        : Email: p.jones188@btinternet.com          //
// Website       : http://www.gamers-central.com             //
// Support       : http://www.babstats.com                   //
//                                                           //
// File Name     : File Update Module                        //
// File Version  : 1.0.0                                     //
//                                                           //
/////////////////////////////////////////////////////////////*/

$errMsg = "";

$verReqArr = array('stats_import.php', 'header.php', 'config.php', 'weapons.php', 
					   'status_update.php', 'gametypes.php', 'common.php', 'status_report.php', 'rating.php'
					   , 'functions.php', 'monthlystats/header.php');

if($_POST["fileUpdate"]) {
	foreach($_POST["getFiles"] as $cfName) {
		$cfName = trim($cfName);
		
		if(strstr($cfName, ".php")) {
			$nfName = substr($cfName, 0, -3)."puf";
		} else {
			$nfName = substr($cfName, 0, -3)."huf";
		}
		
		$fldrArr = array(".", "modules", "modules/admin", "templates", "templates/admin", "monthlystats", 
		                 "monthlystats/modules", "monthlystats/templates");
		
		$i = 0;

		foreach($fldrArr as $fldr) {
			$folderCheck = substr(sprintf('%o', fileperms($fldr)), - 4);
			if($folderCheck != '0777') {
				folderPerm($ftpInfoArr, $fldr, '0777');
			}
		}
		
		sleep(10);
		
		foreach($fldrArr as $fldr) {
		    $folderCheck = substr(sprintf('%o', fileperms($fldr)), - 4);
			if($folderCheck == '0777') {
				$i++;
			}
		}

		if($i < 8) {
			die("Setting folder permissions failed, Update cannot conitue!<br /><br />
			     <u>Possible Causes:</u><br />--------------------------------------------------------------
				 --------------------------------<br />
				 1. You have the incorrect FTP Root folder set in the config.php.<br /><br />
				 2. Your account does not have sufficient privilages to alter folder permissions.<br /><br /><br />
				 <u>Possible Solutions:</u><br />-----------------------------------------------------------
				 --------------------------------<br />
				 1. Correct the FTP Root folder setting in the config.php.<br /><br />
				 2. Manually Chmod the following folders to '0777':<br />
				 &nbsp;-Babstats V5 root folder<br />
				 &nbsp;-Babstats V5/templates folder<br />
				 &nbsp;-Babstats V5/lang folder<br />
				 &nbsp;-Babstats V5/admin folder<br />
				 &nbsp;-Babstats V5/admin/templates folder<br />
				 &nbsp;-Babstats V5/admin/templates/admin folder<br />
				 *This can be done via an FTP program or by a File Manager program provided by your Website Host.<br />
				 **IMPORTANT: for security reasons, remember to Chmod the folders above back to '0755' after the update.<br />".$i);
		}
		
		if($nfName != "fileupdate.puf") {
			@unlink($cfName);
			$fileUrl = "http://babstats.com/downloads/ncv19x/updateFiles/standalone/".$nfName;
			$uSpl = array();
			if(($fh = fopen($fileUrl,'r')) === FALSE){
				//Failed to download update!
			} else {
				$uc = '';
				while (!feof($fh)) {
				  $uc .= fread($fh, 1024);
				}
				
				if(!in_array($cfName, $verReqArr)) {
					$uSpl = explode("\n", $uc);
					$updateInfo = $uSpl[sizeof($uSpl)-1];
					unset($uSpl[sizeof($uSpl)-1]);
					$uc = implode("\n", $uSpl);
				} 
				$nf = fopen($cfName, 'x+');
				fwrite($nf, $uc);
				if(in_array($cfName, $verReqArr)) {fwrite($nf, chr(10));}
				if(!in_array($cfName, $verReqArr)) {fwrite($nf, chr(10).$updateInfo);}
				fclose($nf);
			}
			@fclose($fh);
		}
		folderPerm($ftpInfoArr, ".", '0755');
		folderPerm($ftpInfoArr, "modules", '0755');
		folderPerm($ftpInfoArr, "modules/admin", '0755');
		folderPerm($ftpInfoArr, "templates", '0755');
		folderPerm($ftpInfoArr, "templates/admin", '0755');
		folderPerm($ftpInfoArr, "monthlystats", '0755');
		folderPerm($ftpInfoArr, "monthlystats/modules", '0755');
		folderPerm($ftpInfoArr, "monthlystats/templates", '0755');
		
		$i = 1;
		$k = 0;
		foreach($fldrArr as $fldr) {
			$folderCheck = substr(sprintf('%o', fileperms($fldr)), - 4);
			if($folderCheck != '0755') {
				folderPerm($ftpInfoArr, $fldr, '0755');
				$folderCheck = substr(sprintf('%o', fileperms($fldr)), - 4);
				if($folderCheck == '0755') {
					$i++;
				}
			} else {
				$i++;
			}
			$k++;
		}
		
		if($i < 8) {
			$errMsg = '<br /><table border="0" cellspacing="2" cellpadding="1" class="tableheader" 
			           style="background-color:#333333;  border:medium solid; border-bottom-color:#CCCCCC; 
					   border-right-color:#CCCCCC; border-left-color:#666666; border-top-color:#666666;">
			           <tr><td align="center"><u><b><font color="#FF0000">WARNING!</font></b></u></td></tr>
					   <tr><td>
			           <font color="#FF9900">
			           Chmod folders to \'0755\' failed, please Chmod the following folders to \'0755\' as soon as possible:<br /><br />
			           &nbsp;-Babstats V5 root folder<br />
					   &nbsp;-Babstats V5/templates folder<br />
					   &nbsp;-Babstats V5/lang folder<br />
					   &nbsp;-Babstats V5/admin folder<br />
					   &nbsp;-Babstats V5/admin/templates folder<br />
					   &nbsp;-Babstats V5/admin/templates/admin folder<br /><br />
					   *This can be done via an FTP program or by a File Manager program 
					   provided by your Website Host.<br />
					   **If you do not have access to either, contact your Website host to assist you in changing 
					   folder permissions on your account.</font><br />
					   </td></tr></table><br />';
		}
	}
}

if(file_exists($base_folder."templates/admin/fileupdate.htm")) {
    $tpl = new phemplate();
    $tpl->set_file("template", $base_folder."templates/admin/fileupdate.htm", 1);
	
	/******************************************* Build Latest Files Array *******************************************/
	$filename = "http://babstats.com/downloads/ncv19x/updateInfo.php?request=standalone";
	if(($fh = @fopen($filename,'r')) === FALSE){
		//die('Failed to open remote file');
	} else {
		$lSpl = array();
		/*********************** Latest File Versions Array ***********************/
		while (!feof($fh)) {
			$fc .= fread($fh, 1024);
		}
		fclose($fh);
		$lSpl = explode("<br />", $fc);
		foreach ($lSpl as $index => $value) {
			if (empty($value)) unset($lSpl[$index]);
		}
		/*********************** Latest File Versions Array ***********************/
	}
	/******************************************* Build Latest Files Array *******************************************/
	
	
	/******************************************* Build Current Files Array *******************************************/
	// Get relevant files in root folder
	$cFiles = array();
	$curFiles = dir(".");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != ".." && $current != "install.php" && $current != "phemplate_class.php") {
			if(strstr($current, ".php")) {
				$cFiles[] = $current;
			}
		}
	}
	
	// Get relevant files in modules folder
	$curFiles = dir("modules");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != ".." ) {
			if(strstr($current, ".php")) {
				$cFiles[] = "modules/".$current;
			}
		}
	}
	
	// Get relevant files in templates folder
	$curFiles = dir("templates");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != "..") {
			if(strstr($current, ".htm")) {
				$cFiles[] = "templates/".$current;
			}
		}
	}
	
	// Get relevant files in monthlystats root folder
	$curFiles = dir("monthlystats");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != "..") {
			if(strstr($current, ".php")) {
				$cFiles[] = "monthlystats/".$current;
			}
		}
	}
	
	// Get relevant files in monthlystats/modules folder
	$curFiles = dir("monthlystats/modules");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != "..") {
			if(strstr($current, ".php")) {
				$cFiles[] = "monthlystats/modules/".$current;
			}
		}
	}
	
	// Get relevant files in monthlystats/templates folder
	$curFiles = dir("monthlystats/templates");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != "..") {
			if(strstr($current, ".htm")) {
				$cFiles[] = "monthlystats/templates/".$current;
			}
		}
	}
	/******************************************* Build Current Files Array *******************************************/

	/******************************************* Build Current Admin Files Array *******************************************/
	// Get relevant files in modules/admin folder
	$curFiles = dir("modules/admin");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != ".." ) {
			if(strstr($current, ".php") && filesize("modules/admin/".$current) > '0') {
				$cFiles[] = "modules/admin/".$current;
			}
		}
	}
	
	// Get relevant files in templates/admin folder
	$curFiles = dir("templates/admin");
	while(($current = $curFiles->read()) !== false) {
		if($current != "." && $current != "..") {
			if(strstr($current, ".htm") && filesize("templates/admin/".$current) > '0') {
				$cFiles[] = "templates/admin/".$current;
			}
		}
	}
	/******************************************* Build Current Admin Files Array *******************************************/
	
	$count = 0;
	$us = 0;
	$ms = 0;
	$mis = 0;
	$filesToUpdate = array();
	
	//$missingFiles = array_diff($lSpl, $cFiles);
	//print_r($cFiles);

	foreach($lSpl as $fileNew) {
		$nfSpl = array();
		$nfSpl = explode(" ", $fileNew); // Array Indexes: 0 = File Path+Name, 1 = File Version, 2 = File Size
		$fileFound = false;
		
		if($count % 2 == 0){
			$rowClass = $rowclass1;
		} else {
			$rowClass = $rowclass2;
		}
		
		foreach($cFiles as $curFile) {
			if(strstr($curFile, ".php")) {
				$nfName = substr($curFile, 0, -3)."puf";
			} else {
				$nfName = substr($curFile, 0, -3)."huf";
			}
			if(trim($curFile) == trim($nfSpl[0])) {
				$dSpl = array();
				$status = "Unknown";
				$fSpl = array();
				$fh = @fopen($curFile,'r');
				$fc = fread($fh, filesize($curFile));
				fclose($fh);
				$fSpl = explode("\n", $fc);
				$fSize = 0;
				for($i = 0; $i < sizeof($fSpl); $i++) {
					$fSize += strlen(trim($fSpl[$i]));
				}
				$endKey = sizeof($fSpl)-1;
				$fSpl[$endKey] = ereg_replace('<div style="visibility:hidden; font-size:xx-small;">', "", $fSpl[$endKey]);
				$fSpl[$endKey] = ereg_replace('</div>', "", $fSpl[$endKey]);
				
				if(in_array($curFile, $verReqArr)) {
					$pSpl = array();
					$path = "";
					$vNum = "";
					$pSpl = explode("/", $PHP_SELF);
					for($i = 0; $i < sizeof($pSpl)-1; $i++) {
						$path .= $pSpl[$i]."/";
					}
					$vNum = file('http://'.$_SERVER['HTTP_HOST'].$path.$curFile.'?version=1');
					$fileDetails = trim($vNum[0]);
					$dSpl = explode(" ", $fileDetails);
				} else {
					$fileDetails = $fSpl[$endKey];
					$dSpl = explode(" ", $fileDetails);
				}
	
				if(strlen($dSpl[1]) > '0' && strlen($nfSpl[1]) > '0') {
					$curVer = str_replace(".", "", $dSpl[1]);
					$newVer = str_replace(".", "", $nfSpl[1]);
					$curVer = trim($curVer);
					$newVer = trim($newVer);
					if($newVer > $curVer) {
						$status = '<font color="#FF0000">Update available!</font>';
						$filesToUpdate[] = $nfSpl[0];
						$us = 1;
					} elseif($newVer == $curVer) {
						$status = '<font color="#00FF00">Latest version.</font>';
						if(trim($nfSpl[0]) != "config.php") {
							if($fSize != $nfSpl[2]) {
								$status = '<font color="#FFFF00">File has been modified!</font>';
								$ms = 1;
							}
						}
					} elseif($newVer < $curVer) {
						$status = '<font color="#FF0000">Invalid file version!</font>';
						$filesToUpdate[] = $nfSpl[0];
						$us = 1;
					}
					if(!strstr($curFile, "admin")) {
						$fileList .= '<tr class="'.$rowClass.'">
										 <td>'.$curFile.'</td>
										 <td>'.$dSpl[1].'</td>
										 <td>'.$status.'</td>
									  </tr>';
					} else {
						$fileList2 .= '<tr class="'.$rowClass.'">
										  <td>'.$curFile.'</td>
										  <td>'.$dSpl[1].'</td>
										  <td>'.$status.'</td>
									   </tr>';
					}
					$fileFound = true;
					break 1;
				}
			}
			
		}

		if($fileFound == false) {
			$mis = 1;
			$filesToUpdate[] = $nfSpl[0];
			if(!strstr($nfSpl[0], "admin/")) {
				$fileList .= '<tr class="'.$rowClass.'">
								 <td>'.$nfSpl[0].'</td>
								 <td>&nbsp;</td>
								 <td><font color="#FF6600">File Missing!</font></td>
							  </tr>';
			} else {
				$fileList2 .= '<tr class="'.$rowClass.'">
								  <td>'.$nfSpl[0].'</td>
								  <td>&nbsp;</td>
								  <td><font color="#FF9900">File Missing!</font></td>
							   </tr>';
			}
		}
		
		$count++;
	}
	
	$updateButton = "";
	
	if(($us + $ms + $mis) > 0) {
		$fileStatus = '<br /><table border="0" cellspacing="2" cellpadding="1" class="tableheader">';
		if($us > 0 || $mis > 0) {
			$updateButton = '<tr>
								 <td colspan="3" align="center">
									 <br />
									 <form name="form1" method="post" action="">
										 ';
										 
			foreach($filesToUpdate as $fuName) {
				$updateButton .= '<input type="hidden" name="getFiles[]" value="'.$fuName.'" />';
			}
										 
			$updateButton .=			 '<input name="fileUpdate" type="submit" id="fileUpdate" value="Update Files"  
										  onclick="openwins()">
									 </form>
								 </td>
							 </tr>';
		}
		
		if($us > 0) {
			$fileStatus .= '<tr class="tableheader">
								<td>WARNINIG: Some files are not upto date!<br /><br /></td>
							</tr>';
		}
		
		if($ms > 0) {
			$fileStatus .= '<tr class="tableheader">
								<td>CAUTION: Some files appear to have been modified!<br /><br /></td>
							</tr>';
		}
		
		if($mis > 0) {
			$fileStatus .= '<tr class="tableheader">
								<td>WARNINIG: Some files appear to be missing!</td>
							</tr>';
		}
		
		$fileStatus .= '</table><br /><br />';
	} else {
		$fileStatus = "";
	}
	
	$tpl->set_var("fileList", $fileList);
	$tpl->set_var("fileList2", $fileList2);
	$tpl->set_var("fileStatus", $fileStatus);
	$tpl->set_var("errMsg", $errMsg);
	$tpl->set_var("updateButton", $updateButton);
    $content = $tpl->process("", "template", 1);
} else {
    $content = "Error: template 'fileupdate' does not exist";
}
?>
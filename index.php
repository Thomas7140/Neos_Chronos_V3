<?php


error_reporting(E_ERROR | E_WARNING | E_PARSE);



$mtime1    = explode(" ", microtime());

$starttime = $mtime1[1] + $mtime1[0];



$base_folder  = "";

$mainscript   = $_SERVER['PHP_SELF']."?";

$monthlyscript   = "monthlystats/index.php?";

$content      = "";

$rowclass1    = "tablerow1";

$rowclass2    = "tablerow2";

$selectedasc  = "selectedasc";

$selecteddesc = "selecteddesc";

$unselected   = "unselected";



require "config.php";

require "common.php";

require "weapons.php";

require "gametypes.php";

require "rating.php";

require "functions.php";



// SECURITY FIX: Removed dangerous variable pollution pattern
// Use explicit parameter retrieval instead
$action = getParam('action', 'start');
$id = getParam('id', 0, 'int');
$server = getParam('server', -1, 'int');
$gametype = getParam('gametype', 'All');
$frame = getParam('frame', '');
$page = getParam('page', 1, 'int');
$orderby = getParam('orderby', '');
$ascdesc = getParam('ascdesc', '');


DBConnect();

MakeTableNames();



require "phemplate_class.php";



if(!(@DBQuery("SELECT * FROM $awards_table LIMIT 1"))) {

  $file = "error";

  $install_path = $base_folder . "install.php";
  if (file_exists($install_path)) {
    $install_url = $install_path;
    $content = <<<HTML
<div id="install-modal" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.75); display:flex; align-items:center; justify-content:center; z-index:9999;">
  <div style="background:#1f1f1f; border:1px solid #666666; padding:20px; width:420px; color:#dddddd; font-family:Tahoma, Verdana, Arial; text-align:left;">
    <div style="font-size:14px; font-weight:bold; margin-bottom:10px;">Database setup required</div>
    <div style="font-size:12px; margin-bottom:16px;">The stats database is missing. Run the installer to create the tables.</div>
    <div style="display:flex; gap:10px;">
      <a href="$install_url" style="background:#996633; color:#ffffff; padding:6px 10px; text-decoration:none; border:1px solid #775533;">Install Database</a>
      <a href="#" id="install-modal-close" style="background:#303030; color:#ffffff; padding:6px 10px; text-decoration:none; border:1px solid #555555;">Dismiss</a>
    </div>
  </div>
</div>
<script type="text/javascript">
(function() {
  var closeBtn = document.getElementById("install-modal-close");
  var modal = document.getElementById("install-modal");
  if (closeBtn && modal) {
    closeBtn.onclick = function(e) {
      e.preventDefault();
      modal.style.display = "none";
    };
  }
})();
</script>
HTML;
  } else {
    $content = "Database is not installed and install.php is not available.";
  }

} else {

  $files = array('install.php', 'update.php');
  $errs = array();

  foreach($files as $value) {
    if (file_exists($base_folder.$value)) {
      $file = "error";
      $errs[] = $value;
    } 
  }

  if (!empty($errs)) {
    $fileList = '';
    $i = 0;
    foreach($errs as $value) {
      $i++;
      $fileList .= "<div style=\"padding:4px 0; color:#ffaaaa;\">$i: $base_folder$value</div>";
    }

    $content = <<<HTML
<div id="security-modal" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); display:flex; align-items:center; justify-content:center; z-index:9999;">
  <div style="background:#1f1f1f; border:2px solid #cc3333; padding:24px; width:480px; color:#dddddd; font-family:Tahoma, Verdana, Arial; text-align:left; box-shadow:0 4px 20px rgba(0,0,0,0.5);">
    <div style="font-size:16px; font-weight:bold; margin-bottom:12px; color:#ff5555;">⚠️ Security Warning</div>
    <div style="font-size:13px; margin-bottom:16px; line-height:1.5;">
      Please delete the following files before proceeding. These files pose a security risk if left accessible:
    </div>
    <div style="background:#2a2a2a; border:1px solid #444444; padding:12px; margin-bottom:16px; font-family:monospace; font-size:12px;">
      $fileList
    </div>
    <div style="font-size:11px; color:#aaaaaa; margin-bottom:16px;">
      These files should be removed from your web server after installation is complete.
    </div>
    <div style="display:flex; gap:10px;">
      <a href="#" id="security-modal-close" style="background:#996633; color:#ffffff; padding:8px 16px; text-decoration:none; border:1px solid #775533; font-size:12px;">I Understand</a>
    </div>
  </div>
</div>
<script type="text/javascript">
(function() {
  var closeBtn = document.getElementById("security-modal-close");
  var modal = document.getElementById("security-modal");
  if (closeBtn && modal) {
    closeBtn.onclick = function(e) {
      e.preventDefault();
      modal.style.display = "none";
    };
  }
})();
</script>
HTML;
  }

}



if($content == "") { 

  if(!isset($action) || $action == "") $action = "start";

  if(file_exists("modules/$action.php")) {

    include "modules/$action.php";

  } else {

    $content = "<P class=\"error\">Error: Module '$action' does not exist</P>";    

  }

}


$tpl = new phemplate();

$tpl->set_file("main", "templates/main.htm");

$tpl->set_var("website_title", $website_title);

$tpl->set_var("charset",       $charset);

$tpl->set_var("mainscript",    $mainscript);

$tpl->set_var("monthlyscript", $monthlyscript);

$tpl->set_var("version",       $version);

$tpl->set_var("update",        isset($update) ? $update : "");

$tpl->set_var("base",          $base_folder);

$tpl->set_var("content", $content);

echo $tpl->process("", "main", 1);



$mtime2 = explode(" ", microtime());

$endtime = $mtime2[1] + $mtime2[0];

$totaltime = ($endtime - $starttime);

$totaltime = number_format($totaltime, 3);



echo "<center><font size=\"1\" face=\"Tahoma, Verdana\" color=\"#999999\"><br><br>Page generated in $totaltime seconds</font></center>";

?>



<div style="visibility:hidden; font-size:xx-small;">index.php 1.0.0</div>

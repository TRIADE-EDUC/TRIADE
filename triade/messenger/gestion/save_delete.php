<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
//
require ("../common/display_errors.inc.php"); 
//
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['file'])) $file = $_POST['file']; else $file = "";
if (isset($_POST['sxd']))  $sxd =  $_POST['sxd'];  else $sxd = "";
//
//
$url = "saving.php?lang=" . $lang . "&sxd=" . $sxd . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  $repert = "save/";
  $file = str_replace("/", "", $file);
  $file = str_replace("\\", "", $file);
  $file = str_replace("..", "", $file);
  $file = str_replace("*", "", $file);
  $file = str_replace("%", "", $file);
  $file = str_replace("'", "", $file);
  //
  if ( (is_writeable($repert . $file)) and ($file != "") )
  {
    unlink($repert . $file);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['file'])) $file = $_GET['file']; else $file = "";
if (isset($_GET['folder'])) $folder_log = base64_decode($_GET['folder']); else $folder_log = "";
//
if ($file == "") 
{
  header("location:log.php?lang=" . $lang . "&");  
  break;
}
//
$url = "log.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  $fic = $file . ".txt";
  if (is_writable($folder_log . $fic))
  {
    $handle = fopen($folder_log . $fic, "w");
    //if ($handle) 
    fclose($handle);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
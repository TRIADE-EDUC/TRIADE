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
if (isset($_GET['avatar'])) $avatar = base64_decode($_GET['avatar']);  else $avatar = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
$url = "avatar_changing.php?lang=" . $lang . "&#pending";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ($avatar != "")
  {
    define('INTRAMESSENGER',true);
    require ("../common/config/config.inc.php");
    //
    $repert = "../" . _PUBLIC_FOLDER . "/upload/";
    if (is_dir($repert)) 
    {
      $filename = $repert . $avatar;
      if ( (!is_dir($filename)) and (is_writable($filename)) )
        unlink($filename);
    }
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
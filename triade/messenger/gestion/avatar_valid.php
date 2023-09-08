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
if (isset($_GET['avatar'])) $avatar = $_GET['avatar'];  else $avatar = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
$url = "avatar_changing.php?lang=" . $lang . "&#pending";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  define('INTRAMESSENGER',true);
  require ("../common/config/config.inc.php");
  $repert_src = "../" . _PUBLIC_FOLDER . "/upload/";
  $repert_dst = "../distant/avatar/";
  //
  if ( (is_readable($repert_src . $avatar)) and ($avatar != "") )
  {
    if ( copy($repert_src . $avatar, $repert_dst . $avatar) )
    {
      unlink($repert_src . $avatar);
      //
      require ("../common/log.inc.php");
      write_log("log_user_avatar_valid", $avatar);
    }
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
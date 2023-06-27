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
if (isset($_GET['ban'])) $ban = trim($_GET['ban']);  else $ban = "";
if (isset($_GET['ban_type'])) $ban_type = trim($_GET['ban_type']);  else $ban_type = "";
if (isset($_GET['ban_value'])) $ban_value = trim($_GET['ban_value']);  else $ban_value = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//if ( ($ban_type == "P") and (strlen($ban_value) <> 15) ) $ban_value = ""; // checkpc
//
$url = "list_ban.php?ban=" . $ban . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( (strlen($ban_type) == 1) and (strlen($ban_value) > 2) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " delete from " . $PREFIX_IM_TABLE . "BAN_BANNED ";
    $requete .= " WHERE BAN_TYPE = '" . $ban_type . "' and BAN_VALUE = '" . $ban_value . "' ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-U2a]", $requete);
    //
    mysqli_close($id_connect);
    //
    if ($ban_type == "U") write_log("log_unban_username", $ban_value);
    if ($ban_type == "P") write_log("log_unban_computer", $ban_value);
    if ($ban_type == "I") write_log("log_unban_ip_address", $ban_value);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
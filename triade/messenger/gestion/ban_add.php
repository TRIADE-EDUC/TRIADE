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
if (isset($_REQUEST['ban'])) $ban = trim($_REQUEST['ban']);  else $ban = "";
if (isset($_REQUEST['ban_type'])) $ban_type = trim($_REQUEST['ban_type']);  else $ban_type = "";
if (isset($_REQUEST['ban_value'])) $ban_value = trim($_REQUEST['ban_value']);  else $ban_value = "";
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang']; else $lang = "";
if (isset($_REQUEST['id_user'])) $id_user = intval($_REQUEST['id_user']); else $id_user = "";
//
if ( ($ban_type == "P") and (strlen($ban_value) <> 15) ) $ban_value = ""; // checkpc
if ( ($ban_type != "P") and ($ban_type != "U") and ($ban_type != "I") )  $ban_type = "";
//
//
if ($id_user > 0)
  $url = "user.php?id_user=" . $id_user . "&lang=" . $lang . "&";
else
  $url = "list_ban.php?ban=" . $ban . "&lang=" . $lang . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( (strlen($ban_type) == 1) and (strlen($ban_value) > 2) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " select BAN_TYPE, BAN_VALUE from " . $PREFIX_IM_TABLE . "BAN_BANNED ";
    $requete .= " WHERE BAN_TYPE = '" . $ban_type . "' and BAN_VALUE = '" . $ban_value . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-U1a]", $requete);
    //
    if ( mysqli_num_rows($result) == 0 )
    {
      $requete  = " insert into " . $PREFIX_IM_TABLE . "BAN_BANNED (BAN_TYPE, BAN_VALUE) ";
      $requete .= " values ('" . $ban_type . "', '" . $ban_value . "' ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-U1b]", $requete);
      //
      if ($ban_type == "U") write_log("log_ban_username", $ban_value . ";" . $id_user);
      if ($ban_type == "P") write_log("log_ban_computer", $ban_value . ";" . $id_user);
      if ($ban_type == "I") write_log("log_ban_ip_address", $ban_value . ";" . $id_user);
    }
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
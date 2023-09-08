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
if (isset($_POST['id_user'])) $id_user = intval($_POST['id_user']);  else $id_user = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['get_alert'])) $get_alert = $_POST['get_alert']; else $get_alert = "";
//
//
if ($id_user > 0)
  $url = "user.php?id_user=" . $id_user . "&lang=" . $lang . "&";
else
  $url = "list_users.php?lang=" . $lang . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    require ("../common/functions.inc.php");
    $username = f_get_username_of_id($id_user);
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    if (intval($get_alert) == 1)
      $requete .= " set USR_GET_ADMIN_ALERT = 1 ";
    else
      $requete .= " set USR_GET_ADMIN_ALERT = 0 ";
    //
    $requete .= " WHERE ID_USER = " . $id_user;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-B6-1]", $requete);
    //
    mysqli_close($id_connect);
    //
    if (intval($get_alert) == 1)
      write_log("log_user_admin_alert_get", $username . ";" . $id_user);
    else
      write_log("log_user_admin_alert_not_get", $username . ";" . $id_user);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
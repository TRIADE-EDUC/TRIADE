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
if (isset($_POST['role_name'])) $role_name = $_POST['role_name'];  else $role_name = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$role_name = trim($role_name);
$role_name = str_replace("'", "`", $role_name);
$role_name = str_replace('"', '', $role_name);
$role_name = str_replace("/", "", $role_name);
$role_name = str_replace("--", "-", $role_name);
//
//
$url = "list_roles.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if (strlen($role_name) > 2)
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $id_role = -1;
    $requete  = " select ID_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
    $requete .= " WHERE ROL_NAME like '" . $role_name . "' ";
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G6a1]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($id_role) = mysqli_fetch_row ($result);
    }
    //
    if ($id_role == -1)
    {
      $requete = " insert into " . $PREFIX_IM_TABLE . "ROL_ROLE ";
      $requete .= " (ROL_NAME) ";
      $requete .= " values ('" . $role_name . "' ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G6a2]", $requete);
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
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
if (isset($_POST['id_role'])) $id_role = intval($_POST['id_role']);  else $id_role = 0;
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$role_name = trim($role_name);
$role_name = str_replace("'", "`", $role_name);
$role_name = str_replace('"', '', $role_name);
$role_name = str_replace("/", "", $role_name);
$role_name = str_replace("--", "-", $role_name);
//
//
$url = "list_roles.php?tri=" . $tri . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( (strlen($role_name) > 2) and ($id_role > 0) and (!preg_match("#[^0-9]#", $id_role)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "ROL_ROLE ";
    $requete .= " set ROL_NAME = '" . $role_name . "' ";
    $requete .= " where ID_ROLE = " . $id_role;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G6c]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
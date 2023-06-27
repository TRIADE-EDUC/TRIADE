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
if (isset($_POST['group_name'])) $group_name = $_POST['group_name'];  else $group_name = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$group_name = trim($group_name);
$group_name = str_replace("'", "`", $group_name);
$group_name = str_replace('"', '', $group_name);
$group_name = str_replace("/", "", $group_name);
$group_name = str_replace("--", "-", $group_name);
//
//
$url = "group_adding_user.php?tri=" . $tri . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if (strlen($group_name) > 2)
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " insert into " . $PREFIX_IM_TABLE . "GRP_GROUP ";
    $requete .= " (GRP_NAME) ";
    $requete .= " values ('" . $group_name . "' ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-D1a]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
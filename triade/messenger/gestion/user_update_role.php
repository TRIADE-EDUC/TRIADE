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
if (isset($_POST['id_role'])) $id_role = intval($_POST['id_role']);  else $id_role = 0;
if (isset($_POST['id_role_from'])) $id_role_from = intval($_POST['id_role_from']);  else $id_role_from = 0;
if (isset($_POST['from'])) $from = $_POST['from'];  else $from = "";
if (isset($_POST['page'])) $page = $_POST['page'];  else $page = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "user.php?id_user=" . $id_user . "&lang=" . $lang . "&";
if ($id_role_from > 0) $url = "list_role_members.php?id_role=" . $id_role_from . "&lang=" . $lang . "&";
if ($from == "list_users")  $url = "list_users.php?lang=" . $lang . "&page=" . $page . "&" . "&tri=" . $tri . "&" . "&lang=" . $lang . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) )
  {
    define('INTRAMESSENGER',true);
    //
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    if ($id_role > 0)
      $requete .= " set ID_ROLE = " . $id_role . " ";
    else
      $requete .= " set ID_ROLE = null ";
    //
    $requete .= " WHERE ID_USER = " . $id_user;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G6z]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
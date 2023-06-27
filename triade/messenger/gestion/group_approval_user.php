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
if (isset($_GET['id_user'])) $id_user = intval($_GET['id_user']);  else $id_user = 0;
if (isset($_GET['id_gp'])) $id_gp = intval($_GET['id_gp']);  else $id_gp = 0;
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['action'])) $action = $_GET['action']; else $action = "";
//
//
$url = "list_group_members.php?id_group=" . $id_gp . "&lang=" . $lang . "&tri=" . $tri . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) and ($id_gp > 0) and (!preg_match("#[^0-9]#", $id_gp)) and ($action != "") )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    if ($action == "ok")
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "USG_USERGRP ";
      $requete .= " set USG_PENDING = 0 ";
      $requete .= " where ID_GROUP = " . $id_gp;
      $requete .= " and ID_USER = " . $id_user;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-D7a]", $requete);
    }
    //
    if ($action == "cannot-join")
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "USG_USERGRP ";
      $requete .= " set USG_PENDING = 2 ";
      $requete .= " where ID_GROUP = " . $id_gp;
      $requete .= " and ID_USER = " . $id_user;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-D7b]", $requete);
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
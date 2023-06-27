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
if (isset($_POST['id_gp'])) $id_gp = intval($_POST['id_gp']);  else $id_gp = 0;
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_group.php?tri=" . $tri . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) and ($id_gp > 0) and (!preg_match("#[^0-9]#", $id_gp)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " select ID_GROUP, ID_USER from " . $PREFIX_IM_TABLE . "USG_USERGRP ";
    $requete .= " WHERE ID_GROUP = " . $id_gp . " ";
    $requete .= " and ID_USER = " . $id_user;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-D3b]", $requete);
    //
    if ( mysqli_num_rows($result) == 0 )
    {
      $requete  = " insert into " . $PREFIX_IM_TABLE . "USG_USERGRP (ID_GROUP, ID_USER) ";
      $requete .= " values (" . $id_gp . ", " . $id_user . " ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-D3a]", $requete);
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
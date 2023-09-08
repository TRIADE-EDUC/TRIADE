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
if (isset($_GET['id_role'])) $id_role = intval($_GET['id_role']);  else $id_role = 0;
if (isset($_GET['id_module'])) $id_module = intval($_GET['id_module']);  else $id_module = 0;
if (isset($_GET['state'])) $state = intval($_GET['state']);  else $state = 0;
if (isset($_GET['rlm_value'])) $rlm_value = intval($_GET['rlm_value']);  else $rlm_value = 0;
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
$url = "role_permissions.php?id_role=" . $id_role . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_role > 0) and ($id_module > 0) and (!preg_match("#[^0-9]#", $id_role)) and (!preg_match("#[^0-9]#", $id_module)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    if ($state > 0) 
    {
      $requete  = " insert into " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE ";
      $requete .= " (ID_ROLE, ID_MODULE, RLM_STATE, RLM_VALUE) ";
      if ($state == 3) // numeric
        $requete .= " VALUES (" . $id_role . ", " . $id_module . ", " . $state . ", " . $rlm_value . ") ";
      else
        $requete .= " VALUES (" . $id_role . ", " . $id_module . ", " . $state . ", 0) ";
      //
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G6g]", $requete);
    }
    else
    {
      $requete  = " delete from " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE ";
      $requete .= " WHERE ID_ROLE = " . $id_role;
      $requete .= " and ID_MODULE = " . $id_module;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G6h]", $requete);
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
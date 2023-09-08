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
if (isset($_GET['id_group'])) $id_gp = intval($_GET['id_group']);  else $id_gp = 0;
if (isset($_GET['action'])) $action = $_GET['action'];  else $action = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
$url = "list_group.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($action != "") and ($id_gp > 0) and (!preg_match("#[^0-9]#", $id_gp)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/config/config.inc.php");
    //
    $qry = "";
    if ($action == "enable") 
    {
      $qry = " SET GRP_SHOUTBOX = 1 ";
      // suivant valeur par défaut :
      if (_SHOUTBOX_NEED_APPROVAL != "") 
        $qry .= " , GRP_SBX_NEED_APPROVAL = 1 ";
      else
        $qry .= " , GRP_SBX_NEED_APPROVAL = 0 ";
    }
    if ($action == "disable") $qry = " SET GRP_SHOUTBOX = 0 ";
    if ($action == "noapproval") $qry = " SET GRP_SHOUTBOX = 1, GRP_SBX_NEED_APPROVAL = 0 ";
    if ($action == "needapproval") $qry = " SET GRP_SHOUTBOX = 1, GRP_SBX_NEED_APPROVAL = 1 ";
    //
    if ($action == "public") $qry = " SET GRP_PRIVATE = 0 ";
    if ($action == "official") $qry = " SET GRP_PRIVATE = 1 ";
    if ($action == "private") $qry = " SET GRP_PRIVATE = 2 ";
    if ($qry != "")
    {
      require ("../common/sql.inc.php");
      //
      $requete  = " update " . $PREFIX_IM_TABLE . "GRP_GROUP ";
      $requete .= $qry;
      $requete .= " WHERE ID_GROUP = " . $id_gp;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-D6a]", $requete);
      //
      mysqli_close($id_connect);
    }
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
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
if (isset($_POST['srv_comment'])) $srv_comment = $_POST['srv_comment'];  else $srv_comment = "";
if (isset($_POST['id_srv'])) $id_srv = intval($_POST['id_srv']);  else $id_srv = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$srv_comment = trim($srv_comment);
$srv_comment = str_replace("'", "`", $srv_comment);
$srv_comment = str_replace('"', '', $srv_comment);
$srv_comment = str_replace("/", "", $srv_comment);
$srv_comment = str_replace("--", "-", $srv_comment);
//
$url = "list_servers_status.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_srv > 0) and (!preg_match("#[^0-9]#", $id_srv)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
    $requete .= " set SRV_STATE_COMMENT = '" . $srv_comment . "' ";
    $requete .= " where ID_SERVER = " . $id_srv;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-W7a]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
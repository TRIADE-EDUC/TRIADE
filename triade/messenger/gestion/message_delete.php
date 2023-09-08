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
if (isset($_GET['id_msg'])) $id_msg = intval($_GET['id_msg']);  else  $id_msg = "";
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
$url = "messagerie.php?tri=" . $tri . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_msg > 0) or ($id_msg == "KILL-THEM-ALL") )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
    $requete .= " WHERE ID_USER_AUT = -99 ";
    if ($id_msg > 0) $requete .= " and ID_MESSAGE = " . $id_msg;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C3]", $requete);
    //
    mysqli_close($id_connect);
    //
    $url .= "delete_ok=ok&";
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
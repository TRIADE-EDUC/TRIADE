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
if (isset($_POST['lang'])) $lang = $_POST['lang'];  else $lang = "";
//
//
$url = "list_files_sharing_alert.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  define('INTRAMESSENGER',true);
  //
  require ("../common/sql.inc.php");
  //
  $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " SET FIL_ONLINE = 'Y' ";
  $requete .= " WHERE FIL_ONLINE = 'Z' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N1w]", $requete);
  //
  mysqli_close($id_connect);
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
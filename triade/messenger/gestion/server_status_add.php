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
if (isset($_POST['srv_name'])) $srv_name = $_POST['srv_name'];  else $srv_name = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$srv_name = trim($srv_name);
$srv_name = str_replace("'", "`", $srv_name);
$srv_name = str_replace('"', '', $srv_name);
$srv_name = str_replace("/", "", $srv_name);
$srv_name = str_replace("--", "-", $srv_name);
//
$url = "list_servers_status.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if (strlen($srv_name) > 2)
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $cannot = "";
    $requete  = " select LOWER(SRV_NAME) from " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
    $requete .= " WHERE LOWER(SRV_NAME) like '" . strtolower($srv_name) . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-W3a]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($t_name) = mysqli_fetch_row ($result) )
      {
        if ($t_name == strtolower($srv_name)) $cannot = "X";
      }
    }
    if ($cannot == "")
    {
      $requete  = " insert into " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
      $requete .= " (SRV_NAME, SRV_STATE) ";
      $requete .= " values ('" . $srv_name . "', 2 ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-W3b]", $requete);
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
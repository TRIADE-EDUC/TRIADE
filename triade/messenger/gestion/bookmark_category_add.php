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
if (isset($_POST['bmc_title'])) $bmc_title = $_POST['bmc_title'];  else $bmc_title = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$bmc_title = trim($bmc_title);
$bmc_title = str_replace("'", "`", $bmc_title);
$bmc_title = str_replace('"', '', $bmc_title);
$bmc_title = str_replace("/", "", $bmc_title);
$bmc_title = str_replace("--", "-", $bmc_title);
//
//
$url = "list_bookmarks_categories.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ($bmc_title <> "")
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " insert into " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG ";
    $requete .= " (BMC_TITLE) ";
    $requete .= " values ('" . $bmc_title . "' ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F3n]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
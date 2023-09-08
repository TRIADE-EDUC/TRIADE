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
if (isset($_GET['id_admin'])) $id_admin = intval($_GET['id_admin']);  else $id_admin = 0;
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['adm_level_1'])) $adm_level_1 = $_GET['adm_level_1']; else $adm_level_1 = "0";
if (isset($_GET['adm_level_2'])) $adm_level_2 = $_GET['adm_level_2']; else $adm_level_2 = "0";
if (isset($_GET['adm_level_4'])) $adm_level_4 = $_GET['adm_level_4']; else $adm_level_4 = "0";
if (isset($_GET['adm_level_8'])) $adm_level_8 = $_GET['adm_level_8']; else $adm_level_8 = "0";
if (isset($_GET['adm_level_16'])) $adm_level_16 = $_GET['adm_level_16']; else $adm_level_16 = "0";
if (isset($_GET['adm_level_32'])) $adm_level_32 = $_GET['adm_level_32']; else $adm_level_32 = "0";
if (isset($_GET['adm_level_64'])) $adm_level_64 = $_GET['adm_level_64']; else $adm_level_64 = "0";
if (isset($_GET['adm_level_128'])) $adm_level_128 = $_GET['adm_level_128']; else $adm_level_128 = "0";
if (isset($_GET['adm_level_256'])) $adm_level_256 = $_GET['adm_level_256']; else $adm_level_256 = "0";
if (isset($_GET['adm_level_512'])) $adm_level_512 = $_GET['adm_level_512']; else $adm_level_512 = "0";
if (isset($_GET['adm_level_1024'])) $adm_level_1024 = $_GET['adm_level_1024']; else $adm_level_1024 = "0";
if (isset($_GET['adm_level_2048'])) $adm_level_2048 = $_GET['adm_level_2048']; else $adm_level_2048 = "0";
if (isset($_GET['adm_level_4096'])) $adm_level_4096 = $_GET['adm_level_4096']; else $adm_level_4096 = "0";
if (isset($_GET['adm_level_8192'])) $adm_level_8192 = $_GET['adm_level_8192']; else $adm_level_8192 = "0";
if (isset($_GET['adm_level_16384'])) $adm_level_16384 = $_GET['adm_level_16384']; else $adm_level_16384 = "0";
if (isset($_GET['adm_level_32768'])) $adm_level_32768 = $_GET['adm_level_32768']; else $adm_level_32768 = "0";
if (isset($_GET['adm_level_65536'])) $adm_level_65536 = $_GET['adm_level_65536']; else $adm_level_65536 = "0";
if (isset($_GET['adm_level_131072'])) $adm_level_131072 = $_GET['adm_level_131072']; else $adm_level_131072 = "0";
if (isset($_GET['adm_level_262144'])) $adm_level_262144 = $_GET['adm_level_262144']; else $adm_level_262144 = "0";
if (isset($_GET['adm_level_524288'])) $adm_level_524288 = $_GET['adm_level_524288']; else $adm_level_524288 = "0";
if (isset($_GET['adm_level_1048576'])) $adm_level_1048576 = $_GET['adm_level_1048576']; else $adm_level_1048576 = "0";
//if (isset($_GET['adm_level_'])) $adm_level_ = $_GET['adm_level_']; else $adm_level_ = "0";
//
//$url = "admin_acp_rights.php?lang=" . $lang . "&id_admin=" . $id_admin . "&";
$url = "list_admin_acp.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  $new_level = 0;
  if (intval($adm_level_1) > 0) $new_level = ($new_level + 1);
  if (intval($adm_level_2) > 0) $new_level = ($new_level + 2);
  if (intval($adm_level_4) > 0) $new_level = ($new_level + 4);
  if (intval($adm_level_8) > 0) $new_level = ($new_level + 8);
  if (intval($adm_level_16) > 0) $new_level = ($new_level + 16);
  if (intval($adm_level_32) > 0) $new_level = ($new_level + 32);
  if (intval($adm_level_64) > 0) $new_level = ($new_level + 64);
  if (intval($adm_level_128) > 0) $new_level = ($new_level + 128);
  if (intval($adm_level_256) > 0) $new_level = ($new_level + 256);
  if (intval($adm_level_512) > 0) $new_level = ($new_level + 512);
  if (intval($adm_level_1024) > 0) $new_level = ($new_level + 1024);
  if (intval($adm_level_2048) > 0) $new_level = ($new_level + 2048);
  if (intval($adm_level_4096) > 0) $new_level = ($new_level + 4096);
  if (intval($adm_level_8192) > 0) $new_level = ($new_level + 8192);
  if (intval($adm_level_16384) > 0) $new_level = ($new_level + 16384);
  if (intval($adm_level_32768) > 0) $new_level = ($new_level + 32768);
  if (intval($adm_level_65536) > 0) $new_level = ($new_level + 65536);
  if (intval($adm_level_131072) > 0) $new_level = ($new_level + 131072);
  if (intval($adm_level_262144) > 0) $new_level = ($new_level + 262144);
  if (intval($adm_level_524288) > 0) $new_level = ($new_level + 524288);
  if (intval($adm_level_1048576) > 0) $new_level = ($new_level + 1048576);
  //
  if ( ($id_admin > 0) and (!preg_match("#[^0-9]#", $id_admin)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
    $requete .= " SET ADM_LEVEL = " . $new_level;
    $requete .= " where ID_ADMIN = " . $id_admin;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-P1f]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
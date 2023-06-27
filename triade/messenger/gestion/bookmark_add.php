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
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['bmk_url'])) $bmk_url = $_POST['bmk_url'];  else $bmk_url = "";
if (isset($_POST['bmk_title'])) $bmk_title = $_POST['bmk_title'];  else $bmk_title = "";
//
$bmk_url = trim($bmk_url);
$bmk_url = str_replace("'", "", $bmk_url);
$bmk_url = str_replace('"', '', $bmk_url);
//$bmk_url = str_replace("\", "", $bmk_url);
$bmk_url = str_replace("--", "-", $bmk_url);
$bmk_title = trim($bmk_title);
$bmk_title = str_replace("'", "`", $bmk_title);
$bmk_title = str_replace('"', '', $bmk_title);
$bmk_title = str_replace("/", "", $bmk_title);
$bmk_title = str_replace("--", "-", $bmk_title);
//
$url = "list_bookmarks.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( (strlen($bmk_url) > 9) and (strlen($bmk_title) > 3) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/functions.inc.php");
    require ("../common/sql.inc.php");
    //
    $cannot = "";
    //
    $requete  = " select LOWER(BMK_URL) from " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE LOWER(BMK_URL) like '" . strtolower($bmk_url) . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F2a]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($t_name) = mysqli_fetch_row ($result) )
      {
        if ($t_name == strtolower($bmk_url)) $cannot = "X";
      }
    }
    //
    $requete  = " select LOWER(BMK_TITLE) from " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE LOWER(BMK_TITLE) like '" . strtolower($bmk_title) . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F2b]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($t_name) = mysqli_fetch_row ($result) )
      {
        if ($t_name == strtolower($bmk_title)) $cannot = "X";
      }
    }
    //
    if ($cannot == "")
    {
      $requete  = " insert into " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
      $requete .= " (ID_BOOKMCATEG, ID_USER_AUT, BMK_URL, BMK_TITLE, BMK_DATE, BMK_DISPLAY) ";
      $requete .= " values (null, -99, '" . f_encode64($bmk_url) . "', '" . f_encode64($bmk_title) . "', CURDATE(), 1 ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-F2c]", $requete);
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
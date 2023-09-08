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
if (isset($_POST['id_book'])) $id_book = intval($_POST['id_book']);  else $id_book = 0;
if (isset($_POST['bmk_url'])) $bmk_url = $_POST['bmk_url'];  else $bmk_url = "";
if (isset($_POST['bmk_title'])) $bmk_title = $_POST['bmk_title'];  else $bmk_title = "";
if (isset($_POST['categ'])) $categ = $_POST['categ'];  else $categ = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
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
if ( ($categ <> "null") and ($categ <> "null") ) $categ = intval($categ);
//
//
$url = "list_bookmarks.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ( (strlen($bmk_url) > 9) or (strlen($bmk_title) > 3) or ($categ <> "") ) and ($id_book > 0) and (!preg_match("#[^0-9]#", $id_book)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/functions.inc.php");
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    if ($bmk_url != "") $requete .= " set BMK_URL = '" . f_encode64($bmk_url) . "' ";
    if ($bmk_title != "") $requete .= " set BMK_TITLE = '" . f_encode64($bmk_title) . "' ";
    if ($categ != "") $requete .= " set ID_BOOKMCATEG = " . $categ . " ";
    //
    $requete .= " where ID_BOOKMARK = " . $id_book;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F3d]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
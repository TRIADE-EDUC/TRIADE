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
if (isset($_POST['id_book_max'])) $id_book_max = intval($_POST['id_book_max']);  else $id_book_max = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$url = "list_bookmarks.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_book_max > 0) and (!preg_match("#[^0-9]#", $id_book_max)) )
  {
    define('INTRAMESSENGER',true);
    //
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " set BMK_DISPLAY = 2 ";
    $requete .= " WHERE ID_BOOKMARK <= " . $id_book_max;
    $requete .= " and BMK_DISPLAY = 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F3g]", $requete);
    //
    require ("../common/config/config.inc.php");
    if (_BOOKMARKS_PUBLIC != "")
    {
      require ("../common/functions.inc.php");
      require ("lang.inc.php"); // pour bookmarks_update_rss !
      require ("../common/bookmark.inc.php");
      //
      bookmarks_update_rss();
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
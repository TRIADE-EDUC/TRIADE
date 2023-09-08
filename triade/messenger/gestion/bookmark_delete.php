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
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$url = "list_bookmarks.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_book > 0) and (!preg_match("#[^0-9]#", $id_book)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    require ("../common/functions.inc.php");
    //
    // auteur du message
    $requete  = " select ID_USER_AUT, BMK_URL, BMK_TITLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE ID_BOOKMARK = " . $id_book;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F3b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_aut, $url, $titre) = mysqli_fetch_row ($result);
      //
      $requete  = " delete from " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE ";
      $requete .= " where ID_BOOKMARK = " . $id_book;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-F3c]", $requete);
      //
      $requete  = " delete from " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
      $requete .= " where ID_BOOKMARK = " . $id_book;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-F3a]", $requete);
      //
      $user = f_get_username_of_id($id_aut);
      $url = f_decode64_wd($url);
      $titre = f_decode64_wd($titre);
      write_log("log_bookmark_delete", $user . ";" . $titre . ";" . $url);
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
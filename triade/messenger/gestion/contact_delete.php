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
if (isset($_POST['id_contact'])) $id_contact = intval($_POST['id_contact']);  else $id_contact = 0;
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['page'])) $page = $_POST['page']; else $page = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_contact.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_contact > 0) and (!preg_match("#[^0-9]#", $id_contact)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete = "select ID_USER_1, ID_USER_2 ";
    $requete .= "FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
    $requete .= "WHERE ID_CONTACT = " . $id_contact . " ";
    //$requete .= "and CNT_STATUS > 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A9a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_user_1, $id_user_2) = mysqli_fetch_row ($result);
      //
      // Suppression des contacts
      $requete  = " delete FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
      $requete .= " WHERE ID_USER_1 = " . $id_user_1;
      $requete .= " and ID_USER_2 = " . $id_user_2;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-A9b]", $requete);
      //
      // Suppression des contacts
      $requete  = " delete FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
      $requete .= " WHERE ID_USER_1 = " . $id_user_2;
      $requete .= " and ID_USER_2 = " . $id_user_1;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-A9c]", $requete);
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
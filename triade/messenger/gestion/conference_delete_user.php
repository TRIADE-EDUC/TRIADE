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
if (isset($_POST['id_user'])) $id_user = intval($_POST['id_user']);  else $id_user = 0;
if (isset($_POST['id_conf'])) $id_conf = intval($_POST['id_conf']);  else $id_conf = 0;
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_conference.php?tri=" . $tri . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) and ($id_conf > 0) and (!preg_match("#[^0-9]#", $id_conf)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " delete from " . $PREFIX_IM_TABLE . "USC_USERCONF ";
    $requete .= " where ID_CONFERENCE = " . $id_conf . " ";
    $requete .= " and ID_USER = " . $id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-E1a]", $requete);
    //
    $requete  = " select count(ID_USER) ";
    $requete .= " from " . $PREFIX_IM_TABLE . "USC_USERCONF ";
    $requete .= " where ID_CONFERENCE = " . $id_conf . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-E1b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
      list ($nb) = mysqli_fetch_row ($result);
    //
    if (intval($nb) < 2)
    {
      // s'il ne reste qu'une personne, on vide.
      $requete  = " delete from " . $PREFIX_IM_TABLE . "USC_USERCONF ";
      $requete .= " where ID_CONFERENCE = " . $id_conf . " ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-E1c]", $requete);
      //
      // et on supprime la conférence.
      $requete  = " delete from " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ";
      $requete .= " where ID_CONFERENCE = " . $id_conf . " ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-E1d]", $requete);
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
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
if (isset($_POST['id_dest'])) $id_user = intval($_POST['id_dest']);  else  $id_user = 0;
if (isset($_POST['id_group_dest'])) $id_group_dest = intval($_POST['id_group_dest']);  else  $id_group_dest = 0;
if (isset($_POST['dest'])) $dest = $_POST['dest'];  else  $dest = "";
if (isset($_POST['action'])) $action = $_POST['action'];  else  $action = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else  $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "messagerie.php?tri=" . $tri . "&send_ok=ok&send_nb=" . $send_nb . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  define('INTRAMESSENGER',true);
  require ("../common/functions.inc.php");
  $txt = "SendOrder:";
  if ($action == "STOPCNOW")  $txt .= f_encode64("StopPCNow");
  if ($action == "BOOTPCNOW") $txt .= f_encode64("BootPCNow");
  if ($action == "BOOTIMNOW") $txt .= f_encode64("BootIMNow");
  $send_nb = 0;
  //
  require ("../common/sql.inc.php");
  //
  // envoyer à une seule personne
  if ( ($dest == '1') and ($id_user > 0) and (strlen($txt) > 3) )
  {
    $requete  = " select ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " WHERE ID_USER = " . $id_user;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2e]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_session) = mysqli_fetch_row ($result);
      //
      $requete = "INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_TIME, MSG_DATE) ";
      $requete .= "VALUES (-99, " . $id_user . ", '" . $txt . "|" . trim(f_encode64($id_session)) . "|" . "', CURTIME(), CURDATE() ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-C2a]", $requete);
      $send_nb++;
    }
  }
  //
  // envoyer UNIQUEMENT à ceux en ligne
  if ( ($dest == '2') and (strlen($txt) > 3) )
  {
    $requete  = " select distinct USR.ID_USER, SES.ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    //$requete .= " and USR.USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user, $id_session) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "|" . trim(f_encode64($id_session)) . "|||" . "', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2b]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  // envoyer UNIQUEMENT à ceux en ligne du groupe...
  if ( ($dest == '3') and (strlen($txt) > 3) )
  {
    $requete  = " select distinct USR.ID_USER, SES.ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER and USR.ID_USER = USG.ID_USER";
    $requete .= " and USG.ID_GROUP = " . $id_group_dest;
    //$requete .= " and USR.USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2f]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user, $id_session) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "|" . trim(f_encode64($id_session)) . "|||" . "', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2g]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  //
  mysqli_close($id_connect);
  //
  if ($send_nb > 0) write_log("log_send_order", $action . ";" . $dest . ";" . $id_user);
  //
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
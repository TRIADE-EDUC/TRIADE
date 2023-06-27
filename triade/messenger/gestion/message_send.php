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
if (isset($_POST['id_group_dest1'])) $id_group_dest1 = intval($_POST['id_group_dest1']);  else  $id_group_dest1 = 0;
if (isset($_POST['id_group_dest2'])) $id_group_dest2 = intval($_POST['id_group_dest2']);  else  $id_group_dest2 = 0;
if (isset($_POST['dest'])) $dest = $_POST['dest'];  else  $dest = "";
if (isset($_POST['txt'])) $txt = $_POST['txt'];  else  $txt = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else  $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$url = "messagerie.php?tri=" . $tri . "&lang=" . $lang . "&send_ok=ok&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  define('INTRAMESSENGER',true);
  require ("../common/sql.inc.php");
  require ("../common/functions.inc.php");
  //
  $txt = str_replace("'", "`", $txt);
  $txt = str_replace('"', '', $txt);
  #$txt = str_replace("/", "", $txt);
  $txt = str_replace("--", "-", $txt);
  $txt = trim($txt);
  $txt = f_encode64($txt);
  $send_nb = 0;
  //
  //
  // envoyer à une seule personne
  if ( ($dest == '1') and ($id_user > 0) and (strlen($txt) > 3) )
  {
    $requete  = "INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
    $requete .= "VALUES (-99, " . $id_user . ", '" . $txt . "', '64', CURTIME(), CURDATE() ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2a]", $requete);
    $send_nb++;
  }
  //
  //
  // envoyer UNIQUEMENT à ceux en ligne du groupe...
  if ( ($dest == '2') and (strlen($txt) > 3) and ($id_group_dest1 > 0) )
  {
    $requete  = " select distinct USR.ID_USER, SES.ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER and USR.ID_USER = USG.ID_USER";
    $requete .= " and USG.ID_GROUP = " . $id_group_dest1;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2f]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user, $id_session) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "', '64', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2g]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  // envoyer UNIQUEMENT à ceux du groupe...
  if ( ($dest == '3') and (strlen($txt) > 3) and ($id_group_dest2 > 0) )
  {
    $requete  = " select distinct USR.ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE USR.ID_USER = USG.ID_USER";
    $requete .= " and USG.ID_GROUP = " . $id_group_dest2;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2f]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "', '64', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2g]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  // envoyer UNIQUEMENT à ceux en ligne ET "Disponibles"
  if ( ($dest == '4') and (strlen($txt) > 3) )
  {
    $requete  = " select distinct USR.ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " AND SES.SES_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2h]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "', '64', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2b]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  // envoyer UNIQUEMENT à ceux en ligne
  if ( ($dest == '5') and (strlen($txt) > 3) )
  {
    $requete  = " select distinct USR.ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "', '64', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2b]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  // envoyer A TOUS 
  if ( ($dest == '6') and (strlen($txt) > 3) )
  {
    $requete  = " select distinct ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR ";
    //$requete .= " WHERE ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 ) ";
    $requete .= " WHERE USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user) = mysqli_fetch_row ($result) )
      {
        $requete2  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
        $requete2 .= " VALUES (-99, " . $id_user . ", '" . $txt . "', '64', CURTIME(), CURDATE() ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-C2c]", $requete2);
        $send_nb++;
      }
    }
  }
  //
  //
  mysqli_close($id_connect);
  //
  write_log("log_send_message", $txt . ";" . $dest . ";" . $id_user);
  //
  //
  header("location:" . $url . "send_nb=" . $send_nb . "&");
}
else
  require("redirect_acp_demo.inc.php");
?>
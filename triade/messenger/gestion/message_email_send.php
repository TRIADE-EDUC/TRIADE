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
//if (isset($_POST['id_group_dest1'])) $id_group_dest1 = intval($_POST['id_group_dest1']);  else  $id_group_dest1 = 0;
if (isset($_POST['id_group_dest2'])) $id_group_dest2 = intval($_POST['id_group_dest2']);  else  $id_group_dest2 = 0;
if (isset($_POST['dest'])) $dest = $_POST['dest'];  else  $dest = "";
if (isset($_POST['titre'])) $title = $_POST['titre'];  else  $title = "";
if (isset($_POST['msg'])) $msg = $_POST['msg'];  else  $msg = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$url = "messagerie_email.php?lang=" . $lang . "&send_ok=ok&";
$repertoire  = getcwd() . "/"; 
define('INTRAMESSENGER',true);
require ("lang.inc.php"); // pour charset !
require ("../common/config/config.inc.php");
require ("../common/functions.inc.php");
require ("../common/acp_sessions.inc.php");
//
//
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  require ("../common/sql.inc.php");
  
  //
  //$msg = str_replace("'", "`", $msg);
  $msg = str_replace('"', "``", $msg);
  $msg = str_replace("/", "-", $msg);
  $msg = trim($msg);
  //
  //$title = str_replace("'", "`", $title);
  $title = str_replace('"', "``", $title);
  $title = str_replace("/", "-", $title);
  $title = trim($title);
  $send_nb = 0;
  //
  //
  // envoyer à une seule personne
  if ( ($dest == 'U') and ($id_user > 0) and (strlen($msg) > 3) )
  {
    $requete  = " select USR_EMAIL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $id_user;
    $requete .= " and USR_EMAIL <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2z]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($email) = mysqli_fetch_row ($result);
      //
      if (f_send_email($email, $title, $msg))  $send_nb++;
    }
  }
  //
  //
  // envoyer UNIQUEMENT à ceux du groupe...
  if ( ($dest == 'G') and (strlen($msg) > 3) and ($id_group_dest2 > 0) )
  {
    $requete  = " select distinct URS.USR_EMAIL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE USR.ID_USER = USG.ID_USER";
    $requete .= " and USG.ID_GROUP = " . $id_group_dest2;
    $requete .= " and USR.USR_EMAIL <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2f]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($email) = mysqli_fetch_row ($result) )
      {
        if (f_send_email($email, $title, $msg))  $send_nb++;
      }
    }
  }
  //
  // envoyer UNIQUEMENT à ceux en ligne
  if ( ($dest == 'C') and (strlen($msg) > 3) )
  {
    $requete  = " select URS.USR_EMAIL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " and USR.USR_EMAIL <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($email) = mysqli_fetch_row ($result) )
      {
        if (f_send_email($email, $title, $msg))  $send_nb++;
      }
    }
  }
  //
  // envoyer A TOUS 
  if ( ($dest == 'A') and (strlen($msg) > 3) )
  {
    $requete  = " select USR_EMAIL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE USR_STATUS = 1 ";
    $requete .= " and USR_EMAIL <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C2d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($email) = mysqli_fetch_row ($result) )
      {
        if (f_send_email($email, $title, $msg))  $send_nb++;
      }
    }
  }
  //
  //
  mysqli_close($id_connect);
  //
  if ($send_nb > 0)  write_log("log_send_email", $dest . ";" . $title . ";" . $msg . ";" . $id_user);
  //
  //
  header("location:" . $url . "send_nb=" . $send_nb . "&");
}
else
  require("redirect_acp_demo.inc.php");
?>
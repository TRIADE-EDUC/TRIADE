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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
if ( (!isset($_GET['iu'])) or (!isset($_GET['d'])) or (!isset($_GET['m'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$dest = 		  $_GET['d'];
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
$msg =        $_GET['m'];
$session_chk = f_decode64_wd($_GET['sc']);
if (isset($_GET['ig'])) $id_group = intval(f_decode64_wd($_GET['ig'])); else $id_group = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($dest != "") and ($n_version > 34) and ($msg != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require("lang.inc.php"); // pour format date et heure.
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F59#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_role_send_alert_to_admin = "";
  $t_role_broadcast_alert_to_group = "";
  $t_role_broadcast_alert = "";
  $t_log_messages = _HISTORY_MESSAGES_ON_ACP;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_role_send_alert_to_admin = f_role_permission($id_role, "ROLE_SEND_ALERT_TO_ADMIN", "");
      $t_role_broadcast_alert_to_group = f_role_permission($id_role, "ROLE_BROADCAST_ALERT_TO_GROUP", "");
      $t_role_broadcast_alert = f_role_permission($id_role, "ROLE_BROADCAST_ALERT", "");
      $t_log_messages = f_role_permission($id_role, "HISTORY_MESSAGES_ON_ACP", _HISTORY_MESSAGES_ON_ACP);
    }
  }
  //
  if ( ($dest == "G") and (_SPECIAL_MODE_GROUP_COMMUNITY == "") and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == "") and (_GROUP_FOR_SBX_AND_ADMIN_MSG == "") ) $t_role_broadcast_alert_to_group = "";
  //
  if ( ($dest == "A") and ($t_role_send_alert_to_admin == "") )       die(">F59#KO#3#"); // 3:pas les droits
  if ( ($dest == "G") and ($t_role_broadcast_alert_to_group == "") )  die(">F59#KO#3#"); // 3:pas les droits
  if ( ($dest == "T") and ($t_role_broadcast_alert == "") )           die(">F59#KO#3#"); // 3:pas les droits
  //
  //
  $msg = f_decode64_wd($msg);
  $msg = trim($msg);
  //
  //
  if ($dest == "A")
  {
    $ok_send = "OK";
    send_alert_message_to_admins($msg);
  }
  //
  // on récupère le username expéditeur (pour LOG également) :
  $username_auth = f_get_username_of_id($id_user);
  //
  if ($dest == "G")
  {
    $requete  = " select DISTINCT USR.ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USG_USERGRP USG1, " . $PREFIX_IM_TABLE . "USG_USERGRP USG2, " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SES_SESSION SES ON USR.ID_USER = SES.ID_USER  ";
    $requete .= " WHERE USG1.ID_GROUP = USG2.ID_GROUP ";
    $requete .= " and USG1.ID_USER = USR.ID_USER ";
    $requete .= " and USG2.ID_GROUP = GRP.ID_GROUP ";
    $requete .= " AND USG2.ID_USER = " . $id_user . " ";
    if ($id_group > 0) $requete .= " and USG2.ID_GROUP = " . $id_group . " ";
    $requete .= " AND USR.USR_USERNAME <> '" . $username_auth . "' ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " AND SES.SES_STATUS > 0 ";
    $requete .= " AND SES.SES_STATUS < 5 ";
    //$requete .= " ORDER BY GRP_NAME, USR_USERNAME ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-59a]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      $ok_send = "OK";
      while( list ($id_u_dest) = mysqli_fetch_row ($result) )
      {
        send_alert_message_to_admins_2($id_u_dest, $msg);
      }
    }
  }
  //
  if ($dest == "T")
  {
    $requete  = " select distinct(USR.ID_USER) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "SES_SESSION SES";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " AND SES.SES_STATUS > 0 ";
    $requete .= " AND SES.SES_STATUS < 5 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-59b]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      $ok_send = "OK";
      while( list ($id_u_dest) = mysqli_fetch_row ($result) )
      {
        send_alert_message_to_admins_2($id_u_dest, $msg);
      }
    }
  }
  //
  //
  if ($ok_send == "OK")
  {
    echo ">F59#OK##";
    //
    //
    update_last_activity_user($id_user);
    //
    mysqli_close($id_connect);
    //
    // si option de log (archivage) des messages échangé activé :
    if ($t_log_messages != '')
    {
      $ip = $_SERVER['REMOTE_ADDR'];	
      //
      $chemin = "log/" . "log_send_message.txt" ;
      $fp = fopen($chemin, "a");
      if (flock($fp, 2));
      {
        fputs($fp,date($l_date_format_display . ";" . $l_time_format_display) . ";" . $username_auth . ";" . $dest . ";" . $msg . ";" . $ip ."\r\n");
      }
      flock($fp, 3);
      fclose($fp);
    }
  }
  else
    die(">F59#KO#0#"); // 0: car on ne sait pas pourquoi (au cas où)...
}
?>
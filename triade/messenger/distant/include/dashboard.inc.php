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
if ( (!isset($_GET['iu'])) or (!isset($_GET['is'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$version =    intval($_GET['v']);
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($version > 34) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die ("Session KO.");
  //
  //
  $requete  = " select ID_USER, USR_GET_ADMIN_ALERT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE ID_USER = " . $id_user . " ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-200a]", $requete);
  if ( mysqli_num_rows($result) == 1 ) 
  {
    list ($id_user, $usr_get_admin_alert) = mysqli_fetch_row ($result);
    //
    // si recoit les alert (admin)
    if ($usr_get_admin_alert == 1)
    {
      //
      $requete  = " select count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1a]", $requete);
      list ($nb_row_stats) = mysqli_fetch_row ($result);
      //
      $requete  = " select STA_NB_CREAT, STA_NB_SESSION ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      $requete .= " WHERE STA_DATE = '" . date("Y-m-d") . "' ";
      $requete .= " limit 0, 1";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1f]", $requete);
      list ($nb_create, $nb_session) = mysqli_fetch_row ($result);
      //
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      //$requete .= " WHERE (USR_CHECK = 'WAIT' or USR_STATUS = 2) ";
      $requete .= " WHERE USR_STATUS = 2 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1h]", $requete);
      list ($nb_user_waiting) = mysqli_fetch_row ($result);
      //
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_ACTIVITY) >= 30 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-A3d]", $requete);
      list ($nb_user_activite_more_30) = mysqli_fetch_row ($result);
      if ($nb_user_activite_more_30 > 0)
      {
        $requete  = " SELECT count(*) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_ACTIVITY) < 30 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-A3e]", $requete);
        list ($nb_user_activite_recent) = mysqli_fetch_row ($result);
      }
      //
      //
      //
      require ("lang.inc.php");
      //$msg = f_encode64(date("H:i:s")) . "#";
      if ($l_time_short_format_display == "") $l_time_short_format_display = "H:i";
      $msg = f_encode64(date($l_time_short_format_display)) . "#";  
      //
      if (intval($nb_user_waiting) > 0) $msg .= f_encode64($l_index_waiting_valid . " : " . $nb_user_waiting) . "#";
      //if (intval($nb_avatars) > 0) $msg .= f_encode64($l_index_pending_avatars . " : " . $nb_avatars) . "#";
      //if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
      if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
      {
        if (_GROUP_USER_CAN_JOIN != "") 
        {
          $requete  = " SELECT count(*) ";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USG_USERGRP ";
          $requete .= " WHERE ( USG_PENDING = 1 or USG_PENDING = -1 ) ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1z]", $requete);
          list ($nb_users_pending_group) = mysqli_fetch_row ($result);
          if (intval($nb_users_pending_group) > 0) $msg .= f_encode64($l_index_users_pending_group . " : " . $nb_users_pending_group) . "#";
        }
      }
      //
      if (intval($nb_row_stats) > 2)
      {
        if (intval($nb_user_activite_more_30) > 0) $msg .= f_encode64($l_index_users_recent_activity . " : " . $nb_user_activite_recent) . "#";
        if (intval($nb_create) > 0) $msg .= f_encode64($l_index_today_creat_users . " : " . $nb_create) . "#";
        if (intval($nb_session) > 0) $msg .= f_encode64($l_index_today_sessions . " : " . $nb_session) . "#";
      }
      /*
      if (_SHOUTBOX != "")
      {
        if (intval($sbx_nb_msg_ok) > 0) $msg .= f_encode64($l_index_shoutbox_nb_msg . " : " . $sbx_nb_msg_ok) . "#";
      }
      */
      //
      echo ">F99#OK#" . $msg;
    }
    else
    {
      echo ">F99#KO#non admin#";
    }
  }
  else
  {
    echo ">F99#KO#user not find#";
  }
  //
  mysqli_close($id_connect);
}
?>
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


function f_role_of_user($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = 0; // par défaut
  $t_id_user = intval($t_id_user);
  if ( ($t_id_user > 0) and (_ROLES_TO_OVERRIDE_PERMISSIONS != "") )
  {
    $requete  = " select ID_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G7a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_role) = mysqli_fetch_row ($result);
      $id_role = intval($id_role);
      if ($id_role > 0) 
        $retour = $id_role;
      else
      {
        // If no role, use the default one :
        $requete  = " select SQL_CACHE ID_ROLE ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
        $requete .= " WHERE ROL_DEFAULT = 'D' ";
        $requete .= " limit 1 "; // only first one
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-G7b]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($id_role) = mysqli_fetch_row ($result);
          $id_role = intval($id_role);
          if ($id_role > 0) $retour = $id_role;
        }
      }
    }
  }
  //
	return $retour;
}
	
	
function f_role_permission($id_role, $option, $default)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	//if ($default != "") $default = "X";  NON !
	$retour = $default; // par défaut
  $id_role = intval($id_role);
  if ( ($id_role > 0) and (_ROLES_TO_OVERRIDE_PERMISSIONS != "") )
  {
    $requete  = " select SQL_CACHE RLM.RLM_STATE, RLM.RLM_VALUE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE RLM, " . $PREFIX_IM_TABLE . "MDL_MODULE MDL ";
    $requete .= " WHERE RLM.ID_MODULE = MDL.ID_MODULE ";
    $requete .= " and RLM.ID_ROLE = " . $id_role . " ";
    $requete .= " and MDL.MDL_NAME = '" . $option . "' ";
    $requete .= " LIMIT 2 "; // on ne sait jamais
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G7c]", $requete);
    //if ( mysqli_num_rows($result) == 1 )
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($state, $rlm_value) = mysqli_fetch_row ($result);
      $state = intval($state);
      if ($state == 1) $retour = "";
      if ($state == 2) $retour = "X";
      if ($state == 3) $retour = $rlm_value;
    }
  }
  //
	return $retour;
}

function f_option_activated($mdl_name)
{
  $retour = "";
  //
  if ($mdl_name == "ALLOW_CHANGE_CONTACT_NICKNAME") { if (_ALLOW_CHANGE_CONTACT_NICKNAME != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_CHANGE_EMAIL_PHONE") { if (_ALLOW_CHANGE_EMAIL_PHONE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_CHANGE_FUNCTION_NAME") { if (_ALLOW_CHANGE_FUNCTION_NAME != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_CHANGE_AVATAR") { if (_ALLOW_CHANGE_AVATAR != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_CONFERENCE") { if (_ALLOW_CONFERENCE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_EMAIL_NOTIFIER") { if (_ALLOW_EMAIL_NOTIFIER != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_HIDDEN_TO_CONTACTS") { if (_ALLOW_HIDDEN_TO_CONTACTS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_SEND_TO_OFFLINE_USER") { if (_ALLOW_SEND_TO_OFFLINE_USER != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_SMILEYS") { if (_ALLOW_SMILEYS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_USE_PROXY") { if (_ALLOW_USE_PROXY != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_CONTACT_RATING") { if (_ALLOW_CONTACT_RATING != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_HISTORY_MESSAGES") { if (_ALLOW_HISTORY_MESSAGES != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "CENSOR_MESSAGES") { if (_CENSOR_MESSAGES != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "HISTORY_MESSAGES_ON_ACP") { if (_HISTORY_MESSAGES_ON_ACP != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_MANAGE_CONTACT_LIST") { if (_ALLOW_MANAGE_CONTACT_LIST != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_MANAGE_OPTIONS") { if (_ALLOW_MANAGE_OPTIONS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_MANAGE_PROFILE") { if (_ALLOW_MANAGE_PROFILE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "GROUP_USER_CAN_JOIN") { if (_GROUP_USER_CAN_JOIN != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "PENDING_USER_ON_COMPUTER_CHANGE") { if (_PENDING_USER_ON_COMPUTER_CHANGE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "USER_HIEARCHIC_MANAGEMENT_BY_ADMIN") { if (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SERVERS_STATUS") { if (_SERVERS_STATUS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "BOOKMARKS") { if (_BOOKMARKS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "BOOKMARKS_VOTE") { if (_BOOKMARKS_VOTE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "BOOKMARKS_NEED_APPROVAL") { if (_BOOKMARKS_NEED_APPROVAL != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHOUTBOX") { if (_SHOUTBOX != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHOUTBOX_VOTE") { if (_SHOUTBOX_VOTE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHOUTBOX_NEED_APPROVAL") { if (_SHOUTBOX_NEED_APPROVAL != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_SKIN") { if (_ALLOW_SKIN != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES") { if (_SHARE_FILES != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES_NEED_APPROVAL") { if (_SHARE_FILES_NEED_APPROVAL != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES_EXCHANGE") { if (_SHARE_FILES_EXCHANGE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES_EXCHANGE_NEED_APPROVAL") { if (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES_VOTE") { if (_SHARE_FILES_VOTE != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES_TRASH") { if (_SHARE_FILES_TRASH != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "SHARE_FILES_EXCHANGE_TRASH") { if (_SHARE_FILES_EXCHANGE_TRASH != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "ALLOW_HIDDEN_STATUS") { if (_ALLOW_HIDDEN_STATUS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "BACKUP_FILES") { if (_BACKUP_FILES != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "BACKUP_FILES_ALLOW_SUB_FOLDERS") { if (_BACKUP_FILES_ALLOW_SUB_FOLDERS != "") $retour = 2;  else  $retour = 1; }
  if ($mdl_name == "BACKUP_FILES_ALLOW_MULTI_FOLDERS") { if (_BACKUP_FILES_ALLOW_MULTI_FOLDERS != "") $retour = 2;  else  $retour = 1; }
  //
	return $retour;
}


function f_option_value($mdl_name)
{
  $retour = 0;
  //
  if ($mdl_name == "MAX_NB_IP") $retour = _MAX_NB_IP;
  if ($mdl_name == "MAX_NB_CONTACT_BY_USER") $retour = _MAX_NB_CONTACT_BY_USER;
  if ($mdl_name == "MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER") $retour = _MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER;
  if ($mdl_name == "LOCK_DURATION") $retour = _LOCK_DURATION;
  //if ($mdl_name == "CHECK_NEW_MSG_EVERY") $retour = _CHECK_NEW_MSG_EVERY;
  if ($mdl_name == "SHOUTBOX_REFRESH_DELAY") $retour = _SHOUTBOX_REFRESH_DELAY;
  if ($mdl_name == "SHOUTBOX_QUOTA_USER_DAY") $retour = _SHOUTBOX_QUOTA_USER_DAY;
  if ($mdl_name == "SHOUTBOX_QUOTA_USER_WEEK") $retour = _SHOUTBOX_QUOTA_USER_WEEK;
  if ($mdl_name == "SHOUTBOX_APPROVAL_QUEUE_USER") $retour = _SHOUTBOX_APPROVAL_QUEUE_USER;
  if ($mdl_name == "SHOUTBOX_LOCK_USER_APPROVAL") $retour = _SHOUTBOX_LOCK_USER_APPROVAL;
  if ($mdl_name == "SHOUTBOX_MAX_NOTES_USER_DAY") $retour = _SHOUTBOX_MAX_NOTES_USER_DAY;
  if ($mdl_name == "SHOUTBOX_MAX_NOTES_USER_WEEK") $retour = _SHOUTBOX_MAX_NOTES_USER_WEEK;
  if ($mdl_name == "SHOUTBOX_REMOVE_MESSAGE_VOTES") $retour = _SHOUTBOX_REMOVE_MESSAGE_VOTES;
  if ($mdl_name == "SHOUTBOX_LOCK_USER_VOTES") $retour = _SHOUTBOX_LOCK_USER_VOTES;
  if ($mdl_name == "SHARE_FILES_MAX_FILE_SIZE") $retour = _SHARE_FILES_MAX_FILE_SIZE;
  if ($mdl_name == "SHARE_FILES_MAX_NB_FILES_USER") $retour = _SHARE_FILES_MAX_NB_FILES_USER;
  if ($mdl_name == "SHARE_FILES_MAX_SPACE_SIZE_USER") $retour = _SHARE_FILES_MAX_SPACE_SIZE_USER;
  if ($mdl_name == "SHARE_FILES_QUOTA_FILES_USER_WEEK") $retour = _SHARE_FILES_QUOTA_FILES_USER_WEEK;
  if ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY") $retour = _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY;
  if ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK") $retour = _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK;
  if ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH") $retour = _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH;
  if ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY") $retour = _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY;
  if ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK") $retour = _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK;
  if ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH") $retour = _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH;
  if ($mdl_name == "BACKUP_FILES_MAX_ARCHIVE_SIZE") $retour = _BACKUP_FILES_MAX_ARCHIVE_SIZE;
  if ($mdl_name == "BACKUP_FILES_MAX_NB_ARCHIVES_USER") $retour = _BACKUP_FILES_MAX_NB_ARCHIVES_USER;
  if ($mdl_name == "BACKUP_FILES_MAX_SPACE_SIZE_USER") $retour = _BACKUP_FILES_MAX_SPACE_SIZE_USER;
  //
	return $retour;
}

	
function fill_table_module()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  //
  $nb_rlm = 0;
  $requete  = " select count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G7g]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($nb_rlm) = mysqli_fetch_row ($result);
  }
  //
  if ($nb_rlm <= 0)
  {
    //$requete = "TRUNCATE TABLE " . $PREFIX_IM_TABLE . "MDL_MODULE ";
    $requete = "DELETE FROM " . $PREFIX_IM_TABLE . "MDL_MODULE ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G7d]", $requete);
    //
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != '')
    {
      // Pour éviter les messages d'erreur si doublons, on exécute une par une sans afficher de message d'erreur !!
      //
      // ------------------------- Options -------------------------
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (1, 'ALLOW_CHANGE_CONTACT_NICKNAME', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (2, 'ALLOW_CHANGE_EMAIL_PHONE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (3, 'ALLOW_CHANGE_FUNCTION_NAME', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (4, 'ALLOW_CHANGE_AVATAR', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (5, 'ALLOW_CONFERENCE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (6, 'ALLOW_EMAIL_NOTIFIER', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (7, 'ALLOW_HIDDEN_TO_CONTACTS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (8, 'ALLOW_SEND_TO_OFFLINE_USER', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (9, 'ALLOW_SMILEYS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (10, 'ALLOW_USE_PROXY', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (11, 'ALLOW_CONTACT_RATING', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (12, 'ALLOW_HISTORY_MESSAGES', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (13, 'CENSOR_MESSAGES', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (14, 'HISTORY_MESSAGES_ON_ACP', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (15, 'MAX_NB_IP', 99, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (16, 'MAX_NB_CONTACT_BY_USER', 999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (17, 'MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER', 20, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (18, 'LOCK_DURATION', 9999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (19, 'ALLOW_HIDDEN_STATUS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (30, 'ALLOW_MANAGE_CONTACT_LIST', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (31, 'ALLOW_MANAGE_OPTIONS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (32, 'ALLOW_MANAGE_PROFILE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (40, 'GROUP_USER_CAN_JOIN', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (41, 'PENDING_USER_ON_COMPUTER_CHANGE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (42, 'USER_HIEARCHIC_MANAGEMENT_BY_ADMIN', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (43, 'SERVERS_STATUS', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (50, 'BOOKMARKS', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (51, 'BOOKMARKS_VOTE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (52, 'BOOKMARKS_NEED_APPROVAL', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //

      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (59, 'ALLOW_SKIN', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (60, 'SHOUTBOX', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (61, 'SHOUTBOX_REFRESH_DELAY', 120, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (62, 'SHOUTBOX_QUOTA_USER_DAY', 99, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (63, 'SHOUTBOX_QUOTA_USER_WEEK', 999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (64, 'SHOUTBOX_NEED_APPROVAL', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (65, 'SHOUTBOX_APPROVAL_QUEUE_USER', 9, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (66, 'SHOUTBOX_LOCK_USER_APPROVAL', 99, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (67, 'SHOUTBOX_VOTE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (68, 'SHOUTBOX_MAX_NOTES_USER_DAY', 999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (69, 'SHOUTBOX_MAX_NOTES_USER_WEEK', 999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (70, 'SHOUTBOX_REMOVE_MESSAGE_VOTES', 99, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (71, 'SHOUTBOX_LOCK_USER_VOTES', 99, ''), ";
      $result = mysqli_query($id_connect, $requete);
      //


      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (73, 'BACKUP_FILES', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (74, 'BACKUP_FILES_ALLOW_MULTI_FOLDERS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (75, 'BACKUP_FILES_ALLOW_SUB_FOLDERS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (76, 'BACKUP_FILES_MAX_ARCHIVE_SIZE', 999999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (77, 'BACKUP_FILES_MAX_NB_ARCHIVES_USER', 9, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (78, 'BACKUP_FILES_MAX_SPACE_SIZE_USER', 999999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //

      
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (80, 'SHARE_FILES', 0, 'X'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (81, 'SHARE_FILES_NEED_APPROVAL', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (82, 'SHARE_FILES_EXCHANGE', 0, '');";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (83, 'SHARE_FILES_EXCHANGE_NEED_APPROVAL', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (84, 'SHARE_FILES_MAX_FILE_SIZE', 999999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (85, 'SHARE_FILES_MAX_NB_FILES_USER', 9999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (86, 'SHARE_FILES_MAX_SPACE_SIZE_USER', 99999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (87, 'SHARE_FILES_QUOTA_FILES_USER_WEEK', 999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (88, 'SHARE_FILES_VOTE', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (89, 'SHARE_FILES_TRASH', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (90, 'SHARE_FILES_EXCHANGE_TRASH', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (91, 'SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY', 999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (92, 'SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK', 9999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (93, 'SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH', 99999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (94, 'SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY', 9999, '');  ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (95, 'SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK', 99999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (96, 'SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH', 99999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      //if (!$result) error_sql_log("[ERR-G7e]", $requete);
      //
      //
      // ------------------------- Roles -------------------------
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (100, 'ROLE_GET_ADMIN_ALERT_MESSAGES', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (101, 'ROLE_SEND_ALERT_TO_ADMIN', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (102, 'ROLE_BROADCAST_ALERT_TO_GROUP', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (103, 'ROLE_BROADCAST_ALERT', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (104, 'ROLE_SHARE_FILES_READ_ONLY', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (105, 'ROLE_OFFLINE_MODE', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (106, 'ROLE_CHANGE_SERVER_STATUS', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      //
      //if (!$result) error_sql_log("[ERR-G7f]", $requete);
    }
  }
}
?>
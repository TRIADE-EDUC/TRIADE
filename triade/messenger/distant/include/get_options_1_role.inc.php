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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version = 	intval($_GET['v']);
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($n_version > 0) and ($ip != "") and (_ROLES_TO_OVERRIDE_PERMISSIONS != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/roles.inc.php");
  $id_role = f_role_of_user($id_user);
  //
  if ($id_role > 0)
  {
    $t_allow_change_contact_nickname = f_role_permission($id_role, "ALLOW_CHANGE_CONTACT_NICKNAME", _ALLOW_CHANGE_CONTACT_NICKNAME);
    $t_allow_change_email_phone = f_role_permission($id_role, "ALLOW_CHANGE_EMAIL_PHONE", _ALLOW_CHANGE_EMAIL_PHONE);
    $t_allow_change_function_name = f_role_permission($id_role, "ALLOW_CHANGE_FUNCTION_NAME", _ALLOW_CHANGE_FUNCTION_NAME);
    $t_allow_change_avatar = f_role_permission($id_role, "ALLOW_CHANGE_AVATAR", _ALLOW_CHANGE_AVATAR);
    $t_allow_conference = f_role_permission($id_role, "ALLOW_CONFERENCE", _ALLOW_CONFERENCE);
    $t_allow_email_notifier = f_role_permission($id_role, "ALLOW_EMAIL_NOTIFIER", _ALLOW_EMAIL_NOTIFIER);
    $t_allow_invisible = f_role_permission($id_role, "ALLOW_HIDDEN_TO_CONTACTS", _ALLOW_HIDDEN_TO_CONTACTS);
    $t_allow_send_to_offline_user = f_role_permission($id_role, "ALLOW_SEND_TO_OFFLINE_USER", _ALLOW_SEND_TO_OFFLINE_USER);
    $t_allow_smileys = f_role_permission($id_role, "ALLOW_SMILEYS", _ALLOW_SMILEYS);
    $t_allow_use_proxy = f_role_permission($id_role, "ALLOW_USE_PROXY", _ALLOW_USE_PROXY); // 10
    $t_allow_rating = f_role_permission($id_role, "ALLOW_CONTACT_RATING", _ALLOW_CONTACT_RATING); // 11
    //
    $t_user_cannot_history_messages = f_role_permission($id_role, "ALLOW_HISTORY_MESSAGES", _ALLOW_HISTORY_MESSAGES); // 12
    if ($t_user_cannot_history_messages == "") $t_user_cannot_history_messages = "X";   else  $t_user_cannot_history_messages = "";   // (option inversée !!!)
    //$t_censor_messages = f_role_permission($id_role, "CENSOR_MESSAGES", _CENSOR_MESSAGES); // 13
    if (_CRYPT_MESSAGES != "") 
      $t_log_messages = 1;  // 14
    else
      $t_log_messages = f_role_permission($id_role, "HISTORY_MESSAGES_ON_ACP", _HISTORY_MESSAGES_ON_ACP); // 14
    //
    //MAX_NB_IP - 15
    $t_max_nb_contact_by_user = f_role_permission($id_role, "MAX_NB_CONTACT_BY_USER", _MAX_NB_CONTACT_BY_USER); // 16
    //MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER - 17
    //LOCK_DURATION - 18
    $t_allow_hidden_status = f_role_permission($id_role, "ALLOW_HIDDEN_STATUS", _ALLOW_HIDDEN_STATUS); // 19
    //
    //
    // 30 :
    $t_lock_user_contact_list = f_role_permission($id_role, "ALLOW_MANAGE_CONTACT_LIST", _ALLOW_MANAGE_CONTACT_LIST);
    $t_lock_user_options = f_role_permission($id_role, "ALLOW_MANAGE_OPTIONS", _ALLOW_MANAGE_OPTIONS);
    $t_lock_user_profil = f_role_permission($id_role, "ALLOW_MANAGE_PROFILE", _ALLOW_MANAGE_PROFILE);
    // options inversées depuis version 2.0.5 :
    if ($t_lock_user_contact_list == "") $t_lock_user_contact_list = "X";  else  $t_lock_user_contact_list = "";
    if ($t_lock_user_options == "") $t_lock_user_options = "X";  else  $t_lock_user_options = "";
    if ($t_lock_user_profil == "") $t_lock_user_profil = "X";  else  $t_lock_user_profil = "";
    //
    //
    // 40 :
    $t_group_user_can_join = f_role_permission($id_role, "GROUP_USER_CAN_JOIN", _GROUP_USER_CAN_JOIN);
    //if (PENDING_USER_ON_COMPUTER_CHANGE != '')
    //if (USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '')
    $t_srv_list_status = f_role_permission($id_role, "SERVERS_STATUS", _SERVERS_STATUS);
    // 50 :
    $t_bookmarks = f_role_permission($id_role, "BOOKMARKS", _BOOKMARKS);
    $t_bookmarks_allow_vote = f_role_permission($id_role, "BOOKMARKS_VOTE", _BOOKMARKS_VOTE);
    $t_bookmarks_need_approval = f_role_permission($id_role, "BOOKMARKS_NEED_APPROVAL", _BOOKMARKS_NEED_APPROVAL);
    //
    $t_allow_skin = f_role_permission($id_role, "ALLOW_SKIN", _ALLOW_SKIN);
    // 60 :
    $t_allow_shoutbox = f_role_permission($id_role, "SHOUTBOX", _SHOUTBOX);
    $t_shoutbox_allow_vote = f_role_permission($id_role, "SHOUTBOX_VOTE", _SHOUTBOX_VOTE);
    $t_shoutbox_need_approval = f_role_permission($id_role, "SHOUTBOX_NEED_APPROVAL", _SHOUTBOX_NEED_APPROVAL);
    $t_shoutbox_refresh_delay = f_role_permission($id_role, "SHOUTBOX_REFRESH_DELAY", _SHOUTBOX_REFRESH_DELAY);
    //
    $t_backup_files = f_role_permission($id_role, "BACKUP_FILES", _BACKUP_FILES);
    $t_backup_files_multi_folders = f_role_permission($id_role, "BACKUP_FILES_ALLOW_MULTI_FOLDERS", _BACKUP_FILES_ALLOW_MULTI_FOLDERS);
    $t_backup_files_sub_folders = f_role_permission($id_role, "BACKUP_FILES_ALLOW_SUB_FOLDERS", _BACKUP_FILES_ALLOW_SUB_FOLDERS);
    //$t_backup_files_max_archive_size = f_role_permission($id_role, "BACKUP_FILES_MAX_ARCHIVE_SIZE", _BACKUP_FILES_MAX_ARCHIVE_SIZE);
    //$t_backup_files_max_nb_archive_user = f_role_permission($id_role, "BACKUP_FILES_MAX_NB_ARCHIVES_USER", _BACKUP_FILES_MAX_NB_ARCHIVES_USER);
    //$t_backup_files_max_space_size_user = f_role_permission($id_role, "BACKUP_FILES_MAX_SPACE_SIZE_USER", _BACKUP_FILES_MAX_SPACE_SIZE_USER);
    // 80 :
    $t_allow_share_files = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
    $t_share_files_approval = f_role_permission($id_role, "SHARE_FILES_NEED_APPROVAL", _SHARE_FILES_NEED_APPROVAL);
    $t_allow_share_files_exchange = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
    $t_share_files_exchange_approval = f_role_permission($id_role, "SHARE_FILES_EXCHANGE_NEED_APPROVAL", _SHARE_FILES_EXCHANGE_NEED_APPROVAL);
    $t_share_files_vote = f_role_permission($id_role, "SHARE_FILES_VOTE", _SHARE_FILES_VOTE);
    if (intval($n_version) < 35) $t_allow_share_files = ""; // important !
    if ($t_allow_share_files == "") $t_allow_share_files_exchange = "";
    // 100 :
    $t_role_get_admin_alert_msg = f_role_permission($id_role, "ROLE_GET_ADMIN_ALERT_MESSAGES", ""); // c'est un role, pas une option !
    $t_role_send_alert_to_admin = f_role_permission($id_role, "ROLE_SEND_ALERT_TO_ADMIN", ""); // c'est un role, pas une option !
    $t_role_broadcast_alert_to_group = f_role_permission($id_role, "ROLE_BROADCAST_ALERT_TO_GROUP", ""); // c'est un role, pas une option !
    $t_role_broadcast_alert = f_role_permission($id_role, "ROLE_BROADCAST_ALERT", ""); // c'est un role, pas une option !
    $t_role_share_files_read_only = f_role_permission($id_role, "ROLE_SHARE_FILES_READ_ONLY", ""); // c'est un role, pas une option !
    $t_role_srv_offline_mode = f_role_permission($id_role, "ROLE_OFFLINE_MODE", ""); // c'est un role, pas une option !
    $t_role_change_server_status = f_role_permission($id_role, "ROLE_CHANGE_SERVER_STATUS", ""); // c'est un role, pas une option !
    //
    //$xxxxxxxxxxxxxxxxxxxxxxxxx = f_role_permission($id_role, "XXXXXXXXXXXXXXXXX", _XXXXXXXXXXXXXXXX);
    //$xxxxxxxxxxxxxxxxxxxxxxxxx = f_role_permission($id_role, "XXXXXXXXXXXXXXXXX", _XXXXXXXXXXXXXXXX);
    //
    //if (_PWD_NEED_DIGIT_LETTER != '')                     $t_pass_need_digit_and_letter = "X"; else $t_pass_need_digit_and_letter = "";
    //if (_PWD_NEED_UPPER_LOWER != '')                      $t_pass_need_upper_and_lower = "X"; else $t_pass_need_upper_and_lower = "";
    //if (_PWD_NEED_SPECIAL_CHARACTER != '')                $t_pass_need_special_character = "X"; else $t_pass_need_special_character = "";
    //
    mysqli_close($id_connect);
    //
    // on renvoi les valeurs des options suivant le role :
    echo ">F05#OK#" . $t_allow_change_contact_nickname . "#" . $t_allow_change_email_phone . "#" . $t_allow_change_function_name . "#";
    echo $t_allow_change_avatar . "#" . $t_allow_conference  . "#" . $t_allow_email_notifier . "#" .$t_allow_invisible . "#";
    echo $t_allow_send_to_offline_user . "#" . $t_allow_smileys . "#" . $t_allow_use_proxy . "#";
    echo $t_allow_rating . "#" . $t_user_cannot_history_messages . "#"; // 11 et 12
    echo "#" . $t_log_messages . "#"; // 14
    if ($n_version < 42) // (version 2.0.5 : correction décalage)
      echo $t_max_nb_contact_by_user . "####"; // 16 (en 15ème position)
    else
      echo "#" . $t_max_nb_contact_by_user . "###"; // 16
    //
    echo $t_allow_hidden_status . "####"; // 19
    // 30 :
    echo $t_lock_user_contact_list . "#" . $t_lock_user_options . "#" . $t_lock_user_profil . "#####";
    // 40 :
    echo $t_group_user_can_join . "#". "#". "#".  $t_srv_list_status . "###";
    // 50 :
    echo $t_bookmarks . "#" . $t_bookmarks_allow_vote . "#" . $t_bookmarks_need_approval . "#";
    if ($n_version >= 43)
      echo "###" . $t_allow_skin . "#";
    else
      echo "####";
    //
    // 60 :
    echo $t_allow_shoutbox . "#" . $t_shoutbox_allow_vote . "#" . $t_shoutbox_need_approval . "#" . $t_shoutbox_refresh_delay . "###";
    if ($n_version < 43)
      echo "#" . $t_allow_skin . "###";
    else
      echo $t_backup_files . "#" . $t_backup_files_multi_folders . "#" . $t_backup_files_sub_folders . "##";
    //
    // 80 :
    echo $t_allow_share_files . "#" . $t_share_files_approval . "#" . $t_allow_share_files_exchange . "#";
    echo $t_share_files_exchange_approval . "#" . $t_share_files_vote . "#";
    echo "############" ;
    // 100 :
    echo $t_role_get_admin_alert_msg . "#" . $t_role_send_alert_to_admin  . "#" . $t_role_broadcast_alert_to_group . "#";
    echo $t_role_broadcast_alert .  "#" . $t_role_share_files_read_only . "#" . $t_role_srv_offline_mode . "#";
    echo $t_role_change_server_status . "#";
    //echo $xxxxxxxxxxxxxxxxxx . "#" . $xxxxxxxxxxxxxxxxx . "#" . $xxxxxxxxxxxxxx . "#";
    //echo $xxxxxxxxxxxxxxxxxx . "#" . $xxxxxxxxxxxxxxxxx . "#" . $xxxxxxxxxxxxxx . "#";
    echo "#################" ;
  }
  else
  {
    mysqli_close($id_connect);
    echo ">F05#SO#"; // no role
  }
}
else
  echo ">F05#KO#";
?>
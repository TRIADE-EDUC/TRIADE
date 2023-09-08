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
if ( (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version = 	intval($_GET['v']);
if (isset($_GET['x'])) $exe = $_GET['x']; else $exe = ""; // ajouté le 11/02/10
//
if ( ($n_version > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/constant.inc.php");
  require ("../common/extern/extern.inc.php");
  require ("../common/f_not_empty.inc.php");
  prevent_error_extern_option_missing();
  //
  $option_col_name_default_active = "";
  $user_cannot_history_messages = "";
  if (_ALLOW_HISTORY_MESSAGES == "") $user_cannot_history_messages = "X";   // (option inversée !!!)
  //
  if ( intval($n_version) < intval(_CLIENT_VERSION_MINI) )
  {
    // Version number to old (périmée)
    echo ">F01#KO#" . _CLIENT_VERSION_MINI . "#";
    //
    require ("../common/log.inc.php");
    write_log("error_version_log", $ip);
  }
  else
  {
    $private = '';
    if ( strlen(_PASSWORD_FOR_PRIVATE_SERVER) > 5 ) $private = 'P';
    //
    $opt_srv_display_options_list = '';
    $opt_srv_display_user_list = '';
    $opt_srv_can_propose_avatar = '';
    $opt_srv_shoutbox_public = '';
    $opt_srv_bookmarks_public = '';
    if ( (is_readable("../" . _PUBLIC_FOLDER . "/options.php")) and (_PUBLIC_OPTIONS_LIST != "") ) $opt_srv_display_options_list = 'DOL3';
    if ( (is_readable("../" . _PUBLIC_FOLDER . "/users.php")) and (_PUBLIC_USERS_LIST != "") ) $opt_srv_display_user_list = 'UL1';
    if ( (is_readable("../" . _PUBLIC_FOLDER . "/avatar.php")) and (_PUBLIC_POST_AVATAR != '') ) $opt_srv_can_propose_avatar = 'AV1';
    if ( (is_readable("../" . _PUBLIC_FOLDER . "/shoutbox_sticker.php")) and (_SHOUTBOX_PUBLIC != '') and (_SHOUTBOX != '') ) $opt_srv_shoutbox_public = 'AV1';
    if ( (is_readable("../" . _PUBLIC_FOLDER . "/bookmarks.php")) and (_BOOKMARKS_PUBLIC != '') and (_BOOKMARKS != '') ) $opt_srv_bookmarks_public = 'AV1';
    //
    //
    $can_update_by_server = "";
    $must_update_by_server = "";
    $must_update_by_internet = "";
    if ($exe != "") 
    {
      $exe = f_decode64_wd($exe);
      $exe = f_clean_name($exe);
    }
    if ($exe == "") $exe = "IntraMessenger";
    if ( (is_readable("update/" . $exe . ".exe")) and (is_readable("update/version.ini")) )
    {
      $can_update_by_server = 'X';
      if ( _FORCE_UPDATE_BY_SERVER != '') $must_update_by_server = "X";
    }
    if (_FORCE_UPDATE_BY_INTERNET != '')
    {
      $must_update_by_server = "";
      $must_update_by_internet = "X";
    }
    //
    //
    $authentication_by_extern = "";
    $authentication_extern_type = "";
    if (f_nb_auth_extern() == 1) 
    {
      $authentication_by_extern = "X";
      $authentication_extern_type = f_type_auth_extern();
      if ($authentication_extern_type != "") 
      {
        if (strlen(_SITE_TITLE) > 2) 
          $authentication_extern_type = f_encode64(_SITE_TITLE);
        else
          $authentication_extern_type = f_encode64($authentication_extern_type);
      }
    }
    //
    $t_force_options_from_server = "";
    if (_FORCE_OPTION_FILE_FROM_SERVER != '')
    {
      if (is_readable("update/options.ini")) $t_force_options_from_server = "X";
    }
    //
    //
    $t_check_new_msg_every =                              intval(_CHECK_NEW_MSG_EVERY);
    $t_minimum_username_length =                          intval(_MINIMUM_USERNAME_LENGTH);
    $t_minimum_password_length =                          intval(_MINIMUM_PASSWORD_LENGTH);
    $t_proxy_port_number =                                intval(_PROXY_PORT_NUMBER);
    $t_mx_nb_contact =                                    intval(_MAX_NB_CONTACT_BY_USER);
    $t_proxy_address =                                    f_encode64(_PROXY_ADDRESS);
    $t_backup_files_force_every_at =                      f_encode64(_BACKUP_FILES_FORCE_EVERY_DAY_AT);
    $t_sbx_refresh_delay =                                intval(_SHOUTBOX_REFRESH_DELAY);
    //$t_max_password_validity =                            intval(_PASSWORD_VALIDITY);
    if (_FORCE_USERNAME_TO_PC_SESSION_NAME != '')         $t_force_username_to_pc_session_name = "X"; else $t_force_username_to_pc_session_name = "";
    if (_USER_NEED_PASSWORD != '')                        $t_user_need_password = "X"; else $t_user_need_password = "";
    if (_CRYPT_MESSAGES != '')                            $t_crypt_messages = "X"; else $t_crypt_messages = "";
    if (_FORCE_AWAY_ON_SCREENSAVER != '')                 $t_force_away_on_screensaver = "X"; else $t_force_away_on_screensaver = "";
    if (_ALLOW_COL_FUNCTION_NAME == '')                   $t_hide_col_function_name = "X"; else $t_hide_col_function_name = "";
    if (_ALLOW_HIDDEN_TO_CONTACTS != '')                  $t_allow_invisible = "X"; else $t_allow_invisible = "";
    if (_ALLOW_SEND_TO_OFFLINE_USER != '')                $t_allow_send_to_offline_user = "X"; else $t_allow_send_to_offline_user = "";
    if (_ALLOW_CHANGE_CONTACT_NICKNAME != '')             $t_allow_change_contact_nickname = "X"; else $t_allow_change_contact_nickname = "";
    if (_ALLOW_CONFERENCE != '')                          $t_allow_conference = "X"; else $t_allow_conference = "";
    if (_ALLOW_CHANGE_EMAIL_PHONE != '')                  $t_allow_change_email_phone = "X"; else $t_allow_change_email_phone = "";
    if (_ALLOW_CHANGE_FUNCTION_NAME != '')                $t_allow_change_function_name = "X"; else $t_allow_change_function_name = "";
    if (_ALLOW_MANAGE_CONTACT_LIST == '')                 $t_lock_user_contact_list = "X"; else $t_lock_user_contact_list = ""; // option inversée en version 2.0.5
    if (_ALLOW_MANAGE_OPTIONS == '')                      $t_lock_user_options = "X"; else $t_lock_user_options = ""; // option inversée en version 2.0.5
    if (_ALLOW_MANAGE_PROFILE == '')                      $t_lock_user_profil = "X"; else $t_lock_user_profil = ""; // option inversée en version 2.0.5
    if (_HISTORY_MESSAGES_ON_ACP != '')                   $t_log_messages = "X"; else $t_log_messages = "";
    if (_SPECIAL_MODE_OPEN_COMMUNITY != '')               $t_open_community = "X"; else $t_open_community = "";
    if (_SPECIAL_MODE_GROUP_COMMUNITY != '')              $t_group_community = "X"; else $t_group_community = "";
    if (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '')         $t_group_community = "X"; // else $t_group_community = ""; // $t_open_community = "X";
    if (_FLAG_COUNTRY_FROM_IP != '')                      $t_display_user_flag_country = "X"; else $t_display_user_flag_country = "";
    if (_ALLOW_SMILEYS != '')                             $t_allow_smileys = "X"; else $t_allow_smileys = "";
    if (_ALLOW_CHANGE_AVATAR != '')                       $t_allow_change_avatar = "X"; else $t_allow_change_avatar = "";
    if (_ALLOW_USE_PROXY != '')                           $t_allow_use_proxy = "X"; else $t_allow_use_proxy = "";
    if (_ALLOW_EMAIL_NOTIFIER != '')                      $t_allow_email_notifier = "X"; else $t_allow_email_notifier = "";
    if (_ENTERPRISE_SERVER != '')                         $t_enterprise_server = "X"; else $t_enterprise_server = "";
    if (_ALLOW_CONTACT_RATING != '')                      $t_allow_rating = "X"; else $t_allow_rating = "";
    if (_ALLOW_UPPERCASE_SPACE_USERNAME != '')            $t_allow_space_nickname = "X"; else $t_allow_space_nickname = "";
    if (_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER != '')  $t_need_quick_register = "X"; else $t_need_quick_register = "";
    if (_PWD_NEED_DIGIT_LETTER != '')                     $t_pass_need_digit_and_letter = "X"; else $t_pass_need_digit_and_letter = "";
    if (_PWD_NEED_UPPER_LOWER != '')                      $t_pass_need_upper_and_lower = "X"; else $t_pass_need_upper_and_lower = "";
    if (_PWD_NEED_SPECIAL_CHARACTER != '')                $t_pass_need_special_character = "X"; else $t_pass_need_special_character = "";
    if (_SHOUTBOX != '')                                  $t_allow_shoutbox = "X"; else $t_allow_shoutbox = "";
    if (_SHOUTBOX_VOTE != '')                             $t_shoutbox_allow_vote = "X"; else $t_shoutbox_allow_vote = "";
    if (_GROUP_FOR_SBX_AND_ADMIN_MSG != '')               $t_group_for_shoutbox = "X"; else $t_group_for_shoutbox = "";
    if (_GROUP_USER_CAN_JOIN != '')                       $t_group_user_can_join = "X"; else $t_group_user_can_join = "";
    if (_SERVERS_STATUS != '')                            $t_srv_list_status = "X"; else $t_srv_list_status = "";
    if (_SHOUTBOX_NEED_APPROVAL != '')                    $t_shoutbox_need_approval = "X"; else $t_shoutbox_need_approval = "";
    if (_BOOKMARKS != '')                                 $t_bookmarks = "X"; else $t_bookmarks = "";
    if (_BOOKMARKS_VOTE != '')                            $t_bookmarks_allow_vote = "X"; else $t_bookmarks_allow_vote = "";
    if (_BOOKMARKS_NEED_APPROVAL != '')                   $t_bookmarks_need_approval = "X"; else $t_bookmarks_need_approval = "";
    if (_INVITE_FILL_PROFILE_ON_FIRST_LOGIN != '')        $t_new_user_register_profil_after_start = "X"; else $t_new_user_register_profil_after_start = "";
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != '')             $t_roles = "X"; else $t_roles = "";
    if (_WAIT_STARTUP_IF_SERVER_UNAVAILABLE != '')        $t_wait_on_start_if_server_hs = "X"; else $t_wait_on_start_if_server_hs = "";
    if (_FORCE_LAUNCH_ON_STARTUP != '')                   $t_force_launch_on_startup = "X"; else $t_force_launch_on_startup = "";
    if (_ALLOW_SKIN != '')                                $t_allow_skin = "X"; else $t_allow_skin = "";
    if (_ALLOW_CLOSE_IM != '')                            $t_allow_close_im = "X"; else $t_allow_close_im = "";
    if (_ALLOW_SOUND_USAGE != '')                         $t_allow_sound_usage = "X"; else $t_allow_sound_usage = "";
    if (_ALLOW_REDUCE_MAIN_SCREEN != '')                  $t_allow_reduce_main_screen = "X"; else $t_allow_reduce_main_screen = "";
    if (_ALLOW_REDUCE_MESSAGE_SCREEN != '')               $t_allow_reduce_window_message = "X"; else $t_allow_reduce_window_message = "";
    if (_ALLOW_POST_IT != '')                             $t_allow_postit = "X"; else $t_allow_postit = "";
    if (_SHARE_FILES != '')                               $t_allow_share_files = "X"; else $t_allow_share_files = "";
    if (_SHARE_FILES_EXCHANGE != '')                      $t_allow_share_files_exchange = "X"; else $t_allow_share_files_exchange = "";
    if (_SHARE_FILES_NEED_APPROVAL != '')                 $t_share_files_approval = "X"; else $t_share_files_approval = "";
    if (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != '')        $t_share_files_exchange_approval = "X"; else $t_share_files_exchange_approval = "";
    if (_SHARE_FILES_VOTE != '')                          $t_share_files_vote = "X"; else $t_share_files_vote = "";
    if (_SHARE_FILES_SCREENSHOT != '')                    $t_share_files_screenshot = "X"; else $t_share_files_screenshot = "";
    if (_SHARE_FILES_EXCHANGE_SCREENSHOT != '')           $t_share_files_exchange_screenshot = "X"; else $t_share_files_exchange_screenshot = "";
    if (_SHARE_FILES_WEBCAM != '')                        $t_share_files_webcam = "X"; else $t_share_files_webcam = "";
    if (_SHARE_FILES_EXCHANGE_WEBCAM != '')               $t_share_files_exchange_webcam = "X"; else $t_share_files_exchange_webcam = "";
    if (_SHARE_FILES_COMPRESS != '')                      $t_share_files_compress = "X"; else $t_share_files_compress = "";
    if (_SHARE_FILES_PROTECT != '')                       $t_share_files_protect = "X"; else $t_share_files_protect = "";
    if (_SHARE_FILES_ALLOW_UPPERCASE != '')               $t_share_files_allow_uppercase = "X"; else $t_share_files_allow_uppercase = "";
    if (_ALLOW_HIDDEN_STATUS != '')                       $t_allow_hidden_status = "X"; else $t_allow_hidden_status = "";
    if (_ALLOW_HISTORY_MESSAGES_EXPORT != '')             $t_allow_history_export = "X"; else $t_allow_history_export = "";
    if (_BACKUP_FILES != '')                              $t_allow_backup_files = "X"; else $t_allow_backup_files = "";
    if (_BACKUP_FILES_ALLOW_MULTI_FOLDERS != '')          $t_backup_files_multi_folders = "X"; else $t_backup_files_multi_folders = "";
    if (_BACKUP_FILES_ALLOW_SUB_FOLDERS != '')            $t_backup_files_sub_folders = "X"; else $t_backup_files_sub_folders = "";
    if (_SHARE_FILES_ALLOW_ACCENT != '')                  $t_share_files_no_accent = ""; else $t_share_files_no_accent = "X";
    if (_SHOUTBOX_ALLOW_SCROLLING != '')                  $t_shoutbox_scrolling = ""; else $t_shoutbox_scrolling = "X";
    //
    if (intval($n_version) < 35) $t_allow_share_files = ""; // important !
    if ( (_SHARE_FILES_FTP_ADDRESS == "") or (_SHARE_FILES_FTP_LOGIN == "") or (_SHARE_FILES_FTP_PASSWORD_CRYPT == "") ) $t_allow_share_files = "";
    if ( (_BACKUP_FILES_FTP_ADDRESS == "") or (_BACKUP_FILES_FTP_LOGIN == "") or (_BACKUP_FILES_FTP_PASSWORD_CRYPT == "") ) $t_allow_backup_files = "";
    if ($t_allow_share_files == "") $t_allow_share_files_exchange = "";
    if ($t_lock_user_options != "")
    {
      $t_allow_skin = "";
      #$t_allow_sound_usage = "";
      #$t_allow_email_notifier = "";
    }
    if ($t_lock_user_contact_list != "")
    {
      $t_allow_rating = "";
      $t_allow_invisible = "";
      $t_allow_change_contact_nickname = "";
    }
    if ($t_lock_user_profil != "")
    {
      $t_allow_change_avatar = "";
      $t_allow_change_email_phone = "";
      $t_allow_change_function_name = "";
    }
    //
    $t_allow_webcam = "";
    $t_force_skin_number = "";
    $t_futur_option_skin_1 = "";
    $t_futur_option_skin_2 = "";
    $t_futur_option_skin_3 = "";
    $t_futur_option_skin_4 = "";
    //
    $t_enterprise_server_force_action = "";
    if (_ENTERPRISE_SERVER == '') $t_enterprise_server_force_action = "";
    //
    // on renvoi les valeurs des options.
    echo ">F01#" . $t_force_username_to_pc_session_name . "#" . $t_user_need_password . "#" . _CLIENT_VERSION_MINI . "#" . $t_crypt_messages . "#";
    echo "1" . "#" . $t_force_away_on_screensaver  . "#" . $t_hide_col_function_name . "#" .$option_col_name_default_active . "#";
    echo $private . "#" . $t_allow_invisible . "#" . $t_allow_send_to_offline_user . "#" . $t_allow_change_contact_nickname . "#";
    echo $t_lock_user_contact_list . "#" . $t_log_messages . "#" . $t_lock_user_options . "#";
    echo $t_open_community . "#" . $t_group_community . "#"  . $opt_srv_display_options_list . "#";
    echo $t_check_new_msg_every . "#" . $t_minimum_username_length . "#" . $t_minimum_password_length . "#";
    echo $user_cannot_history_messages . "#" . $t_allow_conference . "#" . $authentication_extern_type . "#";
    echo $authentication_by_extern . "#" . $can_update_by_server . "#" . $must_update_by_server . "#" . $t_display_user_flag_country . "#";
    echo _SERVER_VERSION . "#" . $t_allow_change_email_phone . "#" . $t_allow_change_function_name . "#" . $t_allow_smileys . "#";
    echo $t_allow_change_avatar . "#" . $t_allow_use_proxy . "#" . $opt_srv_display_user_list. "#" . $opt_srv_can_propose_avatar . "#";
    echo $must_update_by_internet . "#" . $t_allow_email_notifier . "#" . $t_enterprise_server . "#" . $t_allow_rating . "#";
    echo $t_proxy_port_number . "#" . $t_proxy_address . "#" . $t_mx_nb_contact . "#" . $t_allow_space_nickname . "#" . $t_need_quick_register . "#";
    echo $t_pass_need_digit_and_letter . "#" . $t_pass_need_upper_and_lower . "#" . $t_pass_need_special_character . "#";
    echo $t_allow_webcam . "#" . $t_allow_shoutbox . "#" . $t_shoutbox_allow_vote . "#" . $t_group_for_shoutbox . "#" . $t_group_user_can_join . "#";
    echo $t_sbx_refresh_delay . "#" . $t_srv_list_status . "#" . $t_shoutbox_need_approval . "#" . $t_new_user_register_profil_after_start . "#";
    echo $t_wait_on_start_if_server_hs . "#" . $opt_srv_shoutbox_public . "#" . $t_bookmarks . "#" . $t_bookmarks_allow_vote . "#";
    echo $t_bookmarks_need_approval . "#" . $opt_srv_bookmarks_public . "#" . "#" . $t_lock_user_profil . "#" . $t_roles . "#";
    echo $t_force_launch_on_startup . "#" . $t_allow_skin . "#" . $t_force_skin_number . "#" . _SKIN_FORCED_COLOR_CUSTOM_VERSION . "#";
    echo $t_futur_option_skin_1 . "#" . $t_futur_option_skin_2 . "#" . $t_futur_option_skin_3 . "#" . $t_futur_option_skin_4 . "#";
    echo $t_allow_close_im . "#" . $t_allow_reduce_window_message . "#". $t_allow_reduce_main_screen . "#" . $t_allow_sound_usage . "#";
    echo $t_allow_postit . "#". $t_allow_share_files . "#" . $t_allow_share_files_exchange . "#";
    echo $t_share_files_approval . "#" . $t_share_files_exchange_approval . "#" . $t_share_files_vote . "#";
    echo $t_share_files_screenshot . "#" . $t_share_files_exchange_screenshot . "#" . $t_share_files_webcam . "#" . $t_share_files_exchange_webcam ."#";
    echo $t_share_files_compress . "#" . $t_share_files_protect . "#". $t_share_files_allow_uppercase . "#" . $t_share_files_no_accent . "#". "#";
    echo $t_allow_hidden_status . "#". $t_allow_history_export . "#";
    echo $t_allow_backup_files . "#". $t_backup_files_multi_folders . "#" . $t_backup_files_sub_folders . "#";
    echo $t_backup_files_force_every_at . "#" . "#";
    echo $t_force_options_from_server . "#" . $t_shoutbox_scrolling . "#" . $t_enterprise_server_force_action . "#";
    echo "#################" ;
  }
}
?>
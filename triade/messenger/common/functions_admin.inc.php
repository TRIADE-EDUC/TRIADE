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


function display_image_percent($num, $comment)
{
  $txt = $num;
  if ($comment != "") $txt = $comment . " : " . $num;
  if (intval($num) < 21) echo "<img src='" . _FOLDER_IMAGES . "z1.png' WIDTH='22' HEIGHT='18' alt='" . $txt . "%' title='" . $txt . "%'>";
  if ( (intval($num) >= 21) and (intval($num) < 41 ) ) echo "<img src='" . _FOLDER_IMAGES . "z2.png' WIDTH='22' HEIGHT='18' alt='" . $txt . "%' title='" . $txt . "%'>";
  if ( (intval($num) >= 41) and (intval($num) < 61 ) ) echo "<img src='" . _FOLDER_IMAGES . "z3.png' WIDTH='22' HEIGHT='18' alt='" . $txt . "%' title='" . $txt . "%'>";
  if ( (intval($num) >= 61) and (intval($num) < 81 ) ) echo "<img src='" . _FOLDER_IMAGES . "z4.png' WIDTH='22' HEIGHT='18' alt='" . $txt . "%' title='" . $txt . "%'>";
  if (intval($num) >= 81 ) echo "<img src='" . _FOLDER_IMAGES . "z5.png' WIDTH='22' HEIGHT='18' alt='" . $txt . "%' title='" . $txt . "%'>";
}


function display_image_rating($num)
{
  if (intval($num) == 1) echo "<img src='" . _FOLDER_IMAGES . "vote_1.png' WIDTH='70' HEIGHT='14' alt='1/5' title='1/5'>"; // <font color='red'> 
  if (intval($num) == 2) echo "<img src='" . _FOLDER_IMAGES . "vote_2.png' WIDTH='70' HEIGHT='14' alt='2/5' title='2/5'>"; // <font color='orange'> 
  if (intval($num) == 3) echo "<img src='" . _FOLDER_IMAGES . "vote_3.png' WIDTH='70' HEIGHT='14' alt='3/5' title='3/5'>"; // yellow // <font color='#DDDD00'> 
  if (intval($num) == 4) echo "<img src='" . _FOLDER_IMAGES . "vote_4.png' WIDTH='70' HEIGHT='14' alt='4/5' title='4/5'>"; // <font color='#00DD00'> 
  if (intval($num) == 5) echo "<img src='" . _FOLDER_IMAGES . "vote_5.png' WIDTH='70' HEIGHT='14' alt='5/5' title='5/5'>"; // <font color='green'> 
}


function f_os_name($win_os)
{
  $ret = "?";
  switch ($win_os)
  {
    case "6.2" :
      $ret = 'Windows 8';
      break;
    case "6.1" :
      $ret = 'Windows Seven';
      break;
    case "6.0" :
      $ret = 'Windows Vista';
      break;
    case "XP" :
      $ret = 'Windows XP';
      break;
    case "2000" :
    case "NT 5" :
    case "NT5" :
    case "NT" :
      $ret = 'Windows 2000';
      break;
    case "NT 4" :
      $ret = 'Windows NT 4';
      break;
    case "2003S" :
      $ret = 'Windows 2003';
      break;
    case "98" :
      $ret = 'Windows 98';
      break;
    case "95" :
      $ret = 'Windows 95';
      break;
    case "ME" :
      $ret = 'Windows Me';
      break;
    default :
      $ret = "?";
      break;
  }
  return $ret;
}


function display_os_picture($win_os)
{
  switch ($win_os)
  {
    case "6.2" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "windows8.png' WIDTH='16' HEIGHT='16' ALT='Windows 8' TITLE='Windows 8'>";
      break;
    case "6.1" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "winseven.gif' WIDTH='24' HEIGHT='13' ALT='Windows Seven' TITLE='Windows Seven'>";
      break;
    case "6.0" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "winvista.gif' WIDTH='36' HEIGHT='13' ALT='Windows Vista' TITLE='Windows Vista'>";
      break;
    case "XP" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "winxp.gif' WIDTH='26' HEIGHT='13' ALT='Windows XP' TITLE='Windows XP'>";
      break;
    case "2000" :
    case "NT 5" :
    case "NT5" :
    case "NT" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "win2000.gif' WIDTH='35' HEIGHT='13' ALT='Windows 2000' TITLE='Windows 2000'>";
      break;
    case "NT 4" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "winnt4.gif' WIDTH='35' HEIGHT='13' ALT='Windows NT 4' TITLE='Windows NT 4'>";
      break;
    case "2003S" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "win2003.gif' WIDTH='35' HEIGHT='13' ALT='Windows 2003' TITLE='Windows 2003'>";
      break;
    case "98" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "win98.gif' WIDTH='27' HEIGHT='13' ALT='Windows 98' TITLE='Windows 98'>";
      break;
    case "95" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "win95.gif' WIDTH='27' HEIGHT='13' ALT='Windows 95' TITLE='Windows 95'>";
      break;
    case "ME" :
      echo "<IMG SRC='" . _FOLDER_IMAGES . "winme.gif' WIDTH='29' HEIGHT='13' ALT='Windows Me' TITLE='Windows Me'>";
      break;
    default :
      echo "&nbsp;";
      break;
  }
}


function display_browser_picture($browser)
{
  if (strstr($browser, "Firefox"))            echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_firefox.gif' WIDTH='16' HEIGHT='16' ALT='Mozilla Firefox' TITLE='Mozilla Firefox'>";
  if (strstr($browser, "Internet Explorer"))  echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_ie.gif' WIDTH='16' HEIGHT='16' ALT='Internet Explorer' TITLE='Internet Explorer'>";
  if (strstr($browser, "I.E."))               echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_ie.gif' WIDTH='16' HEIGHT='16' ALT='Internet Explorer' TITLE='Internet Explorer'>";
  if (strstr($browser, "Opera"))              echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_opera.gif' WIDTH='16' HEIGHT='16' ALT='Opera' TITLE='Opera'>";
  if (strstr($browser, "Chrome"))             echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_chrome.gif' WIDTH='16' HEIGHT='16' ALT='Google Chrome' TITLE='Google Chrome'>";
  if (strstr($browser, "Safari"))             echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_safari.gif' WIDTH='16' HEIGHT='16' ALT='Safari' TITLE='Safari'>";
  if (strstr($browser, "Netscape"))           echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_netscape.gif' WIDTH='16' HEIGHT='16' ALT='Netscape' TITLE='Netscape'>";
  if (strstr($browser, "K-Meleon"))           echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_kmeleon.png' WIDTH='16' HEIGHT='16' ALT='K-Meleon' TITLE='K-Meleon'>";
  if (strstr($browser, "MSN Explorer"))       echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "browser_msn.png' WIDTH='16' HEIGHT='16' ALT='MSN Explorer' TITLE='MSN Explorer'>";
}


function f_reduce_browser_name($browser)
{
  $browser = str_replace("Mozilla", "", $browser);
  //
  return trim($browser);
}


function f_reduce_emailclient_name($emailclient)
{
  $emailclient = str_replace("Mozilla", "", $emailclient);
  $emailclient = str_replace("Microsoft", "", $emailclient);
  //
  return trim($emailclient);
}


function f_img_percent($value)
{
  $img = "sidebar_0.png";
  if ($value > 1) $img = "sidebar_1.png";
  if ($value > 6) $img = "sidebar_2.png"; // 11%
  if ($value > 16) $img = "sidebar_3.png"; // 20%
  if ($value > 27) $img = "sidebar_4.png"; // 29%
  if ($value > 31) $img = "sidebar_5.png"; // 35%
  if ($value > 44) $img = "sidebar_6.png"; // 48%
  if ($value > 54) $img = "sidebar_7.png"; // 58.7 %
  if ($value > 63) $img = "sidebar_8.png"; // 67.8 %
  if ($value > 74) $img = "sidebar_9.png"; // 78%
  if ($value > 86) $img = "sidebar_10.png"; // 90.9 %
  if ($value > 98) $img = "sidebar_11.png";
  //
  return $img;
}


function f_old_distant_files()
{
  $f = array();
  $f[] = "avatar_list.php";
  $f[] = "avatar_update.php";
  $f[] = "chang_pass_user.php";
  $f[] = "chang_pseudo_user.php";
  $f[] = "conference_accept.php";
  $f[] = "conference_invite.php";
  $f[] = "conference_list_user.php";
  $f[] = "conference_msg_send.php";
  $f[] = "conference_quit.php";
  $f[] = "contact_user_ask_add.php";
  $f[] = "contact_user_confirme.php";
  $f[] = "contact_user_delete.php";
  $f[] = "contact_user_delete_wait.php";
  $f[] = "contact_user_group.php";
  $f[] = "contact_user_infos.php";
  $f[] = "contact_user_mask.php";
  $f[] = "contact_user_privilege.php";
  $f[] = "contact_user_pseudo.php";
  $f[] = "contact_user_reject.php";
  $f[] = "leave_server.php";
  $f[] = "list_contact_of_user.php";
  $f[] = "list_contact_online_offline.php";
  $f[] = "list_contact_online_only.php";
  $f[] = "list_contact_user_to_confirm.php";
  $f[] = "list_users.php";
  $f[] = "msg_get.php";
  $f[] = "msg_list_contact.php";
  $f[] = "msg_nb.php";
  $f[] = "msg_send.php";
  $f[] = "phenix_today.php";
  $f[] = "phenix_triade_today.php";
  $f[] = "stop.php";
  $f[] = "user_infos_list.php";
  $f[] = "user_infos_update.php";
  $f[] = "im_annu.php";
  $f[] = "status.php";
  #$f[] = "get_options.php";
  $f[] = "get_options_2.php";
  $f[] = "sql_test.php";
  $f[] = "start.php";
  //
  return $f;
}


function f_option_label($option)
{
  GLOBAL $l_admin_options_maintenance_mode, $l_admin_options_nb_max_user, $l_admin_options_nb_max_session, $l_admin_options_nb_max_contact_by_user,
  $l_admin_options_max_simultaneous_ip_addresses, $l_admin_options_del_user_after_x_days_not_use, $l_admin_options_check_new_msg_every,
  $l_admin_options_full_check, $l_admin_options_allow_use_proxy, $l_admin_options_proxy_port_number, $l_admin_options_proxy_address,
  $l_admin_options_admin_email, $l_admin_options_admin_phone, $l_admin_options_scroll_text, $l_admin_options_pass_register_book,
  $l_admin_options_info_book, $l_admin_option_allow_conference, $l_admin_options_allow_invisible, $l_admin_options_allow_smiley,
  $l_admin_options_can_change_contact_nickname, $l_admin_options_allow_change_email_phone, $l_admin_options_allow_change_function_name,
  $l_admin_options_allow_change_avatar, $l_admin_option_send_offline, $l_admin_options_user_history_messages, $l_admin_options_allow_rating,
  $l_admin_options_uppercase_space_nickname, $l_admin_options_allow_email_notifier, $l_admin_options_force_email_server,
  $l_admin_options_force_away, $l_admin_options_col_name_hide, $l_admin_options_allow_change_contact_list, $l_admin_options_allow_change_options,
  $l_admin_options_allow_change_profile, $l_admin_options_public_folder, $l_admin_options_minimum_length_of_username, $l_admin_options_password_user,
  $l_admin_options_minimum_length_of_password, $l_admin_options_max_pwd_error_lock, $l_admin_options_lock_duration,
  $l_admin_options_pass_need_digit_and_letter, $l_admin_options_pass_need_upper_and_lower, $l_admin_options_pass_need_special_character,
  $l_admin_options_crypt_msg, $l_admin_options_censor_messages, $l_admin_options_log_messages, $l_admin_options_password_for_private_server,
  $l_admin_options_is_usernamePC, $l_admin_options_auto_add_user, $l_admin_options_quick_register, $l_admin_options_need_admin_after_add,
  $l_admin_options_need_admin_if_chang_check, $l_admin_options_shoutbox_title_long, $l_admin_options_shoutbox_title_short, $l_admin_options_shoutbox_refresh_delay,
  $l_admin_options_shoutbox_store_max, $l_admin_options_shoutbox_store_days, $l_admin_options_shoutbox_day_user_quota,
  $l_admin_options_shoutbox_week_user_quota, $l_admin_options_shoutbox_need_approval, $l_admin_options_shoutbox_approval_queue,
  $l_admin_options_shoutbox_approval_queue_user, $l_admin_options_shoutbox_lock_user_approval, $l_admin_options_shoutbox_can_vote,
  $l_admin_options_shoutbox_day_votes_quota, $l_admin_options_shoutbox_week_votes_quota, $l_admin_options_shoutbox_remove_msg_votes,
  $l_admin_options_shoutbox_lock_user_votes, $l_admin_options_shoutbox_public, $l_admin_options_bookmarks, $l_admin_options_bookmarks_can_vote,
  $l_admin_options_bookmarks_public, $l_admin_options_bookmarks_need_approval, $l_admin_options_site_url, $l_admin_options_site_title,
  $l_admin_options_groupcommunity, $l_admin_options_opencommunity, $l_admin_authentication_extern, $l_admin_extern_url_to_register,
  $l_admin_extern_url_password_forget, $l_admin_extern_url_change_password, $l_admin_options_site_title, $l_admin_options_group_for_sbx_and_admin_messages,
  $l_admin_options_group_user_can_join, $l_admin_options_enterprise_server, $l_admin_options_hierachic_management, $l_admin_options_servers_status,
  $l_admin_options_force_update_by_server, $l_admin_options_force_update_by_internet, $l_admin_options_public_see_users,
  $l_admin_options_public_upload_avatar, $l_admin_options_public_see_options, $l_admin_options_flag_country, $l_admin_options_profile_first_register,
  $l_admin_options_time_zones, $l_admin_options_log_session_open, $l_admin_options_statistics, $l_admin_options_check_version_internet,
  $l_admin_options_unread_message_validity, $l_admin_options_roles_to_override_permissions, $l_admin_role_get_admin_alert,
  $l_admin_role_send_alert_to_admin, $l_admin_role_broadcast_alert_to_group, $l_admin_role_broadcast_alert,
  $l_admin_options_wait_startup_if_server_hs, $l_admin_options_opengroupcommunity, $l_admin_options_allow_skin, $l_admin_options_allow_close_im, 
  $l_admin_options_allow_sound_usage, $l_admin_options_allow_reduce_main_screen, $l_admin_options_allow_reduce_message_screen,
  $l_admin_options_send_admin_alert_by_email, $l_admin_options_password_validity, $l_admin_options_allow_postit,
  $l_admin_options_share_files, $l_admin_options_share_files_ftp_address, $l_admin_options_share_files_ftp_login, 
  $l_admin_options_share_files_ftp_password, $l_admin_options_share_files_ftp_port_number, $l_admin_options_share_files_max_file_size,
  $l_admin_options_share_files_max_nb_files_total, $l_admin_options_share_files_max_nb_files_user, $l_admin_options_share_files_max_space_size_total,
  $l_admin_options_share_files_max_space_size_user, $l_admin_options_share_files_need_approval, $l_admin_options_share_files_approval_queue,
  $l_admin_options_share_files_quota_files_user_week, $l_admin_options_share_files_trash, $l_admin_options_share_files_exchange,
  $l_admin_options_share_files_exchange_need_approval, $l_admin_options_share_files_exchange_trash, $l_admin_options_share_files_read_only,
  $l_admin_options_share_files_allow, $l_admin_options_share_files_can_vote,
  $l_admin_options_share_files_trash, $l_admin_options_share_files_exchange_trash, 
  $l_admin_options_share_files_download_quota_day, $l_admin_options_share_files_download_quota_week, 
  $l_admin_options_share_files_download_quota_month, $l_admin_options_share_files_download_quota_mb_day, 
  $l_admin_options_share_files_download_quota_mb_week, $l_admin_options_share_files_download_quota_mb_month,
  $l_admin_role_offline_mode, $l_admin_options_hidden_status, $l_admin_role_change_server_status,
  $l_admin_options_user_history_messages_export, 
  $l_admin_options_backup_files, $l_admin_options_backup_files_allow, $l_admin_options_backup_files_max_file_size,
  $l_admin_options_backup_files_this_local_folder, $l_admin_options_backup_files_multi_folders,
  $l_admin_options_backup_files_sub_folders, $l_admin_options_backup_files_max_nb_backup_user,
  $l_admin_options_backup_files_max_space_size_user;
  //
  $label = "";
  if (substr($option, 0 , 1) <> "_") $option = "_" . $option;
  //
  if ($option == "_MAINTENANCE_MODE") $label = $l_admin_options_maintenance_mode;
  if ($option == "_MAX_NB_USER") $label = $l_admin_options_nb_max_user;
  if ($option == "_MAX_NB_SESSION") $label = $l_admin_options_nb_max_session;
  if ($option == "_MAX_NB_CONTACT_BY_USER") $label = $l_admin_options_nb_max_contact_by_user;
  if ($option == "_MAX_NB_IP") $label = $l_admin_options_max_simultaneous_ip_addresses;
  if ($option == "_OUTOFDATE_AFTER_NOT_USE_DURATION") $label = $l_admin_options_del_user_after_x_days_not_use;
  if ($option == "_CHECK_NEW_MSG_EVERY") $label = $l_admin_options_check_new_msg_every;
  if ($option == "_SLOW_NOTIFY") $label = $l_admin_options_full_check;
  if ($option == "_ALLOW_USE_PROXY") $label = $l_admin_options_allow_use_proxy;
  if ($option == "_PROXY_PORT_NUMBER") $label = $l_admin_options_proxy_port_number;
  if ($option == "_PROXY_ADDRESS") $label = $l_admin_options_proxy_address;
  if ($option == "_ADMIN_EMAIL") $label = $l_admin_options_admin_email;
  if ($option == "_ADMIN_PHONE") $label = $l_admin_options_admin_phone;
  if ($option == "_SCROLL_TEXT") $label = $l_admin_options_scroll_text;
  if ($option == "_IM_ADDRESS_BOOK_PASSWORD") $label = $l_admin_options_pass_register_book . " " . $l_admin_options_info_book;
  if ($option == "_ALLOW_CONFERENCE") $label = $l_admin_option_allow_conference;
  if ($option == "_ALLOW_HIDDEN_TO_CONTACTS") $label = $l_admin_options_allow_invisible;
  if ($option == "_ALLOW_SMILEYS") $label = $l_admin_options_allow_smiley;
  if ($option == "_ALLOW_CHANGE_CONTACT_NICKNAME") $label = $l_admin_options_can_change_contact_nickname;
  if ($option == "_ALLOW_CHANGE_EMAIL_PHONE") $label = $l_admin_options_allow_change_email_phone;
  if ($option == "_ALLOW_CHANGE_FUNCTION_NAME") $label = $l_admin_options_allow_change_function_name;
  if ($option == "_ALLOW_CHANGE_AVATAR") $label = $l_admin_options_allow_change_avatar;
  if ($option == "_ALLOW_SEND_TO_OFFLINE_USER") $label = $l_admin_option_send_offline;
  if ($option == "_ALLOW_HISTORY_MESSAGES") $label = $l_admin_options_user_history_messages;
  if ($option == "_ALLOW_CONTACT_RATING") $label = $l_admin_options_allow_rating;
  if ($option == "_ALLOW_UPPERCASE_SPACE_USERNAME") $label = $l_admin_options_uppercase_space_nickname;
  if ($option == "_ALLOW_EMAIL_NOTIFIER") $label = $l_admin_options_allow_email_notifier;
  if ($option == "_INCOMING_EMAIL_SERVER_ADDRESS") $label = $l_admin_options_force_email_server;
  if ($option == "_FORCE_AWAY_ON_SCREENSAVER") $label = $l_admin_options_force_away;
  #if ($option == "_HIDE_COL_FUNCTION_NAME") $label = $l_admin_options_col_name_hide;  -> _ALLOW_COL_FUNCTION_NAME
  //if ($option == "_LOCK_USER_CONTACT_LIST") $label = $l_admin_options_lock_contact_list;
  //if ($option == "_LOCK_USER_OPTIONS") $label = $l_admin_options_lock_options;
  //if ($option == "_LOCK_USER_PROFILE") $label = $l_admin_options_lock_profile;
  if ($option == "_ALLOW_MANAGE_CONTACT_LIST") $label = $l_admin_options_allow_change_contact_list;
  if ($option == "_ALLOW_MANAGE_OPTIONS") $label = $l_admin_options_allow_change_options;
  if ($option == "_ALLOW_MANAGE_PROFILE") $label = $l_admin_options_allow_change_profile;
  if ($option == "_PUBLIC_FOLDER") $label = $l_admin_options_public_folder;
  if ($option == "_MINIMUM_USERNAME_LENGTH") $label = $l_admin_options_minimum_length_of_username;
  if ($option == "_USER_NEED_PASSWORD") $label = $l_admin_options_password_user;
  if ($option == "_MINIMUM_PASSWORD_LENGTH") $label = $l_admin_options_minimum_length_of_password;
  if ($option == "_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER") $label = $l_admin_options_max_pwd_error_lock;
  if ($option == "_LOCK_DURATION") $label = $l_admin_options_lock_duration;
  if ($option == "_PWD_NEED_DIGIT_LETTER") $label = $l_admin_options_pass_need_digit_and_letter;
  if ($option == "_PWD_NEED_UPPER_LOWER") $label = $l_admin_options_pass_need_upper_and_lower;
  if ($option == "_PWD_NEED_SPECIAL_CHARACTER") $label = $l_admin_options_pass_need_special_character;
  if ($option == "_CRYPT_MESSAGES") $label = $l_admin_options_crypt_msg;
  if ($option == "_CENSOR_MESSAGES") $label = $l_admin_options_censor_messages;
  if ($option == "_HISTORY_MESSAGES_ON_ACP") $label = $l_admin_options_log_messages;
  if ($option == "_PASSWORD_FOR_PRIVATE_SERVER") $label = $l_admin_options_password_for_private_server;
  if ($option == "_FORCE_USERNAME_TO_PC_SESSION_NAME") $label = $l_admin_options_is_usernamePC;
  if ($option == "_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER") $label = $l_admin_options_auto_add_user;
  if ($option == "_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER") $label = $l_admin_options_quick_register;
  if ($option == "_PENDING_NEW_AUTO_ADDED_USER") $label = $l_admin_options_need_admin_after_add;
  if ($option == "_PENDING_USER_ON_COMPUTER_CHANGE") $label = $l_admin_options_need_admin_if_chang_check;
  if ($option == "_SHOUTBOX") $label = $l_admin_options_shoutbox_title_long;
  if ($option == "_SHOUTBOX_REFRESH_DELAY") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_refresh_delay;
  if ($option == "_SHOUTBOX_STORE_MAX") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_store_max;
  if ($option == "_SHOUTBOX_STORE_DAYS") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_store_days;
  if ($option == "_SHOUTBOX_QUOTA_USER_DAY") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_day_user_quota;
  if ($option == "_SHOUTBOX_QUOTA_USER_WEEK") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_week_user_quota;
  if ($option == "_SHOUTBOX_NEED_APPROVAL") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_need_approval;
  if ($option == "_SHOUTBOX_APPROVAL_QUEUE") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_approval_queue;
  if ($option == "_SHOUTBOX_APPROVAL_QUEUE_USER") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_approval_queue_user;
  if ($option == "_SHOUTBOX_LOCK_USER_APPROVAL") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_lock_user_approval;
  if ($option == "_SHOUTBOX_VOTE") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_can_vote;
  if ($option == "_SHOUTBOX_MAX_NOTES_USER_DAY") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_day_votes_quota;
  if ($option == "_SHOUTBOX_MAX_NOTES_USER_WEEK") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_week_votes_quota;
  if ($option == "_SHOUTBOX_REMOVE_MESSAGE_VOTES") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_remove_msg_votes;
  if ($option == "_SHOUTBOX_LOCK_USER_VOTES") $label = $l_admin_options_shoutbox_title_short . " : " . $l_admin_options_shoutbox_lock_user_votes;
  if ($option == "_SHOUTBOX_PUBLIC") $label = $l_admin_options_shoutbox_public;
  //if ($option == "_SHOUTBOX_ALLOW_SCROLLING") $label = $xxxxxxxxxxxxxx;
  if ($option == "_BOOKMARKS") $label = $l_admin_options_bookmarks;
  if ($option == "_BOOKMARKS_VOTE") $label = $l_admin_options_bookmarks_can_vote;
  if ($option == "_BOOKMARKS_PUBLIC") $label = $l_admin_options_bookmarks_public . " + RSS";
  if ($option == "_BOOKMARKS_NEED_APPROVAL") $label = $l_admin_options_bookmarks_need_approval;
  if ($option == "_SITE_URL_TO_SHOW") $label = $l_admin_options_site_url;
  if ($option == "_SITE_TITLE_TO_SHOW") $label = $l_admin_options_site_title;
  if ($option == "_SPECIAL_MODE_GROUP_COMMUNITY") $label = $l_admin_options_groupcommunity;
  if ($option == "_SPECIAL_MODE_OPEN_COMMUNITY") $label = $l_admin_options_opencommunity;
  if ($option == "_EXTERNAL_AUTHENTICATION") $label = $l_admin_authentication_extern;
  if ($option == "_EXTERN_URL_TO_REGISTER") $label = $l_admin_extern_url_to_register;
  if ($option == "_EXTERN_URL_FORGET_PASSWORD") $label = $l_admin_extern_url_password_forget;
  if ($option == "_EXTERN_URL_CHANGE_PASSWORD") $label = $l_admin_extern_url_change_password;
  if ($option == "_SITE_TITLE") $label = $l_admin_options_site_title;
  if ($option == "_GROUP_FOR_SBX_AND_ADMIN_MSG") $label = $l_admin_options_group_for_sbx_and_admin_messages;
  if ($option == "_GROUP_USER_CAN_JOIN") $label = $l_admin_options_group_user_can_join;
  if ($option == "_ENTERPRISE_SERVER") $label = $l_admin_options_enterprise_server;
  if ($option == "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN") $label = $l_admin_options_hierachic_management;
  if ($option == "_SERVERS_STATUS") $label = $l_admin_options_servers_status;
  if ($option == "_FORCE_UPDATE_BY_SERVER") $label = $l_admin_options_force_update_by_server;
  if ($option == "_FORCE_UPDATE_BY_INTERNET") $label = $l_admin_options_force_update_by_internet;
  if ($option == "_PUBLIC_USERS_LIST") $label = $l_admin_options_public_see_users;
  if ($option == "_PUBLIC_POST_AVATAR") $label = $l_admin_options_public_upload_avatar;
  if ($option == "_PUBLIC_OPTIONS_LIST") $label = $l_admin_options_public_see_options;
  if ($option == "_FLAG_COUNTRY_FROM_IP") $label = $l_admin_options_flag_country;
  if ($option == "_INVITE_FILL_PROFILE_ON_FIRST_LOGIN") $label = $l_admin_options_profile_first_register;
  if ($option == "_TIME_ZONES") $label = $l_admin_options_time_zones;
  if ($option == "_LOG_SESSION_OPEN") $label = $l_admin_options_log_session_open;
  if ($option == "_STATISTICS") $label = $l_admin_options_statistics;
  if ($option == "_CHECK_VERSION_INTERNET") $label = $l_admin_options_check_version_internet;
  if ($option == "_UNREAD_MESSAGE_VALIDITY") $label = $l_admin_options_unread_message_validity;
  if ($option == "_ROLES_TO_OVERRIDE_PERMISSIONS") $label = $l_admin_options_roles_to_override_permissions;
  if ($option == "_WAIT_STARTUP_IF_SERVER_UNAVAILABLE") $label = $l_admin_options_wait_startup_if_server_hs;
  //if ($option == "_ONLINE_REASONS_LIST") $label = $xxxxxxx;
  //if ($option == "_BUSY_REASONS_LIST") $label = $xxxxxxx;
  //if ($option == "_DONOTDISTURB_REASONS_LIST") $label = $xxxxxxx;
  if ($option == "_SPECIAL_MODE_OPEN_GROUP_COMMUNITY") $label = $l_admin_options_opengroupcommunity;
  //if ($option == "_FORCE_LAUNCH_ON_STARTUP") $label = $xxxxxxx;
  if ($option == "_ALLOW_SKIN") $label = $l_admin_options_allow_skin;
  if ($option == "_ALLOW_CLOSE_IM") $label = $l_admin_options_allow_close_im;
  if ($option == "_ALLOW_SOUND_USAGE") $label = $l_admin_options_allow_sound_usage;
  if ($option == "_ALLOW_REDUCE_MAIN_SCREEN") $label = $l_admin_options_allow_reduce_main_screen;
  if ($option == "_ALLOW_REDUCE_MESSAGE_SCREEN") $label = $l_admin_options_allow_reduce_message_screen;
  //if ($option == "_SKIN_FORCED_COLOR_CUSTOM_VERSION") $label = $xxxxxxx;
  if ($option == "_SEND_ADMIN_ALERT_EMAIL") $label = $l_admin_options_send_admin_alert_by_email;
  //if ($option == "_AUTO_ADD_CONTACT_USER_ID") $label = $xxxxxxx;
  if ($option == "_PASSWORD_VALIDITY") $label = $l_admin_options_password_validity;
  if ($option == "_ALLOW_POST_IT") $label = $l_admin_options_allow_postit;
  if ($option == "_SHARE_FILES") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_allow;
  //if ($option == "_SHARE_FILES_FTP_ADDRESS") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_ftp_address;
  //if ($option == "_SHARE_FILES_FTP_LOGIN") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_ftp_login;
  //if ($option == "_SHARE_FILES_FTP_PASSWORD") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_ftp_password;
  //if ($option == "_SHARE_FILES_FTP_PASSWORD_CRYPT") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_ftp_password;
  //if ($option == "_SHARE_FILES_FTP_PORT_NUMBER") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_ftp_port_number;
  if ($option == "_SHARE_FILES_MAX_FILE_SIZE") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_max_file_size;
  //if ($option == "_SHARE_FILES_MAX_NB_FILES_TOTAL") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_max_nb_files_total;
  if ($option == "_SHARE_FILES_MAX_NB_FILES_USER") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_max_nb_files_user;
  //if ($option == "_SHARE_FILES_MAX_SPACE_SIZE_TOTAL") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_max_space_size_total;
  if ($option == "_SHARE_FILES_MAX_SPACE_SIZE_USER") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_max_space_size_user;
  if ($option == "_SHARE_FILES_NEED_APPROVAL") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_need_approval;
  //if ($option == "_SHARE_FILES_APPROVAL_QUEUE") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_approval_queue;
  if ($option == "_SHARE_FILES_QUOTA_FILES_USER_WEEK") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_quota_files_user_week;
  if ($option == "_SHARE_FILES_EXCHANGE") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_exchange;
  if ($option == "_SHARE_FILES_EXCHANGE_NEED_APPROVAL") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_exchange_need_approval;
  if ($option == "_SHARE_FILES_VOTE") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_can_vote;
  if ($option == "_SHARE_FILES_TRASH") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_trash;
  if ($option == "_SHARE_FILES_EXCHANGE_TRASH") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_exchange_trash;
  //if ($option == "_SHARE_FILES_COMPRESS") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_compress;
  //if ($option == "_SHARE_FILES_FOLDER") 
  if ($option == "_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_download_quota_day;
  if ($option == "_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_download_quota_week;
  if ($option == "_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_download_quota_month;
  if ($option == "_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_download_quota_mb_day;
  if ($option == "_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_download_quota_mb_week;
  if ($option == "_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH") $label = $l_admin_options_share_files . " : " . $l_admin_options_share_files_download_quota_mb_month;
  //if ($option == "_SHARE_FILES_PROTECT") $label = $xxxxxxx;
  //if ($option == "_SHARE_FILES_ALLOW_ACCENT") $label = $xxxxxxx;
  //if ($option == "_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN") $label = $xxxxxxx;
  if ($option == "_ALLOW_HIDDEN_STATUS") $label = $l_admin_options_hidden_status;
  //if ($option == "_ROLE_ID_DEFAULT_FOR_NEW_USER") $label = $xxxxxxx;
  //if ($option == "_ACP_PROTECT_BY_HTACCESS") $label = $xxxxxxx;
  //if ($option == "_ACP_ALLOW_MEMORY_AUTH") $label = $xxxxxxx;
  if ($option == "_ALLOW_HISTORY_MESSAGES_EXPORT") $label = $l_admin_options_user_history_messages_export;
  if ($option == "_BACKUP_FILES") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_allow;
  if ($option == "_BACKUP_FILES_MAX_NB_ARCHIVES_USER") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_max_nb_backup_user;
  if ($option == "_BACKUP_FILES_MAX_ARCHIVE_SIZE") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_max_file_size;
  if ($option == "_BACKUP_FILES_MAX_SPACE_SIZE_USER") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_max_space_size_user;
  if ($option == "_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_this_local_folder;
  if ($option == "_BACKUP_FILES_ALLOW_MULTI_FOLDERS") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_multi_folders;
  if ($option == "_BACKUP_FILES_ALLOW_SUB_FOLDERS") $label = $l_admin_options_backup_files . " : " . $l_admin_options_backup_files_sub_folders;
  //if ($option == "_BACKUP_FILES_FORCE_EVERY_DAY_AT") $label = $xxxxxxx;
  
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //
  //
  // Roles (pas des options !) :
  if ($option == "_ROLE_GET_ADMIN_ALERT_MESSAGES") $label = $l_admin_role_get_admin_alert;
  if ($option == "_ROLE_SEND_ALERT_TO_ADMIN") $label = $l_admin_role_send_alert_to_admin;
  if ($option == "_ROLE_BROADCAST_ALERT_TO_GROUP") $label = $l_admin_role_broadcast_alert_to_group;
  if ($option == "_ROLE_BROADCAST_ALERT") $label = $l_admin_role_broadcast_alert;
  if ($option == "_ROLE_SHARE_FILES_READ_ONLY") $label = $l_admin_options_share_files_read_only;
  if ($option == "_ROLE_OFFLINE_MODE") $label = $l_admin_role_offline_mode;
  if ($option == "_ROLE_CHANGE_SERVER_STATUS") $label = $l_admin_role_change_server_status;
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //if ($option == "") $label = $xxxxxxx;
  //
  return $label;
}
?>
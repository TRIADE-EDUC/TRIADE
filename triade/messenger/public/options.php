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
require ("../common/display_errors.inc.php"); 
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
//
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_title . "</title>";
include ("../common/menu.inc.php"); // important !
display_header();
if (_PUBLIC_OPTIONS_LIST == "") // not allowed to display...
{
  echo '<META http-equiv="refresh" content="0;url=../"> ';
  die();
}
echo '<META http-equiv="refresh" content="400;url="> ';
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//
//
$authentication_by_extern = "";
$authentication_extern_type = "";
if (f_nb_auth_extern() == 1) 
{
  $authentication_by_extern = "X";
  $authentication_extern_type = f_type_auth_extern();
}

//
$c_missing = "Missing !";
//
$si_not_ok = "OK";
//
// Afficher une ligne d'option.
function display_row($var1, $var2, $comment, $lan, $wan)  
{
	GLOBAL $si_not_ok, $c_missing;
	GLOBAL $l_admin_options_legende_not_empty, $l_admin_options_legende_empty, $l_admin_options_legende_up2u, $l_admin_options_title_2;
	$var1 = trim($var1);
	$info_is_on = $l_admin_options_legende_not_empty; // "On : " . 
	$info_is_off = $l_admin_options_legende_empty; // "Off : " . 
	echo "<TR>";
	/*
	echo "<TD class='row3'>";
	echo "<font face='verdana' size='1' color='gray'>&nbsp;" . $var2 . "&nbsp;</font>";
	echo "</TD>";
	*/
	echo "<TD align='CENTER' class='row1'>";
	echo "<font face='verdana' size='2'>";
	if ($var1 != $c_missing)
	{
		if ($var1 == "")
				echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif' WIDTH='18' HEIGHT='18' ALT='" . $info_is_off . "' TITLE='" . $info_is_off . "'>";
		if ( (intval($var1) > 0) or ($var1 == "0") or (strlen($var1) > 2) )
		{
			if ( ($var2 != "_EXTERN_URL_TO_REGISTER") and ($var2 != "_EXTERN_URL_FORGET_PASSWORD") and ($var2 != "_EXTERN_URL_CHANGE_PASSWORD") )
				echo $var1;
			else
        echo "<A HREF='" . $var1 . "' TITLE='" . $var1 . "' target='_blank'>URL</A>";
		}
		else
			if ($var1 != "") 
				echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='18' HEIGHT='18' ALT='" . $info_is_on . "' TITLE='" . $info_is_on . "'>";
	}
	else
	{
		echo "<FONT color='RED'><B>" . $var1 . "</B></FONT>";
		$si_not_ok = "KO";
	}
	echo "</TD>";

	if ($comment == '')
	{
		echo "<TD class='row2'>";
		echo " &nbsp; ";
	}
	else
	{
		echo "<TD align='LEFT' class='row3'>";
		echo "<font face='verdana' size='2'>&nbsp;" . $comment . "</font>";
	}
	//
	echo "</TR>";
}
//
echo "<CENTER>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
if ($lang != 'FR') echo " <A HREF='?lang=FR&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'EN') echo " <A HREF='?lang=EN&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'IT') echo " <A HREF='?lang=IT&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'ES') echo " <A HREF='?lang=ES&' TITLE='Spanish'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'PT') echo " <A HREF='?lang=PT&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'BR') echo " <A HREF='?lang=BR&' TITLE='Portuguese'><IMG SRC='../images/flags/br.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'DE') echo " <A HREF='?lang=DE&' TITLE='German'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'RO') echo " <A HREF='?lang=RO&' TITLE='Romana'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'NL') echo " <A HREF='?lang=NL&' TITLE='Netherlands'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
echo "<BR/>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
//
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
	echo "<TH align=center COLSPAN='3' class='thHead'>";
	echo "<font face='verdana' size=3><b>" . $l_admin_options_title . "</b></font></TH>";
echo "</TR>";
echo "<TR>";
	//display_row_table($l_admin_options_col_option, '');
	display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
	display_row_table($l_admin_options_col_comment, '');
echo "</TR>";


display_row(_MAX_NB_USER, "_MAX_NB_USER", $l_admin_options_nb_max_user, "", "");
display_row(_MAX_NB_SESSION, "_MAX_NB_SESSION", $l_admin_options_nb_max_session, "", "");
display_row(_MAX_NB_CONTACT_BY_USER, "_MAX_NB_CONTACT_BY_USER", $l_admin_options_nb_max_contact_by_user, "", "");
display_row(_MAX_NB_IP, "_MAX_NB_IP", $l_admin_options_max_simultaneous_ip_addresses, "", "");
display_row(_OUTOFDATE_AFTER_NOT_USE_DURATION, "_OUTOFDATE_AFTER_NOT_USE_DURATION", $l_admin_options_del_user_after_x_days_not_use, "80", "50");
display_row(_ALLOW_CONFERENCE, "_ALLOW_CONFERENCE", $l_admin_option_allow_conference, "X", "");
display_row(_ALLOW_HIDDEN_TO_CONTACTS, "_ALLOW_HIDDEN_TO_CONTACTS", $l_admin_options_allow_invisible, "", "");
display_row(_ALLOW_SMILEYS, "_ALLOW_SMILEYS", $l_admin_options_allow_smiley, "X", "X");
display_row(_ALLOW_CHANGE_CONTACT_NICKNAME, "_ALLOW_CHANGE_CONTACT_NICKNAME", $l_admin_options_can_change_contact_nickname, "X", "X");
display_row(_ALLOW_CHANGE_EMAIL_PHONE, "_ALLOW_CHANGE_EMAIL_PHONE", $l_admin_options_allow_change_email_phone, "X", "X");
display_row(_ALLOW_CHANGE_FUNCTION_NAME, "_ALLOW_CHANGE_FUNCTION_NAME", $l_admin_options_allow_change_function_name, "X", "X");
display_row(_ALLOW_CHANGE_AVATAR, "_ALLOW_CHANGE_AVATAR", $l_admin_options_allow_change_avatar, "X", "X");
display_row(_ALLOW_SEND_TO_OFFLINE_USER, "_ALLOW_SEND_TO_OFFLINE_USER", $l_admin_option_send_offline, "X", "");
display_row(_ALLOW_EMAIL_NOTIFIER, "_ALLOW_EMAIL_NOTIFIER", $l_admin_options_allow_email_notifier, "", "X");
display_row(_ALLOW_SKIN, "_ALLOW_SKIN", $l_admin_options_allow_skin, "X", "X");
display_row(_ALLOW_POST_IT, "_ALLOW_POST_IT", $l_admin_options_allow_postit, "", "");
display_row(_ALLOW_CLOSE_IM, "_ALLOW_CLOSE_IM", $l_admin_options_allow_close_im, "X", "X");
display_row(_ALLOW_SOUND_USAGE, "_ALLOW_SOUND_USAGE", $l_admin_options_allow_sound_usage, "X", "X");
display_row(_ALLOW_REDUCE_MAIN_SCREEN, "_ALLOW_REDUCE_MAIN_SCREEN", $l_admin_options_allow_reduce_main_screen, "X", "X");
display_row(_ALLOW_REDUCE_MESSAGE_SCREEN, "_ALLOW_REDUCE_MESSAGE_SCREEN", $l_admin_options_allow_reduce_message_screen, "X", "X");
display_row(_ALLOW_CONTACT_RATING, "_ALLOW_CONTACT_RATING", $l_admin_options_allow_rating, "", "");
display_row(_FORCE_AWAY_ON_SCREENSAVER, "_FORCE_AWAY_ON_SCREENSAVER", $l_admin_options_force_away, "X", "");
#display_row(_CHECK_NEW_MSG_EVERY, "_CHECK_NEW_MSG_EVERY", $l_admin_options_check_new_msg_every, "30", "30");
display_row(_GROUP_USER_CAN_JOIN, "_GROUP_USER_CAN_JOIN", $l_admin_options_group_user_can_join, "", "");
display_row(_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN , "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", $l_admin_options_hierachic_management, "", "X");
#display_row(_STATISTICS, "_STATISTICS", $l_admin_options_statistics, "X", "X");
#display_row(_FLAG_COUNTRY_FROM_IP, "_FLAG_COUNTRY_FROM_IP", $l_admin_options_flag_country, "-", "X");
display_row(_ENTERPRISE_SERVER, "_ENTERPRISE_SERVER", $l_admin_options_enterprise_server, "", "-");
//

echo "<TR>";
echo "<TD colspan='5' align='center' class='catHead'>";
echo "<font face='verdana' size='2'><B>" . $l_admin_options_security_options . " :</B></font>";
echo "</TD>";
echo "</TR>";
display_row(_FORCE_USERNAME_TO_PC_SESSION_NAME, "_FORCE_USERNAME_TO_PC_SESSION_NAME", $l_admin_options_is_usernamePC, "X", "-");
if (_FORCE_USERNAME_TO_PC_SESSION_NAME == "")
{
  display_row(_MINIMUM_USERNAME_LENGTH, "_MINIMUM_USERNAME_LENGTH", $l_admin_options_minimum_length_of_username, "4", "6");
}
display_row(_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER, "_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER", $l_admin_options_auto_add_user, "X", "X");
display_row(_PENDING_NEW_AUTO_ADDED_USER, "_PENDING_NEW_AUTO_ADDED_USER", $l_admin_options_need_admin_after_add, "-", "-");
display_row(_PENDING_USER_ON_COMPUTER_CHANGE, "_PENDING_USER_ON_COMPUTER_CHANGE", $l_admin_options_need_admin_if_chang_check, "X", "-");
display_row(_USER_NEED_PASSWORD, "_USER_NEED_PASSWORD", $l_admin_options_password_user, "-", "X");
if (_USER_NEED_PASSWORD != "")
{
  display_row(_MINIMUM_PASSWORD_LENGTH, "_MINIMUM_PASSWORD_LENGTH", $l_admin_options_minimum_length_of_password, "4", "6");
  display_row(_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER, "_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", $l_admin_options_max_pwd_error_lock, "5", "5");
  display_row(_LOCK_DURATION, "_LOCK_DURATION", $l_admin_options_lock_duration, "10", "20");
  display_row(_PASSWORD_VALIDITY, "_PASSWORD_VALIDITY", $l_admin_options_password_validity, "", "");
  display_row(_PWD_NEED_DIGIT_LETTER, "_PWD_NEED_DIGIT_LETTER", $l_admin_options_pass_need_digit_and_letter, "", "");
  display_row(_PWD_NEED_UPPER_LOWER, "_PWD_NEED_UPPER_LOWER", $l_admin_options_pass_need_upper_and_lower, "", "");
  display_row(_PWD_NEED_SPECIAL_CHARACTER, "_PWD_NEED_SPECIAL_CHARACTER", $l_admin_options_pass_need_special_character, "", "");
}
#display_row(_ALLOW_HISTORY_MESSAGES, "_ALLOW_HISTORY_MESSAGES", $l_admin_options_user_history_messages, "-", "-");
#display_row(_TIME_ZONES, "_TIME_ZONES", $l_admin_options_time_zones, "-", "");
display_row(_CRYPT_MESSAGES, "_CRYPT_MESSAGES", $l_admin_options_crypt_msg, "-", "-");
display_row(_HISTORY_MESSAGES_ON_ACP, "_HISTORY_MESSAGES_ON_ACP", $l_admin_options_log_messages, "-", "-");
display_row(_LOG_SESSION_OPEN, "_LOG_SESSION_OPEN", $l_admin_options_log_session_open, "-", "X");
display_row(_ALLOW_MANAGE_CONTACT_LIST, "_ALLOW_MANAGE_CONTACT_LIST", $l_admin_options_allow_change_contact_list, "X", "X");
display_row(_ALLOW_MANAGE_OPTIONS, "_ALLOW_MANAGE_OPTIONS", $l_admin_options_allow_change_options, "X", "X");
display_row(_ALLOW_MANAGE_PROFILE, "_ALLOW_MANAGE_PROFILE", $l_admin_options_allow_change_profile, "X", "X");
display_row(_FORCE_UPDATE_BY_SERVER, "_FORCE_UPDATE_BY_SERVER", $l_admin_options_force_update_by_server, "", "-");
display_row(_FORCE_UPDATE_BY_INTERNET, "_FORCE_UPDATE_BY_INTERNET", $l_admin_options_force_update_by_internet, "", "X");
display_row(_ALLOW_USE_PROXY, "_ALLOW_USE_PROXY", $l_admin_options_allow_use_proxy, "", "X");
display_row(_UNREAD_MESSAGE_VALIDITY, "_UNREAD_MESSAGE_VALIDITY", $l_admin_options_unread_message_validity, "30", "40");
//
echo "<TR>";
echo "<TD colspan='5' align='center' class='catHead'>";
echo "<font face='verdana' size='2'><B>" . $l_admin_options_shoutbox_title_long . " :</B></font>";
echo "</TD>";
echo "</TR>";
display_row(_SHOUTBOX, "_SHOUTBOX", $l_admin_options_shoutbox_title_short, "", "X");
if (_SHOUTBOX != "")
{
  display_row(_SHOUTBOX_NEED_APPROVAL, "_SHOUTBOX_NEED_APPROVAL", $l_admin_options_shoutbox_need_approval, "", "");
  display_row(_SHOUTBOX_STORE_MAX, "_SHOUTBOX_STORE_MAX", $l_admin_options_shoutbox_store_max, "200", "300");
  display_row(_SHOUTBOX_STORE_DAYS, "_SHOUTBOX_STORE_DAYS", $l_admin_options_shoutbox_store_days, "10", "20");
  display_row(_SHOUTBOX_QUOTA_USER_DAY, "_SHOUTBOX_QUOTA_USER_DAY", $l_admin_options_shoutbox_day_user_quota, "15", "30");
  display_row(_SHOUTBOX_QUOTA_USER_WEEK, "_SHOUTBOX_QUOTA_USER_WEEK", $l_admin_options_shoutbox_week_user_quota, "30", "50");
  display_row(_SHOUTBOX_VOTE, "_SHOUTBOX_VOTE", $l_admin_options_shoutbox_can_vote, "", "");
  if (_SHOUTBOX_VOTE != "")
  {
    display_row(_SHOUTBOX_MAX_NOTES_USER_DAY, "_SHOUTBOX_MAX_NOTES_USER_DAY", $l_admin_options_shoutbox_day_votes_quota, "", "");
    display_row(_SHOUTBOX_MAX_NOTES_USER_WEEK, "_SHOUTBOX_MAX_NOTES_USER_WEEK", $l_admin_options_shoutbox_week_votes_quota, "", "");
  }
  display_row(_SHOUTBOX_PUBLIC, "_SHOUTBOX_PUBLIC", $l_admin_options_shoutbox_public, "", "");
}
//
echo "<TR>";
echo "<TD colspan='5' align='center' class='catHead'>";
echo "<font face='verdana' size='2'><B>" . $l_admin_options_share_files_title . " :</B></font>";
echo "</TD>";
echo "</TR>";
display_row(_SHARE_FILES, "_SHARE_FILES", $l_admin_options_share_files, "", "");
if (_SHARE_FILES != "")
{
  display_row(_SHARE_FILES_EXCHANGE, "_SHARE_FILES_EXCHANGE", $l_admin_options_share_files_exchange, "", "");
  display_row(_SHARE_FILES_MAX_NB_FILES_USER, "_SHARE_FILES_MAX_NB_FILES_USER", $l_admin_options_share_files_max_nb_files_user, "", "");
  display_row(_SHARE_FILES_NEED_APPROVAL, "_SHARE_FILES_NEED_APPROVAL", $l_admin_options_share_files_need_approval, "", "");
  display_row(_SHARE_FILES_EXCHANGE_NEED_APPROVAL, "_SHARE_FILES_EXCHANGE_NEED_APPROVAL", $l_admin_options_share_files_exchange_need_approval, "", "");
  display_row(_SHARE_FILES_QUOTA_FILES_USER_WEEK, "_SHARE_FILES_QUOTA_FILES_USER_WEEK", $l_admin_options_share_files_quota_files_user_week, "", "");
  display_row(_SHARE_FILES_MAX_SPACE_SIZE_USER, "_SHARE_FILES_MAX_SPACE_SIZE_USER", $l_admin_options_share_files_max_space_size_user, "", "");
  display_row(_SHARE_FILES_VOTE, "_SHARE_FILES_VOTE", $l_admin_options_share_files_can_vote, "", "");
}
//
display_row(_BACKUP_FILES, "_BACKUP_FILES", $l_admin_options_backup_files_allow, "", "");
if (_BACKUP_FILES != "")
{
  //display_row(_BACKUP_FILES_ALLOW_MULTI_FOLDERS, "_BACKUP_FILES_ALLOW_MULTI_FOLDERS", $l_admin_options_backup_files_multi_folders, "X", "X");
  //display_row(_BACKUP_FILES_ALLOW_SUB_FOLDERS, "_BACKUP_FILES_ALLOW_SUB_FOLDERS", $l_admin_options_backup_files_sub_folders, "X", "X");
  display_row(_BACKUP_FILES_MAX_NB_ARCHIVES_USER, "_BACKUP_FILES_MAX_NB_ARCHIVES_USER", $l_admin_options_backup_files_max_nb_backup_user, "3", "2");
  display_row(_BACKUP_FILES_MAX_ARCHIVE_SIZE, "_BACKUP_FILES_MAX_ARCHIVE_SIZE", $l_admin_options_backup_files_max_file_size, "", "");
  display_row(_BACKUP_FILES_MAX_SPACE_SIZE_USER, "_BACKUP_FILES_MAX_SPACE_SIZE_USER", $l_admin_options_share_files_max_space_size_user, "", "");
  //display_row(_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL, "_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL", $l_admin_options_share_files_max_space_size_total, "", "");
}

//
echo "<TR>";
echo "<TD colspan='5' align='center' class='catHead'>";
echo "<font face='verdana' size='2'><B>" . $l_admin_options_bookmarks . " :</B></font>";
echo "</TD>";
echo "</TR>";
display_row(_BOOKMARKS, "_BOOKMARKS", $l_admin_options_bookmarks, "", "X");
if (_BOOKMARKS != "")
{
  display_row(_BOOKMARKS_VOTE, "_BOOKMARKS_VOTE", $l_admin_options_bookmarks_can_vote, "X", "X");
  display_row(_BOOKMARKS_PUBLIC, "_BOOKMARKS_PUBLIC", $l_admin_options_bookmarks_public . " + RSS", "X", "X");
  display_row(_BOOKMARKS_NEED_APPROVAL, "_BOOKMARKS_NEED_APPROVAL", $l_admin_options_bookmarks_need_approval, "X", "X");
}
//
echo "<TR>";
echo "<TD colspan='5' align='center' class='catHead'>";
echo "<font face='verdana' size='2'><B>" . $l_admin_options_special_modes . " :</B></font>";
echo "</TD>";
echo "</TR>";

display_row(_SPECIAL_MODE_OPEN_COMMUNITY, "_SPECIAL_MODE_OPEN_COMMUNITY", $l_admin_options_opencommunity, "-", "-");
display_row(_SPECIAL_MODE_GROUP_COMMUNITY, "_SPECIAL_MODE_GROUP_COMMUNITY", $l_admin_options_groupcommunity, "-", "-");
display_row(_SPECIAL_MODE_OPEN_GROUP_COMMUNITY, "_SPECIAL_MODE_OPEN_GROUP_COMMUNITY", $l_admin_options_opengroupcommunity, "-", "-");
//
echo "<TR>";
echo "<TD colspan='5' align='center' class='catHead'>";
//echo "<font face='verdana' size='2'><B>" . $l_admin_options_autentification . " :</B></font>";
echo "<font face='verdana' size='2'><B>" . $l_admin_options_info_10 . " :</B></font>";
echo "</TD>";
echo "</TR>";
/*
if (_PASSWORD_FOR_PRIVATE_SERVER != "")
  display_row("X", "_PASSWORD_FOR_PRIVATE_SERVER", $l_admin_options_password_for_private_server, "-", "");
else
  display_row("", "_PASSWORD_FOR_PRIVATE_SERVER", $l_admin_options_password_for_private_server, "-", "");
*/
//
$lst = "";
$external_authentication_name = "";
$external_authentication = _EXTERNAL_AUTHENTICATION;
if ($external_authentication != "") 
{
  $external_authentication_name = f_type_auth_extern();
  if ($external_authentication_name == "") $external_authentication = "";
}
if ($external_authentication != "")
  display_row("X", "_EXTERNAL_AUTHENTICATION", $l_admin_authentication_extern . " <B>" . $external_authentication_name . "</B>", "", "");
else
{
  display_row("", "_EXTERNAL_AUTHENTICATION", $l_admin_authentication_extern . "... <B>[*]</B>", "", "");
  $extern_auth_list = array();
  $extern_auth_list = f_extern_auth_list();
  foreach ($extern_auth_list as $name) 
  {
    $lst .= $name . ", ";
  }
  $lst = substr($lst, 0, (strlen($lst)-2) ) . "...";
}
display_row(_EXTERN_URL_TO_REGISTER, "_EXTERN_URL_TO_REGISTER", $l_admin_extern_url_to_register, "", "");
display_row(_EXTERN_URL_FORGET_PASSWORD, "_EXTERN_URL_FORGET_PASSWORD", $l_admin_extern_url_password_forget, "", "");
display_row(_EXTERN_URL_CHANGE_PASSWORD, "_EXTERN_URL_CHANGE_PASSWORD", $l_admin_extern_url_change_password, "", "");

//
echo "</TABLE>";
echo "<BR/>";

//
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'><B>" . $l_legende . "</B></TD></TR>";

echo "</TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='18' HEIGHT='18'>";
echo "</TD><TD class='row3'><font face='verdana' size='2'>&nbsp;On : " . $l_admin_options_legende_not_empty . "&nbsp;";
echo "</TD>";

echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
//echo "&nbsp;";
echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif' WIDTH='18' HEIGHT='18'>";
echo "</TD><TD class='row3'><font face='verdana' size='2'>&nbsp;Off : " . $l_admin_options_legende_empty . "&nbsp;";
echo "</TD>";

echo "</TD></TR>";
echo "</TABLE>";

if ($lst != "")
{
  echo "<BR/>";
  echo "</CENTER>";
  echo "<font face='verdana' size='1'>";
  echo "<B>[*]</B> <U>" . $l_admin_authentication_extern . "</U> : <BR/>";
  echo $lst . "<BR/>";
}

//footer(); ne pas afficher pour que les utilisateurs ne connaissent pas la version du serveur...

echo "</body></html>";
?>
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
//if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_log_read);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_log_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="300;url=""> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
function f_select_file($title, $filename, $folder, $image)
{
  GLOBAL $taille_total, $l_date_format_display, $l_time_format_display, $l_KB, $l_display, $l_admin_bt_empty, $lang;
  GLOBAL $l_admin_log_type_info, $l_admin_log_type_warning, $l_admin_log_type_error, $l_admin_log_type_monitor;
  GLOBAL $l_admin_stats_col_nb_msg;
  //
  $displayed = 0;
  $file = $folder . $filename . ".txt";
  if (is_readable($file))
  {
    $sof = filesize($file);
    if ($sof > 10)
    {
      $taille_total = ($taille_total + $sof);
      echo "<FORM METHOD='POST' name='formulaire' ACTION ='log_display.php?'>";
      echo "<TR>";
      echo "<TD class='row1'>";
      if ($image == "OKOK") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png' ALT='" . $l_admin_log_type_info . "' TITLE='" . $l_admin_log_type_info . "' WIDTH='16' HEIGHT='16' BORDER='0' /> ";
      if ($image == "OK") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "information.png' ALT='" . $l_admin_log_type_info . "' TITLE='" . $l_admin_log_type_info . "' WIDTH='16' HEIGHT='16' BORDER='0' /> ";
      if ($image == "Ko") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "forbidden.png' ALT='" . $l_admin_log_type_warning . "' TITLE='" . $l_admin_log_type_warning . "' WIDTH='16' HEIGHT='16' BORDER='0' /> ";
      if ($image == "!") echo  "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "warning.png' ALT='" . $l_admin_log_type_error . "' TITLE='" . $l_admin_log_type_error . "' WIDTH='16' HEIGHT='16' BORDER='0' /> ";
      if ($image == "spy") echo  "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "eye.png' ALT='" . $l_admin_log_type_monitor . "' TITLE='" . $l_admin_log_type_monitor . "' WIDTH='16' HEIGHT='16' BORDER='0' /> ";
      echo "<font face='verdana' size='1'>";
      //echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_view_log.png' VALUE = '" . $l_display . "' ALT='" . $l_display . "' TITLE='" . $l_display . "' WIDTH='16' HEIGHT='16' />";
      echo "&nbsp;" . $title . "&nbsp;";
      echo "</TD>";
      echo "<TD ALIGN='CENTER' class='row1'>";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_view_log.png' VALUE = '" . $l_display . "' ALT='" . $l_display . "' TITLE='" . $l_display . "' WIDTH='16' HEIGHT='16' />";
      echo "</TD>";
      echo "<TD class='row1' align='CENTER' WIDTH='170'>";
      if (date($l_date_format_display, filemtime($file)) == date($l_date_format_display)) echo "<B>";
      if (date("Y-m", filemtime($file)) <> date("Y-m")) echo "<font color='gray'>";
      echo "<font face='verdana' size='1'>&nbsp;" . date($l_date_format_display . " - ". $l_time_format_display, filemtime($file)) . "&nbsp;";
      echo "</TD>";
      echo "<TD class='row1' align='RIGHT'>";
      $sof2 = round($sof / 1024, 1);
      if (intval($sof2) < 1) echo "<font color='gray'>";
      if (intval($sof2) > 999) echo "<font color='red'>";
      echo "<font face='verdana' size='1'>&nbsp;" . sprintf('%.1f', $sof2) . " " . $l_KB . "&nbsp;";
      //echo "<font face='verdana' size='2'>&nbsp;" . ceil($sof / 1024) . " " . $l_KB . "&nbsp;";
      echo "</TD>";
      echo "<TD class='row1' align='center'>";
      if (f_check_acp_rights(_C_ACP_RIGHT_log_purge) == "OK")
      {
        echo "<A HREF='log_empty.php?lang=" . $lang . "&file=" . $filename . "&folder=" . base64_encode($folder) . "&' >";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_empty . "' TITLE='" . $l_admin_bt_empty . "' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
      }
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "<font face='verdana' size='1' color='gray'><I>" . $file . "</I>&nbsp;";
      echo "</TD>";
      echo "<INPUT TYPE='hidden' name='action' value = '" . $filename . "' />";
      echo "<INPUT TYPE='hidden' name='folder' value = '" . base64_encode($folder) . "' />";
      echo "<INPUT TYPE='hidden' name='title' value = '" . base64_encode($title) . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TR>";
      echo "</FORM>";
      $displayed = 1;
    }
  }
  return $displayed;
}
// 
$taille_total = 0;
echo "<BR/>";
echo "<table width='1000' cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
echo "<TH colspan='6' class='thHead'>";
echo "<FONT size='3'>";
//echo $l_admin_log_select;
echo $l_admin_log_title;
echo "</TH>";
echo "</TR>\n";
//
$nb_aff = 0;
$nb_aff = $nb_aff + f_select_file("<font color='red'>" . $l_admin_log_hack . "</font>", "hack_attempt", "../distant/log/", "!");
$nb_aff = $nb_aff + f_select_file($l_admin_log_error_log, "error_log", "../distant/log/", "!");
$nb_aff = $nb_aff + f_select_file($l_admin_log_error_log, "error_log_connection", "../distant/log/", "!");
$nb_aff = $nb_aff + f_select_file($l_admin_log_password_errors, "log_password_errors", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_lock_user_password, "log_lock_user_for_password_errors", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_check_change, "log_user_check_change", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_change_nickname, "log_user_change_nickname", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_username_unknown, "log_reject_username_unknown", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_reject_username, "log_reject_username", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_reject_ip, "log_reject_ip", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_reject_pc, "log_reject_pc", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_reject_max_same_ip, "log_reject_max_same_ip", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_reject_max_same_pc, "log_reject_duplicate_pc", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_reject_max_users, "log_reject_max_users", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_server_full, "cannot_acces_server_full", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_one_user_two_pc, "log_user_check_double", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_no_ip_address, "error_acces_no_ip_log", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_version_to_old, "error_version_log", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_private_password, "error_acces_private_log", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_alert, "log_files_share_alert", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_max_file_size, "log_files_share_cannot_3a", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_max_space_size_user, "log_files_share_cannot_3b", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_max_space_size_total, "log_files_share_cannot_3c", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_max_nb_files_user, "log_files_share_cannot_4a", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_too_much_pending, "log_files_share_cannot_3d", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_max_nb_files_user_total, "log_files_share_cannot_4b", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_quota_user_week, "log_files_share_cannot_4c", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_error . $l_admin_log_files_error_unknow_media, "log_files_share_cannot_7", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_error . $l_admin_log_files_error_max_file_size, "log_files_backup_cannot_3a", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_error . $l_admin_log_files_error_max_space_size_user, "log_files_backup_cannot_3b", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_error . $l_admin_log_files_error_max_space_size_total, "log_files_backup_cannot_3c", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_error . $l_admin_log_files_error_max_nb_files_user, "log_files_backup_cannot_4a", "../distant/log/", "Ko");
$nb_aff = $nb_aff + f_select_file($l_admin_log_user_create, "log_user_create", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_password_out_of_date, "log_password_too_old", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_upload_avatar, "log_upload_avatar", "../" . _PUBLIC_FOLDER . "/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_sended, "log_files_share_sended", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_proposed, "log_files_share_proposed", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_exchange_sended, "log_files_exchange_sended", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_exchange_proposed, "log_files_exchange_proposed", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_trashed, "log_files_share_trashed", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_deleted, "log_files_share_deleted", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_exchange_trashed, "log_files_exchange_trashed", "../distant/log/", "OK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_exchange_deleted, "log_files_exchange_deleted", "../distant/log/", "OKOK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_session_open, "log_open_session", "../distant/log/", "OKOK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_sended, "log_files_backup_sended", "../distant/log/", "OKOK");
$nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_deleted, "log_files_backup_deleted", "../distant/log/", "OK");


//
if (_HISTORY_MESSAGES_ON_ACP != "") 
{
  $nb_aff = $nb_aff + f_select_file($l_admin_stats_col_nb_msg, "messages_log", "../distant/log/", "spy");
  $nb_aff = $nb_aff + f_select_file($l_admin_options_shoutbox_title_short, "shoutbox_log", "../distant/log/", "spy");
  $nb_aff = $nb_aff + f_select_file($l_admin_bookmarks_title, "bookmarks_log", "../distant/log/", "spy");
}
//
if ($taille_total > 2048)
{
  echo "<TR>";
  echo "<TD colspan='3' class='row3'>&nbsp;</TD>";
  echo "<TD ALIGN='CENTER' class='row1'>";
  echo "<font face='verdana' size='2'>&nbsp;";
  echo round($taille_total / 1024) . " " . $l_KB . "&nbsp;";
  echo "</TD>";
  echo "<TD colspan='2' class='row3'>&nbsp;</TD>";
  echo "</TR>";
}
//
if ($nb_aff <= 0)
{
  echo "<TR>";
  echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
  echo "<font face='verdana' size='2'>&nbsp;";
  echo $l_admin_log_empty . "&nbsp;";
  echo "</TD>";
  echo "</TR>";
}
//
echo "</TABLE>";
echo "<BR/>\n";
//
$taille_total = 0;
echo "<table width='1000' cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
echo "<TH colspan='6' class='thHead'>";
echo "<FONT size='3'>";
echo $l_admin_log_title_admin;
echo "</TH>";
echo "</TR>\n";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) 
{
  echo "<TR>";
  echo "<TD colspan='4' ALIGN='CENTER' class='catBottom'>";
  echo "<font face=verdana size=2 color='gray'><I>Not in demo version </I></font>";
  echo "</TD>";
  echo "</TR>";
}
else
{
  $nb_aff = 0;
  $nb_aff = $nb_aff + f_select_file($l_admin_log_error_log, "error_log", "log/", "!");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_disallow, "log_user_disallow", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_delete, "log_user_delete", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_ban_ip_address, "log_ban_ip_address", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_unban_ip_address, "log_unban_ip_address", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_ban_username, "log_ban_username", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_unban_username, "log_unban_username", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_ban_computer, "log_ban_computer", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_unban_computer, "log_unban_computer", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_admin_alert_not_get, "log_user_admin_alert_not_get", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_admin_alert_get, "log_user_admin_alert_get", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_acp_login_error, "log_acp_login_errors", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_acp_password_error, "log_acp_password_errors", "log/", "Ko");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_avatar_valid, "log_user_avatar_valid", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_send_order, "log_send_order", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_send_message, "log_send_message", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_shoutbox_delete_message, "shoutbox_delete_message", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_bookmark_delete, "log_bookmark_delete", "log/", "OK");
  //$nb_aff = $nb_aff + f_select_file($l_admin_log_files_delete, "log_files_delete", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_files_share_deleted, "log_files_delete", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_files_pendind_delete, "log_file_pendind_delete", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_server_status, "log_server_status", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_options_update, "log_options_update", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_files_backup_deleted, "log_backup_delete", "log/", "OK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_create, "log_user_create", "log/", "OKOK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_user_allow, "log_user_allow", "log/", "OKOK");
  $nb_aff = $nb_aff + f_select_file($l_admin_log_acp_connect, "log_acp_admin_connect", "log/", "OKOK");
  
  //
  if ($taille_total > 2048)
  {
    echo "<TR>";
    echo "<TD colspan='3' class='row3'>&nbsp;</TD>";
    echo "<TD ALIGN='CENTER' class='row1'>";
    echo "<font face='verdana' size='2'>&nbsp;";
    echo round($taille_total / 1024) . " " . $l_KB . "&nbsp;";
    echo "</TD>";
    echo "<TD colspan='2' class='row3'>&nbsp;</TD>";
    echo "</TR>";
  }
  //
  if ($nb_aff <= 0)
  {
    echo "<TR>";
    echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
    echo "<font face='verdana' size='2'>&nbsp;";
    echo $l_admin_log_empty . "&nbsp;";
    echo "</TD>";
    echo "</TR>";
  }
}
//
/*
echo "<TR>";
echo "<TD colspan='4' ALIGN='CENTER' class='catBottom'>";
echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_display . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "</TR>";
echo "</FORM>";
*/
echo "</TABLE>";
echo "<BR/>";
//
//
display_menu_footer();

echo "</body></html>";
?>
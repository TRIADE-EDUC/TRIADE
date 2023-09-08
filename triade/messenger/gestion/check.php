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
# NO ! because check in this page ! require ("../common/display_errors.inc.php"); 
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_users_unlock);
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
//
//
$if_prob_const = "OK";
$txt_const = "";
if (!defined("_SHOUTBOX_ALLOW_SCROLLING"))                    $txt_const .= f_add_file_missing("_SHOUTBOX_ALLOW_SCROLLING", "2.0.6");
if (!defined("_FORCE_OPTION_FILE_FROM_SERVER"))               $txt_const .= f_add_file_missing("_FORCE_OPTION_FILE_FROM_SERVER", "2.0.5");
if (!defined("_GROUP_ID_DEFAULT_FOR_NEW_USER"))               $txt_const .= f_add_file_missing("_GROUP_ID_DEFAULT_FOR_NEW_USER", "2.0.5"); // 2.0.5.241 - 11/2013
if (!defined("_SHARE_FILES_ALLOW_ACCENT"))                    $txt_const .= f_add_file_missing("_SHARE_FILES_ALLOW_ACCENT", "2.0.5");
if (!defined("_BACKUP_FILES_FORCE_EVERY_DAY_AT"))             $txt_const .= f_add_file_missing("_BACKUP_FILES_FORCE_EVERY_DAY_AT", "2.0.5");
if (!defined("_BACKUP_FILES_FTP_PORT_NUMBER"))                $txt_const .= f_add_file_missing("_BACKUP_FILES_FTP_PORT_NUMBER", "2.0.5");
if (!defined("_BACKUP_FILES_FTP_PASSWORD_CRYPT"))             $txt_const .= f_add_file_missing("_BACKUP_FILES_FTP_PASSWORD_CRYPT", "2.0.5");
if (!defined("_BACKUP_FILES_FTP_PASSWORD"))                   $txt_const .= f_add_file_missing("_BACKUP_FILES_FTP_PASSWORD", "2.0.5");
if (!defined("_BACKUP_FILES_FTP_LOGIN"))                      $txt_const .= f_add_file_missing("_BACKUP_FILES_FTP_LOGIN", "2.0.5");
if (!defined("_BACKUP_FILES_FTP_ADDRESS"))                    $txt_const .= f_add_file_missing("_BACKUP_FILES_FTP_ADDRESS", "2.0.5");
if (!defined("_BACKUP_FILES_FOLDER"))                         $txt_const .= f_add_file_missing("_BACKUP_FILES_FOLDER", "2.0.5");
if (!defined("_BACKUP_FILES_ALLOW_SUB_FOLDERS"))              $txt_const .= f_add_file_missing("_BACKUP_FILES_ALLOW_SUB_FOLDERS", "2.0.5");
if (!defined("_BACKUP_FILES_ALLOW_MULTI_FOLDERS"))            $txt_const .= f_add_file_missing("_BACKUP_FILES_ALLOW_MULTI_FOLDERS", "2.0.5");
if (!defined("_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY"))         $txt_const .= f_add_file_missing("_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY", "2.0.5");
if (!defined("_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL"))           $txt_const .= f_add_file_missing("_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL", "2.0.5");
if (!defined("_BACKUP_FILES_MAX_SPACE_SIZE_USER"))            $txt_const .= f_add_file_missing("_BACKUP_FILES_MAX_SPACE_SIZE_USER", "2.0.5");
if (!defined("_BACKUP_FILES_MAX_ARCHIVE_SIZE"))               $txt_const .= f_add_file_missing("_BACKUP_FILES_MAX_ARCHIVE_SIZE", "2.0.5");
if (!defined("_BACKUP_FILES_MAX_NB_ARCHIVES_USER"))           $txt_const .= f_add_file_missing("_BACKUP_FILES_MAX_NB_ARCHIVES_USER", "2.0.5");
if (!defined("_BACKUP_FILES"))                                $txt_const .= f_add_file_missing("_BACKUP_FILES", "2.0.5");
if (!defined("_SLOW_NOTIFY"))                                 $txt_const .= f_add_file_missing("_SLOW_NOTIFY", "2.0.5"); // _FULL_CHECK
if (!defined("_ALLOW_COL_FUNCTION_NAME"))                     $txt_const .= f_add_file_missing("_ALLOW_COL_FUNCTION_NAME", "2.0.5"); // _HIDE_COL_FUNCTION_NAME
if (!defined("_ALLOW_CONTACT_RATING"))                        $txt_const .= f_add_file_missing("_ALLOW_CONTACT_RATING", "2.0.5"); // _ALLOW_USER_RATING
if (!defined("_OUTOFDATE_AFTER_NOT_USE_DURATION"))            $txt_const .= f_add_file_missing("_OUTOFDATE_AFTER_NOT_USE_DURATION", "2.0.5"); // _OUTOFDATE_AFTER_X_DAYS_NOT_USE
if (!defined("_ALLOW_MANAGE_CONTACT_LIST"))                   $txt_const .= f_add_file_missing("_ALLOW_MANAGE_CONTACT_LIST", "2.0.5"); // _LOCK_USER_CONTACT_LIST
if (!defined("_ALLOW_MANAGE_OPTIONS"))                        $txt_const .= f_add_file_missing("_ALLOW_MANAGE_OPTIONS", "2.0.5"); // _LOCK_USER_OPTIONS
if (!defined("_ALLOW_MANAGE_PROFILE"))                        $txt_const .= f_add_file_missing("_ALLOW_MANAGE_PROFILE", "2.0.5"); // _LOCK_USER_PROFILE
if (!defined("_ALLOW_HISTORY_MESSAGES"))                      $txt_const .= f_add_file_missing("_ALLOW_HISTORY_MESSAGES", "2.0.5"); // _ALLOW_USER_TO_HISTORY_MESSAGES
if (!defined("_FLAG_COUNTRY_FROM_IP"))                        $txt_const .= f_add_file_missing("_FLAG_COUNTRY_FROM_IP", "2.0.5"); // _DISPLAY_USER_FLAG_COUNTRY
if (!defined("_HISTORY_MESSAGES_ON_ACP"))                     $txt_const .= f_add_file_missing("_HISTORY_MESSAGES_ON_ACP", "2.0.5"); // _LOG_MESSAGES
if (!defined("_ALLOW_HIDDEN_TO_CONTACTS"))                    $txt_const .= f_add_file_missing("_ALLOW_HIDDEN_TO_CONTACTS", "2.0.5"); // _ALLOW_INVISIBLE
if (!defined("_ALLOW_HISTORY_MESSAGES_EXPORT"))               $txt_const .= f_add_file_missing("_ALLOW_HISTORY_MESSAGES_EXPORT", "2.0.5"); // 07/2013
if (!defined("_ACP_ALLOW_MEMORY_AUTH"))                       $txt_const .= f_add_file_missing("_ACP_ALLOW_MEMORY_AUTH", "2.0.5"); // 07/2013
if (!defined("_ACP_PROTECT_BY_HTACCESS"))                     $txt_const .= f_add_file_missing("_ACP_PROTECT_BY_HTACCESS", "2.0.5"); // 07/2013
if (!defined("_ROLE_ID_DEFAULT_FOR_NEW_USER"))                $txt_const .= f_add_file_missing("_ROLE_ID_DEFAULT_FOR_NEW_USER", "2.0.5"); // 06/2013
if (!defined("_ALLOW_HIDDEN_STATUS"))                         $txt_const .= f_add_file_missing("_ALLOW_HIDDEN_STATUS", "2.0.5"); // 06/2013
if (!defined("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN")) $txt_const .= f_add_file_missing("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN", "2.0.5"); // 06/2013
if (!defined("_SHARE_FILES_ALLOW_UPPERCASE"))                 $txt_const .= f_add_file_missing("_SHARE_FILES_ALLOW_UPPERCASE", "2.0.4");
if (!defined("_SHARE_FILES_PROTECT"))                         $txt_const .= f_add_file_missing("_SHARE_FILES_PROTECT", "2.0.4");
if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH"))    $txt_const .= f_add_file_missing("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH", "2.0.4");
if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK"))     $txt_const .= f_add_file_missing("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK", "2.0.4");
if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY"))      $txt_const .= f_add_file_missing("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY", "2.0.4");
if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH")) $txt_const .= f_add_file_missing("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH", "2.0.4");
if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK"))  $txt_const .= f_add_file_missing("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK", "2.0.4");
if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY"))   $txt_const .= f_add_file_missing("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY", "2.0.4");
if (!defined("_SHARE_FILES_COMPRESS"))                  $txt_const .= f_add_file_missing("_SHARE_FILES_COMPRESS", "2.0.4");
if (!defined("_SHARE_FILES_EXCHANGE_WEBCAM"))           $txt_const .= f_add_file_missing("_SHARE_FILES_EXCHANGE_WEBCAM", "2.0.4");
if (!defined("_SHARE_FILES_WEBCAM"))                    $txt_const .= f_add_file_missing("_SHARE_FILES_WEBCAM", "2.0.4");
if (!defined("_SHARE_FILES_EXCHANGE_SCREENSHOT"))       $txt_const .= f_add_file_missing("_SHARE_FILES_EXCHANGE_SCREENSHOT", "2.0.4");
if (!defined("_SHARE_FILES_SCREENSHOT"))                $txt_const .= f_add_file_missing("_SHARE_FILES_SCREENSHOT", "2.0.4");
if (!defined("_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY"))  $txt_const .= f_add_file_missing("_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY", "2.0.4");
if (!defined("_SHARE_FILES_FOLDER"))                    $txt_const .= f_add_file_missing("_SHARE_FILES_FOLDER", "2.0.4");
if (!defined("_SHARE_FILES_VOTE"))                      $txt_const .= f_add_file_missing("_SHARE_FILES_VOTE", "2.0.4");
if (!defined("_SHARE_FILES_TRASH"))                     $txt_const .= f_add_file_missing("_SHARE_FILES_TRASH", "2.0.4");
if (!defined("_SHARE_FILES_QUOTA_FILES_USER_WEEK"))     $txt_const .= f_add_file_missing("_SHARE_FILES_QUOTA_FILES_USER_WEEK", "2.0.4");
if (!defined("_SHARE_FILES_APPROVAL_QUEUE"))            $txt_const .= f_add_file_missing("_SHARE_FILES_APPROVAL_QUEUE", "2.0.4");
if (!defined("_SHARE_FILES_NEED_APPROVAL"))             $txt_const .= f_add_file_missing("_SHARE_FILES_NEED_APPROVAL", "2.0.4");
if (!defined("_SHARE_FILES_MAX_SPACE_SIZE_USER"))       $txt_const .= f_add_file_missing("_SHARE_FILES_MAX_SPACE_SIZE_USER", "2.0.4");
if (!defined("_SHARE_FILES_MAX_SPACE_SIZE_TOTAL"))      $txt_const .= f_add_file_missing("_SHARE_FILES_MAX_SPACE_SIZE_TOTAL", "2.0.4");
if (!defined("_SHARE_FILES_MAX_NB_FILES_USER"))         $txt_const .= f_add_file_missing("_SHARE_FILES_MAX_NB_FILES_USER", "2.0.4");
if (!defined("_SHARE_FILES_MAX_NB_FILES_TOTAL"))        $txt_const .= f_add_file_missing("_SHARE_FILES_MAX_NB_FILES_TOTAL", "2.0.4");
if (!defined("_SHARE_FILES_MAX_FILE_SIZE"))             $txt_const .= f_add_file_missing("_SHARE_FILES_MAX_FILE_SIZE", "2.0.4");
if (!defined("_SHARE_FILES_FTP_PORT_NUMBER"))           $txt_const .= f_add_file_missing("_SHARE_FILES_FTP_PORT_NUMBER", "2.0.4");
if (!defined("_SHARE_FILES_FTP_PASSWORD_CRYPT"))        $txt_const .= f_add_file_missing("_SHARE_FILES_FTP_PASSWORD_CRYPT", "2.0.4");
if (!defined("_SHARE_FILES_FTP_PASSWORD"))              $txt_const .= f_add_file_missing("_SHARE_FILES_FTP_PASSWORD", "2.0.4");
if (!defined("_SHARE_FILES_FTP_LOGIN"))                 $txt_const .= f_add_file_missing("_SHARE_FILES_FTP_LOGIN", "2.0.4");
if (!defined("_SHARE_FILES_FTP_ADDRESS"))               $txt_const .= f_add_file_missing("_SHARE_FILES_FTP_ADDRESS", "2.0.4");
if (!defined("_SHARE_FILES_EXCHANGE_TRASH"))            $txt_const .= f_add_file_missing("_SHARE_FILES_EXCHANGE_TRASH", "2.0.4");
if (!defined("_SHARE_FILES_EXCHANGE_NEED_APPROVAL"))    $txt_const .= f_add_file_missing("_SHARE_FILES_EXCHANGE_NEED_APPROVAL", "2.0.4");
if (!defined("_SHARE_FILES_EXCHANGE"))                  $txt_const .= f_add_file_missing("_SHARE_FILES_EXCHANGE", "2.0.4");
if (!defined("_SHARE_FILES"))                           $txt_const .= f_add_file_missing("_SHARE_FILES", "2.0.4");
if (!defined("_ALLOW_POST_IT"))                         $txt_const .= f_add_file_missing("_ALLOW_POST_IT", "2.0.4");
if (!defined("_PASSWORD_VALIDITY"))                     $txt_const .= f_add_file_missing("_PASSWORD_VALIDITY", "2.0.4");
if (!defined("_AUTO_ADD_CONTACT_USER_ID"))              $txt_const .= f_add_file_missing("_AUTO_ADD_CONTACT_USER_ID", "2.0.4");
if (!defined("_SEND_ADMIN_ALERT_EMAIL"))                $txt_const .= f_add_file_missing("_SEND_ADMIN_ALERT_EMAIL", "2.0.4");
if (!defined("_SKIN_FORCED_COLOR_CUSTOM_VERSION"))      $txt_const .= f_add_file_missing("_SKIN_FORCED_COLOR_CUSTOM_VERSION", "2.0.4");
if (!defined("_ALLOW_REDUCE_MESSAGE_SCREEN"))           $txt_const .= f_add_file_missing("_ALLOW_REDUCE_MESSAGE_SCREEN", "2.0.4");
if (!defined("_ALLOW_REDUCE_MAIN_SCREEN"))              $txt_const .= f_add_file_missing("_ALLOW_REDUCE_MAIN_SCREEN", "2.0.4");
if (!defined("_ALLOW_SOUND_USAGE"))                     $txt_const .= f_add_file_missing("_ALLOW_SOUND_USAGE", "2.0.4");
if (!defined("_ALLOW_CLOSE_IM"))                        $txt_const .= f_add_file_missing("_ALLOW_CLOSE_IM", "2.0.4");
if (!defined("_ALLOW_SKIN"))                            $txt_const .= f_add_file_missing("_ALLOW_SKIN", "2.0.4");
if (!defined("_FORCE_LAUNCH_ON_STARTUP"))               $txt_const .= f_add_file_missing("_FORCE_LAUNCH_ON_STARTUP", "2.0.4");
if (!defined("_SPECIAL_MODE_OPEN_GROUP_COMMUNITY"))     $txt_const .= f_add_file_missing("_SPECIAL_MODE_OPEN_GROUP_COMMUNITY", "2.0.4");
if (!defined("_ONLINE_REASONS_LIST"))                   $txt_const .= f_add_file_missing("_ONLINE_REASONS_LIST", "2.0.3");
if (!defined("_BUSY_REASONS_LIST"))                     $txt_const .= f_add_file_missing("_BUSY_REASONS_LIST", "2.0.3");
if (!defined("_DONOTDISTURB_REASONS_LIST"))             $txt_const .= f_add_file_missing("_DONOTDISTURB_REASONS_LIST", "2.0.3");
if (!defined("_WAIT_STARTUP_IF_SERVER_UNAVAILABLE"))    $txt_const .= f_add_file_missing("_WAIT_STARTUP_IF_SERVER_UNAVAILABLE", "2.0.3");
if (!defined("_ROLES_TO_OVERRIDE_PERMISSIONS"))         $txt_const .= f_add_file_missing("_ROLES_TO_OVERRIDE_PERMISSIONS", "2.0.3");
if (!defined("_INVITE_FILL_PROFILE_ON_FIRST_LOGIN"))    $txt_const .= f_add_file_missing("_INVITE_FILL_PROFILE_ON_FIRST_LOGIN", "2.0.3");
#if (!defined("_LOCK_USER_PROFILE"))                     $txt_const .= f_add_file_missing("_LOCK_USER_PROFILE", "2.0.3");
if (!defined("_LOCK_AFTER_NO_ACTIVITY_DURATION"))       $txt_const .= f_add_file_missing("_LOCK_AFTER_NO_ACTIVITY_DURATION", "2.0.3");
if (!defined("_LOCK_AFTER_NO_CONTACT_DURATION"))        $txt_const .= f_add_file_missing("_LOCK_AFTER_NO_CONTACT_DURATION", "2.0.3");
if (!defined("_UNREAD_MESSAGE_VALIDITY"))               $txt_const .= f_add_file_missing("_UNREAD_MESSAGE_VALIDITY", "2.0.3");
if (!defined("_LOCK_DURATION"))                         $txt_const .= f_add_file_missing("_LOCK_DURATION", "2.0.3");
if (!defined("_BOOKMARKS"))                             $txt_const .= f_add_file_missing("_BOOKMARKS", "2.0.3");
if (!defined("_BOOKMARKS_VOTE"))                        $txt_const .= f_add_file_missing("_BOOKMARKS_VOTE", "2.0.3");
if (!defined("_BOOKMARKS_PUBLIC"))                      $txt_const .= f_add_file_missing("_BOOKMARKS_PUBLIC", "2.0.3");
if (!defined("_BOOKMARKS_NEED_APPROVAL"))               $txt_const .= f_add_file_missing("_BOOKMARKS_NEED_APPROVAL", "2.0.3");
if (!defined("_SHOUTBOX_PUBLIC"))                       $txt_const .= f_add_file_missing("_SHOUTBOX_PUBLIC", "2.0.3");
if (!defined("_TIME_ZONES"))                            $txt_const .= f_add_file_missing("_TIME_ZONES", "2.0.3");
if (!defined("_CHECK_VERSION_INTERNET"))                $txt_const .= f_add_file_missing("_CHECK_VERSION_INTERNET", "08/2010");
if (!defined("_SERVERS_STATUS"))                        $txt_const .= f_add_file_missing("_SERVERS_STATUS", "08/2010");
if (!defined("_SHOUTBOX_REFRESH_DELAY"))                $txt_const .= f_add_file_missing("_SHOUTBOX_REFRESH_DELAY", "08/2010");
if (!defined("_GROUP_FOR_SBX_AND_ADMIN_MSG"))           $txt_const .= f_add_file_missing("_GROUP_FOR_SBX_AND_ADMIN_MSG", "08/2010");  // _GROUP_FOR_ADMIN_MESSAGES
if (!defined("_GROUP_USER_CAN_JOIN"))                   $txt_const .= f_add_file_missing("_GROUP_USER_CAN_JOIN", "07/2010");
if (!defined("_SHOUTBOX_LOCK_USER_VOTES"))              $txt_const .= f_add_file_missing("_SHOUTBOX_LOCK_USER_VOTES", "07/2010");
if (!defined("_SHOUTBOX_REMOVE_MESSAGE_VOTES"))         $txt_const .= f_add_file_missing("_SHOUTBOX_REMOVE_MESSAGE_VOTES", "07/2010");
if (!defined("_SHOUTBOX_MAX_NOTES_USER_WEEK"))          $txt_const .= f_add_file_missing("_SHOUTBOX_MAX_NOTES_USER_WEEK", "07/2010");
if (!defined("_SHOUTBOX_MAX_NOTES_USER_DAY"))           $txt_const .= f_add_file_missing("_SHOUTBOX_MAX_NOTES_USER_DAY", "07/2010");
if (!defined("_SHOUTBOX_VOTE"))                         $txt_const .= f_add_file_missing("_SHOUTBOX_VOTE", "07/2010");
if (!defined("_SHOUTBOX_LOCK_USER_APPROVAL"))           $txt_const .= f_add_file_missing("_SHOUTBOX_LOCK_USER_APPROVAL", "07/2010");
if (!defined("_SHOUTBOX_APPROVAL_QUEUE"))               $txt_const .= f_add_file_missing("_SHOUTBOX_APPROVAL_QUEUE", "07/2010");
if (!defined("_SHOUTBOX_APPROVAL_QUEUE_USER"))          $txt_const .= f_add_file_missing("_SHOUTBOX_APPROVAL_QUEUE_USER", "07/2010");
if (!defined("_SHOUTBOX_NEED_APPROVAL"))                $txt_const .= f_add_file_missing("_SHOUTBOX_NEED_APPROVAL", "07/2010");
if (!defined("_SHOUTBOX_QUOTA_USER_WEEK"))              $txt_const .= f_add_file_missing("_SHOUTBOX_QUOTA_USER_WEEK", "07/2010");
if (!defined("_SHOUTBOX_QUOTA_USER_DAY"))               $txt_const .= f_add_file_missing("_SHOUTBOX_QUOTA_USER_DAY", "07/2010");
if (!defined("_SHOUTBOX_STORE_MAX"))                    $txt_const .= f_add_file_missing("_SHOUTBOX_STORE_MAX", "07/2010");
if (!defined("_SHOUTBOX_STORE_DAYS"))                   $txt_const .= f_add_file_missing("_SHOUTBOX_STORE_DAYS", "07/2010");
if (!defined("_SHOUTBOX"))                              $txt_const .= f_add_file_missing("_SHOUTBOX", "07/2010");
if (!defined("_PWD_NEED_SPECIAL_CHARACTER"))            $txt_const .= f_add_file_missing("_PWD_NEED_SPECIAL_CHARACTER", "07/2010");
if (!defined("_PWD_NEED_UPPER_LOWER"))                  $txt_const .= f_add_file_missing("_PWD_NEED_UPPER_LOWER", "07/2010");
if (!defined("_PWD_NEED_DIGIT_LETTER"))                 $txt_const .= f_add_file_missing("_PWD_NEED_DIGIT_LETTER", "07/2010");
if (!defined("_CENSOR_MESSAGES"))                       $txt_const .= f_add_file_missing("_CENSOR_MESSAGES", "06/2010");
if (!defined("_ALLOW_UPPERCASE_SPACE_USERNAME"))        $txt_const .= f_add_file_missing("_ALLOW_UPPERCASE_SPACE_USERNAME", "02/2010");
if (!defined("_SITE_TITLE"))                            $txt_const .= f_add_file_missing("_SITE_TITLE", "02/2010");
if (!defined("_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER")) $txt_const .= f_add_file_missing("_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER", "02/2010");
if (!defined("_EXTERNAL_AUTHENTICATION"))               $txt_const .= f_add_file_missing("_EXTERNAL_AUTHENTICATION", "01/2010");
if (!defined("_EXTERN_URL_CHANGE_PASSWORD"))            $txt_const .= f_add_file_missing("_EXTERN_URL_CHANGE_PASSWORD", "11/2009");
#if (!defined("_FULL_CHECK"))                            $txt_const .= f_add_file_missing("_FULL_CHECK", "11/2009");
#if (!defined("_GROUP_FOR_ADMIN_MESSAGES"))              $txt_const .= f_add_file_missing("_GROUP_FOR_ADMIN_MESSAGES", "11/2009");
if (!defined("_MAX_NB_IP"))                             $txt_const .= f_add_file_missing("_MAX_NB_IP", "11/2009");
if (!defined("_PROXY_PORT_NUMBER"))                     $txt_const .= f_add_file_missing("_PROXY_PORT_NUMBER", "11/2009");
if (!defined("_PROXY_ADDRESS"))                         $txt_const .= f_add_file_missing("_PROXY_ADDRESS", "11/2009");
if (!defined("_INCOMING_EMAIL_SERVER_ADDRESS"))         $txt_const .= f_add_file_missing("_INCOMING_EMAIL_SERVER_ADDRESS", "11/2009");
#if (!defined("_ALLOW_USER_RATING"))                     $txt_const .= f_add_file_missing("_ALLOW_USER_RATING", "11/2009");
if (!defined("_ENTERPRISE_SERVER"))                     $txt_const .= f_add_file_missing("_ENTERPRISE_SERVER", "11/2009");
if (!defined("_SCROLL_TEXT"))                           $txt_const .= f_add_file_missing("_SCROLL_TEXT", "11/2009");
if (!defined("_PUBLIC_FOLDER"))                         $txt_const .= f_add_file_missing("_PUBLIC_FOLDER", "11/2009");
if (!defined("_PUBLIC_OPTIONS_LIST"))                   $txt_const .= f_add_file_missing("_PUBLIC_OPTIONS_LIST", "11/2009");
if (!defined("_PUBLIC_USERS_LIST"))                     $txt_const .= f_add_file_missing("_PUBLIC_USERS_LIST", "11/2009");
if (!defined("_PUBLIC_POST_AVATAR"))                    $txt_const .= f_add_file_missing("_PUBLIC_POST_AVATAR", "11/2009");
if (!defined("_FORCE_UPDATE_BY_INTERNET"))              $txt_const .= f_add_file_missing("_FORCE_UPDATE_BY_INTERNET", "11/2009");
if (!defined("_ALLOW_EMAIL_NOTIFIER"))                  $txt_const .= f_add_file_missing("_ALLOW_EMAIL_NOTIFIER", "11/2009");
if (!defined("_EXTERN_URL_FORGET_PASSWORD"))            $txt_const .= f_add_file_missing("_EXTERN_URL_FORGET_PASSWORD", "12/2008");
if (!defined("_SEND_ADMIN_ALERT"))                      $txt_const .= f_add_file_missing("_SEND_ADMIN_ALERT", "09/2008");
if (!defined("_FORCE_STATUS_LIST_FROM_SERVER"))         $txt_const .= f_add_file_missing("_FORCE_STATUS_LIST_FROM_SERVER", "09/2008");
if (!defined("_AWAY_REASONS_LIST"))                     $txt_const .= f_add_file_missing("_AWAY_REASONS_LIST", "09/2008");
if (!defined("_ALLOW_USE_PROXY"))                       $txt_const .= f_add_file_missing("_ALLOW_USE_PROXY", "09/2008");
if (!defined("_ALLOW_CHANGE_AVATAR"))                   $txt_const .= f_add_file_missing("_ALLOW_CHANGE_AVATAR", "09/2008");
if (!defined("_ALLOW_SMILEYS"))                         $txt_const .= f_add_file_missing("_ALLOW_SMILEYS", "08/2008");
if (!defined("_MAINTENANCE_MODE"))                      $txt_const .= f_add_file_missing("_MAINTENANCE_MODE", "08/2008");
if (!defined("_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER"))  $txt_const .= f_add_file_missing("_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", "08/2008");
if (!defined("_PASSWORD_FOR_PRIVATE_SERVER"))           $txt_const .= f_add_file_missing("_PASSWORD_FOR_PRIVATE_SERVER", "08/2008");
#if (!defined("_HIDE_COL_FUNCTION_NAME"))                $txt_const .= f_add_file_missing("_HIDE_COL_FUNCTION_NAME", "08/2008");
#if (!defined("_LOCK_USER_OPTIONS"))                     $txt_const .= f_add_file_missing("_LOCK_USER_OPTIONS", "08/2008");
#if (!defined("_LOCK_USER_CONTACT_LIST"))                $txt_const .= f_add_file_missing("_LOCK_USER_CONTACT_LIST", "08/2008");
if (!defined("_IM_ADDRESS_BOOK_PASSWORD"))              $txt_const .= f_add_file_missing("_IM_ADDRESS_BOOK_PASSWORD", "08/2008");
if (!defined("_SPECIAL_MODE_GROUP_COMMUNITY"))          $txt_const .= f_add_file_missing("_SPECIAL_MODE_GROUP_COMMUNITY", "08/2008");
if (!defined("_SPECIAL_MODE_OPEN_COMMUNITY"))           $txt_const .= f_add_file_missing("_SPECIAL_MODE_OPEN_COMMUNITY", "08/2008");
if (!defined("_SITE_TITLE_TO_SHOW"))                    $txt_const .= f_add_file_missing("_SITE_TITLE_TO_SHOW", "08/2008");
if (!defined("_SITE_URL_TO_SHOW"))                      $txt_const .= f_add_file_missing("_SITE_URL_TO_SHOW", "08/2008");
if (!defined("_LOG_SESSION_OPEN"))                      $txt_const .= f_add_file_missing("_LOG_SESSION_OPEN", "08/2008");
#if (!defined("_LOG_MESSAGES"))                          $txt_const .= f_add_file_missing("_LOG_MESSAGES", "08/2008");
#if (!defined("_ALLOW_USER_TO_HISTORY_MESSAGES"))        $txt_const .= f_add_file_missing("_ALLOW_USER_TO_HISTORY_MESSAGES", "08/2008");
if (!defined("_CRYPT_MESSAGES"))                        $txt_const .= f_add_file_missing("_CRYPT_MESSAGES", "08/2008");
if (!defined("_PENDING_USER_ON_COMPUTER_CHANGE"))       $txt_const .= f_add_file_missing("_PENDING_USER_ON_COMPUTER_CHANGE", "08/2008");
if (!defined("_PENDING_NEW_AUTO_ADDED_USER"))           $txt_const .= f_add_file_missing("_PENDING_NEW_AUTO_ADDED_USER", "08/2008");
if (!defined("_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER"))     $txt_const .= f_add_file_missing("_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER", "08/2008");
if (!defined("_MINIMUM_PASSWORD_LENGTH"))               $txt_const .= f_add_file_missing("_MINIMUM_PASSWORD_LENGTH", "08/2008");
if (!defined("_USER_NEED_PASSWORD"))                    $txt_const .= f_add_file_missing("_USER_NEED_PASSWORD", "08/2008");
if (!defined("_MINIMUM_USERNAME_LENGTH"))               $txt_const .= f_add_file_missing("_MINIMUM_USERNAME_LENGTH", "08/2008");
if (!defined("_FORCE_AWAY_ON_SCREENSAVER"))             $txt_const .= f_add_file_missing("_FORCE_AWAY_ON_SCREENSAVER", "08/2008");
if (!defined("_ALLOW_SEND_TO_OFFLINE_USER"))            $txt_const .= f_add_file_missing("_ALLOW_SEND_TO_OFFLINE_USER", "08/2008");
if (!defined("_ALLOW_CHANGE_CONTACT_NICKNAME"))         $txt_const .= f_add_file_missing("_ALLOW_CHANGE_CONTACT_NICKNAME", "08/2008");
#if (!defined("_ALLOW_INVISIBLE"))                       $txt_const .= f_add_file_missing("_ALLOW_INVISIBLE", "08/2008");
if (!defined("_ALLOW_CONFERENCE"))                      $txt_const .= f_add_file_missing("_ALLOW_CONFERENCE", "08/2008");
if (!defined("_FORCE_USERNAME_TO_PC_SESSION_NAME"))     $txt_const .= f_add_file_missing("_FORCE_USERNAME_TO_PC_SESSION_NAME", "08/2008");
if (!defined("_STATISTICS"))                            $txt_const .= f_add_file_missing("_STATISTICS", "08/2008");
if (!defined("_CHECK_NEW_MSG_EVERY"))                   $txt_const .= f_add_file_missing("_CHECK_NEW_MSG_EVERY", "08/2008");
#if (!defined("_OUTOFDATE_AFTER_X_DAYS_NOT_USE"))        $txt_const .= f_add_file_missing("_OUTOFDATE_AFTER_X_DAYS_NOT_USE", "08/2008");
#if (!defined("_DISPLAY_USER_FLAG_COUNTRY"))             $txt_const .= f_add_file_missing("_DISPLAY_USER_FLAG_COUNTRY", "08/2008");
if (!defined("_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN"))    $txt_const .= f_add_file_missing("_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", "08/2008");
if (!defined("_MAX_NB_CONTACT_BY_USER"))                $txt_const .= f_add_file_missing("_MAX_NB_CONTACT_BY_USER", "08/2008");
if (!defined("_MAX_NB_SESSION"))                        $txt_const .= f_add_file_missing("_MAX_NB_SESSION", "08/2008");
if (!defined("_MAX_NB_USER"))                           $txt_const .= f_add_file_missing("_MAX_NB_USER", "08/2008");
if (!defined("_ADMIN_PHONE"))                           $txt_const .= f_add_file_missing("_ADMIN_PHONE", "05/2008");
if (!defined("_ADMIN_EMAIL"))                           $txt_const .= f_add_file_missing("_ADMIN_EMAIL", "05/2008");
if (!defined("_FORCE_UPDATE_BY_SERVER"))                $txt_const .= f_add_file_missing("_FORCE_UPDATE_BY_SERVER", "02/2008");
if (!defined("_EXTERN_URL_TO_REGISTER"))                $txt_const .= f_add_file_missing("_EXTERN_URL_TO_REGISTER", "02/2008");
if (!defined("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL"))  $txt_const .= f_add_file_missing("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL", "08/2008");
//
//
//
echo "<title>[IntraMessenger] " . $l_admin_check_title . "</title>";
require ("../common/menu.inc.php"); // après config.inc.php !  et APRES les test d'existantes des constantes !!!!
display_header();
echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//
function table_title($title)
{
	echo "<SMALL><BR/></SMALL>";
	echo "<table width='670' cellspacing='1' cellpadding='1' class='forumline'>";
	echo "<TR>";
	echo "<TH colspan='2' class='thHead'>";
	echo "<FONT size='3'>";
	echo $title;
	echo "</TH>";
	echo "</TR>";
}
//
function table_col_1($text)
{
	echo "<TR>";
	echo "<TD width='' class='row2'>";
	echo "<FONT size='2'>";
	echo $text;
	echo "</TD>";
}
//
function table_col_2($etat)
{
	echo "<TD width='20' class='row1' ALIGN='CENTER'>";
	if ($etat == 'OK')
		echo "<IMG SRC='" . _FOLDER_IMAGES . "ok.gif' WIDTH='16' HEIGHT='17' ALT='OK' TITLE='OK'>";
	else
		echo "<IMG SRC='" . _FOLDER_IMAGES . "ko.gif' WIDTH='17' HEIGHT='17' ALT='Not OK !' TITLE='Not OK !'>";
	//
	echo "</TD>";
	echo "</TR>";
}
//
function table_col_vide()
{
	echo "<TD width='20' class='row1' ALIGN='CENTER'>";
  echo "&nbsp;";
	echo "</TD>";
	echo "</TR>";
}
//
function f_add_file_missing($const, $dt_add)
{
	$t = "<I>" . $const . "</I> (added: " . $dt_add . ") : <FONT color='RED'><B>missing</B></FONT><BR/>";
	//
	return $t;
}
//
function table_time_zone($time_server_php, $time_server_mysql)
{
  table_title("Time");
  //
  $txt = "";
  if (ini_get('date.timezone')) 
  {
      $txt .= "PHP date.timezone: " . ini_get('date.timezone') . "<br/>";
  }
  $txt .= "PHP Timezone: " . date('e') . " [" . date('T') . "]<br/>";
  //if ($time_server_php <> $time_server_mysql) $txt .= "<font color='red'>";
  if ($time_server_php <> $time_server_mysql) $txt .= "<div class='warning'>";
  $txt .= "PHP Time: <b>" . $time_server_php . "</b> <font color= 'gray'>(if not OK, you may configure PHP: [Date] date.timezone)</font><br/>";
  $txt .= "MySQL time: <b>" . $time_server_mysql . "</b> <br/>";
  if ($time_server_php <> $time_server_mysql) $txt .= "</div>";
  //
  table_col_1($txt);
  table_col_vide();
  echo "</TABLE>";
}
//
//
$c_OK = "<B><FONT COLOR='GREEN'>OK</B></FONT>";
$c_not_found = "<B><FONT COLOR='RED'><BLINK>" . $l_admin_check_not_found . "</BLINK></FONT></B>";
$c_found = "<B><FONT COLOR='GREEN'>" . $l_admin_check_found . "</FONT></B>";
$c_on_ok = "<B><FONT COLOR='GREEN'>" . $l_admin_check_on . "</FONT></B>";
$c_on_ko = "<B><FONT COLOR='RED'>" . $l_admin_check_on . "</FONT></B>";
$c_off_ko = "<B><FONT COLOR='RED'>" . $l_admin_check_off . "</FONT></B>";
$c_off_ok = "<B><FONT COLOR='GREEN'>" . $l_admin_check_off . "</FONT></B>";
$if_prob = "OK";
//

echo "&nbsp;";
if ($lang != "EN") echo "<A href='?lang=EN&'><IMG SRC='../images/flags/gb.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> "; // <A href='?lang=EN&'>English</A> 
if ($lang != "FR") echo "<A href='?lang=FR&'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> "; // <A href='?lang=FR&'>En français</A> 
if ($lang != "IT") echo "<A href='?lang=IT&'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;"; // <A href='?lang=IT&'>Italiano</A>
if ($lang != 'ES') echo "<A HREF='?lang=ES&'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
if ($lang != "PT") echo "<A href='?lang=PT&'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
if ($lang != "BR") echo "<A href='?lang=BR&'><IMG SRC='../images/flags/br.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
if ($lang != "RO") echo "<A href='?lang=RO&'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
if ($lang != "DE") echo "<A href='?lang=DE&'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
if ($lang != "NL") echo "<A href='?lang=NL&'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";

//echo "<BR/>";
echo "<CENTER>";
switch ($lang)
{
	case "FR" :
    $file_doc = "../doc/fr/versions.html";
    $file_install = "../doc/fr/install.html";
		break;
  /*
	case "IT" :
    $file_doc = "../doc/it/versioni.html";
    $file_install = "../doc/it/install.html";
		break;
	*/
	default : // EN
    $file_doc = "../doc/en/changelog.html";
    $file_install = "../doc/en/install.html";
		break;
}
/*
echo "<div class='info'>";
echo "<FONT color='blue'><B><BLINK>" . $l_admin_check_before_upgrade . "</BLINK></B>, " . $l_admin_check_read_last ." <I>";
//
if (is_readable($file_doc)) 
  echo  "<A HREF='" . $file_doc. "' target='_blank'>";
//
echo $file_doc . "</I></A>";
echo " and <I>";
if (is_readable($file_install)) 
  echo  "<A HREF='" . $file_install . "' target='_blank'>";
//
echo $file_install . "</I></A>";
echo "</FONT><BR/>";
echo "</div>";
*/
//
//
table_title($l_admin_check_title);
//
if (!is_readable("../common/config/config.inc.php")) 
	$if_prob = "KO";
//
$txt = $l_admin_check_conf_file . " (<I>/common/config/config.inc.php</I>) : ";
if ($if_prob == "OK") 
	$txt .= $c_found;
else
	$txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found;
//
table_col_1($txt);
table_col_2($if_prob);
//

if (!is_readable("../common/config/extern.config.inc.php")) 
	$if_prob = "KO";
//
$txt = $l_admin_check_conf_file . " (<I>/common/config/extern.config.inc.php</I>) : ";
if ($if_prob == "OK") 
	$txt .= $c_found;
else
{
	$txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found;
  if ($lang == "FR")
    $txt .= "</B><BR/> Si vous utilisez l'authentification externe, déplacez votre fichier de configuration <BR/><I>/config/extern/***.config.inc.php</I> &nbsp;<B>vers</B>&nbsp; <I>/config/extern.config.inc.php</I> <BR/> et renommer l'option \$table_prefix en \$extern_prefix !";
  else
    $txt .= "</B><BR/> If you use extern authentication, move your configuration file <BR/><I>/config/extern/***.config.inc.php</I> &nbsp;<B>to</B>&nbsp; <I>/config/extern.config.inc.php</I> <BR/> and rename option \$table_prefix to \$extern_prefix !";
}
//
table_col_1($txt);
table_col_2($if_prob);
//





if ($if_prob != "OK") 
{
	echo '</TABLE>';
	echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " access to configuration file</p></div>";
	echo "</body></html>";
	die();
}
//
//
//require ("../common/config/config.inc.php");
//require ("lang.inc.php");
//
if ( isset($l_lang_name) and defined("_LANG") )
{
	if ( ($l_lang_name == "") or (_LANG == "") )
		$if_prob = "KO";
}
else
	$if_prob = "KO";
//
$txt = $l_language . " : ";
if ($if_prob == "OK") 
{
	$txt .= "<FONT COLOR='GREEN'><I><B>";
	if ( ($lang == "") or ($lang == _LANG) )
    $txt .= $l_lang_name . " (" . _LANG . ")";
	else
    $txt .= _LANG;
}
else
	$txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_not_found;
//
table_col_1($txt);
table_col_2($if_prob);
//
if ($if_prob != "OK") 
{
	echo '</TABLE>';
	echo "<div class='warning'><p class='error'>"  . $l_admin_check_cannot_continue . " " . $l_admin_check_missing_option . "</p></div>";
	echo "</body></html>";
	die();
}
//
//
echo "</TABLE>";
//
//
table_title($l_admin_check_last_options);
$txt = $txt_const;
if ($txt != "")
	$if_prob_const = "KO";
else
	$txt = $l_admin_check_new_options_are . " " . $c_OK . " " . $l_admin_check_in_conf_file;
//
table_col_1($txt);
table_col_2($if_prob_const);
if ($if_prob_const != "OK")
{
  echo "<FORM METHOD='GET' ACTION ='list_options_updating.php?'>";
	echo "<TR><TD COLSPAN='2' class='row1'>";
	//echo "<FONT size='2' color='BLUE'>" . $l_admin_options_missing_option . " <I>../common/config/config.inc.php</I> &nbsp; </font>"; // <BR/>
	echo "<FONT size='2' color='BLUE'>" . $l_admin_options_missing_option . " " . $l_admin_options_conf_file . "&nbsp; </font><BR/>"; // 
	echo "<strong>" . $l_admin_check_fix_missing_option . "</strong> : ";
	if (is_writeable("../common/config/config.inc.php"))
  {
    //echo "Save options on this screen (and come back) : ";
    //echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_options_update . "' />";
    echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_options_bt_update . "' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "<INPUT TYPE='hidden' name='check' value = 'update' />";
    //echo "&nbsp; <B><A HREF='list_options_updating.php?lang=" . $lang . "&check=update&#save'>" . $l_admin_options_update . "</A></B>";
  }
  else
  {
    echo "<FONT size='2' color='red'><I>../common/config/config.inc.php</I> " . $l_admin_check_not_writeable . " ! &nbsp; </font>"; // <BR/>
  }
	echo "</TD></TR>";
  echo "</FORM>";
  //
	echo '</TABLE>';
	//echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_missing_option . "</p></div>";
	echo "<br/><div class='info'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_missing_option . "...</div>";
	echo "</body></html>";
	die();
}

//
echo "</TABLE>";
//

//
//
table_title($l_admin_check_mysql);
//
require ("../common/config/mysql.config.inc.php");
//
$id_connect = mysqli_connect($dbhost, $dbuname, $dbpass);
$txt = $l_admin_check_connect_server . " : ";
if (!$id_connect) 
{
	$if_prob = "KO";
	$txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_failed . "</B> : " . mysqli_error($id_connect);
}
else
	$txt .= $c_OK;
//
table_col_1($txt);
table_col_2($if_prob);
//
if ($if_prob != "OK") 
{
	echo '</TABLE>';
	echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_connect_to_server . "</p></div>";
	echo "</body></html>";
	die();
}
//
//
$requete = "SELECT VERSION()";
$result = mysqli_query($id_connect, $requete);
if (!$result) 
{
	$if_prob = "KO";
	$txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_failed . "</B> : " . mysqli_error($id_connect);
}
else
{
	if ( mysqli_num_rows($result) == 1 )
	{
		list ($version) = mysqli_fetch_row ($result);
		$txt = $l_admin_check_version . " : ";
		$txt .= "<FONT COLOR='GREEN'><I>" . $version . "</I></FONT>";
	}
}
table_col_1($txt);
table_col_2($if_prob);
//
if ($if_prob != "OK") 
{
	echo "</TABLE>";
	echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_connect_to_server . "</p></div>";
	echo "</body></html>";
	die();
}
//
//
//$db_selected  = mysql_select_db($database, $id_connect);
$db_selected  = mysqli_select_db($id_connect, $database);
$txt = $l_admin_check_connect_database . " : ";
if (!$db_selected) 
{
	$if_prob = "KO";
	$txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_failed . "</B> : " . mysqli_error($id_connect);
}
else
	$txt .= $c_OK;
//
table_col_1($txt);
table_col_2($if_prob);
//
if ($if_prob != "OK") 
{
	echo "</TABLE>";
	echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_connect_to_database . "</p></div>";
	echo "</body></html>";
	die();
}

if (!isset($PREFIX_IM_TABLE))
{
  $txt = f_add_file_missing("PREFIX_IM_TABLE", "03/2008");
  table_col_1($txt);
  table_col_2("KO");
	echo "<TR><TD COLSPAN='2' class='catBottom'>";
	echo "<FONT size='2' color='BLUE'>" . $l_admin_check_option_missing . " <I>../common/config/mysql.config.inc.php</I>";
	echo "</TD></TR>";
	echo "</TABLE>";
	echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_missing_option . "</p></div>";
	echo "</body></html>";
	die();
}
//
//
echo "</TABLE>";
//
//
table_title($l_admin_check_tables_list);
//
//
$txt = "";
$if_prob_table = "OK";
$arrTableInit = array("#" . $PREFIX_IM_TABLE . "CNT_CONTACT#","#" . $PREFIX_IM_TABLE . "MSG_MESSAGE#","#" . $PREFIX_IM_TABLE . "SES_SESSION#",
                "#" . $PREFIX_IM_TABLE . "USR_USER#", "#" . $PREFIX_IM_TABLE . "USG_USERGRP#", "#" . $PREFIX_IM_TABLE . "GRP_GROUP#", 
                "#" . $PREFIX_IM_TABLE . "STA_STATS#", "#" . $PREFIX_IM_TABLE . "CNF_CONFERENCE#", "#" . $PREFIX_IM_TABLE . "USC_USERCONF#", 
                "#" . $PREFIX_IM_TABLE . "BAN_BANNED#", "#" . $PREFIX_IM_TABLE . "SRV_SERVERSTATE#", "#" . $PREFIX_IM_TABLE . "SBX_SHOUTBOX#", 
                "#" . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS#", "#" . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE#", 
                "#" . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG#", "#" . $PREFIX_IM_TABLE . "BMK_BOOKMARK#", "#" . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE#",
                "#" . $PREFIX_IM_TABLE . "ROL_ROLE#", "#" . $PREFIX_IM_TABLE . "MDL_MODULE#", "#" . $PREFIX_IM_TABLE . "RLM_ROLEMODULE#",
                "#" . $PREFIX_IM_TABLE . "FMD_FILEMEDIA#", "#" . $PREFIX_IM_TABLE . "FPJ_FILEPROJET#", "#" . $PREFIX_IM_TABLE . "FIL_FILE#",
                "#" . $PREFIX_IM_TABLE . "FLV_FILEVOTE#", "#" . $PREFIX_IM_TABLE . "FST_FILESTATS#", "#" . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD#",
                "#" . $PREFIX_IM_TABLE . "ADM_ADMINACP#", "#" . $PREFIX_IM_TABLE . "FIB_FILEBACKUP#"); 
                //     , "#" . $PREFIX_IM_TABLE . "zzzzzzzzzz#" , "#" . $PREFIX_IM_TABLE . "zzzzzzzzzz#" , "#" . $PREFIX_IM_TABLE . "zzzzzzzzzz#" 
                //     , "#" . $PREFIX_IM_TABLE . "zzzzzzzzzz#" , "#" . $PREFIX_IM_TABLE . "zzzzzzzzzz#" , "#" . $PREFIX_IM_TABLE . "zzzzzzzzzz#" 
$requete = "SHOW TABLES";
$result = mysqli_query($id_connect, $requete);
if (!$result) 
	$txt = '<span class="error">Cannot retreive list of tables</span></li>';
//
$table_exists = "##"; // pas vide ! (2 sinon, trouve pas la 1ère table
if ( mysqli_num_rows($result) != 0 )
{
	while( list ($table1) = mysqli_fetch_row ($result) )
	{
		$table_exists .= $table1 . "#";
	}
	foreach($arrTableInit as $table1) 
	{
		$table_aff = str_replace("#", "", $table1); // enlever les #
		#$txt .= "Table <I>" . $table_aff . "</I> : "; ///
		if ( strstr(strtolower($table_exists), strtolower($table1)) )
			#$txt .= $c_OK; ///
			$txt .= "";
		else
		{
			$txt .= $l_admin_check_table . ": <I>" . $table_aff . "</I> "; //  . $table1 . " ";
			$txt .= $c_not_found;
			$if_prob_table = "KO";
			$txt .= '<BR/>';
		}
		#$txt .= '<BR/>'; ///
	}
}
else
	$if_prob_table = "KO";
//
if ($txt == '') 
{
  if ($if_prob_table == "OK")
    $txt = $l_admin_check_tables_ok;
  else
    $txt = "Tables NOT all exist !";
}
//
table_col_1($txt);
table_col_2($if_prob_table);
if ($if_prob_table != "OK")
{
	$if_prob = "KO";
	echo "<TR><TD COLSPAN='2' class='row1'>";
    echo "<FORM METHOD='GET' ACTION='create_tables.php?'>";
    echo "<font face='verdana' size='2'>";
    echo "<input type='radio' name='dbengine' CHECKED value='' /><B>Default</B> <BR/>";
    echo "<input type='radio' name='dbengine' value='myisam' />MyISAM ";
    echo "&nbsp; <INPUT TYPE='submit' VALUE = '" . $l_admin_bt_create . "' class='mainoption' /> <BR/>"; // liteoption
    echo "<input type='radio' name='dbengine' value='innodb' />InnoDB <BR/>";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
	echo "</TD></TR>";
  //
	echo "<TR><TD COLSPAN='2' class='catBottom'>";
    echo "<FONT size='2'> " . $l_admin_check_use . " ";
    //echo "<A HREF='create_tables.php' target='blank'>";
    //echo "create_tables.php</I></A></B><br/>";
    echo "</FONT><FONT color='gray' size='2'> <I>/install/install.sql</I> ";
    echo $l_admin_check_in_admin . " " . $l_admin_check_to_create_table; 
	echo "</TD></TR>";
}

//
if ($if_prob != "OK") 
{
	echo '</TABLE>';
	//echo "<div class='warning'><p class='error'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_all_tables . "</p></div>";
	echo "<br/><div class='info'>" . $l_admin_check_cannot_continue . " " . $l_admin_check_all_tables . "...</div>";
	echo "</body></html>";
	die();
}
//
//
//
echo "</TABLE>";
//
table_title($l_admin_check_optimize_tables);
//
//
$txt = "";
$if_prob_optimize = "OK";
$arrTableInit = array('#CNT_CONTACT#','#MSG_MESSAGE#','#SES_SESSION#','#USR_USER#', '#USG_USERGRP#', '#GRP_GROUP#', 
                    '#STA_STATS#', '#CNF_CONFERENCE#', '#USC_USERCONF#', '#BAN_BANNED#', '#SRV_SERVERSTATE#', 
                    '#SBX_SHOUTBOX#', '#SBS_SHOUTSTATS#', '#SBV_SHOUTVOTE#', 
                    '#BMC_BOOKMCATEG#', '#BMK_BOOKMARK#', '#BMV_BOOKMVOTE#',
                    '#ROL_ROLE#', '#MDL_MODULE#', '#RLM_ROLEMODULE#',
                    '#FMD_FILEMEDIA#', '#FPJ_FILEPROJET#', '#FIL_FILE#', '#FLV_FILEVOTE#', "#FST_FILESTATS#", '#FSD_FILESTATSDOWNLOAD#',
                    '#ADM_ADMINACP#', '#FIB_FILEBACKUP#'); 
                    //  , '#xxxxxxxxxxxx#'     , '#xxxxxxxxxxxx#'      
                    //  , '#xxxxxxxxxxxx#'     , '#xxxxxxxxxxxx#'      
//
foreach($arrTableInit as $table) 
{
  $table_aff = str_replace("#", "", $table); // enlever les #
  //
  $requete = "ANALYZE TABLE " . $PREFIX_IM_TABLE . $table_aff;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) 
    $txt = '<span class="error">cannot analyse table ' . $PREFIX_IM_TABLE . $table_aff . '</span><BR/>';
  //
  $requete = "OPTIMIZE TABLE " . $PREFIX_IM_TABLE . $table_aff;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) 
    $txt = '<span class="error">cannot optimize table ' . $PREFIX_IM_TABLE . $table_aff . '</span><BR/>';
  //
  $requete = "CHECK TABLE " . $PREFIX_IM_TABLE . $table_aff . " EXTENDED ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) 
    $txt = '<span class="error">cannot check table ' . $PREFIX_IM_TABLE . $table_aff . '</span><BR/>';
}
//
if ($txt == '') $txt = "<font color='green'>" . $l_admin_check_tables_are_optimized . "</font> (on problem, try to <A HREF='check_repair.php?lang=" . $lang . "&'>repair</A>)"; 
else 
{
  $if_prob_optimize = "KO";
  $txt .= "Maybe you not use MyIsam tables, or dont have enough rights (on problem, try to <A HREF='check_repair.php?lang=" . $lang . "&'>repair</A>).<BR/>";
}
//
table_col_1($txt);
table_col_2($if_prob_optimize);
//table_col_vide();
////
//
echo "</TABLE>";
//
table_title($l_admin_check_tables_structure);
//
function f_test_table($table, $champs, $added, $file_sql, $try)
{
  GLOBAL $lang, $c_not_found, $PREFIX_IM_TABLE, $id_connect, $l_admin_check_table, $l_admin_check_col, $l_admin_check_use, $l_admin_check_in_admin, 
          $l_admin_check_for_structure, $l_admin_check_update_now, $l_home_or;
  $txt = "<UL>";
  $requete = "select " . $champs . " FROM " . $PREFIX_IM_TABLE . $table . " LIMIT 0, 30 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) 
  {
    $txt .= "<LI> " . $l_admin_check_table . " <I>" . $PREFIX_IM_TABLE . $table . "</I> " . $l_admin_check_col . " <I>" . $champs . "</I> (added " . $added . "): " . $c_not_found . "<BR/>";
    if ($try != '')
      $txt .= " => <B><A HREF='update_table.php?action=" . $try . "&lang=" . $lang . "&'>" . $l_admin_check_update_now . "</A></B> <br/><font color='gray'>". $l_home_or . " ";
    else
      $txt .= " &nbsp; - ";
    //
    $txt .= $l_admin_check_use . " <I>";
    //
    if (is_readable("../install/updates/" . $file_sql . "")) 
      $txt .= "<A HREF='../install/updates/" . $file_sql . "' target='blank'>";
    //
    $txt .=  "/install/updates/" . $file_sql . "</I></A> " . $l_admin_check_in_admin . "</font>"; // . " " . $l_admin_check_for_structure . "</LI>";
  }
  //
  if ($txt == "<UL>")
    $txt = "";
  else
    $txt .= "</UL>";
  //
  return $txt;
}
//
$txt = "";
//
$txt .= f_test_table("USR_USER",    "USR_VERSION", "", "db_upgrade_01.sql", "1");
$txt .= f_test_table("MSG_MESSAGE", "MSG_DATE", "02/2007", "db_upgrade_02.sql", "2");
//$txt .= f_test_table("CNT_CONTACT", "CNT_PSEUDO", "03/2007", "db_upgrade_03.sql", "3"); // enlevé, sinon revient (car renommé en CNT_NEW_USERNAME).
$txt .= f_test_table("USR_USER",    "USR_LEVEL", "05/2007", "db_upgrade_04.sql", "4");
$txt .= f_test_table("USR_USER",    "USR_COUNTRY_CODE", "06/2007", "db_upgrade_05.sql", "5");
$txt .= f_test_table("USG_USERGRP", "ID_GROUP", "08/2007", "db_upgrade_06.sql", "6");
$txt .= f_test_table("GRP_GROUP",   "ID_GROUP", "08/2007", "db_upgrade_06.sql", "7");
$txt .= f_test_table("SES_SESSION", "SES_STARTDATE", "09/2007", "db_upgrade_08.sql", "8");
$txt .= f_test_table("STA_STATS",   "STA_NB_USR", "09/2007", "db_upgrade_09.sql", "9");
$txt .= f_test_table("MSG_MESSAGE", "ID_CONFERENCE", "12/2007", "db_upgrade_10.sql", "10");
$txt .= f_test_table("USR_USER",    "USR_AVATAR", "04/2008", "db_upgrade_11.sql", "11");
$txt .= f_test_table("USR_USER",    "USR_LANGUAGE_CODE", "04/2008", "db_upgrade_11.sql", "12");
$txt .= f_test_table("USR_USER",    "USR_TIME_SHIFT", "04/2008", "db_upgrade_12.sql", "13");
$txt .= f_test_table("MSG_MESSAGE", "MSG_TEXT", "08/2008", "db_upgrade_14.sql", "14");
$txt .= f_test_table("MSG_MESSAGE", "MSG_CR", "08/2008", "db_upgrade_14.sql", "15");
$txt .= f_test_table("USR_USER",    "USR_PWD_ERRORS", "08/2008", "db_upgrade_14.sql", "16");
$txt .= f_test_table("USR_USER",    "USR_EMAIL", "08/2008", "db_upgrade_14.sql", "17");
$txt .= f_test_table("USR_USER",    "USR_PHONE", "08/2008", "db_upgrade_14.sql", "18");
$txt .= f_test_table("USR_USER",    "USR_IP_ADDRESS", "08/2008", "db_upgrade_14.sql", "19");
$txt .= f_test_table("USR_USER",    "USR_OS", "08/2008", "db_upgrade_14.sql", "20");
$txt .= f_test_table("SES_SESSION", "SES_IP_ADDRESS", "08/2008", "db_upgrade_14.sql", "21");
$txt .= f_test_table("USR_USER",    "USR_GENDER", "08/2008", "db_upgrade_14.sql", "22");
$txt .= f_test_table("USR_USER",    "USR_NAME", "08/2008", "db_upgrade_14.sql", "23");
$txt .= f_test_table("USR_USER",    "USR_STATUS", "08/2008", "db_upgrade_14.sql", "24");
$txt .= f_test_table("CNT_CONTACT", "CNT_USER_GROUP", "08/2008", "db_upgrade_14.sql", "25");
$txt .= f_test_table("CNT_CONTACT", "CNT_STATUS", "08/2008", "db_upgrade_14.sql", "26");
$txt .= f_test_table("CNT_CONTACT", "CNT_NEW_USERNAME", "08/2008", "db_upgrade_14.sql", "27");
$txt .= f_test_table("SES_SESSION", "SES_STATUS", "08/2008", "db_upgrade_14.sql", "28");
$txt .= f_test_table("USR_USER",    "USR_NB_CONNECT", "09/2008", "db_upgrade_15.sql", "29");
$txt .= f_test_table("SES_SESSION", "SES_AWAY_REASON", "09/2008", "db_upgrade_15.sql", "30");
$txt .= f_test_table("USR_USER",    "USR_GET_ADMIN_ALERT", "09/2008", "db_upgrade_15.sql", "31");
#if (_AUTHENTICATION_ON_TRIADE != "")
if (_EXTERNAL_AUTHENTICATION == "triade")
  $txt .= f_test_table("USR_USER",  "USR_TRIADE_PHENIX", "01/2009", "db_upgrade_triade.sql", "32");
//
$txt .= f_test_table("BAN_BANNED",    "BAN_TYPE", "11/2009", "db_upgrade_16.sql", "33");
$txt .= f_test_table("USR_USER",      "USR_GET_OFFLINE_MSG", "11/2009", "db_upgrade_16.sql", "34");
$txt .= f_test_table("USR_USER",      "USR_MAC_ADR", "11/2009", "db_upgrade_16.sql", "35");
$txt .= f_test_table("USR_USER",      "USR_COMPUTERNAME", "11/2009", "db_upgrade_16.sql", "36");
$txt .= f_test_table("USR_USER",      "USR_SCREEN_SIZE", "11/2009", "db_upgrade_16.sql", "37");
$txt .= f_test_table("USR_USER",      "USR_EMAIL_CLIENT", "11/2009", "db_upgrade_16.sql", "38");
$txt .= f_test_table("USR_USER",      "USR_BROWSER", "11/2009", "db_upgrade_16.sql", "39");
$txt .= f_test_table("USR_USER",      "USR_OOO", "11/2009", "db_upgrade_16.sql", "40");
$txt .= f_test_table("USR_USER",      "USR_RATING", "11/2009", "db_upgrade_16.sql", "41");
$txt .= f_test_table("CNT_CONTACT",   "CNT_RATING", "11/2009", "db_upgrade_16.sql", "42");
$txt .= f_test_table("USR_USER",      "USR_ONLINE", "11/2009", "db_upgrade_16.sql", "43");
$txt .= f_test_table("USR_USER",      "USR_TIME_LOCK", "11/2009", "db_upgrade_16.sql", "44");
$txt .= f_test_table("USR_USER",      "USR_REG", "11/2009", "db_upgrade_16.sql", "45");
$txt .= f_test_table("USR_USER",      "USR_NICKNAME", "02/2010", "db_upgrade_17.sql", "46");
$txt .= f_test_table("USR_USER",      "USR_DATE_PASSWORD", "05/2010", "db_upgrade_18.sql", "47");
$txt .= f_test_table("USR_USER",      "USR_DATE_ACTIVITY", "05/2010", "db_upgrade_18.sql", "48");
$txt .= f_test_table("STA_STATS",     "STA_SBX_NB_MSG", "07/2010", "db_upgrade_18.sql", "49");
$txt .= f_test_table("GRP_GROUP",     "GRP_SHOUTBOX", "07/2010", "db_upgrade_18.sql", "49");
$txt .= f_test_table("GRP_GROUP",     "GRP_SBX_NEED_APPROVAL", "07/2010", "db_upgrade_18.sql", "49");
$txt .= f_test_table("USG_USERGRP",   "USG_PENDING", "08/2010", "db_upgrade_18.sql", "50");
$txt .= f_test_table("SBV_SHOUTVOTE", "SBV_VOTE_M", "08/2010", "db_upgrade_19.sql", "51");
$txt .= f_test_table("SBV_SHOUTVOTE", "SBV_VOTE_L", "08/2010", "db_upgrade_19.sql", "51");
$txt .= f_test_table("USR_USER",      "ID_ROLE", "07/2011", "db_upgrade_21.sql", "52");
$txt .= f_test_table("MDL_MODULE",    "MDL_MAX_VALUE", "07/2011", "db_upgrade_22.sql", "53");
$txt .= f_test_table("USR_USER",      "USR_DATE_BIRTH", "04/2012", "db_upgrade_23.sql", "54");
$txt .= f_test_table("STA_STATS",     "STA_SF_NB_SHARE", "06/2012", "db_upgrade_23.sql", "55");
$txt .= f_test_table("STA_STATS",     "STA_SF_NB_DOWNLOAD", "07/2012", "db_upgrade_24.sql", "56");
$txt .= f_test_table("FIL_FILE",      "FIL_COMPRESS", "07/2012", "db_upgrade_24.sql", "56");
$txt .= f_test_table("FIL_FILE",      "FIL_PROTECT", "07/2012", "db_upgrade_24.sql", "57");
$txt .= f_test_table("FIL_FILE",      "FIL_PASSWORD", "07/2012", "db_upgrade_24.sql", "57");
$txt .= f_test_table("USR_USER",      "USR_PASS_WEB", "07/2013", "db_upgrade_25.sql", "58");
$txt .= f_test_table("USR_USER",      "USR_PASS_SALT", "07/2013", "db_upgrade_25.sql", "58");
$txt .= f_test_table("USR_USER",      "USR_DATE_BACKUP", "08/2013", "db_upgrade_25.sql", "59");
//
//
//
//
if (is_readable("../common/config/version.tmp")) 
{
  if (!is_writeable("../common/config/version.tmp"))
  {
    $txt = "<I>/common/config/version.tmp</I> : ";
    $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " ! </font></B>(<B>chmod</B>)";
  }
}
else
{
  if ($fp = fopen("../common/config/version.tmp", "a")) fclose($fp);
  //
  $txt = "<I>/common/config/version.tmp</I> : ";
  $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !";
}
//
//
if (!is_readable("log/lastcheck.tmp")) 
{
  if ($fp = fopen("log/lastcheck.tmp", "a")) fclose($fp);
}
if (is_readable("log/lastcheck.tmp")) 
{
  if (!is_writeable("log/lastcheck.tmp"))
  {
    $txt = "<I>/admin/log/lastcheck.tmp</I> : ";
    $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " ! </font></B>(<B>chmod</B>)";
  }
}
else
{
  $txt = "<I>/admin/log/lastcheck.tmp</I> : ";
  $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !";
}
//
//
//
//
if ($txt != "")
	$if_prob = "KO";
else
	$txt = $l_admin_check_tables_structure_are . " " . $c_OK;
//
table_col_1($txt);
table_col_2($if_prob);
//
//
if ($if_prob != "OK") 
{
	echo '</TABLE>';
	//echo "<div class='warning'><p class='error'>" . $l_admin_check_conf_not_ok . " <BR/><BR/>";
	//echo "<B>" . $l_admin_check_tables_structure_are . " " . $l_admin_check_incomplete ." !</B> </p>";

	echo "<BR/><div class='info'>"; // . $l_admin_check_conf_not_ok . " <BR/><BR/>";
	echo $l_admin_check_tables_structure_are . " " . $l_admin_check_incomplete;

  echo "<br/>" . $l_admin_check_cannot_continue . ".";

	echo "</div>";
	echo "</body></html>";
	die();
}

//
//
//
//
//

//
echo "</TABLE>";
//
//
//
//
//
$txt = "";
$arrFolders = array('../distant/log/', '../distant/avatar/', '../' . _PUBLIC_FOLDER . '/log/', '../' . _PUBLIC_FOLDER . '/upload/', 'save/', 'log/', '../common/library/sypex_dumper/sxd/backup/');
foreach ($arrFolders as $folder) 
{
  if (is_dir($folder)) 
  {
		if ( (!is_writeable($folder)) or ( (!is_writeable($folder . "index.php")) and (!is_writeable($folder . "index.html")) ) )
		{
      $txt .= $l_admin_check_folder . " <I>" . $folder . "</I> : ";
			$txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " !</B></FONT> (chmod)<BR/>";
		}
	} 
  else 
  {
    $txt .= $l_admin_check_folder . " <I>" . $folder . "</I> : ";
		$txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . "</B></FONT><BR/>";
	}
}
//
if ($txt != "")
{
	if (_PUBLIC_FOLDER == "") 
    $txt .= "<FONT COLOR='RED'>Option <B>_PUBLIC_FOLDER</B> must be <B>/public/</B> folder !</FONT><BR/>";
	else
    if (strpos($txt, _PUBLIC_FOLDER)) $txt .= "<FONT COLOR='RED'>Option <B>_PUBLIC_FOLDER</B> must be <B>/public/</B> folder !</FONT><BR/>";
	//
	$if_prob = "KO";
  table_title($l_admin_check_folders);
  table_col_1($txt);
  table_col_2($if_prob);
  echo "</TABLE>";
  echo "\n";
}
//
//


//
//
$txt = "";
if ( (_SHARE_FILES != "") and (_SHARE_FILES_FTP_ADDRESS != "") and (_SHARE_FILES_FTP_LOGIN != "") and (_SHARE_FILES_FTP_PASSWORD_CRYPT != "") )
{
  if ( (_SHARE_FILES_FTP_PASSWORD != "") and (_SHARE_FILES_FOLDER == "") )
  {
    table_title($l_admin_share_files_title);
    //
    $nb_file_deleted = 0;
    $requete  = " SELECT ID_FILE, FIL_NAME ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE = 'D' ";
    $requete .= " and ID_PROJET is null ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result)  $txt = '<span class="error">cannot check FTP file list</span><BR/>';
    $nb_files_to_delete = mysqli_num_rows($result);
    if ($nb_files_to_delete > 0)
    {
      $port_num = intval(_SHARE_FILES_FTP_PORT_NUMBER);
      if ( ($port_num <= 0) or ($port_num > 65535) ) $port_num = 21;
      $conn_id = ftp_connect(_SHARE_FILES_FTP_ADDRESS, $port_num) or die("<span class='error'>Couldn't connect to FTP server!</span>"); 
      if (@ftp_login($conn_id, _SHARE_FILES_FTP_LOGIN, _SHARE_FILES_FTP_PASSWORD)) 
      {
        while( list ($id_file, $fil_name) = mysqli_fetch_row ($result) )
        {
          if ((ftp_size($conn_id, $fil_name)) > 0)
          {
            $txt .= $fil_name;
            if (ftp_delete($conn_id, $fil_name))
            {
              $txt .= " : deleted.<br/>";
              $nb_file_deleted++;
            }
            else
              $txt .= " : NOT deleted!<br/>";
          }
          else
            $nb_file_deleted++; // n'existe plus, donc on considère supprimé (afin de le supprimer de la base ensuite).
        }
      }
      else
      {
        $if_prob = "KO";
        $txt = "Failed to connect as " . _SHARE_FILES_FTP_LOGIN;
      }
      //
      ftp_close($conn_id);
      //
      if ($nb_file_deleted == $nb_files_to_delete)
      {
        $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
        $requete .= " WHERE FIL_ONLINE = 'D' ";
        $requete .= " and ID_PROJET is null ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result)  $txt = '<span class="error">cannot delete file list on database</span><BR/>';
      }
    }
    if ($txt == "") $txt = $l_admin_share_file_clean_deleted . " " . $c_OK;
    table_col_1($txt);
    table_col_2($if_prob);
    echo "</TABLE>";
    echo "\n";
  }
}
//
//





//
//
echo "</TABLE>";
echo "<font face='verdana' size='2'>";
//
//
if (!is_writeable("../common/config/config.inc.php"))
{
  echo "<BR/>";
  echo "<div class='warning'><font color='red'>" . $l_admin_options_conf_file . " <I>/common/config/config.inc.php</I> : " . $l_admin_check_not_writeable . " !</font></div>";
  die();
}
//
if (!is_readable("../common/config/config.inc.bak.php")) touch("../common/config/config.inc.bak.php");
if (!is_writeable("../common/config/config.inc.bak.php"))
{
  echo "<BR/>";
  echo "<div class='warning'>" . $l_admin_options_conf_file . " <I>/common/config/config.inc.bak.php</I> : " . $l_admin_check_not_writeable . ".</div>";
  die();
}
//
//

//
echo $l_admin_check_history . " : <I>";
if (is_readable($file_doc)) 
	echo  "<A HREF='" . $file_doc . "' target='_blank'>";
//
echo $file_doc . "</I></A>";
echo "<BR/>";
//
if (is_readable("../common/config/ban_nickname.txt")) 
{
  echo "<BR/><B><font color='red'>" . $l_admin_bt_delete . " : <A HREF='list_ban.php?ban=users&lang=" . $lang . "' target='_blank'>/common/config/ban_nickname.txt</A></font></B><BR/>";
}
if (is_readable("../common/config/ban_ip.txt")) 
{
  echo "<BR/><B><font color='red'>" . $l_admin_bt_delete . " : <A HREF='list_ban.php?ban=ip&lang=" . $lang . "' target='_blank'>/common/config/ban_ip.txt</A></font></B><BR/>";
}
//

if ($if_prob == "OK") 
{
  /*
  if (!is_readable("../im_setup.reg"))
  {
     $l_menu_need_reg = str_replace ("im_setup.reg", "<a href='reg.php?lang=" . $lang . "&' title='im_setup.reg' target='_blank'>im_setup.reg</A>", $l_menu_need_reg);
     echo "<div class='notice'><FONT COLOR='BLUE'>" . $l_menu_need_reg . "</font></div>";
     //echo "<BR/>";
  }
  */
	echo "<SMALL><BR/></SMALL>";
	//require ("../common/constant.inc.php");
	write_file("../common/config/version.tmp", _SERVER_VERSION);
	write_file("log/lastcheck.tmp", ""); // pour forcer à vérifier à nouveau la version (qui a peut être été mise à jour entre temps).
	echo "<font face='verdana' size=3 color='green'>";
	echo "<div class='notice'><B>";
	echo $l_admin_check_conf_ok . "<BR/>";
	echo $l_admin_check_can_go;
	echo " <A HREF='list_options_updating.php?lang=" . $lang  . "'>" . $l_admin_check_admin_panel . "</A> (<acronym title='Admin Control Panel'>ACP</acronym>)";
	echo "</div>";
	echo "</b></font>";
}
else
{
	echo "<SMALL><BR/></SMALL>";
	echo "<div class='warning'>";
	echo "<font face='verdana' size=4><B>";
	echo $l_admin_check_conf_not_ok . "<BR/>";
	echo "</b></font>";
	echo "</div>";
}
//
$requete = "select CURTIME() ";
$result = mysqli_query($id_connect, $requete);
//if (!$result) error_sql_log("[ERR-A2a]", $requete);
list ($time_server_mysql) = mysqli_fetch_row ($result);
$time_server_mysql = date($l_time_format_display, strtotime($time_server_mysql));
$time_server_php = date($l_time_format_display);
//
//
mysqli_close($id_connect);
//
//
if ($time_server_php <> $time_server_mysql) 
{
  //echo "<div class='warning'>";
  table_time_zone($time_server_php, $time_server_mysql);
  //echo "</div>";
}
//

#echo ini_get("disable_functions");
table_title($l_admin_check_system_info);
//
//
$txt = "Server Software : <I>" . $_SERVER["SERVER_SOFTWARE"] . "</I><BR/>";
if ( (phpversion() > "4.3.0") and (phpversion() <> "5.3.0") )
  $txt .= "PHP Version : <I>" . phpversion() . "</I> (" . $c_OK . ")<BR/>";
else
  $txt .= "<FONT size='4' COLOR='RED'>PHP Version : <I>" . phpversion() . "</I></FONT> <B><BLINK>must be >= 4.3.0 (and cannot be 5.3.0)</BLINK></B><BR/>";

$txt .= "Register Globals : <I>";
$txt .= ini_get("register_globals") == 1 ? $c_on_ko : $c_off_ok;
$txt .= "</I><BR/>";

$txt .= "Display errors : <I>";
$txt .= ini_get("display_errors") == 1 ? $c_on_ko : $c_off_ok;
$txt .= "</I> (keep <I>on</I> only on test server)<BR/>";

$txt .= "Log errors : <I>";
$txt .= ini_get("log_errors") == 1 ? $l_admin_check_on : $l_admin_check_off;
$txt .= "</I><BR/>";

$txt .= "Safe Mode : <I>";
$txt .= ini_get("safe_mode") == 1 ? $l_admin_check_on : $l_admin_check_off;
$txt .= "</I><BR/>";

$txt .= "Open Basedir : <I>";
$txt .= ini_get("open_basedir") != '' ? $l_admin_check_on : $l_admin_check_off;
$txt .= "</I><BR/>";

$txt .= "Memory limit : <I>";
$txt .= ini_get("file_uploads") != '' ? $c_on_ok : $c_off_ko;
$txt .= "</I><BR/>";

$txt .= "File upload : <I>";
$txt .= ini_get("file_uploads") == 1 ? $c_on_ok : $c_off_ko;
$txt .= "</I> (must be <I><B>on</B></I> to allow user to upload avatars to <I>/" . _PUBLIC_FOLDER . "/upload/</I> folder)<BR/>";

$txt .= "Allow url fopen : <I>";
$txt .= ini_get("allow_url_fopen") == 1 ? $c_on_ok : $c_off_ko;
$txt .= "</I>";
$txt .= " (must be <I><B>on</B></I> to register on <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>internet public servers directory</A>)<BR/>";
//
$txt .= $l_display . " : <B><A HREF='phpinfo.php'>PHP info</A><BR/></B>";
//
if (ini_get("disable_functions") != "") 
{
  if ($lang == "FR")
    $txt .= "<font color='red'>Fonctions désactivées</font> : " . ini_get("disable_functions") . "<BR/>";
  else
    $txt .= "<font color='red'>Disabled functions</font> : " . ini_get("disable_functions") . "<BR/>";
}

table_col_1($txt);
table_col_vide();
//
echo "</TABLE>";
//
//
//
if ($time_server_php == $time_server_mysql) table_time_zone($time_server_php, $time_server_mysql);

//
//if (ini_get("disable_functions") != "") echo "<BR/>" . "Disabled functions : " . ini_get("disable_functions") . "<BR/>";
//
//

//
table_title("Languages list");
//
//
$txt = "";
$if_prob_lang = "OK";
$one_or_more = false;
$noDir = "no folder";
$rep = opendir('../common/lang/');
while ($file = readdir($rep))
{
	if ($file != ".." && $file != "." && $file !="" && $file !="index.php" && strpos(strtolower($file), ".php") ) // .inc.php
	{
		if (!is_dir($file))
		{
      unset($l_lang_name);
      unset($charset);
      //
			include ("../common/lang/" . $file);
			if ( isset($l_lang_name) and isset($charset) )
			{
        $one_or_more = true;
				$txt .= "<B>" . $l_lang_name . " </B> (" . $charset . ") : ";
			}
      $txt .= $file . "<BR/>";
		}
	}
}
closedir($rep);
if ($one_or_more == false) 
	$if_prob_lang = "KO";
//
table_col_1($txt);
table_col_2($if_prob_lang);
echo "<TR><TD COLSPAN='2' class='catBottom'>";
echo "<FONT size='2'>To add more language (or just update), please read <I>";
if (is_readable("../common/lang/translate.txt")) 
	echo "<A HREF='../common/lang/translate.txt' target='blank'>";
//
echo "../common/lang/translate.txt</I></A></FONT>";
echo "</TD></TR>";

//
//
echo "</TABLE>";
//



////////////////


//
echo "</B>";
echo "</body></html>";
?>
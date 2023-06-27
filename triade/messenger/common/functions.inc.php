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


function prevent_error_option_missing()
{
  if (!defined("_LANG"))                                          define("_LANG", "EN");
  if (!defined("_MAINTENANCE_MODE"))                              define("_MAINTENANCE_MODE", "X");
  if (!defined("_MAX_NB_USER"))                                   define("_MAX_NB_USER", "0");
  if (!defined("_MAX_NB_SESSION"))                                define("_MAX_NB_SESSION", "0");
  if (!defined("_MAX_NB_CONTACT_BY_USER"))                        define("_MAX_NB_CONTACT_BY_USER", "0");
  if (!defined("_MAX_NB_IP"))                                     define("_MAX_NB_IP", "0");
  #if (!defined("_DISPLAY_USER_FLAG_COUNTRY"))                     define("_DISPLAY_USER_FLAG_COUNTRY", "");
  if (!defined("_OUTOFDATE_AFTER_NOT_USE_DURATION"))              define("_OUTOFDATE_AFTER_NOT_USE_DURATION", "90"); // _OUTOFDATE_AFTER_X_DAYS_NOT_USE
  if (!defined("_CHECK_NEW_MSG_EVERY"))                           define("_CHECK_NEW_MSG_EVERY", "30");
  #if (!defined("_FULL_CHECK"))                                    define("_FULL_CHECK", "");
  if (!defined("_STATISTICS"))                                    define("_STATISTICS", "");
  if (!defined("_PUBLIC_FOLDER"))                                 define("_PUBLIC_FOLDER", "public");
  if (!defined("_PUBLIC_OPTIONS_LIST"))                           define("_PUBLIC_OPTIONS_LIST", "");
  if (!defined("_PUBLIC_USERS_LIST"))                             define("_PUBLIC_USERS_LIST", "");
  if (!defined("_PUBLIC_POST_AVATAR"))                            define("_PUBLIC_POST_AVATAR", "");
  if (!defined("_FORCE_USERNAME_TO_PC_SESSION_NAME"))             define("_FORCE_USERNAME_TO_PC_SESSION_NAME", "");
  if (!defined("_ALLOW_CONFERENCE"))                              define("_ALLOW_CONFERENCE", "");
  #if (!defined("_ALLOW_INVISIBLE"))                               define("_ALLOW_INVISIBLE", "");
  if (!defined("_ALLOW_SMILEYS"))                                 define("_ALLOW_SMILEYS", "");
  if (!defined("_ALLOW_CHANGE_CONTACT_NICKNAME"))                 define("_ALLOW_CHANGE_CONTACT_NICKNAME", "");
  if (!defined("_ALLOW_CHANGE_EMAIL_PHONE"))                      define("_ALLOW_CHANGE_EMAIL_PHONE", "");
  if (!defined("_ALLOW_CHANGE_FUNCTION_NAME"))                    define("_ALLOW_CHANGE_FUNCTION_NAME", "");
  if (!defined("_ALLOW_CHANGE_AVATAR"))                           define("_ALLOW_CHANGE_AVATAR", "");
  if (!defined("_ALLOW_SEND_TO_OFFLINE_USER"))                    define("_ALLOW_SEND_TO_OFFLINE_USER", "");
  #if (!defined("_ALLOW_USER_TO_HISTORY_MESSAGES"))                define("_ALLOW_USER_TO_HISTORY_MESSAGES", "");
  if (!defined("_ALLOW_USE_PROXY"))                               define("_ALLOW_USE_PROXY", "");
  #if (!defined("_ALLOW_USER_RATING"))                             define("_ALLOW_USER_RATING", "");
  if (!defined("_ALLOW_EMAIL_NOTIFIER"))                          define("_ALLOW_EMAIL_NOTIFIER", "");
  if (!defined("_INCOMING_EMAIL_SERVER_ADDRESS"))                 define("_INCOMING_EMAIL_SERVER_ADDRESS", "");
  if (!defined("_FORCE_AWAY_ON_SCREENSAVER"))                     define("_FORCE_AWAY_ON_SCREENSAVER", "");
  #if (!defined("_HIDE_COL_FUNCTION_NAME"))                        define("_HIDE_COL_FUNCTION_NAME", "");
  if (!defined("_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN"))            define("_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", "");
  #if (!defined("_LOCK_USER_CONTACT_LIST"))                        define("_LOCK_USER_CONTACT_LIST", "");
  #if (!defined("_LOCK_USER_OPTIONS"))                             define("_LOCK_USER_OPTIONS", "");
  if (!defined("_FORCE_STATUS_LIST_FROM_SERVER"))                 define("_FORCE_STATUS_LIST_FROM_SERVER", "");
  if (!defined("_AWAY_REASONS_LIST"))                             define("_AWAY_REASONS_LIST", "");
  if (!defined("_MINIMUM_USERNAME_LENGTH"))                       define("_MINIMUM_USERNAME_LENGTH", "4");
  if (!defined("_USER_NEED_PASSWORD"))                            define("_USER_NEED_PASSWORD", "");
  if (!defined("_MINIMUM_PASSWORD_LENGTH"))                       define("_MINIMUM_PASSWORD_LENGTH", "4");
  if (!defined("_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER"))          define("_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", "5");
  if (!defined("_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER"))             define("_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER", "");
  if (!defined("_PENDING_NEW_AUTO_ADDED_USER"))                   define("_PENDING_NEW_AUTO_ADDED_USER", "");
  if (!defined("_PENDING_USER_ON_COMPUTER_CHANGE"))               define("_PENDING_USER_ON_COMPUTER_CHANGE", "");
  if (!defined("_CRYPT_MESSAGES"))                                define("_CRYPT_MESSAGES", "");
  #if (!defined("_LOG_MESSAGES"))                                  define("_LOG_MESSAGES", "");
  if (!defined("_LOG_SESSION_OPEN"))                              define("_LOG_SESSION_OPEN", "");
  if (!defined("_PASSWORD_FOR_PRIVATE_SERVER"))                   define("_PASSWORD_FOR_PRIVATE_SERVER", "");
  if (!defined("_FORCE_UPDATE_BY_SERVER"))                        define("_FORCE_UPDATE_BY_SERVER", "");
  if (!defined("_FORCE_UPDATE_BY_INTERNET"))                      define("_FORCE_UPDATE_BY_INTERNET", "");
  if (!defined("_SEND_ADMIN_ALERT"))                              define("_SEND_ADMIN_ALERT", "");
  if (!defined("_PROXY_ADDRESS"))                                 define("_PROXY_ADDRESS", "");
  if (!defined("_PROXY_PORT_NUMBER"))                             define("_PROXY_PORT_NUMBER", "");
  if (!defined("_SITE_URL_TO_SHOW"))                              define("_SITE_URL_TO_SHOW", "");
  if (!defined("_SITE_TITLE_TO_SHOW"))                            define("_SITE_TITLE_TO_SHOW", "");
  if (!defined("_SCROLL_TEXT"))                                   define("_SCROLL_TEXT", "");
  if (!defined("_ADMIN_EMAIL"))                                   define("_ADMIN_EMAIL", "");
  if (!defined("_ADMIN_PHONE"))                                   define("_ADMIN_PHONE", "");
  if (!defined("_ENTERPRISE_SERVER"))                             define("_ENTERPRISE_SERVER", "");
  if (!defined("_IM_ADDRESS_BOOK_PASSWORD"))                      define("_IM_ADDRESS_BOOK_PASSWORD", "");
  //if (!defined("_GROUP_FOR_ADMIN_MESSAGES"))                      define("_GROUP_FOR_ADMIN_MESSAGES", "");
  if (!defined("_SPECIAL_MODE_OPEN_COMMUNITY"))                   define("_SPECIAL_MODE_OPEN_COMMUNITY", "");
  if (!defined("_SPECIAL_MODE_GROUP_COMMUNITY"))                  define("_SPECIAL_MODE_GROUP_COMMUNITY", "");
  if (!defined("_EXTERN_URL_TO_REGISTER"))                        define("_EXTERN_URL_TO_REGISTER", "");
  if (!defined("_EXTERN_URL_FORGET_PASSWORD"))                    define("_EXTERN_URL_FORGET_PASSWORD", "");
  if (!defined("_EXTERN_URL_CHANGE_PASSWORD"))                    define("_EXTERN_URL_CHANGE_PASSWORD", "");
  if (!defined("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL")) define("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL", "");
  if (!defined("_EXTERNAL_AUTHENTICATION"))                       define("_EXTERNAL_AUTHENTICATION", "");
  if (!defined("_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER"))      define("_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER", "");
  if (!defined("_SITE_TITLE"))                                    define("_SITE_TITLE", "");
  if (!defined("_ALLOW_UPPERCASE_SPACE_USERNAME"))                define("_ALLOW_UPPERCASE_SPACE_USERNAME", "");
  if (!defined("_CENSOR_MESSAGES"))                               define("_CENSOR_MESSAGES", "");
  if (!defined("_PWD_NEED_DIGIT_LETTER"))                         define("_PWD_NEED_DIGIT_LETTER", "");
  if (!defined("_PWD_NEED_UPPER_LOWER"))                          define("_PWD_NEED_UPPER_LOWER", "");
  if (!defined("_PWD_NEED_SPECIAL_CHARACTER"))                    define("_PWD_NEED_SPECIAL_CHARACTER", "");
  if (!defined("_SHOUTBOX"))                                      define("_SHOUTBOX", "");
  if (!defined("_SHOUTBOX_REFRESH_DELAY"))                        define("_SHOUTBOX_REFRESH_DELAY", "60");
  if (!defined("_SHOUTBOX_STORE_DAYS"))                           define("_SHOUTBOX_STORE_DAYS", "30");
  if (!defined("_SHOUTBOX_STORE_MAX"))                            define("_SHOUTBOX_STORE_MAX", "200");
  if (!defined("_SHOUTBOX_QUOTA_USER_DAY"))                       define("_SHOUTBOX_QUOTA_USER_DAY", "50");
  if (!defined("_SHOUTBOX_QUOTA_USER_WEEK"))                      define("_SHOUTBOX_QUOTA_USER_WEEK", "100");
  if (!defined("_SHOUTBOX_NEED_APPROVAL"))                        define("_SHOUTBOX_NEED_APPROVAL", "");
  if (!defined("_SHOUTBOX_APPROVAL_QUEUE_USER"))                  define("_SHOUTBOX_APPROVAL_QUEUE_USER", "3");
  if (!defined("_SHOUTBOX_APPROVAL_QUEUE"))                       define("_SHOUTBOX_APPROVAL_QUEUE", "10");
  if (!defined("_SHOUTBOX_LOCK_USER_APPROVAL"))                   define("_SHOUTBOX_LOCK_USER_APPROVAL", "0");
  if (!defined("_SHOUTBOX_VOTE"))                                 define("_SHOUTBOX_VOTE", "");
  if (!defined("_SHOUTBOX_MAX_NOTES_USER_DAY"))                   define("_SHOUTBOX_MAX_NOTES_USER_DAY", "0");
  if (!defined("_SHOUTBOX_MAX_NOTES_USER_WEEK"))                  define("_SHOUTBOX_MAX_NOTES_USER_WEEK", "0");
  if (!defined("_SHOUTBOX_REMOVE_MESSAGE_VOTES"))                 define("_SHOUTBOX_REMOVE_MESSAGE_VOTES", "0");
  if (!defined("_SHOUTBOX_LOCK_USER_VOTES"))                      define("_SHOUTBOX_LOCK_USER_VOTES", "0");
  if (!defined("_GROUP_USER_CAN_JOIN"))                           define("_GROUP_USER_CAN_JOIN", "");
  if (!defined("_GROUP_FOR_SBX_AND_ADMIN_MSG"))                   define("_GROUP_FOR_SBX_AND_ADMIN_MSG", "");
  if (!defined("_SERVERS_STATUS"))                                define("_SERVERS_STATUS", "");
  if (!defined("_CHECK_VERSION_INTERNET"))                        define("_CHECK_VERSION_INTERNET", "X");
  if (!defined("_TIME_ZONES"))                                    define("_TIME_ZONES", "X");
  if (!defined("_SHOUTBOX_PUBLIC"))                               define("_SHOUTBOX_PUBLIC", "");
  if (!defined("_BOOKMARKS"))                                     define("_BOOKMARKS", "");
  if (!defined("_BOOKMARKS_VOTE"))                                define("_BOOKMARKS_VOTE", "");
  if (!defined("_BOOKMARKS_PUBLIC"))                              define("_BOOKMARKS_PUBLIC", "");
  if (!defined("_BOOKMARKS_NEED_APPROVAL"))                       define("_BOOKMARKS_NEED_APPROVAL", "");
  if (!defined("_LOCK_DURATION"))                                 define("_LOCK_DURATION", "");
  if (!defined("_UNREAD_MESSAGE_VALIDITY"))                       define("_UNREAD_MESSAGE_VALIDITY", "");
  if (!defined("_LOCK_AFTER_NO_CONTACT_DURATION"))                define("_LOCK_AFTER_NO_CONTACT_DURATION", "");
  if (!defined("_LOCK_AFTER_NO_ACTIVITY_DURATION"))               define("_LOCK_AFTER_NO_ACTIVITY_DURATION", "");
  #if (!defined("_LOCK_USER_PROFILE"))                             define("_LOCK_USER_PROFILE", "");
  if (!defined("_INVITE_FILL_PROFILE_ON_FIRST_LOGIN"))            define("_INVITE_FILL_PROFILE_ON_FIRST_LOGIN", "");
  if (!defined("_ROLES_TO_OVERRIDE_PERMISSIONS"))                 define("_ROLES_TO_OVERRIDE_PERMISSIONS", "");
  if (!defined("_WAIT_STARTUP_IF_SERVER_UNAVAILABLE"))            define("_WAIT_STARTUP_IF_SERVER_UNAVAILABLE", "");
  if (!defined("_ONLINE_REASONS_LIST"))                           define("_ONLINE_REASONS_LIST", "");
  if (!defined("_BUSY_REASONS_LIST"))                             define("_BUSY_REASONS_LIST", "");
  if (!defined("_DONOTDISTURB_REASONS_LIST"))                     define("_DONOTDISTURB_REASONS_LIST", "");
  if (!defined("_SPECIAL_MODE_OPEN_GROUP_COMMUNITY"))             define("_SPECIAL_MODE_OPEN_GROUP_COMMUNITY", "");
  if (!defined("_FORCE_LAUNCH_ON_STARTUP"))                       define("_FORCE_LAUNCH_ON_STARTUP", "");
  if (!defined("_ALLOW_SKIN"))                                    define("_ALLOW_SKIN", "");
  if (!defined("_ALLOW_CLOSE_IM"))                                define("_ALLOW_CLOSE_IM", "X");
  if (!defined("_ALLOW_SOUND_USAGE"))                             define("_ALLOW_SOUND_USAGE", "X");
  if (!defined("_ALLOW_REDUCE_MAIN_SCREEN"))                      define("_ALLOW_REDUCE_MAIN_SCREEN", "X");
  if (!defined("_ALLOW_REDUCE_MESSAGE_SCREEN"))                   define("_ALLOW_REDUCE_MESSAGE_SCREEN", "X");
  if (!defined("_SKIN_FORCED_COLOR_CUSTOM_VERSION"))              define("_SKIN_FORCED_COLOR_CUSTOM_VERSION", "0-0-0");
  if (!defined("_SEND_ADMIN_ALERT_EMAIL"))                        define("_SEND_ADMIN_ALERT_EMAIL", "");
  if (!defined("_AUTO_ADD_CONTACT_USER_ID"))                      define("_AUTO_ADD_CONTACT_USER_ID", "");
  if (!defined("_PASSWORD_VALIDITY"))                             define("_PASSWORD_VALIDITY", "0");
  if (!defined("_ALLOW_POST_IT"))                                 define("_ALLOW_POST_IT", "");
  if (!defined("_SHARE_FILES"))                                   define("_SHARE_FILES", "");
  if (!defined("_SHARE_FILES_EXCHANGE"))                          define("_SHARE_FILES_EXCHANGE", "");
  if (!defined("_SHARE_FILES_EXCHANGE_NEED_APPROVAL"))            define("_SHARE_FILES_EXCHANGE_NEED_APPROVAL", "");
  if (!defined("_SHARE_FILES_EXCHANGE_TRASH"))                    define("_SHARE_FILES_EXCHANGE_TRASH", "");
  if (!defined("_SHARE_FILES_FTP_ADDRESS"))                       define("_SHARE_FILES_FTP_ADDRESS", "");
  if (!defined("_SHARE_FILES_FTP_LOGIN"))                         define("_SHARE_FILES_FTP_LOGIN", "");
  if (!defined("_SHARE_FILES_FTP_PASSWORD"))                      define("_SHARE_FILES_FTP_PASSWORD", "");
  if (!defined("_SHARE_FILES_FTP_PASSWORD_CRYPT"))                define("_SHARE_FILES_FTP_PASSWORD_CRYPT", "");
  if (!defined("_SHARE_FILES_FTP_PORT_NUMBER"))                   define("_SHARE_FILES_FTP_PORT_NUMBER", "21");
  if (!defined("_SHARE_FILES_MAX_FILE_SIZE"))                     define("_SHARE_FILES_MAX_FILE_SIZE", "0");
  if (!defined("_SHARE_FILES_MAX_NB_FILES_TOTAL"))                define("_SHARE_FILES_MAX_NB_FILES_TOTAL", "0");
  if (!defined("_SHARE_FILES_MAX_NB_FILES_USER"))                 define("_SHARE_FILES_MAX_NB_FILES_USER", "0");
  if (!defined("_SHARE_FILES_MAX_SPACE_SIZE_TOTAL"))              define("_SHARE_FILES_MAX_SPACE_SIZE_TOTAL", "0");
  if (!defined("_SHARE_FILES_MAX_SPACE_SIZE_USER"))               define("_SHARE_FILES_MAX_SPACE_SIZE_USER", "0");
  if (!defined("_SHARE_FILES_NEED_APPROVAL"))                     define("_SHARE_FILES_NEED_APPROVAL", "");
  if (!defined("_SHARE_FILES_APPROVAL_QUEUE"))                    define("_SHARE_FILES_APPROVAL_QUEUE", "0");
  if (!defined("_SHARE_FILES_QUOTA_FILES_USER_WEEK"))             define("_SHARE_FILES_QUOTA_FILES_USER_WEEK", "0");
  if (!defined("_SHARE_FILES_TRASH"))                             define("_SHARE_FILES_TRASH", "");
  if (!defined("_SHARE_FILES_VOTE"))                              define("_SHARE_FILES_VOTE", "");
  if (!defined("_SHARE_FILES_FOLDER"))                            define("_SHARE_FILES_FOLDER", "");
  if (!defined("_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY"))          define("_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY", "30");
  if (!defined("_SHARE_FILES_SCREENSHOT"))                        define("_SHARE_FILES_SCREENSHOT", "");
  if (!defined("_SHARE_FILES_EXCHANGE_SCREENSHOT"))               define("_SHARE_FILES_EXCHANGE_SCREENSHOT", "X");
  if (!defined("_SHARE_FILES_WEBCAM"))                            define("_SHARE_FILES_WEBCAM", "");
  if (!defined("_SHARE_FILES_EXCHANGE_WEBCAM"))                   define("_SHARE_FILES_EXCHANGE_WEBCAM", "X");
  if (!defined("_SHARE_FILES_COMPRESS"))                          define("_SHARE_FILES_COMPRESS", "");
  if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY"))     define("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY", "");
  if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK"))    define("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK", "");
  if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH"))   define("_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH", "");
  if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY"))        define("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY", "");
  if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK"))       define("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK", "");
  if (!defined("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH"))      define("_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH", "");
  if (!defined("_SHARE_FILES_PROTECT"))                           define("_SHARE_FILES_PROTECT", "");
  if (!defined("_SHARE_FILES_ALLOW_UPPERCASE"))                   define("_SHARE_FILES_ALLOW_UPPERCASE", "");
  if (!defined("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN")) define("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN", "");
  if (!defined("_ALLOW_HIDDEN_STATUS"))                           define("_ALLOW_HIDDEN_STATUS", "");
  if (!defined("_ROLE_ID_DEFAULT_FOR_NEW_USER"))                  define("_ROLE_ID_DEFAULT_FOR_NEW_USER", "");
  if (!defined("_ACP_PROTECT_BY_HTACCESS"))                       define("_ACP_PROTECT_BY_HTACCESS", "X");
  if (!defined("_ACP_ALLOW_MEMORY_AUTH"))                         define("_ACP_ALLOW_MEMORY_AUTH", "X");
  if (!defined("_ALLOW_HISTORY_MESSAGES_EXPORT"))                 define("_ALLOW_HISTORY_MESSAGES_EXPORT", "");
  if (!defined("_SLOW_NOTIFY"))                                   define("_SLOW_NOTIFY", ""); // _FULL_CHECK
  if (!defined("_BACKUP_FILES"))                                  define("_BACKUP_FILES", "");
  if (!defined("_BACKUP_FILES_MAX_NB_ARCHIVES_USER"))             define("_BACKUP_FILES_MAX_NB_ARCHIVES_USER", "2");
  if (!defined("_BACKUP_FILES_MAX_ARCHIVE_SIZE"))                 define("_BACKUP_FILES_MAX_ARCHIVE_SIZE", "0");
  if (!defined("_BACKUP_FILES_MAX_SPACE_SIZE_USER"))              define("_BACKUP_FILES_MAX_SPACE_SIZE_USER", "0");
  if (!defined("_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL"))             define("_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL", "0");
  if (!defined("_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY"))           define("_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY", "");
  if (!defined("_BACKUP_FILES_ALLOW_MULTI_FOLDERS"))              define("_BACKUP_FILES_ALLOW_MULTI_FOLDERS", "X");
  if (!defined("_BACKUP_FILES_ALLOW_SUB_FOLDERS"))                define("_BACKUP_FILES_ALLOW_SUB_FOLDERS", "X");
  if (!defined("_BACKUP_FILES_FOLDER"))                           define("_BACKUP_FILES_FOLDER", "");
  if (!defined("_BACKUP_FILES_FTP_ADDRESS"))                      define("_BACKUP_FILES_FTP_ADDRESS", "");
  if (!defined("_BACKUP_FILES_FTP_LOGIN"))                        define("_BACKUP_FILES_FTP_LOGIN", "");
  if (!defined("_BACKUP_FILES_FTP_PASSWORD"))                     define("_BACKUP_FILES_FTP_PASSWORD", "");
  if (!defined("_BACKUP_FILES_FTP_PASSWORD_CRYPT"))               define("_BACKUP_FILES_FTP_PASSWORD_CRYPT", "");
  if (!defined("_BACKUP_FILES_FTP_PORT_NUMBER"))                  define("_BACKUP_FILES_FTP_PORT_NUMBER", "21");
  if (!defined("_BACKUP_FILES_FORCE_EVERY_DAY_AT"))               define("_BACKUP_FILES_FORCE_EVERY_DAY_AT", "");
  if (!defined("_SHARE_FILES_ALLOW_ACCENT"))                      define("_SHARE_FILES_ALLOW_ACCENT", "X");
  if (!defined("_GROUP_ID_DEFAULT_FOR_NEW_USER"))                 define("_GROUP_ID_DEFAULT_FOR_NEW_USER", "");
  if (!defined("_FORCE_OPTION_FILE_FROM_SERVER"))                 define("_FORCE_OPTION_FILE_FROM_SERVER", "");
  if (!defined("_SHOUTBOX_ALLOW_SCROLLING"))                      define("_SHOUTBOX_ALLOW_SCROLLING", "X");
  //
  if (!defined("_ALLOW_HIDDEN_TO_CONTACTS")) // _ALLOW_INVISIBLE
  {
    if (defined("_ALLOW_INVISIBLE"))
    {
      if (_ALLOW_INVISIBLE == "")
        define("_ALLOW_HIDDEN_TO_CONTACTS", "");
      else
        define("_ALLOW_HIDDEN_TO_CONTACTS", "X");
    }
    else
      define("_ALLOW_HIDDEN_TO_CONTACTS", "");
  }
  //
  if (!defined("_HISTORY_MESSAGES_ON_ACP")) // _LOG_MESSAGES
  {
    if (defined("_LOG_MESSAGES"))
    {
      if (_LOG_MESSAGES == "")
        define("_HISTORY_MESSAGES_ON_ACP", "");
      else
        define("_HISTORY_MESSAGES_ON_ACP", "X");
    }
    else
      define("_HISTORY_MESSAGES_ON_ACP", "");
  }
  //
  //
  if (!defined("_ALLOW_HISTORY_MESSAGES")) // _ALLOW_USER_TO_HISTORY_MESSAGES
  {
    if (defined("_ALLOW_USER_TO_HISTORY_MESSAGES"))
    {
      if (_ALLOW_USER_TO_HISTORY_MESSAGES == "")
        define("_ALLOW_HISTORY_MESSAGES", "");
      else
        define("_ALLOW_HISTORY_MESSAGES", "X");
    }
    else
      define("_ALLOW_HISTORY_MESSAGES", "");
  }
  //
  //
  if (!defined("_FLAG_COUNTRY_FROM_IP")) // _DISPLAY_USER_FLAG_COUNTRY
  {
    if (defined("_DISPLAY_USER_FLAG_COUNTRY"))
    {
      if (_DISPLAY_USER_FLAG_COUNTRY == "")
        define("_FLAG_COUNTRY_FROM_IP", "");
      else
        define("_FLAG_COUNTRY_FROM_IP", "X");
    }
    else
      define("_FLAG_COUNTRY_FROM_IP", "");
  }
  //
  //
  if (!defined("_ALLOW_CONTACT_RATING")) // _ALLOW_USER_RATING
  {
    if (defined("_ALLOW_USER_RATING"))
    {
      if (_ALLOW_USER_RATING == "")
        define("_ALLOW_CONTACT_RATING", "");
      else
        define("_ALLOW_CONTACT_RATING", "X");
    }
    else
      define("_ALLOW_CONTACT_RATING", "");
  }
  //
  //
  if (!defined("_ALLOW_MANAGE_OPTIONS")) // _LOCK_USER_OPTIONS
  {
    if (defined("_LOCK_USER_OPTIONS"))
    {
      if (_LOCK_USER_OPTIONS == "")
        define("_ALLOW_MANAGE_OPTIONS", "X"); // option inversée !
      else
        define("_ALLOW_MANAGE_OPTIONS", ""); // option inversée !
    }
    else
      define("_ALLOW_MANAGE_OPTIONS", "X");
  }
  //
  //
  if (!defined("_ALLOW_MANAGE_CONTACT_LIST")) // _LOCK_USER_CONTACT_LIST
  {
    if (defined("_LOCK_USER_CONTACT_LIST"))
    {
      if (_LOCK_USER_CONTACT_LIST == "")
        define("_ALLOW_MANAGE_CONTACT_LIST", "X"); // option inversée !
      else
        define("_ALLOW_MANAGE_CONTACT_LIST", ""); // option inversée !
    }
    else
      define("_ALLOW_MANAGE_CONTACT_LIST", "X");
  }
  //
  //
  if (!defined("_ALLOW_MANAGE_PROFILE")) // _LOCK_USER_PROFILE
  {
    if (defined("_LOCK_USER_PROFILE"))
    {
      if (_LOCK_USER_PROFILE == "")
        define("_ALLOW_MANAGE_PROFILE", "X"); // option inversée !
      else
        define("_ALLOW_MANAGE_PROFILE", ""); // option inversée !
    }
    else
      define("_ALLOW_MANAGE_PROFILE", "X");
  }
  //
  //
  if (!defined("_ALLOW_COL_FUNCTION_NAME")) // _HIDE_COL_FUNCTION_NAME
  {
    if (defined("_HIDE_COL_FUNCTION_NAME"))
    {
      if (_HIDE_COL_FUNCTION_NAME == "")
        define("_ALLOW_COL_FUNCTION_NAME", "X"); // option inversée !
      else
        define("_ALLOW_COL_FUNCTION_NAME", ""); // option inversée !
    }
    else
      define("_ALLOW_COL_FUNCTION_NAME", "X");
  }
  //
  //
  //
}


if (!function_exists('ctype_alnum')) 
{
  function ctype_alnum($text) 
  {
    return !preg_match('/^\w*$/', $text);
  }
}


function f_quote($txt)
{
	$txt = str_replace("'", "&#146;", $txt);
  //
  return $txt;
}


function f_language_of_country($country_code, $default)
{
  GLOBAL $lang;
  //
  if (!isset($l_lng['FR'])) require("lang.inc.php");
  //if ($l_lng[$country_code] != '')
  if (isset($l_lng[$country_code]))
    return $l_lng[$country_code];
  else
    return $default;
}


function f_user_local_time($time_shit)
{
  GLOBAL $l_time_format_display, $l_time_short_format_display;
  //
  if ($l_time_short_format_display == '') $l_time_short_format_display = $l_time_format_display;
  if ($l_time_short_format_display == '') $l_time_short_format_display = 'H:i';
  //
  $time_shit = ($time_shit / 10);
  $tlocal = time() + ($time_shit * 60 * 60);
  //
  return date($l_time_short_format_display, $tlocal);
}


function update_check_user($t_id_user, $t_check)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_user = intval($t_id_user);
  if ($t_id_user > 0)
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    //$requete .= " SET USR_CHECK = '" . $t_check . "' ";
    if ($t_check == "WAIT")
      $requete .= " SET USR_CHECK = '' , USR_STATUS = 2 "; // locked
    else
      $requete .= " SET USR_CHECK = '" . $t_check . "' ";
    //
    //if ($t_check == "WAIT") $requete .= " , USR_STATUS = 2 "; // locked
    if ($t_check == "") $requete .= " , USR_STATUS = 3 "; // chang conf ok
    if (strlen($t_check) > 10) $requete .= " , USR_STATUS = 1 "; // OK
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1a]", $requete);
  }
}


function update_last_activity_user($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_user = intval($t_id_user);
  if ($t_id_user > 0)
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " SET USR_DATE_ACTIVITY= CURDATE()  ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1j]", $requete);
  }
}


function f_verif_check_user_only($t_id_user, $t_check)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = 'KO'; // par défaut
  $t_id_user = intval($t_id_user);
  if ($t_id_user > 0)
  {
    $requete  = " select USR_CHECK ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($usr_check) = mysqli_fetch_row ($result);
      if ($usr_check == $t_check) 
        $retour = 'OK';
      else
        write_log("log_reject_check_user", $t_id_user . ";" . $usr_check . ";" . $t_check);
    }
  }
	return $retour;
}
	

function f_get_nom_user($t_user)  # f_get_user_name(
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = "";
	$t_user = str_replace("'", "", $t_user);
	if ($t_user != '')
	{
    $requete  = " select USR_NAME ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE USR_USERNAME = '" . $t_user . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1f]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($usr_nom) = mysqli_fetch_row ($result);
      $retour = $usr_nom;
    }
  }
	return $retour;
}


function f_get_id_nom_user($t_user)  # f_get_id_of_username
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = "";
	$t_user = str_replace("'", "", $t_user);
	if ($t_user != '')
	{
    $requete  = " select ID_USER, USR_USERNAME ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE USR_USERNAME = '" . $t_user . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1g]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_usr, $usr_name) = mysqli_fetch_row ($result);
      if ($usr_name == $t_user) $retour = $id_usr;
    }
  }
	return $retour;
}


function f_get_username_of_id($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = "";
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $requete  = " select USR_USERNAME ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1h]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($usrname) = mysqli_fetch_row ($result);
      $retour = $usrname;
    }
  }
	return $retour;
}


function f_get_username_nickname_of_id($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = "";
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $requete  = " select USR_NICKNAME, USR_USERNAME ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1h2]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nickname, $usrname) = mysqli_fetch_row ($result);
      $retour = $usrname;
      if ($nickname != "") $retour = $nickname;
    }
  }
	return $retour;
}


function f_get_id_of_renamed_nickname($t_nick) 
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = "";
	$t_nick = str_replace("'", "", $t_nick);
	if ($t_nick != '')
	{
    $requete  = " select ID_USER_2 ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
    $requete .= " WHERE CNT_NEW_USERNAME = '" . $t_nick . "' ";
    $requete .= " and CNT_NEW_USERNAME <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1i]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_usr) = mysqli_fetch_row ($result);
      $retour = $id_usr;
    }
  }
	return $retour;
}


function f_is_deja_in_contacts_id($t_id_u_1, $t_id_u_2)  # f_is_already_in_contacts_id
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_u_1 = intval($t_id_u_1);
  $t_id_u_2 = intval($t_id_u_2);
  if ( ($t_id_u_1 > 0) and ($t_id_u_2 > 0) )
  {
    $requete  = " select CNT.ID_CONTACT, USR.USR_USERNAME, USR.USR_NAME, CNT.CNT_STATUS ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE USR.ID_USER = CNT.ID_USER_2 ";
    $requete .= " and CNT.ID_USER_1 = " . $t_id_u_1 . " ";
    $requete .= " and CNT.ID_USER_2 = '" . $t_id_u_2 . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1k]", $requete);
    //
    return mysqli_num_rows($result);
  }
  else
    return 0;
}


function f_nb_users()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$requete  = " select count(*) ";
	$requete .= " from " . $PREFIX_IM_TABLE . "USR_USER ";
	$requete .= " where USR_STATUS = 1 ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-M1m]", $requete);
	list ($nb_user) = mysqli_fetch_row ($result);
	if (intval($nb_user) <= 0) $nb_user = 0;
	//
	return $nb_user;
}


function f_if_already_max_nb_users()
{
	$ret = '0'; // OK
	if (intval(_MAX_NB_USER) > 0)
	{
    $nb_user = f_nb_users();
    if ($nb_user >= intval(_MAX_NB_USER))
      $ret = $nb_user;  // Ko
  }
  return $ret;
}


function f_update_pass_user($t_id_user, $t_new_pass)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = 'KO';
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $passcr = "";
    if (_USER_NEED_PASSWORD != '')
    {
      if ($t_new_pass != '')
      {
        require("config/auth.inc.php");
        $passcr = sha1($password_pepper . $t_new_pass . "W$*7B0-c6");
      }	
    }	
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " SET USR_PASSWORD = '" . $passcr . "', ";
    $requete .= " USR_DATE_PASSWORD = CURDATE(), USR_DATE_ACTIVITY= CURDATE() ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1m]", $requete);
    //
    // On vérifie que la maj est correcte
    $requete  = " select USR_PASSWORD ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1n]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($usr_pass) = mysqli_fetch_row ($result);
      if ( (_USER_NEED_PASSWORD == '') or ($usr_pass == $passcr) )
        $retour = "OK";
    }
  }
	//
	return $retour;
}


function f_level_of_user($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$retour = "";
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $requete  = " select USR_LEVEL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1p]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($usr_level) = mysqli_fetch_row ($result);
      $retour = $usr_level;
    }
  }
  //
	return $retour;
}


function f_is_user_admin($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $retour = "";
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $requete  = " SELECT USR_GET_ADMIN_ALERT ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1p2]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($get_admin_alert) = mysqli_fetch_row ($result);
      if (intval($get_admin_alert) == 1) $retour = "X";
    }
    //
    if ( (_ROLES_TO_OVERRIDE_PERMISSIONS != "") and ($retour == "") )
    {
      if ( ! function_exists( 'f_role_of_user' ) )  require ("../common/roles.inc.php");
      $id_role = f_role_of_user($t_id_user);
      //
      if ($id_role > 0)
      {
        $t_get_admin_alert = f_role_permission($id_role, "ROLE_GET_ADMIN_ALERT_MESSAGES", ""); // c'est un role, pas une option !
        if ($t_get_admin_alert != "") $retour = "X";
      }
    }
  }
  //
	return $retour;
}


function f_send_email($email, $title, $msg)
{
  GLOBAL $charset;
  //
  $name_author = "IntraMessenger Admin";
  $email_author = _ADMIN_EMAIL;
  $res_send = false;
  if ( (strlen($email) > 5) and (strlen($email_author) > 5) and (strstr($email, "@") != "") and (strstr($email_author, "@") != "") )
  {
    $headers  = "From: \"$name_author\" <" . $email_author . ">\n";
    $headers .= "X-Sender: <" . $email_author . ">\n";
    $headers .= "Content-Type: text/plain; charset=" . $charset;
    if (mail($email, $title, $msg, $headers)) $res_send = true;
  }
  else 
    echo "email : " . $email_author;
  //
  return $res_send;
}


function send_alert_message_to_admins($txt)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $title_email = "IntraMessenger server alert (notification)";
  $txt = str_replace("'", "`", $txt);
  if (_SEND_ADMIN_ALERT != "")
  {
    //
    // ---------------------- Send Instant Message ----------------------
    //
    $requete  = " select distinct(USR.ID_USER) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "SES_SESSION SES";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " AND USR_GET_ADMIN_ALERT = 1 ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " AND SES.SES_STATUS > 0 ";
    //$requete .= " AND SES.SES_STATUS < 5 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1q]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_u_dest) = mysqli_fetch_row ($result) )
      {
        send_alert_message_to_admins_2($id_u_dest, $txt);
      }
    }
    //
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
    {
      $requete  = " select distinct(USR.ID_USER) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE RLM, " . $PREFIX_IM_TABLE . "SES_SESSION SES";
      $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
      $requete .= " AND USR.ID_ROLE = RLM.ID_ROLE ";
      $requete .= " and USR.USR_STATUS = 1 ";
      $requete .= " AND RLM.ID_MODULE = 70 "; // 70 : ROLE_GET_ADMIN_ALERT_MESSAGES
      $requete .= " AND RLM.RLM_STATE = 2 ";  // 2 : role actif
      $requete .= " AND SES.SES_STATUS > 0 ";
      //$requete .= " AND SES.SES_STATUS < 5 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M1t]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($id_u_dest) = mysqli_fetch_row ($result) )
        {
          send_alert_message_to_admins_2($id_u_dest, $txt);
        }
      }
    }
    //
    // ---------------------- Send email ----------------------
    //
    if ( (_SEND_ADMIN_ALERT_EMAIL != "") and (_ADMIN_EMAIL != "") )
    {
      $requete  = " select distinct(USR_EMAIL) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR ";
      $requete .= " WHERE USR_GET_ADMIN_ALERT = 1 ";
      $requete .= " and USR.USR_STATUS = 1 ";
      $requete .= " and USR_EMAIL <> '' ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M1q2]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($email) = mysqli_fetch_row ($result) )
        {
          f_send_email($email, $title_email, $txt);
        }
      }
      //
      if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
      {
        $requete  = " select distinct(USR.USR_EMAIL) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE RLM ";
        $requete .= " WHERE USR.ID_ROLE = RLM.ID_ROLE ";
        $requete .= " and USR.USR_STATUS = 1 ";
        $requete .= " and USR.USR_EMAIL <> '' ";
        $requete .= " AND RLM.ID_MODULE = 70 "; // 70 : ROLE_GET_ADMIN_ALERT_MESSAGES
        $requete .= " AND RLM.RLM_STATE = 2 ";  // 2 : role actif
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M1t2]", $requete);
        if ( mysqli_num_rows($result) > 0 )
        {
          while( list ($email) = mysqli_fetch_row ($result) )
          {
            f_send_email($email, $title_email, $txt);
          }
        }
      }
    }
  }
}


function send_alert_message_to_admins_2($id_u_dest, $txt)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  // Pour éviter les doublons :
  $send_already = "";
  $requete  = " select ID_USER_DEST ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
  $requete .= " WHERE ID_USER_AUT = -99 ";
  $requete .= " and ID_USER_DEST = " . $id_u_dest;
  $requete .= " and MSG_TEXT = '" . f_encode64($txt) . "' ";
  $requete .= " limit 2 ";
  $result2 = mysqli_query($id_connect, $requete);
  if (!$result2) error_sql_log("[ERR-M1u]", $requete);
  if ( mysqli_num_rows($result2) > 0 ) $send_already = "X";
  if ($send_already == "")
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
    $requete .= " VALUES (-99, " . $id_u_dest . ", '" . f_encode64($txt) . "', '64', CURTIME(), CURDATE() ) ";
    $result3 = mysqli_query($id_connect, $requete);
    if (!$result3) error_sql_log("[ERR-M1r]", $requete);
  }
}


function f_is_banned_user_ip_pc($tvalue, $type) // $type : 'U' or 'I' or 'P' (Username IP PC)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  if ( (strlen($type) == 1) and (strlen($tvalue) > 2) )
  {
    $requete  = " select BAN_VALUE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BAN_BANNED ";
    $requete .= " WHERE BAN_TYPE = '" . $type . "' ";
    $requete .= " and '" . $tvalue . "' like LOWER(BAN_VALUE) ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M1s]", $requete);
    if ( mysqli_num_rows($result) > 0 )
      return true;
    else
      return false;
   }
   else
     return false;
}



function f_nb_days_usage_max()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$requete  = " select max(USR_NB_CONNECT) ";
	$requete .= " from " . $PREFIX_IM_TABLE . "USR_USER ";
	$requete .= " where USR_STATUS = 1 ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-M1T]", $requete);
	list ($nb_days) = mysqli_fetch_row ($result);
	if (intval($nb_days) < 10) $nb_days = 0;
	//
	return $nb_days;
}


function f_servers_status()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $lst = "";
  $requete  = " SELECT SRV_STATE, SRV_NAME ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
  $requete .= " ORDER BY UPPER(SRV_NAME) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M1Tzzzzz]", $requete);
  $nb_lig = mysqli_num_rows($result);
  if ( $nb_lig > 0 )
  {
    $lst = $nb_lig . "%";
    while( list ($status, $srv_name) = mysqli_fetch_row ($result) )
    {
      $lst .= $status . "%";
    }
  }
	//
	return $lst;
}


function delete_user($id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  // lock :
  $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " set USR_STATUS = 2, USR_TIME_LOCK = '00:00:00' "; // locked
  $requete .= " WHERE ID_USER = " . $id_user;
  $requete .= " limit 1 "; // (to protect)
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4k]", $requete);
  //
  // Suppression sessions
  $requete  = " delete FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
  $requete .= " WHERE ID_USER = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4a]", $requete);
  //
  sleep(1);
  //
  $requete  = " delete FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ";
  $requete .= " WHERE ID_USER = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4j]", $requete);
  //
  // contacts
  $requete  = " delete FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
  $requete .= " WHERE ID_USER_1 = " . $id_user . " ";
  $requete .= " or ID_USER_2 = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4b]", $requete);
  //
  // sa présence dans les groupes
  $requete  = " delete FROM " . $PREFIX_IM_TABLE . "USG_USERGRP ";
  $requete .= " WHERE ID_USER = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4c]", $requete);
  //
  // Suppression des messages
  $requete  = " delete FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user;
  $requete .= " or ID_USER_DEST = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4d]", $requete);
  //
  // ShoutBox
  $requete  = " delete from " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $requete .= " or ID_USER_VOTE = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4f]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4g]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4h]", $requete);
  //
  // Bookmarks
  $requete  = " delete from " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE ";
  $requete .= " where ID_USER_VOTE = " . $id_user;
  $requete .= " or ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4m]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4n]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4p]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4r]", $requete);
  //
  // On désactive au lieu de supprimer afin que ça ne reste pas sur le serveur FTP !
  $requete  = " update " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " SET FIL_ONLINE = 'D' ";
  $requete .= " where ID_USER_AUT = " . $id_user;
  $requete .= " or ID_USER_DEST = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4q]", $requete);
  //$requete  = " delete from " . $PREFIX_IM_TABLE . "FIL_FILE ";
  //$requete .= " where ID_USER_AUT = " . $id_user;
  //$requete .= " or ID_USER_DEST = " . $id_user;
  //$result = mysqli_query($id_connect, $requete);
  //if (!$result) error_sql_log("[ERR-B4q]", $requete);
  //
  // On désactive au lieu de supprimer afin que ça ne reste pas sur le serveur FTP !
  $requete  = " update " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " SET FIB_ONLINE = 'D' ";
  $requete .= " where ID_USER = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4r]", $requete);
  //
  //
  // Suppression du compte
  $requete  = " delete FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE ID_USER = " . $id_user;
  $requete .= " LIMIT 1 "; // (to protect)
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-B4e]", $requete);  
}


function write_file($file, $text)
{
  if (is_writeable($file))
  {
    $fp = fopen($file, "w");
    fputs($fp, $text);
    fclose($fp);
  }
} 


function f_DelSpecialChar($string)
{
	$start = array("/À/","/Á/","/Â/","/Ã/","/Ä/","/Å/","/Æ/","/à/","/á/","/â/","/ã/",
	"/ä/","/å/","/æ/","/È/","/É/","/Ê/","/Ë/","/è/","/é/","/ê/","/ë/","/Ì/","/Í/","/Î/",
	"/Ï/","/ì/","/í/","/î/","/ï/","/Ò/","/Ó/","/Ô/","/Õ/","/Ö/","/Ø/","/ò/","/ó/","/ô/",
	"/õ/","/ö/","/ø/","/Ù/","/Ú/","/Û/","/Ü/","/ù/","/ú/","/û/","/ü/","/ß/","/Ç/","/ç/",
	"/Ð/","/ð/","/Ñ/","/ñ/","/Þ/","/þ/","/Ý/");
	$end = array("A","A","A","A","A","A","A","a","a","a","a","a","a","a","E","E",
	"E","E","e","e","e","e","I","I","I","I","i","i","i","i","O","O","O","O","O","O",
	"o","o","o","o","o","o","U","U","U","U","u","u","u","u","B","C","c","D","d","N",
	"n","P","p","Y");
	//
	$newString = preg_replace($start, $end, $string);
	//
	//return strtolower($newString);
	return $newString;
}


function f_clean_username($username)
{
  $username = 	trim($username);
  $username = 	strtolower($username);
  $username =   str_replace("'", "", $username);
  $username =   str_replace('"', '', $username);
  $username =   str_replace(" ", "_", $username);
  $username =   str_replace("--", "", $username); // pour éviter failles.
  $username =   str_replace("=", "", $username);
  $username =   str_replace("<", "", $username);
  $username =   str_replace(">", "", $username);
  $username =   str_replace("{", "", $username);
  $username =   str_replace("}", "", $username);
  $username =   str_replace(";", "", $username);
  $username =   str_replace("\\", "", $username);
  $username =   str_replace("/", "", $username);
  $username =   str_replace("%20", "", $username);
  $username =   str_replace("%", "", $username);
  $username =   str_replace("|", "", $username);
  //
	return $username;
}


function f_clean_name($name)
{
  $name = 	trim($name);
  //  $name = 	strtolower($name); NON !!!
  $name =   str_replace("'", "", $name);
  $name =   str_replace('"', '', $name);
  $name =   str_replace("  ", " ", $name); // doubles espaces
  //$name =   str_replace(" ", "_", $name);
  $name =   str_replace("--", "", $name); // pour éviter failles.
  $name =   str_replace("=", "", $name);
  $name =   str_replace("<", "", $name);
  $name =   str_replace(">", "", $name);
  $name =   str_replace("{", "", $name);
  $name =   str_replace("}", "", $name);
  $name =   str_replace(";", "", $name);
  $name =   str_replace("\\", "", $name);
  $name =   str_replace("/", "", $name);
  $name =   str_replace("%20", "", $name);
  $name =   str_replace("%", "", $name);
  $name =   str_replace("|", "", $name);
  //
	return $name;
}


function f_decode64_wd($txt)
{
  $txt = str_replace("|", "+", $txt);
  $txt = base64_decode($txt);
  $txt = trim($txt);
  //
	return $txt;
}


// enlever les signes égal en fin de codage
function f_encode64($txt)
{
  $txt = trim($txt);
  $txt = base64_encode($txt);
  if (substr($txt, -1, 1) == "=") $txt = substr($txt, 0, -1);
  if (substr($txt, -1, 1) == "=") $txt = substr($txt, 0, -1);
  //
	return $txt;
}


// JMA vers AMJ (DMY to YMD)
function convertdate_MDY_to_YMD($date) 
{
	$day = substr($date, 0, 2);
	$month = substr($date, 3, 2);
	$year = substr($date, 6, 4);
	$new_date = $year . "-" . $month . "-" . $day;
	//
 	return $new_date;
}


// AMJ vers JMA (YMD to DMY)
function convertdate_YMD_to_DMY($date) 
{
	$new_date = "";
	if ($date != '0000-00-00')
	{
		$day = substr($date, 8, 2);
		$month = substr($date, 5, 2);
		$year = substr($date, 0, 4);
		$new_date = $day  . "/" . $month . "/" . $year;
	}
	//
 	return $new_date;
}


if ( ! function_exists( 'exif_imagetype' ) ) 
{
  function exif_imagetype ( $filename ) 
  {
    if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) 
    {
      return $type;
    }
    return false;
  }
}


if (!function_exists('mime_content_type')) 
{
    function mime_content_type($filename) 
    {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

// pour éviter l'erreur : 
// Strict Standards: date(): It is not safe to rely on the system's timezone settings
if (function_exists('date_default_timezone_set'))
{
  $fus = ini_get('date.timezone');
  if ($fus != "") 
    date_default_timezone_set($fus);
  else
    date_default_timezone_set("Europe/Paris");
}

// If not works, use :
#date_default_timezone_set("Europe/Paris");

?>
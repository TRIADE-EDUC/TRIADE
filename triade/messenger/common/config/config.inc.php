<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **                  http://www.intramessenger.com    **
 **  Licence:        GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/

if ( !defined('INTRAMESSENGER') ) die(); 

define('_LANG', 'FR'); 
## EN / FR / IT / PT / BR / RO / DE / ES / NL  

define('_MAINTENANCE_MODE', ''); 
## To apply updates (pour effectuer les mises à jour).

 

# 
## 
###################################### USERS RESTRICTIONS OPTIONS ###################################### 
## 
# 
 
define('_ALLOW_CONFERENCE', 'X'); 
define('_ALLOW_HIDDEN_TO_CONTACTS', 'X'); 
define('_ALLOW_HIDDEN_STATUS', ''); 
define('_ALLOW_SMILEYS', 'X'); 
define('_ALLOW_CHANGE_CONTACT_NICKNAME', 'X'); 
define('_ALLOW_CHANGE_EMAIL_PHONE', 'X'); 
define('_ALLOW_CHANGE_FUNCTION_NAME', 'X'); 
define('_ALLOW_CHANGE_AVATAR', 'X'); 
define('_ALLOW_UPPERCASE_SPACE_USERNAME', 'X'); 
define('_ALLOW_SEND_TO_OFFLINE_USER', 'X'); 
define('_ALLOW_HISTORY_MESSAGES', 'X'); 
define('_ALLOW_HISTORY_MESSAGES_EXPORT', ''); 
define('_ALLOW_SKIN', 'X'); 
define('_ALLOW_POST_IT', ''); 
define('_ALLOW_CLOSE_IM', 'X'); 
define('_ALLOW_SOUND_USAGE', 'X'); 
define('_ALLOW_REDUCE_MAIN_SCREEN', 'X'); 
define('_ALLOW_REDUCE_MESSAGE_SCREEN', 'X'); 
define('_ALLOW_CONTACT_RATING', ''); 
## If not empty, allow user to rate their contacts (but cannot see average).
## If 'PUBLIC', users can see their contacts average.
# 
define('_ALLOW_EMAIL_NOTIFIER', ''); 
define('_INCOMING_EMAIL_SERVER_ADDRESS', ''); 
define('_INVITE_FILL_PROFILE_ON_FIRST_LOGIN', ''); 
define('_ALLOW_COL_FUNCTION_NAME', 'X'); 
define('_ALLOW_MANAGE_CONTACT_LIST', 'X'); 
define('_ALLOW_MANAGE_OPTIONS', 'X'); 
define('_ALLOW_MANAGE_PROFILE', 'X'); 
define('_FORCE_AWAY_ON_SCREENSAVER', 'X'); 
define('_FORCE_OPTION_FILE_FROM_SERVER', ''); 
define('_FORCE_STATUS_LIST_FROM_SERVER', ''); 
# 
## // example: 'On phone;Meeting;Not in front of screen;Back in 5 minutes;Eating' 
define('_AWAY_REASONS_LIST', ""); 
define('_ONLINE_REASONS_LIST', ""); 
define('_BUSY_REASONS_LIST', "Meeting;Eating;Working;Looking TV"); 
define('_DONOTDISTURB_REASONS_LIST', ""); 

# 
## 
###################################### SECURITY OPTIONS ###################################### 
## 
# 
 
define('_MINIMUM_USERNAME_LENGTH', '3'); 
define('_USER_NEED_PASSWORD', 'X'); 
define('_MINIMUM_PASSWORD_LENGTH', '4'); 
define('_PWD_NEED_DIGIT_LETTER', ''); 
define('_PWD_NEED_UPPER_LOWER', ''); 
define('_PWD_NEED_SPECIAL_CHARACTER', ''); 
define('_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER', '5'); 
define('_LOCK_DURATION', '0'); 
define('_PASSWORD_VALIDITY', '0'); 
define('_CRYPT_MESSAGES', ''); 
define('_CENSOR_MESSAGES', ''); 
define('_HISTORY_MESSAGES_ON_ACP', ''); 
define('_LOG_SESSION_OPEN', 'X'); 
define('_FORCE_UPDATE_BY_SERVER', ''); 
define('_FORCE_UPDATE_BY_INTERNET', 'X'); 
define('_SEND_ADMIN_ALERT', 'X'); 
define('_UNREAD_MESSAGE_VALIDITY', '90'); 
define('_LOCK_AFTER_NO_CONTACT_DURATION', '0'); 
define('_LOCK_AFTER_NO_ACTIVITY_DURATION', '0'); 
define('_ALLOW_USE_PROXY', 'X'); 
define('_PROXY_ADDRESS', ''); 
define('_PROXY_PORT_NUMBER', '0'); 
define('_PASSWORD_FOR_PRIVATE_SERVER', ""); 
## Use a long password, to improve security transfert (must be more them 5 characters !).

# 
## 
###################################### MAIN OPTIONS ###################################### 
## 
# 
 
define('_SERVERS_STATUS', ''); 
define('_TIME_ZONES', 'X'); 
define('_FLAG_COUNTRY_FROM_IP', ''); 
define('_GROUP_USER_CAN_JOIN', ''); 
define('_GROUP_FOR_SBX_AND_ADMIN_MSG', ''); 
define('_STATISTICS', 'X'); 
define('_PUBLIC_OPTIONS_LIST', ''); 
define('_PUBLIC_USERS_LIST', 'X'); 
define('_PUBLIC_POST_AVATAR', 'X'); 
define('_PUBLIC_FOLDER', 'public'); 

# 
## 
###################################### ADMIN OPTIONS ###################################### 
## 
# 
 
define('_SITE_TITLE', ""); 
define('_SITE_URL_TO_SHOW', ""); 
define('_SITE_TITLE_TO_SHOW', ""); 
define('_SCROLL_TEXT', ""); 
define('_ADMIN_EMAIL', ""); 
define('_ADMIN_PHONE', ""); 
define('_SEND_ADMIN_ALERT_EMAIL', ''); 
define('_MAX_NB_USER', '0'); 
define('_MAX_NB_SESSION', '0'); 
define('_MAX_NB_CONTACT_BY_USER', '0'); 
define('_MAX_NB_IP', '0'); 
define('_OUTOFDATE_AFTER_NOT_USE_DURATION', '90'); 
define('_CHECK_NEW_MSG_EVERY', '20'); 
define('_SLOW_NOTIFY', ''); 
define('_CHECK_VERSION_INTERNET', 'X'); 
define('_IM_ADDRESS_BOOK_PASSWORD', ''); 
define('_WAIT_STARTUP_IF_SERVER_UNAVAILABLE', ''); 
define('_FORCE_LAUNCH_ON_STARTUP', ''); 
define('_SKIN_FORCED_COLOR_CUSTOM_VERSION', '0-0-0'); 
define('_AUTO_ADD_CONTACT_USER_ID', ''); 
define('_ROLE_ID_DEFAULT_FOR_NEW_USER', '0'); 
define('_GROUP_ID_DEFAULT_FOR_NEW_USER', '0'); 

# 
## 
###################################### SPECIALS OPTIONS ###################################### 
## 
# 
 
define('_ACP_PROTECT_BY_HTACCESS', ''); 
define('_ACP_ALLOW_MEMORY_AUTH', ''); 
define('_SPECIAL_MODE_OPEN_COMMUNITY', ''); 
define('_SPECIAL_MODE_GROUP_COMMUNITY', ''); 
define('_SPECIAL_MODE_OPEN_GROUP_COMMUNITY', ''); 
define('_ROLES_TO_OVERRIDE_PERMISSIONS', ''); 
define('_ENTERPRISE_SERVER', ''); 
define('_FORCE_USERNAME_TO_PC_SESSION_NAME', ''); 
define('_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER', 'X'); 
define('_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER', ''); 
define('_PENDING_NEW_AUTO_ADDED_USER', ''); 
define('_PENDING_USER_ON_COMPUTER_CHANGE', ''); 
define('_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN', ''); 

# 
## 
###################################### BOOKMARKS ###################################### 
## 
# 
 
define('_BOOKMARKS', ''); 
define('_BOOKMARKS_VOTE', ''); 
define('_BOOKMARKS_PUBLIC', ''); 
define('_BOOKMARKS_NEED_APPROVAL', ''); 

# 
## 
###################################### SHOUTBOX ###################################### 
## 
# 
 
define('_SHOUTBOX', ''); 
define('_SHOUTBOX_REFRESH_DELAY', '60'); 
define('_SHOUTBOX_STORE_DAYS', '30'); 
define('_SHOUTBOX_STORE_MAX', '200'); 
define('_SHOUTBOX_QUOTA_USER_DAY', '50'); 
define('_SHOUTBOX_QUOTA_USER_WEEK', '0'); 
define('_SHOUTBOX_NEED_APPROVAL', ''); 
define('_SHOUTBOX_APPROVAL_QUEUE_USER', '3'); 
define('_SHOUTBOX_APPROVAL_QUEUE', '10'); 
define('_SHOUTBOX_LOCK_USER_APPROVAL', '0'); 
define('_SHOUTBOX_VOTE', ''); 
define('_SHOUTBOX_MAX_NOTES_USER_DAY', '0'); 
define('_SHOUTBOX_MAX_NOTES_USER_WEEK', '0'); 
define('_SHOUTBOX_REMOVE_MESSAGE_VOTES', '0'); 
define('_SHOUTBOX_LOCK_USER_VOTES', '0'); 
define('_SHOUTBOX_ALLOW_SCROLLING', 'X'); 
define('_SHOUTBOX_PUBLIC', ''); 

# 
## 
###################################### SHARE FILES ###################################### 
## 
# 
 
define('_SHARE_FILES', ''); 
define('_SHARE_FILES_EXCHANGE', ''); 
define('_SHARE_FILES_FOLDER', ""); 
define('_SHARE_FILES_FTP_ADDRESS', ""); 
define('_SHARE_FILES_FTP_LOGIN', ""); 
define('_SHARE_FILES_FTP_PASSWORD', ""); 
define('_SHARE_FILES_FTP_PASSWORD_CRYPT', ""); 
define('_SHARE_FILES_FTP_PORT_NUMBER', '0'); 
define('_SHARE_FILES_MAX_FILE_SIZE', '0'); 
define('_SHARE_FILES_MAX_NB_FILES_TOTAL', '0'); 
define('_SHARE_FILES_MAX_NB_FILES_USER', '0'); 
define('_SHARE_FILES_MAX_SPACE_SIZE_TOTAL', '0'); 
define('_SHARE_FILES_MAX_SPACE_SIZE_USER', '0'); 
define('_SHARE_FILES_NEED_APPROVAL', ''); 
define('_SHARE_FILES_EXCHANGE_NEED_APPROVAL', ''); 
define('_SHARE_FILES_APPROVAL_QUEUE', '20'); 
define('_SHARE_FILES_QUOTA_FILES_USER_WEEK', '0'); 
define('_SHARE_FILES_ALLOW_UPPERCASE', ''); 
define('_SHARE_FILES_ALLOW_ACCENT', 'X'); 
define('_SHARE_FILES_VOTE', ''); 
define('_SHARE_FILES_TRASH', ''); 
define('_SHARE_FILES_EXCHANGE_TRASH', ''); 
define('_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY', '30'); 
define('_SHARE_FILES_SCREENSHOT', ''); 
define('_SHARE_FILES_EXCHANGE_SCREENSHOT', 'X'); 
define('_SHARE_FILES_WEBCAM', ''); 
define('_SHARE_FILES_EXCHANGE_WEBCAM', ''); 
define('_SHARE_FILES_COMPRESS', ''); 
define('_SHARE_FILES_PROTECT', ''); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY', '0'); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK', '0'); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH', '0'); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY', '0'); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK', '0'); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH', '0'); 

# 
## 
###################################### BACKUP FILES ###################################### 
## 
# 
 
define('_BACKUP_FILES', ''); 
define('_BACKUP_FILES_MAX_NB_ARCHIVES_USER', '3'); 
define('_BACKUP_FILES_MAX_ARCHIVE_SIZE', '0'); 
define('_BACKUP_FILES_MAX_SPACE_SIZE_USER', '0'); 
define('_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL', '0'); 
define('_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY', ""); 
define('_BACKUP_FILES_FORCE_EVERY_DAY_AT', ''); 
define('_BACKUP_FILES_ALLOW_MULTI_FOLDERS', ''); 
define('_BACKUP_FILES_ALLOW_SUB_FOLDERS', 'X'); 
define('_BACKUP_FILES_FTP_ADDRESS', ""); 
define('_BACKUP_FILES_FTP_LOGIN', ""); 
define('_BACKUP_FILES_FTP_PASSWORD', ""); 
define('_BACKUP_FILES_FTP_PASSWORD_CRYPT', ""); 
define('_BACKUP_FILES_FTP_PORT_NUMBER', '21'); 
define('_BACKUP_FILES_FOLDER', ""); 

# 
## 
###################################### EXTERNAL AUTHENTICATION OPTIONS ###################################### 
## 
# 
 
define('_EXTERNAL_AUTHENTICATION', ''); 
define('_EXTERN_URL_TO_REGISTER', ""); 
define('_EXTERN_URL_FORGET_PASSWORD', ""); 
define('_EXTERN_URL_CHANGE_PASSWORD', ""); 


## Server change is public:
define('_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL', ""); 
## Only for authenticated users:
define('_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN', ""); 


# Server version: 2.0.6.252
# Options date: 2022-12-25

?>
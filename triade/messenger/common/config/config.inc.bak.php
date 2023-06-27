<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2014 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **                  http://www.intramessenger.com    **
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


define("_LANG", "EN");
## Server language (EN or FR or PT or IT or RO or DE or ES or NL).


define("_MAINTENANCE_MODE", "");
## Maintenance mode (to apply updates).


#
##
###################################### ADMIN OPTIONS ######################################
##
#

define("_MAX_NB_USER", "0");
## Maximum users number ('0' : unlimited).

define("_MAX_NB_SESSION", "0");
## Max number of sessions (users online at same time) (less or egal at _MAX_NB_USER) ('0' : unlimited).

define("_MAX_NB_CONTACT_BY_USER", "0");
## Maximum contacts number by user ('0' : unlimited).

define("_MAX_NB_IP", "0");
## Maximum simultaneous IP addresses (0 : unlimited)

define("_DISPLAY_USER_FLAG_COUNTRY", "");
define("_FLAG_COUNTRY_FROM_IP", "");
## display country flag of IP address (internet usage) in session list 
##      (see /admin/geoip/index.html for monthly update list).

define("_OUTOFDATE_AFTER_X_DAYS_NOT_USE", "90");
define("_OUTOFDATE_AFTER_NOT_USE_DURATION", "90");
## Out-of-date (action in admin user list) users after X days not use ('0' : unlimited).

define("_CHECK_NEW_MSG_EVERY", "20");
## The clients check for new message (from anybody) every ... seconds (10 to 60).
## On chat (when chat with someone), it's every 3 seconds.

define("_FULL_CHECK", "");
define("_SLOW_NOTIFY", "");

define("_STATISTICS", "X");
## To store/display statistics (in admin area).

define("_PUBLIC_FOLDER", "public");
## Folder visible for users.

define("_PUBLIC_OPTIONS_LIST" , "");
## Options list is public.

define("_PUBLIC_USERS_LIST" , "X");
## Users list is public

define("_PUBLIC_POST_AVATAR" , "X");
## Everybody can propose avatars

define("_SERVERS_STATUS" , "");



#
##
######################################## USERS RESTRICTIONS OPTIONS ######################################################
##
#


define("_FORCE_USERNAME_TO_PC_SESSION_NAME", "");
## if not empty, login is username (login on computer : %USERNAME%) (good on LDAP), if empty user can choise his nickname.

define("_ALLOW_UPPERCASE_SPACE_USERNAME", "X");

define("_ALLOW_CONFERENCE", "X");
## if not empty, allow users to create multi-user conferences.

define("_ALLOW_INVISIBLE", "X");
define("_ALLOW_HIDDEN_TO_CONTACTS", "X");
## if not empty, allow to be invisible (hidden when online) (see VIP). 
## (can make a few slow display (sql request) of online users).

define("_ALLOW_HIDDEN_STATUS", "");

define("_ALLOW_SMILEYS", "X");
## allow send smileys (display by pictures).

define("_ALLOW_CHANGE_CONTACT_NICKNAME", "X");
## if not empty, allow to change nickname of contact (in his list).

define("_ALLOW_CHANGE_EMAIL_PHONE", "X");
## if not empty, allow users to change their phone number and email adresse.

define("_ALLOW_CHANGE_FUNCTION_NAME", "X");
## if not empty, allow users to change their name/function (display behind username).

define("_ALLOW_CHANGE_AVATAR", "X");
## if not empty, allow users to change their avatar (picture).

define("_ALLOW_SEND_TO_OFFLINE_USER", "X");
## if not empty, allow to send message to offline contact.

define("_ALLOW_USER_TO_HISTORY_MESSAGES", "X");
define("_ALLOW_HISTORY_MESSAGES", "X");
## Lock user to history (log/save) messages.

define("_ALLOW_HISTORY_MESSAGES_EXPORT", "");

define("_ALLOW_USE_PROXY", "X");
## if not empty, allow user to use proxy server.

define("_ALLOW_SKIN", "X");
define("_ALLOW_POST_IT", "");
define("_ALLOW_CLOSE_IM", "X");
define("_ALLOW_SOUND_USAGE", "X");
define("_ALLOW_REDUCE_MAIN_SCREEN", "X");
define("_ALLOW_REDUCE_MESSAGE_SCREEN", "X");

define("_ALLOW_USER_RATING", "");
define("_ALLOW_CONTACT_RATING", "");
## if not empty, allow user to rate their contacts (but cannot see average).
## if "PUBLIC", users can see their contacts average.

define("_ALLOW_EMAIL_NOTIFIER", "");
## if not empty, allow use email notifier.

define("_INCOMING_EMAIL_SERVER_ADDRESS", "");
## Force incoming mail server address (for notifier).

define("_FORCE_AWAY_ON_SCREENSAVER", "X");
## if not empty, force user state to be 'away' when screensaver (force and hide option to client).

define("_INVITE_FILL_PROFILE_ON_FIRST_LOGIN" , "");

define("_ALLOW_COL_FUNCTION_NAME", "X");
define("_HIDE_COL_FUNCTION_NAME", "");
## if not empty, hide col 'name/function' (service).

define("_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", "");
## if not empty, display col 'level' and active hiearchic user management.

define("_LOCK_USER_CONTACT_LIST", "");
## if not empty, disable manage contacts list (only the admin can do) and alarm set (for school, internet-cafe).
define("_ALLOW_MANAGE_CONTACT_LIST", "X");

define("_LOCK_USER_OPTIONS", "");
## if not empty, disable access to options screen and alarm set (for school, internet-cafe).
define("_ALLOW_MANAGE_OPTIONS", "X");

define("_LOCK_USER_PROFILE", "");
define("_ALLOW_MANAGE_PROFILE", "X");

define("_FORCE_OPTION_FILE_FROM_SERVER", "");

define("_FORCE_STATUS_LIST_FROM_SERVER", "");
## if not empty, force (send) status list (Away, busy...) from server (in server language).

## List reasons to be in away state.
define("_AWAY_REASONS_LIST", ""); // example : "On phone;Meeting;Not in front of screen;Back in 5 minutes;Eating"
define('_ONLINE_REASONS_LIST', ""); 
define('_BUSY_REASONS_LIST', "Meeting;Eating;Working;Looking TV"); 
define('_DONOTDISTURB_REASONS_LIST', ""); 


#
##
######################################## SECURITY OPTIONS ######################################################
##
#


define("_MINIMUM_USERNAME_LENGTH", "3");
## Minimum length of the username (nickname)     >= 3

define("_USER_NEED_PASSWORD", "X");
## if not empty, user need password (must active if user can choise his nickname !  so dont need if _FORCE_USERNAME_TO_PC_SESSION_NAME).

define("_MINIMUM_PASSWORD_LENGTH", "4");
## Minimum length of the password for users (if _USER_NEED_PASSWORD not empty)   >= 4

define('_PWD_NEED_DIGIT_LETTER', ''); 
define('_PWD_NEED_UPPER_LOWER', ''); 
define('_PWD_NEED_SPECIAL_CHARACTER', ''); 

define("_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", "5");
## Maximum password consecutive errors, before server lock user (2 to 20).
define("_LOCK_DURATION", "0");

define("_PASSWORD_VALIDITY", "0");

define("_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER", "");
define("_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER", "X");
## if not empty, every new users are automaticly added in list.

define("_PENDING_NEW_AUTO_ADDED_USER", "");
## if not empty, automaticly users added have to be valided by admin (empty 'WAIT' in colum 'USR_CHECK' of 'T_USR_USER').

define("_PENDING_USER_ON_COMPUTER_CHANGE", "");
## if not empty, user chang check (change PC) have to be valided by admin (empty 'USR_CHECK' on table 'T_USR_USER').

define("_CRYPT_MESSAGES", "");
## if not empty, crypt messages.

define("_CENSOR_MESSAGES", "");

define("_LOG_MESSAGES", "");
## if not empty, messages are saved in a log file on the server (/distant/log/log_messages.txt).
## (example : for school) (_CRYPT_MESSAGES must be empty).
define("_HISTORY_MESSAGES_ON_ACP", "");

define("_LOG_SESSION_OPEN", "X");
## if not empty, log all session open (log IP, date and time). Need writing rights on /distant/log/log_open_session.txt !!!

define("_PASSWORD_FOR_PRIVATE_SERVER", "");
## if not empty, it's the password for client/PC authenfition to server. If empty, it's a public server.
## Use a long password, to improve security transfert (must be more them 5 characters !

define("_FORCE_UPDATE_BY_SERVER", "");
## Force clients to update from server only. 
## User cannot desactivate, and cannot choise 'by internet' (download by official website).

define("_FORCE_UPDATE_BY_INTERNET", "X");
## Force clients to update from internet official website.
## User cannot desactivate, and cannot choise 'by server'.

define("_SEND_ADMIN_ALERT", "X");
## if not empty, send admin alert to 'administrators' users (when activate : "get admin alert").
## Example : pending users (after password errors), pending avatars...

define("_UNREAD_MESSAGE_VALIDITY", "90");
define("_LOCK_AFTER_NO_ACTIVITY_DURATION", "0");
define("_LOCK_AFTER_NO_CONTACT_DURATION", "0");

define("_PROXY_ADDRESS", "");
## Force proxy server address.

define("_PROXY_PORT_NUMBER", "");
## Force proxy server port number.


#
##
###################################### ADMIN OPTIONS ######################################
##
#


define("_SITE_URL_TO_SHOW", "");
## (for internet) If you want to display url of your internet website (not the url of intramessenger ! example : http://www.instanttimezone.com).

define("_SITE_TITLE", "");
define("_SITE_TITLE_TO_SHOW", "");
## If you want to display a title (advertising) for your internet web server.

define("_SCROLL_TEXT", "");
## Scrolling information message.

define("_ADMIN_EMAIL", "");
## Admin email address (to display in "About" client screen).

define("_ADMIN_PHONE", "");
## Admin phone number (to display in "About" client screen).

define("_SEND_ADMIN_ALERT_EMAIL", "");

define("_ENTERPRISE_SERVER", "");
## Enterprise mode : get installed software versions and can stop/reboot computers

define("_ROLES_TO_OVERRIDE_PERMISSIONS", "");

define("_ROLE_ID_DEFAULT_FOR_NEW_USER", "0");

define("_IM_ADDRESS_BOOK_PASSWORD", "");
## Password to write for registry on internet directory (without space !) :
##
##            http://www.intramessenger.net/list/servers/

define("_GROUP_FOR_SBX_AND_ADMIN_MSG", "");
## Allow to manage groups, only to send admin messages (use only if _SPECIAL_MODE_GROUP_COMMUNITY empty) and for shoutbox

define("_GROUP_USER_CAN_JOIN", "");

define("_CHECK_VERSION_INTERNET", "X");

define("_TIME_ZONES", "X");

define("_WAIT_STARTUP_IF_SERVER_UNAVAILABLE", "");

define("_FORCE_LAUNCH_ON_STARTUP", "");

define("_SKIN_FORCED_COLOR_CUSTOM_VERSION", "0-0-0");

define("_AUTO_ADD_CONTACT_USER_ID", "");
## Auto add userID list, in contact list (to new users). example : "2;15;18"

define("_ACP_PROTECT_BY_HTACCESS", "");

define("_ACP_ALLOW_MEMORY_AUTH", "");

define("_GROUP_ID_DEFAULT_FOR_NEW_USER", "");


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
define('_SHOUTBOX_LOCK_USER_APPROVAL', ''); 
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
define('_SHARE_FILES_FOLDER', ''); 
define('_SHARE_FILES_FTP_ADDRESS', ''); 
define('_SHARE_FILES_FTP_LOGIN', ''); 
define('_SHARE_FILES_FTP_PASSWORD', ''); 
define('_SHARE_FILES_FTP_PASSWORD_CRYPT', ''); 
define('_SHARE_FILES_FTP_PORT_NUMBER', ''); 
define('_SHARE_FILES_MAX_FILE_SIZE', ''); 
define('_SHARE_FILES_MAX_NB_FILES_TOTAL', ''); 
define('_SHARE_FILES_MAX_NB_FILES_USER', ''); 
define('_SHARE_FILES_MAX_SPACE_SIZE_TOTAL', ''); 
define('_SHARE_FILES_MAX_SPACE_SIZE_USER', ''); 
define('_SHARE_FILES_NEED_APPROVAL', ''); 
define('_SHARE_FILES_EXCHANGE_NEED_APPROVAL', ''); 
define('_SHARE_FILES_APPROVAL_QUEUE', ''); 
define('_SHARE_FILES_QUOTA_FILES_USER_WEEK', ''); 
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
define('_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY', ''); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK', ''); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH', ''); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY', ''); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK', ''); 
define('_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH', ''); 


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
define('_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY', ''); 
define('_BACKUP_FILES_FORCE_EVERY_DAY_AT', ''); 
define('_BACKUP_FILES_ALLOW_MULTI_FOLDERS', ''); 
define('_BACKUP_FILES_ALLOW_SUB_FOLDERS', 'X'); 
define('_BACKUP_FILES_FTP_ADDRESS', ''); 
define('_BACKUP_FILES_FTP_LOGIN', ''); 
define('_BACKUP_FILES_FTP_PASSWORD', ''); 
define('_BACKUP_FILES_FTP_PASSWORD_CRYPT', ''); 
define('_BACKUP_FILES_FTP_PORT_NUMBER', '21'); 
define('_BACKUP_FILES_FOLDER', ''); 

#
##
###################################### SPECIALS OPTIONS ######################################
##
#


# If you want a special mode, you can active ONE (ONLY) of this 2 options :

define("_SPECIAL_MODE_OPEN_COMMUNITY", "");
## Everybody can see everybody, without add to contact list (example: school, internet cafe...). 
## Add to contact for hide someone. You may active _ALLOW_INVISIBLE, 
## and unactive : _ALLOW_SEND_TO_OFFLINE_USER, _ALLOW_CHANGE_CONTACT_NICKNAME and _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN).

define("_SPECIAL_MODE_GROUP_COMMUNITY", "");
## Everybody can see everybody of SAME GROUP(s), without add to contact list.
## Contact list is disabled and related options :  _ALLOW_CHANGE_CONTACT_NICKNAME
## _LOCK_USER_CONTACT_LIST  _ALLOW_SEND_TO_OFFLINE_USER  _MAX_NB_CONTACT_BY_USER  _ALLOW_INVISIBLE
## and unactive option : _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN  

define("_SPECIAL_MODE_OPEN_GROUP_COMMUNITY", "");

#
##
############################### AUTHENTICATION OPTIONS #####################################
##
#

define('_EXTERNAL_AUTHENTICATION', ''); 

define("_EXTERN_URL_TO_REGISTER", "");
## Address to register (phpBB, VBulletin, Joomla, Phenix Agenda, Dolibarr, dotProject, eGroupWare, Ovidentia...).

define("_EXTERN_URL_FORGET_PASSWORD", "");
## address to get back forgotten password (by extern authentication).

define("_EXTERN_URL_CHANGE_PASSWORD", "");
## Address to change password (by extern authentication) : replace button on profil (client).




#
##############################################################################################
#

define("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL", "");
## ONLY to redirect user to another URL (server address) !!!

define("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN", "");

?>
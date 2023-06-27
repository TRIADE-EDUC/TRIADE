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
//
require ("../common/display_errors.inc.php"); 
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
//
$url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
if (substr($url, strlen($url)-1, 1) != "/") $url .= "/";
$server = trim($_SERVER['SERVER_NAME']);
$bad_config_to_public_book = "";
if (_MAINTENANCE_MODE != "") $bad_config_to_public_book .= "_MAINTENANCE_MODE : " . $l_admin_options_maintenance_mode . "<BR/>";
if (_ENTERPRISE_SERVER != "") $bad_config_to_public_book .= "_ENTERPRISE_SERVER : " . $l_admin_options_enterprise_server . "<BR/>";
if (_PASSWORD_FOR_PRIVATE_SERVER != "") $bad_config_to_public_book .= "_PASSWORD_FOR_PRIVATE_SERVER : " . $l_admin_options_password_for_private_server . "<BR/>";
if (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") $bad_config_to_public_book .= "_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER : " . $l_admin_options_auto_add_user . "<BR/>";
if (_PENDING_USER_ON_COMPUTER_CHANGE != "") $bad_config_to_public_book .= "_PENDING_USER_ON_COMPUTER_CHANGE : " . $l_admin_options_need_admin_if_chang_check . "<BR/>";
if (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != "") $bad_config_to_public_book .= "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN : " . $l_admin_options_hierachic_management . "<BR/>";
if (_FORCE_USERNAME_TO_PC_SESSION_NAME != "") $bad_config_to_public_book .= "_FORCE_USERNAME_TO_PC_SESSION_NAME : " . $l_admin_options_is_usernamePC . "<BR/>";
if (_FORCE_UPDATE_BY_SERVER != "") $bad_config_to_public_book .= "_FORCE_UPDATE_BY_SERVER : " . $l_admin_options_force_update_by_server . "<BR/>";
if (_FORCE_UPDATE_BY_INTERNET == "") $bad_config_to_public_book .= "_FORCE_UPDATE_BY_INTERNET : " . $l_admin_options_force_update_by_internet . "<BR/>";
if (_USER_NEED_PASSWORD == "") $bad_config_to_public_book .= "_USER_NEED_PASSWORD : " . $l_admin_options_password_user . "<BR/>";
if (_HISTORY_MESSAGES_ON_ACP != "") $bad_config_to_public_book .= "_HISTORY_MESSAGES_ON_ACP : " . $l_admin_options_log_messages . "<BR/>";
if ( (_CHECK_VERSION_INTERNET == "") and ($bad_config_to_public_book != "") ) $bad_config_to_public_book .= "_CHECK_VERSION_INTERNET : " . $l_admin_options_check_version_internet . "<BR/>";
if ( (intval(_MAX_NB_USER) < 100) and (intval(_MAX_NB_USER) >0) ) $bad_config_to_public_book .= "_MAX_NB_USER : " . $l_admin_options_nb_max_user . "<BR/>";
if ( (intval(_MAX_NB_SESSION) < 50) and (intval(_MAX_NB_SESSION) > 0) ) $bad_config_to_public_book .= "_MAX_NB_SESSION : " . $l_admin_options_nb_max_session . "<BR/>";
//
if ($bad_config_to_public_book != "")
{
  if (_IM_ADDRESS_BOOK_PASSWORD == "") $bad_config_to_public_book = "_IM_ADDRESS_BOOK_PASSWORD : " . $l_admin_options_pass_register_book . " " . $l_admin_options_info_book . "<BR/><font color='green'>" . $l_admin_options_book_password . "</font><BR/><BR/>" . $bad_config_to_public_book;
}
else
  if (_IM_ADDRESS_BOOK_PASSWORD == "") $bad_config_to_public_book = "<BR/>" . "_IM_ADDRESS_BOOK_PASSWORD : " . $l_admin_options_pass_register_book . " " . $l_admin_options_info_book . "<BR/>" . $bad_config_to_public_book;
//
//
if ($bad_config_to_public_book == "")
{
  if (strlen($url) > 100) $bad_config_to_public_book = "Server address too long.";
  if ( ($server == "127.0.0.1") or ($server == "localhost") ) 
  {
    $bad_config_to_public_book = "Cannot access to this server address from internet !";
  }
  if ( ($bad_config_to_public_book == "") and (substr_count($server, ".") == 3) )
  {
    $server = str_replace(".", "", $server);
    if ($server == intval($server))
    {
      $bad_config_to_public_book = "Not an address IP server, must use a domain name !";
    }
  }
}
if ($bad_config_to_public_book == "") 
{
  header("location:../register.php?lang=" . $lang . "&");
  die();
}
//
//
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_info_book . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
//require ("../common/sql.inc.php");
//
echo "<font face=verdana size=2>";
echo "<BR/>";
//
//
if ($bad_config_to_public_book == "") 
{
  echo "<font color='green'>" . $l_admin_options_info_7 . "</font> : <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A><BR/>";
  echo "<BR/>";
}
else
{
  echo "<font color='red'>" . $l_admin_options_info_8 . "</font> : <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A><BR/>";
  echo "<BR/>";
  echo "</center>";
  echo  " <IMG SRC='" . _FOLDER_IMAGES . "annu_config_error.png' WIDTH='16' HEIGHT='16' ALIGN='LEFT' />";
  echo $l_admin_options_may_change_option . " :<BR/>";
  echo "<BR/>";
  echo $bad_config_to_public_book . "<BR/>";
} 
//
display_menu_footer();
//
echo "</body></html>";
?>
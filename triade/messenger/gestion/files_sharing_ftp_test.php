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
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_published_files);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_title . "</title>";
display_header();
echo "</head>";
echo "<body>";
//
display_menu();
//
$port_num = intval(_SHARE_FILES_FTP_PORT_NUMBER);
if ( ($port_num <= 0) or ($port_num > 65535) ) $port_num = 21;
//
echo "<font face='verdana' size='2'>";
if ( _SHARE_FILES != '' )
{
  if (_SHARE_FILES_FOLDER != "")
  {
    echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _SHARE_FILES_FTP_PASSWORD / _SHARE_FILES_FOLDER</SMALL></div>";
    echo "<br/>";
    echo "<br/>";
  }
  //
  if ( ( _SHARE_FILES_FTP_ADDRESS != '' ) and ( _SHARE_FILES_FTP_LOGIN != '' ) and ( _SHARE_FILES_FTP_PASSWORD != '' ) )
  {
    //echo "Test FTP access: " . _SHARE_FILES_FTP_LOGIN . "@" . _SHARE_FILES_FTP_ADDRESS . "<br/>\n";
    echo "Test FTP server: " . _SHARE_FILES_FTP_ADDRESS . "<br/>\n";
    //
    $conn_id = ftp_connect(_SHARE_FILES_FTP_ADDRESS, $port_num) or die("Couldn't connect to FTP server!"); 
    //
    echo "<br/>";
    echo "Test FTP access: " . _SHARE_FILES_FTP_LOGIN . "<br/>\n";
    if (@ftp_login($conn_id, _SHARE_FILES_FTP_LOGIN, _SHARE_FILES_FTP_PASSWORD)) 
    {
      echo "<br/>";
      echo "<font color='green'><b>" . "Connected" . "</b> (" . "FTP access works fine" . ")</font>";
    } 
    else 
    {
      echo "<br/>";
      echo "<font color='red'>" . "Failed to connect as " . _SHARE_FILES_FTP_LOGIN . "</font>";
    }
    //
    ftp_close($conn_id);
  }
  else
  {
    echo $l_admin_roles_unactivated_options . ": _SHARE_FILES_FTP_ADDRESS &nbsp; _SHARE_FILES_FTP_LOGIN &nbsp; _SHARE_FILES_FTP_PASSWORD";
  }
}
else
{
  echo "<BR/>";
  echo $l_admin_share_files_cannot . "<BR/>";
}
display_menu_footer();
//
echo "</body></html>";
?>
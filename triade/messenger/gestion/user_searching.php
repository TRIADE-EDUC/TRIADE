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
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
//if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
//if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_users_unlock);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_users_searching . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";

echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
echo "<TD align='center' COLSPAN='3' class='catHead'>";
echo "<font face=verdana size=3><b>" . $l_admin_users_searching . "</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";
	
echo "<FORM METHOD='GET' ACTION='list_users.php?'>";
echo "<TD class='row2'>";
echo "<font face=verdana size=2>&nbsp;";
echo $l_admin_session_col_user;
echo "&nbsp;</TD>";
echo "<TD class='row2'>";
echo "<input type='text' name='only_users' maxlength='20' value='' size='15' class='post' />";
echo "</TD>";
echo "<TD class='row2'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_search . "' class='liteoption' />";
echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD></TR>";
echo "</FORM>";
//
echo "<FORM METHOD='GET' ACTION='list_users.php?'>";
echo "<TD class='row2'>";
echo "<font face=verdana size=2>&nbsp;";
echo $l_admin_session_col_ip;
echo "&nbsp;</TD>";
echo "<TD class='row2'>";
echo "<input type='text' name='only_ip' maxlength='23' value='' size='15' class='post' />";
echo "</TD>";

echo "<TD class='row2'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_search . "' class='liteoption' />";
echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD></TR>";
echo "</FORM>";
//
echo "</TABLE>";
//
display_menu_footer();
//
echo "</body></html>";
?>
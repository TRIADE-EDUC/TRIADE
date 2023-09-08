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
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_administrators);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_acp_admin_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _ACP_PROTECT_BY_HTACCESS != '')
{
  echo "<div class='warning'>";
  echo $l_admin_acp_admin_warning_1;
  echo "<br/>";
  echo $l_admin_acp_admin_warning_2;
  echo "</div>";
}

//
//
echo "<BR/>";
echo "<FORM METHOD='POST' ACTION='admin_acp_add.php?'>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
echo "<TH align='center' COLSPAN='3' class='thHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_acp_admin_title . "&nbsp;</b></font>";
echo "</TH>";
echo "</TR>";

echo "<TR>";
echo "<TD align='center' COLSPAN='3' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_acp_admin_create . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
//
echo "<TR>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>&nbsp;";
echo $l_admin_acp_auth_username;
echo "</TD>";
echo "<TD class='row1' align='center'>";
echo "<input type='text' name='adm_username' maxlength='20' value='' size='30' class='post' />";
//echo "<BR/>";
echo "</TD>";
echo "</TR>";
//
echo "<TR>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>&nbsp;";
echo $l_admin_acp_auth_password . " [*]";
echo "</TD>";
echo "<TD class='row1'align='center'>";
echo "<input type='password' name='adm_pass' maxlength='20' value='' size='30' class='post' />";
echo "</TD>";
echo "</TR>";
//
//
echo "<TR>";
echo "<TD align='center' COLSPAN='3' class='row2'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "</TR>";
echo "<TR>";
//
echo "</TABLE>";	//
echo "</FORM>";
echo "[*] " . $l_admin_acp_admin_at_least;
/*
}
else
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_acp_admin_cannot;
  echo "</div>";
}
*/
//
display_menu_footer();
//
echo "</body></html>";
?>
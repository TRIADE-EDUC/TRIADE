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
check_acp_rights(_C_ACP_RIGHT_servers_status);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_servers_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _SERVERS_STATUS != '' )
{
  //
  //
	echo "<BR/>";
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
	echo "<TR>";
	echo "<TH align='center' COLSPAN='3' class='thHead'>";
	echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_servers_list . "&nbsp;</b></font>";
	echo "</TH>";
	echo "</TR>";

	echo "<TR>";
	echo "<TD align='center' COLSPAN='3' class='catHead'>";
	echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_servers_creat . "&nbsp;</b></font>";  // -------------- Create group ----------------
	echo "</TD>";
	echo "</TR>";
	echo "<TR>";
	
  echo "<FORM METHOD='POST' ACTION='server_status_add.php?'>";
  echo "<TD class='row1' align='left'>";
  echo "<font face=verdana size=2>&nbsp;";
  echo $l_admin_servers_col_server . " : <input type='text' name='srv_name' maxlength='60' value='' size='40' class='post' />";
  //echo "<BR/>";
  echo "</TD>";
  echo "<TD class='row1' COLSPAN='2' align='right'>";
  //echo " ";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</TD></TR>";
  echo "</FORM>";
  //
	//
	echo "<TR>";
	echo "<TD align='center' COLSPAN='3' class='row2'> &nbsp;</TD>";
	echo "</TR>";
	echo "<TR>";
	//
	echo "</TABLE>";	//
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_servers_cannot;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
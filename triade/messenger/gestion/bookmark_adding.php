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
check_acp_rights(_C_ACP_RIGHT_bookmars);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_bookmarks_title . "</title>";
display_header();
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _BOOKMARKS != '' )
{
  //
  //
	echo "<BR/>";
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
	echo "<TR>";
	echo "<TH align='center' COLSPAN='3' class='thHead'>";
	echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_bookmarks_title . "&nbsp;</b></font>";
	echo "</TH>";
	echo "</TR>";

	echo "<TR>";
	echo "<TD align='center' COLSPAN='3' class='catHead'>";
	echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_bookmarks_creat . "&nbsp;</b></font>";
	echo "</TD>";
	echo "</TR>";
  echo "<FORM METHOD='POST' ACTION='bookmark_add.php?'>";

	echo "<TR>";
  echo "<TD class='row1' align='left'>";
  echo "<font face=verdana size=2>&nbsp;";
  echo $l_admin_bookmarks_url_title . "&nbsp;";
  echo "</TD><TD>";
  echo "<input type='text' name='bmk_title' maxlength='80' value='' size='50' class='post' />";
  echo "</TD>";
  echo "</TR>";
  //
	echo "<TR>";
  echo "<TD class='row1' align='left'>";
  echo "<font face=verdana size=2>&nbsp;";
  echo $l_admin_bookmarks_url_address . "&nbsp;";
  echo "</TD><TD>";
  echo "<input type='text' name='bmk_url' maxlength='250' value='' size='50' class='post' />";
  echo "</TD>";
  echo "</TR>";
  //
	//
	echo "<TR>";
	echo "<TD align='center' COLSPAN='3' class='catBottom'>"; //  &nbsp;</TD>
  echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
  echo "</TD></TR>";
  echo "</FORM>";
	//
	echo "</TABLE>";	//
}
else
{
  echo "<BR/>";
  echo $l_admin_bookmarks_cannot . "<BR/>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
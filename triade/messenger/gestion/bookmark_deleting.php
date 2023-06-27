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
if (isset($_POST['id_book'])) $id_book = intval($_POST['id_book']);  else $id_book = 0;
if (isset($_POST['bmk_title'])) $bmk_title = $_POST['bmk_title'];  else $bmk_title = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
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
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _BOOKMARKS != '' )
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<B>" . $bmk_title . "</B>";
  echo "<FORM METHOD='POST' name='formulaire' ACTION ='bookmark_delete.php?'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . " ?' />";
  echo "<INPUT TYPE='hidden' name='id_book' value = '" . $id_book . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</FORM>";
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
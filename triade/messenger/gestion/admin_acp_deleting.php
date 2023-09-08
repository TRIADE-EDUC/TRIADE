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
if (isset($_GET['id_admin'])) $id_admin = intval($_GET['id_admin']);  else $id_admin = 0;
if (isset($_GET['adm_username'])) $adm_username = $_GET['adm_username'];  else $adm_username = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
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
//if ( _ACP_PROTECT_BY_HTACCESS == '' )
echo "<BR/>";
echo "<BR/>";
echo "<B>" . $adm_username . "</B>";
echo "<FORM METHOD='GET' name='formulaire' ACTION ='admin_acp_delete.php?'>";
echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . " ?' />";
echo "<INPUT TYPE='hidden' name='id_admin' value = '" . $id_admin . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</FORM>";
/*
}
else
{
  echo "<BR/>";
  echo $l_admin_acp_admin_cannot . "<BR/>";
}
*/
//
display_menu_footer();
//
echo "</body></html>";
?>
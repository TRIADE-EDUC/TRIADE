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
check_acp_rights(_C_ACP_RIGHT_groups);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . "htaccess" . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="10;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
//
if (_ACP_PROTECT_BY_HTACCESS == "")
{
  echo "<br/>";
  echo "<br/>";
  echo "<br/>";
  echo "<div class='warning'>";
  echo $l_admin_htaccess_cannot;
  echo "</div>";
  die();
}
//
$rep = dirname($_SERVER['SCRIPT_FILENAME']) . "/";
$rep_pass = $rep;
if (substr($rep_pass, 1, 1) == ":") $rep_pass = substr($rep_pass, 2, strlen($rep_pass) -2);
$file_passwd = $rep . "/" . ".htpasswd";
$file_access = $rep . "/" . ".htaccess";
echo "<BR/>";
echo $l_admin_htaccess_1 . "<BR/>";
echo "<font color='gray'>" . $l_admin_htaccess_2 . "</font><BR/>";
echo "<BR/>";
if ( (file_exists($file_passwd) == false) and (file_exists($file_access) == false) )
{
  echo "<BR/>";
  echo "<BR/>";
  echo $l_admin_htaccess_3 . "<BR/>";
  //echo $l_admin_users_order_login . " : <B><I>admin</B></I>  &nbsp;/&nbsp;  " . 
  //echo strtolower($l_admin_users_col_password) . " : <B><I>www.theuds.com</B></I><BR/>";
  echo "<BR/>";

  echo "<FORM METHOD='GET' name='formulaire' ACTION ='htaccess_create.php?'>";
  echo "<table width='450' cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR>";
  echo "<TH colspan='4' class='thHead'>";
  echo "<FONT size='3'><B>";
  echo $l_admin_htaccess_create_files;
  echo "</TD>";
  echo "</TR>";
  echo "\n";
		echo "<TR>";
		echo "<TD class='row2' colspan='2'>";
			echo "<font face='verdana' size='2'>" . $l_server . " : <I>" . $_SERVER['SERVER_SOFTWARE'] . "</font>";
		echo "</TD>";
		echo "</TR>";
		echo "<TR>";
		echo "<TD class='row1'>";
      echo "<font face='verdana' size='2'>";
      echo "<INPUT name='os' TYPE='radio' VALUE='windows' class='genmed' ";
      if (substr_count ($_SERVER['SERVER_SOFTWARE'], "(Win") > 0) echo "CHECKED";
      echo " /> Windows";
      echo "<BR/>";
      echo "<INPUT name='os' TYPE='radio' VALUE='other' class='genmed' ";
      if (substr_count ($_SERVER['SERVER_SOFTWARE'], "(Win") <= 0) echo "CHECKED";
      echo " /> Linux/Unix/Mac...";
		echo "</TD>";
		echo "<TD class='row1'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_users_col_user . " ";
      echo "<input type='text' name='username' maxlength='40' value='admin' size='25' class='post' />";
      echo "<br/>&nbsp;" . $l_admin_users_col_password . " : <I>www.theuds.com</I><BR/>";
		echo "</TD>";
		echo "</TR>";
    echo "<TR>";
    echo "<TD colspan='4' ALIGN='CENTER' class='catBottom'>";
      echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_create . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "<INPUT TYPE='hidden' name='action' value = 'create' />";
    echo "</TD>";
    echo "</FORM>";
    echo "</TR>";
  echo "</TABLE>";
}
else
{
  echo "<BR/>";
  echo $l_admin_htaccess_4 . " : ";
  echo "<FORM METHOD='GET' name='formulaire' ACTION ='htaccess_create.php?'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='hidden' name='action' value = 'delete' />";
  echo "</FORM>";
}  
//
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
//echo "<div class='info'>";
echo "<div class='notice'>";
echo $l_admin_htaccess_warning;
echo "</div>";
//
display_menu_footer();
//
echo "</body></html>";
?>
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
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_users);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_users_add_new . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
//
$auth_extern = "";
if (f_nb_auth_extern() == 1) $auth_extern = "X";
//
echo "<font face=verdana size=2>";

echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
echo "<TD align='center' COLSPAN='3' class='catHead'>";
echo "<font face=verdana size=3><b>" . $l_admin_users_add_new . "</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";

if ( (f_if_already_max_nb_users() == '0') and (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == '') )
{
  echo "<FORM METHOD='POST' ACTION='user_add.php?'>";
  echo "<TD class='row2'>";
  echo "<font face=verdana size=2>";
  echo $l_admin_users_order_login . " : <input type='text' name='username' maxlength='20' value='' size='15' class='post' />";
  //echo "<BR/>";
  echo "</TD><TD class='row2'>";
  echo "<font face=verdana size=2>";
  echo $l_admin_users_order_function . " : <input type='text' name='nom' maxlength='40' size='20' class='post' />";
  echo "</TD><TD class='row2'>";
  //echo " ";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
  echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
  echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</TD></TR>";
  echo "</FORM>";
}
else
{
  echo "<TD align='center' COLSPAN='3' class='row1'>";
  echo "<font face=verdana size='2'>";
  if (f_if_already_max_nb_users() != '0')
  {
    echo "<font color='RED'>";
    echo $l_admin_users_cannot_add . "<BR/>";
    echo "</TD></TR>";
    echo "<TR>";
    echo "<TD align='center' COLSPAN='3' class='catBottom'>";
    echo "<font face=verdana size=2>";
    echo $l_admin_users_to_add_more_1;
  }
  else
  {
    echo "<font color='RED'>" . $l_admin_users_no_add_1 . "</FONT> : " . $l_admin_users_no_add_2 . "<BR/>";
    echo "</TD></TR>";
    echo "<TR>";
    echo "<TD align='center' COLSPAN='3' class='catBottom'>";
    echo "<font face=verdana size=2>";
    if ($auth_extern == "")
      echo $l_admin_users_to_add_more_2;
    else
    {
      echo "<font color='red'>";
      echo $l_admin_users_auto_add_user_for_ext_auth;
    }
  }
  echo "</TD></TR>";
}
echo "</TABLE>";
mysqli_close($id_connect);
//
display_menu_footer();
//
echo "</body></html>";
?>
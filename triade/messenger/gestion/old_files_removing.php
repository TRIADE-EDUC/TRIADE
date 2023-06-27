<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2012 THeUDS           **
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
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_old_files_to_delete . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="10;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face='verdana' size='2'>";
echo "<BR/>";
//
#require ("../common/functions_admin.inc.php");
$repert = "../distant/";
$trouv_old = "";
$old_distant_files = f_old_distant_files();
foreach ($old_distant_files as $name) 
{
  if (is_readable($repert . $name)) $trouv_old = "X";
}

//if ( (is_readable($repert . "start.php")) or (is_readable($repert . "sql_test.php")) or (is_readable($repert . "get_options_2.php")) )
if ($trouv_old != "")
{
  echo "<FONT COLOR='red'><B>" . $l_old_files_to_delete . "</B></font><BR/>";
  echo "<BR/>";
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR>";
    echo "<TH align='center' class='thHead'>";
    echo "<font face='verdana' size='3'><b>" . $l_menu_list . "</b></font></TH>";
    echo "<TH align='center' class='thHead'>";
    echo "<font face='verdana' size='3'><b>" . $l_admin_users_col_action . "</b></font></TH>";
  echo "</TR>";
  //
  $cannot_remove = "";
  foreach ($old_distant_files as $name) 
  {
    if (   (is_readable($repert . $name)) and ( ($cannot_remove == "") or ( ($name != "start.php") and ($name != "sql_test.php") and ($name != "get_options_2.php") ) )  )
    {
      echo "<TR>";
      echo "<TD class='row1'><font face='verdana' size='2'>&nbsp;/distant/" . $name . "&nbsp;</TD>";
      if (is_writeable($repert . $name))
      {
        echo "<TD valign='bottom' align='center' class='row1'>";
        echo " <A HREF='old_files_remove.php?f=" . $name . "&lang=" . $lang . "&' title='" . $l_admin_bt_delete . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
      }
      else
      {
        echo "<TD align='left' class='row1'>";
        echo "<font color='red'>&nbsp;" . $l_admin_check_not_writeable . " !&nbsp;</font>";
        $cannot_remove = "X";
      }
      echo "</TD>";
      echo "</TR>";
    }
  }
  echo "</TABLE>";
  //
  if ($cannot_remove == "")
  {
    echo "<BR/>";
    echo "<FORM METHOD='GET' name='formulaire' ACTION ='old_files_remove.php?'>";
    echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . "' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "<INPUT TYPE='hidden' name='f' value = 'ALL' />";
    echo "</FORM>";
  }
}
echo "</CENTER>";
//
display_menu_footer();
//
echo "</body></html>";
?>
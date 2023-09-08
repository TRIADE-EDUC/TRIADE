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
define('INTRAMESSENGER',true);
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_acp_pass_changing . "</title>";
display_header();
echo "</head>";
echo "<body>";
//
display_menu();
//
if (_ACP_PROTECT_BY_HTACCESS == "")
{

    echo "<br/>";
    echo "<form name='form1' method='post' action='acp_pass_update.php'>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<THEAD>";
      echo "<TR>";
        echo "<TH align='center' COLSPAN='2' class='thHead'>";
        echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_acp_pass_changing . "&nbsp;</B></font></TH>";
      echo "</TR>";
      /*
      echo "<TR>";
        echo "<TD align='center' COLSPAN='2' class='row3'>";
          echo "<font face='verdana' size='2'>";
          echo $l_menu_acp_auth;
        echo "</TD>";
      echo "</TR>";
      */
    echo "</THEAD>";
    echo "<TBODY>";
      echo "<TR>";
        echo "<TD class='row1'>&nbsp;";
          echo "<font face='verdana' size='2'>";
          echo $l_admin_acp_pass_1 . "&nbsp;";
        echo "</TD>";
        echo "<TD align='center' class='row1'>";
          echo "<input name='acp_pass_1' type='password' id='acp_pass_1' />";
        echo "</TD>";
      echo "</TR>";
      echo "</TR>";
        echo "<TD COLSPAN='2' class='row3'>";
        echo "</TD>";
      echo "</TR>";
      echo "<TR>";
        echo "<TD class='row1'>&nbsp;";
          echo "<font face='verdana' size='2'>";
          echo $l_admin_acp_pass_2 . "&nbsp;";
        echo "</TD>";
        echo "<TD align='center' class='row1'>";
          echo " <input name='acp_pass_2' type='password' id='acp_pass_2' />";
        echo "</TD>";
      echo "</TR>";
      echo "<TR>";
        echo "<TD class='row1'>&nbsp;";
          echo "<font face='verdana' size='2'>";
          echo $l_admin_acp_pass_3 . "&nbsp;";
        echo "</TD>";
        echo "<TD align='center' class='row1'>";
          echo " <input name='acp_pass_3' type='password' id='acp_pass_3' />";
        echo "</TD>";
      echo "</TR>";
      echo "<TR>";
        echo "<TD align='center' COLSPAN='2' class='catBottom'>";
          echo "<input type='submit' name='Submit' value='" . $l_admin_bt_update . "' class='mainoption' /><br/>";
        echo "</TD>";
      echo "</TR>";

    echo "</TBODY>";
    echo "</TABLE>";
    echo "</form>";
    echo "<br/>";
    //
    //
    echo "<form name='form2' method='post' action='acp_deconnect.php'>";
    echo "<input type='submit' name='Submit' value='" . $l_menu_logout . "' class='liteoption' /><br/>";
    echo "</form><br/>";
}
else
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_acp_admin_cannot;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
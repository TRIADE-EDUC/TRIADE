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
if (isset($_REQUEST['id_admin'])) $id_admin = intval($_REQUEST['id_admin']);  else $id_admin = 0;
//if (isset($_GET['adm_username'])) $adm_username = $_GET['adm_username'];  else $adm_username = "";
//if (isset($_GET['adm_level'])) $adm_level = $_GET['adm_level'];  else $adm_level = "";
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
if ( _ACP_PROTECT_BY_HTACCESS != '')
{
  echo "<div class='warning'>";
  echo $l_admin_acp_admin_warning_1;
  echo "<br/>";
  echo $l_admin_acp_admin_warning_2;
  echo "</div>";
}
//
require ("../common/sql.inc.php");
$requete  = " select ADM_USERNAME, ADM_LEVEL ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
$requete .= " where ID_ADMIN = " . $id_admin;
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-P1g]", $requete);
if ( mysqli_num_rows($result) == 1 )
{
  list ($adm_username, $adm_level) = mysqli_fetch_row ($result);
}
//
echo "<font face=verdana size=2>";
###########################################if ( _ACP_PROTECT_BY_HTACCESS == '' )
if ( $adm_username <> '' )
{
  echo "<BR/>";
  echo "<form name='form1' method='GET' action='admin_acp_rights_update.php'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
    echo "<TR>";
      echo "<TH align='center' COLSPAN='3' class='thHead'>";
      echo "<font face=verdana size=3><b>" . $l_admin_users_admin . " : " .  $adm_username  . "</B></font></TH>";
    echo "</TR>";
    echo "<TR>";
    echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $l_admin_acp_admin_rights . "</b></font> </TD>\n";
    display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
  echo "</THEAD>";
  echo "<TBODY>";
    for($i = 0; $i < 18; $i++) 
    {
      $level = pow(2, $i);
      $status = "off";
      if ($adm_level & $level) $status = "on";
      echo "<TR>";
        echo "<TD align='center' class='row1'>";
          if (_ROLES_TO_OVERRIDE_PERMISSIONS == "") 
            $not_ok = "KO"; 
          else
            $not_ok = "ko"; 
          //
          if ( ($level == 16) and ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '') or (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') ) ) $status = $not_ok;
          if ( ($level == 32) and (_ALLOW_CHANGE_AVATAR == "") ) $status = $not_ok;
          if ( ($level == 64) and (_SPECIAL_MODE_GROUP_COMMUNITY == '' ) and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') and (_GROUP_FOR_SBX_AND_ADMIN_MSG == '') ) $status = $not_ok;
          if ( ($level == 128) and (_ROLES_TO_OVERRIDE_PERMISSIONS == "") ) $status = $not_ok;
          if ( ($level == 256) and (_SHOUTBOX == "") ) $status = $not_ok;
          if ( ($level == 512) and (_SHARE_FILES == "") ) $status = $not_ok;
          if ( ($level == 1024) and (_BOOKMARKS == "") ) $status = $not_ok;
          if ( ($level == 4096) and (_SERVERS_STATUS == "") ) $status = $not_ok;
          //if ( ($level == 8192) and (_XXX == "") ) $status = $not_ok;
          if ( ($level == 16384) and (_ENTERPRISE_SERVER == "") ) $status = $not_ok;
          if ( ($level == 32768) and (strlen(_ADMIN_EMAIL) <= 5) ) $status = $not_ok;
          //
          //if ($adm_level & $level)
          if ($status == "on")  echo "<IMG SRC='" . _FOLDER_IMAGES . "state_on.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_right_on . "' TITLE='" . $l_admin_acp_admin_right_on . "'>";
          if ($status == "off") echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away2.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_right_off . "' TITLE='" . $l_admin_acp_admin_right_off . "'>";
          if ($status == "ko")  echo "<IMG SRC='" . _FOLDER_IMAGES . "menu_roles.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_right_see_role . "' TITLE='" . $l_admin_acp_admin_right_see_role . "'>";
          if ($status == "KO")  echo "<IMG SRC='" . _FOLDER_IMAGES . "forbidden.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_right_no_option . "' TITLE='" . $l_admin_acp_admin_right_no_option . "'>";
        echo "</TD>";
        echo "<TD align='left' class='row1'>&nbsp;";
          echo $l_admin_acp_admin_right[$level];
        echo "</TD>";
        echo "<TD align='center' class='row1'>";
          echo "<input name='adm_level_" . $level . "' VALUE='1' TYPE='CHECKBOX' class='genmed' ";
          if ($adm_level & $level) echo "CHECKED";
          echo " />";
        echo "</TD>";
      echo "</TR>";
    }

    echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='catBottom'>";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='hidden' name='id_admin' value = '" . $id_admin . "' />";
        echo "<input type='submit' name='Submit' value='" . $l_admin_bt_update . "' class='mainoption' /><br/>";
      echo "</TD>";
    echo "</TR>";
  echo "</TBODY>";
  echo "</TABLE>";
  echo "</form>";

  echo "</BR>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'><B>" . $l_legende . "</B></TD></TR>";

  echo "<TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . "state_on.png' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_acp_admin_right_on . "&nbsp;";
  echo "</TD>";
  
  echo "</TR><TR>";
  echo "<TD ALIGN='CENTER' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away2.png' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_acp_admin_right_off . "&nbsp;";
  echo "</TD>";

  echo "</TR><TR>";
  echo "<TD ALIGN='CENTER' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . "menu_roles.png' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_acp_admin_right_see_role . "&nbsp;";
  echo "</TD>";

  echo "</TR><TR>";
  echo "<TD ALIGN='CENTER' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . "forbidden.png' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_acp_admin_right_no_option . "&nbsp;";
  echo "</TD>";
  echo "</TR>";
  //
  echo "</TABLE>";


}
/*
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
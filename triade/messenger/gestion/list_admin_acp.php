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
if (isset($_COOKIE['im_nb_row_by_page'])) $nb_row_by_page = $_COOKIE['im_nb_row_by_page'];  else  $nb_row_by_page = '15';
//
if (isset($_GET['only_pending'])) $only_pending = $_GET['only_pending'];  else  $only_pending = "";
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
if ( _ACP_PROTECT_BY_HTACCESS != '')
{
  echo "<div class='warning'>";
  echo $l_admin_acp_admin_warning_1;
  echo "<br/>";
  echo $l_admin_acp_admin_warning_2;
  echo "</div>";
}
//if ( _ACP_PROTECT_BY_HTACCESS == '')
$acp_login = f_acp_login();
//
require ("../common/sql.inc.php");
//
$requete  = " SELECT ID_ADMIN, ADM_USERNAME, ADM_LEVEL, ADM_DATE_CREAT, ADM_DATE_LAST, ADM_DATE_PASSWORD ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
$requete .= " ORDER BY UPPER(ADM_USERNAME) ";
//
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-P1a]", $requete);
//
echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<THEAD>";
echo "<TR>";
  echo "<TH align='center' COLSPAN='6' class='thHead'>";
  echo "<font face=verdana size=3><b>" . $l_admin_acp_admin_list . " </B></font></TH>";
echo "</TR>";
//
$nb_lig = mysqli_num_rows($result);
if ($nb_lig > 0)
{
  echo "<TR>";
    display_row_table("&nbsp;" . $l_admin_acp_auth_username . "&nbsp;", '');
    display_row_table("&nbsp;" . $l_admin_users_col_level . "&nbsp;", '');  //l_admin_users_col_etat
    display_row_table("&nbsp;" . $l_admin_users_col_creat . "&nbsp;", '');
    display_row_table("&nbsp;" . $l_admin_users_col_last . "&nbsp;", '');
    display_row_table("&nbsp;" . $l_admin_users_col_password . "&nbsp;", '');
    //display_row_table($l_admin_users_col_action, '');
    echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $l_admin_users_col_action . "</b></font> </TD>\n";
  echo "</TR>";
  echo "</THEAD>";
  echo "<TFOOT>";
  // Dernière ligne : trier.
  echo "<TR>";
    echo "<TD align='center' COLSPAN='6' class='catBottom'>";
      echo "<font face=verdana size=2>";
      echo "<A HREF='admin_acp_adding.php?lang=" . $lang . "&'>" . $l_admin_acp_admin_create . "</A>";
    echo "</TD>";
  echo "</TR>";
  echo "</TFOOT>";
  echo "<TBODY>";
  //
  $last_first_letter_group = "";
  $last_first_letter_user = "";
  $last_user = "";
  $last_group = "";
  while( list ($id_admin, $adm_username, $adm_level, $adm_date_creat, $adm_date_last, $adm_date_pass) = mysqli_fetch_row ($result) )
  {
    $adm_date_creat = date($l_date_format_display, strtotime($adm_date_creat));
    $adm_date_pass = date($l_date_format_display, strtotime($adm_date_pass));
    if ($adm_date_last == '0000-00-00')
      $adm_date_last = 	'&nbsp;';
    else
      $adm_date_last = date($l_date_format_display, strtotime($adm_date_last));
    //
    //
    echo "<TR>";
    //
    //
    // --------------------------------------------------------------------
    //
    //
    // Col name
    //
    //echo "<FORM METHOD='POST' ACTION='XXXXX_update_name.php?'>";
    echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
    //echo "<input type='text' name='adm_username' maxlength='20' value='" . $adm_username . "' size='30' class='post' />";
      echo "<font face='verdana' size='2'>";
      echo "&nbsp;" . $adm_username;
    //echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
    //echo "<input type='hidden' name='id_srv' value = '" . $id_srv . "' />";
    //echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "</TD>";
    //echo "</FORM>";
    //
    //
    //
    echo "<TD align='center' class='row1'>";
      //echo $adm_level;
      $level = substr_count(decbin($adm_level), "1");
      if ($acp_login != $adm_username) echo "<A HREF='admin_acp_rights.php?id_admin=" . $id_admin . "&lang=" . $lang . "&' class='cattitle'>";
      if ($level < 4) echo "<IMG SRC='" . _FOLDER_IMAGES . "z1.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_rights . "' TITLE='" . $l_admin_acp_admin_rights . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      if ( ($level >= 4) and ($level < 7) )  echo "<IMG SRC='" . _FOLDER_IMAGES . "z2.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_rights . "' TITLE='" . $l_admin_acp_admin_rights . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      if ( ($level >= 7) and ($level < 11) )  echo "<IMG SRC='" . _FOLDER_IMAGES . "z3.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_rights . "' TITLE='" . $l_admin_acp_admin_rights . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      if ( ($level >= 11) and ($level < 15) )  echo "<IMG SRC='" . _FOLDER_IMAGES . "z4.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_rights . "' TITLE='" . $l_admin_acp_admin_rights . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      if ($level >= 15) echo "<IMG SRC='" . _FOLDER_IMAGES . "z5.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_rights . "' TITLE='" . $l_admin_acp_admin_rights . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      echo "</A>";
    echo "</TD>";
    //
    echo "<TD align='center' class='row2'>";
      echo "<font face='verdana' size='2'>";
      echo $adm_date_creat;
    echo "</TD>";
    //
    echo "<TD align='center' class='row2'>";
      echo "<font face='verdana' size='2'>";
      echo $adm_date_last;
    echo "</TD>";
    //
    echo "<TD align='center' class='row2'>";
      echo "<font face='verdana' size='2'>";
      echo $adm_date_pass;
    echo "</TD>";
    //
    //
    // Col action
    //
    if ($acp_login != $adm_username)
    {
      echo "<TD valign='bottom' align='left' class='row1'>&nbsp;";
      echo "<A HREF='admin_acp_rights.php?id_admin=" . $id_admin . "&lang=" . $lang . "&' class='cattitle'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "menu_sondages.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_acp_admin_rights . "' TITLE='" . $l_admin_acp_admin_rights . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      //
      if ($nb_lig > 1)
      {
        echo "<A HREF='admin_acp_deleting.php?id_admin=" . $id_admin . "&adm_username=" . $adm_username . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      }
    }
    else
      echo "<TD class='row2'>&nbsp;";
    //
    echo "</TD>";
    //
    echo "</TR>";
    echo "\n";
  }
  echo "</TBODY>";
  //
  echo "</TABLE>";
}
else
{
  echo "<TR>";
  echo "<TD colspan='6' ALIGN='CENTER' class='row2'>";
    echo "<font face='verdana' size='2'>" . $l_admin_acp_admin_list_empty;
  echo "</TD>";
  echo "</TR>";
  echo "<TR>";

  echo "<FORM METHOD='POST' ACTION='admin_acp_adding.php?'>";
  echo "<TD valign='bottom' align='center' class='row2'>";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_acp_admin_create . "' class='liteoption' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</TD>";
  echo "</FORM>";

  echo "</TR>";
  echo "</TABLE>";
}
//
mysqli_close($id_connect);
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
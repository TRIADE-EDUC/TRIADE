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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['id_role'])) $id_role = intval($_GET['id_role']);  else  $id_role = "";
//
if ($id_role <= 0) header("location:list_roles.php?lang=" . $lang);
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_roles);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_roles_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
echo "<BR/>";
if ( _ROLES_TO_OVERRIDE_PERMISSIONS != '' )
{
  //
  require ("../common/sql.inc.php");
  //
  //
  echo "<font face=verdana size=2>";
  //
  $requete  = " select ROL_NAME, ROL_DEFAULT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
  $requete .= " WHERE ID_ROLE = " . $id_role;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G6d]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    list ($role, $default) = mysqli_fetch_row ($result);
  }
  
  $requete  = " SELECT USR.USR_USERNAME, USR.USR_NICKNAME, USR.USR_NAME, USR.ID_USER, USR.USR_COUNTRY_CODE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "ROL_ROLE ROL ";
  $requete .= " WHERE USR.ID_ROLE = ROL.ID_ROLE ";
  if (intval($id_role) > 0) $requete .= " AND USR.ID_ROLE = " . $id_role;
  $requete .= " ORDER BY UPPER(ROL_NAME), UPPER(USR_USERNAME) ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A4c]", $requete);
  //
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='6' class='thHead'>";
    echo "&nbsp;<font face=verdana size=3><b>" . $l_admin_roles_members . " </B>";
    echo $role . "</font>&nbsp;</TH>";
  echo "</TR>";
  //
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
      $link_user_col = "&nbsp;<A HREF='list_role_members.php?&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>&nbsp;";
      display_row_table($link_user_col, '');
      echo "<TD align='center' width='' COLSPAN='1' class='catHead'><font face=verdana size=2>&nbsp;<b>" . $l_admin_contact_col_action . "</b>&nbsp;</font></TD>";
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
      // Dernière ligne : 
      echo "<TR>";
        echo "<TD align='center' COLSPAN='6' class='catBottom'>";
          echo "<font face=verdana size=2>";
          //echo "<A HREF='list_roles.php?lang=" . $lang . "&'>" . $l_admin_roles_title . "</A>";
          echo "<A HREF='role_permissions.php?lang=" . $lang . "&id_role=" . $id_role . "&'>" . $l_admin_roles_permissions . "</A></font>";
        echo "</TD>";
      echo "</TR>";

    echo "</TFOOT>";
    echo "<TBODY>";
    //
    while( list ($user, $nickname, $fonction, $id_user, $country_code) = mysqli_fetch_row ($result) )
    {
      //
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $user = $nickname;
      echo "<TR>";
      //
      //
      // --------------------------------------------------------------------
      //
      // Col username
      //
      echo "<TD align='left' class='row1'>";
      /*
      if ($display_flag_country != "")
      {
        if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
        {
          $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
          $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
          echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>";
        }
      }
      */
      echo "<font face='verdana' size='2'>&nbsp;";
      //echo $user . "&nbsp;</font>";
      echo "<A HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
      echo $user . "</A>";

      echo "</TD>";
      //
      //
      // --------------------------------------------------------------------
      //
      // Col function
      //
      /*
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $fonction;
      echo "&nbsp;</TD>";
      */
      //
      //
      // Col action
      echo "<FORM METHOD='POST' ACTION='user_update_role.php?'>";
      echo "<TD valign='bottom' align='center' class='row1'>";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_drop.png' VALUE = '" . $l_admin_bt_delete . "' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' />";
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<input type='hidden' name='id_role' value = '0' />";
        echo "<input type='hidden' name='id_role_from' value = '" . $id_role . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
      echo "</TD>";
      echo "</FORM>";
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
    //echo "<FORM METHOD='POST' ACTION='role_adding.php?'>";
    echo "<TD colspan='6' ALIGN='CENTER' class='row1'>";
      echo "<font face='verdana' size='2'>&nbsp;" . $l_admin_role_no_member . "&nbsp;";
      //echo "<BR/>";
      //echo "<BR/>";
      //echo "<INPUT TYPE='submit' VALUE = '" . $l_menu_group_add_member . "' class='liteoption' />";
      //echo "<INPUT TYPE='hidden' name='id_role' value = '" . $id_role . "'/>";
      //echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    //echo "</FORM>";
    echo "</TR>";
    //
    echo "</TABLE>";
  }
	//
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_roles_cannot_use;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
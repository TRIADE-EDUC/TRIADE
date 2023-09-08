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
echo $l_admin_roles_info . "<BR/>";
echo "<BR/>";
if (_ROLES_TO_OVERRIDE_PERMISSIONS != '')
{
  require ("../common/sql.inc.php");
  //
  require ("../common/roles.inc.php");
  //
  // Si vide, on rempli :
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G5a1]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($nb_roles) = mysqli_fetch_row ($result);
    if ($nb_roles <= 0)
    {
      //$requete = "TRUNCATE TABLE " . $PREFIX_IM_TABLE . "ROL_ROLE ";
      $requete = "DELETE FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G5a2]", $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "ROL_ROLE (ROL_NAME, ROL_DEFAULT) ";
      $requete .= " VALUES ('Others' , 'D'), ('Guest' , ''), ('Manager' , ''), ('Admin' , ''); ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G5a3]", $requete);
      //
      fill_table_module();
    }
  }
  //
  //
  $requete  = " SELECT ROL.ID_ROLE, ROL.ROL_NAME, ROL_DEFAULT, count(USR.ID_ROLE)";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ROL ";
  $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "USR_USER USR ON USR.ID_ROLE = ROL.ID_ROLE ";
  $requete .= " WHERE ROL_DEFAULT <> 'D' ";
  $requete .= " GROUP BY ID_ROLE ";
  $requete .= " ORDER BY UPPER(ROL_NAME) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G5c]", $requete);
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='5' class='thHead'>";
    echo "<font face=verdana size=3><b>" . $l_admin_roles_title . " </B></font></TH>";
  echo "</TR>";
  //
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
      display_row_table($l_admin_role, '');
      display_row_table("&nbsp;" . $l_admin_group_members . "&nbsp;", '');
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    // Dernière ligne :
    /*
    echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='row3'>";
        echo "<font face=verdana size=2>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_default . "' TITLE='" . $l_admin_roles_default . "' border='0'> ";
          echo $l_admin_roles_default;
      echo "</TD>";
    echo "</TR>";
    */
    /*
    echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='catBottom'>";
        echo "<font face=verdana size=2>";
        echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_default . "' TITLE='" . $l_admin_roles_default . "' border='0'> ";
        echo $l_admin_roles_default . "&nbsp;";
        //echo "<A HREF='role_adding.php?lang=" . $lang . "&'>" . $l_admin_roles_creat_role . "</A>";
      echo "</TD>";
    echo "</TR>";
    */
    echo "</TFOOT>";
    echo "<TBODY>";
    //
    $nbre_total = 0;
    while( list ($id_role, $role, $role_default, $nbre) = mysqli_fetch_row ($result) )
    {
      $nbre_total = ($nbre_total + $nbre);
      echo "<TR>";
      //
      //
      // --------------------------------------------------------------------
      //
      //
      // COl role
      //
      echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        //echo "<A HREF='list_role_members.php?id_role=" . $id_role . "&lang=" . $lang . "&' alt='" . $l_admin_group_members . "' title='" . $l_admin_group_members . "' class='cattitle'>";
        //echo "<A HREF='role_renaming.php?id_role=" . $id_role . "&lang=" . $lang . "&' alt='" . $l_admin_roles_rename_role . "' title='" . $l_admin_roles_rename_role . "' class='cattitle'>";
        if ($role_default == "D") echo "<i>";
        echo "<A HREF='role_permissions.php?id_role=" . $id_role . "&lang=" . $lang . "&' alt='" . $l_admin_roles_permissions . "' title='" . $l_admin_roles_permissions . "' class='cattitle'>";
        echo $role . "</A>&nbsp;";
        if ($role_default == "D") echo "</i><IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_default . "' TITLE='" . $l_admin_roles_default . "' border='0'>&nbsp;";
      echo "</TD>";
      //
      // col nombre
      //
      if ($role_default != "D")
      {
        echo "<TD align='right' class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo "<A HREF='list_role_members.php?id_role=" . $id_role . "&lang=" . $lang . "&' alt='" . $l_admin_group_members . "' title='" . $l_admin_group_members . "' class='cattitle'>";
        echo $nbre;
        echo "</A>&nbsp;</TD>";
      }
      else
        echo "<TD class='row2'>&nbsp;</TD>";
      //
      // Col action
      //
      echo "<TD valign='bottom' align='middle' class='row1'>&nbsp;";
        //echo "<A HREF='role_adding_user.php?id_role=" . $id_role . "&lang=" . $lang . "&'>";
        //echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ajout.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
        echo "<A HREF='role_renaming.php?id_role=" . $id_role . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "rename.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_rename_role . "' TITLE='" . $l_admin_roles_rename_role . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
        echo "<A HREF='role_permissions.php?id_role=" . $id_role . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "menu_options.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_permissions . "' TITLE='" . $l_admin_roles_permissions . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
      echo "</TD>";
      //
      echo "</TR>";
      echo "\n";
    }
    //
    /////////////////////////////////////////////////-----------------------------
    //
    echo "<TR>";
      //echo "<TD align='center' COLSPAN='3' class='row3'>";
      echo "<TD align='center' COLSPAN='3' class='catBottom'>";
        echo "<font face=verdana size=2>";
        echo "<A HREF='role_adding.php?lang=" . $lang . "'>" . $l_admin_roles_creat_role . "</A>";
      echo "</TD>";
    echo "</TR>";
    echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='row3'>&nbsp;";
      echo "</TD>";
    echo "</TR>";
    //
    echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='catHead'>";
        echo "<font face=verdana size=2>&nbsp;<b>";
        echo $l_admin_roles_default . "</b>&nbsp; {*}";
      echo "</TD>";
    echo "</TR>";
    $requete  = " SELECT ROL.ID_ROLE, ROL.ROL_NAME, ROL_DEFAULT ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ROL ";
    $requete .= " WHERE ROL_DEFAULT = 'D' ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G5d]", $requete);
    while( list ($id_role, $role) = mysqli_fetch_row ($result) )
    {
      echo "<TR>";
      //
      //
      // --------------------------------------------------------------------
      //
      //
      // COl role
      //
      echo "<TD class='row1' colspan='2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo "<i>";
        echo "<A HREF='role_permissions.php?id_role=" . $id_role . "&lang=" . $lang . "&' alt='" . $l_admin_roles_permissions . "' title='" . $l_admin_roles_permissions . "' class='cattitle'>";
        echo $role . "</A>&nbsp;";
      echo "</TD>";
      //
      echo "<TD valign='bottom' align='middle' class='row1'>&nbsp;";
        echo "<A HREF='role_renaming.php?id_role=" . $id_role . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "rename.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_rename_role . "' TITLE='" . $l_admin_roles_rename_role . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
        echo "<A HREF='role_permissions.php?id_role=" . $id_role . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "menu_options.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_permissions . "' TITLE='" . $l_admin_roles_permissions . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
      echo "</TD>";
      //
      echo "</TR>";
      echo "\n";
    
    }
    //
    //
    /////////////////////////////////////////////////-----------------------------
    //
    echo "</TBODY>";
    //
    echo "</TABLE>";
    
    echo "<br/>";
    echo "{*} " . $l_admin_roles_default_explain;
    //
    /*
    echo "<br/>";
    echo "<FORM METHOD='POST' ACTION='role_adding.php?'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_roles_creat_role . "' class='liteoption' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    */
    if ($nbre_total <= 0) fill_table_module();  //echo "test";
  }
  else
  {
    echo "<TR>";
    echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_roles_list_empty;
    echo "</TD>";
    echo "</TR>";
    echo "<TR>";

    echo "<FORM METHOD='POST' ACTION='role_adding.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_roles_creat_role . "' class='liteoption' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";

    echo "</TR>";
    echo "</TABLE>";
  }
	//
  mysqli_close($id_connect);
  //
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_roles_cannot_use . "<BR/>";
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
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
if (isset($_REQUEST['id_group'])) $id_group = intval($_REQUEST['id_group']); else $id_group = 0;
if (isset($_REQUEST['tri'])) $tri = $_REQUEST['tri'];  else  $tri = "";
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang'];  else  $lang = "";
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
echo "<title>[IM] " . $l_admin_group_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
echo "<BR/>";
//if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
{
  if ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '')
  {
    echo "<B>" . $l_admin_options_group_for_sbx_and_admin_messages . "</B><BR/>";
    echo "(" . $l_admin_options_group_for_admin_messages_2 . ")<BR/>";
    echo "<BR/>";
  }
  //
  require ("../common/sql.inc.php");
  //
  //
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
	echo "<TR>";
	echo "<TH align='center' COLSPAN='3' class='thHead'>";
	echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_group_title_2 . "&nbsp;</b></font>";
	echo "</TH>";
	echo "</TR>";
	//
  $requete = " select GRP_NAME, ID_GROUP ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP ";
  $requete .= " ORDER BY GRP_NAME ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A4f]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
    echo "<TD align='center' COLSPAN='3' class='catHead'>";
    echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_group_title_add_to_group . "&nbsp;</b></font>";  // -------------- Add user to group ----------------
    echo "</TD>";
    echo "</TR>";
    echo "<TR>";
    //
    echo "<FORM METHOD='POST' ACTION='group_add_user.php?'>";
    echo "<TD class='row2'>";
    echo "<font face=verdana size=2>&nbsp;";
    echo $l_admin_users_col_user . " : ";
    //
    echo " <select name='id_user'>";
    $requete  = " SELECT USR_USERNAME, USR_NICKNAME, USR_NAME, ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    //$requete .= " WHERE (USR_CHECK <> 'WAIT' or USR_STATUS = 1) ";
    $requete .= " WHERE USR_STATUS = 1 ";
    $requete .= " ORDER BY USR_USERNAME, USR_NAME ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A4d]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      while( list ($username, $nickname, $nom, $id_user) = mysqli_fetch_row ($result) )
      {
        if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
        echo "<option value='" . $id_user . "' class='genmed'>" . $username;
        if ( ($nom != '') and ($nom != 'HIDDEN') )
          echo " &nbsp; [" . $nom . "]";
        //
        echo "</option>";
      }
    }
    echo "</select>";
    //
    //echo "<BR/>";
    echo "</TD><TD class='row2'>";
    echo "<font face=verdana size=2>&nbsp;";
    echo $l_admin_group_add_to_group . " : ";
    //
    echo " <select name='id_gp'>";
    $requete  = " SELECT GRP_NAME, ID_GROUP ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP ";
    $requete .= " ORDER BY GRP_NAME ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A4e]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      while( list ($group, $id_grp) = mysqli_fetch_row ($result) )
      {
        //echo "<option value='" . $id_grp . "' class='genmed'>" . $group . "</option>";
        echo "<option value='" . $id_grp . "' class='genmed'";
        if ($id_group == $id_grp) echo " SELECTED";
        echo ">" . $group . "</option>";
      }
    }
    echo "</select>";
    //
    echo "</TD><TD class='row2' align='right'>";
    //echo " ";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
    echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD></TR>";
    echo "</FORM>";
    
    //
    echo "<TR>";
    echo "<TD align='center' COLSPAN='3' class='row2'> &nbsp;</TD>";
    echo "</TR>";
    echo "<TR>";
  }
  else
  {
    echo "<TR>";
    echo "<TD colspan='8' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_group_no_group;
    echo "</TD>";
    echo "</TR>";
    echo "<TR>";

    echo "<FORM METHOD='POST' ACTION='group_adding.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_create . "' class='liteoption' />";
      //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";
  }
	//

	echo "</TABLE>";	//
  //
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo $l_admin_group_cannot_use_1 . "<BR/>";
  echo "<BR/>";
  echo $l_admin_group_cannot_use_2 . "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<I>_GROUP_FOR_SBX_AND_ADMIN_MSG</I> : " . $l_admin_options_group_for_sbx_and_admin_messages;
  echo "<BR/>";
  echo $l_admin_options_group_for_admin_messages_2;
}
//
display_menu_footer();
//
echo "</body></html>";
?>
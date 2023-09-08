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
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['id_group'])) $id_group = $_GET['id_group'];  else  $id_group = "";
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
echo '<META http-equiv="refresh" content="120;url="> ';
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
  $display_flag_country = "";
  if (_FLAG_COUNTRY_FROM_IP != "")
  {
    if (is_readable("../common/library/geoip/geoip_2.inc.php"))
    {
      require("../common/library/geoip/geoip_2.inc.php");
      $display_flag_country = "X";
    }
  }
  //
  //  | A | B | C |...
  $alpha_link = "";
  if ($tri == "username")
  {
    $requete  = " SELECT distinct(LEFT(UPPER(USR.USR_USERNAME), 1)) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE USG.ID_USER = USR.ID_USER ";
    if (intval($id_group) > 0) $requete .= " AND USG.ID_GROUP = " . $id_group;
    $requete .= " order by LEFT(UPPER(USR.USR_USERNAME), 1) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A4a]", $requete);
    if ( mysqli_num_rows($result) > 3 )
    {
      while( list ($first) = mysqli_fetch_row ($result) )
      {
        $alpha_link .= " | <A HREF=#" . $first . ">" . $first . "</A>";
      }
      $alpha_link .= " | ";
      //
      if (intval($nb_row_by_page) < 30) $alpha_link = "";
    }
  }
  else
  {
    $requete  = " SELECT distinct(LEFT(UPPER(GRP_NAME), 1)) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE GRP.ID_GROUP = USG.ID_GROUP ";
    if (intval($id_group) > 0) $requete .= " AND GRP.ID_GROUP = " . $id_group;
    $requete .= " order by LEFT(UPPER(GRP_NAME), 1) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A4b]", $requete);
    if ( mysqli_num_rows($result) > 2 )
    {
      echo "<font face=verdana size=2>";
      while( list ($first) = mysqli_fetch_row ($result) )
      {
        $alpha_link .= " | <A HREF=#" . $first . ">" . $first . "</A>";
      }
      $alpha_link .= " | ";
      //
      if (intval($nb_row_by_page) < 30) $alpha_link = "";
    }
  }
  echo "<font face=verdana size=2>";
  // echo $alpha_link; // non, plus bas !
  //
  $requete  = " SELECT GRP.GRP_NAME, USR.USR_USERNAME, USR.USR_NICKNAME, USR.USR_NAME, USG.ID_USER, GRP.ID_GROUP, GRP.GRP_PRIVATE, USR.USR_COUNTRY_CODE, USG.USG_PENDING ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USG_USERGRP USG, " . $PREFIX_IM_TABLE . "GRP_GROUP GRP ";
  $requete .= " WHERE USG.ID_USER = USR.ID_USER ";
  $requete .= " and USG.ID_GROUP = GRP.ID_GROUP ";
  if (intval($id_group) > 0) $requete .= " AND GRP.ID_GROUP = " . $id_group;
  if ($tri == "username")
    $requete .= " ORDER BY UPPER(USR_USERNAME), UPPER(GRP_NAME) ";
  else
    $requete .= " ORDER BY UPPER(GRP_NAME), UPPER(USR_USERNAME) ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A4c]", $requete);
  //
  if ( mysqli_num_rows($result) > 15 )
    echo $alpha_link;
  else
    $alpha_link = "";
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='6' class='thHead'>";
    echo "<font face=verdana size=3><b>" . $l_admin_group_title . " </B></font></TH>";
  echo "</TR>";
  //
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
      if ($tri == "") // order by group
      {
        //$link_group_col = "&nbsp;<A HREF='list_group_members.php?&tri=&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_group_order_group . "' class='cattitle' >" . $l_admin_group_col_group . "</A>&nbsp;";
        $link_group_col = "&nbsp;" . $l_admin_group_col_group . "&nbsp;";
        display_row_table($link_group_col, '');
        $link_user_col = "&nbsp;<A HREF='list_group_members.php?&tri=username&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>&nbsp;";
        display_row_table($link_user_col, '');
      }
      else
      {
        //$link_user_col = "<A HREF='list_group_members.php?&tri=username&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>";
        $link_user_col = $l_admin_users_col_user;
        display_row_table($link_user_col, '');
        $link_group_col = "<A HREF='list_group_members.php?&tri=&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_group_order_group . "' class='cattitle' >" . $l_admin_group_col_group . "</A>";
        display_row_table($link_group_col, '');
      }
      display_row_table($l_admin_users_col_function, '250');
      if (_GROUP_USER_CAN_JOIN != "") 
      {
        display_row_table("&nbsp;" . $l_admin_contact_col_state . "&nbsp;", '');
      }
      //display_row_table($l_admin_users_col_action, '');
      echo "<TD align='center' width='' COLSPAN='2' class='catHead'><font face=verdana size=2>&nbsp;<b>" . $l_admin_contact_col_action . "</b>&nbsp;</font></TD>";
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
      // Dernière ligne : trier.
      echo "<TR>";
        echo "<TD align='center' COLSPAN='6' class='catBottom'>";
          echo "<font face=verdana size=2>";
          if (intval($id_group) <= 0)
          {
            echo $l_order_by . " ";
            if ($tri == "username") echo "<B>";
            echo "<A HREF='list_group_members.php?&tri=username&lang=" . $lang . "&'>" . $l_admin_users_order_login . "</A></B> - ";
            if ($tri == "") echo "<B>";
            echo "<A HREF='list_group_members.php?tri=&lang=" . $lang . "&'>" . $l_admin_group_order_group . "</A></B>";
          }
          else
          {
            echo "<A HREF='list_group.php?lang=" . $lang . "&'>" . $l_menu_list_group_list . "</A>";
          }
        echo "</TD>";
      echo "</TR>";

    echo "</TFOOT>";
    echo "<TBODY>";
    //
    $last_first_letter_group = "";
    $last_first_letter_user = "";
    $last_user = "";
    $last_group = "";
    while( list ($group, $user, $nickname, $fonction, $id_user, $id_group, $grp_private, $country_code, $usg_pending) = mysqli_fetch_row ($result) )
    {
      //
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $user = $nickname;
      if ($tri == "") // order by group
      {
        if ( ($group != $last_group) and ($last_group != '') )
        {
          echo "<TR>";
          echo "<TD class='row2' COLSPAN='6'> </TD>";
          echo "</TR>";
        }
        echo "<TR>";
        //
        //
        // --------------------------------------------------------------------
        //
        //
        // COl group
        //
        $aff_group = "";
        $plus = "";
        $t_g = strtoupper(substr($group, 0, 1));
        if ($t_g != $last_first_letter_group)
        {
          $last_first_letter_group = $t_g;
          $plus = " ID=" . $t_g;
        }
        if ($group != $last_group)
        {
          $last_group = $group;
          //$aff_group  = "<A HREF='group_adding_user.php?id_group=" . $id_group . "&lang=" . $lang . "&'>";
          //$aff_group .= "<IMG SRC='" . _FOLDER_IMAGES . "b_ajout.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
          if ($plus != '') 
          {
            //$aff_group .= "<BALISE " . $plus . ">" . $group . "</BALISE></font>";
            $aff_group .= "<A " . $plus . " HREF='group_renaming.php?id_group=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_admin_group_rename_group . "' title='" . $l_admin_group_rename_group . "' class='cattitle'>";
          }
          else
          {
            $aff_group .= "<A HREF='group_renaming.php?id_group=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_admin_group_rename_group . "' title='" . $l_admin_group_rename_group . "' class='cattitle'>";
          }
          $aff_group .= $group . "</A>";
          //
          echo "<TD class='row1'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          echo $aff_group . "&nbsp;";
        }
        else
        {
          echo "<TD class='row2'>&nbsp;";
        }
        echo "</TD>";
        //
        // Col username
        //
        echo "<TD align='left' class='row1'>";
        if ($display_flag_country != "")
        {
          if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>";
          }
        }

        echo "<font face='verdana' size='2'>&nbsp;";
        //echo $user . "&nbsp;</font>";
        if ($usg_pending <> 0) echo "<I>";
        echo "<A HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
        echo $user . "</A>";

        echo "</TD>";
      }
      else // ($tri == "username") 
      {
        if ( ($user != $last_user) and ($last_user != '') )
        {
          echo "<TR>";
          echo "<TD class='row2' COLSPAN='6'> </TD>";
          echo "</TR>";
        }
        echo "<TR>";

        //
        // Col username
        //
        $aff_user = "";
        $plus = "";
        $t_u = strtoupper(substr($user, 0, 1));
        if ($t_u != $last_first_letter_user)
        {
          $last_first_letter_user = $t_u;
          $plus = " ID=" . $t_u;
        }
        if ($plus != '') 
        {
          if ($user != $last_user)
          {
            $last_user = $user;
            //$aff_user .= "<BALISE " . $plus . ">" . $user . "</BALISE></font>";
            $aff_user  = "<A " . $plus . " HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
            $aff_user .= $user . "</A>";
          }
        }
        else
        {
          if ( ($user != $last_user) or ($tri == "") )
          {
            $last_user = $user;
            //$aff_user .=  $user . "</font>";
            $aff_user  = "<A HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
            $aff_user .= $user . "</A>";
          }
        }
        //
        if ($aff_user != "")
        {
          echo "<TD align='left' class='row1'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          echo $aff_user . "&nbsp;";
        }
        else
        {
          echo "<TD align='left' class='row2'>&nbsp;";
          $fonction = "";
        }
        echo "</TD>";
        //
        // COl group
        //
        echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        //echo $group . "&nbsp;</font>";
        echo "<A HREF='group_adding_user.php?id_group=" . $id_group . "&lang=" . $lang . "&'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ajout.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        echo "<A HREF='group_renaming.php?id_group=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_admin_group_rename_group . "' title='" . $l_admin_group_rename_group . "' class='cattitle'>";
        echo $group . "</A>&nbsp;";
      }
      //
      //
      // --------------------------------------------------------------------
      //
      // Col function
      //
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $fonction;
      echo "&nbsp;</TD>";
      //
      // Col state
      if (_GROUP_USER_CAN_JOIN != "") 
      {
        echo "<TD align='center' class='row2'>";
          if ($usg_pending == 0) echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_ok . "' TITLE='" . $l_admin_contact_info_ok. "'>";
          // request add :
          if ($usg_pending == 1) echo "<IMG SRC='" . _FOLDER_IMAGES . "wait.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_wait_valid . "' TITLE='" . $l_admin_users_info_wait_valid . "'>";
          if ($usg_pending == 2) echo "<IMG SRC='" . _FOLDER_IMAGES . "b_disalow.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_refused . "' TITLE='" . $l_admin_contact_info_refused . "'>";
          // request remove :
          if ($usg_pending == -1) echo "<IMG SRC='" . _FOLDER_IMAGES . "waiting.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_wait_valid . "' TITLE='" . $l_admin_contact_info_wait_valid . "'>";
          if ($usg_pending == -2) echo "<IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_vip . "' TITLE='" . $l_admin_contact_info_vip . "'>";
        echo "</TD>";
      }
      //
      // Col action
      echo "<FORM METHOD='POST' ACTION='group_delete_user.php?'>";
      echo "<TD valign='bottom' align='center' class='row1'>";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_drop.png' VALUE = '" . $l_admin_bt_delete . "' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' />";
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<input type='hidden' name='id_gp' value = '" . $id_group . "' />";
        echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
      //
      if ($tri == "") // order by group
      {
        echo "&nbsp;<A HREF='group_adding_user.php?id_group=" . $id_group . "&lang=" . $lang . "&'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ajout.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>"; // ALIGN='BASELINE'
      }
      echo "</TD>";
      echo "</FORM>";

      if (_GROUP_USER_CAN_JOIN != "") 
      {
        if ($usg_pending == 1) 
        {
          echo "<TD valign='bottom' align='center' class='row1'>&nbsp;";
            echo "<A HREF='group_approval_user.php?id_user=" . $id_user . "&tri=" . $tri . "&id_gp=" . $id_group . "&lang=" . $lang . "&action=ok&' title='" . $l_admin_bt_allow . "'>";
            echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png' WIDTH='16' HEIGHT='16' BORDER='0'></A>&nbsp;";
            //
            echo "<A HREF='group_approval_user.php?id_user=" . $id_user . "&tri=" . $tri . "&id_gp=" . $id_group . "&lang=" . $lang . "&action=cannot-join&' title='" . $l_admin_contact_bt_forbid . "'>";
            echo "<IMG SRC='" . _FOLDER_IMAGES . "b_lock.png' WIDTH='16' HEIGHT='16' BORDER='0'></A>&nbsp;";
          echo "</TD>";
        }
        else
        {
          echo "<TD class='row2'>";
          echo "</TD>";
        }
      }
      //
      echo "</TR>";
      echo "\n";
    }
    echo "</TBODY>";
    //
    echo "</TABLE>";
    
    if ( (strlen($alpha_link) > 3)  and (mysqli_num_rows($result) > 20) )
      echo $alpha_link . "<BR/>";
  }
  else
  {
    echo "<TR>";
    echo "<FORM METHOD='POST' ACTION='group_adding_user.php?'>";
    echo "<TD colspan='6' ALIGN='CENTER' class='row1'>";
      echo "<font face='verdana' size='2'>&nbsp;" . $l_admin_group_no_user_group . "&nbsp;";
      echo "<BR/>";
      echo "<BR/>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_menu_group_add_member . "' class='liteoption' />";
      //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<INPUT TYPE='hidden' name='id_group' value = '" . $id_group . "'/>";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";
    echo "</TR>";
    //
    echo "<TR>";
      echo "<TD class='row3'> &nbsp;";
      echo "</TD>";
    echo "</TR>";
    /*
    echo "<TR>";
    echo "<FORM METHOD='POST' ACTION='group_adding_user.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_menu_group_add_member . "' class='liteoption' />";
      //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";
    echo "</TR>";
    */
    echo "<TR>";
    echo "<FORM METHOD='POST' ACTION='group_delete.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
      //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<input type='hidden' name='id_gp' value = '" . $id_group . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";
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
  echo $l_admin_group_cannot_use_1 . "<BR/>";
  echo "<BR/>";
  echo $l_admin_group_cannot_use_2 . "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<I>_GROUP_FOR_SBX_AND_ADMIN_MSG</I> : " . $l_admin_options_group_for_sbx_and_admin_messages;
  echo "<BR/>";
  echo $l_admin_options_group_for_admin_messages_2;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
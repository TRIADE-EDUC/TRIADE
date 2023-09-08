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
  echo "<font face=verdana size=2>";
  // echo $alpha_link; // non, plus bas !
  //
  $requete  = " SELECT GRP.ID_GROUP, GRP.GRP_NAME, GRP.GRP_SHOUTBOX, GRP.GRP_SBX_NEED_APPROVAL, GRP.GRP_PRIVATE, count(USG.ID_GROUP)";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP ";
  $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "USG_USERGRP USG ON USG.ID_GROUP = GRP.ID_GROUP ";
  if ($only_pending != "") $requete .= " WHERE (USG.USG_PENDING = 1 or USG.USG_PENDING = -1 ) ";
  //$requete .= " GROUP BY ID_GROUP, GRP_NAME ";
  $requete .= " GROUP BY ID_GROUP ";
  $requete .= " ORDER BY UPPER(GRP_NAME) ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A4c]", $requete);
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='5' class='thHead'>";
    echo "<font face=verdana size=3><b>" . $l_admin_group_title . " </B></font></TH>";
  echo "</TR>";
  //
  if ( mysql_num_rows($result) > 0 )
  {
    echo "<TR>";
      $link_group_col = "&nbsp;<A HREF='list_group.php?&tri=&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_group_order_group . "' class='cattitle' >" . $l_admin_group_col_group . "</A>&nbsp;";
      if (_GROUP_USER_CAN_JOIN != "")
        echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $link_group_col . "</b></font> </TD>\n";
      else
        display_row_table($link_group_col, '');
      //
      $link_user_col = "&nbsp;<A HREF='list_group.php?&tri=username&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>&nbsp;";
      display_row_table("&nbsp;" . $l_admin_group_members . "&nbsp;", '');
      if (_SHOUTBOX != "") display_row_table("&nbsp;" . $l_admin_options_shoutbox_title_short . "&nbsp;", '');
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    // Dernière ligne : trier.
    echo "<TR>";
      echo "<TD align='center' COLSPAN='5' class='catBottom'>";
        echo "<font face=verdana size=2>";
        echo "<A HREF='group_adding.php?lang=" . $lang . "&'>" . $l_admin_group_creat_group . "</A>";
      echo "</TD>";
    echo "</TR>";
    echo "</TFOOT>";
    echo "<TBODY>";
    //
    $last_first_letter_group = "";
    $last_first_letter_user = "";
    $last_user = "";
    $last_group = "";
    while( list ($id_group, $group, $grp_shoutbox_allowed, $grp_shoutbox_need_approval, $prg_private, $nbre) = mysql_fetch_row ($result) )
    {
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
        //$last_group = $group;
        //$aff_group  = "<A HREF='group_adding_user.php?id_group=" . $id_group . "&lang=" . $lang . "&'>";
        //$aff_group .= "<IMG SRC='" . _FOLDER_IMAGES . "b_ajout.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'

        //
        if (_GROUP_USER_CAN_JOIN != "")
        {
          echo "<TD class='row1'>";
          /*
          if ($prg_private == 0) // lock_off.png lock_on.png
            echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away.png' VALIGN='BOTTOM' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_public . "' TITLE='" . $l_admin_group_public . "' border='0' />";
          else
            echo "<IMG SRC='" . _FOLDER_IMAGES . "state_off.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_private . "' TITLE='" . $l_admin_group_private . "' border='0' />";
          */
          switch ($prg_private)
          {
            case "1" : // 
              echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_official . "' TITLE='" . $l_admin_group_official . "'>";
              break;
            case "2" : // 
              echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away2.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_private . "' TITLE='" . $l_admin_group_private . "' border='0' />";
              break;
            default : // 0
              echo "<IMG SRC='" . _FOLDER_IMAGES . "state_on.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_public . "' TITLE='" . $l_admin_group_public . "' border='0' />";
              break;
          }
          echo "</TD>";
        }
        echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo "<A HREF='list_group_members.php?id_group=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_admin_group_members . "' title='" . $l_admin_group_members . "' class='cattitle'>";
        echo $group . "</A>&nbsp;";
      }
      else
      {
        echo "<TD class='row2'>&nbsp;";
      }
      echo "</TD>";
      //
      // Col username
      //
      echo "<TD align='right' class='row1'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo "<A HREF='list_group_members.php?id_group=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_admin_group_members . "' title='" . $l_admin_group_members . "' class='cattitle'>";
      echo $nbre;
      echo "</A>&nbsp;</TD>";
      //
      if (_SHOUTBOX != "") 
      {
        echo "<TD align='center' class='row1'>";
          if ($grp_shoutbox_allowed > 0)
          {
            if ($grp_shoutbox_need_approval > 0)
              echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_shoutbox_need_approval . "' TITLE='" . $l_admin_options_shoutbox_need_approval . "' border='0' />";
            else
              echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_no_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_check_on . "' TITLE='" . $l_admin_check_on . "' border='0' />";
          }
          else
          {
            echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_disabled.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_check_off . "' TITLE='" . $l_admin_check_off . "' border='0' />";
          }
          
        echo "</TD>";
      }
      //
      //
      // Col action
      echo "<TD valign='bottom' align='left' class='row2'>&nbsp;";
        echo "<A HREF='group_adding_user.php?id_group=" . $id_group . "&lang=" . $lang . "&'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ajout.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
        echo "<A HREF='group_renaming.php?id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "rename.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_rename_group . "' TITLE='" . $l_admin_group_rename_group . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        //
        ////echo "<A HREF='group_delete_user.php?id_gp=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
        //echo "<A HREF='list_group_members.php?id_gp=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
        //echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        /*
        if ($prg_private == 0)
        {
          echo "<A HREF='group_sbx_change.php?action=private&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_on.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_private . "' TITLE='" . $l_admin_group_private . "' border='0' /></A>&nbsp;";
        }
        else
        {
          echo "<A HREF='group_sbx_change.php?action=public&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_off.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_public . "' TITLE='" . $l_admin_group_public . "' border='0' /></A>&nbsp;";
        }
        */
        if (_GROUP_USER_CAN_JOIN != "")
        {
          switch ($prg_private)
          {
            case "1" : //  
              echo "<A HREF='group_sbx_change.php?action=private&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_on.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_private . "' TITLE='" . $l_admin_group_private . "' border='0' /></A>&nbsp;";
              break;
            case "2" : // 
              echo "<A HREF='group_sbx_change.php?action=public&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_off.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_public . "' TITLE='" . $l_admin_group_public . "' border='0' /></A>&nbsp;";
              break;
            default : // 0
              echo "<A HREF='group_sbx_change.php?action=official&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_on.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_group_official . "' TITLE='" . $l_admin_group_official . "' border='0' /></A>&nbsp;";
              break;
          }
        }
        

        if (_SHOUTBOX != "") 
        {
          if ($grp_shoutbox_allowed > 0)
          {
            echo "<A HREF='group_sbx_change.php?action=disable&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
            echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_disabled.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_invalidate . " " . $l_admin_options_shoutbox_title_short . "' TITLE='" . $l_admin_bt_invalidate . " " . $l_admin_options_shoutbox_title_short  . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
            if ($grp_shoutbox_need_approval > 0)
            {
              echo "<A HREF='group_sbx_change.php?action=noapproval&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_no_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_allow . " " . $l_admin_options_shoutbox_title_short . "' TITLE='" . $l_admin_bt_allow . " " . $l_admin_options_shoutbox_title_short . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
            }
            else
            {
              echo "<A HREF='group_sbx_change.php?action=needapproval&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_shoutbox_need_approval . " " . $l_admin_options_shoutbox_title_short . "' TITLE='" . $l_admin_options_shoutbox_need_approval . " " . $l_admin_options_shoutbox_title_short . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
            }
          }
          else
          {
            echo "<A HREF='group_sbx_change.php?action=enable&id_group=" . $id_group . "&lang=" . $lang . "&' class='cattitle'>";
            echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_no_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_allow . " " . $l_admin_options_shoutbox_title_short . "' TITLE='" . $l_admin_bt_allow . " " . $l_admin_options_shoutbox_title_short . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
          }
        //
        }
      echo "</TD>";
      //
      echo "</TR>";
      echo "\n";
    }
    echo "</TBODY>";
    //
    echo "</TABLE>";
    
    //if ( (strlen($alpha_link) > 3)  and (mysql_num_rows($result) > 20) )
    //  echo $alpha_link . "<BR/>";
  }
  else
  {
    echo "<TR>";
    echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_group_no_user_group;
    echo "</TD>";
    echo "</TR>";
    echo "<TR>";

    //echo "<FORM METHOD='POST' ACTION='group_adding_user.php?'>";
    echo "<FORM METHOD='POST' ACTION='group_adding.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      //echo "<INPUT TYPE='submit' VALUE = '" . $l_menu_group_add_member . "' class='liteoption' />";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_group_creat_group . "' class='liteoption' />";
      //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";

    echo "</TR>";
    echo "</TABLE>";
  }
	//
  mysql_close($id_connect);
  //
  //   Légende :
  if (_GROUP_USER_CAN_JOIN != "")
  {
    echo "</BR>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'><B>" . $l_legende . "</B></TD></TR>";

    echo "</TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
    echo "<IMG SRC='" . _FOLDER_IMAGES . "state_on.png' WIDTH='16' HEIGHT='16'>";
    echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_group_public . " : " . $l_admin_group_public_legende . "&nbsp;";
    echo "</TD>";

    echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
    echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away.png' WIDTH='16' HEIGHT='16'>";
    echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_group_official . " : " . $l_admin_group_official_legende . "&nbsp;";
    echo "</TD>";
    //
    echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
    echo "<IMG SRC='" . _FOLDER_IMAGES . "state_away2.png' WIDTH='16' HEIGHT='16'>";
    echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_group_private . " : " . $l_admin_group_private_legende . "&nbsp;";
    echo "</TD></TR>";
    echo "</TABLE>";
  }
  //
  //
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
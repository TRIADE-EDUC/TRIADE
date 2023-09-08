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
if (isset($_GET['id_role'])) $id_role_select = intval($_GET['id_role']); else $id_role_select = 0;
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
if (isset($_COOKIE['im_option_list_option_name'])) $option_show_option_name = $_COOKIE['im_option_list_option_name'];  else  $option_show_option_name = '';
//
if ($id_role_select <= 0) header("location:list_roles.php?lang=" . $lang);
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
echo "<title>[IM] " . $l_admin_roles_permissions . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _ROLES_TO_OVERRIDE_PERMISSIONS != '' )
{
  require ("../common/roles.inc.php");
  require ("../common/sql.inc.php");
  //
  //
  $err = "";
  $err_default_role = "";
	//echo "<BR/>";
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
	//
  $requete  = " select ROL_NAME, ROL_DEFAULT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
  $requete .= " WHERE ID_ROLE = " . $id_role_select;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G6d]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    list ($role, $default) = mysqli_fetch_row ($result);
    echo "<TR>";
    //echo "<TD align='center' COLSPAN='3' class='catHead'>";
    echo "<TH align='center' COLSPAN='3' class='thHead'>";
    echo "&nbsp;<font face='verdana' size='3'><b>" . $l_admin_roles_permissions_of . " <i>" . $role . "</i></b></font>";
    if ($default == "D") echo " [*]";
    //if ($default == "D") echo " <IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_default . "' TITLE='" . $l_admin_roles_default . "' border='0'>";
    echo "&nbsp;";
    echo "</TH>";
    echo "</TR>";
    //
    $requete  = " select MDL.MDL_NAME, MDL.ID_MODULE, RLM.RLM_STATE, MDL.MDL_MAX_VALUE, RLM.RLM_VALUE, MDL.MDL_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "MDL_MODULE MDL, " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE RLM ";
    $requete .= " WHERE MDL.ID_MODULE = RLM.ID_MODULE ";
    $requete .= " AND ID_ROLE = " . $id_role_select;
    $requete .= " ORDER BY ID_MODULE "; // MDL_NAME 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G6e]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      echo "<TR>";
      echo "<TD align='center' class='catHead'><b>";
        echo $l_admin_options_col_option;
      echo "</b></TD>";
      echo "<TD align='center' class='catHead'>";
        echo "&nbsp;<b>" . $l_admin_users_col_etat . "</b>&nbsp;";
      echo "</TD>";
      echo "<TD align='center' class='catHead'><b>";
        echo $l_admin_users_col_action;
      echo "</b></TD>";
      echo "</TR>";
      //
      //$coul = "row2";
      while( list ($mdl_name, $id_module, $state, $max_value, $rlm_value, $mdl_is_role) = mysqli_fetch_row ($result) )
      {
        //if ($coul == "row2") $coul = "row1";  else  $coul = "row2"; // lignes paires/impaires
        echo "<TR>";
        //
        echo "<TD class='row2'>";
          echo "<font face='verdana' size='1'>&nbsp;";
          $state_option = f_option_activated($mdl_name);
          $info = f_option_label($mdl_name);
          if ( ($option_show_option_name != "") and ($info != "") and ($mdl_is_role == "") )
          {
            if ($info != "") echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE=\"" . $info . "\" />&nbsp;";
            echo $mdl_name . "&nbsp;";
          }
          else
          {
            if ($mdl_is_role == "")  echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE=\"" . $mdl_name . "\" />&nbsp;";
            echo $info . "&nbsp;";
          }
        echo "</TD>";
        //
        // Pour les permissions équivalentes aux options, donc inutiles :
        $couleur = 'row2';
        $add_info = "";
        if ( ( ($state_option == 1) and ($state == 1) ) or
             ( ($state_option == 2) and ($state == 2) ) )
        {
          $couleur = 'row3';
          $add_info = " - " . $l_admin_role_useless_permission; // $l_admin_log_type_error; $l_admin_users_no_add_1;
        }
        if ($max_value > 0)
        {
          if ($rlm_value == f_option_value($mdl_name))
          {
            $couleur = 'row3';
            $add_info = " - " . $l_admin_role_useless_permission; // $l_admin_log_type_error; $l_admin_users_no_add_1;
          }
        }
        //
        if ($max_value > 0)
        {
          echo "<TD align='right' class='" . $couleur . "'>";
          echo "<font face='verdana' size='1'>";
          echo $rlm_value . "&nbsp;";
        }
        else
        {
          echo "<TD align='center' class='" . $couleur . "'>";
          //echo "&nbsp;";
          //if ($state == 1) echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif" . "' TITLE='" . $l_admin_options_legende_empty . "' WIDTH='16' HEIGHT='16'>";
          //if ($state == 2) echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif" . "' TITLE='" . $l_admin_options_legende_not_empty . "' WIDTH='16' HEIGHT='16'>";
          if ($state == 1) echo "<IMG SRC='" . _FOLDER_IMAGES . "b_disalow.png" . "' TITLE='" . $l_admin_role_permission_off . $add_info . "' WIDTH='16' HEIGHT='16'>";
          if ($state == 2) echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png" . "' TITLE='" . $l_admin_role_permission_on . $add_info . "' WIDTH='16' HEIGHT='16'>";
          //echo "&nbsp;";
        }
        echo "</TD>";
        //
        echo "<FORM METHOD='GET' ACTION='role_permission_add.php?'>";
        echo "<TD class='row1' align='right'>";
        //echo " ";
          echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
          echo "<INPUT TYPE='hidden' name='id_module' value = '" . $id_module . "' />";
          echo "<INPUT TYPE='hidden' name='id_role' value = '" . $id_role_select . "' />";
          echo "<INPUT TYPE='hidden' name='state' value = '0' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        echo "</TR>";
        echo "</FORM>";
        //
        // Options :
        if ( ($mdl_name == "SHOUTBOX") and (_SHOUTBOX == "") ) $err .= "_SHOUTBOX - ";
        if ( ($mdl_name == "SHOUTBOX_REFRESH_DELAY") and (_SHOUTBOX == "") and (strpos($err, "SHOUTBOX ") <= 0) ) $err .= "_SHOUTBOX - ";
        if ( ($mdl_name == "SHOUTBOX_QUOTA_USER_DAY") and (_SHOUTBOX == "") and (strpos($err, "SHOUTBOX ") <= 0) ) $err .= "_SHOUTBOX - ";
        if ( ($mdl_name == "SHOUTBOX_QUOTA_USER_WEEK") and (_SHOUTBOX == "") and (strpos($err, "SHOUTBOX ") <= 0) ) $err .= "_SHOUTBOX - ";
        //
        if ( ($mdl_name == "SHOUTBOX_NEED_APPROVAL") and (_SHOUTBOX == "") and (strpos($err, "SHOUTBOX ") <= 0) ) $err .= "_SHOUTBOX - ";
        if ( ($mdl_name == "SHOUTBOX_APPROVAL_QUEUE_USER") and (_SHOUTBOX_NEED_APPROVAL == "") and (strpos($err, "SHOUTBOX_NEED_APPROVAL") <= 0) ) $err .= "_SHOUTBOX_NEED_APPROVAL - ";
        if ( ($mdl_name == "SHOUTBOX_LOCK_USER_APPROVAL") and (_SHOUTBOX_NEED_APPROVAL == "") and (strpos($err, "SHOUTBOX_NEED_APPROVAL") <= 0) ) $err .= "_SHOUTBOX_NEED_APPROVAL - ";
        //
        if ( ($mdl_name == "SHOUTBOX_VOTE") and (_SHOUTBOX == "") and (strpos($err, "SHOUTBOX ") <= 0) ) $err .= "_SHOUTBOX - ";
        if ( ($mdl_name == "SHOUTBOX_MAX_NOTES_USER_DAY") and (_SHOUTBOX_VOTE == "") and (strpos($err, "SHOUTBOX_VOTE") <= 0) ) $err .= "_SHOUTBOX_VOTE - ";
        if ( ($mdl_name == "SHOUTBOX_MAX_NOTES_USER_WEEK") and (_SHOUTBOX_VOTE == "") and (strpos($err, "SHOUTBOX_VOTE") <= 0) ) $err .= "_SHOUTBOX_VOTE - ";
        if ( ($mdl_name == "SHOUTBOX_REMOVE_MESSAGE_VOTES") and (_SHOUTBOX_VOTE == "") and (strpos($err, "SHOUTBOX_VOTE") <= 0) ) $err .= "_SHOUTBOX_VOTE - ";
        if ( ($mdl_name == "SHOUTBOX_LOCK_USER_VOTES") and (_SHOUTBOX_VOTE == "") and (strpos($err, "SHOUTBOX_VOTE") <= 0) ) $err .= "_SHOUTBOX_VOTE - ";
        if ( ($mdl_name == "SHOUTBOX_LOCK_USER_VOTES") and (_SHOUTBOX_VOTE == "") and (strpos($err, "SHOUTBOX_VOTE") <= 0) ) $err .= "_SHOUTBOX_VOTE - ";
        //
        if ( ($mdl_name == "BOOKMARKS") and (_BOOKMARKS == "") ) $err .= "_BOOKMARKS - ";
        if ( ($mdl_name == "BOOKMARKS_VOTE") and (_BOOKMARKS == "") and (strpos($err, "BOOKMARKS") <= 0) ) $err .= "_BOOKMARKS - ";
        if ( ($mdl_name == "BOOKMARKS_NEED_APPROVAL") and (_BOOKMARKS == "") and (strpos($err, "BOOKMARKS") <= 0) ) $err .= "_BOOKMARKS - ";
        if ( ($mdl_name == "SERVERS_STATUS") and (_SERVERS_STATUS == "") ) $err .= "_SERVERS_STATUS - ";
        if ( ($mdl_name == "ROLE_CHANGE_SERVER_STATUS") and (_SERVERS_STATUS == "") ) $err .= "_SERVERS_STATUS - ";
        if ( ($mdl_name == "USER_HIEARCHIC_MANAGEMENT_BY_ADMIN") and (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == "") ) $err .= "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN - ";
        //if ( ($mdl_name == "GROUP_USER_CAN_JOIN") and ( (_SPECIAL_MODE_GROUP_COMMUNITY == "") or (_GROUP_FOR_SBX_AND_ADMIN_MSG == "") ) )  $err .= "_SPECIAL_MODE_GROUP_COMMUNITY or _GROUP_FOR_SBX_AND_ADMIN_MSG - ";
        if ( ($mdl_name == "GROUP_USER_CAN_JOIN") and ( ( (_SPECIAL_MODE_GROUP_COMMUNITY == "") and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == "") ) or (_GROUP_FOR_SBX_AND_ADMIN_MSG == "") ) )  $err .= "_SPECIAL_MODE_GROUP_COMMUNITY or _SPECIAL_MODE_OPEN_GROUP_COMMUNITY or _GROUP_FOR_SBX_AND_ADMIN_MSG - ";
        if ( ($mdl_name == "HISTORY_MESSAGES_ON_ACP") and (_CRYPT_MESSAGES != "") ) $err .= "_CRYPT_MESSAGES - ";
        //
        if ( ($mdl_name == "BACKUP_FILES") and (_BACKUP_FILES == "") ) $err .= "_BACKUP_FILES - ";
        //
        if ( ($mdl_name == "SHARE_FILES") and (_SHARE_FILES == "") ) $err .= "_SHARE_FILES - ";
        if ( ($mdl_name == "SHARE_FILES_NEED_APPROVAL") and (_SHARE_FILES_COMPRESS != "") ) $err .= $l_admin_bt_invalidate . ": _SHARE_FILES_COMPRESS - ";
        if ( ($mdl_name == "SHARE_FILES_EXCHANGE_NEED_APPROVAL") and (_SHARE_FILES_COMPRESS != "") ) $err .= $l_admin_bt_invalidate . ": _SHARE_FILES_COMPRESS - ";
        if ( ($mdl_name == "SHARE_FILES_TRASH") and (_SHARE_FILES_COMPRESS != "") ) $err .= $l_admin_bt_invalidate . ": _SHARE_FILES_COMPRESS - ";
        if ( ($mdl_name == "SHARE_FILES_EXCHANGE_TRASH") and (_SHARE_FILES_COMPRESS != "") ) $err .= $l_admin_bt_invalidate . ": _SHARE_FILES_COMPRESS - ";
        //
        // Roles :
        if ( ($mdl_name == "ROLE_GET_ADMIN_ALERT_MESSAGES") and (_SEND_ADMIN_ALERT == "") ) $err .= "_SEND_ADMIN_ALERT - ";
        if ( ($mdl_name == "ROLE_SEND_ALERT_TO_ADMIN") and (_SEND_ADMIN_ALERT == "") and (strpos($err, "SEND_ADMIN_ALERT") <= 0) ) $err .= "_SEND_ADMIN_ALERT - ";
        if ( ($mdl_name == "ROLE_BROADCAST_ALERT_TO_GROUP") and (_SEND_ADMIN_ALERT == "") and (strpos($err, "SEND_ADMIN_ALERT") <= 0) ) $err .= "_SEND_ADMIN_ALERT - ";
        if ( ($mdl_name == "ROLE_BROADCAST_ALERT") and (_SEND_ADMIN_ALERT == "") and (strpos($err, "SEND_ADMIN_ALERT") <= 0) ) $err .= "_SEND_ADMIN_ALERT - ";
        //
        // Role par défaut :
        if ( ($default == "D") and ($state == 2) )
        {
          if ( ($mdl_name == "SHOUTBOX") and (_SHOUTBOX != "") ) $err_default_role .= "_SHOUTBOX - ";
          if ( ($mdl_name == "BOOKMARKS") and (_BOOKMARKS != "") ) $err_default_role .= "_BOOKMARKS - ";
          if ( ($mdl_name == "SHARE_FILES") and (_SHARE_FILES != "") ) $err_default_role .= "_SHARE_FILES - ";
          if ( ($mdl_name == "BACKUP_FILES") and (_BACKUP_FILES != "") ) $err_default_role .= "_BACKUP_FILES - ";
        }
      }
      if ($default == "D")
      {
        //
        echo "<TR>";
        echo "<TD align='center' COLSPAN='3' class='catBottom'>";
          echo "<font face='verdana' size='2'>";
          echo "[*] ";
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_roles_default . "' TITLE='" . $l_admin_roles_default . "' border='0'> ";
          echo $l_admin_roles_default;
        echo "</TD>";
        echo "</TR>";
        echo "<TR>";
      }
    }
    else
    {
      echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='row2'>";
      echo "<font face='verdana' size='2'>";
      echo $l_admin_roles_permissions_empty;
      echo "</TD>";
      echo "</TR>";
      echo "<TR>";
    }
  }
	//
	echo "</TABLE>";	//
  //
  //
  // -------------------------------------------------------------------------------------------------------------------
  //
  //
  if ($err != "")
  {
    $err = trim($err);
    $err = substr($err, 0, strlen($err) -1);
    echo "<div class='warning'>";
    echo $l_admin_roles_need_active_option . "<br/>";
    echo "<A HREF='list_options_updating.php?lang=" . $lang . "&'>" . $l_admin_roles_unactivated_options . "</A>: <font size='1'>" . $err . "</font>";
    //echo "</div>";
    //echo "<div class='notice'>";
    echo "<br/>";
    echo "<br/>";
    echo "<font color='black'>";
    echo $l_admin_roles_permissions_only_role;
    echo "</div>";
  }
  //
  if ($err_default_role != "")
  {
    $err_default_role = trim($err_default_role);
    $err_default_role = substr($err_default_role, 0, strlen($err_default_role) -1);
    echo "<div class='warning'>";
    echo "<font color='black'>";
    echo $l_admin_roles_default . "<br/><br/>";
    echo "</font>";
    echo $l_admin_roles_need_active_option . "<br/>";
    echo "<A HREF='list_options_updating.php?lang=" . $lang . "&'>" . $l_admin_roles_activated_options . "</A>: <font size='1'>" . $err_default_role . "</font>";
    //echo "</div>";
    //echo "<div class='notice'>";
    echo "<br/>";
    echo "<br/>";
    echo "<font color='black'>";
    echo $l_admin_roles_permissions_only_role;
    echo "</div>";
  }
  else
    echo "<BR/>";
  //
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
    //echo "<TD align='center' COLSPAN='3' class='catHead'>";
    echo "<TH align='center' COLSPAN='3' class='thHead'>";
    echo "&nbsp;<font face='verdana' size='3'><b>" . $l_admin_roles_permissions_add . "</b></font>&nbsp;";
    echo "</TH>";
    echo "</TR>";
    $requete  = " select MDL.MDL_NAME, MDL.ID_MODULE, MDL.MDL_MAX_VALUE, MDL.MDL_ROLE, MDL.MDL_OTHER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "MDL_MODULE MDL ";
    $requete .= " WHERE ID_MODULE NOT IN (select ID_MODULE FROM " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE WHERE ID_ROLE = " . $id_role_select . " ) ";
    if ($default == "D") $requete .= " and MDL_OTHER <> '' ";
    if (_SHOUTBOX == "") $requete .= " and MDL_NAME not like 'SHOUTBOX_%' ";    // on affiche que la première option (pour voir qu'elle n'est pas active)
    if (_BOOKMARKS == "") $requete .= " and MDL_NAME not like 'BOOKMARKS%_' ";  // on affiche que la première option (pour voir qu'elle n'est pas active)
    if (_SHARE_FILES == "") $requete .= " and MDL_NAME not like 'SHARE_FILES_%' ";  // on affiche que la première option (pour voir qu'elle n'est pas active)
    if (_BACKUP_FILES == "") $requete .= " and MDL_NAME not like 'BACKUP_FILES_%' ";  // on affiche que la première option (pour voir qu'elle n'est pas active)
    //$requete .= " ORDER BY ID_MODULE "; // MDL_NAME 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G6f]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      echo "<TR>";
      echo "<TD align='center' class='catHead'>&nbsp;<b>";
        echo $l_admin_users_col_etat;
      echo "</b>&nbsp;</TD>";
      echo "<TD align='center' class='catHead'><b>";
        echo $l_admin_options_col_option;
      echo "</b></TD>";
      echo "<TD align='center' class='catHead'><b>";
        echo $l_admin_users_col_action;
      echo "</b></TD>";
      echo "</TR>";
      //
      // $mdl_other :
      $t_SHOUTBOX = f_role_permission($id_role_select, "SHOUTBOX", _SHOUTBOX);
      $t_BOOKMARKS = f_role_permission($id_role_select, "BOOKMARKS", _BOOKMARKS);
      $t_BACKUP_FILES = f_role_permission($id_role_select, "BACKUP_FILES", _BACKUP_FILES);
      $t_SHARE_FILES = f_role_permission($id_role_select, "SHARE_FILES", _SHARE_FILES);
      $t_SHARE_FILES_EXCHANGE = f_role_permission($id_role_select, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
      $coul = "row2";
      while( list ($mdl_name, $id_module, $max_value, $mdl_is_role, $mdl_other) = mysqli_fetch_row ($result) )
      {
        $display_this_row = "X";
        $display_this_row_but_warning = "";
        //
        // Si le role majeur est désactivé, alors les sous-roles sont masqués :
        if ( ($t_BOOKMARKS == "") and ( ($mdl_name == "BOOKMARKS_VOTE") or ($mdl_name == "BOOKMARKS_NEED_APPROVAL") 
          ) ) $display_this_row = "";
        //
        if ( ($t_SHOUTBOX == "") and ( ($mdl_name == "SHOUTBOX_VOTE") or ($mdl_name == "SHOUTBOX_NEED_APPROVAL") 
          or ($mdl_name == "SHOUTBOX_REFRESH_DELAY") or ($mdl_name == "SHOUTBOX_QUOTA_USER_DAY") or ($mdl_name == "SHOUTBOX_QUOTA_USER_WEEK")
          or ($mdl_name == "SHOUTBOX_APPROVAL_QUEUE_USER") or ($mdl_name == "SHOUTBOX_LOCK_USER_APPROVAL") or ($mdl_name == "SHOUTBOX_MAX_NOTES_USER_DAY")
          or ($mdl_name == "SHOUTBOX_MAX_NOTES_USER_WEEK") or ($mdl_name == "SHOUTBOX_REMOVE_MESSAGE_VOTES") or ($mdl_name == "SHOUTBOX_LOCK_USER_VOTES")
          ) ) $display_this_row = "";
        //
        if ( ($t_BACKUP_FILES == "") and ( ($mdl_name == "BACKUP_FILES_ALLOW_SUB_FOLDERS") or ($mdl_name == "BACKUP_FILES_ALLOW_MULTI_FOLDERS") 
          or ($mdl_name == "BACKUP_FILES_MAX_ARCHIVE_SIZE") or ($mdl_name == "BACKUP_FILES_MAX_NB_ARCHIVES_USER") or ($mdl_name == "BACKUP_FILES_MAX_SPACE_SIZE_USER")
          ) ) $display_this_row = "";
        //
        if ( ($t_SHARE_FILES == "") and ( ($mdl_name == "SHARE_FILES_NEED_APPROVAL") or ($mdl_name == "SHARE_FILES_EXCHANGE") 
          or ($mdl_name == "SHARE_FILES_EXCHANGE_NEED_APPROVAL") or ($mdl_name == "SHARE_FILES_VOTE") or ($mdl_name == "SHARE_FILES_TRASH")
          or ($mdl_name == "SHARE_FILES_EXCHANGE_TRASH") or ($mdl_name == "SHARE_FILES_MAX_FILE_SIZE") or ($mdl_name == "SHARE_FILES_MAX_NB_FILES_USER")
          or ($mdl_name == "SHARE_FILES_MAX_SPACE_SIZE_USER") or ($mdl_name == "SHARE_FILES_QUOTA_FILES_USER_WEEK") or ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY")
          or ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK") or ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH") or ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY")
          or ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK") or ($mdl_name == "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH")
          ) ) $display_this_row = "";
        //
        if ( ($t_SHARE_FILES_EXCHANGE == "") and ( ($mdl_name == "SHARE_FILES_EXCHANGE_NEED_APPROVAL") or ($mdl_name == "SHARE_FILES_EXCHANGE_TRASH") 
          ) ) $display_this_row = "";
        //
        //
        if ( (_SERVERS_STATUS == "") and ( ($mdl_name == "SERVERS_STATUS") or ($mdl_name == "ROLE_CHANGE_SERVER_STATUS") 
          ) ) $display_this_row = "";
        //
        if ( (_SEND_ADMIN_ALERT == "") and ( ($mdl_name == "ROLE_GET_ADMIN_ALERT_MESSAGES") or ($mdl_name == "ROLE_SEND_ALERT_TO_ADMIN") 
          or ($mdl_name == "ROLE_BROADCAST_ALERT_TO_GROUP") or ($mdl_name == "ROLE_BROADCAST_ALERT")
          ) ) $display_this_row = "";
        //
        //
        if ( ($t_SHOUTBOX == "") and ($mdl_name == "SHOUTBOX") ) $display_this_row_but_warning = "X";
        if ( ($t_BOOKMARKS == "") and ($mdl_name == "BOOKMARKS") ) $display_this_row_but_warning = "X";
        if ( ($t_BACKUP_FILES == "") and ($mdl_name == "BACKUP_FILES") ) $display_this_row_but_warning = "X";
        if ( ($t_SHARE_FILES == "") and ($mdl_name == "SHARE_FILES") ) $display_this_row_but_warning = "X";
        //
        //
        if ($display_this_row != "")
        {
          if ($coul == "row2") $coul = "row1";  else  $coul = "row2"; // lignes paires/impaires
          //
          echo "<TR>";
          if ($max_value > 0)
          {
            echo "<TD class='" . $coul . "' ALIGN='RIGHT'>";
            echo "<font face='verdana' size='1'>";
            echo f_option_value($mdl_name) . "&nbsp;";
          }
          else
          {
            $state_option = f_option_activated($mdl_name);
            if ($mdl_is_role == "") // if ( ($state_option == 1) or ($state_option == 2) )
              echo "<TD align='center' class='row1'>";
            else
              echo "<TD align='center' class='rowpic'>";  // 'row3'   ex: ROLE_GET_ADMIN_ALERT_MESSAGES
            //
              if ($state_option == 1) echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif" . "' TITLE='" . $l_admin_options_legende_empty . "' WIDTH='16' HEIGHT='16'>";
              if ($state_option == 2) echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif" . "' TITLE='" . $l_admin_options_legende_not_empty . "' WIDTH='16' HEIGHT='16'>";
          }
          echo "</TD>";
          //
          echo "<TD class='" . $coul . "'>";
            echo "<font face='verdana' size='1'>&nbsp;";
            
            $info = f_option_label($mdl_name);
            if ($display_this_row_but_warning != "") 
            {
              if ($option_show_option_name != "") 
                $mdl_name = "<font color='red'>" . $l_admin_role_cannot_option . "</font> (" . $l_admin_role_cannot_option_see_default_role . ")<br/>&nbsp; &nbsp; &nbsp; " . $mdl_name;
              else
                $info = "<font color='red'>" . $l_admin_role_cannot_option . "</font> (" . $l_admin_role_cannot_option_see_default_role . ")<br/>&nbsp; &nbsp; &nbsp; " . $info;
            }
            //
            if ( ($option_show_option_name != "") and ($info != "")  and ($mdl_is_role == "") )
            {
              if ($info != "") echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE=\"" . $info . "\" />&nbsp;";
              echo $mdl_name . "&nbsp;";
            }
            else
            {
              if ($mdl_is_role == "")  echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE=\"" . $mdl_name . "\" />&nbsp;";
              //
              echo $info . "&nbsp;";
            }
          echo "</TD>";
          //
          echo "<FORM METHOD='GET' ACTION='role_permission_add.php?'>";
          echo "<INPUT TYPE='hidden' name='id_module' value = '" . $id_module . "' />";
          echo "<INPUT TYPE='hidden' name='id_role' value = '" . $id_role_select . "' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
          echo "<TD class='" . $coul . "' align='center'>";
          #if ( ($display_this_row_but_warning == "") or (1 == 1) ) // pour tests
          if ($display_this_row_but_warning == "")
          {
            if ($max_value > 0)
            {
                //$valeur_actuelle = f_option_value($mdl_name);
                //if (intval($valeur_actuelle) <= 0) $valeur_actuelle = "";
                echo "<input name='rlm_value'class='post' maxlength='4' size='3' />";
                //echo " value='" . $valeur_actuelle . "' ";
                echo "<INPUT TYPE='hidden' name='state' value = '3' />";
                //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_create . "' class='liteoption' />"; // l_admin_bt_add l_admin_bt_update
                echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
            }
            else
            {
              //echo "<font face='verdana' size='2'>&nbsp;";
              //echo "<BR/>";
              if ($state_option == 2)
              {
                echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_invalidate . "' class='liteoption' />";
                echo "<INPUT TYPE='hidden' name='state' value = '1' />";
                //echo "&nbsp;";
                //echo "<A HREF='role_permission_add.php?id_role=" . $id_role_select . "&id_module=" . $id_module . "&state=1&lang=" . $lang . "&' title='" . $l_admin_bt_invalidate . "'>";
                //echo "<IMG SRC='" . _FOLDER_IMAGES . "b_disalow.png'  WIDTH='16' HEIGHT='16' BORDER='0'></A>";
              }
              else
              {
                echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_allow . "' class='liteoption' />";
                echo "<INPUT TYPE='hidden' name='state' value = '2' />";
                //echo "<A HREF='role_permission_add.php?id_role=" . $id_role_select . "&id_module=" . $id_module . "&state=2&lang=" . $lang . "&' title='" . $l_admin_bt_allow . "'>";
                //echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
                //echo "&nbsp;";
              }
              //echo "&nbsp;";
            }
          }
          echo "</TD>";
          echo "</FORM>";
          echo "</TR>";
        }
      }
      echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='catBottom'>";
        echo "<font face='verdana' size='2'>";
        echo "<A HREF='role_permissions_list.php?lang=" . $lang . "&'>";
        echo $l_admin_role_dashboard . "</A>";
      echo "</TD>";
      echo "</TR>";
      echo "<TR>";
    }
    else
    {
      fill_table_module();
    }
	//
	echo "</TABLE>";	//
  //
  mysqli_close($id_connect);
  //
  //
  echo "<BR/>";
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
    echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'>";
      //echo "<IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' ALT='" . $l_admin_options_new . "' TITLE='" . $l_admin_options_new . "' /> &nbsp; ";
    echo "<B>" . $l_admin_display_title . "</B></TD></TR>";
    //echo "<TR><TD COLSPAN='2' class='row3'>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN='2' class='row1'>";
      echo "<font face=verdana size=2>";
      echo "<INPUT name='option_show_option_name' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
      //if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == '') or (_SPECIAL_MODE_GROUP_COMMUNITY != '') )  echo " disabled ";
      if ($option_show_option_name <> '') echo "CHECKED";
      echo " />" . $l_admin_options_show_option_name ; //"<BR/>\n";
      echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE='" . $l_admin_options_show_option_name ."' ALT='" . $l_admin_options_show_option_name ."' />&nbsp;";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catBottom'>";
    echo "<input type='hidden' name='action' value = 'role_permissions' />"; // les paramètres de cette page, et y revenir ensuite
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "<INPUT TYPE='hidden' name='id_role' value = '" . $id_role_select . "' />";
    echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
    echo "</TD>";
    echo "</TR>";
    echo "</FORM>";
  echo "</TABLE>";
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
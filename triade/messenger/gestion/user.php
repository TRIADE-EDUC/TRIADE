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
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else $tri = "";
if (isset($_GET['id_user'])) $id_user = intval($_GET['id_user']);  else $id_user = 0;
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['action'])) $action = $_GET['action']; else $action = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['from_list'])) $from_list = $_GET['from_list']; else $from_list = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
//
if ( ($id_user <= 0) or (preg_match("#[^0-9]#", $id_user)) )
{
  header("location:list_users.php?lang=" . $lang . "&tri=" . $tri . "&page=" . $page . "&");
  break;
}
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_user_informations . "</title>";
display_header();
echo '<META http-equiv="refresh" content="60;url=" />';
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
require ("../common/sessions.inc.php");
//
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
$hide_ip = "";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) $hide_ip = "X";
//
//
$requete  = " SELECT USR_USERNAME, USR_NICKNAME, USR_NAME, USR_LEVEL, USR_CHECK, USR_STATUS, USR_DATE_CREAT, USR_DATE_LAST, USR_PASSWORD, ";
$requete .= " USR_VERSION, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, USR_AVATAR, USR_TIME_SHIFT, USR_OS, USR_IP_ADDRESS, USR_GENDER, ";
$requete .= " USR_EMAIL, USR_PHONE, USR_NB_CONNECT, USR_GET_ADMIN_ALERT, USR_MAC_ADR, USR_COMPUTERNAME, USR_SCREEN_SIZE, USR_EMAIL_CLIENT, ";
$requete .= " USR_BROWSER, USR_OOO, USR_RATING, USR_DATE_PASSWORD, USR_DATE_ACTIVITY, USR_DATE_BACKUP, ID_ROLE ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
$requete .= " WHERE ID_USER = " . $id_user . " ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A1f]", $requete);
//
$trouv = "";
if ( mysqli_num_rows($result) == 1 )
{
  list ($username, $nickname, $nom, $usr_level, $usr_check, $usr_status, $usr_datcreat, $usr_datlast, $passcr, $version, $country_code, 
        $language_code, $avatar, $time_shit, $win_os, $ip, $genre, $email, $phone, $nb_connect, $get_admin_alert, $mac_adr, $computername, 
        $screen_size, $mailclient, $browser, $ooo, $usr_rating, $usr_datpwd, $usr_datactivity, $usr_date_backup, $usr_id_role) = mysqli_fetch_row ($result);
  $trouv = "X";
  if ($avatar == "") $avatar = $username . ".jpg";
  if (!is_readable("../distant/avatar/" . $avatar)) $avatar = "";
  if ($usr_datcreat != '0000-00-00') $usr_datcreat = date($l_date_format_display, strtotime($usr_datcreat));
  if ($usr_datlast != '0000-00-00') $usr_datlast = date($l_date_format_display, strtotime($usr_datlast));
  if ($usr_datpwd != '0000-00-00') $usr_datpwd = date($l_date_format_display, strtotime($usr_datpwd));
  if ($usr_datactivity != '0000-00-00') $usr_datactivity = date($l_date_format_display, strtotime($usr_datactivity));
  if ($usr_date_backup != '0000-00-00') $usr_date_backup = date($l_date_format_display, strtotime($usr_date_backup));
}
//
if ($trouv == "") die ("Who ?");
//
$cleanusername = f_DelSpecialChar($username);
$user_banned = f_is_banned_user_ip_pc($cleanusername, "U");
$ip_banned = f_is_banned_user_ip_pc($ip, "I");
$pc_banned = f_is_banned_user_ip_pc($usr_check, "P");
$is_session_open = "";
if (f_get_id_session_id_user($id_user) > 0) $is_session_open = "X";
if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
//
$nbmax_days = f_nb_days_usage_max();
//
//
if (_SHOUTBOX != "")
{
  $sbx_nb_msg_ok = 0;
  $sbx_nb_msg_ko = 0;
  $sbx_nb_votes_p = 0;
  $sbx_nb_votes_c = 0;
  $sbx_nb_give_votes_p = 0;
  $sbx_nb_give_votes_c = 0;
  $sbx_last_vote = "";
  //
  $requete  = " SELECT SBS_NB, SBS_NB_REJECT, SBS_NB_VOTE_M, SBS_NB_VOTE_L, SBS_MAX_VOTE_M, SBS_MAX_VOTE_L, SBS_LAST_DATE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1f2]", $requete);
  list ($sbx_nb_msg_ok, $sbx_nb_msg_ko, $sbx_nb_votes_p, $sbx_nb_votes_c, $sbx_nb_votes_max_tot_p, $sbx_nb_votes_max_tot_c, $sbx_last_vote) = mysqli_fetch_row ($result);
  //
  if (_SHOUTBOX_VOTE != "")
  {
    // Nbre de votes + (donnés)
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
    //$requete .= " where ID_USER_AUT = " . $id_user;
    $requete .= " where ID_USER_VOTE = " . $id_user;
    $requete .= " and SBV_VOTE_M > 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A1f3]", $requete);
    list ($sbx_nb_give_votes_p) = mysqli_fetch_row ($result);
    //
    // Nbre de votes - (donnés)
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
    //$requete .= " where ID_USER_AUT = " . $id_user;
    $requete .= " where ID_USER_VOTE = " . $id_user;
    $requete .= " and SBV_VOTE_L < 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A1f4]", $requete);
    list ($sbx_nb_give_votes_c) = mysqli_fetch_row ($result);
  }
}
//
if ( _SHARE_FILES != '' )
{
  $share_file_nb = 0;
  $share_file_nb_give_votes_p = 0;
  $share_file_nb_give_votes_c = 0;
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user ;
  $requete .= " and FIL_ONLINE = 'Y' ";
  $requete .= " and ID_USER_DEST is null ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1f6]", $requete);
  list ($share_file_nb) = mysqli_fetch_row ($result);
  //
  $requete  = " select SUM(FIL_SIZE) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user ;
  $requete .= " and FIL_ONLINE <> '' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1f9]", $requete);
  list ($t_size_ko) = mysqli_fetch_row ($result);
  $file_share_size_mo = ($t_size_ko / 1024);
  $file_share_size_mo = ceil($file_share_size_mo);
  //
  $requete  = " SELECT FSD_NB_DOWNLOAD ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD ";
  $requete .= " WHERE ID_USER_DL = " . $id_user ;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1f10]", $requete);
  list ($nb_file_download) = mysqli_fetch_row ($result);
  
  //
  if (_SHARE_FILES_VOTE != "")
  {
    // Nbre de votes + (donnés)
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
    //$requete .= " where ID_USER_AUT = " . $id_user;
    $requete .= " where ID_USER_VOTE = " . $id_user;
    $requete .= " and FLV_VOTE_M > 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A1f7]", $requete);
    list ($share_file_nb_give_votes_p) = mysqli_fetch_row ($result);
    //
    // Nbre de votes - (donnés)
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
    //$requete .= " where ID_USER_AUT = " . $id_user;
    $requete .= " where ID_USER_VOTE = " . $id_user;
    $requete .= " and FLV_VOTE_L < 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A1f8]", $requete);
    list ($share_file_nb_give_votes_c) = mysqli_fetch_row ($result);
  }
}
//
if (_BACKUP_FILES != "")
{
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE ID_USER = " . $id_user;
  $requete .= " and FIB_ONLINE <> '' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1f11]", $requete);
  list ($nb_file_backup) = mysqli_fetch_row ($result);
  //
  $requete  = " select SUM(FIB_SIZE) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE ID_USER = " . $id_user;
  $requete .= " and FIB_ONLINE <> '' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1f12]", $requete);
  list ($t_size_ko) = mysqli_fetch_row ($result);
  $file_backup_size_mo = ($t_size_ko / 1024);
  $file_backup_size_mo = ceil($file_backup_size_mo);
}

//
//
//mysqli_close($id_connect); non plus bas !
//
//
if ($action == "wait")
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<FORM METHOD='POST' name='formulaire' ACTION ='user_wait.php?'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_invalidate . " " . $username . " ?' />";
  echo "<INPUT TYPE='hidden' name='id_user' value = '" . $id_user . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "' />";
  echo "<INPUT TYPE='hidden' name='only_status' value = '" . $only_status . "' />";
  echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
  if ($from_list == "") echo "<INPUT TYPE='hidden' name='from' value='user' />";
  echo "</FORM>";
}
//
if ($action == "delete")
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<FORM METHOD='POST' name='formulaire' ACTION ='user_delete.php?'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . " " . $username . " ?' />";
  echo "<INPUT TYPE='hidden' name='id_user' value = '" . $id_user . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "' />";
  echo "<INPUT TYPE='hidden' name='only_status' value = '" . $only_status . "' />";
  echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
  echo "</FORM>";
}
// waiting validating :
//if ( ( ($usr_check == "WAIT") or ($usr_status == 2) ) and ($action == "") )
//if ( ($usr_status == 2) and ($action == "") )
if ( ( ($usr_status == 2) or ($usr_status == 4) ) and ($action == "") )
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<FORM METHOD='GET' name='formulaire' ACTION ='user_autorize.php?'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_allow . " " . $username . " ?' />";
  echo "<INPUT TYPE='hidden' name='id_user' value = '" . $id_user . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "' />";
  echo "<INPUT TYPE='hidden' name='only_status' value = '" . $only_status . "' />";
  echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
  if ($from_list == "") echo "<INPUT TYPE='hidden' name='from' value='user' />";
  echo "</FORM>";
}
//
//
//echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
	echo "<TH align=center COLSPAN='11' class='thHead'>";
	echo "<font face='verdana' size='2'><b>&nbsp; " . $l_user_informations . "</b></font></TH>";
	//echo "<font face='verdana' size='5'><b>&nbsp; " . $username . "</b></font></TH>";
echo "</TR>";
/*
echo "<TR>";
  display_row_table($l_admin_users_col_action, '150');
  display_row_table($l_admin_users_col_action, '160');
  display_row_table('', '10');
  display_row_table('Avatar', '');
echo "</TR>";
*/
//
echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_users_col_user;
    echo "</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'><B> &nbsp;";
      if ($language_code != '')
      {
        if (is_readable("../images/flags/" . strtolower($language_code) . ".png")) 
        {
          if ($display_flag_country != '')
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$language_code];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            $country_name = f_language_of_country($language_code, $country_name);
            $country_name = $l_language . " : " . $country_name;
          }
          else
            $country_name = $l_language;
          //
          echo "<IMG SRC='../images/flags/" . strtolower($language_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
        }
      }
      //
      if ($user_banned == true) echo "<font color='red'>";
      echo $username . "</B>";
      if ($user_banned == true) echo "&nbsp;<A HREF='list_ban.php?ban=users&lang=" . $lang . "&'><IMG SRC='" . _FOLDER_IMAGES . "ko.gif' ALT='" . $l_admin_users_user_banned . "' TITLE='" . $l_admin_users_user_banned . "' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
    echo "</TD>";
    if ( ($user_banned == true) or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") )
    {
      echo "<TD class='row2' align='center'>";
      echo "</TD>";
    }
    else
    {  
      echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
      echo "<TD class='row1' VALIGN='TOP' align='CENTER'>";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_ban.png' VALUE = '" . $l_admin_users_ban_user . "' ALT='" . $l_admin_users_ban_user . "' TITLE='" . $l_admin_users_ban_user . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_user' value = " . $id_user . " />";
        //echo "<input type='hidden' name='ban_value' value = '" . $username . "' />";
        echo "<input type='hidden' name='ban_value' value = '" . $cleanusername . "' />";
        echo "<input type='hidden' name='ban_type' value = 'U' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
    }
    
    echo "<TD class='row1' ROWSPAN='30' ALIGN='CENTER' VALIGN='CENTER'>";
      //echo "<font face='verdana' size='2'>";
      if ($avatar != "")  
      {
        echo "<IMG SRC='../distant/avatar/" . $avatar . "' /><BR/>";
        echo "<FORM METHOD='GET' name='formulaire' ACTION ='avatar_changing.php?'>";
        echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
        echo "<INPUT TYPE='hidden' name='id_user_select' value = '" . $id_user . "' />";
        echo "<INPUT TYPE='hidden' name='username' value = '" . $username . "' />";
        echo "<INPUT TYPE='hidden' name='avatar' value = '" . $avatar . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='hidden' name='from' value='user' />";
        echo "</FORM>";
      }
    echo "</TD>";
echo "</TR>";
//
if ($nom != 'HIDDEN') 
{
  echo "<TR>";
    echo "<TD class='row2' VALIGN='CENTER'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_users_col_function;
    echo "&nbsp;</TD>";
    echo "<FORM METHOD='POST' ACTION='user_update_name.php?'>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<input type='text' name='nom' maxlength='40' value='" . $nom . "' size='25' class='post' />";
      echo " ";
      //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
      echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
      //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      //echo "<input type='hidden' name='only_status' value = '" . $only_status . "' />";
      //echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "<INPUT TYPE='hidden' name='from' value='user' />";
    echo "</TD>";
    echo "<TD class='row1' align='center'>";
      if ($action == "") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
    echo "</TD>";
    echo "</FORM>";
  echo "</TR>";
}
//
echo "<TR>";
    echo "<TD class='row2' VALIGN='CENTER'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_email;
    echo "</TD>";
    echo "<FORM METHOD='POST' ACTION='user_update_email.php?'>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<input type='email' name='email' maxlength='80' value='" . $email . "' size='25' class='post' />"; // html5
      echo " ";
      //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
      echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "<INPUT TYPE='hidden' name='from' value='user' />";
    echo "</TD>";
    echo "<TD class='row1' align='center'>";
      if ($action == "") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
    echo "</TD>";
    echo "</FORM>";
echo "</TR>";
//
echo "<TR>";
    echo "<TD class='row2' VALIGN='CENTER'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_phone;
    echo "</TD>";
    echo "<FORM METHOD='POST' ACTION='user_update_phone.php?'>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<input type='tel' name='phone' maxlength='20' value='" . $phone . "' size='25' class='post' />"; // html5
      echo " ";
      //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
      echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "</TD>";
    echo "<TD class='row1' align='center'>";
      if ($action == "") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
    echo "</TD>";
    echo "</FORM>";
echo "</TR>";
//
if ( (_ALLOW_MANAGE_PROFILE == "") or ( (_ALLOW_CHANGE_EMAIL_PHONE == "") and (_ALLOW_CHANGE_FUNCTION_NAME == "") ) )
{
  echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_gender;
    echo "</TD>";
    echo "<FORM METHOD='POST' ACTION='user_gender.php?'>";
      echo "<TD class='row1'>";
        //if ($genre == "M") echo "<IMG SRC='" . _FOLDER_IMAGES . "man.png' WIDTH='16' HEIGHT='16' ALT='" . $l_man . "' TITLE='" . $l_man . "' BORDER='0'></A> ";
        //if ($genre == "W") echo "<IMG SRC='" . _FOLDER_IMAGES . "woman.png' WIDTH='16' HEIGHT='16' ALT='" . $l_woman . "' TITLE='" . $l_woman . "' BORDER='0'></A> ";
        echo "<select name='genre'>";
          echo "<option value='' ";
          if ($genre == "") echo "SELECTED";
          echo ">";
          echo "</option>";
          //
          echo "<option value='M' ";
          if ($genre == "M") echo "SELECTED";
          echo ">" . $l_man;
          echo "</option>";
          //
          echo "<option value='W' ";
          if ($genre == "W") echo "SELECTED";
          echo ">" . $l_woman;
          echo "</option>";
        echo "</select>";
      echo "</TD>";
      echo "<TD class='row2'>";
          echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
          echo "<INPUT TYPE='hidden' name='from' value='user' />";
          echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "</TD>";
    echo "</FORM>";
  echo "</TR>";
}
else
{
  if ($genre != "")
  {
    echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_gender;
      echo "</TD>";
      echo "<TD class='row1' VALIGN='TOP'>";
        if ($genre == "M") echo "<IMG SRC='" . _FOLDER_IMAGES . "man.png' WIDTH='16' HEIGHT='16' ALT='" . $l_man . "' TITLE='" . $l_man . "' BORDER='0'></A> ";
        if ($genre == "W") echo "<IMG SRC='" . _FOLDER_IMAGES . "woman.png' WIDTH='16' HEIGHT='16' ALT='" . $l_woman . "' TITLE='" . $l_woman . "' BORDER='0'></A> ";
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
}
//
if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') )
{
  echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_level;
      echo "</TD>";
      echo "<FORM METHOD='POST' ACTION='user_level.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<select name='id_level'>";
        if ($c_nb_level == 0 ) $c_nb_level = 5;
        for($i=0; $i < $c_nb_level; $i++)
        {
          echo "<option value='" . $i . "' ";
          if ($i == $usr_level)
            echo "SELECTED";
          echo ">" . $c_level[$i];
          echo "</option>";
        }
        echo "</select>";
        echo " ";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
        //echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='hidden' name='from' value='user' />";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "</TD>";
        echo "</FORM>";
      echo "<TD class='row2'>";
      echo "</TD>";
  echo "</TR>";
}



//
if (_ROLES_TO_OVERRIDE_PERMISSIONS != '')
{
  echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_role;
      echo "</TD>";
      echo "<FORM METHOD='POST' ACTION='user_update_role.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<select name='id_role'>";
        $requete  = " select SQL_CACHE ROL_NAME, ID_ROLE ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
        $requete .= " WHERE ROL_DEFAULT = '' "; // on masque la valeur par défaut (c'est la ligne vide)
        $requete .= " ORDER BY ROL_NAME ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-A1f5]", $requete);
        if ( mysqli_num_rows($result) > 0 )
        {
          // Ligne vide (valeur par défaut) :
          echo "<option value='0' ";
          if ($usr_id_role <= 0) echo "SELECTED";
          echo " class='genmed'></option>";
          //
          while( list ($role, $id_role) = mysqli_fetch_row ($result) )
          {
            echo "<option value='" . $id_role . "' ";
            if ($id_role == $usr_id_role) echo "SELECTED";
            echo " class='genmed'>" . $role . "</option>";
          }
        }
        echo "</select>";
        echo " ";
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='hidden' name='from' value='user' />";
        echo "</TD>";
        echo "<TD class='row1' VALIGN='TOP' align='CENTER'>";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "</TD>";
        echo "</FORM>";
  echo "</TR>";
}



//
echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo ucfirst(trim($l_admin_users_order_creat));
    echo "</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $usr_datcreat;
    echo "</TD>";
    echo "<TD class='row2'>";
    echo "</TD>";
echo "</TR>";
//
echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo ucfirst(trim($l_admin_users_order_last));
    echo "&nbsp;</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $usr_datlast;
    echo "</TD>";
    echo "<TD class='row2'>";
    echo "</TD>";
echo "</TR>";


echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo ucfirst(trim($l_admin_users_order_last_activity));
    echo "&nbsp;</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      if ($usr_datactivity != '0000-00-00') echo $usr_datactivity;
    echo "</TD>";
    echo "<TD class='row2'>";
    echo "</TD>";
echo "</TR>";

if (_BACKUP_FILES != "")
{
  echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo ucfirst(trim($l_admin_options_backup_files));
      echo "&nbsp;</TD>";
      echo "<TD class='row1' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        if ($usr_date_backup != '0000-00-00') echo $usr_date_backup;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
  echo "</TR>";
}

if ( (_USER_NEED_PASSWORD != "") and ($usr_datpwd != '0000-00-00') )
{
  echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_password;
      echo "&nbsp;</TD>";
      echo "<TD class='row1' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $usr_datpwd;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
  echo "</TR>";
}
//
if (intval($time_shit) <> 0) 
{
  echo "<BR/>";
  echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_time_zone;
      echo "</TD>";
      echo "<TD class='row1' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        if ($time_shit < 0) 
          $t = "-"; 
        else
          $t = "+";
        $t .= intval(abs($time_shit) / 10);
        if ( (abs($time_shit / 10) - intval(abs($time_shit) / 10)) <> 0 )
          $t .= ":30";
        else
          $t .= ":00";
        echo $t;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
  echo "</TR>";
}
//
if ($version != "")
{
  echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_users_col_version;
    echo "</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'> &nbsp;";
      echo $version;
    echo "</TD>";
    echo "<TD class='row2'>";
    echo "</TD>";
  echo "</TR>";
}
//
if ( ($win_os != '') and ($win_os != '0') )
{
  echo "<TR>";
      echo "<TD class='row2'>"; //  VALIGN='TOP'
        echo "<font face='verdana' size='2'>&nbsp;";
        echo "OS";
      echo "</TD>";
      echo "<TD class='row1' VALIGN='BOTTOM'>&nbsp;";
        display_os_picture($win_os);
        if ($pc_banned == true) // b_disalow.png
          echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <A HREF='list_ban.php?ban=pc&lang=" . $lang . "&'><IMG SRC='" . _FOLDER_IMAGES . "ko.gif' ALT='" . $l_admin_users_pc_banned . " : " . $usr_check ."' TITLE='" . $l_admin_users_pc_banned . " : " . $usr_check ."' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
      echo "</TD>";

      echo "<TD class='row2' align='center'>";
      echo "</TD>";
      /*
      if ( ($pc_banned == true) or ($usr_check == "") or ($usr_check == "WAIT") )
      {
        echo "<TD class='row2' align='center'>";
        echo "</TD>";
      }
      else
      {  
        echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
        echo "<TD class='row1' VALIGN='TOP' align='CENTER'>";
          echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_disalow.png' VALUE = '" . $l_admin_users_ban_pc . "' ALT='" . $l_admin_users_ban_pc . "' TITLE='" . $l_admin_users_ban_pc . "' WIDTH='16' HEIGHT='16' />";
          echo "<input type='hidden' name='id_user' value = " . $id_user . " />";
          echo "<input type='hidden' name='ban_value' value = '" . $usr_check . "' />";
          echo "<input type='hidden' name='ban_type' value = 'P' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        echo "</FORM>";
      }
      */
  echo "</TR>";
}
//
if ($ip != '')
{
  echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_session_col_ip;
    echo "</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      if ($hide_ip == '')
      { 
        if ($ip_banned == true) echo "<font color='red'><A HREF='list_ban.php?ban=ip&lang=" . $lang . "&'><IMG SRC='" . _FOLDER_IMAGES . "ko.gif' ALT='" . $l_admin_users_ip_banned . "' TITLE='" . $l_admin_users_ip_banned . "' WIDTH='16' HEIGHT='16' BORDER='0' /></A>&nbsp;";
        echo $ip;
      }
      else
        echo "<font color='gray'><I>Not in demo version </I></font>";
    echo "</TD>";
    
    if ($ip_banned == true)
    {
      echo "<TD class='row2'>";
      echo "</TD>";
    }
    else
    {
      echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
      echo "<TD class='row1' VALIGN='TOP' align='CENTER'>";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_ban.png' VALUE = '" . $l_admin_users_ban_ip . "' ALT='" . $l_admin_users_ban_ip . "' TITLE='" . $l_admin_users_ban_ip . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_user' value = " . $id_user . " />";
        echo "<input type='hidden' name='ban_value' value = '" . $ip . "' />";
        echo "<input type='hidden' name='ban_type' value = 'I' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
    }
    
    
  echo "</TR>";
}
//
if ( ($display_flag_country != "") and ($country_code != "") )
{
  echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_country;
      echo "</TD>";
      echo "<TD class='row1' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
          if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            echo "<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'> ";
            echo $country_name;
          }
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
  echo "</TR>";
}
//
if ( (_ENTERPRISE_SERVER != "")  and ($action == "") )
{
  if ($mac_adr <> "")
  {
    echo "<TR>";
      echo "<TD class='row2' VALIGN='TOP'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_mac_adr;
      echo "</TD>";
      echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
          echo $mac_adr;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
  if ($computername <> "")
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_pc;
      echo "</TD>";
      echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
          echo $computername;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
  if ($screen_size <> "")
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_screen;
      echo "</TD>";
      echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $screen_size;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
  if ($mailclient <> "")
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_emailclient;
      echo "&nbsp;</TD>";
      echo "<TD class='row1'>";
        echo "<font face='verdana' size='1'>&nbsp;";
        echo f_reduce_emailclient_name($mailclient);
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
  if ($browser <> "")
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_browser;
      echo "&nbsp;</TD>";
      echo "<TD class='row1'>";
        display_browser_picture($browser);
        echo "<font face='verdana' size='1'>&nbsp;";
        echo f_reduce_browser_name($browser);
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
  if ($ooo <> "")
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_admin_users_col_ooo;
      echo "</TD>";
      echo "<TD class='row1'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $ooo;
      echo "</TD>";
      echo "<TD class='row2'>";
      echo "</TD>";
    echo "</TR>";
  }
}
//
if ( (_ALLOW_CONTACT_RATING != "") and (intval($usr_rating) > 0) )
{
  echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'> &nbsp;";
      echo $l_admin_users_reputation . " (1-5)"; // l_admin_contact_average_1
    echo "</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      display_image_rating($usr_rating);
      //echo $usr_rating;
    echo "</TD>";
    echo "<TD class='row2'>";
    echo "</TD>";
  echo "</TR>";
}
//
if (intval($nb_connect) > 0)
{
  echo "<TR>";
    $moy = 0;
    echo "<TD class='row2'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_users_nb_connect;
    echo "&nbsp;</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
        echo $nb_connect . "&nbsp;";
        //if ( ($nbmax_days > 1) and ($nb_connect > 4) )
        if ( (_ENTERPRISE_SERVER == "") and ($nb_connect > 4) and ($nbmax_days > 10) )
        {
          $moy = round($nb_connect / $nbmax_days * 100, 1);
          echo "(" . $moy . "%)&nbsp;";
          
        }
    echo "</TD>";
    echo "<TD class='row2'>";
      if ($moy > 0) display_image_percent($moy, $l_admin_users_participation);
    echo "</TD>";
  echo "</TR>";
  $moy = 0; // pour le suivant
}
//
echo "<TR>";
    echo "<TD class='row2' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo ucfirst(trim($l_admin_users_order_state));
    echo "</TD>";
    echo "<TD class='row1' VALIGN='TOP'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      //if ($usr_status == 1) $usr_check =
      if ($usr_status == 2) $usr_check = "WAIT";
      if ($usr_status == 3) $usr_check = "";
      if ($usr_status == 4) $usr_check = "LOCK";
      if ($usr_status == 9) $usr_check = "LEAVE";
      switch ($usr_check)
      {
        case "WAIT" : // 2
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "wait.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_wait_valid . "' TITLE='" . $l_admin_users_info_wait_valid . "'> ";
          echo "<font color='blue'>" . $l_admin_users_info_wait_valid;
          break;
        case "" :  // 3
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_users_info_change_ok . "' TITLE='" . $l_admin_users_info_change_ok . "'>" ;
          echo "<font color='#DDDD00'>" . $l_admin_users_info_change_ok;
          break;
        case "LOCK" : // 4
          echo "<font color='blue'>" . $l_admin_users_info_locked;
          break;
        case "LEAVE" : // 9
          echo "<font color='#DDDD00'>" . $l_admin_users_info_leave;
          break;
        default :  // 1
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_valid . "' TITLE='" . $l_admin_users_info_valid . "'> ";
          echo "<font color='green'>" . $l_admin_users_info_valid;
          break;
      }
    echo "</TD>";
    echo "<TD class='row2'>";
    echo "</TD>";
echo "</TR>";
//
if ( (_SEND_ADMIN_ALERT != "") and ($action == "") )
{
  echo "<TR>";
    echo "<TD class='row2'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_admin_users_admin;
      if (intval($get_admin_alert) == 1) echo " <IMG SRC='" . _FOLDER_IMAGES . "b_admin.png' ALIGN='BASELINE' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_admin_alert . "' TITLE='" . $l_admin_users_admin_alert . "'>";
    echo "</TD>";
      echo "<FORM METHOD='POST' ACTION='user_update_admin_alert.php?'>";
      echo "<TD class='row1' VALIGN='TOP'>";
        echo "<input type='radio' name='get_alert' value='1' ";
        if (intval($get_admin_alert) == 1) echo "checked='yes'";
        echo " class='' />";
        echo "<font face='verdana' size='2'>";
        if (intval($get_admin_alert) == 1) echo "<font color='blue'>";
        echo $l_admin_users_admin_alert;
        if (intval($get_admin_alert) == 1) echo "</font>";
        echo "<BR/>";
        echo "<input type='radio' name='get_alert' value='2' ";
        if (intval($get_admin_alert) <> 1) echo "checked='yes'";
        echo " class='' />";
        echo "<font face='verdana' size='2'>";
        echo $l_admin_users_not_admin;
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        //echo " <INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "</TD>";
    echo "<TD class='row1' align='center'>";
      //if (intval($get_admin_alert) == 1) echo "<IMG SRC='" . _FOLDER_IMAGES . "b_admin.png' ALIGN='BASELINE' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_admin_alert . "' TITLE='" . $l_admin_users_admin_alert . "'>";
        echo " <INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
    echo "</TD>";
    echo "</FORM>";
  echo "</TR>";
}
//

if ( (_SPECIAL_MODE_OPEN_COMMUNITY == "") and (_SPECIAL_MODE_GROUP_COMMUNITY == "") and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == "") )
{
  echo "<TR>";
    echo "<TD class='row2'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_hide;
    echo "</TD>";
      echo "<FORM METHOD='POST' ACTION='user_update_hide.php?'>";
      echo "<TD class='row1' VALIGN='TOP'>";
        echo "<input type='checkbox' name='masquer' value='1' ";
        if ($nom == 'HIDDEN') echo "checked='yes'";
        echo " class='' />";
        echo "<font face='verdana' size='2'>";
        if ($nom == 'HIDDEN') echo "<font color='red'>";
        echo $l_admin_users_hide_from_other;
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
    echo "<TD class='row1' align='center'>";
        echo " <INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
    echo "</TD>";
      echo "</FORM>";
  echo "</TR>";
}


if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
{
  echo "<TR>";
    echo "<TD class='row2'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_menu_list_group;
    echo "</TD>";
      echo "<TD class='row1' colspan='2'>";
        echo "<font face='verdana' size='2'>";
        //
        $requete  = " SELECT GRP.GRP_NAME, GRP.ID_GROUP ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USG_USERGRP USG, " . $PREFIX_IM_TABLE . "GRP_GROUP GRP ";
        $requete .= " WHERE USG.ID_GROUP = GRP.ID_GROUP ";
        $requete .= " AND USG.ID_USER = " . $id_user . " ";
        $requete .= " ORDER BY UPPER(GRP_NAME) ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-A1f14]", $requete);
        if ( mysqli_num_rows($result) > 0 )
        {
          $nb = 0;
          while( list ($grp, $id_grp) = mysqli_fetch_row ($result) )
          {
            $nb++;
            if ( $nb % 3 == 0) echo "<br/>";
            echo "<A HREF='list_group_members.php?id_group=" . $id_grp . "&lang=" . $lang . "&' alt='" . $l_admin_group_members . "' title='" . $l_admin_group_members . "' >"; // class='cattitle'
            echo $grp . "</A> &nbsp;";
            //echo "<option value='" . $id_role . "' ";
          }
        }
        
      echo "</TD>";
  echo "</TR>";
}


//
//
mysqli_close($id_connect);
//
//




//
//
if ( ($action != "delete") and ($action != "wait") )
{
  echo "<TR>";
    echo "<TD class='row1' COLSPAN='4' VALIGN='BOTTOM' ALIGN='CENTER'>";
      
      //if ( ($usr_check == 'WAIT') or ($usr_status == 2) )
      //if ($usr_status == 2)
      if ( ($usr_status == 2) or ($usr_status == 4) )
      {
        echo "<A HREF='user_autorize.php?id_user=" . $id_user . "&lang=" . $lang . "&from=user&only_status=" . $only_status . "&' title='" . $l_admin_bt_allow . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_ok.png' ALT='" . $l_admin_bt_allow . "' TITLE='" . $l_admin_bt_allow . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }
      if ($avatar == "")
      {
        echo "<A HREF='avatar_changing.php?id_user_select=" . $id_user . "&lang=" . $lang . "&username=" . $username . "&avatar=" . $avatar . "&from=user&' title='" . $l_admin_contact_bt_avatar . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_avatar.png' ALT='" . $l_admin_contact_bt_avatar . "' TITLE='" . $l_admin_contact_bt_avatar . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }
      //if ( (($usr_check != '') and ($usr_check != 'WAIT')) or ($usr_status == 1) )
      if ($usr_status == 1)
      {
        echo "<A HREF='messagerie.php?id_user_select=" . $id_user . "&lang=" . $lang . "&from=user&' title='" . $l_admin_users_send_admin_message . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_send_msg.png' ALT='" . $l_admin_users_send_admin_message . "' TITLE='" . $l_admin_users_send_admin_message . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }
      //
      if (strlen($email) > 5)
      {
        echo "<A HREF='mailto:" . $email . "' title='" . $l_email . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_email.png' ALT='" . $l_email . "' TITLE='" . $l_email . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }
      //
      echo "<A HREF='list_contact.php?only_one=" . $id_user . "&lang=" . $lang . "' title='" . $l_admin_contact_title . "'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_details.png' ALT='" . $l_admin_contact_title . "' TITLE='" . $l_admin_contact_title . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      //
      if ( (_USER_NEED_PASSWORD !='') and ($passcr != '') )
      {
        echo "<A HREF='user_password_delete.php?id_user=" . $id_user . "&lang=" . $lang . "&from=user&only_status=" . $only_status . "&' title='" . $l_admin_bt_erase . " " . strtolower($l_admin_users_col_password) . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_password.png' ALT='" . $l_admin_bt_erase . " " . strtolower($l_admin_users_col_password) . "' TITLE='" . $l_admin_bt_erase . " " . strtolower($l_admin_users_col_password) . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }      
      //if ( ($usr_check != 'WAIT') or ($usr_status == 1) or ($usr_status == 3) )
      if ( ($usr_status == 1) or ($usr_status == 3) )
      {		
        //echo "<A HREF='user_wait.php?id_user=" . $id_user . "&lang=" . $lang . "&from=user&' title='" . $l_admin_bt_invalidate . "'>";
        echo "<A HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&action=wait&only_status=" . $only_status . "&' title='" . $l_admin_bt_invalidate . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_pause.png' ALT='" . $l_admin_bt_invalidate . "' TITLE='" . $l_admin_bt_invalidate . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }
      //if (  (_ENTERPRISE_SERVER != "") and ($is_session_open != "") and ( (($usr_check != '') and ($usr_check != 'WAIT')) or ($usr_status == 1) )  )
      if (  (_ENTERPRISE_SERVER != "") and ($is_session_open != "") and ($usr_status == 1) )
      {
        echo "<A HREF='messagerie.php?id_user_select=" . $id_user . "&action=stop&lang=" . $lang . "&' title='" . $l_admin_mess_stop_pc . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_stop_pc.png' ALT='" . $l_admin_mess_stop_pc . "' TITLE='" . $l_admin_mess_stop_pc . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      }
      //
      //if ( ($pc_banned == false) and ( (($usr_check != '') and ($usr_check != 'WAIT')) or ($usr_status == 1) ) )
      if ( ($pc_banned == false) and  ($usr_status == 1) )
      {
        echo "<A HREF='ban_add.php?id_user=" . $id_user . "&ban_value=" . $usr_check . "&lang=" . $lang . "&ban_type=P&only_status=" . $only_status . "&' title='" . $l_admin_users_ban_pc . "'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_pc_ban.png' ALT='" . $l_admin_users_ban_pc . "' TITLE='" . $l_admin_users_ban_pc . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
        /*
        echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
          echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "bt_user_pc_ban.png' VALUE = '" . $l_admin_users_ban_pc . "' ALT='" . $l_admin_users_ban_pc . "' TITLE='" . $l_admin_users_ban_pc . "' WIDTH='35' HEIGHT='35' />";
          echo "<input type='hidden' name='id_user' value = " . $id_user . " />";
          echo "<input type='hidden' name='ban_value' value = '" . $usr_check . "' />";
          echo "<input type='hidden' name='ban_type' value = 'P' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</FORM>"; */
      }
      //echo "<A HREF='user_delete.php?id_user=" . $id_user . "&lang=" . $lang . "' title='" . $l_admin_bt_delete . "'>";
      echo "<A HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&action=delete&only_status=" . $only_status . "&' title='" . $l_admin_bt_delete . "'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_eject.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='32' HEIGHT='32' BORDER='0'></A>";
      //
    echo "</TD>";
  echo "</TR>";
}
//
echo "</TABLE>";
echo "<BR/>";









if (_SHARE_FILES != "")
{
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead' >";
    echo "<font face='verdana' size='2'><b>&nbsp;";
    echo $l_admin_options_share_files_title . "&nbsp;</b></font></TH>";
    echo "</TH>";
  echo "</TR>";
  //
  if (intval($share_file_nb) > 0)
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo "<A HREF='list_files_sharing.php?lang=" . $lang . "&id_user_only=" . $id_user . "&' target='_blank'>";
        echo $l_admin_share_files_title;
        echo "</A>";
      echo "</TD>";
      echo "<TD class='row1' align='center' width='60'>";
        echo "<font face='verdana' size='2'>";
        echo $share_file_nb;
      echo "</TD>";
    echo "</TR>";
  }
  if (intval($nb_file_download) > 0)
  {
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_index_share_file_download;
      echo "</TD>";
      echo "<TD class='row1' align='center' width='60'>";
        echo "<font face='verdana' size='2'>";
        echo $nb_file_download;
      echo "</TD>";
    echo "</TR>";
  }
  
  if (_SHARE_FILES_VOTE != "")
  {
    if ($share_file_nb_give_votes_p > 0)
    {
      echo "<TR>";
        echo "<TD class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          echo $l_index_shoutbox_nb_votes; // . " : " . $l_admin_sent;
          echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " +' TITLE='" . $l_index_shoutbox_nb_votes . " +'>";
          echo " " . $l_admin_sent;
        echo "</TD>";
        echo "<TD class='row1' align='center'>";
          echo "<font face='verdana' size='2' color='green'>";
          echo $share_file_nb_give_votes_p;
        echo "</TD>";
      echo "</TR>";
    }
    if ($share_file_nb_give_votes_c > 0)
    {
      echo "<TR>";
        echo "<TD class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          echo $l_index_shoutbox_nb_votes; // . " : " . $l_admin_sent;
          echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " -' TITLE='" . $l_index_shoutbox_nb_votes . " -'>";
          echo " " . $l_admin_sent;
        echo "</TD>";
        echo "<TD class='row1' align='center'>";
          echo "<font face='verdana' size='2' color='red'>";
          echo $share_file_nb_give_votes_c;
        echo "</TD>";
      echo "</TR>";
    }
  }
  //
  echo "<TR>";
    echo "<TD class='row2'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      echo $l_index_files_workspace;
      if (_SHARE_FILES_MAX_SPACE_SIZE_USER > 0)
      {
        $t_percent = ceil(($file_share_size_mo / _SHARE_FILES_MAX_SPACE_SIZE_USER) * 100);
        echo "<br/>&nbsp;";
        echo "<IMG SRC='" . _FOLDER_IMAGES . f_img_percent($t_percent) . "' ALT='" . $t_percent . "%' TITLE='" . $t_percent . "%' WIDTH='95%' HEIGHT='13' BORDER='0' />";
      }
    echo "</TD>";
    echo "<TD class='row1' width='60' align='center'>";
      echo "<font face='verdana' size='2'>";
      echo $file_share_size_mo;
    echo "</TD>";
  echo "</TR>";
  //
  echo "</TABLE>";
}



if (_BACKUP_FILES != "")
{
  if (intval($nb_file_backup) > 0)
  {
    echo "<BR/>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
    echo "<TR>";
      echo "<TH align=center COLSPAN='2' class='thHead' >";
      echo "<font face='verdana' size='2'><b>&nbsp;";
      //echo $l_admin_options_backup_files . "&nbsp;</b></font></TH>";
      echo $l_admin_options_backup_files_title . "&nbsp;</b></font></TH>";
      echo "</TH>";
    echo "</TR>";
  //
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_index_backup_file;
      echo "</TD>";
      echo "<TD class='row1' align='center' width='60'>";
        echo "<font face='verdana' size='2'>";
        echo $nb_file_backup;
      echo "</TD>";
    echo "</TR>";
    //
    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo $l_index_files_workspace;
        if (_BACKUP_FILES_MAX_SPACE_SIZE_USER > 0)
        {
          $t_percent = ceil(($file_backup_size_mo / _BACKUP_FILES_MAX_SPACE_SIZE_USER) * 100);
          echo "<br/>&nbsp;";
          echo "<IMG SRC='" . _FOLDER_IMAGES . f_img_percent($t_percent) . "' ALT='" . $t_percent . "%' TITLE='" . $t_percent . "%' WIDTH='95%' HEIGHT='13' BORDER='0' />";
        }
      echo "</TD>";
      echo "<TD class='row1' align='center'>";
        echo "<font face='verdana' size='2'>";
        echo $file_backup_size_mo;
      echo "</TD>";
    echo "</TR>";
  }
  //
  echo "</TABLE>";
}






if (_SHOUTBOX != "")
{
  if ( ($sbx_nb_msg_ok > 0) or ($sbx_nb_msg_ko > 0) )
  {
    echo "<BR/>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
    echo "<TR>";
      echo "<TH align=center COLSPAN='2' class='thHead' >";
      echo "<font face='verdana' size='2'><b>&nbsp;";
      echo $l_admin_options_shoutbox_title_long . "&nbsp;</b></font></TH>";
      echo "</TH>";
    echo "</TR>";

    echo "<TR>";
      echo "<TD class='row2'>";
        echo "<font face='verdana' size='2'>&nbsp;";
        echo "<A HREF='list_shoutbox.php?lang=" . $lang . "&id_user=" . $id_user . "&' target='_blank'>";
        echo $l_admin_stats_col_nb_msg;
        echo "</A>";
      echo "</TD>";
      echo "<TD class='row1' align='center' width='60'>";
        echo "<font face='verdana' size='2' color='green'>";
        echo $sbx_nb_msg_ok;
      echo "</TD>";
    echo "</TR>";

    if ( (_SHOUTBOX_NEED_APPROVAL != "") or ($sbx_nb_msg_ko > 0) )
    {
      echo "<TR>";
        echo "<TD class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          echo $l_index_shoutbox_nb_msg_rejects . "&nbsp;";
        echo "</TD>";
        echo "<TD class='row1' align='center'>";
          echo "<font face='verdana' size='2'>";
          if ($sbx_nb_msg_ko > 0) echo "<font color='red'>";
          echo $sbx_nb_msg_ko;
        echo "</TD>";
      echo "</TR>";
    }

    if (_SHOUTBOX_VOTE != "")
    {
      if ($sbx_nb_votes_p > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_shoutbox_nb_votes; //  . " : " . $l_admin_received;
            echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " +' TITLE='" . $l_index_shoutbox_nb_votes . " +'>";
            echo " " . $l_admin_received;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='green'>";
            echo $sbx_nb_votes_p;
          echo "</TD>";
        echo "</TR>";
      }
      if ($sbx_nb_votes_c > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_shoutbox_nb_votes; // . " : " . $l_admin_received;
            echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " -' TITLE='" . $l_index_shoutbox_nb_votes . " -'>";
            echo " " . $l_admin_received;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='red'>";
            echo $sbx_nb_votes_c;
          echo "</TD>";
        echo "</TR>";
      }

      if ($sbx_nb_give_votes_p > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_shoutbox_nb_votes; // . " : " . $l_admin_sent;
            echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " +' TITLE='" . $l_index_shoutbox_nb_votes . " +'>";
            echo " " . $l_admin_sent;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='green'>";
            echo $sbx_nb_give_votes_p;
          echo "</TD>";
        echo "</TR>";
      }
      if ($sbx_nb_give_votes_c > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_shoutbox_nb_votes; // . " : " . $l_admin_sent;
            echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " -' TITLE='" . $l_index_shoutbox_nb_votes . " -'>";
            echo " " . $l_admin_sent;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='red'>";
            echo $sbx_nb_give_votes_c;
          echo "</TD>";
        echo "</TR>";
      }
      // Meilleurs score max (par message) en tout
      if ($sbx_nb_votes_max_tot_p > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_admin_users_rating; // . " : " . $l_admin_sent;
            echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " +' TITLE='" . $l_index_shoutbox_nb_votes . " +'>";
            //echo " " . $l_admin_sent;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='green'>";
            echo $sbx_nb_votes_max_tot_p;
          echo "</TD>";
        echo "</TR>";
      }
      if ($sbx_nb_votes_max_tot_c > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_admin_users_rating; // . " : " . $l_admin_sent;
            echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " -' TITLE='" . $l_index_shoutbox_nb_votes . " -'>";
            //echo " " . $l_admin_sent;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='red'>";
            echo $sbx_nb_votes_max_tot_c;
          echo "</TD>";
        echo "</TR>";
      }
    }
  //
  echo "</TABLE>";
  }
}
//
echo "<BR/>";
//
display_menu_footer();
//
echo "</body></html>";
?>
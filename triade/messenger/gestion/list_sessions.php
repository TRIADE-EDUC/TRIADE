<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2019 THeUDS           **
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
if (isset($_COOKIE['im_session_list_col_os'])) $option_show_col_os = $_COOKIE['im_session_list_col_os'];  else  $option_show_col_os = '0';
if (isset($_COOKIE['im_session_list_col_time'])) $option_show_col_time = $_COOKIE['im_session_list_col_time'];  else  $option_show_col_time = '1';
if (isset($_COOKIE['im_session_list_col_role'])) $option_show_col_role = $_COOKIE['im_session_list_col_role'];  else  $option_show_col_role = '0';
if (isset($_COOKIE['im_session_list_col_begin'])) $option_show_col_begin = $_COOKIE['im_session_list_col_begin'];  else  $option_show_col_begin = '1';
if (isset($_COOKIE['im_session_list_col_level'])) $option_show_col_level = $_COOKIE['im_session_list_col_level'];  else  $option_show_col_level = '?';
if (isset($_COOKIE['im_session_list_col_create'])) $option_show_col_create = $_COOKIE['im_session_list_col_create'];  else  $option_show_col_create = '1';
if (isset($_COOKIE['im_session_list_col_rating'])) $option_show_col_rating = $_COOKIE['im_session_list_col_rating'];  else  $option_show_col_rating = '0';
if (isset($_COOKIE['im_session_list_col_reason'])) $option_show_col_reason = $_COOKIE['im_session_list_col_reason'];  else  $option_show_col_reason = '0';
if (isset($_COOKIE['im_session_list_col_version'])) $option_show_col_version = $_COOKIE['im_session_list_col_version'];  else  $option_show_col_version = '0';
if (isset($_COOKIE['im_session_list_col_last_time'])) $option_show_col_last_time = $_COOKIE['im_session_list_col_last_time'];  else  $option_show_col_last_time = '0';
if (isset($_COOKIE['im_session_list_col_language'])) $option_show_col_language = $_COOKIE['im_session_list_col_language'];  else  $option_show_col_language = '0';
if (isset($_COOKIE['im_session_list_col_password'])) $option_show_col_password = $_COOKIE['im_session_list_col_password'];  else  $option_show_col_password = '0';
if (isset($_COOKIE['im_session_list_col_activity'])) $option_show_col_activity = $_COOKIE['im_session_list_col_activity'];  else  $option_show_col_activity = '0';
if (isset($_COOKIE['im_session_list_col_ip_address'])) $option_show_col_ip_address = $_COOKIE['im_session_list_col_ip_address'];  else  $option_show_col_ip_address = '1';
if (isset($_COOKIE['im_session_list_col_name_function'])) $option_show_col_name_function = $_COOKIE['im_session_list_col_name_function'];  else  $option_show_col_name_function = '0';
if (isset($_COOKIE['im_session_list_show_select_cols'])) $im_session_list_show_select_cols = $_COOKIE['im_session_list_show_select_cols'];  else  $im_session_list_show_select_cols = '1';
if (isset($_COOKIE['im_session_list_show_legende'])) $im_session_list_show_legende = $_COOKIE['im_session_list_show_legende'];  else  $im_session_list_show_legende = '1';
//
if (intval($option_show_col_os) <= 0) $option_show_col_os = "";
if (intval($option_show_col_time) <= 0) $option_show_col_time = "";
if (intval($option_show_col_begin) <= 0) $option_show_col_begin = "";
if (intval($option_show_col_role) <= 0) $option_show_col_role = "";
if (intval($option_show_col_create) <= 0) $option_show_col_create = "";
if (intval($option_show_col_rating) <= 0) $option_show_col_rating = "";
if (intval($option_show_col_reason) <= 0) $option_show_col_reason = "";
if (intval($option_show_col_version) <= 0) $option_show_col_version = "";
if (intval($option_show_col_last_time) <= 0) $option_show_col_last_time = "";
if (intval($option_show_col_language) <= 0) $option_show_col_language = "";
if (intval($option_show_col_password) <= 0) $option_show_col_password = "";
if (intval($option_show_col_activity) <= 0) $option_show_col_activity = "";
if (intval($option_show_col_ip_address) <= 0) $option_show_col_ip_address = "";
if (intval($option_show_col_name_function) <= 0) $option_show_col_name_function = "";
if (intval($im_session_list_show_select_cols) <= 0) $im_session_list_show_select_cols = "";
if (intval($im_session_list_show_legende) <= 0) $im_session_list_show_legende = "";
if ( (intval($option_show_col_level) <= 0) and ($option_show_col_level != "?") ) $option_show_col_level = "";
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_session_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="20;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
//
require ("../common/sessions.inc.php");
clean_inactives_session();
//
if (_ALLOW_COL_FUNCTION_NAME == "")   $option_show_col_name_function = "";
if (_ALLOW_CONTACT_RATING == "")   $option_show_col_rating = "";
if ( (_ONLINE_REASONS_LIST == '') and (_BUSY_REASONS_LIST == '') and (_DONOTDISTURB_REASONS_LIST == '') and (_AWAY_REASONS_LIST == '') ) $option_show_col_reason = "";
if (_ROLES_TO_OVERRIDE_PERMISSIONS == "") $option_show_col_role = "";
if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') ) 
{
  if ($option_show_col_level == "?") $option_show_col_level = "1";
}
else
  $option_show_col_level = "";
//
$display_flag = "";
if ( (_FLAG_COUNTRY_FROM_IP != "") or ($option_show_col_language != "") )
{
	if (is_readable("../common/library/geoip/geoip_2.inc.php"))
	{
		require("../common/library/geoip/geoip_2.inc.php");
		$display_flag = "X";
  }
}
//
if ($l_time_short_format_display == '') $l_time_short_format_display = $l_time_format_display;
//
$repertoire  = getcwd() . "/"; 
$hide_ip = "";
if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) $hide_ip = "X";
//
if ($page == 'all')
  $nb_row_by_page = 1000;
else
{
  $nb_row_by_page = intval($nb_row_by_page);
  if ( ($nb_row_by_page < 15) or ($nb_row_by_page > 100) ) $nb_row_by_page = 15;
}
$page = intval($page);
if ($page < 1) $page = 1;
//
//
$requete = "select CURTIME() ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A2a]", $requete);
list ($heure_serveur) = mysqli_fetch_row ($result);
$heure_serveur = date($l_time_format_display, strtotime($heure_serveur));
//
//
//  | A | B | C |...
$alpha_link = "";
if ($tri == "")
{
	$requete  = " SELECT distinct(LEFT(UPPER(USR.USR_USERNAME), 1)) ";
	$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "SES_SESSION SES ";
	$requete .= " WHERE USR.ID_USER = SES.ID_USER ";
  switch ($only_status)
  {
    case "n" :
      //$requete .= " and SES.SES_STATUS = 0 ";
      $requete .= " and SES.SES_STATUS in (0, 8, 9) ";
      break;
    case "o" :
      $requete .= " and SES.SES_STATUS = 1 ";
      break;
    case "a" :
      $requete .= " and SES.SES_STATUS = 2 ";
      break;
    case "b" :
      $requete .= " and SES.SES_STATUS = 3 ";
      break;
    case "d" :
      $requete .= " and SES.SES_STATUS = 4 ";
      break;
  }
	$requete .= " order by LEFT(UPPER(USR_USERNAME), 1) ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-A2b]", $requete);
	if ( mysqli_num_rows($result) > 2 )
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
//
//
if ($option_show_col_role != "")
{
  $role_name = "";
  $liste_roles_orig = "";
  $requete  = " select SQL_CACHE ROL_NAME, ID_ROLE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
  $requete .= " WHERE ROL_DEFAULT = '' "; // on masque la valeur par défaut (c'est la ligne vide)
  $requete .= " ORDER BY ID_ROLE ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A2d]", $requete); 
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($role, $id_role) = mysqli_fetch_row ($result) )
    {
      $role_name[$id_role] = $role;
    }
  }
}
//
//
$nb_char_username = 0;
$nb_use_time_shit = "";
$requete  = " select USR_USERNAME, USR_TIME_SHIFT ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A2d]", $requete);
if ( mysqli_num_rows($result) > 0 )
{
  while( list ($username, $time_shit) = mysqli_fetch_row ($result) )
	{
    if (strlen($username) > $nb_char_username) $nb_char_username = strlen($username);
    if (intval($time_shit) <> 0) $nb_use_time_shit = "X";
	}
}
if ( ($nb_use_time_shit == "") or (_TIME_ZONES == "") ) $option_show_col_time = "";
//
echo "<font face='verdana' size='2'>";
// echo $alpha_link;  // non plus bas !
//
//
$requete  = " SELECT USR.USR_USERNAME, USR.USR_NICKNAME, USR.USR_NAME, SES.SES_STATUS, SES.SES_AWAY_REASON, SES_STARTDATE, SES_STARTTIME, SES_LASTTIME, SES.ID_SESSION, USR.ID_USER, ";
$requete .= " SES_LASTTIME-CURTIME(), SES_IP_ADDRESS, USR.USR_VERSION, USR.USR_LEVEL, USR.USR_COUNTRY_CODE, USR.USR_DATE_CREAT, ";
$requete .= " USR.USR_TIME_SHIFT, USR.USR_OS, USR.USR_LANGUAGE_CODE, USR_GENDER, USR_RATING, USR_GET_ADMIN_ALERT, USR_DATE_PASSWORD, USR_DATE_ACTIVITY, ID_ROLE, count( CNT.ID_USER_2) ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
$requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "CNT_CONTACT AS CNT ON ( CNT.ID_USER_1 = USR.ID_USER ) ";
$requete .= " WHERE USR.ID_USER = SES.ID_USER ";
switch ($only_status)
{
	case "n" :
		//$requete .= " and SES.SES_STATUS = 0 ";
		$requete .= " and SES.SES_STATUS in (0, 8, 9) ";
		break;
	case "o" :
		$requete .= " and SES.SES_STATUS = 1 ";
		break;
	case "a" :
		$requete .= " and SES.SES_STATUS = 2 ";
		break;
	case "b" :
		$requete .= " and SES.SES_STATUS = 3 ";
		break;
	case "d" :
		$requete .= " and SES.SES_STATUS = 4 ";
		break;
}
$requete .= " GROUP BY SES.ID_SESSION ";
switch ($tri)
{
	case "etat" :
		$requete .= " ORDER BY SES_STATUS, UPPER(USR_USERNAME) ";
		break;
	case "nom" :
		$requete .= " ORDER BY UPPER(USR_NAME), UPPER(USR_USERNAME) ";
		break;
	case "level" :
		$requete .= " ORDER BY USR_LEVEL, UPPER(USR_USERNAME) ";
		break;
	case "role" :
		$requete .= " ORDER BY ID_ROLE DESC, UPPER(USR_USERNAME) ";
		break;
	default :
		$requete .= " ORDER BY UPPER(USR_USERNAME) ";
		break;
}
//
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A2c]", $requete);
$nb_row = mysqli_num_rows($result);
if ( $nb_row > 30 )
  echo $alpha_link;
else
  $alpha_link = "";
//
//echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>"; // pour centrage en dessous du tableau (légende et choix colonnes)
//echo "<TR><TD>";
//
//echo "<BR/>";
// Page défilement :
echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>";
if ($nb_row_by_page > 50)
{
  echo "<TR><TD COLSPAN='2' ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "");
  echo "</TD></TR>";
}
echo "<TR><TD COLSPAN='2'>"; //

//
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<THEAD>";
echo "<TR>";
	echo "<TH align=center COLSPAN='12' class='thHead'>";
	$title = $l_admin_session_title_2 . " </B> &nbsp; <SMALL>(";
  switch ($only_status)
  {
    case "n" :
      $title .= $l_admin_session_info_not_connect . " - ";
      break;
    case "o" :
      $title .= $l_admin_session_info_online . " - ";
      break;
    case "a" :
      $title .= $l_admin_session_info_away . " - ";
      break;
    case "b" :
      $title .= $l_admin_session_info_busy . " - ";
      break;
    case "d" :
      $title .= $l_admin_session_info_do_not_disturb . " - ";
      break;
  }
	if ( $nb_row > 1 ) $title .= $nb_row . " ". $l_admin_session_at . " "; 
	$title .= $heure_serveur . ")";
	echo "<font face='verdana' size='3'><b>&nbsp; " . $title . "</SMALL>&nbsp;</b></font></TH>";
echo "</TR>";
if ( $nb_row > 0 )
{
	echo "<TR>";
    $link_state_col = "&nbsp;<A HREF='list_sessions.php?tri=etat&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_session_order_state . "' class='cattitle'>" . $l_admin_session_col_state . "</A>&nbsp;";
    display_row_table($link_state_col, '30');
    //
    if ($option_show_col_reason != '') display_row_table($l_admin_session_col_state_reason, '120');
    //
    $link_user_col = "&nbsp;<A HREF='list_sessions.php?tri=&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_session_order_user . "' class='cattitle'>" . $l_admin_session_col_user . "</A>&nbsp;";
		$larg_col = 160;
		if ($nb_char_username > 11) $larg_col = 180;
		if ($nb_char_username > 15) $larg_col = 210;
		if ($nb_char_username > 18) $larg_col = 230;
		//if ($display_flag <> '') $larg_col = ($larg_col + 20);
		//if ($option_show_col_time == '') $larg_col = ($larg_col + 10); // bidouille quand la colonne time shit est masqué, ca réduit col user et action (pourquoi ?) sous Firefox 2 (à tester avec le 3).
    display_row_table($link_user_col, $larg_col);
    //
		if ($option_show_col_name_function != '')
		{
      $link_function_col = "&nbsp;<A HREF='list_sessions.php?tri=nom&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_function . "'  class='cattitle'>" . $l_admin_session_col_function . "</A>&nbsp;";
      display_row_table($link_function_col, '120');
		}
		//
		//if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') )
		if ($option_show_col_level != "")
		{
      $link_level_col = "&nbsp;<A HREF='list_sessions.php?tri=level&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_level . "'  class='cattitle'>" . $l_admin_users_col_level . "</A>&nbsp;";
      display_row_table($link_level_col, '100');
		}
		//
		if ($option_show_col_role != "")
		{
      $link_role_col = "&nbsp;<A HREF='list_sessions.php?tri=role&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_role . "'  class='cattitle'>" . $l_admin_role . "</A>&nbsp;";
      display_row_table($link_role_col, '100');
		}
		//
		if ( ($option_show_col_language != '')  and ($display_flag != '') )  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' width='16' height='16' ALT='" . $l_language . "' TITLE='" . $l_language . "' >", '30');
		//
		if ($option_show_col_ip_address != '')
		{
      if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") )
        display_row_table($l_admin_session_col_ip, '160');
      else
        display_row_table($l_admin_session_col_ip, '130');
    }
		//
		if ($option_show_col_time != '') display_row_table($l_admin_session_col_time, '75');
    if ($option_show_col_begin != '') display_row_table($l_admin_session_col_begin, '80'); // peut aussi contenir la date
		if (substr_count($l_time_format_display, "A") > 0) 
		{
      if ($option_show_col_last_time != '') display_row_table($l_admin_session_col_last, '90');
    }
    else
    {
      if ($option_show_col_last_time != '') display_row_table($l_admin_session_col_last, '65');
    }
    if ($option_show_col_version != '') display_row_table($l_admin_session_col_version, '');
    if ($option_show_col_os != '') display_row_table("OS", '30');
    if ($option_show_col_activity != '') display_row_table($l_admin_users_col_activity, '85');
    if ($option_show_col_password != '') display_row_table("<SMALL>" . $l_admin_users_col_password . "</SMALL>", '');
		if ($option_show_col_create != '') display_row_table($l_admin_users_col_creat, '80');
    if ($option_show_col_rating != '') display_row_table($l_admin_users_reputation, '40');

	echo "</TR>";
	echo "</THEAD>";
  echo "<TFOOT>";
	if ( mysqli_num_rows($result) > 1 )
	{
    echo "<TR>";
    echo "<TD align='center' COLSPAN='12' class='catBottom'>";
    echo "<font face='verdana' size='2'>";
    echo $l_order_by . " ";
    if ($tri == 'etat') echo "<B>";
    echo "<A HREF='list_sessions.php?tri=etat&lang=" . $lang . "&'>" . $l_admin_session_order_state . "</A></B> - ";
    if ($tri == '') echo "<B>";
    echo "<A HREF='list_sessions.php?tri=&lang=" . $lang . "&'>" . $l_admin_session_order_user . "</A></B>";
    if ($option_show_col_name_function != '')
    {
      if ($tri == 'nom') echo "<B>";
      echo " - <A HREF='list_sessions.php?tri=nom&lang=" . $lang . "&'>" . $l_admin_users_order_function . "</A></B>";
    }
    //
    //if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') )
    if ($option_show_col_level != '')
    {
      if ($tri == 'level') echo "<B>";
      echo " - <A HREF='list_sessions.php?tri=level&lang=" . $lang . "&'>" . $l_admin_users_order_level . "</A></B>";
    }
    //
    if ($option_show_col_role != '')
    {
      if ($tri == 'role') echo "<B>";
      echo " - <A HREF='list_sessions.php?tri=role&lang=" . $lang . "&'>" . $l_admin_users_order_role . "</A></B>";
    }
    //
    echo "</TD>";
    echo "</TR>";
  }
  echo "</TFOOT>";
  echo "\n";
	echo "<TBODY>";
	//
	$last_first_letter = "";
	$nb_status_not_connect = 0;
	$nb_status_online = 0;
	$nb_status_away = 0;
	$nb_status_busy = 0;
	$nb_status_do_not_disturb = 0;
	$nb_status_tot_not_connect = 0;
	$nb_status_tot_online = 0;
	$nb_status_tot_away = 0;
	$nb_status_tot_busy = 0;
	$nb_status_tot_do_not_disturb = 0;
	$row_num = 0;
	$display_start = 0;
	$display_end = 0;
	$nb_page = 1;
  if ($nb_row > $nb_row_by_page)
  {
    $nb_page = ceil($nb_row / $nb_row_by_page);
    if ($page < 1) $page = 1;
    if ($page > $nb_page) $page = $nb_page;
    $display_start = ( ($page - 1) * $nb_row_by_page + 1);
    $display_end = ($display_start + $nb_row_by_page - 1);
    if ($display_end > $nb_row) $display_end = $nb_row;
  }
	while( list ($contact, $nickname, $nom, $eta_num, $status_reason, $startdate, $starttime, $lasttime, $id_ses, $id_user, $difftime, $ip, $version, $level, $country_code, $usr_datcreat, $use_time_shit, $win_os, $language_code, $usr_gender, $usr_rating, $usr_get_admin, $usr_date_password, $usr_dat_activity, $usr_id_role, $nb_contacts) = mysqli_fetch_row ($result) )
	{
    $row_num++;
    if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
    {
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $contact = $nickname;
      if ($nom == 'HIDDEN') $nom = '';
      $status_reason_color = "";
      $status_reason_list = "";
      //
      echo "<TR>";
      echo "<TD align='center' class='row1'>";
        //if  ($eta_num == 0)
        if  ( ($eta_num == 0) or ($eta_num == 8) or ($eta_num == 9) )
        {
          echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_session_info_not_connect . "' TITLE='" . $l_admin_session_info_not_connect . "'>";
          $nb_status_not_connect++;
          $nb_status_tot_not_connect++;
        }
        if  ($eta_num == 1)
        {
          echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_session_info_online . "' TITLE='" . $l_admin_session_info_online . "'>";
          $nb_status_online++;
          $nb_status_tot_online++;
          $status_reason_color = "green";
          $status_reason_list = _ONLINE_REASONS_LIST;
        }
        if  ($eta_num == 2)
        {
          echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_session_info_away . "' TITLE='" . $l_admin_session_info_away . "'>";
          $nb_status_away++;
          $nb_status_tot_away++;
          $status_reason_color = "#FFDA8C";
          $status_reason_list = _AWAY_REASONS_LIST;
        }
        if  ($eta_num == 3)
        {
          echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_orange.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_session_info_busy . "' TITLE='" . $l_admin_session_info_busy ."'>";
          $nb_status_busy++;
          $nb_status_tot_busy++;
          $status_reason_color = "#F2A100";
          $status_reason_list = _BUSY_REASONS_LIST;
        }
        if  ($eta_num == 4)
        {
          echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_red.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_session_info_do_not_disturb . "' TITLE='" . $l_admin_session_info_do_not_disturb . "'>";
          $nb_status_do_not_disturb++;
          $nb_status_tot_do_not_disturb++;
          $status_reason_color = "red";
          $status_reason_list = _DONOTDISTURB_REASONS_LIST;
        }
      echo "</TD>";
      //
      if ($option_show_col_reason != '')
      {
        echo "<TD align='left' class='row1'>";
          $status_reason = intval($status_reason);
          if ( ($status_reason > 0) and ($status_reason_list != "") ) 
          {
            echo "<font face='verdana' size='1' color='" . $status_reason_color . "'>&nbsp;";
            $t = explode(";", $status_reason_list);
            if ($status_reason <= sizeof($t))
              echo $t[($status_reason - 1)];
            else
              echo "&nbsp;";
          }
          else
            echo "&nbsp;";
        echo "</TD>";
      }
      //
      echo "<TD class='row1'>";
        $plus = "";
        if ($tri == "")
        {
          $t = strtoupper(substr($contact, 0, 1));
          if ($t != $last_first_letter)
          {
            $last_first_letter = $t;
            $plus = "ID=" . $t;
          }
        }
        //
        if ($usr_gender == "M") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "man.png' WIDTH='16' HEIGHT='16' ALT='" . $l_man . "' TITLE='" . $l_man . "' BORDER='0'></A>";
        if ($usr_gender == "W") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "woman.png' WIDTH='16' HEIGHT='16' ALT='" . $l_woman . "' TITLE='" . $l_woman . "' BORDER='0'></A>";
        //
        if  ($eta_num == 1) // mettre en gras si "online".
          echo "<B>";
        //
        echo "<font face='verdana' size='2'>";
        //echo "&nbsp;<A " . $plus . " HREF='messagerie.php?id_user_select=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
        echo "&nbsp;<A " . $plus . " HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
        echo $contact . "</A>";
        if (intval($nb_contacts) > 0) echo "</B><SMALL> (<acronym title='" . $l_admin_contacts . " : " . $nb_contacts . "'>" . $nb_contacts . "</acronym>)</SMALL>";
        if ($usr_get_admin) echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "b_admin.png' ALIGN='BASELINE' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_admin_alert . "' TITLE='" . $l_admin_users_admin_alert . "'>";
        echo "&nbsp;</font>";
        echo "</B>";
      echo "</TD>";
      echo "\n";
      if ($option_show_col_name_function != '')
      {
        echo "<TD class='row1'>";
          if ($nom == '') $nom = "&nbsp;";
            echo "<font face='verdana' size='2'>&nbsp;" . $nom . "&nbsp;</font>";
        echo "</TD>";
      }
      //
      //if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') )
      if ($option_show_col_level != "")
      {
        echo "<TD class='row1'>";
          //if ($level == '') or ($level
            //$level = "&nbsp;";
          $level_display = $c_level[$level];
          echo "<font face='verdana' size='2'>&nbsp;" . $level_display . "&nbsp;</font>";
        echo "</TD>";
      }
      //
      if ($option_show_col_role != "")
      {
        echo "<TD class='row1'>";
          $role_display = "";
          $usr_id_role = intval($usr_id_role);
          if ($usr_id_role > 0) $role_display = $role_name[$usr_id_role];
          echo "<font face='verdana' size='2'>&nbsp;" . $role_display . "&nbsp;</font>";
        echo "</TD>";
      }
      //
      if ( ($option_show_col_language != '') and ($display_flag != '') )
      {
        echo "<TD align='center' class='row1'>";
        if ($language_code != '')
        {
          if (is_readable("../images/flags/" . strtolower($language_code) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$language_code];
            $country_name = f_quote($GEOIP_COUNTRY_NAMES[$country_id]);
            $country_name = f_language_of_country($language_code, $country_name);
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($language_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_language . " : " . $country_name . "' TITLE='" . $l_language . " : " . $country_name . "'>&nbsp;";
          }
        }
        else
          echo "&nbsp;";
        echo "</TD>";
      }
      //
      if ($option_show_col_ip_address != '')
      {
        if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") and ($country_code != "") )
        {
          echo "<TD align='left' class='row2'>";
          if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
            $country_name = f_quote($GEOIP_COUNTRY_NAMES[$country_id]);
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
          }
        }
        else
          echo "<TD align='center' class='row2'>";
          //
        
        if ($hide_ip == '') 
        {
          //echo "<font face='verdana' size='2'>" . $ip;
          echo "<font face='verdana' size='2'>";
          if ($ip == "127.0.0.1") echo "<font color='red'>";
          echo $ip;
        }
        else
          echo "<font face='verdana' size='1' color='gray'><I>Not in demo version </I></font>";
        echo "</font></TD>";
      }
      //
      if ($option_show_col_time != '') 
      {
        echo "<TD align='center' class='row2'>";
          if ($use_time_shit <> 0)
          {
            echo "<font face='verdana' size='2'>" . f_user_local_time($use_time_shit) . "</font>";
          }
          else
            echo "&nbsp;";
        echo "</TD>";
      }
      //
      if ($option_show_col_begin != '') 
      {
        echo "<TD align='center' class='row2'>";
          echo "<font face='verdana' size='2'>";
          if ($startdate != '0000-00-00') $startdate = date($l_date_format_display, strtotime($startdate));
          if ( $startdate != date($l_date_format_display) )
            echo "<font color='gray'>" . $startdate . "</font>";
          else
          {
            $starttime = date($l_time_short_format_display, strtotime($starttime));
            echo $starttime;
          }
          echo "</font>";
        echo "</TD>";
      }
      //
      if ($option_show_col_last_time != '') 
      {
        echo "<TD align='center' class='row2'>";
          $lasttime = date($l_time_format_display, strtotime($lasttime));
          echo "<font face='verdana' size='2'>" . $lasttime . "</font>";
        echo "</TD>";
      }
      //
      if ($option_show_col_version != '')
      {
        echo "<TD align='center' class='row2'>";
          if ($version != '')
          {
            echo "<font face='verdana' size='2'>";
            color_num_version($version);
          }
          else
            echo "&nbsp;";
        echo "</TD>";
        echo "</font>";
      }
      //
      if ($option_show_col_os != '')
      {
        echo "<TD align='center' class='row2'>";
          display_os_picture($win_os);
        echo "</TD>";
      }
      //
      if ($option_show_col_activity != '') 
      {
        echo "<TD align='center' class='row2'>";
          if ($usr_dat_activity == '0000-00-00')
            $usr_dat_activity = 	'&nbsp;';
          else
            $usr_dat_activity = date($l_date_format_display, strtotime($usr_dat_activity));
          //
          echo "<font face='verdana' size='2'>";
          if ( $usr_dat_activity != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $usr_dat_activity . "</font>";
        echo "</TD>";
      }
      //
      if ($option_show_col_password != '') 
      {
        echo "<TD align='center' class='row2'>";
          if ($usr_date_password == '0000-00-00')
            $usr_date_password = 	'&nbsp;';
          else
            $usr_date_password = date($l_date_format_display, strtotime($usr_date_password));
          //
          echo "<font face='verdana' size='2'>";
          if ( $usr_date_password != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $usr_date_password . "</font>";
        echo "</TD>";
      }
      //
      if ($option_show_col_create != '') 
      {
        echo "<TD align='center' class='row2'>";
          if ($usr_datcreat == '0000-00-00')
            $datcreat = 	'&nbsp;';
          else
            $datcreat = date($l_date_format_display, strtotime($usr_datcreat));
          //
          echo "<font face='verdana' size='2'>";
          if ( $datcreat != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $datcreat . "</font>";
        echo "</TD>";
      }
      //
      if ($option_show_col_rating != '')
      {
        echo "<TD align='center' class='row2'>";
          if ($usr_rating > 0) 
            display_image_rating($usr_rating);
          else
            echo "&nbsp;";
        echo "</TD>";
      }
      //
      echo "</TD>";
      echo "</TR>";
      echo "\n";
    }
    else
    {
      if ($eta_num == 0) $nb_status_tot_not_connect++;
      if ($eta_num == 1) $nb_status_tot_online++;
      if ($eta_num == 2) $nb_status_tot_away++;
      if ($eta_num == 3) $nb_status_tot_busy++;
      if ($eta_num == 4) $nb_status_tot_do_not_disturb++;
    }
	}
	echo "</TBODY>";
	echo "</TABLE>";
  /*
  if ( (strlen($alpha_link) > 3)  and (mysqli_num_rows($result) > 20) )
  {
    echo "<CENTER><font face='verdana' size='2'>";
    echo $alpha_link;
    echo "<BR/>";
  }
  */
  
  echo "</TD></TR>";
  echo "<TR><TD>";
  //if ($nb_row > $nb_row_by_page)
  if ( ($nb_row > 15) and ($nb_row_by_page < 1000) )
  {
    echo "<font face='verdana' size='2'>";
    echo $l_rows_per_page . " : ";
    display_nb_row_page(15, $nb_row_by_page, "list_sessions_nb_rows");
    echo " | ";
    display_nb_row_page(20, $nb_row_by_page, "list_sessions_nb_rows");
    echo " | ";
    display_nb_row_page(25, $nb_row_by_page, "list_sessions_nb_rows");
    echo " | ";
    display_nb_row_page(30, $nb_row_by_page, "list_sessions_nb_rows");
    echo " | ";
    display_nb_row_page(50, $nb_row_by_page, "list_sessions_nb_rows");
  }
  echo "</TD><TD ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "UP");
  echo "</TD></TR>";



  echo "<TR><TD></TD></TR>";
  echo "<TR><TD></TD></TR>";  // Espacement vertical
  
  
  echo "<TR><TD COLSPAN='2'>";

      echo "<TABLE WIDTH='100%' cellspacing='0' cellpadding='0' BORDER='0'>";
      echo "<TR><TD WITH='50%' VALIGN='TOP'>";
      
      
          echo "<table cellspacing='1' cellpadding='1' class='forumline'>"; // width='650' 
          echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
          echo "<TR>";
          echo "<TD class='catHead' align='center'>";
            echo "<FONT size='2'>&nbsp;<B>" . $l_display_col;

            if ($im_session_list_show_select_cols > 0)
            {
              echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_session_show_select_cols&im_session_list_show_select_cols=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_session_show_select_cols&im_session_list_show_select_cols=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }

          echo "</TD>";
          echo "</TR>";
          //
          if ($im_session_list_show_select_cols > 0)
          {
            echo "<TR>";
            echo "<td class='row1'>";
            echo "<FONT size='2'>";

            echo "<INPUT name='option_show_col_reason' id='option_show_col_reason' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ( (_ONLINE_REASONS_LIST == '') and (_BUSY_REASONS_LIST == '') and (_DONOTDISTURB_REASONS_LIST == '') and (_AWAY_REASONS_LIST == '') ) echo " disabled ";
            if ($option_show_col_reason <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_reason'>" . $l_admin_session_col_state_reason . "</label><BR/>\n";
            //
            echo "<INPUT name='option_show_col_name_function' id='option_show_col_name_function' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if (_ALLOW_COL_FUNCTION_NAME == '') echo " disabled ";
            if ($option_show_col_name_function <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_name_function'>" . $l_admin_session_col_function . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_language' id='option_show_col_language' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_language <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_language'>" . $l_language . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_level' id='option_show_col_level' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == '') or (_SPECIAL_MODE_GROUP_COMMUNITY != '') or (_SPECIAL_MODE_GROUP_COMMUNITY != '') )  echo " disabled ";
            if ($option_show_col_level <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_level'>" . $l_admin_users_col_level . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_role' id='option_show_col_role' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if (_ROLES_TO_OVERRIDE_PERMISSIONS == "") echo " disabled ";
            if ($option_show_col_role <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_role'>" . $l_admin_role . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_ip_address' id='option_show_col_ip_address' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_ip_address <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_ip_address'>" . $l_admin_session_col_ip . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_time' id='option_show_col_time' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ( (_TIME_ZONES == "") or ($nb_use_time_shit == "") ) echo " disabled ";
            if ( (_TIME_ZONES != "") and ($option_show_col_time <> '') ) echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_time'>" . $l_admin_session_col_time . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_begin' id='option_show_col_begin' TYPE='CHECKBOX' VALUE='2' class='genmed' ";
            if ($option_show_col_begin <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_begin'>" . $l_admin_session_col_begin . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_last_time' id='option_show_col_last_time' TYPE='CHECKBOX' VALUE='2' class='genmed' ";
            if ($option_show_col_last_time <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_last_time'>" . $l_admin_session_col_last . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_version' id='option_show_col_version' TYPE='CHECKBOX' VALUE='3' class='genmed' ";
            if ($option_show_col_version <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_version'>" . $l_admin_session_col_version . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_os' id='option_show_col_os' TYPE='CHECKBOX' VALUE='3' class='genmed' ";
            if ($option_show_col_os <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_os'>OS <SMALL>(Operating System)</SMALL></label><BR/>\n";

            echo "<INPUT name='option_show_col_activity' id='option_show_col_activity' TYPE='CHECKBOX' VALUE='1'  class='genmed' ";
            if ($option_show_col_activity <> '') echo "CHECKED";
            //echo " />" . $l_admin_users_col_activity . "<BR/>\n";
            echo " />";
            echo "<label for='option_show_col_activity'>" . ucfirst(trim($l_admin_users_order_last_activity)) . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_password' id='option_show_col_password' TYPE='CHECKBOX' VALUE='1'  class='genmed' ";
            if (_USER_NEED_PASSWORD =='') echo " disabled ";
            if ($option_show_col_password <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_password'>" . $l_admin_users_col_password . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_create' id='option_show_col_create' TYPE='CHECKBOX' VALUE='3' class='genmed' ";
            if ($option_show_col_create <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_create'>" . $l_admin_users_col_creat . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_rating' id='option_show_col_rating' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if (_ALLOW_CONTACT_RATING == "") echo " disabled ";
            if ($option_show_col_rating <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_rating'>" . $l_admin_users_reputation . "</label><BR/>\n";
                
            echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            //echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
            echo "<input type='hidden' name='action' value = 'list_sessions' />"; // les paramètres de cette page, et y revenir ensuite
            echo "</TD>";
            echo "</TR>";
            echo "<TR>";
            echo "<TD ALIGN='CENTER' class='catBottom'>";
            echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            echo "</TD>";
            echo "</TR>";
          }
          echo "</FORM>";
          echo "</TABLE>";

      
      

      echo "</TD><TD WITH='50%' ALIGN='RIGHT' VALIGN='TOP'>\n";

          if ($only_status == '')
          {
            //echo "<SMALL><BR/></SMALL>";
            if ( ($nb_status_away > 0) or ($nb_status_busy > 0) or ($nb_status_do_not_disturb > 0) or ($nb_status_not_connect > 0) )
            {
              echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
              //echo "<TR><TD COLSPAN='3' ALIGN='CENTER' class='catHead'><B>" . $l_legende . " </B>(" . strtolower($l_admin_session_order_state) .") </TD></TR>";
              //echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'>&nbsp;<B>" . $l_legende . " </B>(" . strtolower($l_admin_session_order_state) .") ";
              echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'>&nbsp;<B>" . $l_legende . " </B>(" . $l_admin_session_order_state .") ";
              if ($im_session_list_show_legende > 0)
              {
                echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_session_show_legende&im_session_list_show_legende=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_session_show_legende&im_session_list_show_legende=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              echo "</TD></TR>";
              //
              if ($im_session_list_show_legende > 0)
              {
              
                echo "<TR><TD ALIGN='CENTER' class='row1'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='16' HEIGHT='16'>";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_session_info_online . "&nbsp;";
                if ( ($nb_row_by_page < 1000) and ($nb_page > 1) )
                {
                  echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                  if ($nb_status_online > 0) echo $nb_status_online . "&nbsp;";
                }
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ($nb_status_tot_online > 0) echo "<A HREF='list_sessions.php?tri=" . $tri . "&only_status=o&lang=" . $lang . "&'>" . $nb_status_tot_online . "</A>&nbsp;";
              
                echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18'>";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_session_info_away . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_row_by_page < 1000) and ($nb_page > 1) )
                {
                  if ($nb_status_away > 0) echo $nb_status_away . "&nbsp;";
                  echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                }
                if ($nb_status_tot_away > 0) echo "<A HREF='list_sessions.php?tri=" . $tri . "&only_status=a&lang=" . $lang . "&'>" . $nb_status_tot_away . "</A>&nbsp;";
              
                echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1' WIDTH='25'>";
                echo " <IMG SRC='" . _FOLDER_IMAGES . "bt_orange.gif' WIDTH='18' HEIGHT='18'> ";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_session_info_busy . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_row_by_page < 1000) and ($nb_page > 1) )
                {
                  if ($nb_status_busy > 0) echo $nb_status_busy . "&nbsp;";
                  echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                }
                if ($nb_status_tot_busy > 0) echo "<A HREF='list_sessions.php?tri=" . $tri . "&only_status=b&lang=" . $lang . "&'>" . $nb_status_tot_busy . "</A>&nbsp;";
                
                echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_red.gif' WIDTH='18' HEIGHT='18'>";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_session_info_do_not_disturb . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_row_by_page < 1000) and ($nb_page > 1) )
                {
                  if ($nb_status_do_not_disturb > 0) echo $nb_status_do_not_disturb . "&nbsp;";
                  echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                }
                if ($nb_status_tot_do_not_disturb > 0) echo "<A HREF='list_sessions.php?tri=" . $tri . "&only_status=d&lang=" . $lang . "&'>" . $nb_status_tot_do_not_disturb . "</A>&nbsp;";
              
                echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif' WIDTH='18' HEIGHT='18'>";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_session_info_not_connect . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_row_by_page < 1000) and ($nb_page > 1) )
                {
                  if ($nb_status_not_connect > 0) echo $nb_status_not_connect . "&nbsp;";
                  echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                }
                if ($nb_status_tot_not_connect > 0) echo "<A HREF='list_sessions.php?tri=" . $tri . "&only_status=n&lang=" . $lang . "&'>" . $nb_status_tot_not_connect . "</A>&nbsp;";
                
                echo "</TD></TR>";
              }
              echo "</TABLE>";
            }
          }

    echo "</TD></TR>";
    echo "</TABLE>";


  echo "</TD></TR>";
  echo "</TABLE>";

}
else
{
	echo "<TR>";
	echo "<TD colspan='12' ALIGN='CENTER' class='row2'>";
		echo "<font face='verdana' size='2'>" . $l_admin_session_no_session;
	echo "</TD>";
	echo "</TR>";
	echo "</TABLE>";

  echo "</TD></TR>";
  echo "</TABLE>";
}
//
mysqli_close($id_connect);
//
display_menu_footer();
//
echo "</body></html>";
?>
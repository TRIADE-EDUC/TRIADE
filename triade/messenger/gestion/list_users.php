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


if (isset($_COOKIE['im_user_list_col_os'])) $option_show_col_os = $_COOKIE['im_user_list_col_os'];  else  $option_show_col_os = '1';
if (isset($_COOKIE['im_user_list_col_time'])) $option_show_col_time = $_COOKIE['im_user_list_col_time'];  else  $option_show_col_time = '1';
if (isset($_COOKIE['im_user_list_col_last'])) $option_show_col_last = $_COOKIE['im_user_list_col_last'];  else  $option_show_col_last = '0';
if (isset($_COOKIE['im_user_list_col_role'])) $option_show_col_role = $_COOKIE['im_user_list_col_role'];  else  $option_show_col_role = '0';
if (isset($_COOKIE['im_user_list_col_level'])) $option_show_col_level = $_COOKIE['im_user_list_col_level'];  else  $option_show_col_level = '0';
if (isset($_COOKIE['im_user_list_col_email'])) $option_show_col_email = $_COOKIE['im_user_list_col_email'];  else  $option_show_col_email = '0';
if (isset($_COOKIE['im_user_list_col_create'])) $option_show_col_create = $_COOKIE['im_user_list_col_create'];  else  $option_show_col_create = '1';
if (isset($_COOKIE['im_user_list_col_action'])) $option_show_col_action = $_COOKIE['im_user_list_col_action'];  else  $option_show_col_action = '1';
if (isset($_COOKIE['im_user_list_col_rating'])) $option_show_col_rating = $_COOKIE['im_user_list_col_rating'];  else  $option_show_col_rating = '0';
if (isset($_COOKIE['im_user_list_col_backup'])) $option_show_col_backup = $_COOKIE['im_user_list_col_backup'];  else  $option_show_col_backup = '1';
if (isset($_COOKIE['im_user_list_col_version'])) $option_show_col_version = $_COOKIE['im_user_list_col_version'];  else  $option_show_col_version = '0';
if (isset($_COOKIE['im_user_list_col_language'])) $option_show_col_language = $_COOKIE['im_user_list_col_language'];  else  $option_show_col_language = '1';
if (isset($_COOKIE['im_user_list_col_password'])) $option_show_col_password = $_COOKIE['im_user_list_col_password'];  else  $option_show_col_password = '0';
if (isset($_COOKIE['im_user_list_col_activity'])) $option_show_col_activity = $_COOKIE['im_user_list_col_activity'];  else  $option_show_col_activity = '0';
if (isset($_COOKIE['im_user_list_col_ip_address'])) $option_show_col_ip_address = $_COOKIE['im_user_list_col_ip_address'];  else  $option_show_col_ip_address = '0';
if (isset($_COOKIE['im_user_list_col_name_function'])) $option_show_col_name_function = $_COOKIE['im_user_list_col_name_function'];  else  $option_show_col_name_function = '1';
if (isset($_COOKIE['im_user_list_show_select_cols'])) $im_user_list_show_select_cols = $_COOKIE['im_user_list_show_select_cols'];  else  $im_user_list_show_select_cols = '1';
if (isset($_COOKIE['im_user_list_show_legende'])) $im_user_list_show_legende = $_COOKIE['im_user_list_show_legende'];  else  $im_user_list_show_legende = '1';
if (intval($option_show_col_os) <= 0) $option_show_col_os = "";
if (intval($option_show_col_time) <= 0) $option_show_col_time = "";
if (intval($option_show_col_last) <= 0) $option_show_col_last = "";
if (intval($option_show_col_role) <= 0) $option_show_col_role = "";
if (intval($option_show_col_level) <= 0) $option_show_col_level = "";
if (intval($option_show_col_email) <= 0) $option_show_col_email = "";
if (intval($option_show_col_create) <= 0) $option_show_col_create = "";
if (intval($option_show_col_action) <= 0) $option_show_col_action = "";
if (intval($option_show_col_rating) <= 0) $option_show_col_rating = "";
if (intval($option_show_col_backup) <= 0) $option_show_col_backup = "";
if (intval($option_show_col_version) <= 0) $option_show_col_version = "";
if (intval($option_show_col_language) <= 0) $option_show_col_language = "";
if (intval($option_show_col_password) <= 0) $option_show_col_password = "";
if (intval($option_show_col_activity) <= 0) $option_show_col_activity = "";
if (intval($option_show_col_ip_address) <= 0) $option_show_col_ip_address = "";
if (intval($option_show_col_name_function) <= 0) $option_show_col_name_function = "";
if (intval($im_user_list_show_select_cols) <= 0) $im_user_list_show_select_cols = "";
if (intval($im_user_list_show_legende) <= 0) $im_user_list_show_legende = "";
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['only_users'])) $only_users = $_GET['only_users'];  else  $only_users = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
if (isset($_GET['only_ip'])) $only_ip = $_GET['only_ip'];  else  $only_ip = "";
if (isset($_GET['only_outofdate'])) $only_outofdate = $_GET['only_outofdate'];  else  $only_outofdate = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_users_unlock);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_users_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="60;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
//
$only_users = f_clean_username($only_users);
$only_ip = f_clean_username($only_ip);
if (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == "") $option_show_col_level = "";
if (_SPECIAL_MODE_GROUP_COMMUNITY != '') $option_show_col_level = "";
if (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') $option_show_col_level = "";
if (_ALLOW_CONTACT_RATING == "") $option_show_col_rating = "";
if (_BACKUP_FILES == "") $option_show_col_backup = "";
if (_ROLES_TO_OVERRIDE_PERMISSIONS == "") $option_show_col_role = "";
if (_EXTERNAL_AUTHENTICATION != "") $option_show_col_password = "";
if (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) <= 10) $only_outofdate = "";
if ($only_outofdate != "") $only_status = "";
if ($only_outofdate != "") $option_show_col_last = 'X'; // forcer l'affichage de la colonne date dernière utilisation (car choix des colonnes indisponible pour les comptes périmés).
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
$nb_status_wait_valid = 0;
$nb_status_change_ok = 0;
$nb_status_lock = 0;
$nb_status_valid = 0;
$nb_status_leave = 0;
$nb_status_tot_wait_valid = 0;
$nb_status_tot_change_ok = 0;
$nb_status_tot_lock = 0;
$nb_status_tot_valid = 0;
$nb_status_tot_leave = 0;
//
//  | A | B | C |...
$alpha_link = "";
if ( ($tri == "") and ($nb_row_by_page > 50) )
{
	$requete = " select distinct(LEFT(UPPER(USR_USERNAME), 1)) ";
	$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  switch ($only_status)
  {
    case "w" :
      //$requete .= " WHERE (USR_CHECK = 'WAIT' or USR_STATUS = 2) ";
      //$requete .= " WHERE USR_STATUS = 2 ";
      $requete .= " WHERE USR_STATUS in (2, 4) ";
      break;
    case "c" :
      //$requete .= " WHERE (USR_CHECK = '' or USR_STATUS = 3) ";
      $requete .= " WHERE USR_STATUS = 3 ";
      break;
    case "v" :
      //$requete .= " WHERE ( (USR_CHECK <> '' and USR_CHECK <> 'WAIT' and USR_STATUS < 9) or USR_STATUS = 1 ) ";
      $requete .= " WHERE USR_STATUS = 1 ";
      break;
  }
  if ($only_users != "")
  {
    if (strstr($requete, "WHERE") != "")
      $requete .= " AND USR_USERNAME like '%" . $only_users . "%' ";
    else
      $requete .= " WHERE USR_USERNAME like '%" . $only_users . "%' ";
  }
  if ($only_ip != "")
  {
    if (strstr($requete, "WHERE") != "")
      $requete .= " AND USR_IP_ADDRESS like '%" . $only_ip . "%' ";
    else
      $requete .= " WHERE USR_IP_ADDRESS like '%" . $only_ip . "%' ";
  }
  if ( ($only_outofdate != "") and (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) > 10) )
  {
    if (strstr($requete, "WHERE") != "")
    {
      $requete .= " AND TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) . " ";
      $requete .= " and USR_STATUS <> 4 ";
    }
    else
    {
      $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) . " ";
      $requete .= " and USR_STATUS <> 4 ";
    }
  }
	$requete .= " order by LEFT(UPPER(USR_USERNAME), 1) ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-A3a]", $requete);
	if ( mysqli_num_rows($result) > 5 )
	{
		while( list ($first) = mysqli_fetch_row ($result) )
		{
			$alpha_link .= " | <A HREF=#" . $first . ">" . $first . "</A>";
		}
		$alpha_link .= " | ";
		//
		if ($nb_row_by_page < 30) $alpha_link = "";
	}
}
echo "<font face='verdana' size='2'>";
//echo $alpha_link;  // non plus bas !
//
//
if ($option_show_col_role != "")
{
  $liste_roles_orig = "";
  $requete  = " select SQL_CACHE ROL_NAME, ID_ROLE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
  $requete .= " WHERE ROL_DEFAULT = '' "; // on masque la valeur par défaut (c'est la ligne vide)
  $requete .= " ORDER BY ROL_NAME ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A3d]", $requete); 
  if ( mysqli_num_rows($result) > 0 )
  {
    // Ligne vide (valeur par défaut) :
    $liste_roles_orig .= "<option value='0' ZZZ-0 ";
    $liste_roles_orig .= " class='genmed'></option>";
    //
    while( list ($role, $id_role) = mysqli_fetch_row ($result) )
    {
      $liste_roles_orig .= "<option value='" . $id_role . "' ZZZ-". $id_role . " ";
      $liste_roles_orig .= " class='genmed'>" . $role . "</option>";
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
if (!$result) error_sql_log("[ERR-A3c]", $requete);
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
//
$requete  = " SELECT USR_USERNAME, USR_NICKNAME, USR_NAME, USR_EMAIL, USR.ID_USER, USR_LEVEL, USR_CHECK, USR_STATUS, USR_DATE_CREAT, ";
$requete .= " USR_DATE_LAST, USR_PASSWORD, USR_VERSION, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, USR_TIME_SHIFT, USR_OS, ";
$requete .= " USR_IP_ADDRESS, USR_GENDER, USR_RATING, USR_GET_ADMIN_ALERT, USR_DATE_PASSWORD, USR_DATE_BACKUP, ";
$requete .= " USR_DATE_ACTIVITY, ID_ROLE, count( CNT.ID_USER_2) ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER AS USR ";
$requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "CNT_CONTACT AS CNT ON ( CNT.ID_USER_1 = USR.ID_USER ) ";
switch ($only_status)
{
	case "w" :
		//$requete .= " WHERE (USR_CHECK = 'WAIT' or USR_STATUS = 2)  ";
		//$requete .= " WHERE USR_STATUS = 2  ";
		$requete .= " WHERE USR_STATUS in (2, 4) ";
		break;
	case "c" :
		//$requete .= " WHERE (USR_CHECK = '' or USR_STATUS = 3) ";
		$requete .= " WHERE USR_STATUS = 3 ";
		break;
	case "v" :
		//$requete .= " WHERE ( (USR_CHECK <> '' and USR_CHECK <> 'WAIT' and USR_STATUS < 9) or USR_STATUS = 1 ) ";
		$requete .= " WHERE USR_STATUS = 1 ";
		break;
	case "l" :
		$requete .= " WHERE USR_STATUS = 9 ";
		break;
}
if ($only_users != "")
{
	if (strstr($requete, "WHERE") != "")
    $requete .= " AND USR_USERNAME like '%" . $only_users . "%' ";
	else
    $requete .= " WHERE USR_USERNAME like '%" . $only_users . "%' ";
}
if ($only_ip != "")
{
  if (strstr($requete, "WHERE") != "")
    $requete .= " AND USR_IP_ADDRESS like '%" . $only_ip . "%' ";
  else
    $requete .= " WHERE USR_IP_ADDRESS like '%" . $only_ip . "%' ";
}
if ( ($only_outofdate != "") and (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) > 10) )
{
  if (strstr($requete, "WHERE") != "")
  {
    $requete .= " AND TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) . " ";
    $requete .= " and USR_STATUS <> 4 ";
  }
  else
  {
    $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) . " ";
    $requete .= " and USR_STATUS <> 4 ";
  }
}
$requete .= " GROUP BY USR.ID_USER ";
//
switch ($tri)
{
	case "name" :
		$requete .= "ORDER BY UPPER(USR_NAME), UPPER(USR_USERNAME) ";
		break;
	case "etat" :
		$requete .= "ORDER BY USR_STATUS, USR_CHECK, UPPER(USR_USERNAME), UPPER(USR_NAME) ";
		break;
	case "date_creat" :
		$requete .= "ORDER BY USR_DATE_CREAT DESC, UPPER(USR_USERNAME) ";
		break;
	case "date_last" :
		$requete .= "ORDER BY USR_DATE_LAST DESC, UPPER(USR_USERNAME) ";
		break;
	case "level" :
		$requete .= "ORDER BY USR_LEVEL, UPPER(USR_USERNAME) ";
		break;
	case "role" :
		$requete .= "ORDER BY ID_ROLE DESC, UPPER(USR_USERNAME) ";
		break;
	case "email" : 
		$requete .= "ORDER BY USR_EMAIL ";
		break;
	default :
		$requete .= "ORDER BY UPPER(USR_USERNAME), UPPER(USR_NAME) ";
		break;
}
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A3b]", $requete);
$nb_row = mysqli_num_rows($result);
if ( $nb_row > 0 )
{
  if ( $nb_row > 30 )
    echo $alpha_link;
  else
    $alpha_link = "";
  //
  //echo "<BR/>";
  // Page défilement :
  echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>";
  if ($nb_row_by_page > 50)
  {
    echo "<TR><TD COLSPAN='2' ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&'", "");
    echo "</TD></TR>";
  }
  echo "<TR><TD COLSPAN='2'>"; //
  //
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>\n";
	echo "<THEAD>";
	echo "<TR>";
		echo "<TH align='center' COLSPAN='13' class='thHead'>";
		if ($only_users != "")
      $title = "<b>" . $l_admin_users_searching . " </B>";
		else
		{
      if ($only_outofdate != "")
        $title = "<b>" . $l_admin_users_out_of_date . " </B>";
      else
        $title = "<b>" . $l_admin_users_title . " </B>";
    }
    //
    $title .= "<I>";
    switch ($only_status)
    {
      case "w" :
        $title .= " - " . $l_admin_users_info_wait_valid;
        break;
      case "c" :
        $title .= " - " . $l_admin_users_info_change_ok;
        break;
      case "v" :
        $title .= " - " . $l_admin_users_info_valid;
        break;
      case "l" :
        $title .= " - " . $l_admin_users_info_leave;
        break;
    }
    $title .= "</I>";
		$title .= " (" . $nb_row . ")";
		echo "<font face='verdana' size=3>" . $title . "</font></TH>";
	echo "</TR>";
	echo "<TR>";
    $link_user_col = "<A HREF='list_users.php?tri=&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>";
		$larg_col = 160;
		if ($nb_char_username > 11) $larg_col = 180;
		if ($nb_char_username > 15) $larg_col = 210;
		if ($nb_char_username > 18) $larg_col = 230;
		if ( (_FLAG_COUNTRY_FROM_IP <> '') and ($display_flag != '') ) $larg_col = ($larg_col + 20);
		if ($option_show_col_time == '') $larg_col = ($larg_col + 10); // bidouille quand la colonne time shit est masqué, ca réduit col user et action (pourquoi ?) sous Firefox 2 (à tester avec le 3).
		display_row_table($link_user_col, $larg_col);
    //
		if ($option_show_col_name_function != '')
		{
      $link_fonction_col = "<A HREF='list_users.php?tri=name&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_function . "' class='cattitle' >" . $l_admin_users_col_function . "</A>";
      //
      if (strstr($_SERVER["HTTP_USER_AGENT"], "Windows"))
        display_row_table($link_fonction_col, '163'); // 160 : pas sous Vista-Firefox -> 161 // Seven-Firefox : 163 // 161 : pas sous Linux-Firefox -> 171 //
      else
        display_row_table($link_fonction_col, '171'); // 160 : pas sous Vista-Firefox -> 161 // 161 : pas sous Linux-Firefox -> 171 //
		}
    //
    if ($option_show_col_level != '')
    {
      $link_level_col = "<A HREF='list_users.php?tri=level&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_level . "' class='cattitle' >" . $l_admin_users_col_level . "</A>";
      display_row_table($link_level_col, '140'); // 200
    }
		//
    if ($option_show_col_email != '')
    {
      $link_email_col = "<A HREF='list_users.php?tri=email&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_email . "' class='cattitle' >" . $l_email . "</A>";
      if (strstr($_SERVER["HTTP_USER_AGENT"], "Windows"))
        display_row_table($link_email_col, '163');
      else
        display_row_table($link_email_col, '171');
    }
    //
    if ($option_show_col_role != '')
    {
      $link_role_col = "<A HREF='list_users.php?tri=role&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_role . "' class='cattitle' >" . $l_admin_role . "</A>";
      display_row_table($link_role_col, '140'); // 200
    }
		//
		if ( ($option_show_col_language != '')  and ($display_flag != '') )  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' width='16' height='16' ALT='" . $l_language . "' TITLE='" . $l_language . "' >", '30');
//		if ( (_FLAG_COUNTRY_FROM_IP != "")  and ($display_flag != '') )
//    {
//      display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_country.png' width='16' height='16' ALT='" . $l_country . "' TITLE='" . $l_country . "' >", '40');
//    }
		//
    $link_state_col = "&nbsp;<A HREF='list_users.php?tri=etat&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_state . "' class='cattitle' >" . $l_admin_users_col_etat . "</A>&nbsp;";
    display_row_table($link_state_col, '30');
    //
		//echo "<TD align=center width='130' COLSPAN='2' class='catHead'><font face='verdana' size='2'><b>" . $l_admin_users_col_etat_wait . "</b></font></TD>";
    if ( $option_show_col_action != "")
    {
      if (_USER_NEED_PASSWORD !='')
        display_row_table($l_admin_users_col_action, '70');
      else
        display_row_table($l_admin_users_col_action, '60'); // 80
    }
    //
    if ($option_show_col_ip_address != '')
		{
      if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') )
        display_row_table($l_admin_session_col_ip, '160');
      else
        display_row_table($l_admin_session_col_ip, '130');
    }
    //
		if ($option_show_col_time != '')  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "time_shift.png' width='16' height='16' ALT='" . $l_time_zone . "' ALIGN='CENTER' TITLE='" . $l_time_zone . "' >", '55');
    //
    if ($option_show_col_last != '')
    {
      $link_last_col = "<A HREF='list_users.php?tri=date_last&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_last . "' class='cattitle' >" . $l_admin_users_col_last . "</A>";
      display_row_table($link_last_col, '85');
    }
    //
    if ($option_show_col_create != '')
    {
      $link_creat_col = "<A HREF='list_users.php?tri=date_creat&page=" . $page . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_creat . "' class='cattitle' >" . $l_admin_users_col_creat . "</A>";
      display_row_table($link_creat_col, '87');
    }
    //
    if ($option_show_col_activity != '') display_row_table($l_admin_users_col_activity, '85');
    //
    if ($option_show_col_password != '') display_row_table("<SMALL>" . $l_admin_users_col_password . "</SMALL>", '85');
    //
    //if ($option_show_col_backup != '') display_row_table($l_admin_options_backup_files, '85');
    if ($option_show_col_backup != '') display_row_table($l_admin_users_col_backup, '85');
    //
    if ($option_show_col_version != '') display_row_table($l_admin_users_col_version, '60');
    //
    if ($option_show_col_os != '') display_row_table("OS", '30');
    //
    if ($option_show_col_rating != '') display_row_table($l_admin_users_reputation, '40');

    //
	echo "</TR>";
	echo "</THEAD>\n";


	echo "<TFOOT>";
	// ligne d'info, si affichage (modif) des levels :
	if ($option_show_col_level != '')
	{
    echo "<TR>";
      echo "<TD align='center' COLSPAN='13' class='row3'>";
        echo "<font face='verdana' size='2'>";
        echo $l_admin_users_info_level . " ";
      echo "</TD>";
    echo "</TR>";
  }
  // le contraire, pour indiquer que la colonne nom/fonction, même masquée reste utilisée
  if ( ($option_show_col_name_function == '') and (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == '') )
  {
    echo "<TR>";
      echo "<TD align='center' COLSPAN='13' class='row3'>";
        echo "<font face='verdana' size='1' color='gray'>";
        echo $l_admin_users_info_nm_function . " ";
      echo "</TD>";
    echo "</TR>";
  }
  //
	// Dernière ligne : trier.
	if ($only_outofdate == "")
	{
    echo "<TR>";
      echo "<TD align='center' COLSPAN='13' class='catBottom'>";
        echo "<font face='verdana' size='2'>";
        echo $l_order_by . " ";
        if ($tri == '') echo "<B>";
        echo "<A HREF='list_users.php?tri=&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_login . "</A></B> - ";
        if ($option_show_col_name_function != '')
        {
          if ($tri == 'name') echo "<B>";
          echo "<A HREF='list_users.php?tri=name&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_function . "</A></B> - ";
        }
        if ($option_show_col_level != '')
        {
          if ($tri == 'level') echo "<B>";
          echo "<A HREF='list_users.php?&tri=level&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_level . "</A></B> - ";
        }
        if ($option_show_col_email != '')
        {
          if ($tri == 'email') echo "<B>";
          echo "<A HREF='list_users.php?&tri=email&page=" . $page . "&lang=" . $lang . "&'>" . $l_email . "</A></B> - ";
        }
        if ($option_show_col_role != '')
        {
          if ($tri == 'role') echo "<B>";
          echo "<A HREF='list_users.php?&tri=role&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_role . "</A></B> - ";
        }
        if ($tri == 'etat') echo "<B>";
        echo "<A HREF='list_users.php?tri=etat&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_state . "</A></B> - ";
        if ($tri == 'date_creat') echo "<B>";
        echo "<A HREF='list_users.php?tri=date_creat&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_creat . "</A></B> - ";
        //if ($tri != 'date_last') echo " - ";
        if ($tri == 'date_last') echo "<B>";
        echo " <A HREF='list_users.php?tri=date_last&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_last . "</A></B>";
      echo "</TD>";
    echo "</TR>";
  }
	echo "</TFOOT>";

	echo "\n";
	echo "<TBODY>";
	//
	$last_first_letter = "";
	$row_num = 0;
	$display_start = 0;
	$display_end = 0;
  if ($nb_row > $nb_row_by_page)
  {
    $nb_page = ceil($nb_row / $nb_row_by_page);
    if ($page < 1) $page = 1;
    if ($page > $nb_page) $page = $nb_page;
    $display_start = ( ($page - 1) * $nb_row_by_page + 1);
    $display_end = ($display_start + $nb_row_by_page - 1);
    if ($display_end > $nb_row) $display_end = $nb_row;
  }
  while( list ($contact, $nickname, $nom, $email, $id_user, $usr_level, $usr_check, $usr_status, $usr_datcreat, $usr_datlast, $passcr, $version, $country_code, $language_code, $time_shit, $win_os, $ip, $usr_gender, $usr_rating, $usr_get_admin, $usr_date_password, $usr_date_backup, $usr_dat_activity, $usr_id_role, $nb_contacts) = mysqli_fetch_row ($result) )
	{
    $row_num++;
    if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
    {
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $contact = $nickname;
      //
      echo "<TR>";
      echo "<TD class='row1'>";
        $plus = "";
        if ($tri == "")
        {
          $t = strtoupper(substr($contact, 0, 1));
          if ($t != $last_first_letter)
          {
            $last_first_letter = $t;
            $plus = " ID=" . $t;
          }
        }
        if (($usr_gender == "M") or ($usr_gender == "W"))
        {
          if ($usr_gender == "M") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "man.png' WIDTH='16' HEIGHT='16' ALT='" . $l_man . "' TITLE='" . $l_man . "' BORDER='0'>";
          if ($usr_gender == "W") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "woman.png' WIDTH='16' HEIGHT='16' ALT='" . $l_woman . "' TITLE='" . $l_woman . "' BORDER='0'>";
        }
        else
        {
          if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') and ($option_show_col_ip_address == "") )
          {
            if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
            {
              $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
              $country_name = f_quote($GEOIP_COUNTRY_NAMES[$country_id]);
              echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_country . " : " . $country_name . "' TITLE='" . $l_country . " : " . $country_name . "'>";
            }
          }
        }
       
        echo "&nbsp;<font face='verdana' size='2'>";
        //if ( ($usr_check != '') and ($usr_check != 'WAIT') )
        echo "<A " . $plus . " HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
          //echo "<A " . $plus . " HREF='list_contact.php?only_one=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
          //echo "<A " . $plus . " HREF='messagerie.php?id_user_select=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
        echo $contact . "</A>";
        if (intval($nb_contacts) > 0) echo "<SMALL> (<acronym title='" . $l_admin_contacts . " : " . $nb_contacts . "'>" . $nb_contacts . "</acronym>)</SMALL>";
        if ($usr_get_admin) echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "b_admin.png' ALIGN='BASELINE' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_admin_alert . "' TITLE='" . $l_admin_users_admin_alert . "'>";
        echo "&nbsp;</font>";
      echo "</TD>";
      //
      if ($option_show_col_name_function != '')
      {
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<FORM METHOD='POST' ACTION='user_update_name.php?'>";
        echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<input type='text' name='nom' maxlength='40' value='" . $nom . "' size='20' class='post' />";
        echo " ";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
        echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "</FORM>";
      }
      echo "\n";
      //
      if ($option_show_col_email != '')
      {
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<FORM METHOD='POST' ACTION='user_update_email.php?'>";
        echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<input type='text' name='email' maxlength='80' value='" . $email . "' size='20' class='post' />";
        echo " ";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
        echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
        echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "</FORM>";
      }
      //
      if ($option_show_col_level != '')
      {
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<FORM METHOD='POST' ACTION='user_level.php?'>";
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
        echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
        echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "</TD>";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "</FORM>";
      }
      echo "\n";
      //


      if ($option_show_col_role != '')
      {
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<FORM METHOD='POST' ACTION='user_update_role.php?'>";
        echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
          echo "<select name='id_role'>";
          $usr_id_role = intval($usr_id_role); // cause valeures nulles
          if ($usr_id_role <= 0) $usr_id_role = 0;
          $liste_roles = str_replace("ZZZ-" . $usr_id_role , "SELECTED", $liste_roles_orig);
          //if ($usr_id_role <= 0) echo "SELECTED";
          //$liste_roles .= "<option value='0' ZZZ-0 ";
          echo $liste_roles;
          echo "</select>";
          echo " ";
          echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
          echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
          echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
          echo "<INPUT TYPE='hidden' name='from' value = 'list_users' />";
          if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "</TD>";
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") echo "</FORM>";
      }
      echo "\n";


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
      /*
      if ( (_FLAG_COUNTRY_FROM_IP != "")  and ($display_flag != '') )
      {
        echo "<TD align='center' class='row1'>";
        if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
        {
          $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
          $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
          echo "<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_country . " : " . $country_name . "' TITLE='" . $l_country . " : " . $country_name . "'>";
        }
        echo "</TD>";
      }
      */
      //
      echo "<TD align='center' class='row1'>";
      if ($usr_status == 2) $usr_check = "WAIT";
      if ($usr_status == 3) $usr_check = "";
      if ($usr_status == 4) $usr_check = "LOCK";
      if ($usr_status == 9) $usr_check = "LEAVE";
      switch ($usr_check)
      {
        case "WAIT" : // 2
          echo "<IMG SRC='" . _FOLDER_IMAGES . "wait.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_wait_valid . "' TITLE='" . $l_admin_users_info_wait_valid . "'>";
          $nb_status_wait_valid++;
          $nb_status_tot_wait_valid++;
          break;
        case "" : // 3
          echo "<IMG SRC='" . _FOLDER_IMAGES . "use_up.gif' WIDTH='16' HEIGHT='20' ALT='" . $l_admin_users_info_change_ok . "' TITLE='" . $l_admin_users_info_change_ok . "'>";
          $nb_status_change_ok++;
          $nb_status_tot_change_ok++;
          break;
        case "LOCK" : // 4
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_lock.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_locked . "' TITLE='" . $l_admin_users_info_locked . "'>";
          $nb_status_lock++;
          $nb_status_tot_lock++;
          break;
        case "LEAVE" : // 9
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_leave.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_leave . "' TITLE='" . $l_admin_users_info_leave . "'>";
          $nb_status_leave++;
          $nb_status_tot_leave++;
          break;
        default : // 1
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_valid . "' TITLE='" . $l_admin_users_info_valid . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_valid . "' TITLE='" . $l_admin_users_info_valid . "'>";
          $nb_status_valid++;
          $nb_status_tot_valid++;
          break;
      }
      echo "</TD>";
      //
      if ( $option_show_col_action != "")
      {
        echo "<TD valign='bottom' align='center' class='row1'>";
        //if ( ($usr_check == 'WAIT') or ($usr_status == 2) )
        if ( ($usr_status == 2) or ($usr_status == 4) )
        {
          echo "<A HREF='user_autorize.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&' title='" . $l_admin_bt_allow . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png' ALT='" . $l_admin_bt_allow . "' TITLE='" . $l_admin_bt_allow . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
        }
        else
        {		
          //echo "<A HREF='user_wait.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "' title='" . $l_admin_bt_invalidate . "'>";
          echo "<A HREF='user.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "&action=wait&from_list=X&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&' title='" . $l_admin_bt_invalidate . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_lock.png' ALT='" . $l_admin_bt_invalidate . "' TITLE='" . $l_admin_bt_invalidate . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
        }
        //
        if (_USER_NEED_PASSWORD =='') echo " ";
        echo "&nbsp;";
        //
        if (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") 
        {
          //echo "<A HREF='user_delete.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "' title='" . $l_admin_bt_delete . "'>";
          echo "<A HREF='user.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "&action=delete&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&' title='" . $l_admin_bt_delete . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
          //
          if (_USER_NEED_PASSWORD =='') echo " ";
          if ( (_USER_NEED_PASSWORD != '') and ($passcr != '') and (_EXTERNAL_AUTHENTICATION == '') )
          {
            echo "&nbsp;";
            //
            $t = $l_admin_bt_erase . " " . strtolower($l_admin_users_col_password);
            echo "<A HREF='user_password_delete.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&' title='" . $t . "'>";
            echo "<IMG SRC='" . _FOLDER_IMAGES . "b_empty_password.png' ALT='" . $t . "' TITLE='" . $t . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
          }
        }
        echo "</TD>";
      }
      echo "\n";
      //
      //
      if ($option_show_col_ip_address != '')
      {
        if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') and ($country_code != "") )
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
          echo "<font face='verdana' size='2'>" . $ip;
        else
          echo "<font face='verdana' size='1' color='gray'><I>Not in demo version </I></font>";
        echo "</font></TD>";
      }
      //
      //
      if ($option_show_col_time != '')
      {
        echo "<TD align='center' class='row2'>";
          if (intval($time_shit) <> 0) 
          {
            if ($time_shit < 0) 
              $t = "-"; 
            else
              $t = "+";
            $t .= intval(abs($time_shit) / 10);
            if ( (abs($time_shit / 10) - intval(abs($time_shit) / 10)) <> 0 )
              $t .= ":30";
            else
              $t .= ":00";
            echo "<font face='verdana' size='2'>";
            echo $t;
            echo "</font>";
          }
          else
            echo "&nbsp;";
        echo "</TD>";
      }
      //
      if ($option_show_col_last != '')
      {
        echo "<TD align='center' class='row2'>";
          if ($usr_datlast == '0000-00-00')
            $datlast = 	'&nbsp;';
          else
            $datlast = date($l_date_format_display, strtotime($usr_datlast));
          //
          echo "<font face='verdana' size='2'>";
          if ( $datlast != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $datlast . "</font>";
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
      if ($option_show_col_backup != "")
      {
        echo "<TD align='center' class='row2'>";
          if ($usr_date_backup == '0000-00-00')
            $usr_date_backup = 	'&nbsp;';
          else
            $usr_date_backup = date($l_date_format_display, strtotime($usr_date_backup));
          //
          echo "<font face='verdana' size='2'>";
          if ( $usr_date_backup != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $usr_date_backup . "</font>";
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
      }
      //
      if ($option_show_col_os != '')
      {
        echo "<TD align='center' class='row2'>";
          display_os_picture($win_os);
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
      echo "</TR>";
      echo "\n";
    }
    else
    {
      if ($only_status == '') 
      {
        if ($usr_status == 2) $usr_check = "WAIT";
        if ($usr_status == 3) $usr_check = "";
        if ($usr_status == 4) $usr_check = "LOCK";
        if ($usr_status == 9) $usr_check = "LEAVE";
        switch ($usr_check)
        {
          case "WAIT" : // 2
            $nb_status_tot_wait_valid++;
            break;
          case "" : // 3
            $nb_status_tot_change_ok++;
            break;
          case "LOCK" : // 4
            $nb_status_tot_lock++;
            break;
          case "LEAVE" : // 9
            $nb_status_tot_leave++;
            break;
          default : // 1
            $nb_status_tot_valid++;
            break;
        }
      }
    }
	}
	//
	echo "</TABLE>";
	echo "</TBODY>\n";
  //
  echo "</TD></TR>";
  echo "<TR><TD>";
  
	if ($only_outofdate == "")
	{
    //if ($nb_row > $nb_row_by_page)
    if ( ($nb_row > 15) and ($nb_row_by_page < 1000) )
    {
      echo "<font face='verdana' size='2'>";
      echo $l_rows_per_page . " : ";
      display_nb_row_page(15, $nb_row_by_page, "list_users_nb_rows");
      echo " | ";
      display_nb_row_page(20, $nb_row_by_page, "list_users_nb_rows");
      echo " | ";
      display_nb_row_page(25, $nb_row_by_page, "list_users_nb_rows");
      echo " | ";
      display_nb_row_page(30, $nb_row_by_page, "list_users_nb_rows");
      echo " | ";
      display_nb_row_page(50, $nb_row_by_page, "list_users_nb_rows");
    }
  }
  echo "</TD><TD ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&only_outofdate=" . $only_outofdate . "&lang=" . $lang . "&'", "UP");
  echo "</TD></TR>";
  
  
  echo "<TR><TD></TD></TR>";
  echo "<TR><TD></TD></TR>";  // Espacement vertical
  
  if ( (strlen($alpha_link) > 3)  and ($nb_row > 30) )
  {
    echo "<TR><TD align='center'>";
    echo "<font face='verdana' size='2'>";
    echo $alpha_link;
    echo "<BR/>";
    echo "</TD></TR>";
  }


  echo "<TR><TD></TD></TR>";
  echo "<TR><TD></TD></TR>";  // Espacement vertical


  echo "<TR><TD COLSPAN='2'>";
  
  
  if ($only_outofdate == "")
	{
	

  
      //echo "<SMALL><BR/></SMALL>";
  
      echo "<TABLE WIDTH='100%' cellspacing='0' cellpadding='0' BORDER='0'>";
      echo "<TR><TD WITH='50%' VALIGN='TOP'>";

          echo "<table cellspacing='1' cellpadding='1' class='forumline'>"; // width='650' 
          echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
          echo "<TR>";
          echo "<TD class='catHead' align='center'>";
          echo "<FONT size='2'>&nbsp;<B>" . $l_display_col;

            echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
            echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            echo "<input type='hidden' name='action' value = 'list_users' />"; // les paramètres de cette page, et y revenir ensuite

            if ($im_user_list_show_select_cols > 0)
            {
              echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_users_show_select_cols&im_user_list_show_select_cols=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_users_show_select_cols&im_user_list_show_select_cols=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
          echo "</TD>";
          echo "</TR>";
          //
          if ($im_user_list_show_select_cols > 0)
          {
            echo "<TR>";
            echo "<td class='row1'>";
            echo "<FONT size='2'>";

            echo "<INPUT name='option_show_col_name_function' id='option_show_col_name_function' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_name_function <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_name_function'>" . $l_admin_users_col_function . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_email' id='option_show_col_email' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_email <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_email'>" . $l_email . "</label><BR/>\n";
            
            echo "<INPUT name='option_show_col_level' id='option_show_col_level' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == "") or (_SPECIAL_MODE_GROUP_COMMUNITY != '') or (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') )  echo " disabled ";
            if ($option_show_col_level <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_level'>" . $l_admin_users_col_level . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_role' id='option_show_col_role' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if (_ROLES_TO_OVERRIDE_PERMISSIONS == "") echo " disabled ";
            if ($option_show_col_role <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_role'>" . $l_admin_role . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_language' id='option_show_col_language' TYPE='CHECKBOX' VALUE='2' class='genmed' ";
            if ($option_show_col_language <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_language'>" . $l_language . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_action' id='option_show_col_action' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_action <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_action'>" . $l_admin_users_col_action . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_ip_address' id='option_show_col_ip_address' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_ip_address <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_ip_address'>" . $l_admin_session_col_ip . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_time' id='option_show_col_time' TYPE='CHECKBOX' VALUE='1'  class='genmed' ";
            if ( (_TIME_ZONES == "") or ($nb_use_time_shit == "") ) echo " disabled ";
            if ( (_TIME_ZONES != "") and ($option_show_col_time <> '') ) echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_time'>" . $l_admin_session_col_time . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_last' id='option_show_col_last' TYPE='CHECKBOX' VALUE='1'  class='genmed' ";
            if ($option_show_col_last <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_last'>" . $l_admin_users_col_last . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_create' id='option_show_col_create' TYPE='CHECKBOX' VALUE='1'  class='genmed' ";
            if ($option_show_col_create <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_create'>" . $l_admin_users_col_creat . "</label><BR/>\n";

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
            
            echo "<INPUT name='option_show_col_backup' id='option_show_col_backup' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if (_BACKUP_FILES == "") echo " disabled ";
            if ($option_show_col_backup <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_backup'>" . $l_admin_users_col_backup . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_version' id='option_show_col_version' TYPE='CHECKBOX' VALUE='3' class='genmed' ";
            if ($option_show_col_version <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_version'>" . $l_admin_users_col_version . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_os' id='option_show_col_os' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_os <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_os'>OS <SMALL><SMALL>(Operating System)</SMALL></SMALL></label><BR/>\n";
            
            echo "<INPUT name='option_show_col_rating' id='option_show_col_rating' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if (_ALLOW_CONTACT_RATING == "") echo " disabled ";
            if ($option_show_col_rating <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_rating'>" . $l_admin_users_reputation . "</label><BR/>\n";
            
                
            echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
            echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            echo "<input type='hidden' name='action' value = 'list_users' />"; // les paramètres de cette page, et y revenir ensuite
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
          //if ( ($nb_status_wait_valid > 0) or ($nb_status_change_ok > 0) or ($nb_status_valid > 0) or ($nb_status_leave > 0) )  
          if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_change_ok > 0) or ($nb_status_tot_leave > 0) or ($nb_status_tot_lock > 0) )  
          {
              
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
            //echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'><B>" . $l_legende . " </B>(" . strtolower($l_admin_users_order_state) .") </TD></TR>";
            echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'><B>&nbsp;" . $l_legende . " </B>(" . strtolower($l_admin_users_order_state) .")&nbsp;";
            if ($nb_status_wait_valid <= 0)
            {
              if ($im_user_list_show_legende > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_users_show_legende&im_user_list_show_legende=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_users_show_legende&im_user_list_show_legende=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
            }
            echo "</TD></TR>";
            //
            if ( ($im_user_list_show_legende > 0) or ($nb_status_wait_valid > 0) )
            {

              if ( ($nb_status_tot_wait_valid > 0) or (_USER_NEED_PASSWORD != '') or (_PENDING_NEW_AUTO_ADDED_USER != '') or (_PENDING_USER_ON_COMPUTER_CHANGE != '') )
              {
                echo "</TD></TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
                //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_orange.gif' WIDTH='18' HEIGHT='18'> ";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "wait.gif' WIDTH='16' HEIGHT='16'> ";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_users_info_wait_valid . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_change_ok > 0) or ($nb_status_tot_valid > 0) or ($nb_status_tot_leave > 0) or ($nb_status_lock > 0) )  
                {
                  if ($nb_row_by_page < 1000) 
                  {
                    if ($nb_status_wait_valid > 0) echo $nb_status_wait_valid . "&nbsp;";
                    echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                  }
                  if ($nb_status_tot_wait_valid > 0) 
                    echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=w&lang=" . $lang . "&'>" . $nb_status_tot_wait_valid . "</A>&nbsp;";
                }
                else
                  if ($nb_status_wait_valid > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=w&lang=" . $lang . "&'>" . $nb_status_wait_valid . "</A>&nbsp;";
              }

              if ($nb_status_tot_leave > 0)
              {
                echo "</TD></TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "b_leave.png' WIDTH='16' HEIGHT='16'> ";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_users_info_leave . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_change_ok > 0) or ($nb_status_tot_valid > 0) or ($nb_status_tot_leave > 0) or ($nb_status_lock > 0) )  
                {
                  if ($nb_row_by_page < 1000) 
                  {
                    if ($nb_status_leave > 0) echo $nb_status_leave . "&nbsp;";
                    echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                  }
                  if ($nb_status_tot_leave > 0) 
                    echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=l&lang=" . $lang . "&'>" . $nb_status_tot_leave . "</A>&nbsp;";
                }
                else
                  if ($nb_status_leave > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=l&lang=" . $lang . "&'>" . $nb_status_leave . "</A>&nbsp;";
              }
              //
              if ( ($nb_status_tot_change_ok > 0) or (_PENDING_NEW_AUTO_ADDED_USER != '') or (_PENDING_USER_ON_COMPUTER_CHANGE != '') )
              {
                echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
                //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "use_up.gif' WIDTH='16' HEIGHT='20'>";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_users_info_change_ok . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_change_ok > 0) or ($nb_status_tot_valid > 0) or ($nb_status_tot_leave > 0) or ($nb_status_lock > 0) )  
                {
                  if ($nb_row_by_page < 1000) 
                  {
                    if ($nb_status_change_ok > 0) echo $nb_status_change_ok . "&nbsp;";
                    echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                  }
                  if ($nb_status_tot_change_ok > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=c&lang=" . $lang . "&'>" . $nb_status_tot_change_ok . "</A>&nbsp;";
                }
                else
                  if ($nb_status_change_ok > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=c&lang=" . $lang . "&'>" . $nb_status_change_ok . "</A>&nbsp;";
              }



              //
              if ($nb_status_tot_lock > 0)
              {
                echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "b_lock.png' WIDTH='16' HEIGHT='16'>";
                echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_users_info_locked . "&nbsp;";
                echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_change_ok > 0) or ($nb_status_tot_valid > 0) or ($nb_status_tot_leave > 0) or ($nb_status_lock > 0) )
                {
                  if ($nb_row_by_page < 1000) 
                  {
                    if ($nb_status_lock > 0) echo $nb_status_lock . "&nbsp;";
                    echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                  }
                  if ($nb_status_tot_lock > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=w&lang=" . $lang . "&'>" . $nb_status_tot_lock . "</A>&nbsp;";
                }
                else
                  if ($nb_status_lock > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=w&lang=" . $lang . "&'>" . $nb_status_lock . "</A>&nbsp;";
              }




              //
              echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
              //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='16' HEIGHT='16'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16'>";
              echo "</TD><TD class='row2'><font face='verdana' size='2'>&nbsp;" . $l_admin_users_info_valid . "&nbsp;";
              echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
              if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_change_ok > 0) or ($nb_status_tot_valid > 0) or ($nb_status_tot_leave > 0) or ($nb_status_lock > 0) )  
              {
                if ($nb_row_by_page < 1000) 
                {
                  if ($nb_status_valid > 0) echo $nb_status_valid . "&nbsp;";
                  echo "</TD><TD class='row2' ALIGN='RIGHT'><font face='verdana' size='2'>&nbsp;";
                }
                if ($nb_status_tot_valid > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=v&lang=" . $lang . "&'>" . $nb_status_tot_valid . "</A>&nbsp;";
              }
              else
                if ($nb_status_valid > 0) echo "<A HREF='list_users.php?tri=" . $tri . "&only_status=v&lang=" . $lang . "&'>" . $nb_status_valid . "</A>&nbsp;";
              //
              echo "</TD></TR>";
            }
            echo "</TABLE>";
          }
        }
    

      
      echo "</TD></TR>";
    echo "</TABLE>";


  }

  
  echo "</TD></TR>";
  echo "</TABLE>";
}
else
{
  echo "<BR/>";
  echo "<div class='info'>";
  //
  if ($only_users != "")
    echo "<b>" . $l_admin_users_no_found . " </B> (" . $only_users . ")";
  else
    echo $l_admin_users_empty;
  //
  echo "</div>";
}
//
//
//
//
mysqli_close($id_connect);
//
//
display_menu_footer();
//
echo "</body></html>";
?>
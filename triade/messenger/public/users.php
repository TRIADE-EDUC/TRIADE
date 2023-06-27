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
//
require ("../common/display_errors.inc.php"); 
//
$option_show_col_os = "";
$option_show_col_time = "X";
$option_show_col_last = "";
$option_show_col_create = "";
$option_show_col_language = "X";
$option_show_col_country = "X";
$option_show_col_name_function_level = "X";
//
//
if (isset($_COOKIE['im_nb_row_by_page'])) $nb_row_by_page = $_COOKIE['im_nb_row_by_page'];  else  $nb_row_by_page = '15';
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['level'])) $level = $_GET['level'];  else  $level = "";
//if (isset($_GET['only_users'])) $only_users = $_GET['only_users'];  else  $only_users = "";
//if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
$only_status = "v";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php");
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_users_title . "</title>";
display_header();
if (_PUBLIC_USERS_LIST == "") // not allowed to display...
{
  echo '<META http-equiv="refresh" content="0;url=../"> ';
  die();
}
echo '<META http-equiv="refresh" content="400;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//
require ("../common/sql.inc.php");
//
//$only_users = f_clean_username($only_users);
//
$display_flag_country = "";
if ( (_FLAG_COUNTRY_FROM_IP != "") or ($option_show_col_language != "") )
{
	if (is_readable("../common/library/geoip/geoip_2.inc.php"))
	{
		require("../common/library/geoip/geoip_2.inc.php");
		$display_flag_country = "X";
  }
}
//
if ($page == 'all')
  $nb_row_by_page = 1000;
else
{
  if (intval($nb_row_by_page) < 15) $nb_row_by_page = 15;
  if ( ($nb_row_by_page <> 15) and ($nb_row_by_page <> 20) and ($nb_row_by_page <> 30) and ($nb_row_by_page <> 40) and ($nb_row_by_page <> 50) and ($nb_row_by_page <> 100) )  $nb_row_by_page = 20;
}
$page = intval($page);
if ($page < 1) $page = 1;
//
$nb_status_wait_valid = 0;
$nb_status_change_ok = 0;
$nb_status_valid = 0;
$nb_status_tot_wait_valid = 0;
$nb_status_tot_change_ok = 0;
$nb_status_tot_valid = 0;
//
//  | A | B | C |...
$alpha_link = "";
if ( ($tri == "") and ($nb_row_by_page > 50) )
{
	$requete = " select distinct(LEFT(UPPER(USR_USERNAME), 1)) ";
	$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  switch ($only_status)
  {
    case "w" : // 2
      //$requete .= " WHERE USR_CHECK = 'WAIT' or USR_STATUS = 2 ";
      $requete .= " WHERE USR_STATUS = 2 ";
      break;
    case "c" : // 3
      //$requete .= " WHERE USR_CHECK = '' or USR_STATUS = 3 ";
      $requete .= " WHERE USR_STATUS = 3 ";
      break;
    case "v" :  // 1
      //$requete .= " WHERE ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 )  ";
      $requete .= " WHERE USR_STATUS = 1 ";
      break;
  }
/*  if ($only_users != "")
  {
    if (strstr($requete, "WHERE") != "")
      $requete .= " AND USR_USERNAME like '%" . $only_users . "%' ";
    else
      $requete .= " WHERE USR_USERNAME like '%" . $only_users . "%' ";
  }
  */
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
echo "<font face=verdana size=2>";
// echo $alpha_link;  // non plus bas !
//
echo "<CENTER>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
if ($lang != 'FR') echo " <A HREF='?lang=FR&tri=" . $tri . "&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'EN') echo " <A HREF='?lang=EN&tri=" . $tri . "&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'IT') echo " <A HREF='?lang=IT&tri=" . $tri . "&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'ES') echo " <A HREF='?lang=ES&tri=" . $tri . "&' TITLE='Spanish'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'PT') echo " <A HREF='?lang=PT&tri=" . $tri . "&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'RO') echo " <A HREF='?lang=RO&tri=" . $tri . "&' TITLE='Romana'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'DE') echo " <A HREF='?lang=DE&tri=" . $tri . "&' TITLE='German'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'NL') echo " <A HREF='?lang=NL&tri=" . $tri . "&' TITLE='Netherlands'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";

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
if ($nb_use_time_shit == "") $option_show_col_time = "";
//
//
$requete  = " SELECT USR_USERNAME, USR_NAME, USR.ID_USER, USR_LEVEL, USR_CHECK, USR_DATE_CREAT, USR_DATE_LAST, USR_PASSWORD, USR_VERSION, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, USR_TIME_SHIFT, USR_OS, USR_IP_ADDRESS, USR_GENDER ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER AS USR ";
//$requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "CNT_CONTACT AS CNT ON ( CNT.ID_USER_1 = USR.ID_USER ) ";
switch ($only_status)
{
	case "w" : // 2
		//$requete .= " WHERE USR_CHECK = 'WAIT' or USR_STATUS = 2 ";
		$requete .= " WHERE USR_STATUS = 2 ";
		break;
	case "c" :  // 3
		//$requete .= " WHERE USR_CHECK = '' or USR_STATUS = 3 ";
		$requete .= " WHERE USR_STATUS = 3 ";
		break;
	case "v" : // 1
		//$requete .= " WHERE ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 ) ";
		$requete .= " WHERE USR_STATUS = 1 ";
		break;
}
/*if ($only_users != "")
{
	if (strstr($requete, "WHERE") != "")
    $requete .= " AND USR_USERNAME like '%" . $only_users . "%' ";
	else
    $requete .= " WHERE USR_USERNAME like '%" . $only_users . "%' ";
}*/
$requete .= " GROUP BY USR.ID_USER ";
//
switch ($tri)
{
	case "name" :
		$requete .= "ORDER BY UPPER(USR_NAME), UPPER(USR_USERNAME) ";
		break;
	case "etat" :
		$requete .= "ORDER BY USR_CHECK, UPPER(USR_USERNAME), UPPER(USR_NAME) ";
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
	default :
		$requete .= "ORDER BY UPPER(USR_USERNAME), UPPER(USR_NAME) ";
		break;
}
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A3b]", $requete);
$nb_row = mysqli_num_rows($result);
if ( $nb_row > 0 )
{
  if ( $nb_row > 15 )
    echo $alpha_link;
  else
    $alpha_link = "";
  //
  // Page défilement :
  echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>";
  if ($nb_row_by_page > 50)
  {
    echo "<TR><TD ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&level=" . $level . "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "");
    echo "</TD></TR>";
  }
  echo "<TR><TD>"; //
  //
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>\n";
	echo "<THEAD>";
	echo "<TR>";
		echo "<TH align='center' COLSPAN='11' class='thHead'>";
		/*if ($only_users != "")
      $title = "<b>" . $l_admin_users_searching . " </B>";
		else */
      $title = "<b>" . $l_admin_users_title . " </B>";
    /*
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
    }
    */
		$title .= " (" . $nb_row . ")";
		echo "<font face=verdana size=3>" . $title . "</font></TH>";
	echo "</TR>";
	echo "<TR>";
    $link_user_col = "<A HREF='users.php?level=" . $level . "&tri=&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>";
		//$larg_col = 160;
		//if ($nb_char_username > 11) $larg_col = 180;
		//if ($nb_char_username > 15) $larg_col = 210;
		//if ($nb_char_username > 18) $larg_col = 230;
		//if ($display_flag_country <> '') $larg_col = ($larg_col + 20);
		//if ($option_show_col_time == '') $larg_col = ($larg_col + 10); // bidouille quand la colonne time shit est masqué, ca réduit col user et action (pourquoi ?) sous Firefox 2 (à tester avec le 3).
		display_row_table($link_user_col, 230);
    //
		if ($option_show_col_name_function_level != '')
		{
			if ($level != '')
			{
		    $link_level_col = "<A HREF='users.php?level=level&tri=level&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_level . "' class='cattitle' >" . $l_admin_users_col_level . "</A>";
        display_row_table($link_level_col, '150'); // 200
      }
			else
			{
		    $link_fonction_col = "<A HREF='users.php?level=&tri=name&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_function . "' class='cattitle' >" . $l_admin_users_col_function . "</A>";
        display_row_table($link_fonction_col, '240');
      }
		}
		//
		if ($option_show_col_language != '')  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' width='16' height='16' ALT='" . $l_language . "' TITLE='" . $l_language . "' >", '30');
		//
    //
		if ($display_flag_country != "")
    {
      display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_country.png' width='16' height='16' ALT='" . $l_country . "' TITLE='" . $l_country . "' >", '40');
    }
    //
		if ($option_show_col_time != '')  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "time_shift.png' width='16' height='16' ALT='" . $l_time_zone . "' ALIGN='CENTER' TITLE='" . $l_time_zone . "' >", '55');
    //
    if ($option_show_col_last != '')
    {
      $link_last_col = "<A HREF='users.php?level=" . $level . "&tri=date_last&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_last . "' class='cattitle' >" . $l_admin_users_col_last . "</A>";
      display_row_table($link_last_col, '85');
    }
    //
    if ($option_show_col_create != '')
    {
      $link_creat_col = "<A HREF='users.php?level=" . $level . "&tri=date_creat&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_creat . "' class='cattitle' >" . $l_admin_users_col_creat . "</A>";
      display_row_table($link_creat_col, '87');
    }
    //
    if ($option_show_col_os != '') 
      display_row_table("OS", '30');
    //
	echo "</TR>";
	echo "</THEAD>\n";


	echo "<TFOOT>";
	// Dernière ligne : trier.
	echo "<TR>";
		echo "<TD align='center' COLSPAN='13' class='catBottom'>";
			echo "<font face=verdana size=2>";
			echo $l_order_by . " ";
			if ($tri == '') echo "<B>";
			echo "<A HREF='users.php?level=" . $level . "&tri=&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_login . "</A></B> - ";
			if ($option_show_col_name_function_level != '')
			{
        if ($tri == 'name') echo "<B>";
				echo "<A HREF='users.php?level=&tri=name&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_function . "</A></B> - ";
				if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') )
				{
          if ($tri == 'level') echo "<B>";
					echo "<A HREF='users.php?level=level&tri=level&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_level . "</A></B> - ";
				}
			}
			if ($option_show_col_create != "")
			{
        if ($tri == 'date_creat') echo "<B>";
        echo "<A HREF='users.php?level=" . $level . "&tri=date_creat&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_creat . "</A></B> - ";
      }
      //if ($tri != 'date_last') echo " - ";
			if ($tri == 'date_last') echo "<B>";
			echo " <A HREF='users.php?level=" . $level . "&tri=date_last&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_last . "</A></B>";
		echo "</TD>";
	echo "</TR>";
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
  while( list ($contact, $nom, $id_user, $usr_level, $usr_check, $usr_datcreat, $usr_datlast, $passcr, $version, $country_code, $language_code, $time_shit, $win_os, $ip, $usr_gender) = mysqli_fetch_row ($result) )
	{
    $row_num++;
    if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
    {
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
          if ($usr_gender == "M") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "man.png' WIDTH='16' HEIGHT='16' ALT='" . $l_man . "' TITLE='" . $l_man . "' BORDER='0'></A>";
          if ($usr_gender == "W") echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "woman.png' WIDTH='16' HEIGHT='16' ALT='" . $l_woman . "' TITLE='" . $l_woman . "' BORDER='0'></A>";
        }
        else
        {
          if ( ($display_flag_country != "") and ($option_show_col_country == "") )
          {
            if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
            {
              $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
              $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
              echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_country . " : " . $country_name . "' TITLE='" . $l_country . " : " . $country_name . "'>";
            }
          }
        }
       
        echo "&nbsp;<font face='verdana' size='2'>";
        echo "<B>";
        echo $contact . "</B>";
        echo "&nbsp;</font>";
      echo "</TD>";
      //
      if ($option_show_col_name_function_level != '')
      {
        if ($level != '')
        {
          echo "<FORM METHOD='GET' ACTION=''>";
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
          //echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
          //echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
          //echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
          //echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
          //echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' />";
          echo "</TD>";
          echo "</FORM>";
        }
        else
        {
          echo "<TD valign='left' VALIGN='MIDDLE' class='row1'>";
          echo "&nbsp;<font face='verdana' size='2'>";
          echo $nom;
          echo "</TD>";
        }
        //
      }
      echo "\n";
      //
      if ($option_show_col_language != '')
      {
        echo "<TD align='center' class='row1'>";
        if ($language_code != '')
        {
          if (is_readable("../images/flags/" . strtolower($language_code) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$language_code];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            $country_name = f_language_of_country($language_code, $country_name);
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($language_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_language . " : " . $country_name . "' TITLE='" . $l_language . " : " . $country_name . "'>&nbsp;";
          }
        }
        else
          echo "&nbsp;";
        echo "</TD>";
      }
      //
      //
      echo "\n";
      //
      //
      if ($option_show_col_country != '')
      {
        echo "<TD align='center' class='row2'>";
        if ( ($display_flag_country != "") and ($country_code != "") )
        {
          if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
          }
        }
        echo "</TD>";
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
            echo "<font face=verdana size=2>";
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
          echo "<font face=verdana size=2>";
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
          echo "<font face=verdana size=2>";
          if ( $datcreat != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $datcreat . "</font>";
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
      echo "</TR>";
      echo "\n";
    }
	}
	//
	echo "</TABLE>";
	echo "</TBODY>\n";
  //
  echo "</TD></TR><TR><TD ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&level=" . $level . "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "UP");
  echo "</TD></TR>";
  
  
  
  echo "<TR><TD></TD></TR>";
  echo "<TR><TD></TD></TR>";  // Espacement vertical
  
  
  echo "<TR><TD>";
  
      //echo "<SMALL><BR/></SMALL>";
  
      echo "<TABLE WIDTH='100%' cellspacing='0' cellpadding='0' BORDER='0'>";
      echo "<TR><TD WITH='50%' VALIGN='TOP'>";

  
    echo "</TD><TD WITH='50%' ALIGN='RIGHT' VALIGN='TOP'>\n";
  
  
    echo "</TD></TR>";
    echo "</TABLE>";

  
  echo "</TD></TR>";
  echo "</TABLE>";
}
/*
else
{
  if ($only_users != "")
    echo "<b>" . $l_admin_users_no_found . " </B> (" . $only_users . ")";
}
*/
//
//
mysqli_close($id_connect);
//
//
//
//
if ( (strlen($alpha_link) > 3)  and ($nb_row > 20) )
{
  echo "<font face=verdana size=2>";
  echo $alpha_link;
  echo "<BR/>";
}
//
//
echo "</body></html>";
?>
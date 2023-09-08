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
if (isset($_COOKIE['im_user_list_col_time'])) $option_show_col_time = $_COOKIE['im_user_list_col_time'];  else  $option_show_col_time = '1';
//if (isset($_COOKIE['im_user_list_col_action'])) $option_show_col_action = $_COOKIE['im_user_list_col_action'];  else  $option_show_col_action = '0';
if (isset($_COOKIE['im_user_list_col_language'])) $option_show_col_language = $_COOKIE['im_user_list_col_language'];  else  $option_show_col_language = '1';
if (isset($_COOKIE['im_user_list_col_name_function_level'])) $option_show_col_name_function_level = $_COOKIE['im_user_list_col_name_function_level'];  else  $option_show_col_name_function_level = '0';
$option_show_col_os = "1";
$option_show_col_version = "1";
$option_show_col_last = "1";
$option_show_col_create = "1";
$option_show_col_ip_address = '1';
if (intval($option_show_col_time) <= 0) $option_show_col_time = "";
//if (intval($option_show_col_action) <= 0) $option_show_col_action = "";
if (intval($option_show_col_language) <= 0) $option_show_col_language = "";
if (intval($option_show_col_name_function_level) <= 0) $option_show_col_name_function_level = "";
//if ( (intval($option_show_col_level) <= 0) and ($option_show_col_level != "?") ) $option_show_col_level = "";
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['level'])) $level = $_GET['level'];  else  $level = "";
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
//
echo "<font face=verdana size=2>";
//
//
$requete  = " SELECT USR_CHECK ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
$requete .= " GROUP by USR_CHECK ";
$requete .= " HAVING count(USR_CHECK) > 1 ";
$requete .= " ORDER by USR_CHECK ";
//
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A3b1]", $requete);
$list_check = "#";
$nb_row = 0; // important !
if ( mysqli_num_rows($result) > 0 )
{
  while( list ($check) = mysqli_fetch_row ($result) )
	{
    $list_check .= $check . "#";
  }
  $requete  = " SELECT USR_USERNAME, USR_NICKNAME, USR_NAME, USR.ID_USER, USR_LEVEL, USR_CHECK, USR_STATUS, USR_DATE_CREAT, USR_DATE_LAST, USR_PASSWORD, USR_VERSION, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, USR_TIME_SHIFT, USR_OS, USR_IP_ADDRESS, USR_GENDER, count( CNT.ID_USER_2) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER AS USR ";
  $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "CNT_CONTACT AS CNT ON ( CNT.ID_USER_1 = USR.ID_USER ) ";
  $requete .= " WHERE USR_CHECK <> '' and USR_CHECK <> 'WAIT' "; // pas de usr_status ici ! (car on cherche les doublons de vrais USR_CHECK !!!).
  $requete .= " GROUP BY USR.ID_USER ";
  $requete .= " ORDER by USR_CHECK, USR_USERNAME ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A3b2]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($contact, $nickname, $nom, $id_user, $usr_level, $usr_check, $usr_status, $usr_datcreat, $usr_datlast, $passcr, $version, $country_code, $language_code, $time_shit, $win_os, $ip, $usr_gender, $nb_contacts) = mysqli_fetch_row ($result) )
    {
      if (strstr($list_check, "#" .$usr_check . "#")) $nb_row++;
    }
  }
}
//
//
//
if ($nb_row > 0)
{
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A3b3]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<BR/>";
    // Page défilement :
    echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>";
    if ($nb_row_by_page > 50)
    {
      echo "<TR><TD COLSPAN='2' ALIGN='RIGHT'>";
      display_nb_page($page, $nb_row_by_page, $nb_row, "&level=" . $level . "&tri=" . $tri . "&lang=" . $lang . "&'", "");
      echo "</TD></TR>";
    }
    echo "<TR><TD COLSPAN='2'>"; //
    //
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>\n";
    echo "<THEAD>";
    echo "<TR>";
      echo "<TH align='center' COLSPAN='11' class='thHead'>";
      $title = "<b>" . $l_admin_users_title . " - </B>" . $l_menu_list_users_double;
      $title .= " (" . $nb_row . ")";
      echo "<font face=verdana size=3>" . $title . "</font></TH>";
    echo "</TR>";
    echo "<TR>";
      display_row_table("#", '20');
      if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") )
        display_row_table($l_admin_session_col_ip, '160');
      else
        display_row_table($l_admin_session_col_ip, '130');
      //
      display_row_table($l_admin_users_col_user, 260);
      //
      if ($option_show_col_name_function_level != '')
      {
        if ($level != '')
        {
          display_row_table($l_admin_users_col_level, '150'); // 200
        }
        else
        {
          display_row_table($l_admin_users_col_function, '161'); // 220      // 160 : pas sous Vista-Firefox
        }
      }
      //
      if ( ($option_show_col_language != '') and ($display_flag != '') )  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' width='16' height='16' ALT='" . $l_language . "' TITLE='" . $l_language . "' >", '30');
  //		if ($display_flag != "")
  //    {
  //      display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_country.png' width='16' height='16' ALT='" . $l_country . "' TITLE='" . $l_country . "' >", '40');
  //    }
      //
      display_row_table($l_admin_users_col_etat, '30');
      //
      /*
      if ( $option_show_col_action != "")
      {
        if (_USER_NEED_PASSWORD !='')
          display_row_table($l_admin_users_col_action, '70');
        else
          display_row_table($l_admin_users_col_action, '60'); // 80
      }
      */
      //
      if ($option_show_col_time != '')  display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "time_shift.png' width='16' height='16' ALT='" . $l_time_zone . "' ALIGN='CENTER' TITLE='" . $l_time_zone . "' >", '55');
      //
      if ($option_show_col_last != '')
      {
        display_row_table($l_admin_users_col_last, '85');
      }
      //
      if ($option_show_col_create != '')
      {
        display_row_table($l_admin_users_col_creat, '87');
      }
      //
      if ($option_show_col_version != '') 
        display_row_table($l_admin_users_col_version, '60');
      //
      if ($option_show_col_os != '') 
        display_row_table("OS", '30');
      //
    echo "</TR>";
    echo "</THEAD>\n";

    echo "\n";
    echo "<TBODY>";
    //
    $last_check = ""; // important !!!
    $last_first_letter = "";
    $row_num = 0;
    $check_num = 0;
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
    while( list ($contact, $nickname, $nom, $id_user, $usr_level, $usr_check, $usr_status, $usr_datcreat, $usr_datlast, $passcr, $version, $country_code, $language_code, $time_shit, $win_os, $ip, $usr_gender, $nb_contacts) = mysqli_fetch_row ($result) )
    {
      if (strstr($list_check, "#" .$usr_check . "#"))
      {
        $row_num++;
        if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
        {
          if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $contact = $nickname;
          //
          if (($last_check != $usr_check) and ($last_check != '') )
          {
            echo "<TR>";
            echo "<TD class='row2' COLSPAN='11'> </TD>";
            echo "</TR>";
          }
          //
          echo "<TR>";
          if ($last_check != $usr_check)
          {
            $check_num++;
            echo "<TD align='center' class='row1'>";
            echo "<font face=verdana size=1 color='gray'>";
            echo $check_num;
            echo "</font></TD>";
          }
          else
          {
            echo "<TD class='row2'>&nbsp;</TD>";
          }
          //
          if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") and ($country_code != "") )
          {
            echo "<TD align='left' class='row1'>";
            if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
            {
              $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
              $country_name = f_quote($GEOIP_COUNTRY_NAMES[$country_id]);
              echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
            }
          }
          else
            echo "<TD align='center' class='row1'>";
            //
          $last_check = $usr_check;
          if ($hide_ip == '') 
            echo "<font face=verdana size=2>" . $ip;
          else
            echo "<font face=verdana size=1 color='gray'><I>Not in demo version </I></font>";
          echo "</font></TD>";
          //
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
              if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") and ($option_show_col_ip_address == "") )
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
            echo "<A " . $plus . " HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
            echo $contact . "</A>";
            if (intval($nb_contacts) > 0) echo "<SMALL> (<acronym title='" . $l_admin_contacts . " : " . $nb_contacts . "'>" . $nb_contacts . "</acronym>)</SMALL>";
            echo "&nbsp;</font>";
          echo "</TD>";
          //
          if ($option_show_col_name_function_level != '')
          {
            if ($level != '')
            {
              echo "<FORM METHOD='GET' ACTION='user_level.php?'>";
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
              echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
              echo "&nbsp;<font face='verdana' size='2'>";
              echo $nom;
              echo "</TD>";
            }
            //
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
          //
          echo "<TD align='center' class='row1'>";
          if ($usr_status == 2) $usr_check = "WAIT";
          if ($usr_status == 3) $usr_check = "";
          if ($usr_status == 9) $usr_check = "LEAVE";
          switch ($usr_check)
          {
            case "WAIT" : // 2
              echo "<IMG SRC='" . _FOLDER_IMAGES . "wait.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_wait_valid . "' TITLE='" . $l_admin_users_info_wait_valid . "'>";
              break;
            case "LEAVE" : // 9
              echo "<IMG SRC='" . _FOLDER_IMAGES . "b_leave.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_leave . "' TITLE='" . $l_admin_users_info_leave . "'>";
              break;
            case "" : // 3
              echo "<IMG SRC='" . _FOLDER_IMAGES . "use_up.gif' WIDTH='16' HEIGHT='20' ALT='" . $l_admin_users_info_change_ok . "' TITLE='" . $l_admin_users_info_change_ok . "'>";
              break;
            default : // 1
              echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_info_valid . "' TITLE='" . $l_admin_users_info_valid . "'>";
              break;
          }
          echo "</TD>";
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
          if ($option_show_col_version != '')
          {
            echo "<TD align='center' class='row2'>";
              if ($version != '')
              {
                echo "<font face=verdana size=2>";
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
          echo "</TR>";
          echo "\n";
        }
      }
    }
    //
    echo "</TABLE>";
    echo "</TBODY>\n";
    //
    echo "</TD></TR>";
    echo "<TR><TD>";
    /*
    if ($nb_row > $nb_row_by_page)
    {
      //echo "<font face=verdana size=2>";
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
    */
    echo "</TD><TD ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&level=" . $level . "&tri=" . $tri . "&lang=" . $lang . "&'", "UP");
    echo "</TD></TR>";
    
    
    
    echo "<TR><TD></TD></TR>";
    echo "<TR><TD></TD></TR>";  // Espacement vertical
    
    
    echo "<TR><TD COLSPAN='2'>";
    

    
    echo "</TD></TR>";
    echo "</TABLE>";
  }
}
else
{
  echo "<BR/>";
  echo "<div class='info'>";
  echo $l_admin_acp_admin_list_empty;
  echo "</div>";
}
//
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
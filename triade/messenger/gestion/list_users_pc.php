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
if (isset($_COOKIE['im_user_pc_list_col_os'])) $option_show_col_os = $_COOKIE['im_user_pc_list_col_os'];  else  $option_show_col_os = '1';
if (isset($_COOKIE['im_user_pc_list_col_ooo'])) $option_show_col_ooo = $_COOKIE['im_user_pc_list_col_ooo'];  else  $option_show_col_ooo = '0';
if (isset($_COOKIE['im_user_pc_list_col_last'])) $option_show_col_last = $_COOKIE['im_user_pc_list_col_last'];  else  $option_show_col_last = '0';
if (isset($_COOKIE['im_user_pc_list_col_browser'])) $option_show_col_browser = $_COOKIE['im_user_pc_list_col_browser'];  else  $option_show_col_browser = '1';
if (isset($_COOKIE['im_user_pc_list_col_mac_adr'])) $option_show_col_mac_adr = $_COOKIE['im_user_pc_list_col_mac_adr'];  else  $option_show_col_mac_adr = '0';
if (isset($_COOKIE['im_user_pc_list_col_version'])) $option_show_col_version = $_COOKIE['im_user_pc_list_col_version'];  else  $option_show_col_version = '0';
if (isset($_COOKIE['im_user_pc_list_col_username'])) $option_show_col_username = $_COOKIE['im_user_pc_list_col_username'];  else  $option_show_col_username = '1';
if (isset($_COOKIE['im_user_pc_list_col_ip_address'])) $option_show_col_ip_address = $_COOKIE['im_user_pc_list_col_ip_address'];  else  $option_show_col_ip_address = '1';
if (isset($_COOKIE['im_user_pc_list_col_screen_size'])) $option_show_col_screen_size = $_COOKIE['im_user_pc_list_col_screen_size'];  else  $option_show_col_screen_size = '1';
if (isset($_COOKIE['im_user_pc_list_col_emailclient'])) $option_show_col_emailclient = $_COOKIE['im_user_pc_list_col_emailclient'];  else  $option_show_col_emailclient = '1';
if (isset($_COOKIE['im_user_pc_list_col_computername'])) $option_show_col_computername = $_COOKIE['im_user_pc_list_col_computername'];  else  $option_show_col_computername = '1';
if (isset($_COOKIE['im_user_pc_list_col_name_function'])) $option_show_col_name_function = $_COOKIE['im_user_pc_list_col_name_function'];  else  $option_show_col_name_function = '0';
if (isset($_COOKIE['im_user_pc_list_show_select_cols'])) $im_user_pc_list_show_select_cols = $_COOKIE['im_user_pc_list_show_select_cols'];  else  $im_user_pc_list_show_select_cols = '1';
if (intval($option_show_col_os) <= 0) $option_show_col_os = "";
if (intval($option_show_col_ooo) <= 0) $option_show_col_ooo = "";
if (intval($option_show_col_last) <= 0) $option_show_col_last = "";
if (intval($option_show_col_browser) <= 0) $option_show_col_browser = "";
if (intval($option_show_col_mac_adr) <= 0) $option_show_col_mac_adr = "";
if (intval($option_show_col_version) <= 0) $option_show_col_version = "";
if (intval($option_show_col_username) <= 0) $option_show_col_username = "";
if (intval($option_show_col_ip_address) <= 0) $option_show_col_ip_address = "";
if (intval($option_show_col_screen_size) <= 0) $option_show_col_screen_size = "";
if (intval($option_show_col_emailclient) <= 0) $option_show_col_emailclient = "";
if (intval($option_show_col_computername) <= 0) $option_show_col_computername = "";
if (intval($option_show_col_name_function) <= 0) $option_show_col_name_function = "";
if (intval($im_user_pc_list_show_select_cols) <= 0) $im_user_pc_list_show_select_cols = "";
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['only_users'])) $only_users = $_GET['only_users'];  else  $only_users = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
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
echo "<title>[IM] " . $l_admin_users_pc_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="60;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
if ( _ENTERPRISE_SERVER != '' )
{
  require ("../common/sql.inc.php");
  //
  $only_users = f_clean_username($only_users);
  //
  $display_flag = "";
  if (_FLAG_COUNTRY_FROM_IP != "")
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
  //  | A | B | C |...
  $alpha_link = "";
  if ( ($tri == "") and ($nb_row_by_page > 50) )
  {
    $requete = " select distinct(LEFT(UPPER(USR_USERNAME), 1)) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE USR_COMPUTERNAME <> '' ";
    switch ($only_status)
    {
      case "w" :
        //$requete .= " WHERE (USR_CHECK = 'WAIT' or USR_STATUS = 2) ";
        $requete .= " WHERE USR_STATUS = 2 ";
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
    if ($only_users != "")  $requete .= " AND USR_USERNAME like '%" . $only_users . "%' ";
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
  //
  $nb_char_username = 0;
  $requete  = " select USR_USERNAME ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A3c]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($username) = mysqli_fetch_row ($result) )
    {
      if (strlen($username) > $nb_char_username) $nb_char_username = strlen($username);
    }
  }
  //
  //
  $requete  = " SELECT ID_USER, USR_ONLINE, USR_USERNAME, USR_NICKNAME, USR_NAME, USR_DATE_LAST, USR_VERSION, USR_IP_ADDRESS, USR_COUNTRY_CODE, ";
  $requete .= " USR_OS, USR_COMPUTERNAME, USR_MAC_ADR, USR_SCREEN_SIZE, USR_EMAIL_CLIENT, USR_BROWSER, USR_OOO";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE USR_COMPUTERNAME <> '' ";
  switch ($only_status)
  {
    case "w" :
      //$requete .= " WHERE (USR_CHECK = 'WAIT' or USR_STATUS = 2) ";
      $requete .= " WHERE USR_STATUS = 2 ";
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
  if ($only_users != "")  $requete .= " AND USR_USERNAME like '%" . $only_users . "%' ";
  //
  switch ($tri)
  {
    case "pc" :
      $requete .= "ORDER BY UPPER(USR_COMPUTERNAME), UPPER(USR_USERNAME) ";
      break;
    case "name" :
      $requete .= "ORDER BY UPPER(USR_NAME), UPPER(USR_USERNAME) ";
      break;
    case "date_last" :
      $requete .= "ORDER BY USR_DATE_LAST DESC, UPPER(USR_USERNAME) ";
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
    echo "<BR/>";
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
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>\n";
    echo "<THEAD>";
    echo "<TR>";
      echo "<TH align='center' COLSPAN='13' class='thHead'>";
      if ($only_users != "")
        $title = "<b>" . $l_admin_users_searching . " </B>";
      else
        $title = "<b>" . $l_admin_users_pc_title . " </B>";
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
      $title .= " (" . $nb_row . ")";
      echo "<font face=verdana size=3>" . $title . "</font></TH>";
    echo "</TR>";
    echo "<TR>";
      display_row_table($l_admin_users_col_etat, '30');
      //
      $link_user_col = "<A HREF='list_users_pc.php?tri=&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_login . "' class='cattitle' >" . $l_admin_users_col_user . "</A>";
      $larg_col = 160;
      if ($nb_char_username > 11) $larg_col = 180;
      if ($nb_char_username > 15) $larg_col = 210;
      if ($nb_char_username > 18) $larg_col = 230;
      if ( (_FLAG_COUNTRY_FROM_IP <> '') and ($display_flag != '') ) $larg_col = ($larg_col + 20);
      if ($option_show_col_username != '') display_row_table($link_user_col, $larg_col);
      //
      if ($option_show_col_name_function != '')
      {
        $link_fonction_col = "<A HREF='list_users_pc.php?tri=name&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_function . "' class='cattitle' >" . $l_admin_users_col_function . "</A>";
        display_row_table($link_fonction_col, '161'); // 220      // 160 : pas sous Vista-Firefox
      }
      //
      if ($option_show_col_computername != '') 
      {
        $link_pc_col = "<A HREF='list_users_pc.php?tri=pc&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_col_pc . "' class='cattitle' >" . $l_admin_users_col_pc . "</A>";
        display_row_table($link_pc_col, '140');
      }
      //
      //$link_state_col = "&nbsp;<A HREF='list_users_pc.php?tri=etat&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_state . "' class='cattitle' >" . $l_admin_users_col_etat . "</A>&nbsp;";
      //display_row_table($link_state_col, '30');
      //
      if ($option_show_col_ip_address != '')
      {
        if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') )
          display_row_table($l_admin_session_col_ip, '160');
        else
          display_row_table($l_admin_session_col_ip, '130');
      }
      //
      if ($option_show_col_mac_adr != '')  display_row_table($l_admin_users_col_mac_adr, '110');
      //
      if ($option_show_col_os != '') display_row_table("OS", '30');
      if ($option_show_col_screen_size != '') display_row_table($l_admin_users_col_screen, '70');
      if ($option_show_col_emailclient != '') display_row_table($l_admin_users_col_emailclient, '160');
      if ($option_show_col_browser != '') display_row_table($l_admin_users_col_browser, '180');
      if ($option_show_col_ooo != '') display_row_table($l_admin_users_col_ooo, '30');
      //
      if ($option_show_col_last != '')
      {
        $link_last_col = "<A HREF='list_users_pc.php?tri=date_last&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_users_order_last . "' class='cattitle' >" . $l_admin_users_col_last . "</A>";
        display_row_table($link_last_col, '85');
      }
      //
      if ($option_show_col_version != '') 
        display_row_table($l_admin_users_col_version, '60');
      //
    echo "</TR>";
    echo "</THEAD>\n";


    echo "<TFOOT>";
    //
    // Dernière ligne : trier.
    echo "<TR>";
      echo "<TD align='center' COLSPAN='13' class='catBottom'>";
        echo "<font face=verdana size=2>";
        echo $l_order_by . " ";
        if ($tri == '') echo "<B>";
        echo "<A HREF='list_users_pc.php?tri=&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_login . "</A></B> - ";
        if ($tri == 'name') echo "<B>";
        echo "<A HREF='list_users_pc.php?tri=name&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_function . "</A></B> - ";
        if ($tri == 'pc') echo "<B>";
        echo "<A HREF='list_users_pc.php?tri=pc&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_col_pc . "</A></B> - ";
        //if ($tri != 'date_last') echo " - ";
        if ($tri == 'date_last') echo "<B>";
        echo " <A HREF='list_users_pc.php?tri=date_last&page=" . $page . "&lang=" . $lang . "&'>" . $l_admin_users_order_last . "</A></B>";
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
    while( list ($id_user, $usr_online, $username, $nickname, $nom, $usr_datlast, $version, $ip, $country_code, $win_os, $computername, $mac_adr, $screen_size, $emailclient, $browser, $ooo) = mysqli_fetch_row ($result) )
    {
      $row_num++;
      if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
      {
        if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
        //
        echo "<TR>";
        echo "<TD class='row1' align='CENTER'>";
        switch ($usr_online)
        {
          case "0" : // 
            echo "<IMG SRC='" . _FOLDER_IMAGES . "state_off.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_state_off . "' TITLE='" . $l_admin_users_state_off . "'>";
            break;
          case "1" : // 
            echo "<IMG SRC='" . _FOLDER_IMAGES . "state_on.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_state_on . "' TITLE='" . $l_admin_users_state_on . "'>";
            break;
          case "2" : // 
            echo "<IMG SRC='" . _FOLDER_IMAGES . "state_eject.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_users_state_sleep . "' TITLE='" . $l_admin_users_state_sleep . "'>";
            break;
        }
        echo "</TD>";
        //
        if ($option_show_col_username != '')
        {
          echo "<TD class='row1'>";
            $plus = "";
            if ($tri == "")
            {
              $t = strtoupper(substr($username, 0, 1));
              if ($t != $last_first_letter)
              {
                $last_first_letter = $t;
                $plus = " ID=" . $t;
              }
            }
            if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') and ($option_show_col_ip_address == "") )
            {
              if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
              {
                $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
                $country_name = f_quote($GEOIP_COUNTRY_NAMES[$country_id]);
                echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_country . " : " . $country_name . "' TITLE='" . $l_country . " : " . $country_name . "'>";
              }
            }
           
            echo "&nbsp;<font face='verdana' size='2'>";
            echo "<A " . $plus . " HREF='user.php?id_user=" . $id_user . "&lang=" . $lang . "&only_status=" . $only_status . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
              //echo "<A " . $plus . " HREF='list_contact.php?only_one=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
              //echo "<A " . $plus . " HREF='messagerie.php?id_user_select=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
            echo $username . "</A>";
            //if (intval($nb_contacts) > 0) echo "<SMALL> (<acronym title='" . $l_admin_contacts . " : " . $nb_contacts . "'>" . $nb_contacts . "</acronym>)</SMALL>";
            echo "&nbsp;</font>";
          echo "</TD>";
        }
        //
        //
        if ($option_show_col_name_function != '')
        {
          echo "<TD align='left' class='row2'>";
          echo "<font face=verdana size=2>&nbsp;" . $nom;
          echo "</TD>";
        }
        //
        //
        if ($option_show_col_computername != '')
        {
          echo "<TD align='left' class='row1'>";
          echo "<font face=verdana size=2>&nbsp;" . $computername;
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
            echo "<font face=verdana size=2>" . $ip;
          else
            echo "<font face=verdana size=1 color='gray'><I>Not in demo version </I></font>";
          echo "</font></TD>";
        }
        //
        //
        if ($option_show_col_mac_adr != '')
        {
          echo "<TD align='left' class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;" . $mac_adr . "&nbsp;";
          echo "</TD>";
        }
        echo "\n";
        //
        //
        if ($option_show_col_os != '')
        {
          echo "<TD align='center' class='row2'>";
            display_os_picture($win_os);
          echo "</TD>";
        }
        //
        //
        if ($option_show_col_screen_size != '')
        {
          echo "<TD align='center' class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;" . $screen_size . "&nbsp;";
          echo "</TD>";
        }
        //
        //
        if ($option_show_col_emailclient != '')
        {
          echo "<TD align='left' class='row2'>";
          echo "<font face='verdana' size='1'>&nbsp;" . f_reduce_emailclient_name($emailclient) . "&nbsp;";
          echo "</TD>";
        }
        //
        //
        if ($option_show_col_browser != '')
        {
          echo "<TD align='left' class='row2'>";
          display_browser_picture($browser);
          echo "<font face='verdana' size='1'>&nbsp;" . f_reduce_browser_name($browser) . "&nbsp;";
          echo "</TD>";
        }
        //
        //
        if ($option_show_col_ooo != '')
        {
          echo "<TD align='center' class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;" . $ooo . "&nbsp;";
          echo "</TD>";
        }
        //
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
        echo "</TR>";
        echo "\n";
      }
    }
    //
    echo "</TABLE>";
    echo "</TBODY>\n";
    //
    echo "</TD></TR>";
    echo "<TR><TD>";
    //if ($nb_row > $nb_row_by_page)
    if ($nb_row > 15)
    {
      echo "<font face=verdana size=2>";
      echo $l_rows_per_page . " : ";
      display_nb_row_page(15, $nb_row_by_page, "list_users_pc_nb_rows");
      echo " | ";
      display_nb_row_page(20, $nb_row_by_page, "list_users_pc_nb_rows");
      echo " | ";
      display_nb_row_page(25, $nb_row_by_page, "list_users_pc_nb_rows");
      echo " | ";
      display_nb_row_page(30, $nb_row_by_page, "list_users_pc_nb_rows");
      echo " | ";
      display_nb_row_page(50, $nb_row_by_page, "list_users_pc_nb_rows");
    }
    echo "</TD><TD ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "UP");
    echo "</TD></TR>";
    
    
    
    echo "<TR><TD></TD></TR>";
    echo "<TR><TD></TD></TR>";  // Espacement vertical
    
    
    echo "<TR><TD COLSPAN='2'>";
    
        //echo "<SMALL><BR/></SMALL>";
    

    
            echo "<table cellspacing='1' cellpadding='1' class='forumline'>"; // width='650' 
            echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
            echo "<TR>";
            echo "<TD class='catHead' align='center'>";
              echo "<FONT size='2'>&nbsp;<B>" . $l_display_col;
              if ($im_user_pc_list_show_select_cols > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_users_pc_show_select_cols&im_user_pc_list_show_select_cols=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_users_pc_show_select_cols&im_user_pc_list_show_select_cols=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
            echo "</TD>";
            echo "</TR>";
            if ($im_user_pc_list_show_select_cols > 0)
            {
              echo "<TR>";
              echo "<td class='row1'>";
              echo "<FONT size='2'>";


              echo "<INPUT name='option_show_col_username' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_username <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_user . "<BR/>\n";

              echo "<INPUT name='option_show_col_name_function' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_name_function <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_function . "<BR/>\n";

              echo "<INPUT name='option_show_col_computername' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_computername <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_pc . "<BR/>\n";

              echo "<INPUT name='option_show_col_ip_address' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_ip_address <> '') echo "CHECKED";
              echo " />" . $l_admin_session_col_ip . "<BR/>\n";

              echo "<INPUT name='option_show_col_mac_adr' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_mac_adr <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_mac_adr . "<BR/>\n";

              echo "<INPUT name='option_show_col_os' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_os <> '') echo "CHECKED";
              echo " />OS <SMALL><SMALL>(Operating System)</SMALL></SMALL><BR/>\n";
                  
              echo "<INPUT name='option_show_col_screen_size' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_screen_size <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_screen . "<BR/>\n";

              echo "<INPUT name='option_show_col_emailclient' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_emailclient <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_emailclient . "<BR/>\n";

              echo "<INPUT name='option_show_col_browser' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_browser <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_browser . "<BR/>\n";

              echo "<INPUT name='option_show_col_ooo' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
              if ($option_show_col_ooo <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_ooo . "<BR/>\n";

              echo "<INPUT name='option_show_col_last' TYPE='CHECKBOX' VALUE='1'  class='genmed' ";
              if ($option_show_col_last <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_last . "<BR/>\n";

              echo "<INPUT name='option_show_col_version' TYPE='CHECKBOX' VALUE='3' class='genmed' ";
              if ($option_show_col_version <> '') echo "CHECKED";
              echo " />" . $l_admin_users_col_version . "<BR/>\n";

              echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
              echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
              echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
              //echo "<input type='hidden' name='action' value = 'list_users_pc_show_select_cols' />"; // les paramètres de cette page, et y revenir ensuite
              echo "<input type='hidden' name='action' value = 'list_users_pc' />"; // les paramètres de cette page, et y revenir ensuite
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

    

    
    echo "</TD></TR>";
    echo "</TABLE>";
  }
  else
  {
    if ($only_users != "")
      echo "<b>" . $l_admin_users_no_found . " </B> (" . $only_users . ")";
  }
  //
  //
  //
  //
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
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_mess_cannot_order . "<BR/>";
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
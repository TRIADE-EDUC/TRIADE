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
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else $tri = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else $only_status = "";
if (isset($_GET['only_one'])) $only_one = intval($_GET['only_one']);  else $only_one = 0;
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_user_contacts);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_contact_title . "</title>";
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
//
$display_flag = "";
#if (_FLAG_COUNTRY_FROM_IP != "")
#{
	if (is_readable("../common/library/geoip/geoip_2.inc.php"))
	{
		require("../common/library/geoip/geoip_2.inc.php");
		$display_flag = "X";
  }
#}
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
$requete = "SELECT count(*) FROM " . $PREFIX_IM_TABLE . "USR_USER ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A1a]", $requete);
if ( mysqli_num_rows($result) == 1 ) list($nb_users) = mysqli_fetch_row($result);
//
//$requete  = " SELECT distinct(CNT.ID_USER_1) ";
$requete  = " SELECT count(CNT.ID_USER_1) ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER US1, " . $PREFIX_IM_TABLE . "USR_USER US2 ";
$requete .= " WHERE US1.ID_USER = CNT.ID_USER_1 and US2.ID_USER = CNT.ID_USER_2 ";
$requete .= " AND CNT.CNT_STATUS > 0 ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A1b]", $requete);
//$nb_user_uniq_contact = mysqli_num_rows($result);
list ($nb_user_uniq_contact) = mysqli_fetch_row ($result);
//
$nb_status_wait_valid = 0;
$nb_status_ok = 0;
$nb_status_vip = 0;
$nb_status_hidden = 0;
$nb_status_refused = 0;
$nb_status_tot_wait_valid = 0;
$nb_status_tot_ok = 0;
$nb_status_tot_vip = 0;
$nb_status_tot_hidden = 0;
$nb_status_tot_refused = 0;
//
//  | A | B | C |...
$alpha_link = "";
if ( ($tri == "") and ($nb_row_by_page > 50) and ($only_one <= 0) )
{
	$requete  = " SELECT distinct(LEFT(UPPER(USR.USR_USERNAME), 1)) ";
	$requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER USR ";
	$requete .= " WHERE USR.ID_USER = CNT.ID_USER_1 ";
  switch ($only_status)
  {
    case "w" :
      $requete .= " and CNT_STATUS = 0 ";
      break;
    case "o" :
      $requete .= " and CNT_STATUS = 1 ";
      break;
    case "v" :
      $requete .= " and CNT_STATUS = 2 ";
      break;
    case "h" :
      $requete .= " and CNT_STATUS = 5 ";
      break;
    case "r" :
      $requete .= " and CNT_STATUS = -1 ";
      break;
  }
	$requete .= " order by LEFT(UPPER(USR.USR_USERNAME), 1) ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-A1c]", $requete);
	if ( mysqli_num_rows($result) > 3 )
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
//
// --------------------- IF ONLY ON SELECTED ---------------------
if ($only_one > 0)
{
  $requete  = " select USR_USERNAME, USR_NICKNAME, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, USR_AVATAR, USR_TIME_SHIFT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE ID_USER = " . $only_one . " ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A1e]", $requete);
  //
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($name_only_one, $nickname, $country_code, $language_code, $avatar, $time_shift) = mysqli_fetch_row ($result);
    /*
    if ($avatar == "") $avatar = $name_only_one . ".jpg";
   	if (is_readable("../distant/avatar/" . $avatar))
       echo "<IMG SRC='../distant/avatar/" . $avatar . "' /><BR/>";
     else
       $avatar = "";
    */
    if ($nickname != "") $name_only_one = $nickname;
    echo "<font face='arial,verdana' size='5'><B>";
    echo $name_only_one . "</B></font><BR/>";
    echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
  }
}
//
//
echo "<font face=verdana size=2>";
//echo "<BR/>";
echo $alpha_link;
//
$requete  = " SELECT CNT_STATUS, ID_CONTACT, US1.USR_USERNAME, US1.USR_NICKNAME, US2.USR_USERNAME, US2.USR_NICKNAME, CNT.CNT_NEW_USERNAME, US1.ID_USER, US2.ID_USER, US1.USR_COUNTRY_CODE, US2.USR_COUNTRY_CODE, US1.USR_LANGUAGE_CODE, US2.USR_LANGUAGE_CODE "; // ,  USR.USR_NAME,  
$requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER US1, " . $PREFIX_IM_TABLE . "USR_USER US2 ";
$requete .= " WHERE US1.ID_USER = CNT.ID_USER_1 and US2.ID_USER = CNT.ID_USER_2 ";
if (intval($only_one) > 0)
  $requete .= " and US1.ID_USER = " . $only_one;
//
switch ($only_status)
{
	case "w" :
		$requete .= " and CNT_STATUS = 0 ";
		break;
	case "o" :
		$requete .= " and CNT_STATUS = 1 ";
		break;
	case "v" :
		$requete .= " and CNT_STATUS = 2 ";
		break;
	case "h" :
		$requete .= " and CNT_STATUS = 5 ";
		break;
	case "r" :
		$requete .= " and CNT_STATUS = -1 ";
		break;
}
$requete .= " ORDER BY UPPER(US1.USR_USERNAME), CNT_STATUS, UPPER(US2.USR_USERNAME) ";
//
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A1d]", $requete);
$nb_row = mysqli_num_rows($result);
//
if ( ($nb_row > 0) and (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') )
{
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
		echo "<TH align=center COLSPAN='7' class='thHead'>";
		$title = $l_admin_contact_title . "</b> (";
    switch ($only_status)
    {
      case "w" :
        $title .= "<SMALL>". $l_admin_contact_info_wait_valid . " : ";
        break;
      case "o" :
        $title .= "<SMALL>". $l_admin_contact_info_ok . " : ";
        break;
      case "v" :
        $title .= "<SMALL>". $l_admin_contact_info_vip . " : ";
        break;
      case "h" :
        $title .= "<SMALL>". $l_admin_contact_info_hidden . " : ";
        break;
      case "r" :
        $title .= "<SMALL>". $l_admin_contact_info_refused . " : ";
        break;
    }
		$title .= $nb_row ."</SMALL>) ";
		echo "<font face=verdana size=3><b>" . $title . "</font></TH>";
	echo "</TR>";
	echo "<TR>";
    display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' ALT='" . $l_language . "' TITLE='" . $l_language . "' >", '30');
		display_row_table($l_admin_users_col_user, '210'); //
    display_row_table("<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' ALT='" . $l_language . "' TITLE='" . $l_language . "' >", '30');
		display_row_table($l_admin_contact_col_contact, '290');
		display_row_table("&nbsp;" . $l_admin_contact_col_state . "&nbsp;", '');
		echo "<TD align='center' width='' COLSPAN='2' class='catHead'><font face=verdana size=2>&nbsp;<b>" . $l_admin_contact_col_action . "</b>&nbsp;</font></TD>";
	echo "</TR>";
	echo "</THEAD>";
	//
	if ( ($nb_row > 10) and ($only_status == '') and (intval($only_one) <= 0) )
	{
    echo "<TFOOT>";
    echo "<TR>";
      echo "<TD align='center' COLSPAN='7' class='catBottom'>";
        echo "<font face=verdana size=2>";
        echo $l_admin_contact_average_1 . " : " . round($nb_user_uniq_contact / $nb_users, 2) . " " . $l_admin_contact_average_2 . " (" . $l_admin_contact_total . " : " . round($nb_row / $nb_users, 2) . ") ";
        //echo "<BR/>";
        //echo $l_admin_contact_average_1 . " : " . round($nb_row / $nb_user_uniq_contact, 2) . " " . $l_admin_contact_average_2 . " (" . $l_admin_contact_total . " : " . round($nb_row / $nb_users, 2) . ") ";
      echo "</TD>";
    echo "</TR>";
    echo "</TFOOT>";
  }
	//
  echo "\n";
	echo "<TBODY>";
	//
  $last_user = "";
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
	while( list ($priv, $id_contact, $username_1, $nickname_1, $username_2, $nickname_2, $pseudo, $id_user_1, $id_user_2, $country_code_1, $country_code_2, $language_code_1, $language_code_2) = mysqli_fetch_row ($result) )
	{
    $row_num++;
    if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
    {
      if ( ($nickname_1 != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username_1 = $nickname_1;
      if ( ($nickname_2 != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username_2 = $nickname_2;
      //
      if (($username_1 != $last_user) and ($last_user != '') )
      {
        echo "<TR>";
        echo "<TD class='row2' COLSPAN='7'> </TD>";
        echo "</TR>";
      }
      echo "<TR>";
      //
      if ($username_1 != $last_user)
      {
        // Language
        echo "<TD class='row1' align='center'>";
          if ($language_code_1 != '')
          {
            if (is_readable("../images/flags/" . strtolower($language_code_1) . ".png")) 
            {
              if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') )
              {
                $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$language_code_1];
                $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
                $country_name = f_language_of_country($language_code_1, $country_name);
              }
              else
                $country_name = "";
              echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($language_code_1) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_language . " : " . $country_name . "' TITLE='" . $l_language . " : " . $country_name . "'>&nbsp;";
            }
          }
        echo "</TD>";
        //
        echo "<TD class='row1'>";
        $last_user = $username_1;
        if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") )
        {
          if (is_readable("../images/flags/" . strtolower($country_code_1) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code_1];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code_1) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_country . " : " . $country_name . "' TITLE='" . $l_country . " : " . $country_name . "'>";
          }
        }
        //echo "<font face=verdana size=2>&nbsp;<B>" . $username_1 . "</B>&nbsp;</font>";
        echo "<font face=verdana size=2>&nbsp;";
        echo "<A HREF='user.php?id_user=" . $id_user_1 . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
        //echo "<A HREF='list_contact.php?only_one=" . $id_user_1 . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
          //echo "<A " . $plus . " HREF='messagerie.php?id_user_select=" . $id_user . "&lang=" . $lang . "&' alt='" . $l_clic_for_message . "' title='" . $l_clic_for_message . "' class='cattitle'>";
        echo $username_1 . "</A>&nbsp;</font>";
        //
        echo "</TD>";
      }
      else
      {
        echo "<TD class='row2'>&nbsp;</TD>";
        echo "<TD class='row2'>&nbsp;</TD>";
      }
      //
      // Language
      echo "<TD class='row1' align='center'>";
        if ($language_code_2 != '')
        {
          if (is_readable("../images/flags/" . strtolower($language_code_2) . ".png")) 
          {
            if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != '') )
            {
              $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$language_code_2];
              $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
              $country_name = f_language_of_country($language_code_2, $country_name);
            }
            else
              $country_name = "";
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($language_code_2) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_language . " : " . $country_name . "' TITLE='" . $l_language . " : " . $country_name . "'>&nbsp;";
          }
        }
      echo "</TD>";
      //
      echo "<TD class='row1'>";
        if ($tri == "")
        {
          $t = strtoupper(substr($username_1, 0, 1));
          if ($t != $last_first_letter)
          {
            $last_first_letter = $t;
            echo "<BALISE ID=" . $t . "></BALISE>";
          }
        }
        //
        if ( (_FLAG_COUNTRY_FROM_IP != "") and ($display_flag != "") )
        {
          if (is_readable("../images/flags/" . strtolower($country_code_2) . ".png")) 
          {
            $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code_2];
            $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
            echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code_2) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $l_country . " : " . $country_name . "' TITLE='" . $l_country . " : " . $country_name . "'>";
          }
        }
        echo "<font face='verdana' size='2'>";
        if ($priv == 0) echo "<I>";
        echo "&nbsp;<A HREF='user.php?id_user=" . $id_user_2 . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
        if ( ($pseudo != '') and ($pseudo != $username_2) )
        {
          echo $username_2 . "</A> (" . $pseudo . ")";
          //echo $pseudo . "</A> (" . $username_2 . ")";
        }
        else
          echo $username_2 ."</A>";
        //
        if ($priv == 0) echo "</I>";
        echo "&nbsp;</font>";
      echo "</TD>";
      echo "\n";
      //
      echo "<TD align='center' class='row1'>";
        if ($priv == 0)
        {
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_contact_info_wait_valid . "' TITLE='" . $l_admin_contact_info_wait_valid . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "waiting.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_wait_valid . "' TITLE='" . $l_admin_contact_info_wait_valid . "'>";
          $nb_status_wait_valid++;
          $nb_status_tot_wait_valid++;
        }
        if ($priv == 1)
        {
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_ok . "' TITLE='" . $l_admin_contact_info_ok. "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_ok . "' TITLE='" . $l_admin_contact_info_ok. "'>";
          $nb_status_ok++;
          $nb_status_tot_ok++;
        }
        if ($priv == 2)
        {
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_blue.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_contact_info_vip . "' TITLE='" . $l_admin_contact_info_vip . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_vip . "' TITLE='" . $l_admin_contact_info_vip . "'>";
          $nb_status_vip++;
          $nb_status_tot_vip++;
        }
        if ($priv == 5)
        {
          echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray_2.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_contact_info_hidden . "' TITLE='" . $l_admin_contact_info_hidden . "'>";
          $nb_status_hidden++;
          $nb_status_tot_hidden++;
        }
        if ($priv == -1)
        {
          //echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_red.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_contact_info_refused . "' TITLE='" . $l_admin_contact_info_refused . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_disalow.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_contact_info_refused . "' TITLE='" . $l_admin_contact_info_refused . "'>";
          $nb_status_refused++;
          $nb_status_tot_refused++;
        }
      echo "</TD>";
      //
      echo "<FORM METHOD='POST' ACTION ='contact_delete.php?'>";
      echo "<TD valign='center' ' align='center' class='row1'>";
      //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_drop.png' VALUE = '" . $l_admin_bt_delete . "' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' />";
      echo "<input type='hidden' name='id_contact' value = '" . $id_contact . "' />";
      echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      if ( ($priv == 0) or ($priv == 5) ) // si en attente, on autorise à interdire (empêcher de demander l'ajout du contact)
      {
        echo "<FORM METHOD='POST' ACTION ='contact_reject.php?'>";
        echo "<TD valign='center' ' align='center' class='row1'>";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_contact_bt_forbid . "' class='liteoption' />";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_lock.png' VALUE = '" . $l_admin_contact_bt_forbid . "' ALT='" . $l_admin_contact_bt_forbid . "' TITLE='" . $l_admin_contact_bt_forbid . "' />";
        echo "<input type='hidden' name='id_contact' value = '" . $id_contact . "' />";
        echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
        echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        echo "</FORM>";
      }
      else
      {
        echo "<TD class='row2'>";
        echo " &nbsp;";
        echo "</TD>";
      }

      echo "</TR>";
      echo "\n";
    }
    else
    {
      if ($only_status == '') 
      {
        switch ($priv)
        {
          case 0 :
            $nb_status_tot_wait_valid++;
            break;
          case 1 :
            $nb_status_tot_ok++;
            break;
          case 2 :
            $nb_status_tot_vip++;
            break;
          case 5 :
            $nb_status_tot_hidden++;
            break;
          case -1 :
            $nb_status_tot_refused++;
            break;
        }
      }
    }
	}
	echo "</TBODY>";
	echo "</TABLE>";
  //
  //echo "</TD></TR><TR><TD ALIGN='RIGHT'>";
  echo "</TD></TR>";
  echo "<TR><TD>";
  //if ($nb_row > $nb_row_by_page)
  if ($nb_row > 15)
  {
    echo "<font face=verdana size=2>";
    echo $l_rows_per_page . " : ";
    display_nb_row_page(15, $nb_row_by_page, "list_contact_nb_rows");
    echo " | ";
    display_nb_row_page(20, $nb_row_by_page, "list_contact_nb_rows");
    echo " | ";
    display_nb_row_page(25, $nb_row_by_page, "list_contact_nb_rows");
    echo " | ";
    display_nb_row_page(30, $nb_row_by_page, "list_contact_nb_rows");
    echo " | ";
    display_nb_row_page(50, $nb_row_by_page, "list_contact_nb_rows");
  }
  echo "</TD><TD ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "UP");
  echo "</TD></TR>";
  echo "</TABLE>";
  //
  //
	mysqli_close($id_connect);
  //
  //
  if ( (strlen($alpha_link) > 3)  and ($nb_row > 20) )
  {
    echo "<font face=verdana size=2>";
    echo $alpha_link;
    echo "<BR/>";
  }
  
	if ($only_status == '')
	{
    if (intval($only_one) > 0)
    {
      $nb_status_wait_valid = 0;
      $nb_status_ok = 0;
      $nb_status_vip = 0;
      $nb_status_hidden = 0;
      $nb_status_refused = 0;
      $nb_status_tot_wait_valid = 0;
      $nb_status_tot_ok = 0;
      $nb_status_tot_vip = 0;
      $nb_status_tot_hidden = 0;
      $nb_status_tot_refused = 0;
    }
    
		echo "<SMALL><BR/></SMALL>";
			
		echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    //echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'><B>" . $l_legende . " </B>(" . strtolower($l_admin_contact_col_state) .") </TD></TR>";
    echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'><B>" . $l_legende . " </B>(" . strtolower($l_admin_session_order_state) .") </TD></TR>";
			
		echo "</TD></TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
		//echo " <IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18'> ";
		echo " <IMG SRC='" . _FOLDER_IMAGES . "waiting.gif' WIDTH='16' HEIGHT='16'> ";
		echo "</TD><TD class='row2'><font face=verdana size=2>&nbsp;" . $l_admin_contact_info_wait_valid . "&nbsp;";
		echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
    if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_ok > 0) or ($nb_status_tot_vip > 0) or ($nb_status_tot_hidden > 0) or ($nb_status_tot_refused > 0) )  
    {
      if ($nb_status_wait_valid > 0) echo $nb_status_wait_valid . "&nbsp;";
      echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
      if ($nb_status_tot_wait_valid > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=w&lang=" . $lang . "&'>" . $nb_status_tot_wait_valid . "</A>&nbsp;";
    }
    else
      if ($nb_status_wait_valid > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=w&lang=" . $lang . "&'>" . $nb_status_wait_valid . "</A>&nbsp;";
		//
		echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
		//echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' WIDTH='17' HEIGHT='17'>";
		echo "<IMG SRC='" . _FOLDER_IMAGES . "etat_ok.gif' WIDTH='16' HEIGHT='16'>";
		echo "</TD><TD class='row2'><font face=verdana size=2>&nbsp;" . $l_admin_contact_info_ok . "&nbsp;";
		echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
    if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_ok > 0) or ($nb_status_tot_vip > 0) or ($nb_status_tot_hidden > 0) or ($nb_status_tot_refused > 0) )  
    {
      if ($nb_status_ok > 0) echo $nb_status_ok . "&nbsp;";
      echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
      if ($nb_status_tot_ok > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=o&lang=" . $lang . "&'>" . $nb_status_tot_ok . "</A>&nbsp;";
    }
    else
      if ($nb_status_ok > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=o&lang=" . $lang . "&'>" . $nb_status_ok . "</A>&nbsp;";
		//
		echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
		//echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_blue.gif' WIDTH='18' HEIGHT='18'>";
		echo "<IMG SRC='" . _FOLDER_IMAGES . "vip.gif' WIDTH='16' HEIGHT='16'>";
		echo "</TD><TD class='row2'><font face=verdana size=2>&nbsp;" . $l_admin_contact_info_vip . "&nbsp;";
		echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
    if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_ok > 0) or ($nb_status_tot_vip > 0) or ($nb_status_tot_hidden > 0) or ($nb_status_tot_refused > 0) )  
    {
      if ($nb_status_vip > 0) echo $nb_status_vip . "&nbsp;";
      echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
      if ($nb_status_tot_vip > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=v&lang=" . $lang . "&'>" . $nb_status_tot_vip . "</A>&nbsp;";
    }
    else
      if ($nb_status_vip > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=v&lang=" . $lang . "&'>" . $nb_status_vip . "</A>&nbsp;";
		//
		echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
		echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray_2.gif' WIDTH='18' HEIGHT='18'>";
		echo "</TD><TD class='row2'><font face=verdana size=2>&nbsp;" . $l_admin_contact_info_hidden . "&nbsp;";
		echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
    if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_ok > 0) or ($nb_status_tot_vip > 0) or ($nb_status_tot_hidden > 0) or ($nb_status_tot_refused > 0) )  
    {
      if ($nb_status_hidden > 0) echo $nb_status_hidden . "&nbsp;";
      echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
      if ($nb_status_tot_hidden > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=h&lang=" . $lang . "&'>" . $nb_status_tot_hidden . "</A>&nbsp;";
    }
    else
      if ($nb_status_hidden > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=h&lang=" . $lang . "&'>" . $nb_status_hidden . "</A>&nbsp;";
		//
		echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
		//echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_red.gif' WIDTH='18' HEIGHT='18'>";
		echo "<IMG SRC='" . _FOLDER_IMAGES . "b_disalow.png' WIDTH='16' HEIGHT='16'>";
		echo "</TD><TD class='row2'><font face=verdana size=2>&nbsp;" . $l_admin_contact_info_refused . "&nbsp;";
		echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
    if ( ($nb_status_tot_wait_valid > 0) or ($nb_status_tot_ok > 0) or ($nb_status_tot_vip > 0) or ($nb_status_tot_hidden > 0) or ($nb_status_tot_refused > 0) )  
    {
      if ($nb_status_refused > 0) echo $nb_status_refused . "&nbsp;";
      echo "</TD><TD class='row2' ALIGN='RIGHT'><font face=verdana size=2>&nbsp;";
      if ($nb_status_tot_refused > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=r&lang=" . $lang . "&'>" . $nb_status_tot_refused . "</A>&nbsp;";
    }
    else
      if ($nb_status_refused > 0) echo "<A HREF='list_contact.php?tri=" . $tri . "&only_status=r&lang=" . $lang . "&'>" . $nb_status_refused . "</A>&nbsp;";
    //
		echo "</TD></TR>";
		echo "</TABLE>";
	}
}
else
{
  if ( (_SPECIAL_MODE_GROUP_COMMUNITY != '') or (_SPECIAL_MODE_GROUP_COMMUNITY != '') )
  {
    echo "<BR/>";
    echo "<div class='warning'>";
    echo $l_admin_contact_cannot_use;
    echo "</div>";
  }
  else
  {
    echo "<BR/>";
    echo "<div class='info'>";
    echo $l_admin_contact_empty;
    echo "</div>";
  }
}
//
display_menu_footer();
//
echo "</body></html>";
?>
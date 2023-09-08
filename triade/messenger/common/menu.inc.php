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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
//
//
$force_check = "";
if ( (!defined("_MAINTENANCE_MODE")) or (!defined("_SHOUTBOX_ALLOW_SCROLLING")) or (!defined("_ACP_ALLOW_MEMORY_AUTH")) or (!defined("_FLAG_COUNTRY_FROM_IP"))  )  $force_check = "X";
// or (!defined("_XXXXXXXXXXXXXXXXXXXx")) 
//   
$last_version = "";
$file_version = "../common/config/version.tmp";
if (!defined("_SERVER_VERSION")) require ("../common/constant.inc.php");
if (file_exists($file_version))
{
  $fp = fopen($file_version, "r");
  $last_version = fgets($fp);
  fclose($fp);
  if ($last_version != _SERVER_VERSION) $last_version = "";
}
if ($last_version == "") $force_check = "X";
//
//
if ($force_check != "")
{
  $t = $_SERVER["PHP_SELF"];
  $t = strrchr($t, "/");
  $t = substr($t, 1, strlen($t)-1);
  if ( ($t != "check.php") and ($t != "list_options_updating.php") and ( (_ACP_PROTECT_BY_HTACCESS != "") or (isset($_SESSION['acp_login'])) ) )
  {
    echo '<META http-equiv="refresh" content="0;url=check.php?lang='. $lang .'&"> ';
    die();
  }
}
//
//
if (isset($_COOKIE['im_charset'])) $charset_selected = $_COOKIE['im_charset'];  else  $charset_selected = '';
if (isset($_COOKIE['im_full_menu'])) $full_menu = $_COOKIE['im_full_menu'];  else  $full_menu = '';
#if (isset($_COOKIE['im_top_menu'])) $im_top_menu = $_COOKIE['im_top_menu'];  else  $im_top_menu = '';
#if ($im_top_menu != "") define('_MENU_TOP', true);  else define('_MENU_TOP', false);
//
if (isset($_COOKIE['im_position_menu'])) $im_position_menu = $_COOKIE['im_position_menu'];  else  $im_position_menu = '';
switch ($im_position_menu)
{
  case "TOP" :
    define('_MENU_ON', "TOP");
    break;
  case "RIGHT" :
    define('_MENU_ON', "RIGHT");
    break;
  default : // LEFT
    define('_MENU_ON', "LEFT");
    break;
}
//
require ("f_not_empty.inc.php");
require ("extern/extern.inc.php");
prevent_error_extern_option_missing();
//
require ("../common/functions.inc.php");
prevent_error_option_missing();
require ("../common/functions_admin.inc.php");
//
$current_page = $_SERVER["PHP_SELF"];
$current_page = strrchr($current_page, "/");
$current_page = substr($current_page, 1, strlen($current_page)-1);
//
function display_header()
{
  GLOBAL $charset, $charset_selected, $file_style_css, $lang, $full_menu;
  //
  if ($charset_selected != "") $charset = $charset_selected;
  //
  echo '<LINK REL="SHORTCUT ICON" HREF="../images/favicon.ico" />';
  echo '<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW,NOARCHIVE" />';
  echo "\n";
  echo '<META NAME="Author" CONTENT="THeUDS.com" />';
  echo '<META NAME="Copyright" content="THeUDS.com" />';
  echo '<meta name="Publisher" content="www.intramessenger.net" />';
  echo '<meta name="Generator" content="One head, 10 fingers" />';
  echo '<meta http-equiv="Content-Type" content="text/html;charset=' . $charset . '" />';
  echo '<meta http-equiv="Content-Style-Type" content="text/css" />';
  echo "<link href='../common/styles/" . $file_style_css . "' rel='stylesheet' media='screen, print' type='text/css'/>";
  echo "<link href='../common/styles/default/menu_class.css' rel='stylesheet' media='screen, print' type='text/css'/>";
  if (_MENU_ON == "TOP")
  {
    echo '<script language="javascript" type="text/javascript" src="../common/library/menu.js"></script>';
    echo "<link href='../common/library/menu.css' rel='stylesheet' media='screen, print' type='text/css'/>";
    echo '<script language="javascript" type="text/javascript" >';
    echo 'largeur_menu=120;';
    echo 'largeur_sous_menu=160;';
    echo 'top_ssmenu=27;';
    echo "centrer_menu = true;";
    echo "marge_en_haut_de_page = 28;";
    //
    GLOBAL $l_menu_index, $l_menu_manage, $l_menu_currently, $l_menu_list_users, $l_menu_list_group, $l_menu_list_group, $l_menu_messagerie, $l_menu_options, $l_admin_roles_title, $l_language;
    $larg[1] = (strlen($l_menu_index) * 9);
    $larg[2] = (strlen($l_menu_manage) * 9);
    $larg[3] = (strlen($l_menu_options) * 9);
    $larg[4] = (strlen($l_menu_messagerie) * 9);
    $larg[5] = (strlen($l_menu_currently) * 9);
    $larg[6] = (strlen($l_menu_list_users) * 9);
    $larg[7] = (strlen($l_menu_list_group) * 9);
    $larg[8] = (strlen($l_admin_roles_title) * 9);
    $larg[9] = (strlen($l_language) * 9);
    //
    if ($full_menu == "")
    {
      echo "largeur_menu = new Array(" . $larg[1] . ", " . $larg[2] . ", " . $larg[3] . ", ";
/*
      //if ( _SPECIAL_MODE_GROUP_COMMUNITY == '') echo $larg[4] . ", ";
      if ( ( _SPECIAL_MODE_GROUP_COMMUNITY == '') and ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') ) echo $larg[4] . ", ";
      if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '') or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') or (_GROUP_FOR_SBX_AND_ADMIN_MSG  != '') ) echo $larg[5] . ", ";
*/
      if ( (f_check_acp_rights(_C_ACP_RIGHT_admin_messages) == "OK") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_emails) == "OK") )
        echo $larg[4] . ", ";
      //
      echo $larg[5] . ", " . $larg[6] . ", ";
      //
      if ( (f_check_acp_rights(_C_ACP_RIGHT_groups) == "OK") and ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '') or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') or (_GROUP_FOR_SBX_AND_ADMIN_MSG  != '') ) )
        echo $larg[7] . ", ";
      //
      if (  ( _ROLES_TO_OVERRIDE_PERMISSIONS != '') and (f_check_acp_rights(_C_ACP_RIGHT_roles) == "OK") ) 
        echo $larg[8] . ", ";
      //
      echo $larg[9] . ");";
    }
    else
    {
      echo "largeur_menu = new Array(" . $larg[1] . ", " . $larg[2] . ", " . $larg[3] . ", " . $larg[4] . ", " . $larg[5] . ", " . $larg[6] . ", " . $larg[7] . ", " . $larg[8] . ", " . $larg[9] . ");";
    }
    echo '</script>';
  }
}


function display_menu_button_top($link, $text, $title, $lang, $submenu, $img, $num_menu)
{
	$title = str_replace("'", "&#146;", $title);
  if ($submenu == '')
  {

    echo "<p id='menu" . $num_menu ."' class='menu' onmouseover='MontrerMenu(\"ssmenu" . $num_menu . "\");' onmouseout='CacherDelai();'>";
    //if ($num_menu == 10) and ($img != "")) echo "<img src=' . _FOLDER_IMAGES . " . $img . "' align='absmiddle' WIDTH='15' HEIGHT='20' align='' alt='" . $title. "' title='" . $title. "' />";
    //if ($img != "") echo "<img src='" . _FOLDER_IMAGES . $img . "' align='absmiddle' WIDTH='16' HEIGHT='16' align='absmiddle' alt='" . $title. "' title='" . $title. "' />";
    echo "<a href='" . $link . "?lang=" . $lang . "&' alt='" . $title. "' title='" . $title. "' onfocus='MontrerMenu(\"ssmenu" . $num_menu . "\");'>" . $text . "<span>&nbsp;:</span></A>";
    echo "</p>";
    if ($link == "") echo "<ul id='ssmenu" . $num_menu . "' class='ssmenu'	onmouseover='AnnulerCacher();' onmouseout='CacherDelai();' onfocus='AnnulerCacher();' onblur='CacherDelai();'>";
  }
  else
  {
    echo "<li>";
    echo "<a href='" . $link . "?lang=" . $lang . "&' alt='" . $title. "' title='" . $title. "'>";
    //if ($num_menu < 9) // langues
    if ($img != "")
    {
      if (strstr($img, "flag"))
        echo "<img src='" . $img . "' align='absmiddle' alt='" . $title. "' title='" . $title. "' />";
      else
        echo "<img src='" . _FOLDER_IMAGES . $img . "' WIDTH='16' HEIGHT='16' align='absmiddle' alt='" . $title. "' title='" . $title. "' />";
    }
    //
    echo $text. "<span>&nbsp;;</span></a>";
    echo "</li>";
  }
  echo "\n";
}


function f_background_image_color()
{
  $retour = "blue/";
  //
  if (isset($_COOKIE['im_background_color'])) $cookie_color = $_COOKIE['im_background_color']; else $cookie_color = "";
  if ($cookie_color == "green") $retour = "green/";
  if ($cookie_color == "red") $retour = "red/";
  if ($cookie_color == "yellow") $retour = "yellow/";
  if ($cookie_color == "pink") $retour = "pink/";
  //
  return $retour;
}


function display_menu_top()
{
	GLOBAL $tri, $lang, $full_menu, $current_page;
  $c_missing = "Missing !";
  //
  $current_folder  = getcwd() . "/"; 
  $demo_folder = "";
  if ( (substr_count($current_folder, "/admin_demo/") > 0) or (substr_count($current_folder, "\admin_demo/") > 0) ) $demo_folder = "X";
  //
	require("lang.inc.php");
	//
	echo "\n";
	echo "<TABLE BORDER='0' WIDTH='100%' height='100%' cellspacing='0' cellpadding='5'>";
	echo "<TR>";
	echo "<TD BGCOLOR='#709BC8' ALIGN='CENTER' HEIGHT='55' background='" . _FOLDER_IMAGES . f_background_image_color() . "background_top.png'>";
    echo "<font face=verdana size='6' color='white'>";
    echo "IntraMessenger";
	echo "</TD>";
	echo "</TR>";
  //
	//echo "<TR>";
	//echo "<TD>";
	echo "\n";
	echo "<div id='conteneurmenu'>\n";
	echo "<script language='Javascript' type='text/javascript'>\n"; 
	echo "preChargement();\n";  // pour éviter le clignotement désagréable
  echo "</script>\n";
  echo '<noscript>';
  echo "<div class='warning' align='center'><B>";
  echo "<a href='set_cookies.php?lang=" . $lang . "&action=top_menu&tri=&page=" . $current_page ."&'>" . $l_menu_no_javascript . "</B></A></div>"; 
  echo '</noscript>';
  //
  // --------------------------------------------- Menu 1 ---------------------------------------------
  //
  $num = 1;
  display_menu_button_top("", $l_menu_index, "", $lang, "", "menu_home.png", $num);
  display_menu_button_top("index.php", $l_menu_dash_board, "", $lang, "X", "chart_pie.png", $num);
  if ( ($full_menu != "") or (_STATISTICS != "") )
    display_menu_button_top("statistics.php", $l_menu_statistics, $l_admin_stats_title, $lang, "X", "menu_statistics.png", $num);
  //
  echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
  display_menu_button_top("check.php", "Check config !", $l_admin_check_title, $lang, "X", "menu_check.png", $num);
  //if ($demo_folder == "") 
  if (_ACP_PROTECT_BY_HTACCESS == "")
  {
    echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    display_menu_button_top("acp_pass_updating.php", $l_admin_acp_pass_changing, $l_admin_acp_auth_title, $lang, "X", "b_empty_password.png", $num);
    display_menu_button_top("acp_deconnect.php", $l_menu_logout, $l_admin_acp_auth_title, $lang, "X", "b_leave.png", $num);
  }
  // if ($full_menu != "") 
  //{
    echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    display_menu_button_top("http://www.intramessenger.net/custom-version.php", $l_menu_customize, $l_menu_customize_info, $lang, "X", "customize.png", $num);
    display_menu_button_top("donate.php", $l_menu_donate, $l_menu_donate, $lang, "X", "donate.png", $num);
  //}
  echo "</ul>";
  //
  // --------------------------------------------- Menu 2 ---------------------------------------------
  //
  $num++;
  display_menu_button_top("", $l_menu_manage, "", $lang, "", "", $num); // $l_admin_contact_title
  #if ( ($full_menu != "") or (_ACP_PROTECT_BY_HTACCESS == "") )
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_administrators) == "OK") )
    display_menu_button_top("list_admin_acp.php", $l_menu_acp_auth, $l_admin_acp_auth_title, $lang, "X", "b_empty_password.png", $num);
  //
  if ( ($full_menu != "") or ( (_SERVERS_STATUS != "") and (f_check_acp_rights(_C_ACP_RIGHT_servers_status) == "OK") ) )
    display_menu_button_top("list_servers_status.php", $l_admin_servers_title, $l_admin_servers_list, $lang, "X", "menu_check.png", $num);
  //
  display_menu_button_top("list_ban.php", $l_menu_ban, $l_menu_ban, $lang, "X", "menu_ban_ip.png", $num);
  //
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_log_read) == "OK") )
    display_menu_button_top("log.php", $l_menu_log, $l_admin_log_title, $lang, "X", "menu_log.png", $num);
  //
  display_menu_button_top("saving.php", $l_menu_backup, $l_admin_save_title, $lang, "X", "b_save.png", $num);
  //
  echo "</ul>";
  //
  // --------------------------------------------- Menu 3 ---------------------------------------------
  //
  $num++;
  display_menu_button_top("", $l_menu_options, "", $lang, "", "menu_options.png", $num); // $l_admin_group_title
  #if (is_writeable("../common/config/config.inc.php"))
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_options) == "OK") )
    display_menu_button_top("list_options_updating.php", $l_menu_list, $l_admin_options_update, $lang, "X", "menu_options.png", $num);
  #else
  #{
  #  display_menu_button_top("list_options.php", $l_menu_list, $l_admin_options_title, $lang, "X", "menu_options.png", $num);
  #  display_menu_button_top("list_options_updating.php", $l_admin_bt_update, $l_admin_options_update, $lang, "X", "b_save.png", $num);
  #}
  $nb_auth_extern = f_nb_auth_extern();
  if ( ($full_menu != "") or ( ($nb_auth_extern > 0) and (f_check_acp_rights(_C_ACP_RIGHT_options) == "OK") ) )
    display_menu_button_top("list_options_auth_updating.php", $l_admin_options_autentification, $l_admin_options_info_10, $lang, "X", "menu_auth.png", $num);
  //
  //
  echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
  display_menu_button_top("display_updating.php", $l_admin_display_title, $l_admin_display_options, $lang, "X", "customize.png", $num);
  //
  /*
  echo "<li>";
  echo "<a href='set_cookies.php?lang=" . $lang . "&action=full_menu&page=" . $current_page . "&'>";
  echo "<img src='" . _FOLDER_IMAGES . "menu_full.png' align='absmiddle' WIDTH='16' HEIGHT='16' alt='" . $l_menu_left . "' title='" . $l_menu_left . "' />";
  if ($full_menu != "") 
    echo $l_menu_not_full;
  else
    echo $l_menu_full;
  echo "</A>";
  echo "</li>";
  */
  echo "</ul>";
  //
  // --------------------------------------------- Menu 4 ---------------------------------------------
  //
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages) == "OK") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_emails) == "OK") )
  {
    $num++;
    display_menu_button_top("", $l_menu_messagerie, $l_admin_mess_title, $lang, "", "", $num);
    if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages) == "OK") )
      display_menu_button_top("messagerie.php", $l_menu_messagerie_instant, $l_admin_mess_title, $lang, "X", "menu_messagerie.png", $num);
    if ( ($full_menu != "") or ( (strlen(_ADMIN_EMAIL) > 5) and (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_emails) == "OK") ) )
      display_menu_button_top("messagerie_email.php", $l_menu_messagerie_emails, $l_admin_mess_email_title, $lang, "X", "email_edit.png", $num);
    echo "</ul>";
  }
  //
  // --------------------------------------------- Menu 5 ---------------------------------------------
  //
  $num++;
  display_menu_button_top("", $l_menu_currently, "", $lang, "", "menu_sessions.png", $num); // $l_admin_conference_title
  display_menu_button_top("list_sessions.php", $l_menu_list_sessions, $l_admin_session_title, $lang, "X", "menu_sessions.png", $num);
  if ( ($full_menu != "") or (_ALLOW_CONFERENCE != "") )
    display_menu_button_top("list_conference.php", $l_menu_conference, $l_menu_list_conference_list, $lang, "X", "menu_conference.png", $num);
  
  if ( ($full_menu != "") or ( (_SHOUTBOX != "") and (f_check_acp_rights(_C_ACP_RIGHT_shoutbox) == "OK") ) )
    display_menu_button_top("list_shoutbox.php", $l_admin_options_shoutbox_title_short, $l_admin_options_shoutbox_title_long, $lang, "X", "shoutbox2.png", $num);

  echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
  if ( ($full_menu != "") or ( (_SHARE_FILES != "") and (f_check_acp_rights(_C_ACP_RIGHT_published_files) == "OK") ) )
    display_menu_button_top("list_files_sharing.php", $l_admin_options_share_files, $l_admin_options_share_files_title, $lang, "X", "files.png", $num);

  if ( ($full_menu != "") or ( (_BACKUP_FILES != "") and (f_check_acp_rights(_C_ACP_RIGHT_published_files) == "OK") ) )
    display_menu_button_top("list_files_backup.php", $l_index_backup_file, $l_admin_options_backup_files_title, $lang, "X", "b_save.png", $num);

  if ( ($full_menu != "") or ( (_BOOKMARKS != "") and (f_check_acp_rights(_C_ACP_RIGHT_bookmars) == "OK") ) )
  {
    display_menu_button_top("list_bookmarks.php", $l_menu_bookmarks, $l_admin_options_bookmarks, $lang, "X", "bookmarks.png", $num);
  }
  echo "</ul>";
  //
  // --------------------------------------------- Menu 6 ---------------------------------------------
  //
  $num++;
  display_menu_button_top("", $l_menu_list_users, "", $lang, "", "menu_users.png", $num); // $l_admin_users_title
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_users_unlock) == "OK") )
  {
    display_menu_button_top("list_users.php", $l_menu_list, $l_admin_users_title, $lang, "X", "menu_users.png", $num);
    if ( ($full_menu != "") or (_FLAG_COUNTRY_FROM_IP != "") ) 
      display_menu_button_top("list_country.php", $l_country, $l_menu_users_by_country, $lang, "X", "country.png", $num);
    //
    if ($full_menu != "")  display_menu_button_top("list_timezone.php", $l_time_zone, $l_time_zone, $lang, "X", "time_shift.png", $num);
    display_menu_button_top("user_searching.php", $l_admin_bt_search, $l_admin_users_searching, $lang, "X", "menu_lookfor.png", $num);
    echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    if ( ($full_menu != "") or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") ) 
      display_menu_button_top("user_adding.php", $l_admin_bt_add, $l_admin_users_add_new, $lang, "X", "menu_user_ajout.png", $num);
  }
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") )
    display_menu_button_top("user_deleting_older.php", $l_admin_bt_erase, $l_admin_users_out_of_date, $lang, "X", "b_drop.png", $num);
  //
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_users_unlock) == "OK") )
  {
    echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    display_menu_button_top("list_users_ip.php", $l_menu_list_users_ip, $l_admin_users_title, $lang, "X", "menu_ip_double.png", $num);
    display_menu_button_top("list_users_double.php", $l_menu_list_users_double, $l_admin_users_title, $lang, "X", "menu_pc_double.png", $num);
  }
  if ( ($full_menu != "") or  ( ( _SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') and (f_check_acp_rights(_C_ACP_RIGHT_user_contacts) == "OK") ) )
  {
    echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    //display_menu_button_top("", $l_menu_list_contact, "", $lang, "", "menu_contacts.png", $num); // $l_admin_contact_title
    display_menu_button_top("list_contact.php", $l_menu_list_contact, $l_admin_contact_title, $lang, "X", "menu_contacts2.png", $num);
    if ( ($full_menu != "") or (_ALLOW_MANAGE_CONTACT_LIST == "") )
    {
      echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
      display_menu_button_top("contact_adding.php", $l_admin_bt_add, $l_admin_contact_add_contact, $lang, "X", "menu_contact_ajout.png", $num);
    }
  }
  if ( ($full_menu != "") or ( (_ALLOW_CHANGE_AVATAR != "") and (f_check_acp_rights(_C_ACP_RIGHT_avatars) == "OK") ) )
  {
    display_menu_button_top("avatar_changing.php", $l_menu_avatars, $l_menu_avatars, $lang, "X", "menu_avatars.png", $num);
    //echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
  }
  if ( ($full_menu != "") or (_ENTERPRISE_SERVER != "") )
  {
    echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    display_menu_button_top("list_users_pc.php", $l_menu_ban_pc, $l_admin_users_pc_title, $lang, "X", "menu_list_computer.png", $num);
  }
  echo "</ul>";
  //
  // --------------------------------------------- Menu 7 ---------------------------------------------
  //
  if ( ($full_menu != "") or ( _SPECIAL_MODE_GROUP_COMMUNITY != '') or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') or (_GROUP_FOR_SBX_AND_ADMIN_MSG  != '') )
  {
    if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_groups) == "OK") )
    {
      $num++;
      display_menu_button_top("", $l_menu_list_group, "", $lang, "", "menu_groups.png", $num); // $l_admin_group_title
      display_menu_button_top("list_group.php", $l_menu_list, $l_menu_list_group_list, $lang, "X", "menu_groups.png", $num);
      echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
      display_menu_button_top("group_adding.php", $l_admin_bt_create, $l_admin_group_creat_group, $lang, "X", "menu_ajout.png", $num);
      display_menu_button_top("group_adding_user.php", $l_menu_group_add_member, $l_admin_group_title_add_to_group, $lang, "X", "menu_group_ajout.png", $num);
      echo "</ul>";
    }
  }
  //
  // --------------------------------------------- Menu 8 ---------------------------------------------
  //
  if ( ($full_menu != "") or ( ( _ROLES_TO_OVERRIDE_PERMISSIONS != '') and (f_check_acp_rights(_C_ACP_RIGHT_roles) == "OK") ) )
  {
    $num++;
    //echo "<img src='../common/library/lookxphr.gif' class='hr' alt='' />";
    display_menu_button_top("", $l_admin_roles_title, "", $lang, "", "menu_groups.png", $num); 
    display_menu_button_top("list_roles.php", $l_menu_list, $l_menu_list_roles_list, $lang, "X", "menu_roles.png", $num);
    display_menu_button_top("role_permissions_list.php", $l_menu_dash_board, $l_admin_role_dashboard, $lang, "X", "menu_log.png", $num);
    echo "</ul>";
  }
/*
  if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_banned) == "OK") )
  {
    $num++;
    display_menu_button_top("", $l_menu_ban, "", $lang, "", "menu_ban.png", $num);
    if ( ($full_menu != "") or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != "") )
      display_menu_button_top("list_ban.php?ban=users&lang=" . $lang . "&", $l_menu_ban_user, $l_admin_ban_users, $lang, "X", "menu_users.png", $num);
    //
    display_menu_button_top("list_ban.php?ban=ip&lang=" . $lang . "&", $l_menu_ban_ip, $l_admin_ban_ip, $lang, "X", "menu_ban_ip.png", $num);
    display_menu_button_top("list_ban.php?ban=pc&lang=" . $lang . "&", $l_menu_ban_pc, $l_admin_ban_pc, $lang, "X", "ban_computer.png", $num);
    echo "</ul>";
  }
*/
  //
  // --------------------------------------------- Menu 9 ---------------------------------------------
  //
  $num++;
  display_menu_button_top("", $l_language, "Language", $lang, "", "", $num);
  if ($lang == "") $lang = _LANG;
  if ( ($full_menu != "") or ($lang != 'EN') ) display_menu_button_top("display_updating.php", "English", "English", "EN", "X", "../images/flags/us.png", $num);
  if ( ($full_menu != "") or ($lang != 'FR') ) display_menu_button_top("display_updating.php", "Français", "Français", "FR", "X", "../images/flags/fr.png", $num);
  if ( ($full_menu != "") or ($lang != 'IT') ) display_menu_button_top("display_updating.php", "Italian", "Italian", "IT", "X", "../images/flags/it.png", $num);
  if ( ($full_menu != "") or ($lang != 'ES') ) display_menu_button_top("display_updating.php", "Spanish", "Spanish", "ES", "X", "../images/flags/es.png", $num);
  if ( ($full_menu != "") or ($lang != 'PT') ) display_menu_button_top("display_updating.php", "Portuguese", "Portuguese", "PT", "X", "../images/flags/pt.png", $num);
  if ( ($full_menu != "") or ($lang != 'BR') ) display_menu_button_top("display_updating.php", "Brazilian", "Portuguese", "PT", "X", "../images/flags/br.png", $num);
  if ( ($full_menu != "") or ($lang != 'RO') ) display_menu_button_top("display_updating.php", "Romana", "Romana", "RO", "X", "../images/flags/ro.png", $num);
  if ( ($full_menu != "") or ($lang != 'DE') ) display_menu_button_top("display_updating.php", "German", "Deutsch", "DE", "X", "../images/flags/de.png", $num);
  if ( ($full_menu != "") or ($lang != 'NL') ) display_menu_button_top("display_updating.php", "Netherlands", "Dutch", "NL", "X", "../images/flags/nl.png", $num);
  echo "</ul>";
  //



  /*
  $num++;
  //display_menu_button_top("", "&nbsp;", "", $lang, "", "menu-pick-button.gif", "10");
  echo "<p id='menu" . $num . "' class='menu' style='padding:0px;margin:0px;height:22px;' >";
  echo "<a href='set_cookies.php?lang=" . $lang . "&action=top_menu&tri=&page=" . $current_page . "&'>";
  //echo "<img src='" . _FOLDER_IMAGES . "menu-pick-button.gif' align='top' WIDTH='15' HEIGHT='20' alt='" . $l_menu_left . "' title='" . $l_menu_left . "' />";
  echo "<img src='" . _FOLDER_IMAGES . "menu_on_left.png' align='top' WIDTH='16' HEIGHT='16' alt='" . $l_menu_left . "' title='" . $l_menu_left . "' />";
  echo "</A></p>";
  */
  //
  //
  //
  //
  echo "</div>";
  echo "<script language='Javascript' type='text/javascript'>nbmenu=" . $num . "; Chargement();</script>";
  echo '<noscript>';
  echo "<div class='warning' align='center'><B>";
  echo "<a href='set_cookies.php?lang=" . $lang . "&action=top_menu&tri=&page=" . $current_page . "&'>" . $l_menu_no_javascript . "</B></A></div>"; 
  echo '</noscript>';
	//echo "</TD>";
	//echo "</TR>";
  //
  display_menu_2($current_folder, $demo_folder);
}


function display_menu_2($current_folder, $demo_folder)
{
  GLOBAL $lang, $c_missing;
  //
	require("lang.inc.php");
	echo "<TD VALIGN='TOP' BGCOLOR='#EAEDF4' background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>"; // La page...
    $display_msg_error = "X";
    $script_en_cours = strrchr($_SERVER["SCRIPT_NAME"], "/");
    echo "<CENTER>"; 
    if (_ACP_PROTECT_BY_HTACCESS != "")
    {
      if ( (substr_count($current_folder, "/admin/") > 0) or (substr_count($current_folder, "\admin/") > 0) or (substr_count($current_folder, "/acp/") > 0) or (substr_count($current_folder, "\acp/") > 0) )
      {
        if ( ($script_en_cours != "/list_options_updating.php") and ($script_en_cours != "/list_options_auth_updating.php")  )
        {
          echo "</BR></BR></BR></BR></BR></BR></BR></BR></BR></BR></BR></BR></BR></BR></BR></BR>";
          echo "<div class='warning'>" . $l_menu_need_change_admin_dir . "</div>";
          $current_folder = str_replace("admin", "<STRONG><U>admin</U></STRONG>", $current_folder);
          echo $current_folder;
          //echo $l_admin_check_cannot_continue;
          display_menu_footer();
          die();
        }
        else
        {
          echo "<div class='warning'>" . $l_menu_need_change_admin_dir . "</div>";
          $display_msg_error = ""; // juste après l'install on n'affichage d'abord que ce message d'erreur.
        }
      }
    }
    //
    if ($display_msg_error != "")
    {
      if (_MAINTENANCE_MODE != '') 
          echo "<div class='warning'><FONT COLOR='RED'>" . $l_menu_maintenance_mode_on . "</font></div>";
      //
      if (phpversion() == "5.3.0")
          echo "<div class='warning'>Cannot use with <B>PHP version 5.3.0 !</B> (update to PHP 5.3.1 or more)</div>";
      //
      if ( (f_is_empty(_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER) ) and (f_not_empty(_PENDING_NEW_AUTO_ADDED_USER)) ) 
        echo "<div class='warning'><B>" . $l_admin_options_info_5 . "</B> <SMALL><I>_PENDING_NEW_AUTO_ADDED_USER (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER</I> : </SMALL>off)</div>";
      //
      //if ( (f_is_empty(_PENDING_USER_ON_COMPUTER_CHANGE)) and (f_is_empty(_USER_NEED_PASSWORD)) ) 
      if ( (f_is_empty(_PENDING_USER_ON_COMPUTER_CHANGE)) and (f_is_empty(_USER_NEED_PASSWORD)) and (f_is_empty(_FORCE_USERNAME_TO_PC_SESSION_NAME)) ) 
        echo "<div class='warning'><B>" . $l_admin_options_info_2 . "</B> : <SMALL><I>_USER_NEED_PASSWORD / _PENDING_USER_ON_COMPUTER_CHANGE</I></SMALL></div>";
      //  
      if ( (f_not_empty(_CRYPT_MESSAGES)) and (f_not_empty(_HISTORY_MESSAGES_ON_ACP)) ) 
        echo "<div class='warning'>" . $l_admin_options_info_3 . "</div>";
      //  
      if ( (f_not_empty(_SPECIAL_MODE_OPEN_COMMUNITY)) and (f_not_empty(_SPECIAL_MODE_GROUP_COMMUNITY)) ) 
        echo "<div class='warning'><B>" . $l_admin_options_info_4 . "</B></div>";
      //
      if ( (f_not_empty(_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN)) and (f_not_empty(_SPECIAL_MODE_GROUP_COMMUNITY)) ) 
        echo "<div class='warning'><SMALL>_SPECIAL_MODE_GROUP_COMMUNITY</SMALL> <B>" . $l_admin_options_info_9 . "</B> <SMALL>_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN.</SMALL></div>";
      if ( (f_not_empty(_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN)) and (f_not_empty(_SPECIAL_MODE_OPEN_COMMUNITY)) ) 
        echo "<div class='warning'><SMALL>_SPECIAL_MODE_OPEN_COMMUNITY</SMALL> <B>" . $l_admin_options_info_9 . "</B> <SMALL>_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN.</SMALL></div>";
      if ( (f_not_empty(_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN)) and (f_not_empty(_SPECIAL_MODE_OPEN_GROUP_COMMUNITY)) ) 
        echo "<div class='warning'><SMALL>_SPECIAL_MODE_OPEN_GROUP_COMMUNITY</SMALL> <B>" . $l_admin_options_info_9 . "</B> <SMALL>_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN.</SMALL></div>";
      //
      if (is_readable("../install/install.php")) 
        echo "<div class='notice'><FONT COLOR='RED'>" . $l_menu_need_delete_install_dir . "</font></div>";
      //
      if ( (f_not_empty(_GROUP_FOR_SBX_AND_ADMIN_MSG)) and (f_not_empty(_SPECIAL_MODE_GROUP_COMMUNITY)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _GROUP_FOR_SBX_AND_ADMIN_MSG / _SPECIAL_MODE_GROUP_COMMUNITY</SMALL></div>";
      if ( (f_not_empty(_GROUP_FOR_SBX_AND_ADMIN_MSG)) and (f_not_empty(_SPECIAL_MODE_OPEN_GROUP_COMMUNITY)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _GROUP_FOR_SBX_AND_ADMIN_MSG / _SPECIAL_MODE_OPEN_GROUP_COMMUNITY</SMALL></div>";
      //
      if ( (f_not_empty(_FORCE_UPDATE_BY_SERVER)) and (f_not_empty(_FORCE_UPDATE_BY_INTERNET)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _FORCE_UPDATE_BY_SERVER / _FORCE_UPDATE_BY_INTERNET</SMALL></div>";
      //
      if ( (f_not_empty(_PUBLIC_OPTIONS_LIST)) and (!is_readable("../" . _PUBLIC_FOLDER . "/options.php")) )
        echo "<div class='notice'>_PUBLIC_OPTIONS_LIST " . $l_admin_options_legende_not_empty . ", " . $l_admin_options_cannot_access_to . " : " . _PUBLIC_FOLDER . "/options.php" . "</div>";
      if ( (f_not_empty(_PUBLIC_USERS_LIST))  and (!is_readable("../" . _PUBLIC_FOLDER . "/users.php")) )
        echo "<div class='notice'>_PUBLIC_USERS_LIST " . $l_admin_options_legende_not_empty . ", " . $l_admin_options_cannot_access_to . " : " . _PUBLIC_FOLDER . "/users.php" . "</div>";
      if ( (f_not_empty(_PUBLIC_POST_AVATAR)) and (!is_readable("../" . _PUBLIC_FOLDER . "/avatar.php")) )
        echo "<div class='notice'>_PUBLIC_POST_AVATAR " . $l_admin_options_legende_not_empty . ", " . $l_admin_options_cannot_access_to . " : " . _PUBLIC_FOLDER . "/avatar.php" . "</div>";
      //
      if ( (f_not_empty(_SHARE_FILES_FTP_PASSWORD)) and (f_not_empty(_SHARE_FILES_FOLDER)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _SHARE_FILES_FTP_PASSWORD / _SHARE_FILES_FOLDER</SMALL></div>";
      //
      if ( (f_not_empty(_SHARE_FILES_COMPRESS)) and (f_not_empty(_SHARE_FILES_NEED_APPROVAL)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _SHARE_FILES_COMPRESS / _SHARE_FILES_NEED_APPROVAL</SMALL></div>";
      if ( (f_not_empty(_SHARE_FILES_COMPRESS)) and (f_not_empty(_SHARE_FILES_EXCHANGE_NEED_APPROVAL)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _SHARE_FILES_COMPRESS / _SHARE_FILES_EXCHANGE_NEED_APPROVAL</SMALL></div>";
      if ( (f_not_empty(_SHARE_FILES_COMPRESS)) and (f_not_empty(_SHARE_FILES_TRASH)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _SHARE_FILES_COMPRESS / _SHARE_FILES_TRASH</SMALL></div>";
      if ( (f_not_empty(_SHARE_FILES_COMPRESS)) and (f_not_empty(_SHARE_FILES_EXCHANGE_TRASH)) ) 
        echo "<div class='notice'>" . $l_admin_options_info_12 . "<SMALL> : _SHARE_FILES_COMPRESS / _SHARE_FILES_EXCHANGE_TRASH</SMALL></div>";
      //
      //
      $nb_auth_extern = f_nb_auth_extern();
      if ($nb_auth_extern > 0)
      {
        if ( (_USER_NEED_PASSWORD == '') or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == '') or (_PENDING_NEW_AUTO_ADDED_USER != '') )
          echo "<div class='warning'><B>" . $l_admin_options_info_10 . " " . $l_admin_options_info_9 . "</B><I> _USER_NEED_PASSWORD _ALLOW_AUTO_ADD_NEW_USER_ON_SERVER _PENDING_NEW_AUTO_ADDED_USER</I>.</div>";
        //
        if ( (_EXTERN_URL_TO_REGISTER == "") or (_EXTERN_URL_TO_REGISTER == $c_missing) )
          echo "<div class='info'><B>" . $l_admin_options_info_10 . " " . $l_admin_options_info_9 . "</B><I> _EXTERN_URL_TO_REGISTER</I>.</div>";
        //
        if ($nb_auth_extern > 1)
          echo "<div class='warning'><B>" . $l_admin_options_info_10 . " : " . $l_admin_options_info_11 . "</B></div>";
        //
      }
      //
      //
      $sof1 = 0;
      $sof2 = 0;
      if ( _ACP_PROTECT_BY_HTACCESS != '')
      {
        //if ( ($script_en_cours != "/htaccess.php") and ($script_en_cours != "/list_options_updating.php") )
        if ($script_en_cours != "/htaccess.php")
        {
          if (is_readable(".htaccess")) $sof1 = filesize(".htaccess");
          if (is_readable(".htpasswd")) $sof2 = filesize(".htpasswd");
          //if ( (!is_readable(".htaccess")) and ($demo_folder == "") )
          if ( ( (intval($sof1) < 10) or (intval($sof2) < 10) ) and ($demo_folder == "") )
          {
            if ($demo_folder == "") $l_menu_need_htaccess = str_replace ("htaccess", "<a href='htaccess.php?lang=" . $lang . "&' title='htaccess'>htaccess</A>", $l_menu_need_htaccess);
            if ( ($_SERVER['SERVER_NAME'] != "127.0.0.1") and ($_SERVER['SERVER_NAME'] != "localhost") )
              echo "<div class='notice'><FONT COLOR='RED'>" . $l_menu_need_htaccess . "</font></div>";
            else
              echo "<div class='info'><FONT COLOR='BLACK'>" . $l_menu_need_htaccess . "</font></div>";
          }
        }
      }
      //
      //
      if ( ($script_en_cours != "/old_files_removing.php") and ( (is_readable("../distant/start.php")) or (is_readable("../distant/sql_test.php")) or (is_readable("../distant/get_options_2.php")) ) )
        echo "<div class='info'><FONT COLOR='RED'>" . $l_old_files_to_delete . " : <A HREF='old_files_removing.php?lang=" . $lang . "&'>" . $l_admin_bt_delete . "</A></font></div>";
      //
      if ( (f_is_empty(_FORCE_UPDATE_BY_SERVER)) and (f_is_empty(_FORCE_UPDATE_BY_INTERNET)) ) 
        echo "<div class='info'><B>" . $l_admin_options_info_2b . "</B> : <SMALL><I>_FORCE_UPDATE_BY_SERVER &nbsp; &nbsp;/&nbsp;&nbsp; _FORCE_UPDATE_BY_INTERNET</I></SMALL></div>";
      //
       //
      if ( (!is_readable("../im_setup.reg")) and ($demo_folder == "") and (_MAINTENANCE_MODE == '') )
      {
         if ($demo_folder == "") $l_menu_need_reg = str_replace ("im_setup.reg", "<a href='reg.php?lang=" . $lang . "&' title='im_setup.reg'>im_setup.reg</A>", $l_menu_need_reg);
         echo "<div class='notice'><FONT COLOR='BLUE'>" . $l_menu_need_reg . "</font></div>";
         //echo "<BR/>";
      }
    }
    /*
    echo "<BR/>";
    echo "<font color='white'><B>";
    if ($lang == 'FR')
      echo "La version 2.0.1 est disponible en téléchargement, inclue de <A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2127#2127'>nombreuses améliorations</A>";
    else
     echo "Version 2.0.1 is avaible to <A HREF='http://www.intramessenger.net/'>download</A>, include many improvements...";
    echo "</B></font>";
    echo "<BR/>";
    echo "<BR/>";
    */
}



function display_menu_button_left($link, $text, $title, $lang, $submenu, $img)
{
	//$title = str_replace("'", "`", $title);
	$title = str_replace("'", "&#146;", $title);
	//if (strpos(" " . $text, "&nbsp; &nbsp;") == 0 )
  if ($submenu == '')
    echo "<SMALL><SMALL><SMALL><BR/></SMALL></SMALL></SMALL>";
  //
  if ($img != '') 
  {
    if ($link != '') echo "<A href='" . $link . "?lang=" . $lang . "&' title='" . $title . "' >";
    echo "<IMG SRC='" . _FOLDER_IMAGES . $img . "' WIDTH='16' HEIGHT='16' alt='" . $title. "' title='" . $title. "' border='0' />"; 
    if ($link != '') echo "</A>";
  }
	if (strlen($link) > 4) 
	{
    if ($submenu != '') echo "&nbsp; &nbsp;";
    if ( (substr_count($_SERVER['PHP_SELF'], $link ) > 0) or (substr_count($_SERVER['REQUEST_URI'], $link ) > 0)  )  echo "<B><span class='select'>";
    //
    if (strpos($link, "?") == 0 )
      echo "<a href='" . $link . "?lang=" . $lang . "&' title='" . $title . "' >&nbsp;" . $text . "&nbsp;</a></span></B>";
    else
      echo "<a href='" . $link . "&lang=" . $lang . "&' title='" . $title . "' >&nbsp;" . $text . "&nbsp;</a></span></B>";
    //
    if ($submenu != '')
      echo "&nbsp; &nbsp;";
  }
	else
    echo "&nbsp;" . $text . "&nbsp;";
  //
	//echo " <a href='" . $link . "?lang=" . $lang . "&' class='select' title='" . $title . "' >" . $text . "</a></B>";
	//echo " <a href='" . $link . "?lang=" . $lang . "&' class='select' title='" . $title . "' ><span class='selected'>" . $text . "</span></a></B>";
	//echo " <a href='" . $link . "?lang=" . $lang . "&' class='contentViews' title='" . $title . "' ><span class='buttonText'>" . $text . "</span></a></B>";
	echo "<BR/>";
  echo "\n";
}


function display_menu()
{
	if (_MENU_ON == "TOP") display_menu_top();
	if (_MENU_ON == "LEFT") display_menu_left("");
	if (_MENU_ON == "RIGHT") display_menu_left("D1");
}


function display_menu_left($left_or_right)
{
	GLOBAL $tri, $lang, $full_menu, $current_page;
  $c_missing = "Missing !";
  //
  $current_folder  = getcwd() . "/"; 
  $demo_folder = "";
  if ( (substr_count($current_folder, "/admin_demo/") > 0) or (substr_count($current_folder, "\admin_demo/") > 0) ) $demo_folder = "X";
  //if (isset($_COOKIE['im_full_menu'])) $full_menu = $_COOKIE['im_full_menu'];  else  $full_menu = '';
  //
	require("lang.inc.php");
	//
	echo "\n";
	if ( ($left_or_right == "") or ($left_or_right == "D1") )
	{
    echo "<TABLE BORDER='0' WIDTH='100%' height='100%' cellspacing='0' cellpadding='5'>";
    echo "<TR>";
    //echo "<TD COLSPAN='2' BGCOLOR='#76A8DB' ALIGN='CENTER' HEIGHT='55'>";
    //echo "<TD COLSPAN='2' BGCOLOR='#79A4D1' ALIGN='CENTER' HEIGHT='55'>"; // idem haut du fond jpg
    echo "<TD COLSPAN='2' BGCOLOR='#709BC8' ALIGN='CENTER' HEIGHT='55' background='" . _FOLDER_IMAGES . f_background_image_color() . "background_top.png'>";
      echo "<font face=verdana size='6' color='white'>";
      echo "IntraMessenger";
    echo "</TD>";
    echo "</TR>";
  }
  
	if ( ($left_or_right == "") or ($left_or_right == "D2") )
	{
    if ($left_or_right == "") echo "<TR>";
    echo "<TD WIDTH='200' VALIGN='TOP' BGCOLOR='#D9E2EC' class='menu_left' background='" . _FOLDER_IMAGES . f_background_image_color() . "background_left.png'>"; // Menu à gauche 
      echo "<CENTER>";
      //echo "<!–- ";
      #echo '<SCRIPT language="javascript" type="text/javascript">';
      #echo 'document.write("<a href=set_cookies.php?lang=' . $lang . '&action=top_menu&tri=x&page=' . $current_page . '&><img src=\"' . _FOLDER_IMAGES . 'menu_on_top.png\" align=`top` WIDTH=`16` HEIGHT=`16` border=`0`alt=\"' . $l_menu_top . '\" title=\"' . $l_menu_top . '\" /></A>&nbsp;"); ';
      #echo "</SCRIPT>";
      //echo " //-–> ";
      echo "<NOSCRIPT>";
        echo "<a href='menu_info_no_js.php?lang=" . $lang . "&'>";
        echo "<img src='" . _FOLDER_IMAGES . "menu_on_top.png' align='top' WIDTH='16' HEIGHT='16' border='0'align='' alt='" . $l_menu_top . "' title='" . $l_menu_top . "' /></A> &nbsp; ";
      echo "</NOSCRIPT>";

      echo "<font face=verdana size='2'>";
      if ($lang == "") $lang = _LANG;
      if ($lang != 'FR') echo " <A HREF='display_updating.php?lang=FR&tri=" . $tri . "&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'EN') echo " <A HREF='display_updating.php?lang=EN&tri=" . $tri . "&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'IT') echo " <A HREF='display_updating.php?lang=IT&tri=" . $tri . "&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'ES') echo " <A HREF='display_updating.php?lang=ES&tri=" . $tri . "&' TITLE='Spanish'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'PT') echo " <A HREF='display_updating.php?lang=PT&tri=" . $tri . "&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'BR') echo " <A HREF='display_updating.php?lang=BR&tri=" . $tri . "&' TITLE='Portuguese'><IMG SRC='../images/flags/br.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'RO') echo " <A HREF='display_updating.php?lang=RO&tri=" . $tri . "&' TITLE='Romana'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'DE') echo " <A HREF='display_updating.php?lang=DE&tri=" . $tri . "&' TITLE='German'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      if ($lang != 'NL') echo " <A HREF='display_updating.php?lang=NL&tri=" . $tri . "&' TITLE='Netherlands'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
      //
      $no_trans_yet = "No have this translation yet. If you can do it, thanks to post it on the official forum.";
      //echo ' <A HREF="javascript:alert(\''.$no_trans_yet.'\');" TITLE="' . $no_trans_yet .'"><IMG SRC="../images/flags/es.png" WIDTH="18" HEIGHT="12" BORDER="0" ALIGN=""></A>';
      //
      $acp_login = f_acp_login();
      //if ($acp_login <> "") echo "<strong>" . $acp_login . "</strong><br/>";
      if ($acp_login <> "") echo "[<strong><a href='acp_pass_updating.php?lang=" . $lang . "&' TITLE='" . $l_admin_acp_pass_changing . "'>" . $acp_login . "</a></strong>]<br/>";
      //
      echo "</CENTER>";
      //
      // -------------------------------------------- TABLEAU DE BORD --------------------------------------------
      //
      //display_menu_button_left("index.php", $l_menu_dash_board, $l_menu_dash_board, $lang, "", "menu_home.png");
      //display_menu_button_left("index.php", $l_menu_dash_board, $l_menu_dash_board, $lang, "", "chart_pie.png");
      display_menu_button_left("", $l_menu_index, "", $lang, "", "chart_pie.png");
      display_menu_button_left("index.php", $l_menu_dash_board, $l_menu_dash_board, $lang, "X", "");
      //
      display_menu_button_left("statistics.php", $l_menu_statistics, $l_admin_stats_title, $lang, "X", ""); // menu_statistics.png
      //
      // -------------------------------------------- OPTIONS --------------------------------------------
      //
      //display_menu_button_left("list_options.php", $l_menu_options, $l_admin_options_title, $lang, "", "menu_options.png");
      display_menu_button_left("", $l_menu_options, $l_admin_options_title, $lang, "", "menu_options.png"); // $l_admin_group_title
      #if (is_writeable("../common/config/config.inc.php"))
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_options) == "OK") )
        display_menu_button_left("list_options_updating.php", $l_menu_list, $l_admin_options_update, $lang, "X", "");
      #else
      #{
      #  display_menu_button_left("list_options.php", $l_menu_list, $l_admin_options_title, $lang, "X", "");
      #  display_menu_button_left("list_options_updating.php", $l_admin_bt_update, $l_admin_options_update, $lang, "X", "");
      #}
      //
      $nb_auth_extern = f_nb_auth_extern();
      if ( ($full_menu != "") or ( ($nb_auth_extern > 0) and (f_check_acp_rights(_C_ACP_RIGHT_options) == "OK") ) )
        display_menu_button_left("list_options_auth_updating.php", $l_admin_options_autentification, $l_admin_options_info_10, $lang, "X", "");  
      //
      //
      display_menu_button_left("display_updating.php", $l_admin_display_title, $l_admin_display_options, $lang, "X", "");  
      //
      // -------------------------------------------- GESTION --------------------------------------------
      //
      display_menu_button_left("", $l_menu_manage, "", $lang, "", "cog.png");
      #if ( ($full_menu != "") or (_ACP_PROTECT_BY_HTACCESS == "") )
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_administrators) == "OK") )
        display_menu_button_left("list_admin_acp.php", $l_menu_acp_auth, $l_admin_acp_auth_title, $lang, "X", "");
      //
      if ( ($full_menu != "") or ( (_SERVERS_STATUS != "") and (f_check_acp_rights(_C_ACP_RIGHT_servers_status) == "OK") ) )
        display_menu_button_left("list_servers_status.php", $l_admin_servers_title, $l_admin_servers_list, $lang, "X", "");
      //
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_banned) == "OK") )
        display_menu_button_left("list_ban.php", $l_menu_ban, $l_menu_ban, $lang, "X", "");
      //
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_log_read) == "OK") )
        display_menu_button_left("log.php", $l_menu_log, $l_admin_log_title, $lang, "X", "");
      //
      display_menu_button_left("saving.php", $l_menu_backup, $l_admin_save_title, $lang, "X", ""); // b_save.png
      display_menu_button_left("check.php", "Check config !", $l_admin_check_title, $lang, "X", "");      
      //
      // -------------------------------------------- MESSAGERIE --------------------------------------------
      //
      /*
      #display_menu_button_left("messagerie.php", $l_menu_messagerie, $l_admin_mess_title, $lang, "", "menu_messagerie.png");
      if ( (strlen(_ADMIN_EMAIL) > 5) or ($full_menu != "") )
      {
        display_menu_button_left("", $l_menu_messagerie, $l_admin_mess_title, $lang, "", "menu_messagerie.png");
        display_menu_button_left("messagerie.php", $l_menu_messagerie_instant, $l_admin_mess_title, $lang, "X", "");
        display_menu_button_left("messagerie_email.php", $l_menu_messagerie_emails, $l_admin_mess_email_title, $lang, "X", "");
      }
      else
        display_menu_button_left("messagerie.php", $l_menu_messagerie, $l_admin_mess_title, $lang, "", "menu_messagerie.png");
      */
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages) == "OK") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_emails) == "OK") )
      {
        display_menu_button_left("", $l_menu_messagerie, $l_admin_mess_title, $lang, "", "menu_messagerie.png");
        if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_admin_messages) == "OK") )
          display_menu_button_left("messagerie.php", $l_menu_messagerie_instant, $l_admin_mess_title, $lang, "X", "");
        if ( ($full_menu != "") or ( (strlen(_ADMIN_EMAIL) > 5) and (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_emails) == "OK") ) )
          display_menu_button_left("messagerie_email.php", $l_menu_messagerie_emails, $l_admin_mess_email_title, $lang, "X", "");
      }
      //
      // -------------------------------------------- ACTUELLEMENT --------------------------------------------
      //
      display_menu_button_left("", $l_menu_currently, $l_menu_currently, $lang, "", "menu_sessions.png"); // $l_admin_conference_title
      display_menu_button_left("list_sessions.php", $l_menu_list_sessions, $l_admin_session_title, $lang, "X", "");
      if ( ($full_menu != "") or (_ALLOW_CONFERENCE != "") )
      {
        display_menu_button_left("list_conference.php", $l_menu_conference, $l_menu_list_conference_list, $lang, "X", "");
      }
      if ( ($full_menu != "") or ( (_SHOUTBOX != "") and (f_check_acp_rights(_C_ACP_RIGHT_shoutbox) == "OK") ) )
      {
        display_menu_button_left("list_shoutbox.php", $l_admin_options_shoutbox_title_short, $l_admin_options_shoutbox_title_long, $lang, "X", "");
      }
      if ( ($full_menu != "") or ( (_SHARE_FILES != "") and (f_check_acp_rights(_C_ACP_RIGHT_published_files) == "OK") ) )
      {
        display_menu_button_left("list_files_sharing.php", $l_admin_options_share_files, $l_admin_options_share_files_title, $lang, "X", "");
      }
      
      if ( ($full_menu != "") or ( (_BACKUP_FILES != "") and (f_check_acp_rights(_C_ACP_RIGHT_published_files) == "OK") ) )
      {
        display_menu_button_left("list_files_backup.php", $l_index_backup_file, $l_admin_options_backup_files_title, $lang, "X", "");
      }
      if ( ($full_menu != "") or ( (_BOOKMARKS != "") and (f_check_acp_rights(_C_ACP_RIGHT_bookmars) == "OK") ) )
      {
        display_menu_button_left("list_bookmarks.php", $l_menu_bookmarks, $l_admin_options_bookmarks, $lang, "X", "");
      }
      //
      // -------------------------------------------- USERS --------------------------------------------
      //
      display_menu_button_left("", $l_menu_list_users, $l_menu_list_users, $lang, "", "menu_users.png"); // $l_admin_users_title
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_users_unlock) == "OK") )
      {
        display_menu_button_left("list_users.php", $l_menu_list, $l_admin_users_title, $lang, "X", "");
        display_menu_button_left("user_searching.php", $l_admin_bt_search, $l_admin_users_searching, $lang, "X", "");
        if ( ($full_menu != "") or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") ) 
        {
          display_menu_button_left("user_adding.php", $l_admin_bt_add, $l_admin_users_add_new, $lang, "X", "");
        }
      }
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") )
        display_menu_button_left("user_deleting_older.php", $l_admin_bt_erase, $l_admin_users_out_of_date, $lang, "X", "");
      //
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_users) == "OK") )
      {
        display_menu_button_left("list_users_ip.php", $l_menu_list_users_ip, $l_admin_users_title, $lang, "X", "");
        display_menu_button_left("list_users_double.php", $l_menu_list_users_double, $l_admin_users_title, $lang, "X", "");
      }
      if ( ($full_menu != "") and (_FLAG_COUNTRY_FROM_IP != "") )  // "and" et non "or"
      {
        display_menu_button_left("list_country.php", $l_country, $l_menu_users_by_country, $lang, "X", "");
      }
      if ($full_menu != "")  display_menu_button_left("list_timezone.php", $l_time_zone, $l_time_zone, $lang, "X", "");
      //
      if ( ( _SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') and (f_check_acp_rights(_C_ACP_RIGHT_user_contacts) == "OK") )
      {
        //display_menu_button_left("", $l_menu_list_contact, $l_menu_list_contact, $lang, "", "menu_contacts.png", ""); // $l_admin_contact_title
        display_menu_button_left("list_contact.php", $l_admin_contact_title, $l_menu_list_contact, $lang, "X", "");
        
        if ( ($full_menu != "") or (_ALLOW_MANAGE_CONTACT_LIST == "") )
        {
          display_menu_button_left("contact_adding.php", $l_admin_bt_add, $l_admin_contact_add_contact, $lang, "X", "");
        }
      }
      else
      {
        if ($full_menu != "") display_menu_button_left("list_contact.php", $l_admin_contact_title, $l_menu_list_contact, $lang, "X", "", "");
      }
      //
      if ( ($full_menu != "") or ( (_ALLOW_CHANGE_AVATAR != "") and (f_check_acp_rights(_C_ACP_RIGHT_avatars) == "OK") ) )
        display_menu_button_left("avatar_changing.php", $l_menu_avatars, $l_menu_avatars, $lang, "X", "");
      //
      if ( ($full_menu != "") or ( (_ENTERPRISE_SERVER != "") and (f_check_acp_rights(_C_ACP_RIGHT_users_unlock) == "OK") ) )
      {
        display_menu_button_left("list_users_pc.php", $l_menu_ban_pc, $l_admin_users_pc_title, $lang, "X", "");
      }
      //
/*
      //
      // -------------------------------------------- CONTACTS --------------------------------------------
      //
      //if ( _SPECIAL_MODE_GROUP_COMMUNITY == '' )
      if ( ( _SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') and (f_check_acp_rights(_C_ACP_RIGHT_user_contacts) == "OK") )
      {
        display_menu_button_left("", $l_menu_list_contact, $l_menu_list_contact, $lang, "", "menu_contacts.png", ""); // $l_admin_contact_title
        display_menu_button_left("list_contact.php", $l_menu_list, $l_admin_contact_title, $lang, "X", "");
        
        if ( ($full_menu != "") or (_ALLOW_MANAGE_CONTACT_LIST == "") )
        {
          display_menu_button_left("contact_adding.php", $l_admin_bt_add, $l_admin_contact_add_contact, $lang, "X", "");
        }
      }
      else
      {
        if ($full_menu != "") display_menu_button_left("list_contact.php", $l_menu_list_contact, $l_menu_list_contact, $lang, "", "menu_contacts.png", "");
      }
*/
      //
      // -------------------------------------------- GROUPES --------------------------------------------
      //
      if ( (f_check_acp_rights(_C_ACP_RIGHT_groups) == "OK") and ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') or (_GROUP_FOR_SBX_AND_ADMIN_MSG != '') ) )
      {
        display_menu_button_left("", $l_menu_list_group, $l_menu_list_group, $lang, "", "menu_groups.png"); // $l_admin_group_title
        display_menu_button_left("list_group.php", $l_menu_list, $l_menu_list_group_list, $lang, "X", "");
        //display_menu_button_left("group_adding.php", $l_admin_bt_create, $l_admin_group_creat_group, $lang, "X", "");
        //display_menu_button_left("group_renaming.php", $l_admin_bt_update, $l_admin_group_rename_group, $lang, "X", "");
        display_menu_button_left("group_adding_user.php", $l_menu_group_add_member, $l_admin_group_title_add_to_group, $lang, "X", "");
      }
      else
      {
        if ($full_menu != "") display_menu_button_left("list_group.php", $l_menu_list_group, $l_menu_list_group, $lang, "", "menu_groups.png");
      }
      //
      // -------------------------------------------- ROLES --------------------------------------------
      //
      if ( ($full_menu != "") or ( ( _ROLES_TO_OVERRIDE_PERMISSIONS != '' ) and (f_check_acp_rights(_C_ACP_RIGHT_roles) == "OK") ) )
      {
        //display_menu_button_left("list_roles.php", $l_admin_roles_title, $l_admin_roles_title, $lang, "", "menu_roles.png");
        display_menu_button_left("", $l_admin_roles_title, $l_admin_roles_title, $lang, "", "menu_roles.png");
        display_menu_button_left("list_roles.php", $l_menu_list, $l_menu_list_roles_list, $lang, "X", "");
        //display_menu_button_left("role_adding.php", $l_admin_bt_create, $l_admin_roles_creat_role, $lang, "X", "");
        display_menu_button_left("role_permissions_list.php", $l_menu_dash_board, $l_admin_role_dashboard, $lang, "X", "");
        ////display_menu_button_left("role_adding_user.php", $l_menu_group_add_member, $l_admin_roles_title_add_to_role, $lang, "X", "");
      }
      //
      // --------------------------------------------
      //
/*
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_banned) == "OK") )
      {
        display_menu_button_left("", $l_menu_ban, $l_menu_ban, $lang, "", "menu_ban.png");
        if ( ($full_menu != "") or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != "") )
          display_menu_button_left("list_ban.php?ban=users", $l_menu_ban_user, $l_admin_ban_users, $lang, "X", "");
        //
        display_menu_button_left("list_ban.php?ban=ip", $l_menu_ban_ip, $l_admin_ban_ip, $lang, "X", "");
        display_menu_button_left("list_ban.php?ban=pc", $l_menu_ban_pc, $l_admin_ban_pc, $lang, "X", "");
      }
      //
      // -------------------------------------------- STATS --------------------------------------------
      //
      if ( ($full_menu != "") or (_STATISTICS != "") )
      {
        display_menu_button_left("statistics.php", $l_menu_statistics, $l_admin_stats_title, $lang, "", "menu_statistics.png");
      }
      //
      // -------------------------------------------- JOURNAUX LOG --------------------------------------------
      //
      if ( ($full_menu != "") or (f_check_acp_rights(_C_ACP_RIGHT_log_read) == "OK") )
        display_menu_button_left("log.php", $l_menu_log, $l_admin_log_title, $lang, "", "menu_log.png");
      //
      // -------------------------------------------- SAUVEGARDE --------------------------------------------
      //
      display_menu_button_left("saving.php", $l_menu_backup, $l_admin_save_title, $lang, "", "b_save.png");
      //
      // -------------------------------------------- CHECK --------------------------------------------
      //
      display_menu_button_left("check.php", "Check config !", $l_admin_check_title, $lang, "", "menu_check.png");
*/
      //
      // --------------------------------------------
      //
      if ($full_menu != "") display_menu_button_left("donate.php", $l_menu_donate, $l_menu_donate_info, $lang, "", "donate.png", "");
      //display_menu_button_left("donate.php", $l_menu_donate, $l_menu_donate_info, $lang, "", "donate.png", "");
      //
      // --------------------------------------------
      //
      if ($full_menu != "") display_menu_button_left("http://www.intramessenger.net/custom-version.php", $l_menu_customize, $l_menu_customize_info, $lang, "", "customize.png", "");
      //
      // --------------------------------------------
      //
      if (_ACP_PROTECT_BY_HTACCESS == "") display_menu_button_left("acp_deconnect.php", $l_menu_logout, $l_admin_acp_auth_title, $lang, "", "b_leave.png");
      //
      echo "<BR/>";
      //echo "<SMALL><SMALL><SMALL><BR/></SMALL></SMALL></SMALL>";
      /*
      echo '<SCRIPT language="javascript" type="text/javascript">';
      //echo "<!–- ";
      echo 'document.write(" <a href=set_cookies.php?lang=' . $lang . '&action=top_menu&tri=x&page=' . $current_page . '&><img src=\"' . _FOLDER_IMAGES . 'menu_on_top.png\" align=`top` WIDTH=`16` HEIGHT=`16` border=`0`alt=\"' . $l_menu_top . '\" title=\"' . $l_menu_top . '\" /></A> &nbsp;    "); ';
      //echo " //-–> ";
      echo "</SCRIPT>";
      echo "<NOSCRIPT>";
        echo "<a href='menu_info_no_js.php?lang=" . $lang . "&'>";
        echo "<img src='" . _FOLDER_IMAGES . "menu_on_top.png' align='top' WIDTH='16' HEIGHT='16' border='0'align='' alt='" . $l_menu_top . "' title='" . $l_menu_top . "' /></A> &nbsp; ";
      echo "</NOSCRIPT>";
      */

      //echo "<a href='set_cookies.php?lang=" . $lang . "&action=top_menu&tri=x&page=" . $current_page . "&'>";
      //echo "<img src='" . _FOLDER_IMAGES . "menu_on_top.png' align='top' WIDTH='16' HEIGHT='16' border='0'align='' alt='" . $l_menu_top . "' title='" . $l_menu_top . "' /></A> &nbsp; ";
      /*
      echo "<a href='set_cookies.php?lang=" . $lang . "&action=full_menu&page=" . $current_page . "&'>";
      if ($full_menu != "") 
        echo "<img src='" . _FOLDER_IMAGES . "menu-pick-button2.gif' align='top' WIDTH='15' HEIGHT='20' border='0'align='' alt='" . $l_menu_not_full . "' title='" . $l_menu_not_full . "' />";
      else
        echo "<img src='" . _FOLDER_IMAGES . "menu-pick-button.gif' align='top' WIDTH='15' HEIGHT='20' border='0'align='' alt='" . $l_menu_full . "' title='" . $l_menu_full . "' /> ";
      echo "</A>";
      */
    echo "</TD>";

    //
    //
  }
	if ( ($left_or_right == "") or ($left_or_right == "D1") )
	{
    display_menu_2($current_folder, $demo_folder);
  }
}


function footer()
{
  require("constant.inc.php");
  //
	echo "<BR/><span class='copyright'><a href='http://www.intramessenger.net/' target='_blank' class='copyright' alt='THeUDS.com' title='THeUDS.com'>IntraMessenger</A> server " . _SERVER_VERSION;
	echo " by <a href='http://www.theuds.com/' target='_blank' class='copyright' alt='THeUDS.com' title='THeUDS.com'>THeUDS</A> &copy; 2006 - 2019";
  echo " |&nbsp;Support on <a href='http://www.intramessenger.com/forum/' class='copyright' target='_blank'>official forum</A></span>";
	//
	if (  ( strpos($_SERVER['HTTP_USER_AGENT'], "Gecko") == 0 ) and ( strpos(" " . $_SERVER['HTTP_USER_AGENT'], "Opera") == 0 )  ) // "MSIE"
	{
		echo '<BR/><BR/><A HREF="http://getfirefox.com/" TARGET="_blank">';
		echo '<img src="../images/get_firefox.png" border="0" title="FireFox (Mozilla)" align="center" width="80" height="15" ALIGN="TOP"></A><BR/>';
	}
}


function display_row_table($text, $width)  
{
	//echo "<TH align='center' width='" . $width . "' bgcolor='" . $color . "' class='row1'><font face='verdana' size='2'><b>" . $text . "</b></font></TH>";
	echo "<TD align='center' width='" . $width . "' class='catHead'> <font face='verdana' size='2'><b>" . $text . "</b></font> </TD>\n";
}


function display_nb_page($num_page, $nb_by_page, $nb_res, $add_url, $go_up)
{
  $num_page = intval($num_page);
  $nb_res = intval($nb_res);
  $nb_by_page = intval($nb_by_page);
  if ($nb_by_page < 10) $nb_by_page = 20;
  $add_url = trim($add_url);
  $go_up = trim($go_up);
  //
  if ($nb_res > $nb_by_page)
  {
    GLOBAL $lang;
    require("lang.inc.php");
    //
    $nb_page = ceil($nb_res / $nb_by_page);
    if ($num_page < 1) $num_page = 1;
    if ($num_page > $nb_page) $num_page = $nb_page;
    //
    echo "<TABLE class='forumline' cellspacing='1' cellpadding='3' border='0'>";
    echo "<TR>";
    echo "<TD class='catHead'>&nbsp;Page " . $num_page . " " . $l_pg_of . " " . $nb_page . "&nbsp;</td>";
    if ($num_page > 1)
    {
      if ($num_page > 2)
      {
        echo "<TD class='row1'><A class='forumlink' href='?page=1" . $add_url . "' title='" . $l_pg_first_page . " - " . $l_pg_result . " 1 " . $l_pg_to . " " . $nb_by_page . " " . $l_pg_of . " " . $nb_res . "'>&laquo; " . $l_pg_first . "</A></td>";
      }
      $last_page = ($num_page - 1);
      $start = ( ($last_page - 1) * $nb_by_page + 1);
      $end = ($start + $nb_by_page - 1);
      echo "<TD class='row1'><A class='forumlink' href='?page=" . $last_page . $add_url . "' title='" . $l_pg_prev_page . " - " . $l_pg_result . " " . $start . " " . $l_pg_to . " " . $end . " " . $l_pg_of . " " . $nb_res . "'>&lt;</A></td>";
      echo "<TD class='row1'><A class='forumlink' href='?page=" . $last_page . $add_url . "' title='" . $l_pg_show_result . " " . $start . " " . $l_pg_to . " " . $end . " " . $l_pg_of . " " . $nb_res . "'>" . $last_page . "</A></td>";
    }
    //
    $start = ( ($num_page - 1) * $nb_by_page + 1);
    $end = ($start + $nb_by_page - 1);
    //echo "<td class='row1'><span class='forumlink' title='" . $l_pg_show_result . " " . $start . " " . $l_pg_to . " " . $end . " " . $l_pg_of . " " . $nb_res . "'><strong>" . $num_page . "</strong></span></TD>";
    echo "<td class='row2'><span class='forumlink' title='" . $l_pg_show_result . " " . $start . " " . $l_pg_to . " " . $end . " " . $l_pg_of . " " . $nb_res . "'>" . $num_page . "</span></TD>";
    //
    if ( $num_page < $nb_page )
    {
      $next_page = ($num_page + 1);
      $start = ( ($next_page - 1) * $nb_by_page + 1);
      $end = ($start + $nb_by_page - 1);
      echo "<td class='row1'><A class='forumlink' href='?page=" . $next_page . $add_url . "' title='" . $l_pg_show_result . " " . $start . " " . $l_pg_to . " " . $end . " " . $l_pg_of . " " . $nb_res . "'>" . $next_page . "</A></TD>";
      echo "<td class='row1'><A class='forumlink' href='?page=" . $next_page . $add_url . "' title='" . $l_pg_next_page . " - " . $l_pg_result . " " . $start . " " . $l_pg_to . " " . $end . " " . $l_pg_of . " " . $nb_res . "'>&gt;</A></TD>";
      if ( $num_page < ($nb_page - 1) )
      {
        $start = ( ($nb_page-1) * $nb_by_page );
        $end = $nb_res;
        echo "<td class='row1'><A class='forumlink' href='?page=" . $nb_page . $add_url . "' title='" . $l_pg_last_page . " - " . $l_pg_result . " " . $start . " " . $l_pg_to . " " . $end . "'>" . $l_pg_last . " &raquo;</A></TD>";
      }
    }
    //echo "<td class='catHead' title='" . $l_pg_all . "'><a class='forumlink' HREF='?page=all" . $add_url . "'>&laquo; &raquo;</A></TD>";
    echo "<td class='catHead' title='" . $l_pg_all . "'>&nbsp;";
    echo "<a class='forumlink' HREF='?page=all" . $add_url . "'><IMG SRC='" . _FOLDER_IMAGES . "view_all.png' WIDTH='16' HEIGHT='16' border='0' /></A>";
    echo "&nbsp;</TD>";
    //
    if ( ($go_up != '') and ($nb_by_page > 20) )
      echo "<td class='catHead' title='Top'><a HREF='#top'><IMG SRC='" . _FOLDER_IMAGES . "b_top.gif' height='15' width='15' border='0'></A></TD>";
    //echo "<td class='vbmenu_control' title='Top'><a HREF='#top'>^</A></TD>";
    //echo "<td class='vbmenu_control' title='Bottom'><a HREF='#bottom'>^</A></TD>";
    echo "</TR>";
    echo "</TABLE>";
  }
}



function display_menu_footer()
{
  if (_MENU_ON == "RIGHT") display_menu_left("D2");
  //
  GLOBAL $lang;
  require("lang.inc.php");
  require("config/mysql.config.inc.php");
  if ( ($dbuname == "root") and ($dbpass == "") ) 
  {
    //
    echo "</TR>";
    echo "<TR>";
    echo "<TD COLSPAN='2' ALIGN='CENTER' BGCOLOR='#FCFDFF' HEIGHT='40'>";
    echo "<div class='warning'>" . $l_menu_pass_root_empty . "</div>";
    echo "</TD>";
    echo "</TR>";
  }
  unset($dbpass);
  //
  //
  if (!defined("_SERVER_VERSION")) require("constant.inc.php");
  //
  echo "</TD>";
  echo "</TR>";
  echo "<TR>";
  echo "<TD COLSPAN='2' ALIGN='CENTER' BGCOLOR='#FCFDFF' HEIGHT='40'>"; // F4F4F4
    echo "<span class='copyright'><a href='http://www.intramessenger.net/' target='_blank' class='copyright' alt='IntraMessenger.net' title='IntraMessenger.net'>IntraMessenger</A> server " . _SERVER_VERSION;
    echo " by <a href='http://www.theuds.com/' target='_blank' class='copyright' alt='THeUDS.com' title='THeUDS.com'>THeUDS</A> &copy; 2006 - 2019";
    echo " |&nbsp;<a href='http://www.intramessenger.net/contact.php?lang=" . $lang . "&' class='copyright' target='_blank'>Support</A>";
    echo " on <a href='http://www.intramessenger.com/forum/' class='copyright' target='_blank'>official forum</A>";
    if ($lang == "FR")
    {
      if (is_readable("../doc/fr/versions.html")) 
        echo " |&nbsp;<A HREF='../doc/fr/versions.html' class='copyright' title='Historique des versions' target='blank'>Nouveautés</A>";
      else
        echo " |&nbsp;<A HREF='http://www.intramessenger.net/doc/versions.html' class='copyright' title='Historique des versions' target='blank'>Nouveautés</A>";
    }
    else
    {
      if (is_readable("../doc/en/changelog.html")) 
        echo " |&nbsp;<A HREF='../doc/en/changelog.html' class='copyright' title='Version history' target='blank'>ChangeLog</A>";
      else
        echo " |&nbsp;<A HREF='http://www.intramessenger.net/doc/changelog.html' class='copyright' title='Version history' target='blank'>ChangeLog</A>";
    }
    echo " |&nbsp;<a href='http://www.intramessenger.net/custom-version.php?lang=" . $lang . "&' class='copyright' target='_blank'>" . $l_menu_customize . "</A>";
    echo " |&nbsp;<a href='donate.php?lang=" . $lang . "&' class='copyright'>" . $l_menu_donate . "</A>";
    echo "</span>";
    $current_folder  = getcwd() . "/"; 
    if ( (substr_count($current_folder, "/admin_demo/") > 0) or (substr_count($current_folder, "\admin_demo/") > 0) ) 
    {
    /*
      echo '<BR/>';
      echo '<BR/>';
      echo '<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.intramessenger.net%2F&amp;layout=standard&amp;show_faces=false&amp;width=500&amp;action=recommend&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:23px;" allowTransparency="true"></iframe><BR/>';
    */
    }
    else if (  ( strpos($_SERVER['HTTP_USER_AGENT'], "Gecko") == 0 ) and ( strpos(" " . $_SERVER['HTTP_USER_AGENT'], "Opera") == 0 )  ) // "MSIE"
      {
        echo '<BR/><BR/><A HREF="http://getfirefox.com/" TARGET="_blank">';
        echo '<img src="../images/get_firefox.png" border="0" title="FireFox (Mozilla)" align="center" width="80" height="15" ALIGN="TOP"></A><BR/>';
      }
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE>";
}


function display_nb_row_page($nb, $nb_row_by_page, $from)
{
  GLOBAL $tri, $page, $lang;
  //
  if (intval($nb) == intval($nb_row_by_page) ) 
    echo "<B>" . $nb . "</B>";
  else
  {
    echo "<A HREF='set_cookies.php?action=" . $from . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "&nb_row_by_page=" . $nb . "&'>" . $nb . "</A>";
  }
}

function color_num_version($version)
{
  if ( (substr($version, 0, 3) == "1.0") or (substr($version, 0, 3) == "1.1") or (substr($version, 0, 4) == "1.20") )
  {
    //if ($version == "1.06Q")
    //  echo "<font color='#FF9933'>";
    //else
      echo "<font color='red'>";
  }
  if ( (substr($version, 0, 4) == "1.21") or (substr($version, 0, 4) == "1.22") )
    echo "<font color='#CC6600'>";
  //
  echo $version;
}

?>
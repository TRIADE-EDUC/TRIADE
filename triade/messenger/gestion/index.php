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
if (isset($_COOKIE['im_dashboard_show_os'])) $im_dashboard_show_os = $_COOKIE['im_dashboard_show_os'];  else  $im_dashboard_show_os = '1';
if (isset($_COOKIE['im_dashboard_show_os_graph'])) $im_dashboard_show_os_graph = $_COOKIE['im_dashboard_show_os_graph'];  else  $im_dashboard_show_os_graph = '0';
if (isset($_COOKIE['im_dashboard_show_gender'])) $im_dashboard_show_gender = $_COOKIE['im_dashboard_show_gender'];  else  $im_dashboard_show_gender = '1';
if (isset($_COOKIE['im_dashboard_show_gender_graph'])) $im_dashboard_show_gender_graph = $_COOKIE['im_dashboard_show_gender_graph'];  else  $im_dashboard_show_gender_graph = '0';
if (isset($_COOKIE['im_dashboard_show_browser'])) $im_dashboard_show_browser = $_COOKIE['im_dashboard_show_browser'];  else  $im_dashboard_show_browser = '1';
if (isset($_COOKIE['im_dashboard_show_browser_graph'])) $im_dashboard_show_browser_graph = $_COOKIE['im_dashboard_show_browser_graph'];  else  $im_dashboard_show_browser_graph = '0';
if (isset($_COOKIE['im_dashboard_show_email'])) $im_dashboard_show_email = $_COOKIE['im_dashboard_show_email'];  else  $im_dashboard_show_email = '1';
if (isset($_COOKIE['im_dashboard_show_email_graph'])) $im_dashboard_show_email_graph = $_COOKIE['im_dashboard_show_email_graph'];  else  $im_dashboard_show_email_graph = '0';
if (isset($_COOKIE['im_dashboard_show_language'])) $im_dashboard_show_language = $_COOKIE['im_dashboard_show_language'];  else  $im_dashboard_show_language = '1';
if (isset($_COOKIE['im_dashboard_show_language_graph'])) $im_dashboard_show_language_graph = $_COOKIE['im_dashboard_show_language_graph'];  else  $im_dashboard_show_language_graph = '1';
if (isset($_COOKIE['im_dashboard_show_country'])) $im_dashboard_show_country = $_COOKIE['im_dashboard_show_country'];  else  $im_dashboard_show_country = '1';
if (isset($_COOKIE['im_dashboard_show_country_graph'])) $im_dashboard_show_country_graph = $_COOKIE['im_dashboard_show_country_graph'];  else  $im_dashboard_show_country_graph = '1';
if (isset($_COOKIE['im_dashboard_show_timezone'])) $im_dashboard_show_timezone = $_COOKIE['im_dashboard_show_timezone'];  else  $im_dashboard_show_timezone = '1';
if (isset($_COOKIE['im_dashboard_show_timezone_graph'])) $im_dashboard_show_timezone_graph = $_COOKIE['im_dashboard_show_timezone_graph'];  else  $im_dashboard_show_timezone_graph = '0';
//
if (intval($im_dashboard_show_os) <= 0) $im_dashboard_show_os = "";
if (intval($im_dashboard_show_os_graph) <= 0) $im_dashboard_show_os_graph = "";
if (intval($im_dashboard_show_gender) <= 0) $im_dashboard_show_gender = "";
if (intval($im_dashboard_show_gender_graph) <= 0) $im_dashboard_show_gender_graph = "";
if (intval($im_dashboard_show_browser) <= 0) $im_dashboard_show_browser = "";
if (intval($im_dashboard_show_browser_graph) <= 0) $im_dashboard_show_browser_graph = "";
if (intval($im_dashboard_show_email) <= 0) $im_dashboard_show_email = "";
if (intval($im_dashboard_show_email_graph) <= 0) $im_dashboard_show_email_graph = "";
if (intval($im_dashboard_show_language) <= 0) $im_dashboard_show_language = "";
if (intval($im_dashboard_show_language_graph) <= 0) $im_dashboard_show_language_graph = "";
if (intval($im_dashboard_show_country) <= 0) $im_dashboard_show_country = "";
if (intval($im_dashboard_show_country_graph) <= 0) $im_dashboard_show_country_graph = "";
if (intval($im_dashboard_show_timezone) <= 0) $im_dashboard_show_timezone = "";
if (intval($im_dashboard_show_timezone_graph) <= 0) $im_dashboard_show_timezone_graph = "";
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['checkversion'])) $checkversion = $_GET['checkversion']; else $checkversion = "";
//
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("../common/constant.inc.php");
require ("lang.inc.php");
//
//
// On vérifie les dernières options :
$txt_const = "";
if (!defined("_SHOUTBOX_ALLOW_SCROLLING"))                  $txt_const .= "_SHOUTBOX_ALLOW_SCROLLING";
if (!defined("_FORCE_OPTION_FILE_FROM_SERVER"))             $txt_const .= "_FORCE_OPTION_FILE_FROM_SERVER";
if (!defined("_GROUP_ID_DEFAULT_FOR_NEW_USER"))             $txt_const .= "_GROUP_ID_DEFAULT_FOR_NEW_USER";
if (!defined("_SHARE_FILES_ALLOW_ACCENT"))                  $txt_const .= "_SHARE_FILES_ALLOW_ACCENT";
if (!defined("_BACKUP_FILES_FORCE_EVERY_DAY_AT"))           $txt_const .= "_BACKUP_FILES_FORCE_EVERY_DAY_AT";
if (!defined("_BACKUP_FILES"))                              $txt_const .= "_BACKUP_FILES";
if (!defined("_SLOW_NOTIFY"))                               $txt_const .= "_SLOW_NOTIFY";
if (!defined("_ALLOW_COL_FUNCTION_NAME"))                   $txt_const .= "_ALLOW_COL_FUNCTION_NAME";
if (!defined("_OUTOFDATE_AFTER_NOT_USE_DURATION"))          $txt_const .= "_OUTOFDATE_AFTER_NOT_USE_DURATION";
if ($txt_const != "")
{
  echo "<html><head>";
  echo '<META http-equiv="refresh" content="3;url=check.php"> ';
  echo "</head>";
  echo "<body>";
  echo "<div class='notice'>";
  //echo "<FONT COLOR='RED'>" . $l_index_after_upd_chk . " </FONT><I>";
  echo $l_index_after_upd_chk . " <I>";
  if (is_readable("check.php")) 
    echo  "<A HREF='check.php'>";
  //
  echo "<IMG src='" . _FOLDER_IMAGES . "menu_check.png' border='0'>Check config</I></A>";
  echo "</div>";
  echo "<BR/>";
  die();
}
//
require ("../common/acp_sessions.inc.php");
//
function f_mysql_table_exists($table , $db) 
{
  $requete = "SHOW TABLES LIKE '" . $table . "' ";
  $result = mysqli_query($id_connect, $requete);
  //
  return mysqli_num_rows($result);
}
//
function aff_img_evolution($sens, $value)
{
  GLOBAL $l_index_trend_7_days;
  //
  if ($sens == "<") echo "<IMG src='" . _FOLDER_IMAGES . "evo_0.png' ALT='" . $l_index_trend_7_days . "' TITLE='" . $l_index_trend_7_days . "' WIDTH='16' HEIGHT='16' border='0' />";
  if ($sens == "=") echo "<IMG src='" . _FOLDER_IMAGES . "evo_1.png' ALT='" . $l_index_trend_7_days . "' TITLE='" . $l_index_trend_7_days . "' WIDTH='16' HEIGHT='16' border='0' />";
  if ($sens == ">") echo "<IMG src='" . _FOLDER_IMAGES . "evo_2.png' ALT='" . $l_index_trend_7_days . "' TITLE='" . $l_index_trend_7_days . "' WIDTH='16' HEIGHT='16' border='0' />";
}


//
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IntraMessenger] " . $l_menu_index . "</title>";
display_header();
echo '<META http-equiv="refresh" content="600;url="> ';
require ("index_graph.inc.php");
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<CENTER>";
echo "<font face='verdana' size='2'>";
if (_MAINTENANCE_MODE != '')
{
  echo "<BR/>";
  echo "<H2>" . $l_index_welcome . "</H2>";
  echo "<BR/>";
  echo "<BR/>";
  echo $l_index_soon_dashboard_here . "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  //echo $l_index_can_cfg . " <I>../common/config/config.inc.php</I><BR/>";
  //echo $l_index_can_lng . " (" . $l_index_actualy . " : " . $l_lang_name . ") " . $l_index_chg . " : <B><I>define('_LANG, '</I>". $lang . "<I>');</I></B><BR/>";
  echo "<BR/>";
  echo "<BR/>";
  $fic_doc = "../doc/en/install.html";
  if ($lang == 'FR') $fic_doc = "../doc/fr/install.html";
  if ($lang == 'IT') $fic_doc = "../doc/it/install.html";
  echo $l_index_find_doc . " <I>";
  if (is_readable($fic_doc)) echo  "<A HREF='" . $fic_doc . "#install' target='_blank'>";
  //
  echo $fic_doc . "</I></A>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  /*
  if (is_readable("../install/install.php")) 
  {
    echo $l_index_chk_opt . " <I>";
    echo  "<A HREF='../install/install.php' target='_blank'>";
    echo "../install/install.php</I></A>";
    echo "<BR/>";
  }
  echo "<BR/>";
  */
  echo "<div class='notice'>";
  //echo "<FONT COLOR='RED'>" . $l_index_after_upd_chk . " </FONT><I>";
  echo $l_index_after_upd_chk . " <I>";
  if (is_readable("check.php")) 
    echo  "<A HREF='check.php'>";
  //
  echo "<IMG src='" . _FOLDER_IMAGES . "menu_check.png' border='0'>Check config</I></A>";
  echo "</div>";
  echo "<BR/>";
}
else
{
  $repertoire  = getcwd() . "/"; 
  $demo_folder = "";
  if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) $demo_folder = "X";
  //
#  require ("../common/sql.inc.php");
  require ("../common/shoutbox.inc.php");
  //
  $requete = "select CURTIME() ";
  $result = mysqli_query($id_connect, $requete);
  //if (!$result) error_sql_log("[ERR-A2a]", $requete);
  list ($time_server_mysql) = mysqli_fetch_row ($result);
  $time_server_mysql = date($l_time_format_display, strtotime($time_server_mysql));
  $time_server_php = date($l_time_format_display);
  //
  //
  $requete  = " select count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1a]", $requete);
  list ($nb_row_stats) = mysqli_fetch_row ($result);
  //
  //$requete  = " select max(STA_NB_MSG), max(STA_NB_CREAT), max(STA_NB_SESSION), max(STA_NB_USR), max(STA_DATE) ";
  //$requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
  //$result = mysqli_query($id_connect, $requete);
  //if (!$result) error_sql_log("[ERR-K1b]", $requete);
  //list ($max_nb_msg, $max_nb_creat, $max_nb_session, $max_nb_user, $max_dat) = mysqli_fetch_row ($result);
  //
  $nb_user_perim = 0;
  $total_nb_messages = 0;
  $total_nb_users = 0;
  $total_nb_create = 0;
  if (intval($nb_row_stats) > 2)
  {
    $requete  = " select STA_NB_MSG, STA_DATE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " order by STA_NB_MSG desc ";
    $requete .= " limit 0, 1";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1b]", $requete);
    list ($max_nb_msg, $max_nb_msg_dat) = mysqli_fetch_row ($result);
    //
    $requete  = " select STA_NB_CREAT, STA_DATE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " order by STA_NB_CREAT desc ";
    $requete .= " limit 0, 1";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1c]", $requete);
    list ($max_nb_creat, $max_nb_creat_dat) = mysqli_fetch_row ($result);
    //
    $requete  = " select STA_NB_SESSION, STA_DATE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " order by STA_NB_SESSION desc ";
    $requete .= " limit 0, 1";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1d]", $requete);
    list ($max_nb_session, $max_nb_session_dat) = mysqli_fetch_row ($result);
    //
    $requete  = " select STA_NB_USR, STA_DATE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " order by STA_NB_USR desc ";
    $requete .= " limit 0, 1";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1e]", $requete);
    list ($max_nb_user, $max_nb_user_dat) = mysqli_fetch_row ($result);
    //
    if (_SHOUTBOX != "")
    {
      $requete  = " select STA_SBX_NB_MSG, STA_DATE ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      $requete .= " order by STA_SBX_NB_MSG desc ";
      $requete .= " limit 0, 1";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1e2]", $requete);
      list ($max_sbx_nb_user, $max_sbx_nb_user_dat) = mysqli_fetch_row ($result);
    }
    //
    $requete  = " select sum(STA_NB_MSG) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wa]", $requete);
    list ($total_nb_messages) = mysqli_fetch_row ($result);
    //
    $requete  = " select sum(STA_NB_USR) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wb]", $requete);
    list ($total_nb_users) = mysqli_fetch_row ($result);
    //
    $requete  = " select sum(STA_NB_CREAT) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wc]", $requete);
    list ($total_nb_create) = mysqli_fetch_row ($result);
  }
  //
  $nbmax_days = f_nb_days_usage_max();
  //
  $nb_messages_evol = "";
  $nb_users_evol = "";
  $nb_create_evol = "";
  $nb_messages_7_evol = "";
  $nb_users_7_evol = "";
  $nb_create_7_evol = "";
  $most_connect_username = "";
  if (intval($nb_row_stats) > 60)
  {
    $requete  = " select sum(STA_NB_MSG) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 60 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz1]", $requete);
    list ($nb_messages_60) = mysqli_fetch_row ($result);
    //
    $requete  = " select sum(STA_NB_USR) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 60 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz2]", $requete);
    list ($nb_users_60) = mysqli_fetch_row ($result);
    //
    $requete  = " select sum(STA_NB_CREAT) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 60 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz3]", $requete);
    list ($nb_create_60) = mysqli_fetch_row ($result);
    //
    $requete  = " select count(STA_DATE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 60 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz4]", $requete);
    list ($nb_60) = mysqli_fetch_row ($result);
    //
    //
    $requete  = " select sum(STA_NB_MSG) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 7 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz5]", $requete);
    list ($nb_messages_7) = mysqli_fetch_row ($result);
    //
    $requete  = " select sum(STA_NB_USR) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 7 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz6]", $requete);
    list ($nb_users_7) = mysqli_fetch_row ($result);
    //
    $requete  = " select sum(STA_NB_CREAT) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 7 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz7]", $requete);
    list ($nb_create_7) = mysqli_fetch_row ($result);
    //
    $requete  = " select count(STA_DATE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= 7 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1wz8]", $requete);
    list ($nb_7) = mysqli_fetch_row ($result);
    //
    if ($nb_7 > 0)
    {
      $nb_messages_7_evol = round($nb_messages_7 / $nb_7);
      $nb_users_7_evol = round($nb_users_7 / $nb_7);
      $nb_create_7_evol = round($nb_create_7 / $nb_7);
      //  ($total_nb_messages > 2) and ($total_nb_users > 2) and ($total_nb_create > 2) )
      if ( ($nb_60 > 30) and ($nb_7 > 2) )
      {
        if ( $nb_messages_7_evol < round($nb_messages_60 / $nb_60) ) $nb_messages_evol = "<";
        if ( $nb_messages_7_evol == round($nb_messages_60 / $nb_60) ) $nb_messages_evol = "=";
        if ( $nb_messages_7_evol > round($nb_messages_60 / $nb_60) ) $nb_messages_evol = ">";
        //
        if ( $nb_users_7_evol < round($nb_users_60 / $nb_60) ) $nb_users_evol = "<";
        if ( $nb_users_7_evol == round($nb_users_60 / $nb_60) ) $nb_users_evol = "=";
        if ( $nb_users_7_evol > round($nb_users_60 / $nb_60) ) $nb_users_evol = ">";
        //
        if ( $nb_create_7_evol < round($nb_create_60 / $nb_60) ) $nb_create_evol = "<";
        if ( $nb_create_7_evol == round($nb_create_60 / $nb_60) ) $nb_create_evol = "=";
        if ( $nb_create_7_evol > round($nb_create_60 / $nb_60) ) $nb_create_evol = ">";
      }
    }
    //
    //
    // Most connected user :
    if (_ENTERPRISE_SERVER == "")
    {
      $requete  = " SELECT USR_USERNAME, USR_NICKNAME, ID_USER, TO_DAYS(USR_DATE_LAST)-TO_DAYS(USR_DATE_CREAT) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " WHERE USR_NB_CONNECT = " . $nbmax_days;
      $requete .= " and USR_STATUS = 1 ";
      $requete .= " limit 0, 3"; // for speed
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1f3a]", $requete);
      if ( mysqli_num_rows($result) == 1 ) // if only ONE !
      {
        list ($most_connect_username, $t, $id_most_connect_user, $most_connect_days) = mysqli_fetch_row ($result);
        if ($t != "") $most_connect_username = $t;
        //
        // Corriger ancien bug de décompte (max +1 par jour) :
        if ($nbmax_days > $most_connect_days)
        {
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " SET USR_NB_CONNECT = " . $most_connect_days;
          $requete .= " WHERE ID_USER = " . $id_most_connect_user;
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1f3b]", $requete);
        }
      }
    }
    //
    //
    // Liste des utilisateurs "désactivés" par le role : ROLE_OFFLINE_MODE (pour ne pas les verrouillés en les croyant à tort comme "ghots users").
    //
    $list_id_users_role_offline_mode = "";
    if ( (_ROLES_TO_OVERRIDE_PERMISSIONS != "") and ( (intval(_LOCK_AFTER_NO_ACTIVITY_DURATION) >= 10) or (intval(_LOCK_AFTER_NO_CONTACT_DURATION) >= 10) ) )
    {
      $requete  = " SELECT USR.ID_USER ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE RLM ";
      $requete .= " WHERE USR.ID_ROLE = RLM.ID_ROLE ";
      $requete .= " and RLM.ID_MODULE = 105 "; // ROLE_OFFLINE_MODE
      //$requete .= " and RLM.RLM_STATE = 2 ";
      $requete .= " ";
      $requete .= " ";
      $requete .= " ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1f5c]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($tid) = mysqli_fetch_row ($result) )
        {
          $list_id_users_role_offline_mode .= $tid . ", ";
        }
      }
      if (strlen($list_id_users_role_offline_mode) > 2)
      {
        // on enlève la dernière virgule en trop.
        $list_id_users_role_offline_mode = substr($list_id_users_role_offline_mode, 0, (strlen($list_id_users_role_offline_mode)-2) );
      }
    }
    //
    // Unactive ghost users :
    //
    if (intval(_LOCK_AFTER_NO_ACTIVITY_DURATION) >= 10 )
    {
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "USR_USER ";
      //$requete .= " SET USR_STATUS = 2 "; // Lock (wait admin)
      $requete .= " SET USR_STATUS = 4 "; // Lock
      $requete .= " WHERE DATEDIFF(CURDATE() , USR_DATE_ACTIVITY) > " . intval(_LOCK_AFTER_NO_ACTIVITY_DURATION);
      $requete .= " or ( USR_DATE_ACTIVITY = '0000-00-00' and DATEDIFF(CURDATE() , USR_DATE_CREAT) > " . intval(_LOCK_AFTER_NO_ACTIVITY_DURATION) . " )";  
      if ($list_id_users_role_offline_mode != "") 
      {
        $requete .= " and ID_USER not in (" . $list_id_users_role_offline_mode . ") ";  
      }
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1f5a]", $requete);
    }
    if (intval(_LOCK_AFTER_NO_CONTACT_DURATION) >= 10 )
    {
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "USR_USER USR LEFT JOIN " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT ON USR.ID_USER = CNT.ID_USER_1 ";
      //$requete .= " SET USR_STATUS = 2 "; // Lock (wait admin)
      $requete .= " SET USR.USR_STATUS = 4 "; // Lock
      $requete .= " WHERE CNT.ID_USER_1 is null ";
      $requete .= " and DATEDIFF(CURDATE() , USR_DATE_CREAT) > " . intval(_LOCK_AFTER_NO_CONTACT_DURATION);
      $requete .= " and ( USR_DATE_ACTIVITY = '0000-00-00' or DATEDIFF(CURDATE() , USR_DATE_ACTIVITY) > 10 ) ";  
      $requete .= " and USR.USR_STATUS = 1 ";
      if ($list_id_users_role_offline_mode != "") 
      {
        $requete .= " and USR.ID_USER not in (" . $list_id_users_role_offline_mode . ") ";  
      }
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1f5b]", $requete);
    }
  }
  //
  $requete  = " select STA_NB_CREAT, STA_NB_SESSION ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
  $requete .= " WHERE STA_DATE = '" . date("Y-m-d") . "' ";
  $requete .= " limit 0, 1";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1f1]", $requete);
  list ($nb_create, $nb_session) = mysqli_fetch_row ($result);
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
  $requete .= " where ID_USER_AUT = -99 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1f2]", $requete);
  list ($nb_admin_msg_not_read) = mysqli_fetch_row ($result);
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  //$requete .= " WHERE (USR_CHECK = 'WAIT' or USR_STATUS = 2) ";
  $requete .= " WHERE USR_STATUS = 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1f3]", $requete);
  list ($nb_user_waiting) = mysqli_fetch_row ($result);
  //
  $nb_bookmarks = 0;
  if ( (_BOOKMARKS != "") and (_BOOKMARKS_NEED_APPROVAL != "") )
  {
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE BMK_DISPLAY < 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1f4]", $requete);
    list ($nb_bookmarks) = mysqli_fetch_row ($result);
  }
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE USR_STATUS = 9 ";
  //$requete .= " WHERE USR_STATUS = 9 or (USR_CHECK = '' and USR_NAME = 'LEAVE SERVER') "; //   < version 2.0
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1y]", $requete);
  list ($nb_user_leave) = mysqli_fetch_row ($result);
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE (USR_CHECK = '' or USR_STATUS = 3) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1k]", $requete);
  list ($nb_user_valid) = mysqli_fetch_row ($result);
  //
  $requete  = " SELECT AVG(USR_NB_CONNECT) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE USR_NB_CONNECT > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1L]", $requete);
  list ($nb_connect) = mysqli_fetch_row ($result);
  //

  if (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) > 10)
  {
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) . " ";
    $requete .= " and USR_STATUS <> 4 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A3c]", $requete);
    list ($nb_user_perim) = mysqli_fetch_row ($result);
  }
  //
  //
  // SELECT min(USR_DATE_ACTIVITY ) FROM `IM_USR_USER` WHERE USR_DATE_ACTIVITY > 0 // la date la plus ancienne
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_ACTIVITY) >= 30 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A3d]", $requete);
  list ($nb_user_activite_more_30) = mysqli_fetch_row ($result);
  if ($nb_user_activite_more_30 > 0)
  {
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_ACTIVITY) < 30 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-A3e]", $requete);
    list ($nb_user_activite_recent) = mysqli_fetch_row ($result);
  }
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
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1t]", $requete);
  list ($nb_users) = mysqli_fetch_row ($result);
  //
  $last_username = "";
  $id_last_user = 0;
  if ($nb_users > 2)
  {
    $requete  = " select max(ID_USER) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1t2]", $requete);
    list ($id_last_user) = mysqli_fetch_row ($result);
    //$last_username = f_get_username_of_id($id_last_user);
    $last_username = f_get_username_nickname_of_id($id_last_user);
  }
  //
  //
  //if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  {
    if (_GROUP_USER_CAN_JOIN != "") 
    {
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USG_USERGRP ";
      $requete .= " WHERE ( USG_PENDING = 1 or USG_PENDING = -1 ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1z]", $requete);
      list ($nb_users_pending_group) = mysqli_fetch_row ($result);
    }
  }
  //
  //
  if (_SHARE_FILES != "")
  {
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE = 'Y' ";
    $requete .= " and ID_USER_DEST is null ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1pm]", $requete);
    list ($nb_file_share) = mysqli_fetch_row ($result);
    //
    $nb_file_download = 0;
    if ($nb_file_share > 9)
    {
      $requete  = " select sum(FIL_NB_DOWNLOAD) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
      $requete .= " WHERE ID_USER_DEST is null ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1pm2]", $requete);
      list ($nb_file_download) = mysqli_fetch_row ($result);
    }
    //
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE = 'T' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1pp]", $requete);
    list ($nb_file_share_trash) = mysqli_fetch_row ($result);
    //
    //$nb_file_pending = 0;
    //if ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") )
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE = 'W' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1pn]", $requete);
    list ($nb_file_pending) = mysqli_fetch_row ($result);
    //
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE = 'Z' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1ps]", $requete);
    list ($nb_file_alert) = mysqli_fetch_row ($result);
    //
    $requete  = " select SQL_CACHE SUM(FIL_SIZE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1py]", $requete);
    list ($t_size_ko) = mysqli_fetch_row ($result);
    $file_share_size_mo = ($t_size_ko / 1024);
    $file_share_size_mo = ceil($file_share_size_mo);
    //
    if (_SHARE_FILES_VOTE != "")
    {
      // Nbre de vote +
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
      $requete .= " WHERE FLV_VOTE_M > 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1pq]", $requete);
      list ($share_files_nb_votes_p) = mysqli_fetch_row ($result);
      //
      // Nbre de vote -
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
      $requete .= " WHERE FLV_VOTE_L < 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1pr]", $requete);
      list ($share_files_nb_votes_c) = mysqli_fetch_row ($result);
    }
  }
  //
  //
  if (_BACKUP_FILES != "")
  {
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE FIB_ONLINE = 'Y' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1pt]", $requete);
    list ($nb_file_backup) = mysqli_fetch_row ($result);
    //
    $requete  = " select count(distinct(ID_USER)) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE FIB_ONLINE = 'Y' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1pu]", $requete);
    list ($nb_file_user_backup) = mysqli_fetch_row ($result);
    //
    $requete  = " select SUM(FIB_SIZE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE FIB_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1pv]", $requete);
    list ($t_size_ko) = mysqli_fetch_row ($result);
    $file_backup_size_mo = ($t_size_ko / 1024);
    $file_backup_size_mo = ceil($file_backup_size_mo);
    //$file_backup_size_mo = (ceil($file_backup_size_mo * 10) / 10);
  }
  //
  //
  if (_SHOUTBOX != "")
  {
    $sbx_nb_user_locked_reject = 0;
    $sbx_nb_user_locked_votes = 0;
    $sbx_nb_votes_p = 0;
    $sbx_nb_votes_c = 0;
    $sbx_nb_votes_max_p = 0;
    $sbx_nb_votes_max_c = 0;
    $sbx_nb_votes_max_tot_p = 0;
    $sbx_nb_votes_max_tot_c = 0;
    $sbx_nb_votes_max_p_id_user = 0;
    $sbx_nb_votes_max_c_id_user = 0;
    $sbx_nb_votes_max_tot_p_username = 0;
    $sbx_nb_votes_max_tot_c_username = 0;
    //
    // Ménage avant affichage :
    shoutbox_remove_old_msg();
    //  
    // Nbre de messages (approuvés)
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " where SBX_DISPLAY > 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1SBa]", $requete);
    list ($sbx_nb_msg_ok) = mysqli_fetch_row ($result);
    //
    // Nbre de messages en attente d'approbation
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " where SBX_DISPLAY = 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1SBb]", $requete);
    list ($sbx_nb_msg_wait) = mysqli_fetch_row ($result);
    //
    // Nbre de messages rejetés
    $requete  = " SELECT SUM(SBS_NB_REJECT) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1SBc]", $requete);
    list ($sbx_nb_msg_deleted) = mysqli_fetch_row ($result);
    //
    // Nbre total de messages 
    $requete  = " SELECT SUM(SBS_NB) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K1SBn]", $requete);
    list ($sbx_nb_msg_total) = mysqli_fetch_row ($result);
    //
    if ( (_SHOUTBOX_NEED_APPROVAL != "") and (intval(_SHOUTBOX_LOCK_USER_APPROVAL) > 0) )
    {
      // Nbre d'utilisateurs verrouillés suite refus
      $requete  = " select count(ID_USER_AUT) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
      $requete .= " where SBS_NB_REJECT >= " . intval(_SHOUTBOX_LOCK_USER_APPROVAL);
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBd]", $requete);
      list ($sbx_nb_user_locked_reject) = mysqli_fetch_row ($result);
    }
    //
    if (_SHOUTBOX_VOTE != "")
    {
      if (intval(_SHOUTBOX_LOCK_USER_VOTES) > 0)
      {
        // Nbre d'utilisateurs verrouillés suite aux votes
        $requete  = " SELECT ID_USER_AUT, count(*) as nb ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
        $requete .= " WHERE SBV_VOTE_L < 0 ";
        ## $requete .= " and SBV_DATE = CURDATE() ";                                  // only today
        ## $requete .= " and TIMESTAMPDIFF(WEEK, SBV_DATE, CURDATE() ) = 0 ";         // only this week
        $requete .= " group by ID_USER_AUT ";
        $requete .= " having nb > " . intval(_SHOUTBOX_LOCK_USER_VOTES);
        $requete .= " limit 1";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-K1SBe]", $requete);
        list ($sbx_nb_user_locked_votes) = mysqli_fetch_row ($result);
      }
      //
      // Nbre de vote +
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
      $requete .= " WHERE SBV_VOTE_M > 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBf]", $requete);
      list ($sbx_nb_votes_p) = mysqli_fetch_row ($result);
      //
      // Nbre de vote -
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
      $requete .= " WHERE SBV_VOTE_L < 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBg]", $requete);
      list ($sbx_nb_votes_c) = mysqli_fetch_row ($result);
      //
      // Auteur ayant le plus de vote + (nombre mini : 5) actuellement
      $requete  = " SELECT ID_USER_AUT, max(SBS_NB_VOTE_M) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
      $requete .= " group by ID_USER_AUT ";
      $requete .= " having max(SBS_NB_VOTE_M) >= 5";
      $requete .= " order by SBS_NB_VOTE_M DESC ";
      $requete .= " limit 1";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBh]", $requete);
      list ($sbx_nb_votes_max_p_id_user, $sbx_nb_votes_max_p) = mysqli_fetch_row ($result);
      //$sbx_nb_votes_max_p_username = f_get_username_of_id($sbx_nb_votes_max_p_id_user);
      $sbx_nb_votes_max_p_username = f_get_username_nickname_of_id($sbx_nb_votes_max_p_id_user);
      //
      // Auteur ayant le plus de vote - (nombre mini : 5) actuellement
      $requete  = " SELECT ID_USER_AUT, max(SBS_NB_VOTE_L) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
      $requete .= " group by ID_USER_AUT ";
      $requete .= " having max(SBS_NB_VOTE_L) >= 5";
      $requete .= " order by SBS_NB_VOTE_L DESC ";
      $requete .= " limit 1";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBj]", $requete);
      list ($sbx_nb_votes_max_c_id_user, $sbx_nb_votes_max_c) = mysqli_fetch_row ($result);
      //$sbx_nb_votes_max_c_username = f_get_username_of_id($sbx_nb_votes_max_c_id_user);
      $sbx_nb_votes_max_c_username = f_get_username_nickname_of_id($sbx_nb_votes_max_c_id_user);
      //
      // Auteur ayant le plus de vote + sur un message (nombre mini : 5) en tout
      $requete  = " SELECT ID_USER_AUT, max(SBS_MAX_VOTE_M) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
      $requete .= " group by ID_USER_AUT ";
      $requete .= " having max(SBS_MAX_VOTE_M) >= 5";
      $requete .= " order by SBS_MAX_VOTE_M DESC ";
      $requete .= " limit 1";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBk]", $requete);
      list ($sbx_nb_votes_max_tot_p_id_user, $sbx_nb_votes_max_tot_p) = mysqli_fetch_row ($result);
      //$sbx_nb_votes_max_tot_p_username = f_get_username_of_id($sbx_nb_votes_max_tot_p_id_user);
      $sbx_nb_votes_max_tot_p_username = f_get_username_nickname_of_id($sbx_nb_votes_max_tot_p_id_user);
      //
      // Auteur ayant le plus de vote - sur un message (nombre mini : 5) en tout
      $requete  = " SELECT ID_USER_AUT, max(SBS_MAX_VOTE_L) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
      $requete .= " group by ID_USER_AUT ";
      $requete .= " having max(SBS_MAX_VOTE_L) >= 5";
      $requete .= " order by SBS_MAX_VOTE_L DESC ";
      $requete .= " limit 1";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1SBm]", $requete);
      list ($sbx_nb_votes_max_tot_c_id_user, $sbx_nb_votes_max_tot_c) = mysqli_fetch_row ($result);
      //$sbx_nb_votes_max_tot_c_username = f_get_username_of_id($sbx_nb_votes_max_tot_c_id_user);
      $sbx_nb_votes_max_tot_c_username = f_get_username_nickname_of_id($sbx_nb_votes_max_tot_c_id_user);
    }
    //
    // 
    // 
    // 
  }
  //
  //
  //
  $repert = "../" . _PUBLIC_FOLDER . "/upload/";
  $nb_avatars = 0;
  if (is_dir($repert)) 
  {
    $rep = opendir($repert);
    $tab_files = array(); // on déclare le tableau contenant le nom des fichiers
    while ($file = readdir($rep))
    {
      if ($file != ".." && $file != "." && $file !="" ) // .inc.php && strpos(strtolower($file), ".*") 
      {
        $ext = strtolower(substr($file,-5));
        if ( (!is_dir($file)) and (strlen($file) <= 20) and ( (strpos($ext, ".png")) or (strpos($ext, ".gif")) or (strpos($ext, ".jpg")) or (strpos($ext, ".jpeg")) ) )
        {
          $tab_files[] = $file;
        }
      }
    }
    closedir($rep);
    //
    if (!empty($tab_files))
    {
      foreach($tab_files as $file) 
      {
          $nb_avatars++;
      }
    }
  }
  //
  //
  /*
  if ( (!is_readable("../im_setup.reg")) and ($demo_folder == "") )
  {
     if ($demo_folder == "") $l_menu_need_reg = str_replace ("im_setup.reg", "<a href='reg.php?lang=" . $lang . "&' title='im_setup.reg'>im_setup.reg</A>", $l_menu_need_reg);
     echo "<div class='notice'><FONT COLOR='BLUE'>" . $l_menu_need_reg . "</font></div>";
     echo "<BR/>";
  }
  */
  //
  //
  
  //echo "<BR/>";
  echo "<TABLE border='0' width='100%'>";
  echo "<TR>";
  echo "<TD VALIGN='TOP' ALIGN='CENTER'>";



      /* ------------------------------ Time Problem ------------------------------ */
      if ($time_server_php <> $time_server_mysql) 
      {
        //echo "<div class='warning'>";
        //table_time_zone($time_server_php, $time_server_mysql);
        echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
        echo "<TR>";
          echo "<TH align=center COLSPAN='2' class='thHead'>";
          echo "<font face='verdana' size='2'><b>&nbsp; " . $l_time_zone . "</b></font></TH>";
        echo "</TR>";

        echo "<TR>";
          echo "<TD class='row2' width='150'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "PHP Timezone";
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            echo "" . date('e') . " [" . date('T')  . "]";
          echo "</TD>";
        echo "</TR>";

        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "PHP Time";
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='red'>";
            echo "<B>" . $time_server_php . "</B>";
          echo "</TD>";
        echo "</TR>";

        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "MySQL time";
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2' color='red'>";
            echo "<B>" . $time_server_mysql . "</B>";
          echo "</TD>";
        echo "</TR>";

        echo "<TR>";
          echo "<TD align='center' COLSPAN='3' class='catBottom'>";
          echo "<font face='verdana' size='2'>";
          echo "You may setup PHP (or MySQL) time zone";
          echo "</font>";
          echo "</TD>";
        echo "</TR>";

        echo "</TABLE>";
        //echo "</div>";
        echo "<BR/>";
      }















      echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
      echo "<TR>";
        echo "<TH align=center COLSPAN='2' class='thHead'>";
        echo "<font face='verdana' size='2'><b>&nbsp; " . $l_user_informations . "</b></font></TH>";
      echo "</TR>";

      echo "<TR>";
        echo "<TD class='row2' width='300'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          if (intval($nb_user_waiting) > 0) echo "<A HREF='list_users.php?tri=&only_status=w&lang=" . $lang . "&'>";
          echo $l_index_waiting_valid . "</A>";
        echo "</TD>";
        echo "<TD class='row1' align='center'>";
          echo "<font face='verdana' size='2'>";
          if (intval($nb_user_waiting) > 0)
            echo "<font color='red'>";
          else
            echo "<font color='green'>";
          echo "<B>" . $nb_user_waiting . "</B>";
        echo "</TD>";
      echo "</TR>";
      //
      if ( (_BOOKMARKS != "") and (_BOOKMARKS_NEED_APPROVAL != "") )
      {
        echo "<TR>";
          echo "<TD class='row2' width='300'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            if (intval($nb_bookmarks) > 0) echo "<A HREF='list_bookmarks.php?lang=" . $lang . "&'>";
            echo $l_index_bookmarks_pending . "</A>";
          echo "</TD>";
          echo "<TD class='row1' width='40' align='center'>";
            echo "<font face='verdana' size='2'>";
            if (intval($nb_bookmarks) > 0)
              echo "<font color='red'>";
            else
              echo "<font color='green'>";
            echo "<B>" . $nb_bookmarks . "</B>";
          echo "</TD>";
        echo "</TR>";
      }
      //
      echo "<TR>";
        echo "<TD class='row2' width='300'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          if (intval($nb_avatars) > 0) echo "<A HREF='avatar_changing.php?lang=" . $lang . "&#pending'>";
          echo $l_index_pending_avatars . "</A>";
        echo "</TD>";
        echo "<TD class='row1' width='40' align='center'>";
          echo "<font face='verdana' size='2'>";
          if (intval($nb_avatars) > 0)
            echo "<font color='red'>";
          else
            echo "<font color='green'>";
          echo "<B>" . $nb_avatars . "</B>";
        echo "</TD>";
      echo "</TR>";
      //
      //if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
      if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
      {
        if (_GROUP_USER_CAN_JOIN != "") 
        {
          echo "<TR>";
            echo "<TD class='row2' width='300'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              if (intval($nb_users_pending_group) > 0) echo "<A HREF='list_group.php?lang=" . $lang . "&only_pending=x&'>";
              echo $l_index_users_pending_group . "</A>";
            echo "</TD>";
            echo "<TD class='row1' width='40' align='center'>";
              echo "<font face='verdana' size='2'>";
              if (intval($nb_users_pending_group) > 0)
                echo "<font color='red'>";
              else
                echo "<font color='green'>";
              echo "<B>" . $nb_users_pending_group . "</B>";
            echo "</TD>";
          echo "</TR>";
        }
      }
      //
      if ($demo_folder != "") $nb_admin_msg_not_read = 0;
      echo "<TR>";
        echo "<TD class='row2' width='300'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          if (intval($nb_admin_msg_not_read) > 0) echo "<A HREF='messagerie.php?lang=" . $lang . "&'>";
          echo $l_admin_mess_title_3;
          if (intval($nb_admin_msg_not_read) > 0) echo "</A><BR/>&nbsp;<font size='1' color='gray'>(" .$l_admin_mess_title . ")";
        echo "</TD>";
        echo "<TD class='row1' width='40' align='center'>";
          echo "<font face='verdana' size='2'>";
          if (intval($nb_admin_msg_not_read) > 0)
            echo "<font color='red'>";
          else
            echo "<font color='green'>";
          echo "<B>" . $nb_admin_msg_not_read . "</B>";
        echo "</TD>";
      echo "</TR>";

      //
      if (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            if (intval($nb_user_perim) > 0) echo "<A HREF='user_deleting_older.php?lang=" . $lang . "&'>";
            echo $l_admin_users_out_of_date;
            if (intval($nb_user_perim) > 0) echo "</A>"; //<BR/><font color='gray'>(" .$l_admin_mess_title . ")";
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            if (intval($nb_user_perim) > 0)
              echo "<font color='red'>";
            else
              echo "<font color='green'>";
            echo "<B>" . $nb_user_perim . "</B>";
          echo "</TD>";
        echo "</TR>";
      }
      //
      if (intval($nb_user_leave) > 0)
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            if (intval($nb_user_leave) > 0) echo "<A HREF='list_users.php?tri=&only_status=l&lang=" . $lang . "&'>";
            echo $l_index_leave_users . "</A>";
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            echo $nb_user_leave;
          echo "</TD>";
        echo "</TR>";
      }
      //
      //
      if ( (intval($nb_user_valid) > 0) or (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") or (_PENDING_NEW_AUTO_ADDED_USER != "") or (_PENDING_USER_ON_COMPUTER_CHANGE != "") )
      {
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_ready_users;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            echo $nb_user_valid;
          echo "</TD>";
        echo "</TR>";
      }
      //
      //
      if (intval($nb_row_stats) > 2)
      {
        if ($nb_user_activite_more_30 > 0)
        {
          echo "<TR>";
            echo "<TD class='row2'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              echo $l_index_users_recent_activity;
            echo "</TD>";
            echo "<TD class='row1' align='center'>";
              echo "<font face='verdana' size='2'>";
              echo $nb_user_activite_recent;
            echo "</TD>";
          echo "</TR>";
        }
        //

        if (intval($nb_create) <= 0) $nb_create = 0;
        if (intval($nb_session) <= 0) $nb_session = 0;
        //
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_today_creat_users;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            echo $nb_create;
          echo "</TD>";
        echo "</TR>";
        
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_index_today_sessions;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            echo $nb_session;
          echo "</TD>";
        echo "</TR>";

        if ( ($id_last_user > 0) and ($last_username != "") )
        {
          echo "<TR>";
            echo "<TD class='row2' COLSPAN='2'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              echo $l_index_last_valid_username . " ";
              echo "<A HREF='user.php?id_user=" . $id_last_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
              echo $last_username . "</A>";
            echo "</TD>";
          echo "</TR>";
        }
        
        echo "</TABLE>";
        //echo "<BR/>";




        if (_SHOUTBOX != "")
        {
          echo "<BR/>";
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead' >";
            echo "<font face='verdana' size='2'><b>&nbsp;";
            //if ( (intval($sbx_nb_msg_ok) > 0) and ($sbx_nb_msg_wait <= 0) ) echo "<A HREF='list_shoutbox.php?lang=" . $lang . "&'>";
            echo $l_admin_options_shoutbox_title_long . "&nbsp;</b></font></TH>";
            echo "</TH>";
          echo "</TR>";

          if ($sbx_nb_msg_ok > 0)
          {
            echo "<TR>";
              echo "<TD class='row2'COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                //if ( (intval($sbx_nb_msg_ok) > 0) and ($sbx_nb_msg_wait <= 0) ) echo "<A HREF='list_shoutbox.php?lang=" . $lang . "&'>";
                echo $l_index_shoutbox_nb_msg . "</A>";
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                echo $sbx_nb_msg_ok;
              echo "</TD>";
            echo "</TR>";
          }

            echo "<TR>";
              echo "<TD class='row2'COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_msg . " (" . $l_admin_contact_total . ")";
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                echo $sbx_nb_msg_total;
              echo "</TD>";
            echo "</TR>";


          if ( (_SHOUTBOX_NEED_APPROVAL != "") or ($sbx_nb_msg_wait > 0) )
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                if (intval($sbx_nb_msg_wait) > 0) echo "<A HREF='list_shoutbox.php?lang=" . $lang . "&'>";
                echo $l_index_shoutbox_nb_msg_wait . "</A>";
                //echo $l_index_pending_shoutbox;
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                if (intval($sbx_nb_msg_wait) > 0)
                  echo "<font color='red'>";
                else
                  echo "<font color='green'>";
                echo "<B>" . $sbx_nb_msg_wait . "</B>";
              echo "</TD>";
            echo "</TR>";
          }
          
          if ( (_SHOUTBOX_NEED_APPROVAL != "") or ($sbx_nb_msg_deleted > 0) )
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2' >";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_msg_rejects;
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                if (intval($sbx_nb_msg_deleted) > 0) echo "<font color='red'>";
                echo intval($sbx_nb_msg_deleted);
              echo "</TD>";
            echo "</TR>";
          }

          if ( (_SHOUTBOX_NEED_APPROVAL != "") and (intval(_SHOUTBOX_LOCK_USER_APPROVAL) > 0) )
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_user_lock_rejects;
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                echo $sbx_nb_user_locked_reject;
              echo "</TD>";
            echo "</TR>";
          }

          if (_SHOUTBOX_VOTE != "")
          {
            if (intval(_SHOUTBOX_LOCK_USER_VOTES) > 0)
            {
              echo "<TR>";
                echo "<TD class='row2' COLSPAN='2'>";
                  echo "<font face='verdana' size='2'>&nbsp;";
                  echo $l_index_shoutbox_nb_user_lock_votes;
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='2'>";
                  if ($sbx_nb_user_locked_votes > 0) echo "<font color='red'>";
                  echo $sbx_nb_user_locked_votes;
                echo "</TD>";
              echo "</TR>";
            }
            
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_votes;
                echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " +' TITLE='" . $l_index_shoutbox_nb_votes . " +'>";
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                if ($sbx_nb_votes_p > 0) echo "<font color='green'>";
                echo $sbx_nb_votes_p;
              echo "</TD>";
            echo "</TR>";

            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_votes;
                echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " -' TITLE='" . $l_index_shoutbox_nb_votes . " -'>";
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                if ($sbx_nb_votes_c > 0) echo "<font color='red'>";
                echo $sbx_nb_votes_c;
              echo "</TD>";
            echo "</TR>";

            if ($sbx_nb_votes_max_p_username != "")
            {
              echo "<TR>";
                echo "<TD class='row2'>";
                  echo "<font face='verdana' size='2' color='green'>&nbsp;";
                  //echo $l_index_shoutbox_author_max_more . " &nbsp;&nbsp; <I>" . $sbx_nb_votes_max_p_username . "</I>";
                  echo $l_index_shoutbox_best_author . " ";
                  echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' />";
                  //echo "&nbsp;&nbsp; <I>" . $sbx_nb_votes_max_p_username . "</I>";
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='1' color='green'>";
                  echo $sbx_nb_votes_max_p_username;
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='2' color='green'>";
                  echo $sbx_nb_votes_max_p;
                echo "</TD>";
              echo "</TR>";
            }
            
            if ($sbx_nb_votes_max_c_username != "")
            {
              echo "<TR>";
                echo "<TD class='row2'>";
                  echo "<font face='verdana' size='2' color='red'>&nbsp;";
                  //echo $l_index_shoutbox_author_max_less . " &nbsp;&nbsp; <I>" . $sbx_nb_votes_max_c_username . "</I>";
                  echo $l_index_shoutbox_best_author . " ";
                  echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' />";
                  //echo "&nbsp;&nbsp; <I>" . $sbx_nb_votes_max_c_username . "</I>";
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='1' color='red'>";
                  echo $sbx_nb_votes_max_c_username;
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='2' color='red'>";
                  echo $sbx_nb_votes_max_c;
                echo "</TD>";
              echo "</TR>";
            }
            //
            // Meilleur score + pour UN message
            if ($sbx_nb_votes_max_tot_p_username != "")
            {
              echo "<TR>";
                echo "<TD class='row2'>";
                  echo "<font face='verdana' size='2' color='green'>&nbsp;";
                  echo $l_admin_users_rating . "";
                  //echo " (" . strtolower($l_admin_mess_message) . ") ";
                  echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' />";
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='1' color='green'>";
                  echo $sbx_nb_votes_max_tot_p_username;
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='2' color='green'>";
                  echo $sbx_nb_votes_max_tot_p;
                echo "</TD>";
              echo "</TR>";
            }
            // Meilleur score - pour UN message
            if ($sbx_nb_votes_max_tot_c_username != "")
            {
              echo "<TR>";
                echo "<TD class='row2'>";
                  echo "<font face='verdana' size='2' color='red'>&nbsp;";
                  echo $l_admin_users_rating;
                  echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' />";
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='1' color='red'>";
                  echo $sbx_nb_votes_max_tot_c_username;
                echo "</TD>";
                echo "<TD class='row1' align='center'>";
                  echo "<font face='verdana' size='2' color='red'>";
                  echo $sbx_nb_votes_max_tot_c;
                echo "</TD>";
              echo "</TR>";
            }

              //
           }
/*
        echo "<TR>";
          echo "<TD class='row2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "" .$zzzzz;
          echo "</TD>";
          echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>";
            echo $xxxxx;
          echo "</TD>";
        echo "</TR>";
*/

          echo "</TABLE>";
        }
        
        

        if (_SHARE_FILES != "")
        {
          echo "<BR/>";
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead' >";
            echo "<font face='verdana' size='2'><b>&nbsp;";
            //if ( (intval($sbx_nb_msg_ok) > 0) and ($sbx_nb_msg_wait <= 0) ) echo "<A HREF='list_shoutbox.php?lang=" . $lang . "&'>";
            echo $l_admin_options_share_files_title . "&nbsp;</b></font></TH>";
            echo "</TH>";
          echo "</TR>";
          //
          if (intval($nb_file_share) > 0)
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_admin_share_files_title;
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                echo $nb_file_share;
              echo "</TD>";
            echo "</TR>";
          }
          if (intval($nb_file_download) > 0)
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_share_file_download;
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                echo $nb_file_download;
              echo "</TD>";
            echo "</TR>";
          }
          if ( (intval($nb_file_pending) > 0) or (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") )
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                if (intval($nb_file_pending) > 0) echo "<A HREF='list_files_sharing_pending.php?lang=" . $lang . "&'>";
                echo $l_index_share_file_pending;
                if (intval($nb_file_pending) > 0) echo "</A>";
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                if ($nb_file_pending > 0) echo "<font color='red'><b>";
                echo $nb_file_pending;
              echo "</TD>";
            echo "</TR>";
          }
          if ( (intval($nb_file_share_trash) > 0) or (_SHARE_FILES_TRASH != "") or (_SHARE_FILES_EXCHANGE_TRASH != "") )
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                if (intval($nb_file_share_trash) > 0) echo "<A HREF='list_files_sharing_trash.php?lang=" . $lang . "&'>";
                echo $l_index_share_file_trash;
                if (intval($nb_file_share_trash) > 0) echo "</A>";
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                if ($nb_file_share_trash > 0) 
                  echo "<font color='red'><b>";
                else
                  echo "<font color='green'><b>";
                //
                echo $nb_file_share_trash;
              echo "</TD>";
            echo "</TR>";
          }
          
          //if (intval($nb_file_alert) > 0)
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                if (intval($nb_file_alert) > 0) echo "<A HREF='list_files_sharing_alert.php?lang=" . $lang . "&'>";
                echo $l_index_share_file_alert;
                if (intval($nb_file_alert) > 0) echo "</A>";
              echo "</TD>";
              echo "<TD class='row1' align='center' width='60'>";
                echo "<font face='verdana' size='2'>";
                if ($nb_file_alert > 0) 
                  echo "<font color='red'><b>";
                else
                  echo "<font color='green'><b>";
                echo $nb_file_alert;
              echo "</TD>";
            echo "</TR>";

          if (_SHARE_FILES_VOTE != "")
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_votes;
                echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " +' TITLE='" . $l_index_shoutbox_nb_votes . " +'>";
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                if ($share_files_nb_votes_p > 0) echo "<font color='green'>";
                echo $share_files_nb_votes_p;
              echo "</TD>";
            echo "</TR>";

            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_shoutbox_nb_votes;
                echo " <IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='14' ALT='" . $l_index_shoutbox_nb_votes . " -' TITLE='" . $l_index_shoutbox_nb_votes . " -'>";
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                if ($share_files_nb_votes_c > 0) echo "<font color='red'>";
                echo $share_files_nb_votes_c;
              echo "</TD>";
            echo "</TR>";
          }
          //
          echo "<TR>";
            echo "<TD class='row2' COLSPAN='2'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              echo $l_index_files_workspace;
              if (_SHARE_FILES_MAX_SPACE_SIZE_TOTAL > 0)
              {
                $t_percent = ceil(($file_share_size_mo / _SHARE_FILES_MAX_SPACE_SIZE_TOTAL) * 100);
                echo "<br/>&nbsp;";
                echo "<IMG SRC='" . _FOLDER_IMAGES . f_img_percent($t_percent) . "' ALT='" . $t_percent . "%' TITLE='" . $t_percent . "%' WIDTH='95%' HEIGHT='13' BORDER='0' />";
              }
            echo "</TD>";
            echo "<TD class='row1' align='center'>";
              echo "<font face='verdana' size='2'>";
              echo $file_share_size_mo;
            echo "</TD>";
          echo "</TR>";
          //
          echo "</TABLE>";
        }
        


        if (_BACKUP_FILES != "")
        {
          if ( (intval($nb_file_backup) > 0) and (intval($nb_file_user_backup) > 0) )
          {
            echo "<BR/>";
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
            echo "<TR>";
              echo "<TH align=center COLSPAN='3' class='thHead' >";
              echo "<font face='verdana' size='2'><b>&nbsp;";
              //echo $l_admin_options_backup_files . "&nbsp;</b></font></TH>";
              echo $l_admin_options_backup_files_title . "&nbsp;</b></font></TH>";
              echo "</TH>";
            echo "</TR>";
          //
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
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
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_backup_file_users;
              echo "</TD>";
              echo "<TD class='row1' align='center'>";
                echo "<font face='verdana' size='2'>";
                echo $nb_file_user_backup;
              echo "</TD>";
            echo "</TR>";
            //
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_index_files_workspace;
                if (_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL > 0)
                {
                  $t_percent = ceil(($file_backup_size_mo / _BACKUP_FILES_MAX_SPACE_SIZE_TOTAL) * 100);
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






/*         os    */



        //
        $requete  = " SELECT distinct(USR_GENDER), count(*) as NB";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        //$requete .= " WHERE USR_GENDER <> '' ";
        $requete .= " GROUP by USR_GENDER ";
        $requete .= " ORDER by NB desc ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-K1r]", $requete);
        if ( mysqli_num_rows($result) > 1 )
        {
          echo "<BR/>";
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345
            if ($im_dashboard_show_gender_graph > 0)
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_gender_graph&value=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_gender_graph&value=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            //echo "<font face='verdana' size='2'><b>&nbsp;" . $l_gender . "&nbsp;</b></font></TH>";
            echo "<font face='verdana' size='2'><b>&nbsp;" . $l_gender;
            if ($im_dashboard_show_gender > 0)
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_gender&value=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_gender&value=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
          echo "</TH>";
          echo "</TR>";
          if ($im_dashboard_show_gender_graph > 0)
          {
            echo "<TR>";
              echo "<TD align='center' class='row1' COLSPAN='3'>";
              echo '<div id="graph_gender"></div>';
              echo "</TD>";
            echo "</TR>";
          }
          if ($im_dashboard_show_gender > 0)
          {
            while( list ($tgenre, $nb) = mysqli_fetch_row ($result) )
            {
              echo "<TR>";
              echo "<TD align='left' class='row2'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                if ($tgenre == "M") echo "<IMG SRC='" . _FOLDER_IMAGES . "man.png' WIDTH='16' HEIGHT='16' ALT='" . $l_man . "' TITLE='" . $l_man . "' BORDER='0'> ";
                if ($tgenre == "W") echo "<IMG SRC='" . _FOLDER_IMAGES . "woman.png' WIDTH='16' HEIGHT='16' ALT='" . $l_woman . "' TITLE='" . $l_woman . "' BORDER='0'> ";
                $genre = "?";
                if ($tgenre == "M") $genre = $l_man;
                if ($tgenre == "W") $genre = $l_woman;
                echo $genre;
                echo "&nbsp;";
              echo "</font></TD>";
              echo "<TD class='row1' align='center' width='50'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo $nb;
              echo "</font></TD>";
              echo "<TD class='row2' align='center' width='60'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo round($nb / $nb_users * 100, 1);
                echo " %";
              echo "</font></TD>";
              echo "</TR>";
              echo "\n";
            }
          }
          echo "</TABLE>";
        }
        //



        if (_ENTERPRISE_SERVER != "")
        {
          //
          $requete  = " SELECT distinct(USR_BROWSER), count(*) as NB";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " WHERE USR_BROWSER <> '' ";
          $requete .= " GROUP by USR_BROWSER ";
          $requete .= " ORDER by NB desc, USR_BROWSER ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1xa]", $requete);
          if ( mysqli_num_rows($result) > 1 )
          {
            echo "<BR/>";
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
            echo "<TR>";
              echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345
              if ($im_dashboard_show_browser_graph > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_browser_graph&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_browser_graph&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              echo "<font face='verdana' size='2'><b>&nbsp;" . $l_admin_users_col_browser . "&nbsp;"; //</b></font></TH>";
              if ($im_dashboard_show_browser > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_browser&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_browser&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
            echo "</TH>";
            echo "</TR>";
            if ($im_dashboard_show_browser_graph > 0)
            {
              echo "<TR>";
                echo "<TD align='center' class='row1' COLSPAN='3'>";
                echo '<div id="graph_browser"></div>';
                echo "</TD>";
              echo "</TR>";
            }
            if ($im_dashboard_show_browser > 0)
            {
              while( list ($tversion, $nb) = mysqli_fetch_row ($result) )
              {
                echo "<TR>";
                echo "<TD align='left' class='row2'>";
                  display_browser_picture($tversion);
                  echo "&nbsp;<font face='verdana' size='2'>";
                  echo f_reduce_browser_name($tversion);
                  echo "&nbsp;";
                echo "</font></TD>";
                echo "<TD class='row1' align='center' width='50'>";
                  echo "&nbsp;<font face='verdana' size='2'>";
                  echo $nb;
                echo "</font></TD>";
                echo "<TD class='row2' align='center' width='60'>";
                  echo "&nbsp;<font face='verdana' size='2'>";
                  echo round($nb / $nb_users * 100, 1);
                  echo " %";
                echo "</font></TD>";
                echo "</TR>";
                echo "\n";
              }
            }
            echo "</TABLE>";
          }
          //
          //
          $requete  = " SELECT distinct(USR_EMAIL_CLIENT), count(*) as NB";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " WHERE USR_EMAIL_CLIENT <> '' ";
          $requete .= " GROUP by USR_EMAIL_CLIENT ";
          $requete .= " ORDER by NB desc ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1xb]", $requete);
          if ( mysqli_num_rows($result) > 1 )
          {
            echo "<BR/>";
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
            echo "<TR>";
              echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345
              if ($im_dashboard_show_email_graph > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_email_graph&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_email_graph&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              echo "<font face='verdana' size='2'><b>&nbsp;" . $l_admin_users_col_emailclient . "&nbsp;"; // </b></font></TH>";
              if ($im_dashboard_show_email > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_email&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_email&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
            echo "</TH>";
            echo "</TR>";
            if ($im_dashboard_show_email_graph > 0)
            {
              echo "<TR>";
                echo "<TD align='center' class='row1' COLSPAN='3'>";
                echo '<div id="graph_email"></div>';
                echo "</TD>";
              echo "</TR>";
            }
            if ($im_dashboard_show_email > 0)
            {
              while( list ($tversion, $nb) = mysqli_fetch_row ($result) )
              {
                echo "<TR>";
                echo "<TD align='left' class='row2'>";
                  echo "&nbsp;<font face='verdana' size='2'>";
                  echo f_reduce_emailclient_name($tversion);
                  echo "&nbsp;";
                echo "</font></TD>";
                echo "<TD class='row1' align='center' width='50'>";
                  echo "&nbsp;<font face='verdana' size='2'>";
                  echo $nb;
                echo "</font></TD>";
                echo "<TD class='row2' align='center' width='60'>";
                  echo "&nbsp;<font face='verdana' size='2'>";
                  echo round($nb / $nb_users * 100, 1);
                  echo " %";
                echo "</font></TD>";
                echo "</TR>";
                echo "\n";
              }
            }
            echo "</TABLE>";
          }
        }
        
        //
        if ($demo_folder == "")
        {
          $requete  = " SELECT distinct(USR_VERSION), count(*) as NB";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " WHERE USR_VERSION <> '' and USR_VERSION <> '8.00Bd' ";
          $requete .= " GROUP by USR_VERSION ";
          $requete .= " ORDER by NB desc, USR_VERSION ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1q]", $requete);
          if ( mysqli_num_rows($result) > 1 )
          {
            echo "<BR/>";
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
            echo "<TR>";
              echo "<TH align=center COLSPAN='3' class='thHead'>";
              echo "<font face='verdana' size='2'><b>&nbsp;" . $l_admin_users_col_version . "&nbsp;</b></font></TH>";
            echo "</TR>";
            while( list ($tversion, $nb) = mysqli_fetch_row ($result) )
            {
              echo "<TR>";
              echo "<TD align='left' class='row2'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                color_num_version($tversion);
                echo "&nbsp;";
              echo "</font></TD>";
              echo "<TD class='row1' align='center' width='50'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo $nb;
              echo "</font></TD>";
              echo "<TD class='row2' align='center' width='60'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo round($nb / $nb_users * 100, 1);
                echo " %";
              echo "</font></TD>";
              echo "</TR>";
              echo "\n";
            }
            echo "</TABLE>";
          }
        }
        //







    echo "</TD>";
    echo "<TD  VALIGN='TOP' ALIGN='CENTER'>"; /////////////////-------------------- changement de colonne -----------------/////////////////////







      //
      // Vérifie la version sur internet
      //
      if ( ( ($nb_users > 4) or ($nb_row_stats > 30) ) and ($demo_folder == "") )
      {
        require ("../common/check_version.inc.php");
        //
        echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
        echo "<TR>";
          echo "<TH align=center COLSPAN='2' class='thHead'>";
          //echo "<font face='verdana' size='2'>&nbsp; " . $l_admin_users_col_version . "</font><font face='verdana' size='1'> (" . $l_server . ")</font></TH>";
          echo "<font face='verdana' size='2'>&nbsp; " . $l_index_checking_version . "</font><font face='verdana' size='1'> (" . $l_server . ")</font></TH>";
        echo "</TR>";
        echo "<TR>";
          if ( (_CHECK_VERSION_INTERNET != "") or ($checkversion != "") or ($last_check_version_today != "") )
          {
            echo "<TD class='row2'>"; // width='300'
            if ($last_check_version_today != "")
              $webVersion = $last_check_version_today;
            else
            {
              // Pour le cas ou ça ne marche pas (par défaut) :
              if ($last_check_version_today == "") 
              {
                write_file($file_last_check_version, date("d/m/Y") . "#KO!#");
              }
              //
              echo "<font size='1' color='gray'>";
              $srv_version = _SERVER_VERSION;
              //$srv_version = "2.0.2.218";  <----------------------------***************** <<< TESTS
              $webVersion = file_get_contents("http://www.intramessenger.net/version-server.php?version=" . _SERVER_VERSION . "&lang=" . _LANG . "&auth=" . base64_encode(_EXTERNAL_AUTHENTICATION) . "&s=" . base64_encode($_SERVER['SERVER_NAME']) . "&");
              echo "</font>";
            }
            echo "<font face='verdana' size='2'>";
            if ($webVersion !== false) 
            {
              if (substr($webVersion, 0, 3) == "NEW") 
              {
                echo "&nbsp<div class='notice'><a href='http://www.intramessenger.net/download.php'>" . $l_index_new_server_version_available . "</a></div>";
                if ($last_check_version_today == "") 
                {
                  write_file($file_last_check_version, date("d/m/Y") . "#NEW#");
                }
              }
              if (strtoupper(substr($webVersion, 0, 2)) == "OK") 
              {
                echo "&nbsp<FONT color='green'>" . $l_index_server_up_to_date; // Pas de version officelle plus récente...
                if ($last_check_version_today == "") 
                {
                  write_file($file_last_check_version, date("d/m/Y") . "#OK.#");
                }
              }
            }
            else
            {
              echo "<font face='verdana' size='2' color='red'>" . $l_index_cannot_check_version;
              if ( (_CHECK_VERSION_INTERNET != "") and ($checkversion == "") )  echo "<BR/>" . $l_admin_bt_invalidate . " " . $l_admin_options_col_option . " : CHECK_VERSION_INTERNET";
            }
          }
          else
          {
            echo "<TD class='row2' ALIGN='CENTER'>"; // width='300'
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "<BR/>";
            echo " <A HREF='list_options_updating.php?lang=" . $lang . "&onglet=7&' TITLE='Option : CHECK_VERSION_INTERNET'>" . $l_admin_stats_option_not . "</A><BR/>";
            echo "<BR/>";
            //echo " <A HREF='index.php?lang=" . $lang . "&checkversion=x&' TITLE='" . $l_admin_options_check_version_internet . "'>" . $l_admin_options_check_now . "</A><BR/>";
            echo "<FORM METHOD='GET' ACTION ='index.php?'>";
            echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_options_check_now . "' class='liteoption' />";
            //echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_drop.png' VALUE = '" . $l_admin_bt_delete . "' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' />";
            echo "<INPUT TYPE='hidden' name='checkversion' value = 'x' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            echo "</FORM>";

          }
          echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "<BR/>";
      }
      //
      //
      //
      //
      
        if ( (intval($nb_row_stats) > 1) and ($total_nb_messages > 2) and ($total_nb_users > 2) and ($total_nb_create > 2) )
        {
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead'>";
            echo "<font face='verdana' size='2'><b>&nbsp; " . $l_menu_statistics . " &nbsp;</b></font></TH>";
          echo "</TR>";
          echo "<TR>";
            echo "<TD class='row2' width='280'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              echo $l_index_messages_per_day;
            echo "</TD>";
            echo "<TD class='row1' width='30' align='center'>";
              aff_img_evolution($nb_messages_evol, $nb_messages_7_evol);
            echo "</TD>";
            echo "<TD class='row1' width='60' align='center'>";
              echo "<font face='verdana' size='2'>";
              echo round($total_nb_messages / $nb_row_stats, 1);
            echo "</TD>";
          echo "</TR>";
          echo "<TR>";
            echo "<TD class='row2'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              echo $l_index_users_per_day;
            echo "</TD>";
            echo "<TD class='row1' align='center'>";
              aff_img_evolution($nb_users_evol, $nb_users_7_evol);
            echo "</TD>";
            echo "<TD class='row1'align='center'>";
              echo "<font face='verdana' size='2'>";
              echo round($total_nb_users / $nb_row_stats, 1);
            echo "</TD>";
          echo "</TR>";
          echo "<TR>";
            echo "<TD class='row2'>";
              echo "<font face='verdana' size='2'>&nbsp;";
              echo $l_index_created_users_per_day;
            echo "</TD>";
            echo "<TD class='row1' align='center'>";
              aff_img_evolution($nb_create_evol, $nb_create_7_evol);
            echo "</TD>";
            echo "<TD class='row1' align='center'>";
              echo "<font face='verdana' size='2'>";
              echo round($total_nb_create / $nb_row_stats, 1);
            echo "</TD>";
          echo "</TR>";
          //
          if (intval($nb_connect) > 0)
          {
            echo "<TR>";
              echo "<TD class='row2' COLSPAN='2'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_admin_users_nb_connect;
              echo "&nbsp;</TD>";
              echo "<TD class='row1' align='center' VALIGN='TOP'>";
                echo "<font face='verdana' size='2'>";
                  echo round($nb_connect, 1);
                  /*
                  if ( (_ENTERPRISE_SERVER == "") and ($nb_connect > 4) and ($nbmax_days > 10) )
                  {
                    $moy = round($nb_connect / $nbmax_days * 100, 1);
                    echo "(" . $moy . "%)&nbsp;";
                    if ($moy > 0) display_image_percent($moy, $l_admin_users_participation);
                  }
                  */
              echo "</TD>";
            echo "</TR>";
          }
          //

          
          echo "</TABLE>";
          echo "\n";
          echo "<BR/>";
        }



        if ( ($max_nb_msg > 1) or ($max_nb_session > 1) or ($max_nb_user > 1) or ($max_nb_creat > 1) )
        {
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead'>";
            echo "<font face='verdana' size='2'><b>&nbsp; " . $l_index_records . " &nbsp;</b></font></TH>";
          echo "</TR>";
          if ($max_nb_msg > 1)
          {
            echo "<TR>";
              echo "<TD class='row2' width='200'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_admin_stats_col_nb_msg;
              echo "</TD>";
              echo "<TD class='row1' width='50' align='center'>";
                echo "<font face='verdana' size='2'>";
                echo $max_nb_msg;
              echo "</TD>";
              echo "<TD class='row2' align='center'>";
                echo "<font face='verdana' size='2'>";
                $max_nb_msg_dat = date($l_date_format_display, strtotime($max_nb_msg_dat));
                echo "&nbsp;" .$max_nb_msg_dat . "&nbsp;";
              echo "</TD>";
            echo "</TR>";
          }
          
          if ($max_nb_session > 1)
          {
            echo "<TR>";
              echo "<TD class='row2' width='200'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_admin_stats_col_nb_session;
              echo "</TD>";
              echo "<TD class='row1' width='50' align='center'>";
                echo "<font face='verdana' size='2'>";
                echo $max_nb_session;
              echo "</TD>";
              echo "<TD class='row2' align='center'>";
                echo "<font face='verdana' size='2'>";
                $max_nb_session_dat = date($l_date_format_display, strtotime($max_nb_session_dat));
                echo "&nbsp;" .$max_nb_session_dat . "&nbsp;";
              echo "</TD>";
            echo "</TR>";
          }

          if ($max_nb_user > 1)
          {
            echo "<TR>";
              echo "<TD class='row2' width='200'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_admin_stats_col_nb_users;
              echo "</TD>";
              echo "<TD class='row1' width='50' align='center'>";
                echo "<font face='verdana' size='2'>";
                echo $max_nb_user;
              echo "</TD>";
              echo "<TD class='row2' align='center'>";
                echo "<font face='verdana' size='2'>";
                $max_nb_user_dat = date($l_date_format_display, strtotime($max_nb_user_dat));
                echo "&nbsp;" .$max_nb_user_dat . "&nbsp;";
              echo "</TD>";
            echo "</TR>";
          }

          if ($max_nb_creat > 1)
          {
            echo "<TR>";
              echo "<TD class='row2' width='200'>";
                echo "<font face='verdana' size='2'>&nbsp;";
                echo $l_admin_stats_col_nb_creat;
              echo "</TD>";
              echo "<TD class='row1' width='50' align='center'>";
                echo "<font face='verdana' size='2'>";
                echo $max_nb_creat;
              echo "</TD>";
              echo "<TD class='row2' align='center'>";
                echo "<font face='verdana' size='2'>";
                $max_nb_creat_dat = date($l_date_format_display, strtotime($max_nb_creat_dat));
                echo "&nbsp;" .$max_nb_creat_dat . "&nbsp;";
              echo "</TD>";
            echo "</TR>";
          }
          
          if (_SHOUTBOX != "")
          {
            if ($max_sbx_nb_user > 1)
            {
              echo "<TR>";
                echo "<TD class='row2' width='200'>";
                  echo "<font face='verdana' size='2'>&nbsp;";
                  echo $l_admin_options_shoutbox_title_short . " <SMALL>(" . strtolower($l_admin_stats_col_nb_msg) . ")</SMALL>";
                echo "</TD>";
                echo "<TD class='row1' width='50' align='center'>";
                  echo "<font face='verdana' size='2'>";
                  echo $max_sbx_nb_user;
                echo "</TD>";
                echo "<TD class='row2' align='center'>";
                  echo "<font face='verdana' size='2'>";
                  $max_sbx_nb_user_dat = date($l_date_format_display, strtotime($max_sbx_nb_user_dat));
                  echo "&nbsp;" .$max_sbx_nb_user_dat . "&nbsp;";
                echo "</TD>";
              echo "</TR>";
            }
          }
          
          if (_ENTERPRISE_SERVER == "") // and ($demo_folder == "")  )
          {
            if ($most_connect_username != "")
            {
              echo "<TR>";
                echo "<TD class='row2' COLSPAN='3'>";
                  echo "<font face='verdana' size='2'>&nbsp;";
                  echo $l_index_most_connected . " <small>(" . $nbmax_days . " ". $l_days . ") </small> ";
                  echo "<A HREF='user.php?id_user=" . $id_most_connect_user . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
                  echo $most_connect_username . "</A>";
                echo "</TD>";
              echo "</TR>";
            }
          }
          
          echo "</TABLE>";
        }
        
        

                //
        $requete  = " SELECT distinct(USR_OS), count(*) as NB";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " WHERE USR_OS <> '' ";
        $requete .= " GROUP by USR_OS ";
        $requete .= " ORDER by NB desc ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-K1p]", $requete);
        if ( mysqli_num_rows($result) > 1 )
        {
          echo "<BR/>";
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345
            if ($im_dashboard_show_os_graph > 0)
            {
              echo "<A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_os_graph&value=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_os_graph&value=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            echo "<font face='verdana' size='2'><b>&nbsp; OS "; // &nbsp;</b></font></TH>";
            if ($im_dashboard_show_os > 0)
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_os&value=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_os&value=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
          echo "</TH>";
          if ($im_dashboard_show_os_graph > 0)
          {
            echo "</TR>";
            echo "<TR>";
              echo "<TD align='center' class='row1' COLSPAN='3'>";
              echo '<div id="graph_os"></div>';
              echo "</TD>";
            echo "</TR>";
          }
          if ($im_dashboard_show_os > 0)
          {
            while( list ($win_os, $nb) = mysqli_fetch_row ($result) )
            {
              echo "<TR>";
              echo "<TD align='left' class='row2'>";
                echo "<font face='verdana' size='2'>";
                echo "&nbsp;";
                display_os_picture($win_os);
                //echo "&nbsp;";
                //echo f_os_name($win_os);
                echo "&nbsp;";
              echo "</font></TD>";
              echo "<TD class='row1' align='center' width='50'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo $nb;
              echo "</font></TD>";
              echo "<TD class='row2' align='center' width='60'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo round($nb / $nb_users * 100, 1);
                echo " %";
              echo "</font></TD>";
              echo "</TR>";
              echo "\n";
            }
          }
          echo "</TABLE>";
        }
        //



        if ($display_flag_country != "")
        {
          $requete  = " SELECT count(*) ";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " WHERE USR_LANGUAGE_CODE <> '' ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1n]", $requete);
          list ($nb_users_lng) = mysqli_fetch_row ($result);
          //
          $requete  = " SELECT distinct(USR_LANGUAGE_CODE), count(*) as NB";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " GROUP by USR_LANGUAGE_CODE ";
          $requete .= " ORDER by NB desc ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1m]", $requete);
          if ( mysqli_num_rows($result) > 1 )
          {
            echo "<BR/>";
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
            echo "<TR>";
              echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345 343
              if ($im_dashboard_show_language_graph > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_language_graph&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_language_graph&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              echo "<font face='verdana' size='2'><b>&nbsp; " . $l_language . " &nbsp;"; // </b></font></TH>";
              if ($im_dashboard_show_language > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_language&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_language&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
            echo "</TH>";
            echo "</TR>";
            if ($im_dashboard_show_language_graph > 0)
            {
              echo "<TR>";
                echo "<TD align='center' class='row1' COLSPAN='3'>";
                echo '<div id="graph_language"></div>';
                echo "</TD>";
              echo "</TR>";
            }
            if ($im_dashboard_show_language > 0)
            {
              while( list ($language_code, $nb) = mysqli_fetch_row ($result) )
              {
                if (is_readable("../images/flags/" . strtolower($language_code) . ".png")) 
                {
                  echo "<TR>";
                  echo "<TD align='left' class='row2'>";
                    echo "<font face='verdana' size='2'>";
                    $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$language_code];
                    $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
                    $country_name = ucfirst(f_language_of_country($language_code, $country_name));
                    echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($language_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
                    echo $country_name;
                    echo "&nbsp;";
                  echo "</font></TD>";
                  echo "<TD class='row1' align='center' width='50'>";
                    echo "&nbsp;<font face='verdana' size='2'>";
                    echo $nb;
                  echo "</font></TD>";
                  echo "<TD class='row2' align='center' width='60'>";
                    echo "&nbsp;<font face='verdana' size='2'>";
                    echo round($nb / $nb_users_lng * 100, 1);
                    echo " %";
                  echo "</font></TD>";
                  echo "</TR>";
                  echo "\n";
                }
              }
            }
            echo "</TABLE>";
          }
          //
          //
          //
          //
          $requete  = " SELECT count(*) ";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " WHERE USR_COUNTRY_CODE <> '' ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1v]", $requete);
          list ($nb_users_pays) = mysqli_fetch_row ($result);
          //
          $requete  = " SELECT distinct(USR_COUNTRY_CODE), count(*) as NB";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " GROUP by USR_COUNTRY_CODE ";
          $requete .= " ORDER by NB desc, USR_COUNTRY_CODE ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-K1u]", $requete);
          if ( mysqli_num_rows($result) > 1 )
          {
            echo "<BR/>";
            echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
            echo "<TR>";
              echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345 343
              if ($im_dashboard_show_country_graph > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_country_graph&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_country_graph&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              echo "<font face='verdana' size='2'><b>&nbsp; " . $l_menu_users_by_country . " &nbsp;"; //</b></font></TH>";
              if ($im_dashboard_show_country > 0)
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_country&value=0&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
              else
              {
                echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_country&value=1&'>";
                echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
              }
            echo "</TH>";
            echo "</TR>";
            if ($im_dashboard_show_country_graph > 0)
            {
              echo "<TR>";
                echo "<TD align='center' class='row1' COLSPAN='3'>";
                echo '<div id="graph_country"></div>';
                echo "</TD>";
              echo "</TR>";
            }
            if ($im_dashboard_show_country > 0)
            {
              $nb_lig = 1;
              while( list ($country_code, $nb) = mysqli_fetch_row ($result) )
              {
                if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
                {
                  if ($nb_lig > 3) break;
                  echo "<TR>";
                  echo "<TD align='left' class='row2'>";
                    echo "<font face='verdana' size='2'>";
                    $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
                    $country_name = $GEOIP_COUNTRY_NAMES[$country_id];
                    echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
                    echo $country_name;
                    echo "&nbsp;";
                  echo "</font></TD>";
                  echo "<TD class='row1' align='center' width='50'>";
                    echo "&nbsp;<font face='verdana' size='2'>";
                    echo $nb;
                  echo "</font></TD>";
                  echo "<TD class='row2' align='center' width='60'>";
                    echo "&nbsp;<font face='verdana' size='2'>";
                    echo round($nb / $nb_users_pays * 100, 1);
                    echo " %";
                  echo "</font></TD>";
                  echo "</TR>";
                  echo "\n";
                  $nb_lig++;
                }
              }
              if ($nb_lig > 3)
              {
                echo "<TR>";
                  echo "<TD align='center' COLSPAN='3' class='catBottom'>";
                  echo "<font face='verdana' size='2'>";
                  echo "<A HREF='list_country.php?lang=" . $lang . "&'>" . $l_index_full_list . "</A></font>";
                  echo "</TD>";
                echo "</TR>";
              }
            }
            echo "</TABLE>";
          }
          //
          //
          //
          //
        }
        
        




        //
        $requete  = " SELECT distinct(USR_TIME_SHIFT), count(*) as NB";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        //$requete .= " WHERE USR_TIME_SHIFT <> '' ";
        $requete .= " GROUP by USR_TIME_SHIFT ";
        $requete .= " ORDER by NB desc, USR_TIME_SHIFT ";
        //$requete .= " ORDER by USR_TIME_SHIFT, NB desc ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-K1s]", $requete);
        if ( mysqli_num_rows($result) > 1 )
        {
          echo "<BR/>";
          echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='400'>";
          echo "<TR>";
            echo "<TH align=center COLSPAN='3' class='thHead'>"; // 345 343
            if ($im_dashboard_show_timezone_graph > 0)
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_timezone_graph&value=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_delete.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_timezone_graph&value=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "chart_pie_add.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='LEFT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            echo "<font face='verdana' size='2'><b>&nbsp;" . $l_time_zone . "&nbsp;</b></font>"; //</TH>";
            if ($im_dashboard_show_timezone > 0)
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_timezone&value=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo " <A HREF='set_cookies.php?lang=" . $lang . "&action=dashboard_show_timezone&value=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
          echo "</TH>";
          echo "</TR>";
          if ($im_dashboard_show_timezone_graph > 0)
          {
            echo "<TR>";
              echo "<TD align='center' class='row1' COLSPAN='3'>";
              echo '<div id="graph_timezone"></div>';
              echo "</TD>";
            echo "</TR>";
          }
          if ($im_dashboard_show_timezone > 0)
          {
            $nb_lig = 1;
            while( list ($timeshift, $nb) = mysqli_fetch_row ($result) )
            {
              if ($nb_lig > 3) break;
              echo "<TR>";
              echo "<TD align='left' class='row2'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                if (intval($timeshift) <> 0) 
                {
                  if ($timeshift < 0) 
                    $t = "-"; 
                  else
                    $t = "+";
                  $t .= intval(abs($timeshift) / 10);
                  if ( (abs($timeshift / 10) - intval(abs($timeshift) / 10)) <> 0 )
                    $t .= ":30";
                  else
                    $t .= ":00";
                  echo $t;
                }
                echo "&nbsp;";
              echo "</font></TD>";
              echo "<TD class='row1' align='center' width='50'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo $nb;
              echo "</font></TD>";
              echo "<TD class='row2' align='center' width='60'>";
                echo "&nbsp;<font face='verdana' size='2'>";
                echo round($nb / $nb_users * 100, 1);
                echo " %";
              echo "</font></TD>";
              echo "</TR>";
              echo "\n";
              $nb_lig++;
            }
            if ($nb_lig > 3)
            {
              echo "<TR>";
                echo "<TD align='center' COLSPAN='3' class='catBottom'>";
                echo "<font face='verdana' size='2'>";
                echo "<A HREF='list_timezone.php?lang=" . $lang . "&'>" . $l_index_full_list . "</A></font>";
                echo "</TD>";
              echo "</TR>";
            }
          }
          echo "</TABLE>";
        }
        //
        
        
        
        

      }
      else
      {
        echo "</TABLE>";
        echo "<br/>";
        echo "<div class='info'>";
        echo $l_index_dashboard_empty;
        echo "</div>";
      }
      //
      //
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE>";

  //
  //
  //
/*
  $arrTableInit = array('#CNT_CONTACT#','#MSG_MESSAGE#','#SES_SESSION#','#USR_USER#', '#USG_USERGRP#', '#GRP_GROUP#', 
                        '#STA_STATS#', '#CNF_CONFERENCE#', '#USC_USERCONF#', '#BAN_BANNED#', '#SRV_SERVERSTATE#', 
                        '#SBX_SHOUTBOX#', '#SBS_SHOUTSTATS#', '#SBV_SHOUTVOTE#', 
                        '#BMC_BOOKMCATEG#', '#BMK_BOOKMARK#', '#BMV_BOOKMVOTE#',
                        '#ROL_ROLE#', '#MDL_MODULE#', '#RLM_ROLEMODULE#',
                        '#FMD_FILEMEDIA#', '#FPJ_FILEPROJET#', '#FIL_FILE#', '#FLV_FILEVOTE#', '#FST_FILESTATS#');
  //
  foreach($arrTableInit as $table) 
  {
    $table_aff = str_replace("#", "", $table); // enlever les #
    //
    if (f_mysql_table_exists($PREFIX_IM_TABLE . $table_aff, $database) > 0)
    {
      $requete = "ANALYZE TABLE " . $PREFIX_IM_TABLE . $table_aff;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) echo '<span class="error">cannot analyse table ' . $table_aff . '</span><BR/>';
      //
      $requete = "OPTIMIZE TABLE " . $PREFIX_IM_TABLE . $table_aff;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) echo '<span class="error">cannot optimize table ' . $table_aff . '</span><BR/>';
      //
      $requete = "CHECK TABLE " . $PREFIX_IM_TABLE . $table_aff;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) echo '<span class="error">cannot check table ' . $table_aff . '</span><BR/>';
    }
    else
    {
      echo "<div class='warning'><strong>Table <I>" . $PREFIX_IM_TABLE . $table_aff . "</I> MISSING !</strong></div>";
      echo "<H1><span class='error'>Go to <A HREF='check.php?lang=" . $lang . "&'>check config</A> <BLINK>NOW !</BLINK></span></H1>";
    }
  }
*/
  //
  //
  mysqli_close($id_connect);
}

echo "</CENTER>";
//
display_menu_footer();
//
echo "</body></html>";
?>
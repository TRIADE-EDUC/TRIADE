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
if (isset($_GET['tri']))  $tri  = $_GET['tri'];   else  $tri = "";
if (isset($_GET['page'])) $page = $_GET['page'];  else  $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang'];  else  $lang = "";
if (isset($_GET['lang'])) $lang = $_GET['lang'];  else  $lang = "";
if (isset($_GET['action'])) $action = $_GET['action'];  else  $action = "";
//
if ($action == "list_users")
{
  if (isset($_GET['option_show_col_os'])) $option_show_col_os = $_GET['option_show_col_os'];  else  $option_show_col_os = "0";
  if (isset($_GET['option_show_col_time'])) $option_show_col_time = $_GET['option_show_col_time'];  else  $option_show_col_time = "0";
  if (isset($_GET['option_show_col_last'])) $option_show_col_last = $_GET['option_show_col_last'];  else  $option_show_col_last = "0";
  if (isset($_GET['option_show_col_role'])) $option_show_col_role = $_GET['option_show_col_role'];  else  $option_show_col_role = "0";
  if (isset($_GET['option_show_col_level'])) $option_show_col_level = $_GET['option_show_col_level'];  else  $option_show_col_level = "0";
  if (isset($_GET['option_show_col_email'])) $option_show_col_email = $_GET['option_show_col_email'];  else  $option_show_col_email = "0";
  if (isset($_GET['option_show_col_create'])) $option_show_col_create = $_GET['option_show_col_create'];  else  $option_show_col_create = "0";
  if (isset($_GET['option_show_col_action'])) $option_show_col_action = $_GET['option_show_col_action'];  else  $option_show_col_action = "0";
  if (isset($_GET['option_show_col_backup'])) $option_show_col_backup = $_GET['option_show_col_backup'];  else  $option_show_col_backup = "0";
  if (isset($_GET['option_show_col_rating'])) $option_show_col_rating = $_GET['option_show_col_rating'];  else  $option_show_col_rating = "0";
  if (isset($_GET['option_show_col_version'])) $option_show_col_version = $_GET['option_show_col_version'];  else  $option_show_col_version = "0";
  if (isset($_GET['option_show_col_language'])) $option_show_col_language = $_GET['option_show_col_language'];  else  $option_show_col_language = "0";
  if (isset($_GET['option_show_col_password'])) $option_show_col_password = $_GET['option_show_col_password'];  else  $option_show_col_password = "0";
  if (isset($_GET['option_show_col_activity'])) $option_show_col_activity = $_GET['option_show_col_activity'];  else  $option_show_col_activity = "0";
  if (isset($_GET['option_show_col_ip_address'])) $option_show_col_ip_address = $_GET['option_show_col_ip_address'];  else  $option_show_col_ip_address = "0";
  if (isset($_GET['option_show_col_name_function'])) $option_show_col_name_function = $_GET['option_show_col_name_function'];  else  $option_show_col_name_function = "0";
  //  
  setcookie("im_user_list_col_os", $option_show_col_os, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_time", $option_show_col_time, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_last", $option_show_col_last, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_role", $option_show_col_role, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_level", $option_show_col_level, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_email", $option_show_col_email, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_create", $option_show_col_create, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_action", $option_show_col_action, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_rating", $option_show_col_rating, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_backup", $option_show_col_backup, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_version", $option_show_col_version, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_language", $option_show_col_language, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_password", $option_show_col_password, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_activity", $option_show_col_activity, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_ip_address", $option_show_col_ip_address, mktime(0,0,0,12,31,2037));
  setcookie("im_user_list_col_name_function", $option_show_col_name_function, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_users_pc")
{
  if (isset($_GET['option_show_col_os'])) $option_show_col_os = $_GET['option_show_col_os'];  else  $option_show_col_os = "0";
  if (isset($_GET['option_show_col_ooo'])) $option_show_col_ooo = $_GET['option_show_col_ooo'];  else  $option_show_col_ooo = "0";
  if (isset($_GET['option_show_col_last'])) $option_show_col_last = $_GET['option_show_col_last'];  else  $option_show_col_last = "0";
  if (isset($_GET['option_show_col_browser'])) $option_show_col_browser = $_GET['option_show_col_browser'];  else  $option_show_col_browser = "0";
  if (isset($_GET['option_show_col_mac_adr'])) $option_show_col_mac_adr = $_GET['option_show_col_mac_adr'];  else  $option_show_col_mac_adr = "0";
  if (isset($_GET['option_show_col_version'])) $option_show_col_version = $_GET['option_show_col_version'];  else  $option_show_col_version = "0";
  if (isset($_GET['option_show_col_username'])) $option_show_col_username = $_GET['option_show_col_username'];  else  $option_show_col_username = "0";
  if (isset($_GET['option_show_col_ip_address'])) $option_show_col_ip_address = $_GET['option_show_col_ip_address'];  else  $option_show_col_ip_address = "0";
  if (isset($_GET['option_show_col_screen_size'])) $option_show_col_screen_size = $_GET['option_show_col_screen_size'];  else  $option_show_col_screen_size = "0";
  if (isset($_GET['option_show_col_emailclient'])) $option_show_col_emailclient = $_GET['option_show_col_emailclient'];  else  $option_show_col_emailclient = "0";
  if (isset($_GET['option_show_col_computername'])) $option_show_col_computername = $_GET['option_show_col_computername'];  else  $option_show_col_computername = "0";
  if (isset($_GET['option_show_col_name_function'])) $option_show_col_name_function = $_GET['option_show_col_name_function'];  else  $option_show_col_name_function = "0";
  //
  setcookie("im_user_pc_list_col_os", $option_show_col_os, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_ooo", $option_show_col_ooo, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_last", $option_show_col_last, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_browser", $option_show_col_browser, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_mac_adr", $option_show_col_mac_adr, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_version", $option_show_col_version, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_username", $option_show_col_username, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_ip_address", $option_show_col_ip_address, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_screen_size", $option_show_col_screen_size, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_emailclient", $option_show_col_emailclient, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_computername", $option_show_col_computername, mktime(0,0,0,12,31,2037));
  setcookie("im_user_pc_list_col_name_function", $option_show_col_name_function, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users_pc.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_sessions")
{
  if (isset($_GET['option_show_col_name_function'])) $option_show_col_name_function = $_GET['option_show_col_name_function'];  else  $option_show_col_name_function = "0";
  if (isset($_GET['option_show_col_ip_address'])) $option_show_col_ip_address = $_GET['option_show_col_ip_address'];  else  $option_show_col_ip_address = "0";
  if (isset($_GET['option_show_col_last_time'])) $option_show_col_last_time = $_GET['option_show_col_last_time'];  else  $option_show_col_last_time = "0";
  if (isset($_GET['option_show_col_language'])) $option_show_col_language = $_GET['option_show_col_language'];  else  $option_show_col_language = "0";
  if (isset($_GET['option_show_col_password'])) $option_show_col_password = $_GET['option_show_col_password'];  else  $option_show_col_password = "0";
  if (isset($_GET['option_show_col_activity'])) $option_show_col_activity = $_GET['option_show_col_activity'];  else  $option_show_col_activity = "0";
  if (isset($_GET['option_show_col_version'])) $option_show_col_version = $_GET['option_show_col_version'];  else  $option_show_col_version = "0";
  if (isset($_GET['option_show_col_create'])) $option_show_col_create = $_GET['option_show_col_create'];  else  $option_show_col_create = "0";
  if (isset($_GET['option_show_col_reason'])) $option_show_col_reason = $_GET['option_show_col_reason'];  else  $option_show_col_reason = "0";
  if (isset($_GET['option_show_col_rating'])) $option_show_col_rating = $_GET['option_show_col_rating'];  else  $option_show_col_rating = "0";
  if (isset($_GET['option_show_col_begin'])) $option_show_col_begin = $_GET['option_show_col_begin'];  else  $option_show_col_begin = "0";
  if (isset($_GET['option_show_col_level'])) $option_show_col_level = $_GET['option_show_col_level'];  else  $option_show_col_level = "0";
  if (isset($_GET['option_show_col_role'])) $option_show_col_role = $_GET['option_show_col_role'];  else  $option_show_col_role = "0";
  if (isset($_GET['option_show_col_time'])) $option_show_col_time = $_GET['option_show_col_time'];  else  $option_show_col_time = "0";
  if (isset($_GET['option_show_col_os'])) $option_show_col_os = $_GET['option_show_col_os'];  else  $option_show_col_os = "0";
  //  
  setcookie("im_session_list_col_name_function", $option_show_col_name_function, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_ip_address", $option_show_col_ip_address, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_last_time", $option_show_col_last_time, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_language", $option_show_col_language, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_password", $option_show_col_password, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_activity", $option_show_col_activity, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_version", $option_show_col_version, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_create", $option_show_col_create, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_reason", $option_show_col_reason, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_rating", $option_show_col_rating, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_begin", $option_show_col_begin, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_level", $option_show_col_level, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_role", $option_show_col_role, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_time", $option_show_col_time, mktime(0,0,0,12,31,2037));
  setcookie("im_session_list_col_os", $option_show_col_os, mktime(0,0,0,12,31,2037));
  //
  header("location:list_sessions.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_sharing")
{
  if (isset($_GET['option_show_col_project'])) $option_show_col_project = $_GET['option_show_col_project'];  else  $option_show_col_project = "0";
  if (isset($_GET['option_show_col_comment'])) $option_show_col_comment = $_GET['option_show_col_comment'];  else  $option_show_col_comment = "0";
  if (isset($_GET['option_show_col_creat'])) $option_show_col_creat = $_GET['option_show_col_creat'];  else  $option_show_col_creat = "0";
  if (isset($_GET['option_show_col_media'])) $option_show_col_media = $_GET['option_show_col_media'];  else  $option_show_col_media = "0";
  if (isset($_GET['option_show_col_hash'])) $option_show_col_hash = $_GET['option_show_col_hash'];  else  $option_show_col_hash = "0";
  if (isset($_GET['option_show_col_auth'])) $option_show_col_auth = $_GET['option_show_col_auth'];  else  $option_show_col_auth = "0";
  if (isset($_GET['option_show_col_size'])) $option_show_col_size = $_GET['option_show_col_size'];  else  $option_show_col_size = "0";
  //  
  setcookie("im_file_list_col_project", $option_show_col_project, mktime(0,0,0,12,31,2037));
  setcookie("im_file_list_col_comment", $option_show_col_comment, mktime(0,0,0,12,31,2037));
  setcookie("im_file_list_col_creat", $option_show_col_creat, mktime(0,0,0,12,31,2037));
  setcookie("im_file_list_col_media", $option_show_col_media, mktime(0,0,0,12,31,2037));
  setcookie("im_file_list_col_hash", $option_show_col_hash, mktime(0,0,0,12,31,2037));
  setcookie("im_file_list_col_auth", $option_show_col_auth, mktime(0,0,0,12,31,2037));
  setcookie("im_file_list_col_size", $option_show_col_size, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_sharing.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}

//
//
// Afficher le nom des options (ou le libellé/traduction).
if ($action == "list_options_updating")
{
  if (isset($_GET['option_show_option_name'])) $option_show_option_name = $_GET['option_show_option_name'];  else  $option_show_option_name = "";
  setcookie("im_option_list_option_name", $option_show_option_name, mktime(0,0,0,12,31,2037));
  //
  header("location:list_options_updating.php?lang=" . $lang . "&");
  die();
}
if ($action == "role_permissions")
{
  if (isset($_GET['option_show_option_name'])) $option_show_option_name = $_GET['option_show_option_name'];  else  $option_show_option_name = "";
  setcookie("im_option_list_option_name", $option_show_option_name, mktime(0,0,0,12,31,2037));
  //
  if (isset($_GET['id_role'])) $id_role_select = intval($_GET['id_role']); else $id_role_select = 0;
  header("location:role_permissions.php?lang=" . $lang . "&id_role=" . $id_role_select . "&");
  die();
}
if ($action == "role_permissions_list")
{
  if (isset($_GET['option_show_option_name'])) $option_show_option_name = $_GET['option_show_option_name'];  else  $option_show_option_name = "";
  setcookie("im_option_list_option_name", $option_show_option_name, mktime(0,0,0,12,31,2037));
  //
  header("location:role_permissions_list.php?lang=" . $lang . "&id_role=" . $id_role_select . "&");
  die();
}


//
// Nombre de lignes par pages
//
if ($action == "list_users_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_users_pc_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users_pc.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_sessions_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_sessions.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_contact_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_contact.php?lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_sharing_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_sharing.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_sharing_trash_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_sharing_trash.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_sharing_pending_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_sharing_pending.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_sharing_alert_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_sharing_alert.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_exchanging_pending_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_exchanging_pending.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_exchanging_trash_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_exchanging_trash.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_projects_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_projects.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_backup_nb_rows")
{
  if (isset($_GET['nb_row_by_page'])) $nb_row_by_page = $_GET['nb_row_by_page'];  else  $nb_row_by_page = "15";
  //  
  setcookie("im_nb_row_by_page", $nb_row_by_page, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_backup.php.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//

//
// Activer/masquer la sélection des colonnes à afficher
//
if ($action == "list_users_show_select_cols")
{
  if (isset($_GET['im_user_list_show_select_cols'])) $im_user_list_show_select_cols = $_GET['im_user_list_show_select_cols'];  else  $im_user_list_show_select_cols = "1";
  //  
  setcookie("im_user_list_show_select_cols", $im_user_list_show_select_cols, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_users_pc_show_select_cols")
{
  if (isset($_GET['im_user_pc_list_show_select_cols'])) $im_user_pc_list_show_select_cols = $_GET['im_user_pc_list_show_select_cols'];  else  $im_user_pc_list_show_select_cols = "1";
  //  
  setcookie("im_user_pc_list_show_select_cols", $im_user_pc_list_show_select_cols, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users_pc.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_session_show_select_cols")
{
  if (isset($_GET['im_session_list_show_select_cols'])) $im_session_list_show_select_cols = $_GET['im_session_list_show_select_cols'];  else  $im_session_list_show_select_cols = "1";
  //  
  setcookie("im_session_list_show_select_cols", $im_session_list_show_select_cols, mktime(0,0,0,12,31,2037));
  //
  header("location:list_sessions.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_files_sharing_show_select_cols")
{
  if (isset($_GET['im_file_list_show_select_cols'])) $im_file_list_show_select_cols = $_GET['im_file_list_show_select_cols'];  else  $im_file_list_show_select_cols = "1";
  //  
  setcookie("im_file_list_show_select_cols", $im_file_list_show_select_cols, mktime(0,0,0,12,31,2037));
  //
  header("location:list_files_sharing.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
// Activer/masquer la légende
//
if ($action == "list_users_show_legende")
{
  if (isset($_GET['im_user_list_show_legende'])) $im_user_list_show_legende = $_GET['im_user_list_show_legende'];  else  $im_user_list_show_legende = "1";
  //  
  setcookie("im_user_list_show_legende", $im_user_list_show_legende, mktime(0,0,0,12,31,2037));
  //
  header("location:list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "list_session_show_legende")
{
  if (isset($_GET['im_session_list_show_legende'])) $im_session_list_show_legende = $_GET['im_session_list_show_legende'];  else  $im_session_list_show_legende = "1";
  //  
  setcookie("im_session_list_show_legende", $im_session_list_show_legende, mktime(0,0,0,12,31,2037));
  //
  header("location:list_sessions.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&");
  die();
}
//
if ($action == "messagerie_show_order")
{
  if (isset($_GET['im_messagerie_show_order'])) $im_messagerie_show_order = $_GET['im_messagerie_show_order'];  else  $im_messagerie_show_order = "1";
  //  
  setcookie("im_messagerie_show_order", $im_messagerie_show_order, mktime(0,0,0,12,31,2037));
  //
  header("location:messagerie.php?lang=" . $lang . "&");
  die();
}
//
// Filtrer ou non le format des images (affichage avatars)
if ($action == "avatar_changing")
{
  if (isset($_GET['im_no_avatar_images_filter'])) $im_no_avatar_images_filter = $_GET['im_no_avatar_images_filter'];  else  $im_no_avatar_images_filter = "0";
  //  
  setcookie("im_no_avatar_images_filter", $im_no_avatar_images_filter, mktime(0,0,0,12,31,2037));
  //
  header("location:avatar_changing.php?lang=" . $lang . "&");
  die();
}
//
// Dashboard ; afficher certains éléments
//
if (isset($_GET['value'])) $value = $_GET['value'];  else  $value = "1";
$dashboard = false;
if ($action == "dashboard_show_os")
{
  setcookie("im_dashboard_show_os", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_os_graph")
{
  setcookie("im_dashboard_show_os_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($action == "dashboard_show_gender")
{
  setcookie("im_dashboard_show_gender", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_gender_graph")
{
  setcookie("im_dashboard_show_gender_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($action == "dashboard_show_browser")
{
  setcookie("im_dashboard_show_browser", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_browser_graph")
{
  setcookie("im_dashboard_show_browser_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($action == "dashboard_show_email")
{
  setcookie("im_dashboard_show_email", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_email_graph")
{
  setcookie("im_dashboard_show_email_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($action == "dashboard_show_language")
{
  setcookie("im_dashboard_show_language", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_language_graph")
{
  setcookie("im_dashboard_show_language_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($action == "dashboard_show_country")
{
  setcookie("im_dashboard_show_country", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_country_graph")
{
  setcookie("im_dashboard_show_country_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($action == "dashboard_show_timezone")
{
  setcookie("im_dashboard_show_timezone", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
if ($action == "dashboard_show_timezone_graph")
{
  setcookie("im_dashboard_show_timezone_graph", $value, mktime(0,0,0,12,31,2037));
  $dashboard = true;
}
//
if ($dashboard == true)
{
  header("location:index.php?lang=" . $lang . "&");
  die();
}
//
// Basculer menu à droite ou en haut
//
/*
if ($action == "top_menu")
{
  $tri = trim($tri);  
  $top_menu = "";
  if ($tri != "") $top_menu = "X";
  //  
  setcookie("im_top_menu", $top_menu, mktime(0,0,0,12,31,2037));
}
//
// Activer menus en entier, ou juste ce qui est nécessaire.
//
if ($action == "full_menu")
{
  $full_menu = '';
  if (isset($_COOKIE['im_full_menu'])) $full_menu = $_COOKIE['im_full_menu'];
  //
  if ($full_menu != "") 
    $full_menu = "";
  else
    $full_menu = "X";
  //  
  setcookie("im_full_menu", $full_menu, mktime(0,0,0,12,31,2037));
}
*/
/*
// Malgrès le basculement de menu gauche/haut, on affiche la même page
if ( (substr_count($page, ".php") == 1) and (substr_count($page, "/") == 0) )
  header("location:" . $page . "?lang=" . $lang . "&");
else
  header("location:index.php?lang=" . $lang . "&");
*/
header("location:display_updating.php?lang=" . $lang . "&");
//
die();
?>
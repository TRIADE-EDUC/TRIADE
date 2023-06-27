<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2013 THeUDS           **
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
function hack_attempt()
{
  $ip = $_SERVER['REMOTE_ADDR'];	
  $qry = $_SERVER['QUERY_STRING'];
  if ($qry != "")
  {
    $fp = fopen("log/" . "hack_attempt.txt", "a");
    if (flock($fp, 2));
    {
      fputs($fp,date("d/m/Y;H:i:s") . ";" . $ip . ";" . $qry ."\r\n");
    }
    flock($fp, 3);
    fclose($fp);
  }
  //
  sleep(5);
  die();
}
//
if (!isset($_GET['a']))     hack_attempt();
if (strlen($_GET['a']) > 3) hack_attempt();
//
$action = intval($_GET['a']);
if (preg_match("#[^0-9]#", $action)) hack_attempt();
//
require ("../common/display_errors.inc.php"); 
//
define('INTRAMESSENGER',true);
//
if (is_readable("../common/config/config.inc.php"))  require ("../common/config/config.inc.php");
//
$incomplete_options = "";
if ( (!defined("_MAINTENANCE_MODE")) or (!defined("_ROLE_ID_DEFAULT_FOR_NEW_USER")) or (!defined("_ALLOW_HIDDEN_STATUS")) or (!defined("_FLAG_COUNTRY_FROM_IP")) )  $incomplete_options = "X"; // mode maintenance
//
if ( (_MAINTENANCE_MODE != '') or ($incomplete_options != "") )
{
  sleep(2);
  die("#KO#MAINTENANCE#"); // mode maintenance
}
//
require ("../common/functions.inc.php");
prevent_error_option_missing();
//
switch ($action)
{
  case 1 : 
      require ("include/sql_test.inc.php");
      break;
  case 2 : 
      require ("include/get_options_1.inc.php");
      break;
  case 3 : 
      require ("include/get_options_2.inc.php");
      break;
  case 4 :
      require ("include/start.inc.php");
      break;
  case 5 : 
      require ("include/get_options_1_role.inc.php");
      break;
  case 6 : 
      require ("include/get_options_3.inc.php");
      break;
  case 8 :
      require ("include/stop.inc.php");
      break;
  case 9 :
      require ("include/leave_server.inc.php");
      break;  
  //
  // List
  case 10 :
      $all = "yes";
      require ("include/list_all_users.inc.php");
      break;
  case 11 :
      $all = "not_in_contact_list";
      require ("include/list_all_users.inc.php");
      break;
  case 12 :
      require ("include/list_all_contacts.inc.php");
      break;
  case 19 :
      require ("include/list_avatars.inc.php");
      break;
  case 20 :
      require ("include/list_contact_online_offline.inc.php"); // inclu : msg_nb
      break;
  case 22 :
      require ("include/list_contact_online_only.inc.php");    // inclu : msg_nb
      break;
  case 25 :
      require ("include/list_contact_to_confirm.inc.php");
      break;
  //
  // contact_user_  : 30  40
  case 30 : 
      require ("include/contact_user_vote.inc.php");
      break;
  case 31 : 
      require ("include/contact_user_ask_add.inc.php");
      break;
  case 32 : 
      require ("include/contact_user_confirm.inc.php");
      break;
  case 33 : 
      require ("include/contact_user_delete.inc.php");
      break;
  case 34 : 
      require ("include/contact_user_reject.inc.php");
      break;
  case 35 : 
      require ("include/contact_user_delete_wait.inc.php");
      break;
  case 36 : 
      require ("include/contact_user_group.inc.php");
      break;
  case 37 : 
      require ("include/contact_user_mask.inc.php");
      break;
  case 38 : 
      require ("include/contact_user_privilege.inc.php");
      break;
  case 39 : 
      require ("include/contact_user_nickname.inc.php");
      break;
  case 40 : 
      require ("include/contact_user_search.inc.php");
      break;
  //
  // msg
  case 50 :
      require ("include/message_send.inc.php");
      break;
  case 51 :
      require ("include/message_get.inc.php");
      break;
  case 52 :
      //require ("include/message_nb.inc.php");
      break;
  case 53 :
      require ("include/message_list_contact.inc.php");
      break;
  case 59 :
      require ("include/message_send_alert.inc.php");
      break;
  // 
  // conference
  case 60 :
      require ("include/conference_invite.inc.php");
      break;
  case 61 : 
      require ("include/conference_accept.inc.php");
      break;
  case 62 : 
      require ("include/conference_list_user.inc.php");
      break;
  case 63 : 
      require ("include/conference_msg_send.inc.php");
      break;
  case 65 : 
      require ("include/conference_quit.inc.php");
      break;
  //
  // chang pass, user info, avatar
  case 70 : 
      require ("include/user_chang_passwd.inc.php");
      break;
  case 71 : 
      require ("include/user_chang_nickname.inc.php");
      break;
  case 72 :
      require ("include/user_infos_update.inc.php");
      break;
  case 73 :
      require ("include/user_infos_list.inc.php");
      break;
  case 74 :
      require ("include/user_avatar_update.inc.php");
      break;
  case 75 :
      require ("include/user_status_update.inc.php");
      break;
  case 79 :
      require ("include/user_register.inc.php");
      break;
  //
  case 87 :
      require ("include/server_list_status_update.inc.php");
      break;
  case 88 :
      require ("include/server_list_status.inc.php");
      break;
  case 89 :
      require ("include/server_list.inc.php");
      break;
  //
  // extern application
  case 90 : 
      require ("include/extern_phenix_today.inc.php");
      break;
  case 91 : 
      require ("include/extern_phenix_triade_today.inc.php");
      break;
  /*
  case 91 :   91 aussi ?
      require ("include/extern_opengoo_today.inc.php");
      break;
  */
  case 100 : 
      require ("include/im_book.inc.php"); // im_annu.php
      break;
  //
  // Shoutbox
  case 110 : 
      require ("include/shoutbox_list.inc.php");
      break;
  case 111 : 
      require ("include/shoutbox_send.inc.php");
      break;
  case 112 : 
      //require ("include/shoutbox_approval.inc.php");
      break;
  case 113 : 
      //require ("include/shoutbox_delete.inc.php");
      break;
  case 115 : 
      require ("include/shoutbox_vote.inc.php");
      break;
  case 117 : 
      require ("include/shoutbox_group_list.inc.php");
      break;
  //
  // Group :
  case 120 :
      require ("include/group_list.inc.php");
      break;
  case 121 :
      require ("include/group_ask_join.inc.php");
      break;
  case 122 :
      require ("include/group_leave.inc.php");
      break;
  //
  // Bookmarks :
  case 130 :
      require ("include/bookmark_list.inc.php");
      break;
  case 131 :
      require ("include/bookmark_send.inc.php");
      break;
  case 132 :
      require ("include/bookmark_vote.inc.php");
      break;
  case 133 :
      require ("include/bookmark_category_list.inc.php");
      break;
  //
  // Share files :
  case 140 :
      require ("include/sharefiles_request_send.inc.php");
      break;
  case 141 :
      require ("include/sharefiles_send.inc.php");
      break;
  case 142 :
      require ("include/sharefiles_delete.inc.php");
      break;
  case 143 :
      require ("include/sharefiles_download.inc.php");
      break;
  case 144 :
      require ("include/sharefiles_alert.inc.php");
      break;
  case 145 :
      require ("include/sharefiles_vote.inc.php");
      break;
  case 146 :
      require ("include/sharefiles_list.inc.php");
      break;
  case 148 :
      require ("include/sharefiles_list_media.inc.php");
      break;
  case 149 :
      require ("include/sharefiles_list_projet.inc.php");
      break;
  //
  // Files Backup :
  case 150 :
      require ("include/backupfiles_request_send.inc.php");
      break;
  case 151 :
      require ("include/backupfiles_send.inc.php");
      break;
  case 152 :
      require ("include/backupfiles_delete.inc.php");
      break;
  case 153 :
      require ("include/backupfiles_download.inc.php");
      break;
  case 156 :
      require ("include/backupfiles_list.inc.php");
      break;
  //
  case 200 :
      require ("include/dashboard.inc.php");
      break;
  //
  default:
      hack_attempt($action);
      break;
}
?>

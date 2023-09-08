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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$ip = 			    f_decode64_wd($_GET['ip']);
$version =	    intval($_GET['v']);
$status =       intval($_GET['st']);
if (isset($_GET['bi'])) $last_id_sb = intval(f_decode64_wd($_GET['bi'])); else $last_id_sb = "";
if (isset($_GET['bm'])) $last_id_bm = intval(f_decode64_wd($_GET['bm'])); else $last_id_bm = "-1";
if (isset($_GET['sf'])) $last_id_sf = intval(f_decode64_wd($_GET['sf'])); else $last_id_sf = "-1";
if (isset($_GET['ef'])) $last_id_ef = intval(f_decode64_wd($_GET['ef'])); else $last_id_ef = "-1";
if (isset($_GET['is'])) $id_session = intval(f_decode64_wd($_GET['is'])); else $id_session = "";   // IS
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $last_id_sb)) $last_id_sb = "";
if (preg_match("#[^0-9]#", $last_id_bm)) $last_id_bm = "";
if (preg_match("#[^0-9]#", $last_id_sf)) $last_id_sf = "";
if (preg_match("#[^0-9]#", $last_id_ef)) $last_id_ef = "";
//
if ( ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F12#KO#1#"); // 1:session non ouverte.      //die ("Session KO.");
  //
  //
  $nb_pm = -1;
  $id_max_shoutbox = -1;
  $id_grp_max_sbx = 0;
  $srv_list_status = "";
  $id_bookmarks_max = 0;
  $id_sharefiles_max = -1;
  $id_sharefiles_exchange_max = -1;
  //
  //
  $t_bookmarks = _BOOKMARKS;
  $t_allow_shoutbox = _SHOUTBOX;
  $t_srv_list_status = _SERVERS_STATUS;
  $t_share_files = _SHARE_FILES;
  $t_share_files_exchange = _SHARE_FILES_EXCHANGE;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_bookmarks = f_role_permission($id_role, "BOOKMARKS", _BOOKMARKS);
      $t_share_files = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
      $t_share_files_exchange = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
    }
  }
  if (_SLOW_NOTIFY != "") // si coché
  {
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
    {
      if ($id_role > 0)
      {
        $t_allow_shoutbox = f_role_permission($id_role, "SHOUTBOX", _SHOUTBOX);
        $t_srv_list_status = f_role_permission($id_role, "SERVERS_STATUS", _SERVERS_STATUS);
      }
    }
    //
    //
    // -------------------------------------------- FORUMS : COMPTER MESSAGES PRIVES (PM) -------------------------------------------- 
    if (_EXTERNAL_AUTHENTICATION != "")
    {
      require ("../common/extern/extern.inc.php");
      $nb_pm = f_nb_unread_pm_extern($id_user);
    }
    //
    // -------------------------------------------- SHOUTBOX NOUVEAU MESSAGE ? -------------------------------------------- 
    if ($t_allow_shoutbox != "")
    {
      require ("../common/shoutbox.inc.php");
      $id_max_shoutbox = f_shoutbox_last_id_if_new($last_id_sb);
      if ($id_max_shoutbox > 0) $id_grp_max_sbx = f_id_group_id_sbx($id_max_shoutbox);
    }
    //
    // -------------------------------------------- SERVERS STATUS LIST -------------------------------------------- 
    if ($t_srv_list_status != "")
    {
      $srv_list_status = f_servers_status();
    }
    //
    // -------------------------------------------- public SHARE FILES : new one ? -------------------------------------------- 
    if ($t_share_files != "")
    {
      if ($last_id_sf >= 0)
      {
        $requete  = " select max(ID_FILE)";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
        $requete .= " WHERE FIL_ONLINE = 'Y' ";
        $requete .= " and ID_USER_DEST is null and ID_GROUP_DEST is null "; // shared files to ALL users.
        $requete .= " and ID_USER_AUT <> " . $id_user;
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-25e]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($id_sharefiles_max) = mysqli_fetch_row ($result);
          //if ( ($id_sharefiles_max <= intval($last_id_sf)) and (intval($last_id_sf) > 0) )
          //  $id_sharefiles_max = 0; // rien de neuf
        }
      }
    }
  }
  //
  //
  //
  // -------------------------------------------- SHARE FILES EXCHANGE : new one ? -------------------------------------------- 
  if ( ($t_share_files != "") and ($t_share_files_exchange != "") )
  {
    if ($last_id_ef >= 0)
    {
      $requete  = " select max(ID_FILE)";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
      $requete .= " WHERE FIL_ONLINE = 'Y' ";
      $requete .= " and ID_USER_DEST = "  . $id_user; // private exchange files.
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-25f]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($id_sharefiles_exchange_max) = mysqli_fetch_row ($result);
        //if ( ($id_sharefiles_exchange_max <= intval($last_id_ef)) and (intval($last_id_ef) > 0) )
        //  $id_sharefiles_exchange_max = 0; // rien de neuf
      }
    }
  }
  //
  // -------------------------------------------- BOOKMARKS : NEW ONE ? -------------------------------------------- 
  if ($t_bookmarks != "")
  {
    if ($last_id_bm >= 0)
    {
      $requete  = " select max(ID_BOOKMARK) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
      $requete .= " WHERE BMK_DISPLAY > 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-25d]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($id_bookmarks_max) = mysqli_fetch_row ($result);
        if ( ($id_bookmarks_max <= intval($last_id_bm)) and (intval($last_id_bm) > 0) )
          $id_bookmarks_max = 0; // rien de neuf
      }
    }
  }
  //
  //
  //
  echo ">F12#" . $nb_pm . "#" . $id_max_shoutbox . "#" . $id_grp_max_sbx . "#" . f_encode64($srv_list_status) . "#" . $id_bookmarks_max . "#" . $id_sharefiles_max . "#" . $id_sharefiles_exchange_max . "###|"; // séparateur de ligne : '|' (pipe).
  //
  //
  //
  // -------------------------------------------- LISTER LES CONTACTS EN ATTENTE -------------------------------------------- 
  //
  //
  //
  $requete  = " select CNT.ID_CONTACT, USR.USR_USERNAME, USR.USR_NICKNAME, USR.USR_NAME, CNT.ID_USER_1, USR.USR_COUNTRY_CODE, CNT.CNT_NEW_USERNAME ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER USR ";
  $requete .= " WHERE USR.ID_USER = CNT.ID_USER_1 and CNT.ID_USER_2 = " . $id_user . " ";
  $requete .= " and CNT.CNT_STATUS = 0 ";
  $requete .= " and USR.USR_STATUS = 1 ";
  $requete .= " ORDER BY USR_USERNAME ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-25a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($id_contact, $contact, $nickname, $nom, $id_user1, $country, $msg) = mysqli_fetch_row ($result) )
    {
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $contact = $nickname;
      if ($nom == 'HIDDEN') $nom = '';
      //
      $nom =        f_encode64($nom);
      $contact =    f_encode64($contact);
      //
      // on renvoi les contacts du user
      echo ">F12#" . $id_contact . "#" . $contact . "#" . $nom . "#" . $id_user1 . "#" . $country . "#" . f_encode64($msg) . "#|"; // séparateur de ligne : '|' (pipe).
    }
  }
  else
  {
    // renvoie : aucun contact pour ce user
    echo ">F12#0#-#0#";
  }
  //
  //
  // update date of last use of user   // USR_DATE_LAST et USR_NB_CONNECT en même temps !!!
  $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " SET USR_DATE_LAST = CURDATE(), USR_NB_CONNECT = (USR_NB_CONNECT + 1) ";
  $requete .= " WHERE ID_USER = " . $id_user . " ";
  $requete .= " and USR_DATE_LAST <> CURDATE() ";
  $requete .= " and USR_STATUS = 1 ";
  $requete .= " LIMIT 1 "; // (to protect)
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-25b]", $requete);
  //
  //
  if ($id_session <= 0) $id_session = f_get_id_session_id_user($id_user);
  // pour éviter que ceux qui ont perdu la connexion restent offline pour les autres (alors qu'ils ont l'impression d'être online).
  //if ($status > 0)
  if ( ($status > 0) and ($id_session > 0) ) // modifié le 23/06/12
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " SET SES_STATUS = " . $status . " ";
    $requete .= " WHERE ID_SESSION = " . $id_session . " ";
    $requete .= " and ID_USER = " . $id_user ;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-25c]", $requete);
  }
  //
  clean_inactives_session();
  //
  mysqli_close($id_connect);
}
?>
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
//						IDEM list_contact_online_only.php mais les offline aussi.
//						(donc liste des contacts du user, online ou non).
//
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
if ( (!isset($_GET['iu'])) or (!isset($_GET['v'])) or (!isset($_GET['ip'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$version =    intval($_GET['v']);
$ip = 			  f_decode64_wd($_GET['ip']);
if (isset($_GET['bi'])) $last_id_m = intval(f_decode64_wd($_GET['bi'])); else $last_id_m = "";
if (isset($_GET['sf'])) $last_id_sf = intval(f_decode64_wd($_GET['sf'])); else $last_id_sf = "-1";
//if (isset($_GET['ef'])) $last_id_ef = intval(f_decode64_wd($_GET['ef'])); else $last_id_ef = "-1";
if (isset($_GET['is'])) $id_session = intval(f_decode64_wd($_GET['is'])); else $id_session = "";      // IS
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $last_id_m)) $last_id_m = "";
//
if ( ($id_user > 0) and ($version > 18) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')   die(">F16#KO#1#"); // 1:session non ouverte.      //die ("Session KO.");
  //
  //
  $nb_contact_waiting = 0;
  $nb_pm = -1;
  $id_max_shoutbox = -1;
  $id_grp_max_sbx = 0;
  $nb_msg = 0;
  $if_conf_invit = "";
  $srv_list_status = "";
  $id_sharefiles_max = 0;
  $id_sharefiles_exchange_max = 0;
  //
  //
  $t_allow_conference = _ALLOW_CONFERENCE;
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
      $t_allow_conference = f_role_permission($id_role, "ALLOW_CONFERENCE", _ALLOW_CONFERENCE);
    }
  }
  //
  //
  if (_SLOW_NOTIFY == "") // si PAS coché
  {
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
    {
      if ($id_role > 0)
      {
        $t_allow_shoutbox = f_role_permission($id_role, "SHOUTBOX", _SHOUTBOX);
        $t_srv_list_status = f_role_permission($id_role, "SERVERS_STATUS", _SERVERS_STATUS);
        $t_share_files = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
        $t_share_files_exchange = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
      }
    }
    //
    // -------------------------------------------- COMPTER LES CONTACTS EN ATTENTE -------------------------------------------- 
    $requete  = " select count(CNT.ID_CONTACT) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE USR.ID_USER = CNT.ID_USER_1 and CNT.ID_USER_2 = " . $id_user . " ";
    $requete .= " and CNT.CNT_STATUS = 0 ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " ORDER BY USR_USERNAME ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-20a]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      list ($nb_contact_waiting) = mysqli_fetch_row ($result);
    }
    //
    // -------------------------------------------- FORUMS : COMPTER MESSAGES PRIVES (PM) -------------------------------------------- 
    if (_EXTERNAL_AUTHENTICATION != "")
    {
      require ("../common/extern/extern.inc.php");
      $nb_pm = f_nb_unread_pm_extern($id_user);
    }
    //
    // -------------------------------------------- SHOUTBOX : NOUVEAU MESSAGE ? -------------------------------------------- 
    if ($t_allow_shoutbox != "")
    {
      require ("../common/shoutbox.inc.php");
      $id_max_shoutbox = f_shoutbox_last_id_if_new($last_id_m);
      if ($id_max_shoutbox > 0) $id_grp_max_sbx = f_id_group_id_sbx($id_max_shoutbox);
    }
    //
    // -------------------------------------------- SERVERS STATUS LIST -------------------------------------------- 
    if ($t_srv_list_status != "")
    {
      $srv_list_status = f_servers_status();
    }
    //
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
        if (!$result) error_sql_log("[ERR-20g]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($id_sharefiles_max) = mysqli_fetch_row ($result);
          //if ( ($id_sharefiles_max <= intval($last_id_sf)) and (intval($last_id_sf) > 0) )
          //  $id_sharefiles_max = 0; // rien de neuf
        }
      }
    }
    // -------------------------------------------- SHARE FILES EXCHANGE : new one ? -------------------------------------------- 
/*
    if ( ($t_share_files != "") and ($t_share_files_exchange != "") )
    {
      if ($last_id_ef >= 0)
      {
        $requete  = " select max(ID_FILE)";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
        $requete .= " WHERE FIL_ONLINE = 'Y' ";
        $requete .= " and ID_USER_DEST = "  . $id_user; // private exchange files.
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-20g]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($id_sharefiles_exchange_max) = mysqli_fetch_row ($result);
          if ( ($id_sharefiles_exchange_max <= intval($last_id_ef)) and (intval($last_id_ef) > 0) )
            $id_sharefiles_exchange_max = 0; // rien de neuf
        }
      }
    }
*/
  }
  //
  //
  //
  // -------------------------------------------- COMPTER MESSAGES -------------------------------------------- 
  //
  //
  //
  $requete  = " SELECT COUNT(ID_MESSAGE) ";
  $requete .= " from " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
  $requete .= " where ID_USER_DEST = " . $id_user . " ";
  $requete .= " and ID_CONFERENCE = 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-20b]", $requete);
  if ( mysqli_num_rows($result) > 0  )
  {
    list ($nb_msg) = mysqli_fetch_row ($result);
  }
  //
  if (intval($nb_msg) <= 0)
  {
    // Pas de message, mais peut être une invitation à une conférence...          (voir aussi msg_get.php !!!)
    if ($t_allow_conference != '')
    {
      $id_conf = 0;
      $requete  = " select CNF.ID_CONFERENCE, CNF.ID_USER ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE CNF, " . $PREFIX_IM_TABLE . "USC_USERCONF USC ";
      $requete .= " WHERE CNF.ID_CONFERENCE = USC.ID_CONFERENCE ";
      $requete .= " and USC.ID_USER = " . $id_user . " ";
      $requete .= " AND USC_ACTIVE = 0 "; // en attente de validation
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-20c]", $requete);
      if ( mysqli_num_rows($result) == 1 ) // normalement pas plus...
      {
        list ($id_conf, $id_u_creat) = mysqli_fetch_row ($result);
        if (intval($id_conf) > 0)
        {
          $u_creat = f_get_username_of_id($id_u_creat);
          $u_creat = f_encode64($u_creat);
          $if_conf_invit = "CONF#ADD#" . $id_conf . "#" . $u_creat . "###"; // pas de message, mais invitation à conférence par contre...
        }
      }
    }
  }
  //
  echo ">F16#" . $nb_pm . "#" . $nb_contact_waiting . "#" . $nb_msg . "#" . $if_conf_invit . "#" . $id_max_shoutbox . "#" . $id_grp_max_sbx . "#" . f_encode64($srv_list_status) . "#" . $id_sharefiles_max . "###|";
  //
  //
  //
  // -------------------------------------------- LISTER LES CONTACTS -------------------------------------------- 
  //
  //
  //
  $hide_list = "#";
  if ( (_ALLOW_HIDDEN_TO_CONTACTS != '') or (_SPECIAL_MODE_OPEN_COMMUNITY != "") ) // and (_SPECIAL_MODE_GROUP_COMMUNITY == '')
  {
    // on récupère la liste de ceux qui ne veulent pas de nous (l'état de privilège de l'auteur chez le contact destinataire) (list people dont want us).
    $requete  = " select CNT.ID_USER_1 ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT ";
    $requete .= " WHERE CNT.ID_USER_1 <> " . $id_user . " ";
    $requete .= " and CNT.ID_USER_2 = " . $id_user . " ";
    $requete .= " and (CNT.CNT_STATUS < 0 or CNT.CNT_STATUS = 5) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-20d]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      while( list ($usr_id) = mysqli_fetch_row ($result) )
      {
        $hide_list .= $usr_id . "#";
      }
    }
  }
  //
  //
  // 1 - MODE NORMAL :
  if ( (_SPECIAL_MODE_OPEN_COMMUNITY == "") and (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') )
  {
    $requete  = " select CNT.ID_CONTACT, SES.SES_STATUS, USR.ID_USER, SES.SES_AWAY_REASON, '', USR.ID_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SES_SESSION SES ON USR.ID_USER = SES.ID_USER ";
    $requete .= " WHERE CNT.ID_USER_2 = USR.ID_USER ";
    $requete .= " and CNT.ID_USER_1 = " . $id_user . " ";
    $requete .= " and CNT.ID_USER_2 <> " . $id_user . " ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " and CNT.CNT_STATUS > 0 ";
    $requete .= " and CNT.CNT_STATUS < 5 ";
    $requete .= " ORDER BY CNT_USER_GROUP, SES_STATUS, USR_USERNAME ";
  }
  //
  //
  // 2 - MODE OpenCommunity :
  $reject_list = "#";
  if ( (_SPECIAL_MODE_OPEN_COMMUNITY != "")  and  (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') )
  {
    // on récupère la liste de ceux qu'on ne veut pas (list people we dont want).
    $requete  = " select CNT.ID_USER_2 ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT ";
    $requete .= " WHERE CNT.ID_USER_1 = " . $id_user . " ";
    $requete .= " and CNT.ID_USER_2 <> " . $id_user . " ";
    $requete .= " and (CNT.CNT_STATUS < 0 or CNT.CNT_STATUS = 5) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-20e]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      while( list ($usr_id) = mysqli_fetch_row ($result) )
      {
        $reject_list .= $usr_id . "#";
      }
    }
    //
    $requete  = " SELECT 0, SES.SES_STATUS, USR.ID_USER, SES.SES_AWAY_REASON, '', USR.ID_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SES_SESSION SES ON USR.ID_USER = SES.ID_USER ";
    $requete .= " WHERE USR.ID_USER <> " . $id_user . " ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " ORDER BY SES_STATUS DESC, USR_USERNAME ";
  }
  //
  // 3 - MODE GroupCommunity :
  if ( (_SPECIAL_MODE_GROUP_COMMUNITY != '')  and  (_SPECIAL_MODE_OPEN_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') )
  {
    $username = f_get_username_of_id($id_user);
    $requete  = " select DISTINCT 0, SES.SES_STATUS, USR.ID_USER, SES.SES_AWAY_REASON, GRP.GRP_NAME, USR.ID_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USG_USERGRP USG1, " . $PREFIX_IM_TABLE . "USG_USERGRP USG2, " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SES_SESSION SES ON USR.ID_USER = SES.ID_USER  ";
    $requete .= " WHERE USG1.ID_GROUP = USG2.ID_GROUP ";
    $requete .= " and USG1.ID_USER = USR.ID_USER ";
    $requete .= " and USG1.USG_PENDING = 0 "; // (17/09/11)
    $requete .= " and USG2.ID_GROUP = GRP.ID_GROUP ";
    $requete .= " and USG2.ID_USER = " . $id_user . " ";
    $requete .= " and USG2.USG_PENDING = 0 "; // (17/09/11)
    $requete .= " and USR.USR_USERNAME <> '" . $username . "' ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " ORDER BY GRP_NAME, USR_USERNAME ";
    //if ($tri == 'etat')
    //  $requete .= " ORDER BY GRP_NAME, SES_STATUS, USR_USERNAME ";
    //else
  }
  //
  // 4 - MODE OpenGroupCommunity :
  if ( (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '')  and  (_SPECIAL_MODE_OPEN_COMMUNITY == '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') )
  {
    //$username = f_get_username_of_id($id_user);
    $requete  = " select DISTINCT 0, SES.SES_STATUS, USR.ID_USER, SES.SES_AWAY_REASON, GRP.GRP_NAME, USR.ID_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USG_USERGRP USG, " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SES_SESSION SES ON USR.ID_USER = SES.ID_USER  ";
    $requete .= " WHERE USG.ID_USER = USR.ID_USER ";
    $requete .= " and USG.ID_GROUP = GRP.ID_GROUP ";
    $requete .= " and USG.USG_PENDING = 0 ";
    //$requete .= " and USR.USR_USERNAME <> '" . $username . "' ";
    $requete .= " and USR.ID_USER <> " . $id_user . " ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " ORDER BY GRP_NAME, USR_USERNAME ";
  }
  //
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-20f]", $requete);
  if ( mysqli_num_rows($result) != 0 )
  {
    $nb_c_send = 0;
    while( list ($id_contact, $eta_num, $usr_id, $away_reason, $group, $id_role_cnt) = mysqli_fetch_row ($result) )
    {
      $ok = 'OK';
      if ( strlen($reject_list) > 1 )
      {
        if (strstr($reject_list, "#" . $usr_id . "#") != "" ) $ok = 'Ko';
      }
      if ( strlen($hide_list) > 1 )
      {
        if (strstr($hide_list, "#" . $usr_id . "#") != "" ) $ok = 'Ko';
      }
      //
      // Hide user from a role - example: users id_role=2 cannot see users id_role=1
      if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
      {
        if ($id_role == 2) // example: 2
        {
          #if ($id_role_cnt == 1) $ok = 'Ko'; // example: 1
        }
      }
      //
      // si non masqué et non rejeté
      if ( $ok == 'OK')
      {
        if ($eta_num > 4) $eta_num = 0; // si masqué ou offline
        //
        // on renvoi les contacts du user (un par un)
        echo ">F16#" . f_encode64($id_contact . "#" . $eta_num . "#" . $usr_id  . "#" . $away_reason . "#" . $group . "#") . "|"; // séparateur de ligne : '|' (pipe).
        $nb_c_send += 1;
      }
    }
    //
    if ($nb_c_send < 1) // si aucun contact envoyé (car invisibles)
    {
      // renvoie : aucun contact pour ce user
      echo ">F16#0#-#0#";
    }
  }
  else
  {
    // renvoie : aucun contact pour ce user
    echo ">F16#0#-#0#";
  }
  //
  if ($id_session <= 0) $id_session = f_get_id_session_id_user($id_user);
  update_time_session_id_session($id_session);
  //
  mysqli_close($id_connect);
}
?>
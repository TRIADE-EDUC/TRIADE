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
if ( (!isset($_GET['iu'])) or (!isset($_GET['m'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
$msg =        $_GET['m'];
if (isset($_GET['ig'])) $id_grp = intval($_GET['ig']);  else  $id_grp = "0";  
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_grp)) $id_grp = "0";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "0";
if (preg_match("#[^0-9]#", $n_version)) $n_version = "0";
//
if ( ($id_user > 0) and ($n_version > 34) and ($msg != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/shoutbox.inc.php");
  require("lang.inc.php"); // pour format date et heure.
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F81#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_allow_shoutbox = _SHOUTBOX;
  $t_shoutbox_need_approval = _SHOUTBOX_NEED_APPROVAL;
  $t_censor_messages = _CENSOR_MESSAGES;
  $t_log_messages = _HISTORY_MESSAGES_ON_ACP;
  $t_shoutbox_quota_user_day = _SHOUTBOX_QUOTA_USER_DAY;
  $t_shoutbox_quota_user_week = _SHOUTBOX_QUOTA_USER_WEEK;
  $t_shoutbox_approval_queue_user = _SHOUTBOX_APPROVAL_QUEUE_USER;
  $t_shoutbox_lock_user_approval = _SHOUTBOX_LOCK_USER_APPROVAL;
  $t_shoutbox_lock_user_votes = _SHOUTBOX_LOCK_USER_VOTES;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_shoutbox = f_role_permission($id_role, "SHOUTBOX", _SHOUTBOX);
      $t_shoutbox_need_approval = f_role_permission($id_role, "SHOUTBOX_NEED_APPROVAL", _SHOUTBOX_NEED_APPROVAL);
      $t_censor_messages = f_role_permission($id_role, "CENSOR_MESSAGES", _CENSOR_MESSAGES);
      $t_log_messages = f_role_permission($id_role, "HISTORY_MESSAGES_ON_ACP", _HISTORY_MESSAGES_ON_ACP);
      $t_shoutbox_quota_user_day = f_role_permission($id_role, "SHOUTBOX_QUOTA_USER_DAY", _SHOUTBOX_QUOTA_USER_DAY);
      $t_shoutbox_quota_user_week = f_role_permission($id_role, "SHOUTBOX_QUOTA_USER_WEEK", _SHOUTBOX_QUOTA_USER_WEEK);
      $t_shoutbox_approval_queue_user = f_role_permission($id_role, "SHOUTBOX_APPROVAL_QUEUE_USER", _SHOUTBOX_APPROVAL_QUEUE_USER);
      $t_shoutbox_lock_user_approval = f_role_permission($id_role, "SHOUTBOX_LOCK_USER_APPROVAL", _SHOUTBOX_LOCK_USER_APPROVAL);
      $t_shoutbox_lock_user_votes = f_role_permission($id_role, "SHOUTBOX_LOCK_USER_VOTES", _SHOUTBOX_LOCK_USER_VOTES);
    }
  }
  //
  if ($t_allow_shoutbox == "") // pour plus bas si par groupe.
  {
    die(">F81#KO#2#"); // 2: Not allowed (option not activated)  
  }
  //
  if (intval($t_shoutbox_quota_user_day) > 0)
  {
    $requete  = " select SBS_NB_LAST_DATE";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $requete .= " and SBS_LAST_DATE = CURDATE() ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-111a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_today) = mysqli_fetch_row ($result);
      if ($nb_today >= intval($t_shoutbox_quota_user_day) )
        die(">F81#KO#4#"); // 4: Over quota
    }
  }
  //
  if (intval($t_shoutbox_quota_user_week) > 0)
  {
    $requete  = " select SBS_NB_LAST_WEEK";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $requete .= " and SBS_LAST_WEEK = WEEK(CURDATE()) ";
    $requete .= " and TIMESTAMPDIFF(WEEK, SBS_LAST_DATE, CURDATE() ) = 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-111b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_week) = mysqli_fetch_row ($result);
      if ($nb_week >= intval($t_shoutbox_quota_user_week) )
        die(">F81#KO#4#"); // 4: Over quota
    }
  }
  //
  //
  $grp_shoutbox_allowed = 0;
  $grp_shoutbox_need_approval = -1; // 0
  if ($id_grp > 0)
  {
    $grp_shoutbox_need_approval = 1;
    $requete  = " select GRP_SHOUTBOX, GRP_SBX_NEED_APPROVAL";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP ";
    $requete .= " WHERE ID_GROUP = " . $id_grp;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-111j]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($grp_shoutbox_allowed, $grp_shoutbox_need_approval) = mysqli_fetch_row ($result);
    }
    if ($grp_shoutbox_allowed <= 0)  
    {
      die(">F81#KO#2#"); // 2: Not allowed (option not activated)
    }
  }
  //
  //
  $this_group_box_need_approval = ""; // que ce soit en groupe OU non.
  if ( ($t_shoutbox_need_approval != "") or ($grp_shoutbox_need_approval > 0) )
  {
    $this_group_box_need_approval = "X"; // default  = _SHOUTBOX_NEED_APPROVAL
    if ($grp_shoutbox_need_approval == 0) $this_group_box_need_approval = "";
    //
    if ($this_group_box_need_approval != "")
    {
      // si l'utilisateur a trop de messages en attente d'approbation (ex: tentative de SPAM).
      if (intval($t_shoutbox_approval_queue_user) > 0)
      {
        $requete  = " select count(*) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
        $requete .= " WHERE ID_USER_AUT = " . $id_user;
        $requete .= " and SBX_DISPLAY = 0 ";
        if ($id_grp > 0) $requete .= " and ID_GROUP_DEST = " . $id_grp;
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-111c]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($nb_wait) = mysqli_fetch_row ($result);
          if ($nb_wait >= intval($t_shoutbox_approval_queue_user) )
            die(">F81#KO#5#"); // 5: Approval queue Over quota
        }
      }
      // si trop de messages en attente d'approbation (en tout)
      if (intval(_SHOUTBOX_APPROVAL_QUEUE) > 0)
      {
        $requete  = " select count(*) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
        $requete .= " WHERE SBX_DISPLAY = 0 ";
        if ($id_grp > 0) $requete .= " and ID_GROUP_DEST = " . $id_grp;
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-111d]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($nb_reject) = mysqli_fetch_row ($result);
          if ($nb_reject >= intval(_SHOUTBOX_APPROVAL_QUEUE) )
            die(">F81#KO#5#"); // 5: Approval queue Over quota
        }
      }
    }
    //
    // Si trop de rejets
    if (intval($t_shoutbox_lock_user_approval) > 0)
    {
      $requete  = " select SBS_NB_REJECT ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
      $requete .= " WHERE ID_USER_AUT = " . $id_user;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-111e]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($nb_reject) = mysqli_fetch_row ($result);
        if ($nb_reject >= intval($t_shoutbox_lock_user_approval) )
          die(">F81#KO#4#");  // 4: Over quota
      }
    }
  }
  //
  if (intval($t_shoutbox_lock_user_votes) > 0)
  {
    $requete  = " select count(*)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $requete .= " and SBV_VOTE_L < 0 ";
    ## $requete .= " and SBV_DATE = CURDATE() ";                                  // only today
    ## $requete .= " and TIMESTAMPDIFF(WEEK, SBV_DATE, CURDATE() ) = 0 ";         // only this week
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-111f]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_vote_negatif) = mysqli_fetch_row ($result);
      if ($nb_vote_negatif >= intval($t_shoutbox_lock_user_votes) )
      {
        //
        die(">F81#KO#4#" . $nb_vote_negatif . "#"); // over quota
      }
    }
  }
  //
  $msg_clair = "";
  if ( ($t_censor_messages != '') or ($t_log_messages != '') )
  {
    $msg_clair = f_decode64_wd($msg);
  }
  //
  // on censure les mots interdits par l'administrateur :
  if ($t_censor_messages != '')
  {
    if (is_readable("../common/config/censure.txt"))
    {
      $msg_clair = trim($msg_clair);
      require ("../common/words_filtering.inc.php");
      $msg_clair = textCensure($msg_clair, "../common/config/censure.txt");
      $msg = f_encode64($msg_clair);
    }
  }
  //
  //
  $sending = "#";
  $requete  = "INSERT INTO " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ( ID_GROUP_DEST, ID_USER_AUT, SBX_TEXT, SBX_TIME, SBX_DATE, SBX_DISPLAY) ";
  $requete .= "VALUES (" . $id_grp . ", " . $id_user . ", '" . $msg . "', CURTIME(), CURDATE(), ";
  if ($this_group_box_need_approval != "")
    $requete .= "0 ) ";
  else
  {
    $requete .= "1 ) ";
    $sending = date("H:i:s") . "#"; 
  }
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-111g]", $requete);
  //
  // message bien envoyé
  echo ">F81#OK#" . $sending . "#";
  //
  stats_sbx_inc($id_user);
  //
  if ( ($this_group_box_need_approval != "") and (_SEND_ADMIN_ALERT != "") )
  {
    if (f_is_user_admin($id_user) == "")
    {
      $txt = $l_index_shoutbox_pending;
      if ($txt == "") $txt = "Shoutbox message(s) waiting Approval...";
      send_alert_message_to_admins($txt);
    }
  }
  //
  // si option de log (archivage) des messages échangé activé :
  if ($t_log_messages != '')
  {
    // on récupère le username expéditeur :
    $username = f_get_username_of_id($id_user);
    //
    $ip = $_SERVER['REMOTE_ADDR'];	
    //
    $chemin = "log/" . "shoutbox_log.txt" ;
    $fp = fopen($chemin, "a");
    if (flock($fp, 2));
    {
      //fputs($fp,date("d/m/Y;H:i:s") . ";" . $username . ";" . $msg_clair . ";" . $ip ."\r\n");
      fputs($fp,date($l_date_format_display . ";" . $l_time_format_display) . ";" . $username . ";" . $msg_clair . ";" . $ip ."\r\n");
    }
    flock($fp, 3);
    fclose($fp);
  }
  //
  //
  // Ménage :
  shoutbox_remove_old_msg();
  //
  //
  // Flux RSS :
  //
  //if ( (_SHOUTBOX_PUBLIC != "") and ($grp_shoutbox_need_approval == 0) )
  if ( (_SHOUTBOX_PUBLIC != "") and ($this_group_box_need_approval == "") )
  {
    shoutbox_update_rss($id_grp);
  }
  //
  mysqli_close($id_connect);
}
?>
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
if ( (!isset($_GET['u1'])) or (!isset($_GET['sc'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['u1']));
$id_user = 		  (intval($id_user) - intval($action));
$id_u_2 = 		  intval(f_decode64_wd($_GET['u2']));
$id_grp =       intval(f_decode64_wd($_GET['ig']));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$fil_name = 		f_decode64_wd($_GET['nf']);
$fil_hash = 		f_decode64_wd($_GET['hf']);
$fil_comment = 	f_decode64_wd($_GET['ct']);
$fil_date = 		$_GET['df'];
$fil_size =	    intval($_GET['tf']);
$fil_project =	intval($_GET['pj']);
$n_version =	  intval($_GET['v']);
if (isset($_GET['cp'])) $compress = $_GET['cp']; else $compress = "";
if (isset($_GET['cc'])) $protect = $_GET['cc']; else $protect = "";
$compress = trim($compress);
$protect = trim($protect);
//
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
if (preg_match("#[^0-9]#", $id_grp)) $id_grp = "";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version > 0) and ($ip != "") and ($fil_name != "") and ($fil_size > 0) and ($fil_date != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/share_files.inc.php");
  require ("lang.inc.php"); // pour l_index_share_file_pending
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')    die(">F141#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_sharefiles = _SHARE_FILES;
  $t_sharefiles_need_approval = _SHARE_FILES_NEED_APPROVAL;
  $t_sharefiles_exchange = _SHARE_FILES_EXCHANGE;
  $t_sharefiles_exchange_need_approval = _SHARE_FILES_EXCHANGE_NEED_APPROVAL;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_sharefiles = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
      $t_sharefiles_need_approval = f_role_permission($id_role, "SHARE_FILES_NEED_APPROVAL", _SHARE_FILES_NEED_APPROVAL);
      $t_sharefiles_exchange = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
      $t_sharefiles_exchange_need_approval = f_role_permission($id_role, "SHARE_FILES_EXCHANGE_NEED_APPROVAL", _SHARE_FILES_EXCHANGE_NEED_APPROVAL);
    }
  }
  //
  if ($t_sharefiles == "")
  {
    die(">F141#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  if ($id_u_2 > 0)
  {
    if ($t_sharefiles_exchange == "")
    {
      die(">F141#KO#5#"); // 5: n'a pas les droits (option non activée).
    }
  }
  //
  //
  $fil_name = trim($fil_name);
  $fil_name = str_replace("'", "", $fil_name);
  $fil_name = str_replace("~", "", $fil_name);
  $fil_name = str_replace("`", "", $fil_name);
  $fil_name = str_replace(chr(34), "", $fil_name);
  //$fil_name = f_DelSpecialChar($fil_name);
  //
  $fil_comment = trim($fil_comment);
  $fil_comment = str_replace("'", " ", $fil_comment);
  //
  $ok = "";
  $requete  = " select ID_FILE, ID_USER_DEST, ID_GROUP_DEST ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE FIL_NAME = '" . $fil_name . "' ";
  $requete .= " and ID_USER_AUT = " . $id_user;
  $requete .= " and FIL_HASH = '" . $fil_hash . "' ";
  $requete .= " and FIL_SIZE = " . $fil_size . " "; 
  $requete .= " and FIL_ONLINE = '' "; // autorisé (par le serveur), mais pas encore uploadé
  if ($fil_project > 0) 
    $requete .= " and ID_PROJET = " . $fil_project . " "; 
  else
    $requete .= " and ID_PROJET is null "; 
  //
  $requete .= " limit 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-141a]", $requete);
  if ( mysqli_num_rows($result) == 1)
  {
    list ($t_id_file, $t_id_u_dest, $t_id_g_dest) = mysqli_fetch_row ($result);
    $t_id_file = intval($t_id_file);
    $t_id_u_dest = intval($t_id_u_dest);
    $t_id_g_dest = intval($t_id_g_dest);
    if ( ($id_u_2 > 0) and ($id_u_2 == $t_id_u_dest) ) $ok = "X";
    if ( ($id_grp > 0) and ($id_grp == $t_id_g_dest) and ($ok == "") ) $ok = "X";
    if ( ($id_u_2 == 0) and ($id_grp == 0)  ) $ok = "X";
  }
  else
    die(">F141#KO#4#");
  //
  //
  if ( ($ok != "") and ($t_id_file > 0) )
  {
    $online = "Y"; // uploaded, online
    if ( ( ($t_sharefiles_need_approval != "") and ($id_u_2 <= 0) )  or  ( ($t_sharefiles_exchange_need_approval != "") and ($id_u_2 > 0) ) )   $online = "W"; // uploaded, Wait admin.
    //
    if ($compress == "x")
    {
      $compress = "C";
      if ($protect != "") $compress = "P";
    }
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " SET FIL_ONLINE = '" . $online . "', "; 
    $requete .= " FIL_DATE = '" . $fil_date . "', "; 
    //$requete .= " FIL_TAGS = '" . $zzzzzzzzz . "', "; 
    $requete .= " FIL_DATE_ADD = CURDATE() , "; 
    $requete .= " FIL_COMPRESS = '" . $compress . "', "; 
    $requete .= " FIL_PROTECT = '" . $protect . "', "; 
    $requete .= " FIL_COMMENT = '" . $fil_comment . "' "; 
    $requete .= " WHERE ID_FILE = " . $t_id_file . " ";
    $requete .= " and FIL_ONLINE = '' "; 
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-141b]", $requete);
    //
    echo ">F141#OK###";
    //
    //
    // on récupère le username expéditeur :
    $username_1 = f_get_username_of_id($id_user);
    if ($id_u_2 > 0)
    { 
      $username_2 = f_get_username_of_id($id_u_2);
      //
      if ($online == "Y")
        write_log("log_files_exchange_sended", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $fil_size  . ";" . $fil_date . ";" . $fil_project . ";");
      else
        write_log("log_files_exchange_proposed", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $fil_size  . ";" . $fil_date . ";" . $fil_project . ";");
    }
    else
    {
      if ($id_grp > 0)
      {
        if ($online == "Y")
          write_log("log_files_share_group_sended", $username_1 . ";" . $fil_name . ";" . $fil_size  . ";" . $fil_date . ";" . $fil_project . ";" . $id_grp . ";");
        else
          write_log("log_files_share_group_proposed", $username_1 . ";" . $fil_name . ";" . $fil_size  . ";" . $fil_date . ";" . $fil_project . ";" . $id_grp . ";");
      }
      else
      {
        if ($online == "Y")
          write_log("log_files_share_sended", $username_1 . ";" . $fil_name . ";" . $fil_size  . ";" . $fil_date . ";" . $fil_project . ";");
        else
          write_log("log_files_share_proposed", $username_1 . ";" . $fil_name . ";" . $fil_size  . ";" . $fil_date . ";" . $fil_project . ";");
      }
    }
    //
    //
    if ( ($online == "W") and (_SEND_ADMIN_ALERT != "") ) // uploaded, Wait admin.
    {
      // Do not send admin notify, if already admin.
      if (f_is_user_admin($id_user) == "")
      {
        $txt = $l_index_share_file_pending;
        if ($txt == "") $txt = "Sharing file(s) waiting Approval...";
        send_alert_message_to_admins($txt);
      }
    }
    //
    //
    if ($id_u_2 > 0)
      stats_sharefile_inc($id_user, "E");
    else
      stats_sharefile_inc($id_user, "S");
    //
    //
    // Mettre en corbeille les fichiers échangés non lus :
    if ($id_u_2 > 0)
    {
      $validy = intval(_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY);
      if ($validy < 10) $validy = 10;
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
      $requete .= " SET FIL_ONLINE = 'T' "; // Trash
      $requete .= " WHERE FIL_ONLINE = 'Y' "; 
      $requete .= " and ID_USER_DEST > 0 "; 
      $requete .= " and ID_GROUP_DEST is null "; 
      $requete .= " and TO_DAYS(NOW()) - TO_DAYS(FIL_DATE_ADD) > " . $validy;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-141c]", $requete);
    }
    //
  }
  else
    echo ">F141#KO#5#" . "-" . $id_u_2 . "-" . $id_grp . "-";
  //
  mysqli_close($id_connect);
}
?>
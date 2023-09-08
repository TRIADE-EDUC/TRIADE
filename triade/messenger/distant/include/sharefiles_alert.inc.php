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
if ( (!isset($_GET['iu'])) or (!isset($_GET['sc'])) or (!isset($_GET['fi'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$session_chk =  f_decode64_wd($_GET['sc']);
$file_id =      intval(f_decode64_wd($_GET['fi']));
$ip = 			    f_decode64_wd($_GET['ip']);
$n_version =	  intval($_GET['v']);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $file_id)) $file_id = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($file_id > 0) and ($n_version > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/share_files.inc.php");
  require("lang.inc.php"); // pour l_index_share_file_pending
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F145#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_sharefiles = _SHARE_FILES;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_sharefiles = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
    }
  }
  //
  if ($t_sharefiles == "")
  {
    die(">F144#KO#2#"); // 2:n'a pas les droits (option non activée).
  }
  //
  $requete  = " select FIL_NAME, ID_USER_AUT, ID_USER_DEST, FIL_COMMENT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE ID_FILE = " . $file_id;
  $requete .= " limit 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-144a]", $requete);
  if ( mysqli_num_rows($result) == 1)
  {
    list ($fil_name, $t_id_u_aut, $t_id_u_dest, $fil_comment) = mysqli_fetch_row ($result);
  }
  else
    die(">F144#KO#4#");
  //
  //
  $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " set FIL_ONLINE = 'Z', ";
  $requete .= " FIL_NB_ALERT = (FIL_NB_ALERT + 1) ";
  $requete .= " WHERE ID_FILE = " . $file_id;
  $requete .= " and FIL_ONLINE <> 'Z' ";
  $requete .= " limit 1 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-144b]", $requete);
  //
  echo ">F144#OK##"; // bien enregistré 
  //
  if (_SEND_ADMIN_ALERT != "")
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
  stats_sharefile_add_alert_reject($id_user, "A");
  //
  $username_alert = f_get_username_of_id($id_user);
  $username_auth = f_get_username_of_id($t_id_u_aut);
  $username_dest = "";
  if (intval($t_id_u_dest) > 0) $username_dest = f_get_username_of_id($t_id_u_dest);
  //
  mysqli_close($id_connect);
  //
  write_log("log_files_share_alert", $username_alert . ";" . $fil_name . ";" . $fil_comment . ";" . $username_auth  . ";" . $username_dest . ";" );
}
?>
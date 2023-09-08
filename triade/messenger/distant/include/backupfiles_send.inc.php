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
if ( (!isset($_GET['iu'])) or (!isset($_GET['sc'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$fil_name = 		f_decode64_wd($_GET['nf']);
$fil_hash = 		f_decode64_wd($_GET['hf']);
$fil_size =	    intval($_GET['tf']);
$n_version =	  intval($_GET['v']);
$protect =      f_decode64_wd($_GET['cc']);
//$protect =      trim($protect);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version > 42) and ($ip != "") and ($fil_name != "") and ($fil_size > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  #require ("lang.inc.php"); // pour l_index_share_file_pending
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')    die(">F151#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_backupfiles = _BACKUP_FILES;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_backupfiles = f_role_permission($id_role, "BACKUP_FILES", _BACKUP_FILES);
    }
  }
  //
  if ($t_backupfiles == "")
  {
    die(">F151#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  //
  //
  $fil_name = trim($fil_name);
  $fil_name = str_replace("'", "", $fil_name);
  $fil_name = str_replace("~", "", $fil_name);
  $fil_name = str_replace("`", "", $fil_name);
  $fil_name = str_replace(chr(34), "", $fil_name);
  $fil_name = f_DelSpecialChar($fil_name);
  //
  $t_id_file = 0;
  $requete  = " select ID_FILEBACKUP ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE FIB_NAME = '" . $fil_name . "' ";
  $requete .= " and ID_USER = " . $id_user;
  //$requete .= " and FIB_HASH = '" . $fil_hash . "' ";  NON ! car si envoie plusieurs fois dans la même journée !
  //$requete .= " and FIB_SIZE = " . $fil_size . " ";   NON ! car si envoie plusieurs fois dans la même journée !
  //$requete .= " and FIB_ONLINE = '' ";  enlevé, pouvoir faire la sauvegarde plusieurs fois par jour                    // autorisé (par le serveur), mais pas encore uploadé
  $requete .= " limit 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-151a]", $requete);
  if ( mysqli_num_rows($result) == 1)
  {
    list ($t_id_file) = mysqli_fetch_row ($result);
    $t_id_file = intval($t_id_file);
  }
  else
    die(">F151#KO#4#" . $requete . "#");
  //
  //
  if ($t_id_file > 0)
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " SET FIB_ONLINE = 'Y', ";  // uploaded, online
    $requete .= " FIB_DATE_ADD = CURDATE() , "; 
    $requete .= " FIB_SIZE = " . $fil_size . ", "; 
    $requete .= " FIB_HASH = '" . $fil_hash . "', "; 
    $requete .= " FIB_PROTECT = '" . $protect . "' "; 
    $requete .= " WHERE ID_FILEBACKUP = " . $t_id_file . " ";
    //$requete .= " and FIB_ONLINE = '' ";  NON ! car si envoie plusieurs fois dans la même journée !
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-151b]", $requete);
    //
    echo ">F151#OK###";
    //
    //
    // on récupère le username expéditeur :
    $username_1 = f_get_username_of_id($id_user);
    write_log("log_files_backup_sended", $username_1 . ";" . $fil_name . ";" . $fil_size  . ";");
    //
    //if ($id_u_2 > 0)
    //  stats_sharefile_inc($id_user, "E");
    //else
    //  stats_sharefile_inc($id_user, "S");
    //
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " SET USR_DATE_BACKUP = CURDATE() "; 
    $requete .= " WHERE ID_USER = " . $id_user . " ";
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-151c]", $requete);
    //
  }
  else
    echo ">F151#KO#5#";
  //
  mysqli_close($id_connect);
}
?>
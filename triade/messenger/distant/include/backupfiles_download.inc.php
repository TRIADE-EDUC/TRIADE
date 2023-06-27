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
if ( (!isset($_GET['iu'])) or (!isset($_GET['sc'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die("");
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$file_id =	    intval($_GET['fi']);
//$fil_size =	    intval($_GET['tf']);
$n_version =	  intval($_GET['v']);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $file_id)) $file_id = "";
//
if ( ($id_user > 0) and ($n_version > 42) and ($ip != "") and ($file_id > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F153#KO#1#"); // 1:session non ouverte.
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
    die(">F153#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  //
  //
  $requete  = " select FIB_NAME, ID_FILEBACKUP, FIB_HASH, FIB_PROTECT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE ID_FILEBACKUP = " . $file_id . " ";
  $requete .= " and ID_USER = " . $id_user . " ";
  $requete .= " and FIB_ONLINE = 'Y' ";
  //$requete .= " and FIB_SIZE = " . $fil_size . " ";
  $requete .= " LIMIT 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-153a]", $requete);
  if ( mysqli_num_rows($result) == 1)
  {
    list ($t_fic, $t_id_fic, $t_hash, $t_protect) = mysqli_fetch_row ($result);
    if ($t_id_fic == $file_id)
    {
      echo ">F153#OK#" . f_encode64($t_fic) . "#" . f_encode64($t_hash) . "#" . f_encode64($t_protect) . "###";
    }
    else
      echo ">F153#KO#9##";
  }
  else
    echo ">F153#KO#8##";
  //
  mysqli_close($id_connect);
}
?>
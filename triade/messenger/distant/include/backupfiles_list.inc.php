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
$n_version =	  intval($_GET['v']);
if (isset($_GET['dtf'])) $dt_f = $_GET['dtf'];  else  $dt_f = "";  
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version > 42) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F156#KO#1#"); // 1:session non ouverte.
  //
  if (!ctype_alnum($dt_f))  $dt_f = "";
  $dt_form = "d/m/Y"; // FR
  if ($dt_f == 'EN') $dt_form = "m-d-Y";
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
    die(">F156#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  //
  //
  $requete  = " select ID_FILEBACKUP, FIB_NAME, FIB_SIZE, FIB_DATE_ADD, FIB_PROTECT "; // FIB_HASH, 
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE ID_USER = " . $id_user . " ";
  $requete .= " and FIB_ONLINE = 'Y' ";
  $requete .= " order by FIB_DATE_ADD DESC ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-156a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo ">F156#OK####|";
    while( list ($id_filebackup, $fib_name, $fib_size, $date_add, $fib_protect) = mysqli_fetch_row ($result) )
    {
      $date_add = date($dt_form, strtotime($date_add));
      //
      echo f_encode64("#" . $id_filebackup . "#" . $fib_name . "#" . $fib_size . "#" . $date_add . "#" . $fib_protect . "#") . "|"; // séparateur de ligne : '|' (pipe).
    }
  }
  else
  {
    // renvoie : aucun fichier pour ce user
    echo ">F156#0#-#0#";
  }
  //
  mysqli_close($id_connect);
}
?>
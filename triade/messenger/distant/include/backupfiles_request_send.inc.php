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
if (isset($_GET['ro'])) $remove_old = $_GET['ro']; else $remove_old = "";
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
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F150#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_backupfiles = _BACKUP_FILES;
  $t_backupfiles_max_file_size = intval(_BACKUP_FILES_MAX_ARCHIVE_SIZE);
  $t_backupfiles_max_nb_files_user = intval(_BACKUP_FILES_MAX_NB_ARCHIVES_USER);
  $t_backupfiles_max_space_size_user = intval(_BACKUP_FILES_MAX_SPACE_SIZE_USER);
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_backupfiles = f_role_permission($id_role, "BACKUP_FILES", _BACKUP_FILES);
      $t_backupfiles_max_file_size = f_role_permission($id_role, "BACKUP_FILES_MAX_ARCHIVE_SIZE", _BACKUP_FILES_MAX_ARCHIVE_SIZE);
      $t_backupfiles_max_nb_files_user = f_role_permission($id_role, "BACKUP_FILES_MAX_NB_ARCHIVES_USER", _BACKUP_FILES_MAX_NB_ARCHIVES_USER);
      $t_backupfiles_max_space_size_user = f_role_permission($id_role, "BACKUP_FILES_MAX_SPACE_SIZE_USER", _BACKUP_FILES_MAX_SPACE_SIZE_USER);
    }
  }
  //
  if ($t_backupfiles == "")
  {
    die(">F150#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  //
  $username = f_get_username_of_id($id_user);
  //
  //
  $fil_size_mo = ($fil_size / 1024);
  if (intval($t_backupfiles_max_file_size) > 0)
  {
    //if ($fil_size > intval($t_backupfiles_max_file_size))
    if ($fil_size_mo > intval($t_backupfiles_max_file_size)) // option en MB et non en KB
    {
      echo ">F150#KO#3#A#" . $fil_size_mo . "#" . $t_backupfiles_max_file_size . "#"; // 3a: To big
      write_log("log_files_backup_cannot_3a", $username . ";" . $fil_name . ";" . $fil_size_mo  . ";" . $t_backupfiles_max_file_size . ";");
      die();
    }
  }
  //
  if (intval($t_backupfiles_max_nb_files_user) > 0)
  {
    $requete  = " select COUNT(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE ID_USER = " . $id_user . " ";
    $requete .= " and FIB_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-150a]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_nb_file_user) = mysqli_fetch_row ($result);
      if ( intval($t_nb_file_user) >= intval($t_backupfiles_max_nb_files_user) )
      {
        $older_file_name = "";
        $older_file_id = 0;
        // Over Quota :
        $requete  = " select FIB_NAME, ID_FILEBACKUP ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
        $requete .= " WHERE ID_USER = " . $id_user . " ";
        $requete .= " and FIB_ONLINE <> '' ";
        $requete .= " order by FIB_DATE_ADD ";
        $requete .= " limit 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-150b]", $requete);
        if ( mysqli_num_rows($result) > 0 )
        {
          list ($older_file_name, $older_file_id) = mysqli_fetch_row ($result);
        }
        
        echo ">F150#KO#4#A#" . $t_nb_file_user . "#" . $older_file_name . "#" . $older_file_id . "#" . $t_backupfiles_max_nb_files_user . "#"; // 4A: too much files for this user
        if ($remove_old == "") write_log("log_files_backup_cannot_4a", $username . ";" . $fil_name . ";" . $t_nb_file_user  . ";" . $t_backupfiles_max_nb_files_user . ";");
        die();
      }
    }
  }
  /*
  if (intval(_BACKUP_FILES_MAX_NB_FILES_TOTAL) > 0)
  {
    $requete  = " select SQL_CACHE COUNT(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE FIB_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-150c]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_nb_file) = mysqli_fetch_row ($result);
      if ( intval($t_nb_file) >= intval(_BACKUP_FILES_MAX_NB_FILES_TOTAL) )
        die(">F150#KO#4#B#"); // 4b: too much files
    }
  }
  */
  if (intval($t_backupfiles_max_space_size_user) > 0)
  {
    $requete  = " select SUM(FIB_SIZE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE ID_USER = " . $id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-150d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_size_ko) = mysqli_fetch_row ($result);
      if ( (($t_size_ko / 1024) + $fil_size_mo) >= intval($t_backupfiles_max_space_size_user) )
      {
        echo ">F150#KO#3#B#" . $fil_size_mo . "#" . $t_backupfiles_max_space_size_user . "#"; // 3b : Over quota user
        write_log("log_files_backup_cannot_3b", $username . ";" . $fil_name . ";" . $fil_size_mo  . ";" . $t_backupfiles_max_space_size_user . ";");
        die();
      }
    }
  }
  //
  if (intval(_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL) > 0)
  {
    $requete  = " select SUM(FIB_SIZE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE FIB_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-150e]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_size_ko) = mysqli_fetch_row ($result);
      if ( (($t_size_ko / 1024) + $fil_size_mo) >= intval(_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL) )
      {
        echo ">F150#KO#3#C#"; // 3c : Over quota total
        write_log("log_files_backup_cannot_3c", $username . ";" . $fil_name . ";" . $fil_size_mo  . ";");
        die();
      }
    }
  }
  //
  //
  //
  // Purge des anciennes tentatives d'envoies non abouties :
  $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE FIB_ONLINE = '' ";
  $requete .= " and FIB_DATE_ADD <> CURDATE() ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-150f]", $requete);
  //
  //
  $fil_name = trim($fil_name);
  $fil_name = str_replace("'", "", $fil_name);
  $fil_name = str_replace("~", "", $fil_name);
  $fil_name = str_replace("`", "", $fil_name);
  $fil_name = str_replace(chr(34), "", $fil_name);
  $fil_name = f_DelSpecialChar($fil_name);
  //
  $deja_envoye = "";
  // Vérifier si le fichier n'existe pas déja :
  $requete  = " select ID_USER, FIB_ONLINE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
  $requete .= " WHERE FIB_NAME = '" . $fil_name . "' ";
  $requete .= " LIMIT 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-150g]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    list ($deja_envoye, $online) = mysqli_fetch_row ($result);
    //if ( ($online == "") and (intval($deja_envoye) == $id_user) )
    if (intval($deja_envoye) == $id_user)
    {
      $deja_envoye = "X"; // pas d'insert into
      echo ">F150#OK#ALREADY_TRY#" . "###"; 
    }
    else
      echo ">F150#KO#9#" . $deja_envoye . "#";
  }
  //
  //
  // AUTORISATION par AJOUT dans la table IM_FIB_FILEBACKUP (avec FIB_ONLINE à vide) :
  if ($deja_envoye == "")
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " ( FIB_NAME, FIB_HASH, FIB_SIZE, ID_USER, FIB_DATE_ADD, FIB_ONLINE ) ";
    $requete .= " VALUES ('" . $fil_name . "', '" . $fil_hash . "', " . $fil_size . ", " . $id_user . ", CURDATE(), '' ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-150h]", $requete);
    //
    echo ">F150#OK#NEW#" . "###";
  }
  //
  mysqli_close($id_connect);
}
?>
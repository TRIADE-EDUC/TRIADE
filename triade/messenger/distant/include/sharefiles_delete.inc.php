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
if ( (!isset($_GET['u1'])) or (!isset($_GET['sc'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die("");
//
$id_user =	    intval(f_decode64_wd($_GET['u1']));
$id_user = 		  (intval($id_user) - intval($action));
$id_u_2 = 		  intval(f_decode64_wd($_GET['u2']));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$file_id =	    intval(f_decode64_wd($_GET['fi']));
//$fil_size =	    intval($_GET['tf']);
//$fil_media =	  intval($_GET['md']);
//$fil_project =	intval($_GET['pj']);
$n_version =	  intval($_GET['v']);
$exchange =	    $_GET['ex'];
//
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $file_id)) $file_id = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version > 0) and ($ip != "") and ($file_id > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F142#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_sharefiles = _SHARE_FILES;
  $t_sharefiles_trash = _SHARE_FILES_TRASH;
  $t_sharefiles_exchange = _SHARE_FILES_EXCHANGE;
  $t_sharefiles_exchange_trash = _SHARE_FILES_EXCHANGE_TRASH;
  $t_sharefiles_read_only = "";
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_sharefiles = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
      $t_sharefiles_trash = f_role_permission($id_role, "SHARE_FILES_TRASH", _SHARE_FILES_TRASH);  
      $t_sharefiles_exchange = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
      $t_sharefiles_exchange_trash = f_role_permission($id_role, "SHARE_FILES_EXCHANGE_TRASH", _SHARE_FILES_EXCHANGE_TRASH);  
      $t_sharefiles_read_only = f_role_permission($id_role, "ROLE_SHARE_FILES_READ_ONLY", ""); // c'est un role, pas une option !
    }
  }
  //
  if ( ($t_sharefiles == "") or ($t_sharefiles_read_only != "") )
  {
    die(">F142#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  if ($exchange <> "")
  {
    if ($t_sharefiles_exchange == "")
    {
      die(">F142#KO#3#"); // 3: n'a pas les droits (option non activée).
    }
  }
  //
  //
  $to_trash = "";
  $deja_envoye = "";
  $requete  = " select FIL_NAME, ID_FILE, ID_PROJET ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE ID_FILE = " . $file_id . " ";
  $requete .= " and FIL_ONLINE = 'Y' ";
  if ($exchange <> "")
  {
    $requete .= " and ID_USER_AUT = " . $id_u_2 . " ";
    $requete .= " and ID_USER_DEST = " . $id_user . " "; //   <<<---- pour les échanges
  }
  else
  {
    $requete .= " and ID_USER_AUT = " . $id_user . " ";
    $requete .= " and ID_USER_DEST is null ";  //             <<<---- pour les partages
  }
  //
  $requete .= " LIMIT 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-142a]", $requete);
  if ( mysqli_num_rows($result) == 1)
  {
    list ($t_fic, $t_id_fic, $t_project) = mysqli_fetch_row ($result);
    if ($t_id_fic == $file_id)
    {
      $folder = "";
      if ($t_project > 0) 
      {
        $requete  = " select FPJ_FOLDER ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
        $requete .= " WHERE ID_PROJET = " . $t_project . " ";
        $requete .= " LIMIT 2 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-142b]", $requete);
        if ( mysqli_num_rows($result) == 1)
        {
          list ($folder) = mysqli_fetch_row ($result);
        }
        if ($folder <> "") $folder = f_encode64($folder);
      }
      //
      //
      if ($exchange <> "")
      {
        //
        // ---------- Echanges ----------
        //
        if ($t_sharefiles_exchange_trash != "")
        {
          $to_trash = "X";
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " SET FIL_ONLINE = 'T' , ";       // Trash
          $requete .= " FIL_DATE_TRASH = CURDATE() ";
          $requete .= " WHERE ID_FILE = " . $file_id . " ";
          $requete .= " and ID_USER_AUT = " . $id_u_2 . " ";
          $requete .= " and ID_USER_DEST = " . $id_user . " "; //   <<<---- pour les échanges
          $requete .= " LIMIT 1 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-142c]", $requete);
        }
        else
        {
          /*
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " SET FIL_ONLINE = 'D' , ";       // Delete
          $requete .= " FIL_DATE_TRASH = CURDATE() ";
          $requete .= " WHERE ID_FILE = " . $file_id . " ";
          $requete .= " and FIL_ONLINE = 'Y' ";
          $requete .= " and ID_USER_AUT = " . $id_u_2 . " ";
          $requete .= " and ID_USER_DEST = " . $id_user . " "; //   <<<---- pour les échanges
          $requete .= " LIMIT 1 ";
          */
          //
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
          $requete .= " WHERE ID_FILE = " . $file_id;
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-142d]", $requete);
          //
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " WHERE ID_FILE = " . $file_id . " ";
          $requete .= " and FIL_ONLINE = 'Y' ";
          $requete .= " and ID_USER_AUT = " . $id_u_2 . " ";
          $requete .= " and ID_USER_DEST = " . $id_user . " "; //   <<<---- pour les échanges
          $requete .= " LIMIT 1 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-142e]", $requete);
        }
      }
      else
      {
        //
        // ---------- Partages ----------
        //
        if ($t_sharefiles_trash != "")
        {
          $to_trash = "X";
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " SET FIL_ONLINE = 'T' , ";       // Trash
          $requete .= " FIL_DATE_TRASH = CURDATE() ";
          $requete .= " WHERE ID_FILE = " . $file_id . " ";
          $requete .= " and ID_USER_AUT = " . $id_user . " ";
          $requete .= " and ID_USER_DEST is null ";  //             <<<---- pour les partages
          $requete .= " LIMIT 1 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-142f]", $requete);
        }
        else
        {
          /*
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " SET FIL_ONLINE = 'D' , ";       // Delete
          $requete .= " FIL_DATE_TRASH = CURDATE() ";
          $requete .= " WHERE ID_FILE = " . $file_id . " ";
          $requete .= " and FIL_ONLINE = 'Y' ";
          $requete .= " and ID_USER_AUT = " . $id_user . " ";
          $requete .= " and ID_USER_DEST is null ";  //             <<<---- pour les partages
          $requete .= " LIMIT 1 ";
          */
          //
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
          $requete .= " WHERE ID_FILE = " . $file_id;
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-142g]", $requete);
          //
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " WHERE ID_FILE = " . $file_id . " ";
          $requete .= " and FIL_ONLINE = 'Y' ";
          $requete .= " and ID_USER_AUT = " . $id_user . " ";
          $requete .= " and ID_USER_DEST is null ";  //             <<<---- pour les partages
          $requete .= " LIMIT 1 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-142h]", $requete);
        }
      }
      //
      echo ">F142#OK#" . $to_trash . "#" . $folder . "###"; 
      //
      //
      //
      // on récupère le username expéditeur :
      $username_1 = f_get_username_of_id($id_user);
      //if ( ($exchange <> "") and ($id_u_2 > 0) )
      if ($exchange <> "")
      { 
        $username_2 = f_get_username_of_id($id_u_2);
        //
        if ($to_trash == "")
          write_log("log_files_exchange_deleted", $t_fic . ";" . $username_2 . ";" . $username_1 . ";");
        else
          write_log("log_files_exchange_trashed", $t_fic . ";" . $username_2 . ";" . $username_1 . ";");
      }
      else
      {
        if ($to_trash == "")
          write_log("log_files_share_deleted", $t_fic . ";" . $username_1 . ";");
        else
          write_log("log_files_share_trashed", $t_fic . ";" . $username_1 . ";");
      }
    }
    else
      echo ">F142#KO#9##";
  }
  else
    echo ">F142#KO#8##";
  //
  mysqli_close($id_connect);
}
?>
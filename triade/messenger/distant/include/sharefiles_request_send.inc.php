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
$grp_name = 		f_decode64_wd($_GET['ng']);
//$fil_date = 		f_decode64_wd($_GET['df']);
$fil_size =	    intval($_GET['tf']);
$fil_project =	intval($_GET['pj']);
$n_version =	  intval($_GET['v']);
if (isset($_GET['na'])) $fil_name_no_accents = $_GET['na']; else $fil_name_no_accents = "";
//
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
if (preg_match("#[^0-9]#", $id_grp)) $id_grp = "";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version > 0) and ($ip != "") and ($fil_name != "") and ($fil_size > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/share_files.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F140#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_sharefiles = _SHARE_FILES;
  $t_sharefiles_2 = _SHARE_FILES;
  $t_sharefiles_need_approval = _SHARE_FILES_NEED_APPROVAL;
  $t_sharefiles_exchange = _SHARE_FILES_EXCHANGE;
  $t_sharefiles_exchange_2 = _SHARE_FILES_EXCHANGE;
  $t_sharefiles_exchange_need_approval = _SHARE_FILES_EXCHANGE_NEED_APPROVAL;
  $t_sharefiles_quota_user_week = _SHARE_FILES_QUOTA_FILES_USER_WEEK;
  $t_sharefiles_max_file_size = _SHARE_FILES_MAX_FILE_SIZE;
  $t_sharefiles_max_nb_files_user = _SHARE_FILES_MAX_NB_FILES_USER;
  $t_sharefiles_max_space_size_user = _SHARE_FILES_MAX_SPACE_SIZE_USER;
  $t_sharefiles_read_only = "";
  $t_role_srv_offline_mode_2 = "";
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
      $t_sharefiles_quota_user_week = f_role_permission($id_role, "SHARE_FILES_QUOTA_FILES_USER_WEEK", _SHARE_FILES_QUOTA_FILES_USER_WEEK);
      $t_sharefiles_max_file_size = f_role_permission($id_role, "SHARE_FILES_MAX_FILE_SIZE", _SHARE_FILES_MAX_FILE_SIZE);
      $t_sharefiles_max_nb_files_user = f_role_permission($id_role, "SHARE_FILES_MAX_NB_FILES_USER", _SHARE_FILES_MAX_NB_FILES_USER);
      $t_sharefiles_max_space_size_user = f_role_permission($id_role, "SHARE_FILES_MAX_SPACE_SIZE_USER", _SHARE_FILES_MAX_SPACE_SIZE_USER);
      $t_sharefiles_read_only = f_role_permission($id_role, "ROLE_SHARE_FILES_READ_ONLY", ""); // c'est un role, pas une option !
    }
    //
    if ($id_u_2 > 0)
    {
      $id_role = f_role_of_user($id_u_2);
      if ($id_role > 0)
      {
        $t_sharefiles_2 = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
        $t_sharefiles_exchange_2 = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
        $t_role_srv_offline_mode_2 = f_role_permission($id_role, "ROLE_OFFLINE_MODE", ""); // c'est un role, pas une option !
      }
    }
  }
  //
  if ( ($t_sharefiles == "") or ($t_sharefiles_read_only != "") )
  {
    die(">F140#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  if ($id_u_2 > 0)
  {
    if ($t_sharefiles_exchange == "")
    {
      die(">F140#KO#5#"); // 5: n'a pas les droits (option non activée).
    }
    //
    // Droits du destinataire :
    if ( ($t_role_srv_offline_mode_2 != "") or ($t_sharefiles_2 == "") or ($t_sharefiles_exchange_2 == "") )
    {
      die(">F140#KO#8#"); // 8: Destinataire n'a pas les droits (option non activée).   ou forcé en offline
    }
  }
  //
  $username_1 = f_get_username_of_id($id_user);
  $username_2 = "";
  if ($id_u_2 > 0) $username_2 = f_get_username_of_id($id_u_2);
  //
  //
  $fil_size_mo = ($fil_size / 1024);
  if (intval($t_sharefiles_max_file_size) > 0)
  {
    if ($fil_size > intval($t_sharefiles_max_file_size))
    {
      echo ">F140#KO#3#A#" . $fil_size . "#" . $t_sharefiles_max_file_size . "#"; // 3a: To big
      write_log("log_files_share_cannot_3a", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $fil_size  . ";" . $t_sharefiles_max_file_size . ";");
      die();
    }
  }
  //
  if (intval($t_sharefiles_max_nb_files_user) > 0)
  {
    $requete  = " select COUNT(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user . " ";
    $requete .= " and FIL_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140a]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_nb_file_user) = mysqli_fetch_row ($result);
      if ( intval($t_nb_file_user) >= intval($t_sharefiles_max_nb_files_user) )
      {
        echo ">F140#KO#4#A#" . $t_nb_file_user . "#" . $t_sharefiles_max_nb_files_user . "#"; // 4A: too much files for this user
        write_log("log_files_share_cannot_4a", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $t_nb_file_user  . ";" . $t_sharefiles_max_nb_files_user . ";");
        die();
      }
    }
  }
  //
  if (intval(_SHARE_FILES_MAX_NB_FILES_TOTAL) > 0)
  {
    $requete  = " select SQL_CACHE COUNT(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140b]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_nb_file) = mysqli_fetch_row ($result);
      if ( intval($t_nb_file) >= intval(_SHARE_FILES_MAX_NB_FILES_TOTAL) )
      {
        echo ">F140#KO#4#B#"; // 4b: too much files
        write_log("log_files_share_cannot_4b", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $t_nb_file  . ";");
        die();
      }
    }
  }
  //
  /*
  if (intval(_SHARE_FILES_QUOTA_FILES_USER_WEEK) > 0)    NON, voir plus bas !
  {
    $requete  = " select COUNT(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user . " ";
    $requete .= " and FIL_ONLINE <> '' ";
    $requete .= " and WEEK(FIL_DATE_ADD) = WEEK(CURDATE()) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140c]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_nb_file_user) = mysqli_fetch_row ($result);
      if ( intval($t_nb_file_user) >= intval(_SHARE_FILES_QUOTA_FILES_USER_WEEK) )
      {
        die(">F140#KO#4#C#"); // 4C : too much files for this user this week
      }
    }
  }
  */
  if (intval($t_sharefiles_quota_user_week) > 0)
  {
    $requete  = " select FST_NB_LAST_WEEK";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $requete .= " and FST_LAST_WEEK = WEEK(CURDATE()) ";
    $requete .= " and TIMESTAMPDIFF(WEEK, FST_LAST_DATE, CURDATE() ) = 0 ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140c]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_week) = mysqli_fetch_row ($result);
      if ($nb_week >= intval($t_sharefiles_quota_user_week) )
      {
        echo ">F140#KO#4#C#" . $nb_week . "#" . $t_sharefiles_quota_user_week . "#"; // 4C : too much files for this user this week (Over quota)
        write_log("log_files_share_cannot_4c", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $nb_week  . ";" . $t_sharefiles_quota_user_week . ";");
        die();
      }
    }
  }
  //
  if (intval($t_sharefiles_max_space_size_user) > 0)
  {
    $requete  = " select SUM(FIL_SIZE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_size_ko) = mysqli_fetch_row ($result);
      if ( (($t_size_ko / 1024) + $fil_size_mo) >= intval($t_sharefiles_max_space_size_user) )
      {
        echo ">F140#KO#3#B#" . $fil_size_mo . "#" . $t_sharefiles_max_space_size_user . "#"; // 3b : Over quota user
        write_log("log_files_share_cannot_3b", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $fil_size_mo  . ";" . $t_sharefiles_max_space_size_user . ";");
        die();
      }
    }
  }
  //
  if (intval(_SHARE_FILES_MAX_SPACE_SIZE_TOTAL) > 0)
  {
    $requete  = " select SQL_CACHE SUM(FIL_SIZE) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE <> '' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140e]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      list ($t_size_ko) = mysqli_fetch_row ($result);
      if ( (($t_size_ko / 1024) + $fil_size_mo) >= intval(_SHARE_FILES_MAX_SPACE_SIZE_TOTAL) )
      {
        echo ">F140#KO#3#C#"; // 3c : Over quota total
        write_log("log_files_share_cannot_3c", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $fil_size_mo  . ";");
        die();
      }
    }
  }
  //
  if ( ( ($t_sharefiles_need_approval != "") and ($id_u_2 <= 0) )  or  ( ($t_sharefiles_exchange_need_approval != "") and ($id_u_2 > 0) ) )
  {
    if (intval(_SHARE_FILES_APPROVAL_QUEUE) > 0)
    {
      $requete  = " select COUNT(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
      $requete .= " WHERE FIL_ONLINE = 'W' "; // attente approbation
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-140f]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        list ($t_nb_file) = mysqli_fetch_row ($result);
        if ( intval($t_nb_file) >= intval(_SHARE_FILES_APPROVAL_QUEUE) )
        {
          echo ">F140#KO#3#D#"; // 3d: too much pending files
          write_log("log_files_share_cannot_3d", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $t_nb_file  . ";");
          die();
        }
      }
    }
  }
  //
  //
  if ($id_u_2 > 0)
  {
    $requete  = " select USR_VERSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $id_u_2 . " ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140n]", $requete);
    if ( mysqli_num_rows($result) == 1 ) 
    {
      list ($version_dest) = mysqli_fetch_row ($result);
      if ( (substr($version_dest, 0, 3) == "1.2") and (substr($version_dest, 0, 4) != "1.24") and (substr($version_dest, 0, 4) != "1.25") and (substr($version_dest, 0, 4) != "1.26") )
        die(">F140#KO#8#A#"); // 8: destinataire n'est pas équipé
    }
    else
      die(">F140#KO#6#"); // 6: destinataire n'existe pas
    //
    //
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
    {
      $id_role_dest = f_role_of_user($id_u_2);
      //
      if ($id_role_dest > 0)
      {
        $t_sharefiles_dest = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
        //$t_sharefiles_need_approval_dest = f_role_permission($id_role_dest, "SHARE_FILES_NEED_APPROVAL", _SHARE_FILES_NEED_APPROVAL);
        //if ($t_sharefiles_need_approval_dest == "")
        if ($t_sharefiles_dest == "")
          die(">F140#KO#8#B#"); // 8: destinataire n'a pas le droit.
      }
    }
  }
  //
  //
  $real_grp_id = 0;
  if ($id_grp  > 0) // par défaut à 1 si envoi à un groupe.
  {
    if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
    {
      $requete  = " SELECT GRP.ID_GROUP ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
      $requete .= " WHERE USG.ID_GROUP = GRP.ID_GROUP ";
      //$requete .= " and GRP.ID_GROUP = " . $id_grp;
      $requete .= " and GRP.GRP_NAME = '" . $grp_name . "' ";
      $requete .= " and USG.ID_USER = " . $id_user;
      $requete .= " limit 2 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-140g]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($real_grp_id) = mysqli_fetch_row ($result);
        $real_grp_id = intval($real_grp_id);
      }
    }
    //if ($id_grp <> $real_grp_id) 
    //  die(">F140#KO#5#"); // 5: Groupes non activés, ou auteur non membre du groupe destinataire.
  }
  //
  //
  // Purge des anciennes tentatives d'envoies non abouties :
  $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE FIL_ONLINE = '' ";
  $requete .= " and FIL_DATE_ADD <> CURDATE() ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-140h]", $requete);
  //
  //
  $fil_name = trim($fil_name);
  $fil_name = str_replace("'", "", $fil_name);
  $fil_name = str_replace("~", "", $fil_name);
  $fil_name = str_replace("`", "", $fil_name);
  $fil_name = str_replace(chr(34), "", $fil_name);
  //$fil_name = f_DelSpecialChar($fil_name);
  //
  $folder = f_share_files_projet_folder($fil_project);
  if ($folder <> "") $folder = f_encode64($folder);
  //
  // Vérifier si le fichier n'existe pas déja :
  $deja_envoye = "";
  $requete  = " select ID_USER_AUT, FIL_ONLINE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE FIL_NAME = '" . $fil_name . "' ";
  if ($fil_project > 0)
    $requete .= " and ID_PROJET = " . $fil_project . " ";
  else
    $requete .= " and ID_PROJET is null ";
  //
  $requete .= " LIMIT 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-140j]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    list ($deja_envoye, $online) = mysqli_fetch_row ($result);
    if ( ($online == "") and (intval($deja_envoye) == $id_user) )
    {
      $deja_envoye = "X"; // pas d'insert into
      echo ">F140#OK#ALREADY_TRY#" . $folder . "#" . $real_grp_id . "###"; 
    }
    else
      echo ">F140#KO#9#" . $deja_envoye . "#";
  }
  //
  //
  $ext_file = strrchr($fil_name, '.');
  $id_media = 0;
  $requete  = " select SQL_CACHE ID_FILEMEDIA, FMD_EXTENSIONS ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA ";
  $requete .= " order by FMD_NAME ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-140k]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($t_id_media, $extensions) = mysqli_fetch_row ($result) )
    {
      if ( (substr_count($extensions, $ext_file . ".") > 0) )  
      {
        $id_media = $t_id_media;
        break;
      }
    }
  }
  $id_media = intval($id_media);
  if ($id_media <= 0) 
  {
    echo ">F140#KO#7#"; // 7: Unknow media (extension)
    write_log("log_files_share_cannot_7", $username_1 . ";" . $username_2 . ";" . $fil_name . ";" . $ext_file  . ";");
    die();
  }
  //
  //
  // AUTORISATION par AJOUT dans la table IM_FIL_FILE (avec FIL_ONLINE à vide) :
  if ($deja_envoye == "")
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " ( FIL_NAME, FIL_HASH, FIL_SIZE, ID_USER_AUT, ID_USER_DEST, ID_GROUP_DEST, ID_PROJET, ID_FILEMEDIA, FIL_DATE_ADD, FIL_ONLINE ) ";
    $requete .= " VALUES ('" . $fil_name . "', '" . $fil_hash . "', " . $fil_size . ", " . $id_user . ", "; 
    if ($id_u_2 > 0)
      $requete .= $id_u_2 . ", ";
    else
      $requete .= "null, ";
    //
    if ( ($real_grp_id > 0) and ($id_u_2 <= 0) )
      $requete .= $real_grp_id . ", ";
    else
      $requete .= "null, ";
    //
    if ($fil_project > 0)
      $requete .= $fil_project . ", ";
    else
      $requete .= "null, ";
    //
    $requete .= $id_media . ", ";
    $requete .= " CURDATE(), '' )";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-140m]", $requete);
    //
    echo ">F140#OK#NEW#" . $folder . "#" . $real_grp_id . "###";
  }
  //
  mysqli_close($id_connect);
}
?>
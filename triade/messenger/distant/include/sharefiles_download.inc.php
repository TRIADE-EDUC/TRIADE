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
//$id_grp =       intval(f_decode64_wd($_GET['ig']));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$file_id =	    intval($_GET['fi']);
$fil_size =	    intval($_GET['tf']);
$n_version =	  intval($_GET['v']);
$exchange =	    trim($_GET['ex']);
//
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $file_id)) $file_id = "";
//
if ( ($id_user > 0) and ($n_version > 0) and ($ip != "") and ($file_id > 0) and ($fil_size > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/share_files.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F143#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_sharefiles = _SHARE_FILES;
  $t_sharefiles_exchange = _SHARE_FILES_EXCHANGE;
  $t_sharefiles_download_quota_file_day = _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY;
  $t_sharefiles_download_quota_file_week = _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK;
  $t_sharefiles_download_quota_file_month = _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH;
  $t_sharefiles_download_quota_mb_day =  _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY;
  $t_sharefiles_download_quota_mb_week = _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK;
  $t_sharefiles_download_quota_mb_month = _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_sharefiles = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
      $t_sharefiles_exchange = f_role_permission($id_role, "SHARE_FILES_EXCHANGE", _SHARE_FILES_EXCHANGE);
      $t_sharefiles_download_quota_file_day = f_role_permission($id_role, "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY", _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY);
      $t_sharefiles_download_quota_file_week = f_role_permission($id_role, "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK", _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK);
      $t_sharefiles_download_quota_file_month = f_role_permission($id_role, "SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH", _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH);
      $t_sharefiles_download_quota_mb_day = f_role_permission($id_role, "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY", _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY);
      $t_sharefiles_download_quota_mb_week = f_role_permission($id_role, "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK", _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK);
      $t_sharefiles_download_quota_mb_month = f_role_permission($id_role, "SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH", _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH);
    }
  }
  //
  if ($t_sharefiles == "")
  {
    die(">F143#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  if ($exchange <> "")
  {
    if ($t_sharefiles_exchange == "")
    {
      die(">F143#KO#3#"); // 3: n'a pas les droits (option non activée).
    }
  }
  //
  //
  if (intval($t_sharefiles_download_quota_file_day) <= 1) $t_sharefiles_download_quota_file_day = 0;
  if (intval($t_sharefiles_download_quota_file_week) <= 1) $t_sharefiles_download_quota_file_week = 0;
  if (intval($t_sharefiles_download_quota_file_month) <= 1) $t_sharefiles_download_quota_file_month = 0;
  if (intval($t_sharefiles_download_quota_mb_day) <= 1) $t_sharefiles_download_quota_mb_day = 0;
  if (intval($t_sharefiles_download_quota_mb_week) <= 1) $t_sharefiles_download_quota_mb_week = 0;
  if (intval($t_sharefiles_download_quota_mb_month) <= 1) $t_sharefiles_download_quota_mb_month = 0;
  //
  $deja_envoye = "";
  $requete  = " select FIL_NAME, ID_FILE, ID_PROJET, FIL_HASH, FIL_COMPRESS, FIL_PROTECT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
  $requete .= " WHERE ID_FILE = " . $file_id . " ";
  $requete .= " and FIL_ONLINE = 'Y' ";
  $requete .= " and FIL_SIZE = " . $fil_size . " ";
  if ($exchange <> "")
  {
    $requete .= " and ID_USER_AUT = " . $id_u_2 . " ";
    $requete .= " and ID_USER_DEST = " . $id_user . " "; //   <<<---- pour les échanges
  }
  else
    $requete .= " and ID_USER_DEST is null ";  //             <<<---- pour les partages
  //
  $requete .= " LIMIT 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-143a]", $requete);
  if ( mysqli_num_rows($result) == 1)
  {
    list ($t_fic, $t_id_fic, $t_project, $t_hash, $t_compress, $t_protect) = mysqli_fetch_row ($result);
    if ($t_id_fic == $file_id)
    {
      $t_delete_after = "";
      if ($exchange <> "") $t_delete_after = "X";
      //
      //
      $have_quota_download = "";
      if ($exchange == "")
      {
        if ( ($t_sharefiles_download_quota_file_day > 0) or ($t_sharefiles_download_quota_file_week > 0) or ($t_sharefiles_download_quota_file_month > 0) 
          or ($t_sharefiles_download_quota_mb_day > 0) or ($t_sharefiles_download_quota_mb_week > 0) or ($t_sharefiles_download_quota_mb_month > 0) )
        {
          $have_quota_download = "X";
          $fil_size_mb = ($fil_size / 1024);
          $traffic_last_date_mb = 0;
          $traffic_last_week_mb = 0;
          $traffic_last_month_mb = 0;
          $requete  = " SELECT FSD_LAST_DATE, FSD_NB_LAST_DATE, FSD_TRAFFIC_LAST_DATE, ";
          $requete .= " WEEK(CURDATE()), FSD_LAST_WEEK, FSD_NB_LAST_WEEK, FSD_TRAFFIC_LAST_WEEK, ";
          $requete .= " MONTH(CURDATE()), FSD_LAST_MONTH, FSD_NB_LAST_MONTH, FSD_TRAFFIC_LAST_MONTH ";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD ";
          $requete .= " WHERE ID_USER_DL = " . $id_user;
          $requete .= " LIMIT 2 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-143d]", $requete);
          if ( mysqli_num_rows($result) == 1 ) 
          {
            list($last_date, $nb_last_date, $traffic_last_date,
              $this_week, $last_week, $nb_last_week, $traffic_last_week, 
              $this_month, $last_month, $nb_last_month, $traffic_last_month) = mysqli_fetch_row ($result);
            //
            $over_quota = "";
            if ($traffic_last_date > 0) $traffic_last_date_mb = ($traffic_last_date / 1024);
            if ($traffic_last_week > 0) $traffic_last_week_mb = ($traffic_last_date / 1024);
            if ($traffic_last_month > 0) $traffic_last_month_mb = ($traffic_last_date / 1024);
            $last_date = date("Ymd", strtotime($last_date));
            if ($last_date == date("Ymd"))
            {
              if ( ($t_sharefiles_download_quota_file_day > 0) and ($nb_last_date >= $t_sharefiles_download_quota_file_day) ) $over_quota = "D#N";
              if ( ($t_sharefiles_download_quota_mb_day > 0) and (($traffic_last_date_mb + $fil_size_mb) > $t_sharefiles_download_quota_mb_day) ) $over_quota = "D#S";
            }
            if ($last_week == $this_week)
            {
              if ( ($t_sharefiles_download_quota_file_week > 0) and ($nb_last_week >= $t_sharefiles_download_quota_file_week) ) $over_quota = "W#N";
              if ( ($t_sharefiles_download_quota_mb_week > 0) and (($traffic_last_week_mb + $fil_size_mb) > $t_sharefiles_download_quota_mb_week) ) $over_quota = "W#S";
            }
            if ($last_month == $this_month)
            {
              if ( ($t_sharefiles_download_quota_file_month > 0) and ($nb_last_month >= $t_sharefiles_download_quota_file_month) ) $over_quota = "M#N";
              if ( ($t_sharefiles_download_quota_mb_month > 0) and (($traffic_last_month_mb + $fil_size_mb) > $t_sharefiles_download_quota_mb_month) ) $over_quota = "M#S";
            }
            //
            if ($over_quota != "")
            {
               die(">F143#KO#4#" . $over_quota . "##"); // 4 : overquota
            }
          }
        }
      }
      //
      //
      $folder = f_share_files_projet_folder($t_project);
      if ($folder <> "") $folder = f_encode64($folder);
      //
      echo ">F143#OK#" . $t_delete_after . "#" . f_encode64($t_fic) . "#" . f_encode64($t_hash) . "#" . $folder . "#" . $t_compress . "#" . $t_protect . "###"; // . $file_id; 
      //
      //
      // On incrémente le compteur de téléchargements
      if ($t_delete_after == "")
      {
        $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
        $requete .= " SET FIL_NB_DOWNLOAD = (FIL_NB_DOWNLOAD + 1) , "; 
        $requete .= " ID_USER_LAST_DL = " . $id_user . " ";
        $requete .= " WHERE ID_FILE = " . $file_id . " ";
        $requete .= " and FIL_ONLINE = 'Y' ";
        $requete .= " and ID_USER_AUT <> " . $id_user . " "; // on ne compte pas si c'est l'auteur qui télécharger.
        $requete .= " and (ID_USER_LAST_DL <> " . $id_user . " or ID_USER_LAST_DL is null) "; // pour limiter la triche abusive.
        $requete .= " limit 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-143c]", $requete);
        //
        //
        stats_sharefile_download_inc($id_user, $file_id, $fil_size);
      }
    }
    else
      echo ">F143#KO#9##";
  }
  else
    echo ">F143#KO#8##";
  //
  mysqli_close($id_connect);
}
?>
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
if (isset($_POST['id_file'])) $id_file = intval($_POST['id_file']);  else $id_file = 0;
//if (isset($_POST['f_name'])) $f_name = base64_decode($_POST['f_name']); else $f_name = "";
//if (isset($_POST['f_hash'])) $f_hash = base64_decode($_POST['f_hash']); else $f_hash = "";
if (isset($_POST['page'])) $page = intval($_POST['page']);  else $page = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['tri'])) $tri = $_POST['tri']; else $tri = "";
if (isset($_POST['source'])) $source = $_POST['source']; else $source = "";
if (isset($_POST['stats'])) $stats = $_POST['stats']; else $stats = "";
if (isset($_POST['id_user_only'])) $id_user_only = intval($_POST['id_user_only']); else $id_user_only = "";
if (isset($_POST['id_media_only'])) $id_media_only = intval($_POST['id_media_only']); else $id_media_only = "";
if (isset($_POST['id_project_only'])) $id_project_only = intval($_POST['id_project_only']); else $id_project_only = "";
//
//
function f_supprime_fichier_server($file_to_delete, $folder)
{
  $ret = false;
  //
  if ( (_SHARE_FILES != "") and (_SHARE_FILES_FTP_ADDRESS != "") and (_SHARE_FILES_FTP_LOGIN != "") and (_SHARE_FILES_FTP_PASSWORD_CRYPT != "") )
  {
    // FTP on another server :
    if ( (_SHARE_FILES_FTP_PASSWORD != "") and (_SHARE_FILES_FOLDER == "") )
    {
      if ($folder != "") $folder .= "/";
      $port_num = intval(_SHARE_FILES_FTP_PORT_NUMBER);
      if ( ($port_num <= 0) or ($port_num > 65535) ) $port_num = 21;
      $conn_id = ftp_connect(_SHARE_FILES_FTP_ADDRESS, $port_num) or die("<span class='error'>Couldn't connect to FTP server!</span>"); 
      if (@ftp_login($conn_id, _SHARE_FILES_FTP_LOGIN, _SHARE_FILES_FTP_PASSWORD)) 
      {
        if ((ftp_size($conn_id, $folder . $file_to_delete)) > 0)
        {
          if (ftp_delete($conn_id, $folder . $file_to_delete)) 
            $ret = true;
          else
            echo "Bug supprime !";
        }
        else
          $ret = true; // n'existe plus, donc on considère supprimé (afin de le supprimer de la base ensuite).
      }
      ftp_close($conn_id);
    }
    // ELSE (FTP on this webserver) :
    if ( (_SHARE_FILES_FOLDER != "") and (_SHARE_FILES_FTP_PASSWORD == "") )
    {
      if ($folder != "") $folder .= "/";
      if (is_readable(_SHARE_FILES_FOLDER . $folder . $file_to_delete)) 
      {
        if (is_writeable(_SHARE_FILES_FOLDER . $folder . $file_to_delete))
        {
          if (unlink(_SHARE_FILES_FOLDER . $folder . $file_to_delete)) $ret = true;
        }
      }
      else
        $ret = true; // n'existe plus, donc on considère supprimé (afin de le supprimer de la base ensuite).
    }
  }
  //
  return $ret;
}
//
$fic_dest = "list_files_sharing";
if ($source == "share_files_alert") $fic_dest = "list_files_sharing_alert";
if ($source == "share_files_trash") $fic_dest = "list_files_sharing_trash";
if ($source == "share_files_exch_trash") $fic_dest = "list_files_exchanging_trash";
if ($source == "share_files_pending") $fic_dest = "list_files_sharing_pending";
if ($source == "share_files_exch_pending") $fic_dest = "list_files_exchanging_pending";
$url = $fic_dest . ".php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&id_user_only=" . $id_user_only . "&id_media_only=" . $id_media_only . "&id_project_only=" . $id_project_only . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_file > 0) and (!preg_match("#[^0-9]#", $id_file)) ) // and ($fil_name != "") and ($fil_hash != "") )
  {
    define('INTRAMESSENGER',true);
    //
    require ("../common/sql.inc.php");
    require ("../common/config/config.inc.php");
    require ("../common/functions.inc.php");
    require ("../common/share_files.inc.php");
    //
    $requete  = " select ID_FILE, FIL_NAME, ID_USER_AUT, ID_PROJET ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE ID_FILE = " . $id_file;
    //$requete .= " and FIL_NAME = '" . $f_name . "' ";
    //$requete .= " and FIL_HASH = '" . $f_hash . "' ";
    $requete .= " and FIL_ONLINE in ('Y', 'A', 'T', 'W', 'Z') ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N1d]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($file_id, $fil_name, $id_aut, $id_project) = mysqli_fetch_row ($result);
      //
      if ($id_file == $file_id)
      {
        $folder = f_share_files_projet_folder($id_project);
        if (f_supprime_fichier_server($fil_name, $folder))
        {
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
          $requete .= " WHERE ID_FILE = " . $id_file;
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-N1e]", $requete);
          //
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " WHERE ID_FILE = " . $id_file;
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-N1f]", $requete);
        }
        else
        {
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " SET FIL_ONLINE = 'D' ";
          //$requete .= " FIL_DATE_TRASH = CURDATE() ";
          $requete .= " WHERE ID_FILE = " . $id_file;
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-N1g]", $requete);
        }
        //
        //
        if ($stats == "inc_nb_reject")
        {
          // only if : file pending, or after alert.
          stats_sharefile_add_alert_reject($id_aut, "R");
        }
        //
        //
        $user = f_get_username_of_id($id_aut);
        write_log("log_files_delete", $fil_name . ";" . $file_id . ";" . $user);
      }
    }
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
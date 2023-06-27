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
if (isset($_POST['page'])) $page = intval($_POST['page']);  else $page = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['tri'])) $tri = $_POST['tri']; else $tri = "";
if (isset($_POST['id_user_only'])) $id_user_only = intval($_POST['id_user_only']); else $id_user_only = "";
//
//
function f_delete_file_from_server($file_to_delete)
{
  $ret = false;
  //
  if ( (_BACKUP_FILES != "") and (_BACKUP_FILES_FTP_ADDRESS != "") and (_BACKUP_FILES_FTP_LOGIN != "") and (_BACKUP_FILES_FTP_PASSWORD_CRYPT != "") )
  {
    // FTP on another server :
    if ( (_BACKUP_FILES_FTP_PASSWORD != "") and (_BACKUP_FILES_FOLDER == "") )
    {
      $folder = "/";
      $port_num = intval(_BACKUP_FILES_FTP_PORT_NUMBER);
      if ( ($port_num <= 0) or ($port_num > 65535) ) $port_num = 21;
      $conn_id = ftp_connect(_BACKUP_FILES_FTP_ADDRESS, $port_num) or die("<span class='error'>Couldn't connect to FTP server!</span>"); 
      if (@ftp_login($conn_id, _BACKUP_FILES_FTP_LOGIN, _BACKUP_FILES_FTP_PASSWORD)) 
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
    if ( (_BACKUP_FILES_FOLDER != "") and (_BACKUP_FILES_FTP_PASSWORD == "") )
    {
      if ($folder != "") $folder .= "/";
      if (is_readable(_BACKUP_FILES_FOLDER . $folder . $file_to_delete)) 
      {
        if (is_writeable(_BACKUP_FILES_FOLDER . $folder . $file_to_delete))
        {
          if (unlink(_BACKUP_FILES_FOLDER . $folder . $file_to_delete)) $ret = true;
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
//
$url = "list_files_backup.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&id_user_only=" . $id_user_only . "&";
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
    //
    $requete  = " select ID_FILEBACKUP, FIB_NAME, ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
    $requete .= " WHERE ID_FILEBACKUP = " . $id_file;
    $requete .= " and FIB_ONLINE <> '' ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N1d]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($file_id, $fil_name, $id_aut) = mysqli_fetch_row ($result);
      //
      if ($id_file == $file_id)
      {
        if (f_delete_file_from_server($fil_name))
        {
          $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
          $requete .= " WHERE ID_FILEBACKUP = " . $id_file;
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-N1f]", $requete);
        }
        else
        {
          $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ";
          $requete .= " SET FIB_ONLINE = 'D' ";
          //$requete .= " FIL_DATE_TRASH = CURDATE() ";
          $requete .= " WHERE ID_FILEBACKUP = " . $id_file;
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-N1g]", $requete);
        }
        //
        //
        //
        //
        $user = f_get_username_of_id($id_aut);
        write_log("log_backup_delete", $fil_name . ";" . $file_id . ";" . $user);
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
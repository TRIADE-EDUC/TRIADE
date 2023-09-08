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
if (isset($_GET['id_file'])) $id_file = intval($_GET['id_file']);  else $id_file = 0;
//if (isset($_GET['file'])) $file = $_GET['file']; else $file = "";
//
if ( ($id_file > 0) and (!preg_match("#[^0-9]#", $id_file)) ) // and ($fil_name != "") and ($fil_hash != "") )
{
  define('INTRAMESSENGER',true);
  //
  require ("../common/sql.inc.php");
  require ("../common/functions.inc.php");
  require ("../common/config/config.inc.php");
  require ("../common/share_files.inc.php");
  //
  //
  if (_SHARE_FILES != '')
  {
    $requete  = " select ID_FILE, FIL_NAME, ID_PROJET ";
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
      list ($file_id, $file, $id_project) = mysqli_fetch_row ($result);
      //
      if ($id_file == $file_id)
      {
        $folder = f_share_files_projet_folder($id_project);
        //
        // FTP on this webserver :
        if ( (_SHARE_FILES_FOLDER != "") and (_SHARE_FILES_FTP_PASSWORD == '') )
        {
          if ($folder != "") $folder .= "/";
          header("Content-disposition: attachment; filename=" . $file);
          header("Content-type: application/octet-stream" );
          readfile(_SHARE_FILES_FOLDER . $folder . $file);
        }
        // Else FTP on another server :
        if ( (_SHARE_FILES_FTP_PASSWORD != "") and (_SHARE_FILES_FOLDER == '') )
        {
          $ok = false;
          if ($folder != "") $folder .= "/";
          $local_tmp_folder = "../share/files/";
          $port_num = intval(_SHARE_FILES_FTP_PORT_NUMBER);
          if ( ($port_num <= 0) or ($port_num > 65535) ) $port_num = 21;
          //
          $conn_id = ftp_connect(_SHARE_FILES_FTP_ADDRESS, $port_num) or die("Couldn't connect to server!"); 
          if (@ftp_login($conn_id, _SHARE_FILES_FTP_LOGIN, _SHARE_FILES_FTP_PASSWORD)) 
          {
            if (ftp_get($conn_id, $local_tmp_folder . $file, $folder . $file, FTP_BINARY)) $ok = true;
          }
          else 
          {
            echo "<font color='red'>" . "Failed to connect as " . _SHARE_FILES_FTP_LOGIN . "</font><br/>";
            echo "<br/>";
            echo "<A HREF='files_sharing_ftp_test.php'>Test FTP access</A>";
          }
          //
          ftp_close($conn_id);
          //
          if ($ok == true)
          {
            sleep(1);
            header("Content-disposition: attachment; filename=" . $file);
            header("Content-type: application/octet-stream" );
            readfile($local_tmp_folder . $file);
            //
            sleep(1);
            unlink($local_tmp_folder . $file);
          }
        }
      }
    }
  }
}
?>
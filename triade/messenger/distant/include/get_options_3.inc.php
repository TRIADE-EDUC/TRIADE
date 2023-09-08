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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$n_version = 	  intval($_GET['v']);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version >= 43) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F08#KO#1#"); // 1:session non ouverte.
  //
  mysqli_close($id_connect);
  //
  //
  $t_sharefile_ftp_address = "";
  $t_sharefile_ftp_login = "";
  $t_sharefile_ftp_pass = "";
  $t_sharefile_ftp_port = "";
  $t_backup_ftp_address = "";
  $t_backup_ftp_login = "";
  $t_backup_ftp_pass = "";
  $t_backup_ftp_port = "";
  $t_backup_files_only_this_local_folder = "";
  $t_admin_email =   f_encode64(_ADMIN_EMAIL);
  $t_admin_phone =   f_encode64(_ADMIN_PHONE);
  $t_email_server =  f_encode64(_INCOMING_EMAIL_SERVER_ADDRESS);
  $t_chang_p_url =   f_encode64(_EXTERN_URL_CHANGE_PASSWORD);
  if (_SHARE_FILES != "")
  {
    $t_sharefile_ftp_address =  f_encode64(_SHARE_FILES_FTP_ADDRESS);
    $t_sharefile_ftp_login =    f_encode64(_SHARE_FILES_FTP_LOGIN);
    $t_sharefile_ftp_pass =     _SHARE_FILES_FTP_PASSWORD_CRYPT;
    $t_sharefile_ftp_port =     f_encode64(_SHARE_FILES_FTP_PORT_NUMBER);
  }
  if (_BACKUP_FILES != "")
  {
    $t_backup_ftp_address =  f_encode64(_BACKUP_FILES_FTP_ADDRESS);
    $t_backup_ftp_login =    f_encode64(_BACKUP_FILES_FTP_LOGIN);
    $t_backup_ftp_pass =     _BACKUP_FILES_FTP_PASSWORD_CRYPT;
    $t_backup_ftp_port =     f_encode64(_BACKUP_FILES_FTP_PORT_NUMBER);
    $t_backup_files_only_this_local_folder = f_encode64(_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY);
  }
  //
  echo ">F08#OK#". $t_admin_email . "#" . $t_admin_phone . "#";
  echo $t_email_server . "#" . $t_chang_p_url . "#";
  echo $t_sharefile_ftp_address . "#" . $t_sharefile_ftp_login . "#" . $t_sharefile_ftp_pass . "#" . $t_sharefile_ftp_port . "#";
  echo $t_backup_ftp_address . "#" . $t_backup_ftp_login . "#" . $t_backup_ftp_pass . "#" . $t_backup_ftp_port . "#" . $t_backup_files_only_this_local_folder . "#";
  echo "#############";
}
?>
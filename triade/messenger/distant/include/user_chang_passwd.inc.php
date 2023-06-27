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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) or (!isset($_GET['pa'])) or (!isset($_GET['pn'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$pass_anc =		f_decode64_wd($_GET['pa']);
$pass_new =		f_decode64_wd($_GET['pn']);
$version =    intval($_GET['v']);
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($pass_new != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/config/auth.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die (">F60#KO#Session KO.#");
  //
  //
  $requete  = " select USR_PASSWORD FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE ID_USER = " . $id_user . " ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-70a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($usr_pass) = mysqli_fetch_row ($result);
    //
    $passcr = sha1($password_pepper . $pass_anc . "W$*7B0-c6");
    if ($usr_pass == $passcr)
    {
      $ret = f_update_pass_user($id_user, $pass_new);
      echo ">F60#". $ret . "##";
    }
    else
      echo ">F60#KO#OLD#";
  }
  //
  mysqli_close($id_connect);
}
?>
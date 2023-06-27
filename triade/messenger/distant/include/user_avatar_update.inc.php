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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) or (!isset($_GET['av'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$ip = 			    f_decode64_wd($_GET['ip']);
$version =	    intval($_GET['v']);
$avatar = 		  f_decode64_wd($_GET['av']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die ("Session KO.");
  //
  //
  $t_allow_change_avatar = _ALLOW_CHANGE_AVATAR;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_change_avatar = f_role_permission($id_role, "ALLOW_CHANGE_AVATAR", _ALLOW_CHANGE_AVATAR);
    }
  }
  //
  //
  $ext = strtolower(substr($avatar,-5));
  if ( ($t_allow_change_avatar != "") and (strlen($avatar) <= 20) and ( (strpos($ext, ".png")) or (strpos($ext, ".gif")) or (strpos($ext, ".jpg")) or (strpos($ext, ".jpeg")) ) )
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
    $requete .= " set USR_AVATAR = '" . $avatar . "' ";
    $requete .= " WHERE ID_USER = " . $id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-74a]", $requete);
    //
    echo ">F69#OK###";
  }
  else
    echo ">F69#KO##";
  //
  mysqli_close($id_connect);
}
?>
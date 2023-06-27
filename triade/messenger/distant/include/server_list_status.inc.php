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
$ip = 			    f_decode64_wd($_GET['ip']);
$version =	    intval($_GET['v']);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  //
  $list_status = "";
  $t_srv_list_status = _SERVERS_STATUS;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_srv_list_status = f_role_permission($id_role, "SERVERS_STATUS", _SERVERS_STATUS);
    }
  }
  //
  if ($t_srv_list_status != "")
  {
    $list_status = f_servers_status();
  }
  mysqli_close($id_connect);
  //
  //
  echo ">F88#" . f_encode64($list_status) . "###|"; // séparateur de ligne : '|' (pipe).
}
?>
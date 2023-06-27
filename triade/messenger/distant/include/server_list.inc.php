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
if ( (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version = 	intval($_GET['v']);
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($n_version > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  //
  //
  $t_srv_list_status = _SERVERS_STATUS;
  if ( (_ROLES_TO_OVERRIDE_PERMISSIONS != "") and ($id_user > 0) )    // TESTER les 2 !  (car id_user rajouté récemment 10/07/2011)
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
  if ($t_srv_list_status == "")
  {
    die(">F89#KO#2#"); // 2: Not allowed (option not activated)
  }
  //
  $requete  = " SELECT ID_SERVER, SRV_NAME ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
  $requete .= " ORDER BY UPPER(SRV_NAME) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-80a]", $requete);
  $nb_lig = mysqli_num_rows($result);
  if ( $nb_lig > 0 )
  {
    echo ">F89#OK#" . $nb_lig . "###|";
    while( list ($id_srv, $srv_name) = mysqli_fetch_row ($result) )
    {
      $msg = "#" . $id_srv . "#" . $srv_name . "#";
      $msg = f_encode64($msg);
      echo $msg . "|"; // séparateur de ligne : '|' (pipe).
    }
  }
  else
  {
    // renvoie : la liste est vide.
    echo ">F89#OK#0##";
  }
  //
  mysqli_close($id_connect);
}
?>
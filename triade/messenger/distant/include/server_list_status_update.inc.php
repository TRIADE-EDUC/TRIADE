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
$session_chk =  f_decode64_wd($_GET['sc']);
$list_srv =     f_decode64_wd($_GET['sv']);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($ip != "") and ($session_chk != "") and ($list_srv != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die (">F87#KO#Session KO.##");
  //
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
    $requete  = " SELECT ID_SERVER, SRV_NAME, SRV_STATE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
    $requete .= " ORDER BY UPPER(SRV_NAME) ";
    //
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-87a]", $requete);
    $nb_srv = mysqli_num_rows($result);
    if ( ($nb_srv > 0) and ($nb_srv == strlen($list_srv)) ) 
    {
      $num_srv = 0;
      while( list ($id_srv, $srv_name, $srv_status) = mysqli_fetch_row ($result) )
      {
        $num_srv++;
        if ($num_srv <= strlen($list_srv))
        {
          $new_status = substr($list_srv, ($num_srv - 1) , 1);
          if ( ($new_status == "0") or ($new_status == "1") or ($new_status == "2") )
          {
            $requete2  = " UPDATE " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
            $requete2 .= " SET SRV_STATE = " . $new_status . " ";
            $requete2 .= " WHERE ID_SERVER = " . $id_srv . " ";
            $requete2 .= " limit 1 ";
            //
            $result2 = mysqli_query($id_connect, $requete2);
            if (!$result2) error_sql_log("[ERR-87b]", $requete2);
          }
        }
      }
    }
    echo ">F87#OK###";
  }
  else
    echo ">F87#KO###";
  //
  mysqli_close($id_connect);
}
?>
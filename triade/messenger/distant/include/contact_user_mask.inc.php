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
if ( (!isset($_GET['u1'])) or (!isset($_GET['u2'])) or (!isset($_GET['ic'])) or (!isset($_GET['ip'])) ) die();
//
$id_u_1 =	      intval(f_decode64_wd($_GET['u1']));
$id_u_1 = 		  (intval($id_u_1) - intval($action));
$id_u_2 =		    intval(f_decode64_wd($_GET['u2']));
$contact_id = 	intval(f_decode64_wd($_GET['ic']));
$ip = 			    f_decode64_wd($_GET['ip']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_u_1)) $id_u_1 = "";
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
if (preg_match("#[^0-9]#", $contact_id)) $contact_id = "";
//
if ( ($id_u_1 > 0) and ($id_u_2 > 0) and ($contact_id > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_u_1, $session_chk, $action) != 'OK')  die ("Session KO.");
  //
  //
  $t_allow_invisible = _ALLOW_HIDDEN_TO_CONTACTS;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_u_1);
    //
    if ($id_role > 0)
    {
      $t_allow_invisible = f_role_permission($id_role, "ALLOW_HIDDEN_TO_CONTACTS", _ALLOW_HIDDEN_TO_CONTACTS);
    }
  }
  //
  if ($t_allow_invisible != "")
  {
    $requete  = " select CNT_STATUS from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
    $requete .= " WHERE ID_CONTACT = " . $contact_id . " ";
    $requete .= " and ID_USER_1 = " . $id_u_1 . " "; // (pour le cas ou)
    $requete .= " and ID_USER_2 = " . $id_u_2 . " "; // (pour le cas ou)
    $requete .= " and ( CNT_STATUS = 1 "; // doit être déjà validé
    $requete .= " or CNT_STATUS = 5 ) "; // ou masqué 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-37a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($priv) = mysqli_fetch_row ($result);
      //
      $requete = " update " . $PREFIX_IM_TABLE . "CNT_CONTACT "; 
      if ($priv == 1)
        $requete .= " set CNT_STATUS = 5 ";    // rend invisible
      if ($priv == 5)
        $requete .= " set CNT_STATUS = 1 ";    // rend visible
      //
      $requete .= " WHERE ID_CONTACT = " . $contact_id . " ";
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-37b]", $requete);
    }
  }
  //
  mysqli_close($id_connect);
}
?>
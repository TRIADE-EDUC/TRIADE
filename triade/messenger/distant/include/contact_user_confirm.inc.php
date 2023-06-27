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
$id_u_1 =		  intval(f_decode64_wd($_GET['u1'])); 
$id_u_1 = 		(intval($id_u_1) - intval($action));
$id_u_2 =		  intval(f_decode64_wd($_GET['u2'])); 
$contact_id = intval(f_decode64_wd($_GET['ic']));
$ip = 			  f_decode64_wd($_GET['ip']);
$deja_en_contact = 0;
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
  // on autorise le demandeur
  $requete  = " update " . $PREFIX_IM_TABLE . "CNT_CONTACT "; 
  $requete .= " set CNT_STATUS = 1, CNT_NEW_USERNAME = '' "; // and delete confirm message (stored in CNT_NEW_USERNAME).
  $requete .= " WHERE ID_CONTACT = " . $contact_id . " ";
  $requete .= " and ID_USER_1 = " . $id_u_2 . " "; // (pour le cas ou)
  $requete .= " and ID_USER_2 = " . $id_u_1 . " "; // (pour le cas ou)
  $requete .= " and CNT_STATUS = 0 "; // pour le cas ou > 1 !
  $requete .= " LIMIT 1 "; // (to protect)
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-32a]", $requete);
  //
  // on ajoute (ou autorise) le demandeur dans la liste de celui qui vient de l'accepter.
  // si deja dans la liste (peut être simplement en attente)
  $deja_en_contact = f_is_deja_in_contacts_id($id_u_1, $id_u_2);
  if ($deja_en_contact > 0)
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "CNT_CONTACT "; 
    $requete .= " set CNT_STATUS = 1, CNT_NEW_USERNAME = '' "; // and delete confirm message (stored in CNT_NEW_USERNAME).
    $requete .= " WHERE ID_USER_1 = " . $id_u_1 . " "; 
    $requete .= " and ID_USER_2 = " . $id_u_2 . " "; 
    $requete .= " and CNT_STATUS = 0 "; // pour le cas ou > 1 !
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-32b]", $requete);
  }
  else
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "CNT_CONTACT (ID_USER_1, ID_USER_2, CNT_STATUS) ";
    $requete .= " VALUES (" . $id_u_1 . ", " . $id_u_2 . ", 1) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-32c]", $requete);
  }
  //
  update_last_activity_user($id_u_1);
  //
  mysqli_close($id_connect);
}
?>
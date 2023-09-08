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
  $requete  = " select CNT_STATUS from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
  $requete .= " WHERE ID_CONTACT = " . $contact_id . " ";
  $requete .= " and ID_USER_1 = " . $id_u_1 . " "; // (pour le cas ou)
  $requete .= " and ID_USER_2 = " . $id_u_2 . " "; // (pour le cas ou)
  $requete .= " and (CNT_STATUS = 1 "; // doit être déjà validé, 
  $requete .= " or CNT_STATUS = 2) "; // ou déjà VIP pour le cas ou on l'enlève des VIP (MAIS ne doit être 'invisible' !).
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-38a]", $requete);
  //
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($priv) = mysqli_fetch_row ($result);
    //
    // Si c'est pour passer en VIP, on vérifie qu'on ne va pas avoir tous les contacts en VIP (sinon, ce n'est plus du VIP).
    $ok = 'OK';
    if ($priv == '1')
    {
      $nb_contacts = 0;
      $nb_vip = 0;
      //
      // on compte les contacts valides
      $requete  = " select count(*) from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
      $requete .= " WHERE ID_USER_1 = " . $id_u_1 . " "; 
      $requete .= " and CNT_STATUS > 0 "; 
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-38b]", $requete);
      if ( mysqli_num_rows($result) == 1 )
        list ($nb_contacts) = mysqli_fetch_row ($result);
      //
      // on compte les VIP
      $requete  = " select count(*) from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
      $requete .= " WHERE ID_USER_1 = " . $id_u_1 . " "; 
      $requete .= " and CNT_STATUS = 2 "; // VIP
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-38c]", $requete);
      if ( mysqli_num_rows($result) == 1 )
        list ($nb_vip) = mysqli_fetch_row ($result);
      //
      if ( ( ($nb_vip + 1) >= intval($nb_contacts) ) and (intval($nb_contacts) > 1) )
        $ok = 'KO';
    }
    //
    if ($ok == 'OK')
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "CNT_CONTACT "; 
      $requete .= " set CNT_STATUS = " . (3 - $priv) . " ";    // inverser 1 <-> 2
      $requete .= " WHERE ID_CONTACT = " . $contact_id . " ";
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-38d]", $requete);
    }
  }
  //
  mysqli_close($id_connect);
}
?>
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
// see also : list_contact_user_to_confirm.php
//
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
if ( (!isset($_GET['u'])) or (!isset($_GET['st'])) or (!isset($_GET['ip'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['u']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$status = 		intval($_GET['st']);
$version =    intval($_GET['v']);
$away =       intval($_GET['aw']);
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
  if ( ($status == 1) and (_ONLINE_REASONS_LIST == "") ) $away = 0;
  if ( ($status == 2) and (_AWAY_REASONS_LIST == "") ) $away = 0;
  if ( ($status == 3) and (_BUSY_REASONS_LIST == "") ) $away = 0;
  if ( ($status == 4) and (_DONOTDISTURB_REASONS_LIST == "") ) $away = 0;
  if ($away > 12) $away = 0;
  //
  $requete  = " update " . $PREFIX_IM_TABLE . "SES_SESSION ";
  $requete .= " SET SES_STATUS = " . $status . ", ";
  $requete .= " SES_AWAY_REASON = " . $away . " ";
  //$requete .= " WHERE ID_SESSION = " . $id_session . " ";
  $requete .= " WHERE ID_USER = " . $id_user ;
  $requete .= " LIMIT 1 "; // (to protect)
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-75a]", $requete);
  //
  // on vérifie si la modif à bien eu lieu
  $retour = 'KO'; // par défaut
  $requete  = " select ID_SESSION, SES_STATUS ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
  $requete .= " WHERE ID_USER = " . $id_user . " ";
  $requete .= " limit 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-75b]", $requete);
  if ( mysqli_num_rows($result) == 1 ) // il ne doit y avoir qu'une seule ligne !
  {
    list ($session_id, $etat_num) = mysqli_fetch_row ($result);
    //if ( ($etat_num == $status) and ($session_id = $id_session) )
    if ($etat_num == $status)
    {
      $retour = 'OK'; 
      update_time_session_id_session($session_id);
    }
  }
  if ($retour <> 'OK') 
  {
    close_session_id_user($id_user);
  }
  //
  echo ">F30#" . $retour . "#";
  //
  mysqli_close($id_connect);
}
?>
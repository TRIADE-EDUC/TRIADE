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
if ( (!isset($_GET['u1'])) or (!isset($_GET['u2'])) or (!isset($_GET['ip'])) ) die();
//
$id_u_1 =	    intval(f_decode64_wd($_GET['u1']));
$id_u_1 = 		(intval($id_u_1) - intval($action));
$id_u_2 =		  intval(f_decode64_wd($_GET['u2']));
$ip = 			  f_decode64_wd($_GET['ip']);
//$version =    intval($_GET['v']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_u_1)) $id_u_1 = "";
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
//
if ( ($id_u_1 > 0) and ($id_u_2 > 0) and ($ip != "") )
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
  $requete  = " delete from " . $PREFIX_IM_TABLE . "CNT_CONTACT "; 
  $requete .= " WHERE ID_USER_1 = " . $id_u_1 . " "; 
  $requete .= " and ID_USER_2 = " . $id_u_2 . " "; 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-33a]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "CNT_CONTACT "; 
  $requete .= " WHERE ID_USER_1 = " . $id_u_2 . " "; 
  $requete .= " and ID_USER_2 = " . $id_u_1 . " "; 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-33b]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "MSG_MESSAGE "; 
  $requete .= " WHERE ID_USER_AUT = " . $id_u_1 . " "; 
  $requete .= " and ID_USER_DEST = " . $id_u_2 . " "; 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-33c]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "MSG_MESSAGE "; 
  $requete .= " WHERE ID_USER_AUT = " . $id_u_2 . " "; 
  $requete .= " and ID_USER_DEST = " . $id_u_1 . " "; 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-33d]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "FIL_FILE "; 
  $requete .= " WHERE ID_USER_AUT = " . $id_u_1 . " "; 
  $requete .= " and ID_USER_DEST = " . $id_u_2 . " "; 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-33e]", $requete);
  //
  $requete  = " delete from " . $PREFIX_IM_TABLE . "FIL_FILE "; 
  $requete .= " WHERE ID_USER_AUT = " . $id_u_2 . " "; 
  $requete .= " and ID_USER_DEST = " . $id_u_1 . " "; 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-33f]", $requete);
  //
  mysqli_close($id_connect);
}
?>
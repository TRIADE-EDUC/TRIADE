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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$ip = 			    f_decode64_wd($_GET['ip']);
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
  $requete  = " delete from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
  $requete .= " where ID_USER_1 = " . $id_user . " and CNT_STATUS = 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-35a]", $requete);
  //
  // On fait aussi le ménage sur les contacts dont le user n'existe plus :
  $requete  = " select distinct(ID_USER_2) ";
  $requete .= " from " . $PREFIX_IM_TABLE . "CNT_CONTACT LEFT JOIN " . $PREFIX_IM_TABLE . "USR_USER ON " . $PREFIX_IM_TABLE . "CNT_CONTACT.ID_USER_2 = " . $PREFIX_IM_TABLE . "USR_USER.ID_USER ";
  $requete .= " where " . $PREFIX_IM_TABLE . "USR_USER.ID_USER IS NULL ";
  $requete .= " and " . $PREFIX_IM_TABLE . "CNT_CONTACT.ID_USER_1 = " . $id_user . " ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-35b]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($id_user_to_delete) = mysqli_fetch_row ($result) )
    {
      $requete_2  = " delete from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
      $requete_2 .= " where ID_USER_1 = " . $id_user_to_delete . " or ID_USER_2 = " . $id_user_to_delete . " ";
      $result2 = mysqli_query($id_connect, $requete_2);
      if (!$result2) error_sql_log("[ERR-35c]", $requete_2);
    }
  }
  //
  mysqli_close($id_connect);
}
?>
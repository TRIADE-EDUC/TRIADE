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

function f_check_session_id_user($t_id_user, $t_session, $t_action)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_user = intval($t_id_user);
  $t_session = trim($t_session);
	if ( ($t_id_user <= 0) or ($t_session == "") )
	{
		return 'KO';
	}
	else
	{
    $check = "?";
    $retour = 'KO'; // par défaut
    //
    $requete  = " select ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3g]", $requete);
    if ( mysqli_num_rows($result) == 1 ) // il ne doit y avoir qu'une seule ligne !
    {
      list ($id_session) = mysqli_fetch_row ($result);
      //$check = md5( md5($id_session) . md5($t_action) . md5($t_id_user) );
      $check = md5($id_session . $t_action . $t_id_user);
      $check = substr($check, 5, 10);
      if ($check == $t_session)
      {
        $retour = 'OK'; 
      }
    }
  }
	if ($retour <> 'OK')
	{
		close_session_id_user($t_id_user);
		sleep(1);
	}
	//	
	return $retour;
}


function f_verif_id_session_id_user($t_id_user, $t_id_session)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_user = intval($t_id_user);
  $t_id_session = intval($t_id_session);
	if ( ($t_id_user <= 0) or ($t_id_session <= 0) )
	{
		return 'KO';
	}
	else
	{
    $retour = 'KO'; // par défaut
    $id_session = 0; // par défaut
    //
    $requete  = " select ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3g]", $requete);
    if ( mysqli_num_rows($result) == 1 ) // il ne doit y avoir qu'une seule ligne !
    {
      list ($id_session) = mysqli_fetch_row ($result);
      if ($id_session == $t_id_session)
      {
        $retour = 'OK'; 
      }
    }
  }
	if ($retour <> 'OK')
	{
		close_session_id_user($t_id_user);
		sleep(1);
	}
	//	
	return $retour;
}


function close_session_id_user($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
		// donc on supprime les éventuelles sessions
		$requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
		$requete .= " WHERE ID_USER = " . $t_id_user;
		$result = mysqli_query($id_connect, $requete);
		if (!$result) error_sql_log("[ERR-M3a]", $requete);
		//
		// on supprime sa participation aux éventuelles conférences.
    if (!defined("_ALLOW_CONFERENCE"))  include("config/config.inc.php");
    //
		if (_ALLOW_CONFERENCE != '')
    {
      $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
      $requete .= " WHERE ID_USER = " . $t_id_user;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M3b]", $requete);
    }
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    //$requete .= " SET USR_ONLINE=0 , USR_DATE_LAST = CURDATE() "; // NON !  USR_DATE_LAST et USR_NB_CONNECT en même temps !!!
    $requete .= " SET USR_ONLINE=0 ";
    $requete .= " where ID_USER = " . $t_id_user . "  ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3q]", $requete);
	//
	}
}


function open_session_user($t_id_user, $t_user, $t_os, $t_check)
{
  global $PREFIX_IM_TABLE, $id_connect;
	$ip_local = $_SERVER['REMOTE_ADDR'];	
  $t_id_user = intval($t_id_user);
  if ( ($t_os == "NT 5") or ($t_os == "NT5") or ($t_os == "NT") ) $t_os = "2000";
  //
  if ($t_id_user > 0)
  {
    // on ajoute une nouvelle session
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " (ID_USER, SES_STATUS, SES_STARTDATE, SES_STARTTIME, SES_LASTTIME, SES_IP_ADDRESS) ";
    $requete .= " VALUES (" . $t_id_user . ", 0, CURDATE(), CURTIME(), CURTIME(), '" . $ip_local . "' ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3c]", $requete);
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " set USR_DATE_LAST = CURDATE() , USR_NB_CONNECT = (USR_NB_CONNECT + 1) ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $requete .= " and USR_DATE_LAST <> CURDATE() "; //   <---
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3n1]", $requete);
    //  USR_DATE_LAST et USR_NB_CONNECT en même temps !!!
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " set USR_OS = '" . $t_os . "', ";
    $requete .= " USR_IP_ADDRESS = '" . $ip_local . "', ";
    $requete .= " USR_PWD_ERRORS=0, ";
    $requete .= " USR_ONLINE=1 ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    //$requete .= " and USR_STATUS = 1 ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3n2]", $requete);
    //
    if (_LOG_SESSION_OPEN != '')
      write_log("log_open_session", $t_user. ";" . $t_check);
  }
}


function f_get_id_session_user($t_username)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$id_session = 0;
	if ($t_username != '')
	{
    $requete  = " select ID_SESSION, SES_STATUS ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " and USR.USR_USERNAME = '" . $t_username . "' ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3e]", $requete);
    if ( mysqli_num_rows($result) == 1 )
      list ($id_session, $etat_num) = mysqli_fetch_row ($result);
    //else
      //fermer_sessions_user($t_user);
  }
	// renvoyer le N° de session
  return $id_session; 
}


function f_get_id_session_id_user($t_id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$id_session = 0;
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $requete  = " select ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " and USR.ID_USER = " . $t_id_user . " ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3f]", $requete);
    if ( mysqli_num_rows($result) == 1 )
      list ($id_session) = mysqli_fetch_row ($result);
  }
  // renvoyer le N° de session
	return $id_session; 
}


// indiquer que la session est toujours valide.
function update_time_session_id_session($t_id_session)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $t_id_session = intval($t_id_session);
	if ($t_id_session > 0)
	{
    $requete  = " update " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " SET SES_LASTTIME = CURTIME() ";
    $requete .= " where ID_SESSION = " . $t_id_session . " ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3h]", $requete);
  }
}


function clean_inactives_session()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  if ( _ENTERPRISE_SERVER != '' )
  {
    // Computer sleep (display on /admin/list_users_pc.php) :
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "SES_SESSION SES"; 
    $requete .= " set USR.USR_ONLINE = 2 "; 
    $requete .= " WHERE SES.ID_USER = USR.ID_USER  ";
    ##$requete .= " and ABS( EXTRACT(HOUR_MINUTE from NOW()) - (EXTRACT(HOUR_MINUTE from SES.SES_LASTTIME)) ) > 4 ";
    $requete .= " and ( ( (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES.SES_LASTTIME)) > 4 ";
    $requete .= "     and (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES.SES_LASTTIME)) < 40 )"; // 40! car de :59 à :00 ya 40 minutes !
    $requete .= "    or ( (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES.SES_LASTTIME)) < -1 ";
    $requete .= "     and (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES.SES_LASTTIME)) > -2355 )  )";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3p]", $requete);
  }
  //
  //
  //
	// on purge les trop anciennes. Remove sessions if not alive about 4 minutes :
	$requete  = " delete from " . $PREFIX_IM_TABLE . "SES_SESSION ";
	##$requete .= " where ABS( EXTRACT(HOUR_MINUTE from NOW()) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) ) > 4 ";
	$requete .= " where ( (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) > 4 ";
	$requete .= "     and (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) < 40 ) "; // 40! car de :59 à :00 ya 40 minutes !
  $requete .= " or    ( (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) > 45 ) "; // 27/06/12 (à cause de "40").
	$requete .= " or    ( (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) < -1 ";
	$requete .= "     and (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) > -2355 ) ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-M3i]", $requete);
	//
	// on désactive les relativement anciennes. Away sessions if not alive about 2 minutes :
	$requete  = " update " . $PREFIX_IM_TABLE . "SES_SESSION ";
	$requete .= " SET SES_STATUS = 0 ";
	##$requete .= " where ABS( EXTRACT(HOUR_MINUTE from NOW()) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) ) > 2 ";
	$requete .= " where (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) > 2 ";
	$requete .= " and (EXTRACT(HOUR_MINUTE from NOW())) - (EXTRACT(HOUR_MINUTE from SES_LASTTIME)) < 40 "; // 40! car de :59 à :00 ya 40 minutes !
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-M3k]", $requete);
  //
  //
	if (_STATISTICS != '')
	{
    // dernière date en date du jour (sinon, on ne comptabiliserait pas dans les stats ceux qui restent plusieurs jours de rangs).
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "SES_SESSION SES"; 
    $requete .= " set USR.USR_DATE_LAST = CURDATE(), "; // USR_DATE_LAST et USR_NB_CONNECT en même temps !!!
    $requete .= "   USR_NB_CONNECT = (USR_NB_CONNECT + 1) "; // USR_DATE_LAST et USR_NB_CONNECT en même temps !!!
    $requete .= " WHERE SES.ID_USER = USR.ID_USER  ";
    $requete .= "   and SES.SES_STATUS > 0 "; 
    $requete .= "   and USR.USR_DATE_LAST <> CURDATE() "; //  <--
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3r]", $requete);
    //
    if (!function_exists('stats_max')) require ("stats.inc.php");
    //
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3m]", $requete);
    list ($nb_ses) = mysqli_fetch_row ($result);
    if (intval($nb_ses) > 1)
    {
      stats_max("STA_NB_SESSION", $nb_ses);
    }
  }
  //
  if (_UNREAD_MESSAGE_VALIDITY > 1)
  {
    $requete  = " delete from " . $PREFIX_IM_TABLE . "MSG_MESSAGE "; 
    $requete .= " WHERE  TO_DAYS(NOW()) - TO_DAYS(MSG_DATE) > " . intval(_UNREAD_MESSAGE_VALIDITY);
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3s1]", $requete);
  }
  //
  // Déverrouiller les comptes en attente (après x erreurs de mot de passe) après Y minutes.
  if (_LOCK_DURATION > 1)  // /*-GIC (pas d'index ci-dessous : à optimiser)
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
    $requete .= " SET USR_STATUS = 3, ";
    $requete .= "   USR_TIME_LOCK = '00:00:00' ";
    $requete .= " WHERE USR_STATUS = 2 ";
    $requete .= "   and USR_TIME_LOCK <> '00:00:00' ";
    $requete .= "   and USR_TIME_LOCK < CURTIME() ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M3s2]", $requete);
  }
}

?>
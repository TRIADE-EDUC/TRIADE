<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2019 THeUDS           **
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

function inc_user_password_error($t_id_user, $t_user, $t_check, $t_max_pass_errors_before_lock, $t_lock_duration)
{
  global $PREFIX_IM_TABLE, $id_connect, $l_start_waiting_valid;
  //
  //$t_max_pass_errors_before_lock = intval(_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER);
  if ($t_max_pass_errors_before_lock < 2) $t_max_pass_errors_before_lock = 5;
  if ($t_max_pass_errors_before_lock > 20) $t_max_pass_errors_before_lock = 10;
  //
  $t_id_user = intval($t_id_user);
	if ($t_id_user > 0)
	{
    $requete  = " select USR_PWD_ERRORS ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M4a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_errors) = mysqli_fetch_row ($result);
      //
      $nb_errors = (intval($nb_errors) + 1);
      $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " set USR_PWD_ERRORS = (USR_PWD_ERRORS + 1) ";
      $requete .= " WHERE ID_USER = " . $t_id_user . " ";
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M4b]", $requete);
      //
      if ($nb_errors >= $t_max_pass_errors_before_lock)
      {
        $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " set USR_STATUS = 2 "; // locked  // USR_CHECK = 'WAIT'
        if ($t_lock_duration > 0)
          $requete .= " , USR_TIME_LOCK = CURTIME() + 60 * " . intval($t_lock_duration); // lock x minutes
        else
          $requete .= " , USR_TIME_LOCK = '00:00:00' "; // unlimited (need admin to unlock)
        //
        $requete .= " WHERE ID_USER = " . $t_id_user . " ";
        $requete .= " LIMIT 1 "; // (to protect)
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M4c]", $requete);
        //
        write_log("log_lock_user_for_password_errors", $t_user . ";" . $t_check);
        //
        if (_SEND_ADMIN_ALERT != "")
        {
          $txt = $l_start_waiting_valid;
          if ($txt == "") $txt = "Locked user(s) waiting...";
          send_alert_message_to_admins($txt);
        }
      }
    }
  }
  write_log("log_password_errors", $t_user . ";" . $t_check);
}


function f_verif_check_user_creat($t_user, $t_id_user, $t_check, $t_pass, $n_version, $t_nick, $t_max_nb_ip, $t_pending_user_on_computer_change)
{
  global $PREFIX_IM_TABLE, $id_connect, $l_index_pending_avatars, $l_start_waiting_valid;
  //
	//require("config/config.inc.php");
	//require("extern/extern.inc.php");
  require("config/auth.inc.php");
  require("f_not_empty.inc.php");
  require("user.inc.php");
  //
	$passcr_old = ""; // before 1.4
	$passcr = "";     // after 1.4
	$salt_and_pepper = ""; // before 1.4
	$t_id_user = intval($t_id_user);
	if (_USER_NEED_PASSWORD != '')
	{
		if ($t_pass != '')
		{
			if (f_nb_auth_extern() == 1)
			{
        if (substr($t_pass, 0, 5) == "M%zK:") 
        {
          $t_pass = substr($t_pass,-(strlen($t_pass)-4));
          $t_pass = base64_decode($t_pass);
        }
			}
			//$t_pass = f_clean_name($t_pass);
			$passcr_old = substr(md5($salt_and_pepper . $t_pass), 5, 20); // before 1.4
			$passcr = sha1($password_pepper . $t_pass . "W$*7B0-c6");
		}	
	}	
	//
	//
	// Eviter le flood (plusieurs sessions depuis un même PC) // too many users from same computer.
	if ( (intval(_MAX_NB_USER) > 0) or (intval(_MAX_NB_SESSION) > 0) or (intval($t_max_nb_ip) > 0) or ($t_pending_user_on_computer_change != "") )
	{
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " and USR.USR_CHECK = '" . $t_check . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M4h]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_same_pc) = mysqli_fetch_row ($result);
      if (intval($nb_same_pc) > 2)
        return "KO-DUPLICATE-PC"; // anciennement KO-MAX-SESSION
    }
  }
	//
	//if ($t_id_user > 0) close_session_id_user($t_id_user);
	// Eviter doubles sessions pour le même user :
	/* enlevé 20/06/09, par bloque, si plantage exe.
	if ($t_id_user > 0)
    {
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " WHERE ID_USER = " . $t_id_user;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M4i]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_same_user) = mysqli_fetch_row ($result);
      if (intval($nb_same_user) > 0)
        return "KO-MAX-SESSION";
    }
  }
	*/
	//
	if (intval(_MAX_NB_SESSION) > 0)
	{
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-K4i]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_ses) = mysqli_fetch_row ($result);
      //
      if (intval($nb_ses) >= intval(_MAX_NB_SESSION))
        return "KO-MAX-SESSION";
    }
  }
	//
	//
	$retour = ''; // par défaut
	//
	$requete  = " select USR_NAME, USR_CHECK, USR_PASSWORD, USR_STATUS ";
	$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
	$requete .= " WHERE USR_USERNAME = '" . $t_user . "' ";
	$result = mysqli_query($id_connect, $requete);
	if (!$result) error_sql_log("[ERR-M4d]", $requete);
	//
	// ***************************************** If user exist already ****************************************************
	//
	//if (f_get_id_nom_user($t_user) > 0) // Non !!! 
	if ( mysqli_num_rows($result) == 1 )
	{
		list ($usr_nom, $usr_check, $usr_pass, $usr_status) = mysqli_fetch_row ($result);
		//
		//if ( ($usr_status == 2) or ($usr_check == "WAIT") ) // en attente de validation de l'admin (ou verrouillé pour erreurs de mot de passe).
		if ($usr_status == 2)  // en attente de validation de l'admin (ou verrouillé pour erreurs de mot de passe).
      return "WAIT";
    //
    if ($usr_status == 4) // verrouillé
    {
      write_log("log_reject_user_locked", $t_user . ";" . $usr_check . ";" . $t_id_user . ";");
      return "WAIT";
		}
		//
		//
		// on vérifie le password AVANT le update_check_user : sinon, risque usurpation pseudo !
		// on vérifie le mot de passe (si demandé dans les options)
		if (_USER_NEED_PASSWORD != '')
		{
      if (f_nb_auth_extern() == 1)
      {
        // Check extern authentication
        //$retour = f_check_if_auth_exten_ok($t_user, $t_pass);
        $retour = f_check_if_auth_exten_ok($t_nick, $t_pass);
      }
      //
      // --------- classic access, NO extern auth --------- 
      if ($retour == '')
      {
        $retour = 'KO-PASS';
        if ( ($usr_pass == $passcr_old) or ($usr_pass == $passcr) )  $retour = 'OK';
        //
        if ( ($usr_pass == '') or ($usr_pass == $passcr_old) ) // si pas encore dans la BDD (ou ancien cryptage), on le met (et si non en extern).
        {
          $retour = f_update_pass_user($t_id_user, $t_pass);
          /*
          $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
          $requete .= " SET USR_PASSWORD = '" . $passcr . "' ";
          $requete .= " WHERE ID_USER = " . intval($t_id_user) . " ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-M4e]", $requete);
          //$usr_pass = $passcr;
          $retour = 'OK';
          */
        }
      }
		}
		else
      $retour = 'OK';
    //
    // Pour ne pas que ca traine :
    unset($usr_pass); 
    unset($t_pass);
    unset($passcr);
    unset($passcr_old);
    //
		// Si pas de problème de mot de passe, on vérifie le check du poste.
		//if ( ($retour != 'KO-PASS') and ($retour != 'KO-PHENIX') and ($retour != 'KO-PHPBB') ...)
		//if ( ($retour == 'OK') or ($retour == '') )
		if ($retour == 'OK')
		{
      if ($usr_status <> 1) // config validée  ou  leave.
      {
        $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " SET USR_STATUS = 1, USR_TIME_LOCK = '00:00:00' ";
        $requete .= " WHERE ID_USER = " . $t_id_user . " ";
        $requete .= " LIMIT 1 "; // (to protect)
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M4k]", $requete);
      }
      //
      if ($usr_check == "")
      {
        update_check_user($t_id_user, $t_check);
        $usr_check = $t_check;
      }
      //
			switch ($usr_check)
			{
#				case "" : // si valeur de controle non renseignée, on la récupère.
#					$retour = ''; // vide pour récupérer auto la valeur
#					break;
				case $t_check : // si valeur OK
					$retour = 'OK';
					break;
				case "WAIT" : // en attente de validation de l'admin
					$retour = 'WAIT';
					break;
				default : // tous les autres cas (donc si différent de la 'bonne' valeur)
					if ($t_pending_user_on_computer_change != '')
					{
						error_check_log($t_id_user, "'" . $t_check . "' <> '" . $usr_check . "'" );
						update_check_user($t_id_user, 'WAIT');
						$retour = 'KO';
            //if (defined("_SEND_ADMIN_ALERT"))
            if (_SEND_ADMIN_ALERT != "")
            {
              $txt = $l_start_waiting_valid;
              if ($txt == "") $txt = "Pending user(s) waiting...";
              send_alert_message_to_admins($txt);
            }
					}
					else
					{
						// Vérifie si une session en cours avec un autre check (donc le même compte depuis un autre PC).
						if (f_get_id_session_id_user($t_id_user) == 0)
						{
              update_check_user($t_id_user, $t_check);
              $retour = 'OK';
              //
              // si l'ancien existait :
              if ( ($usr_check != "") and ($usr_check != "WAIT") )
              {
                write_log("log_user_check_change", $t_user . ";" . $usr_check . ";" . $t_check );
              }
            }
            else
            {
              $retour = 'KO-OTHER-PC';
              write_log("log_user_check_double", $t_user . ";" . $usr_check . ";" . $t_check );
            }
					}
					break;
			}
		}
	}
	else 	// ***************************************** If NEW user ****************************************************
	{
		if (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != '')
		{
			$tt = f_if_already_max_nb_users();
			if (intval($tt) == 0)
			{
				if (f_get_id_nom_user($t_user) != '')
				{
					$retour = 'DOUBLON'; // pseudo existe déjà.
				}
				else
				{
          if (f_nb_auth_extern() == 1)
          {
            // Check extern authentication
            //$retour = f_check_if_auth_exten_ok($t_user, $t_pass);
            $retour = f_check_if_auth_exten_ok($t_nick, $t_pass);
          }
          else
          {
            if ( (intval($n_version) >= 22) and (_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER != '') and (_USER_NEED_PASSWORD != '') ) $retour = "KO-NEED_REGISTER";
          }
          //
          if (($retour == "") or ($retour == "OK")) // si ok extern (ou pas d'auth extern)
          {
            if ($retour == "OK") // si extern OK, alors on ne stocke pas le mot de passe dans la base IM (car inutile).
            {
              // Login Phenix via Triade
              #if (_AUTHENTICATION_ON_TRIADE != '')   
              if (_EXTERNAL_AUTHENTICATION == "triade")
              {
                $user_phenix_triade = f_triade_auth_to_phenix($t_user, $t_pass);
              }
              //
              unset($t_pass);
              $passcr = ""; // not unset !!! 
            }
            //
            $usr_status = 1; // ok
            if (_PENDING_NEW_AUTO_ADDED_USER != '')
            {
              $usr_status = 2; // locked (ajouté, mais reste en attente)
              //$t_i_chk = "WAIT"; // ajouté, mais reste en attente
              //if (defined("_SEND_ADMIN_ALERT"))
              if (_SEND_ADMIN_ALERT != "")
              {
                $txt = $l_start_waiting_valid;
                if ($txt == "") $txt = "Pending new user(s) waiting...";
                send_alert_message_to_admins($txt);
              }
            }
            //
            $name_or_function = ""; // Name and first name in col name_function (USR_NAME) (for default)
            $f = f_clean_username(_EXTERNAL_AUTHENTICATION);
            if ($f <> "")
            {
              if (strstr("#phenix#ovidentia#taskfreak#webcollab#sugarcrm#phprojekt#toutateam#groupoffice#cuteflow#", $f)) $name_or_function = f_extern_name_of_user($t_user);
            }
            if (_EXTERNAL_AUTHENTICATION == "triade")    
            {
              $name_or_function = $t_user;
              $name_or_function = str_replace('.',' ',$name_or_function);
              $name_or_function = str_replace('_',' ',$name_or_function);
              $name_or_function = trim($name_or_function);
              $name_or_function = ucwords($name_or_function);
            }
            if ($name_or_function != "")
            {
              $name_or_function = f_clean_name($name_or_function);   // f_clean_username($name_or_function); NON si met tout en minuscules
            }
            if ($t_nick == $t_user) $t_nick = "";
            //
            $requete  = " insert into " . $PREFIX_IM_TABLE . "USR_USER ";
            $requete .= " (USR_USERNAME, USR_NICKNAME, USR_CHECK, USR_DATE_CREAT, USR_PASSWORD, USR_NAME, USR_STATUS, ID_ROLE) VALUES (";
            $requete .= " '" . $t_user . "' , '" . $t_nick . "' , '" . $t_check . "' , CURDATE() , '" . $passcr . "' , ";
            $requete .= " '" . $name_or_function . "', " . $usr_status . ", ";
            if ( (_ROLES_TO_OVERRIDE_PERMISSIONS != "") and (intval(_ROLE_ID_DEFAULT_FOR_NEW_USER) > 0) )
              $requete .= intval(_ROLE_ID_DEFAULT_FOR_NEW_USER);
            else
              $requete .= " null ";
            //
            $requete .= " )";
            $result = mysqli_query($id_connect, $requete);
            if (!$result) error_sql_log("[ERR-M4f]", $requete);
            //
            $last_id = mysqli_insert_id($id_connect);
            //
            write_log("log_user_create", $t_user . ";" . $t_check);
            //
            if ($last_id > 0)
            {
              // Login Phenix via Triade
              #if ( (_AUTHENTICATION_ON_TRIADE != '') and isset($user_phenix_triade) )
              if ( (_EXTERNAL_AUTHENTICATION == "triade") and isset($user_phenix_triade) )
              {
                $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
                $requete .= " set USR_TRIADE_PHENIX = '" . $user_phenix_triade . "' ";
                $requete .= " where ID_USER = " . intval($last_id);
                $requete .= " LIMIT 1 "; // (to protect)
                $result = mysqli_query($id_connect, $requete);
                if (!$result) error_sql_log("[ERR-M4g]", $requete);
              }
              //
              if ( (_USER_NEED_PASSWORD != '') and ($passcr != "") )
              {
                $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
                $requete .= " SET USR_DATE_PASSWORD = CURDATE() ";
                $requete .= " WHERE ID_USER = " . intval($last_id);
                $requete .= " LIMIT 1 "; // (to protect)
                $result = mysqli_query($id_connect, $requete);
                if (!$result) error_sql_log("[ERR-M4m]", $requete);
              }
              //
              if (_AUTO_ADD_CONTACT_USER_ID != "")
              {
                $list_id = explode(";", _AUTO_ADD_CONTACT_USER_ID);
                foreach($list_id as $t_id)
                {
                  $t_id = intval($t_id);
                  if ($t_id > 0)
                  {
                    if (f_get_username_of_id($t_id) != "")  user_add_valid_contact($t_id, $last_id);
                  }
                } 
              }
              //
              if (defined("_GROUP_ID_DEFAULT_FOR_NEW_USER"))
              {
                $t = intval(_GROUP_ID_DEFAULT_FOR_NEW_USER);
                if ($t > 0)
                {
                  user_add_in_default_group($last_id, $t);
                }
              }
            }
            //
            if (_STATISTICS != '')
            {
              if (!function_exists('stats_inc')) require ("stats.inc.php"); 
              stats_inc("STA_NB_CREAT");
            }
            // si l'ajout c'est bien passé :
            if (_PENDING_NEW_AUTO_ADDED_USER != '')
              $retour = 'WAIT'; // ajouté, mais reste en attente
            else
              $retour = 'OK'; // ajouté et directement valide
          }
				}
			}
			else
				$retour = 'KO-MAX'; // nbre de user max atteint.
			//
		}
		else
			$retour = 'NO'; // User inconnu
	}
  //
	return $retour;  // renvoie OK KO NO WAIT ou vide
}


function f_max_same_ip_already($t_max_nb_ip)
{
  global $PREFIX_IM_TABLE, $id_connect;
	//
	$retour = "OK";
	//if (!defined("_MAX_NB_IP")) define("_MAX_NB_IP", 0);
	//
	if (intval($t_max_nb_ip) > 0)
	{
    $ip_local = $_SERVER['REMOTE_ADDR'];	
    //
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
    $requete .= " WHERE SES_IP_ADDRESS = '" . $ip_local . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M4n]", $requete); //ERR-K4i
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_ses) = mysqli_fetch_row ($result);
      //
      if (intval($nb_ses) >= intval($t_max_nb_ip))
        $retour = 'KO-MAX-SESSION';
    }
  }
  //
	return $retour;
}


function f_is_password_out_of_date($t_id_user, $t_username)
{
  global $PREFIX_IM_TABLE, $id_connect;
  $retour = "OK";
  //
  if ( (_USER_NEED_PASSWORD != '') and (intval(_PASSWORD_VALIDITY) >= 10) )
  {
    $requete  = " select DATEDIFF(CURDATE(), USR_DATE_PASSWORD), USR_DATE_PASSWORD ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE ID_USER = " . $t_id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M4p]", $requete);
    if ( mysqli_num_rows($result) == 1 ) 
    {
      list ($nb_days, $last_date) = mysqli_fetch_row ($result);
      $nb_days = abs($nb_days); // pour les tricheurs...
      if ( ($nb_days > intval(_PASSWORD_VALIDITY)) or (intval($last_date) == 0) )
      {
        $retour = "Ko";
        write_log("log_password_to_old", $t_username . ";" . $last_date . ";" . $nb_days);
        //
        $requete  = " update " . $PREFIX_IM_TABLE . "SES_SESSION ";
        $requete .= " SET SES_STATUS = 0 ";
        $requete .= " WHERE ID_USER = " . $t_id_user . " ";
        //$requete .= " LIMIT 1 "; // (to protect)
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M4q]", $requete);
      }
    }
  }
  //
  return $retour;
}

?>
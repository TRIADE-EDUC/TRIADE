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
if ( (!isset($_GET['ip'])) or (!isset($_GET['v'])) or (!isset($_GET['u'])) ) die();
//
///$id_user =	    intval(f_decode64_wd($_GET['iu']));
//$id_user = 		  (intval($id_user) - intval($action));
$user = 			  f_decode64_wd($_GET['u']);
$ip = 			    f_decode64_wd($_GET['ip']);
$version =	    intval($_GET['v']);
$name = 			  f_decode64_wd($_GET['n']);
$phone = 			  f_decode64_wd($_GET['t']);
$email = 			  f_decode64_wd($_GET['e']);
$check = 		    f_decode64_wd($_GET['c']);
$passcr = 		  f_decode64_wd($_GET['p']);
$gender =			  $_GET['g'];
$user =         trim($user);
$check =        trim($check);
$ip =           trim($ip);
//
if ( ($version > 0) and ($user != "") and ($check != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  //require ("../common/sessions.inc.php");
  require ("../common/extern/extern.inc.php");
  //
  // Only for register new user !
  if (f_get_id_nom_user($user) > 0)
	{
    echo ">F79#KO#ALREADY###";
    die();
  }
  //
  // Do no use this script on external authentication !
  if (f_nb_auth_extern() == 1)
	{
    echo ">F79#KO#AUTH-EXT###";
    die();
  }
  //
  $email  = trim(strtolower($email));
  $name   = f_clean_name($name);
  $gender = trim($gender);
  if (!preg_match('/^[-a-z0-9._@]+$/i', $email) ) $email = "";
  if (!preg_match('/^[+0-9.()]+$/i', $phone) ) $phone = "";
  //
  if (_ALLOW_CHANGE_EMAIL_PHONE == "")
  {
    $phone = "";
    $email = "";
    $gender = "";
  }
  else
  {
    // on censure les mots interdits par l'administrateur :
    if (_CENSOR_MESSAGES != '')
    {
      if (is_readable("../common/censure.txt"))
      {
        require ("../common/words_filtering.inc.php");
        $email = textCensure($email, "../common/config/censure.txt");
        $name  = textCensure($name,  "../common/config/censure.txt");
      }
    }
  }
  if (_ALLOW_CHANGE_FUNCTION_NAME == "") $name = "";
  if ($name == "HIDDEN") $name = "";
  //
  // Username/nickname :
  $nickname = f_clean_name($user);
  $user = f_clean_username($user);
  if (function_exists('mysqli_real_escape_string'))  
  {
    if (mysqli_real_escape_string($id_connect, "test") == "test")
    {
      $user = mysqli_real_escape_string($id_connect, $user);
      $nickname = mysqli_real_escape_string($id_connect, $nickname);
      $passcr = mysqli_real_escape_string($id_connect, $passcr);
    }
  }
  //
  if (strlen($user) < 3) die (">F79#KO#" . "USERNAME:NOT-OK#" . $l_start_short_username);
  if ( (strlen($nickname) < 3) or (_ALLOW_UPPERCASE_SPACE_USERNAME == '') ) $nickname = $user;
  if ( (intval(_MINIMUM_USERNAME_LENGTH) > 1) and (strlen($user) < _MINIMUM_USERNAME_LENGTH) ) die (">F79#KO#" . "USERNAME:NOT-OK#" . $l_start_short_username);
  //
  $cleanusername = f_DelSpecialChar($user);
  if ( (strstr($cleanusername, 'admin')) or (strstr($cleanusername, 'root')) or (strstr($cleanusername, 'system')) or ($user == 'Cancel-X') )
  {
    write_log("log_reject_username", $user . ";" . $check . ";" . $ip . ";" . $version );
    die (">F79#KO#" . "USERNAME:NOT-OK#");
  }
  //
  if (f_is_banned_user_ip_pc($cleanusername, "U"))
  {
    write_log("log_reject_username", $user . ";" . $check . ";" . $ip . ";" . $version );
    die (">F79#KO#". "USERNAME:NOT-OK#");
  }
  //
  //
	if (_USER_NEED_PASSWORD != '') 
	{
    if ($passcr == '')
    {
      echo ">F79#KO#PASS###";
      die();
    }
    require("../common/config/auth.inc.php");
    $passcr = sha1($password_pepper . $passcr . "W$*7B0-c6");
  }
  //
  //
  if (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != '')
	{
    $tt = f_if_already_max_nb_users();
		if (intval($tt) == 0)
		{
      $usr_status = 1;
      if (_PENDING_NEW_AUTO_ADDED_USER != '')
      {
        $usr_status = 2; // locked (ajouté, mais reste en attente)
        if (_SEND_ADMIN_ALERT != "")
        {
          $txt = $l_start_waiting_valid;
          if ($txt == "") $txt = "Pending new user(s) waiting...";
          send_alert_message_to_admins($txt);
        }
      }
      if ($nickname == $user) $nickname = "";
      //
      $requete  = " insert into " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " (USR_USERNAME, USR_NICKNAME, USR_CHECK, USR_DATE_CREAT, USR_PASSWORD, USR_NAME, USR_STATUS, USR_PHONE, USR_EMAIL, USR_GENDER) VALUES (";
      $requete .= " '" . $user . "' , '" . $nickname . "' , '" . $check . "' , CURDATE() , '" . $passcr . "' , '" . $name . "', " . $usr_status . ", ";
      $requete .= " '" . $phone . "' , '" . $email . "' , '" . $gender . "' )";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-79b]", $requete);
      write_log("log_user_create", $user . ";" . $check);
      //
      echo ">F79#OK####";
		}
		else
		{
      echo ">F79#KO#MAX###"; // nbre de user max atteint.
      write_log("log_reject_max_users", $user . ";" . $check . ";" . $ip . ";" . $version );
      die();
		}
	}
  else
	{
    echo ">F79#KO#NO###";
    die();
  }
  //
  mysqli_close($id_connect);
}
?>
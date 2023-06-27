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
//
if ( (!isset($_GET['un'])) or (!isset($_GET['ip'])) or (!isset($_GET['c'])) or (!isset($_GET['p'])) or (!isset($_GET['v'])) ) die();
//
$username = 	  f_decode64_wd($_GET['un']);
$ip = 			    f_decode64_wd($_GET['ip']);
$check = 		    f_decode64_wd($_GET['c']);
$pass = 		    $_GET['p'];
$version = 		  $_GET['v'];
$n_version =    intval($_GET['v_n']);
$language =     $_GET['ln'];
$hr =           $_GET['hr'];
$mn =           $_GET['mn'];
$dt_m =         $_GET['dt_m'];
$dt_j =         $_GET['dt_j'];
$os =           $_GET['os'];
if (isset($_GET['pc'])) $computername = $_GET['pc']; else $computername = "";
if (isset($_GET['rs'])) $screensize = $_GET['rs']; else $screensize = "";
if (isset($_GET['br'])) $browser = $_GET['br']; else $browser = "";
if (isset($_GET['ml'])) $mailreader = $_GET['ml']; else $mailreader = "";
if (isset($_GET['mc'])) $mac_adr = $_GET['mc']; else $mac_adr = "";
if (isset($_GET['oo'])) $ooo = $_GET['oo']; else $ooo = "";
$check = trim($check);
$ip = trim($ip);
//$domain = $_SERVER['SERVER_NAME'];
//

if ( ($n_version > 30) and ($version != '') and ($username != "") and ($check != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/extern/extern.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/user_start.inc.php");
  require ("lang.inc.php"); // important !
  prevent_error_extern_option_missing();
  prevent_error_option_missing();
  //

  #write_log("test", $username . ";" . $check . ";" . $ip . ";" . $version );

  if (!ctype_alnum($language)) $language = ""; // après functions.inc.php !
  //
  $add = ">F05#KO#";
  //
  $nickname = trim($username);
  $nickname = f_clean_name($nickname);
  $username = f_clean_username($username);
  $pass = f_decode64_wd($pass);
  if (function_exists('mysqli_real_escape_string'))  
  {
    if (mysqli_real_escape_string($id_connect, "test") == "test")
    {
      $nickname = mysqli_real_escape_string($id_connect, $nickname);
      $username = mysqli_real_escape_string($id_connect, $username);
      $pass = mysqli_real_escape_string($id_connect, $pass);
    }
  }
  if (strlen($username) < 2) die ($add . $l_start_short_username);
  if ( (strlen($nickname) < 2) or (_ALLOW_UPPERCASE_SPACE_USERNAME == '') ) $nickname = $username;
  if ( (intval(_MINIMUM_USERNAME_LENGTH) > 1) and (strlen($username) < _MINIMUM_USERNAME_LENGTH) ) die ($add . $l_start_short_username);
  //
  $cleanusername = f_DelSpecialChar($username);
  if ( (strstr($cleanusername, 'admin')) or (strstr($cleanusername, 'root')) or (strstr($cleanusername, 'system')) or ($username == 'Cancel-X') )
  {
    write_log("log_reject_username", $username . ";" . $check . ";" . $ip . ";" . $version );
    die ($add . "USERNAME:NOT-OK#");
  }
  //
  // Username/nickname :
  if (f_is_banned_user_ip_pc($cleanusername, "U"))
  {
    write_log("log_reject_username", $username . ";" . $check . ";" . $ip . ";" . $version );
    die ($add . "USERNAME:NOT-OK#");
  }
  // IP Address :
  if (f_is_banned_user_ip_pc($ip, "I"))
  {
    write_log("log_reject_ip", $username . ";" . $check . ";" . $ip . ";" . $version );
    die ($add . "PC:NOT-OK#");
  }
  // Computer :
  if (f_is_banned_user_ip_pc($check, "P"))
  {
    write_log("log_reject_pc", $username . ";" . $check . ";" . $ip . ";" . $version );
    die ($add . "PC:NOT-OK#");
  }
  //
  if ( strlen(_PASSWORD_FOR_PRIVATE_SERVER) > 5 )
  {
    $p1 = "";
    $p2 = "";
    if (isset($_GET['prc'])) $p1 = f_decode64_wd($_GET['prc']);
    if (isset($_GET['pcr'])) $p2 = f_decode64_wd($_GET['pcr']);
    $private_pass = $p1 . $p2;
    if (_PASSWORD_FOR_PRIVATE_SERVER != $private_pass)
    {
      write_log("error_acces_private_log", $username . ";" . $check . ";" . $ip . ";" . $version . ";" . "Pass : " . $private_pass  );
      die ($add . "PRV#");
    }
  }
  //
  //
  // if change server
  if (defined("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL"))
  { 
    if (strlen(_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL) > 10)
    {
      die (">F05#URL#NEW#" . f_encode64(_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL) . "###" );
    }
  }
  //
  $new_user = "";
  $id_user = intval(f_get_id_nom_user($username));
  if ($id_user <= 0) $new_user = "N";
  //
  //
  $t_max_nb_ip = _MAX_NB_IP;
  $t_lock_duration = _LOCK_DURATION;
  $t_max_pass_errors_before_lock = _MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER;
  $t_pending_user_on_computer_change = _PENDING_USER_ON_COMPUTER_CHANGE;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_max_nb_ip = f_role_permission($id_role, "MAX_NB_IP", _MAX_NB_IP);
      $t_lock_duration = f_role_permission($id_role, "LOCK_DURATION", _LOCK_DURATION);
      $t_max_pass_errors_before_lock = f_role_permission($id_role, "MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", _MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER);
      $t_pending_user_on_computer_change = f_role_permission($id_role, "PENDING_USER_ON_COMPUTER_CHANGE", _PENDING_USER_ON_COMPUTER_CHANGE);
    }
  }
  //
  //
  $ret = f_verif_check_user_creat($username, $id_user, $check, $pass, $n_version, $nickname, $t_max_nb_ip, $t_pending_user_on_computer_change);
  switch ($ret)
  {
    case "OK" :
      close_session_id_user($id_user);
      $ret = f_max_same_ip_already($t_max_nb_ip);
      if ($ret != "OK")  
      {
        write_log("log_reject_max_same_ip", $username . ";" . $check . ";" . $ip . ";" . $version );
        die ($add . "KO-MAX-SESSION#IP#");
      }
      break;
#    case "" : // si valeur de controle non renseignée, on la récupère.
#      update_check_user($id_user, $check);
#      break;
    case "NO" :
      sleep(rand(2,5));
      write_log("log_reject_username_unknown", $username . ";" . $check . ";" . $ip . ";" . $version );
      die ($add . "USERNAME:UNKNOW#");
      break;
    case "WAIT" :
      die ($add . "KO-WAIT#");
      break;
    case "KO" :
      die ($add . "KO-ADMIN-CHECK#");
      break;
    case "KO-OTHER-PC" :
      die ($add . "KO-OTHER-PC#");
      break;
    case "KO-PASS" :
      //inc_user_password_error($id_user, $username, $check, $version);
      inc_user_password_error($id_user, $username, $check, $t_max_pass_errors_before_lock, $t_lock_duration);
      sleep(rand(2,5));
      die ($add . "KO-PASS#");
      break;
    case "KO-MAX" : // cannot create new user
      sleep(rand(2,5));
      write_log("log_reject_max_users", $username . ";" . $check . ";" . $ip . ";" . $version );
      die ($add . "KO-MAX#");
      break;
    case "KO-MAX-SESSION" : 
      sleep(rand(2,5));
      write_log("cannot_acces_server_full", $username . ";" . $check . ";" . $ip . ";" . $version );
      die ($add . "KO-MAX-SESSION#");
      break;
    case "KO-DUPLICATE-PC" : // DOUBLON
      write_log("log_reject_duplicate_pc", $username . ";" . $check . ";" . $ip . ";" . $version );
      die ($add . "KO-DUPLICATE-PC#KO-MAX-SESSION#");
      break;
    case "KO-AUTH-EXT" :
      sleep(rand(2,5));
      //inc_user_password_error($id_user, $username, $check, $version);
      inc_user_password_error($id_user, $username, $check, $t_max_pass_errors_before_lock, $t_lock_duration);
      die ($add . "KO-AUTH-EXT#");
      break;
    case "KO-NEED_REGISTER" :
      die ($add . "KO-NEED_REGISTER#");
      break;
    default : // bug
      die ($add . "Server problem ?");
      break; 
  }
  //
  // arrivé ici, on a le droit d'ouvrir une session
  //
  $id_user = 0;
  $requete  = " select ID_USER, USR_NAME, USR_VERSION, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, USR_TIME_SHIFT, USR_GET_ADMIN_ALERT, ";
  $requete .= " USR_MAC_ADR, USR_COMPUTERNAME, USR_SCREEN_SIZE, USR_EMAIL_CLIENT, USR_BROWSER, USR_OOO ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE USR_USERNAME = '" . $username . "' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-04a]", $requete);
  if ( mysqli_num_rows($result) == 1 ) 
  {
    list ($id_user, $name, $usr_version, $usr_country_code, $usr_language, $usr_time_shift, $usr_get_admin_alert, $usr_pc ,$usr_screensize ,$usr_browser ,$usr_mailreader ,$usr_mac_adr ,$usr_ooo) = mysqli_fetch_row ($result);
  }
  //
  if ($id_user > 0)
  {
    if (defined("_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN"))
    { 
      if (strlen(_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN) > 15)
      {
        echo ">F05#URL#NEW#" . f_encode64(_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN) . "###";
        die();
      }
    }
    //
    $id_max_msg_sbx = 0;
    if (_SHOUTBOX != "")
    {
      require ("../common/shoutbox.inc.php");
      $id_max_msg_sbx = f_shoutbox_last_id_if_new(0);
    }
    //
    $for_time_zones = "";
    if (_TIME_ZONES != "") $for_time_zones = date("H:i:s");
    //
    // donc on commence par supprimer les éventuelles sessions précédentes (avant dans ouvrir une nouvelle).
    close_session_id_user($id_user);
    //
    // on ajoute une nouvelle session
    open_session_user($id_user, $username, $os, $check);
    //
    $id_session = f_get_id_session_id_user($id_user);
    //
    //
    // Une fois la session ouverte (indispensable pour chang pass !), on vérifie si le mot de passe a expiré :
    if (f_is_password_out_of_date($id_user, $username) <> "OK")
    {
      die ($add . "KO-PASS-TO-OLD#" . f_encode64($id_user) . "#" . f_encode64($id_session) . "#");
    }
    //
    //
    //
    //
    // on renvoi les valeurs pour confirmer l'ouverture de session.
    echo ">F05#" . f_encode64($id_session) . "#" . f_encode64($id_user) . "#" . $for_time_zones . "#" . date("d")  . "#" . f_encode64($id_max_msg_sbx) . "#";
    //
    if ($usr_get_admin_alert == 1) echo "A";
    if ($new_user == "N") echo "N";
    echo "#"; // séparateur !
    //
    echo "########"; // for next options...
    //
    //
    // on supprime sa participation aux éventuelles conférences (périmées pour lui).
    if (_ALLOW_CONFERENCE != '')
    {
      $requete = "DELETE FROM " . $PREFIX_IM_TABLE . "USC_USERCONF WHERE ID_USER = '" . $id_user . "' ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-04b]", $requete);
    }
    //
    // Compter le nombre de messages admin déja en attente :
    $tmp_nb_msg_admin = 0;
    $requete  = " SELECT COUNT(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
    $requete .= " WHERE ID_USER_AUT = -99 and ID_USER_DEST = " . $id_user . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-04p]", $requete);
    if ( mysqli_num_rows($result) == 1 ) 
    {
      list ($tmp) = mysqli_fetch_row ($result);
      $tmp_nb_msg_admin = $tmp;
    }
    //
    // Si aucun message, on peut envoyer une éventuelle info 
    if (intval($tmp_nb_msg_admin) <= 0 )
    {
      // si recoit les alert (admin)
      if ($usr_get_admin_alert == 1)
      {
        // 1 : Waiting users
        $msg_admin = "";
        $nb_wait_user = 0;
        $requete  = " SELECT count(*) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " WHERE USR_STATUS = 2 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-04c]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($nb_wait_user) = mysqli_fetch_row ($result);
          if (intval($nb_wait_user) > 0) $msg_admin = $l_start_waiting_valid . chr(13);
        }
        //
        // 2 : waiting avatars
        $repert = "../" . _PUBLIC_FOLDER . "/upload/";
        if (is_dir($repert)) 
        {
          $rep = opendir($repert);
          $tab_files = array(); // on déclare le tableau contenant le nom des fichiers
          while ($file = readdir($rep))
          {
            if ($file != ".." && $file != "." && $file !="" ) // .inc.php && strpos(strtolower($file), ".*") 
            {
              $ext = strtolower(substr($file,-5));
              if ( (!is_dir($file)) and (strlen($file) <= 20) and ( (strpos($ext, ".png")) or (strpos($ext, ".gif")) or (strpos($ext, ".jpg")) or (strpos($ext, ".jpeg")) ) )
              {
                $tab_files[] = $file;
              }
            }
          }
          closedir($rep);
          //
          if (!empty($tab_files))
          {
            $msg_admin .= $l_index_pending_avatars . "..." . chr(13);
          }
        }
        //
        // 3 : Shoutbox pending message
        if ( (_SHOUTBOX != "") and (_SHOUTBOX_NEED_APPROVAL != "") )
        {
          $requete  = " SELECT count(*) ";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
          $requete .= " WHERE SBX_DISPLAY < 1 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-04q]", $requete);
          if ( mysqli_num_rows($result) == 1 )
          {
            list ($nb_wait_sbx) = mysqli_fetch_row ($result);
            if (intval($nb_wait_sbx) > 0) $msg_admin .= $l_index_shoutbox_pending . "...";
          }
        }
        //
        // 4 : send message
        if ($msg_admin != "")
        {
          $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
          $requete .= " VALUES (-99, " . $id_user . ", '" . f_encode64($msg_admin) . "', '64', CURTIME(), CURDATE() ) ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-04d]", $requete);
        }
      }
      else
      {
        if ($n_version < 43) // (43 au 25/02/16, 39 au 15/07/13, 33 au 21/06/12, 31 au 12/02/11)
        {
          $txt = "Get the latest version of IntraMessenger at www.theuds.com";
          $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_TIME, MSG_DATE) ";
          $requete .= " VALUES (-99, " . $id_user . ", '" . $txt . "', CURTIME(), CURDATE() ) ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-04e]", $requete);
        }
        else
        {
          if ( (_USER_NEED_PASSWORD == "") and (_FORCE_USERNAME_TO_PC_SESSION_NAME == "") and (_PASSWORD_FOR_PRIVATE_SERVER == "") and (_PENDING_USER_ON_COMPUTER_CHANGE == "") )
          {
            $txt = "Welcome on this TEST server (public and no password required for authentication)";
            $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_TIME, MSG_DATE) ";
            $requete .= " VALUES (-99, " . $id_user . ", '" . $txt . "', CURTIME(), CURDATE() ) ";
            $result = mysqli_query($id_connect, $requete);
            if (!$result) error_sql_log("[ERR-04f]", $requete);
          }
          //
          //
          if ( ($_SERVER['SERVER_NAME'] == 'www.intramessenger.com') or ($_SERVER['SERVER_NAME'] == 'www.theuds.com') ) // or ($_SERVER['SERVER_NAME'] == 'www.intramessenger.net')
          {
            if ($language == "FR")
              $txt = "Bienvenue sur ce serveur de démonstration..."; // /tests
            else
              $txt = "Welcome on this DEMO server..."; //  TEST/ (if you like it, install it your own one !)
            //
            $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_TIME, MSG_DATE) ";
            $requete .= " VALUES (-99, " . $id_user . ", '" . $txt . "', CURTIME(), CURDATE() ) ";
            $result = mysqli_query($id_connect, $requete);
            if (!$result) error_sql_log("[ERR-04f]", $requete);
          }
        }
      }
    }
    //
    if ($usr_version != $version)
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
      $requete .= " set USR_VERSION = '" . $version . "' ";
      $requete .= " WHERE ID_USER = " . $id_user . " ";
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-04g]", $requete);
    }
    //
    if (strlen($language) == 2)
    {
      if ($language == 'EN') $language = 'GB';
      //if ($language == 'DE') $language = 'GE';
      if ($language != $usr_language)
      {
        $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
        $requete .= " set USR_LANGUAGE_CODE = '" . $language . "' ";
        $requete .= " WHERE ID_USER = " . $id_user . " ";
        $requete .= " LIMIT 1 "; // (to protect)
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-04h]", $requete);
      }
    }
    //
    if (_FLAG_COUNTRY_FROM_IP != "")
    {
      if ( (is_readable("../common/library/geoip/geoip.inc")) and (is_readable("../common/library/geoip/GeoIP.dat")) )
      {
        $ip_distant = $_SERVER['REMOTE_ADDR'];	
        require("../common/library/geoip/geoip.inc");
        $gi = geoip_open("../common/library/geoip/GeoIP.dat",GEOIP_MEMORY_CACHE);
        $country_code = geoip_country_code_by_addr($gi, $ip_distant);
        geoip_close($gi);
        $country_code = trim($country_code);
        if ( ($country_code != "") and ($country_code != $usr_country_code) )
        {
          $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
          $requete .= " set USR_COUNTRY_CODE = '" . $country_code . "' ";
          $requete .= " WHERE ID_USER = " . $id_user . " ";
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-04i]", $requete);
        }
      }
    }
    //
    // Time shit
    if (_TIME_ZONES != "")
    {
      if ( ($hr != '') and ($mn != '') )
      {
        $hr = intval($hr);
        $mn = intval($mn);
        $dt_j = intval($dt_j);
        $hr_s = date("H");
        $mn_s = date("i");
        $dt_j_s = date("j");
        $decal_hr = ($hr - $hr_s);
        $decal_mn = ($mn - $mn_s);
        if ( ($dt_j > 0) and ($dt_j <= 31) ) // pour ne pas passer de +4 à -20 (si connexion le soir, ou contraire le matin).
        {
          if ($dt_j_s <> $dt_j)
          {
            if ($hr_s > $hr)
              $decal_hr = ($decal_hr + 24);
            else
              $decal_hr = ($decal_hr - 24);
          }
        }
        if ( (abs($decal_mn) < 20) or (abs($decal_mn) > 40) ) 
          $decal_mn = 0;
        else
        {
          // on arrondi à la demi-heure (half hour)
          if ($decal_mn < 0) $decal_mn = -5;
          if ($decal_mn > 0) $decal_mn =  5;
        }
        $decal = intval( ($decal_hr * 10) + $decal_mn); // +03:30 -> 3.5 * 10 -> 35 
        //
        if ($decal <> $usr_time_shift)
        {
          $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
          $requete .= " set USR_TIME_SHIFT = " . $decal . " ";
          $requete .= " WHERE ID_USER = " . $id_user . " ";
          $requete .= " LIMIT 1 "; // (to protect)
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-04k]", $requete);
        }
      }
    }
    //
    if (_ENTERPRISE_SERVER != "")
    {
      $computername = f_decode64_wd($computername);
      $screensize = f_decode64_wd($screensize);
      $browser = f_decode64_wd($browser);
      $mailreader = f_decode64_wd($mailreader);
      $mac_adr = f_decode64_wd($mac_adr);
      $mac_adr = str_replace("-", "", $mac_adr);
      $ooo = f_decode64_wd($ooo);
      if ( ($usr_pc != $computername) or ($usr_screensize != $screensize) or ($usr_browser != $browser) or ($usr_mailreader != $mailreader) or ($usr_mac_adr != $mac_adr) or ($usr_ooo != $ooo) )
      {
        $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
        $requete .= " set USR_MAC_ADR = '" . $mac_adr . "', ";
        $requete .= " USR_COMPUTERNAME = '" . $computername . "', ";
        $requete .= " USR_SCREEN_SIZE = '" . $screensize . "', ";
        $requete .= " USR_EMAIL_CLIENT = '" . $mailreader . "', ";
        $requete .= " USR_BROWSER = '" . $browser . "', ";
        $requete .= " USR_OOO = '" . $ooo . "'  ";
        $requete .= " WHERE ID_USER = " . $id_user . " ";
        $requete .= " LIMIT 1 "; // (to protect)
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-04m]", $requete);
      }
    }
    //
    //
    clean_inactives_session();
  }
  else
    die ($add . $l_start_cannot_authenticate . ", " . $l_start_no_find_iduser);
  //
  mysqli_close($id_connect);
}
?>
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
if ( (!isset($_GET['u1'])) or (!isset($_GET['u2'])) or (!isset($_GET['m'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_u_1 =	    intval(f_decode64_wd($_GET['u1']));
$id_u_1 = 		(intval($id_u_1) - intval($action));
$id_u_2 = 		intval(f_decode64_wd($_GET['u2']));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
$msg =        $_GET['m'];
if (isset($_GET['cr'])) $cr = $_GET['cr']; else $cr = ""; // optionnel (peut être vide, donc non déclaré)
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_u_1)) $id_u_1 = "";
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
//
if ( ($id_u_1 > 0) and ($id_u_2 > 0) and ($n_version > 0) and ($msg != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require("lang.inc.php"); // pour format date et heure.
  //
  //if ( (f_verif_id_session_id_user($id_u_1, $id_session) != 'OK') or (f_verif_id_session_id_user($id_u_2, $id_ses2) != 'OK') )
  // ne plus tester la session, mais juste si 'online' (en cas de changement rapide de session)
  //
  if (f_check_session_id_user($id_u_1, $session_chk, $action) != 'OK')  die(">F40#KO#1#"); // 1:session non ouverte.
  //
  if ($id_u_2 <= 0)     die(">F40#KO#6#"); // 6:pas de destinataire.
  //
  //
  $t_allow_send_to_offline_user = _ALLOW_SEND_TO_OFFLINE_USER;
  $t_allow_invisible = _ALLOW_HIDDEN_TO_CONTACTS;
  $t_censor_messages = _CENSOR_MESSAGES;
  $t_log_messages = _HISTORY_MESSAGES_ON_ACP;
  $t_role_srv_offline_mode_2 = "";
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_u_1);
    //
    if ($id_role > 0)
    {
      $t_allow_send_to_offline_user = f_role_permission($id_role, "ALLOW_SEND_TO_OFFLINE_USER", _ALLOW_SEND_TO_OFFLINE_USER);
      $t_allow_invisible = f_role_permission($id_role, "ALLOW_HIDDEN_TO_CONTACTS", _ALLOW_HIDDEN_TO_CONTACTS);
      $t_censor_messages = f_role_permission($id_role, "CENSOR_MESSAGES", _CENSOR_MESSAGES);
      $t_log_messages = f_role_permission($id_role, "HISTORY_MESSAGES_ON_ACP", _HISTORY_MESSAGES_ON_ACP);
    }
    //
    //
    $id_role = f_role_of_user($id_u_2);
    if ($id_role > 0)
    {
      $t_role_srv_offline_mode_2 = f_role_permission($id_role, "ROLE_OFFLINE_MODE", ""); // c'est un role, pas une option !
    }
  }
  //
  if ($t_role_srv_offline_mode_2 != "")
  {
    die(">F40#KO#2#"); // 2:utilisateur indisponible.
  }
  //
  //
  $is_offline = "";
  if (f_get_id_session_id_user($id_u_2) == 0)
  {
    if ($t_allow_send_to_offline_user == '') 
      die(">F40#KO#1#"); // 1:session non ouverte.
    else
      $is_offline = "X";
  }
  //
  $msg_original = "";
  $msg_cr = $msg;
  if ($cr == '64') 
  {
    if ( (_CRYPT_MESSAGES == '') and ( ($t_censor_messages != '') or ($t_log_messages != '') ) )
    {
      $msg = str_replace("µ§", "/", $msg);
      $msg = f_decode64_wd($msg);
      $msg_original = $msg;
      if ($t_censor_messages != '')
      {
        $msg_cr = "";
        $cr = "";
      }
    }
  }
  else
  {
    $msg = str_replace('"', '`', $msg);
    $msg = str_replace("'", "`", $msg);
    $msg = str_replace("/", "", $msg);
  }
  $msg = trim($msg);
  //
  require ("../common/constant.inc.php");
  if ( $n_version < intval(_CLIENT_VERSION_MINI) )
  {
    die(">F40#KO#3#"); // 3:version incorrecte.
  }
  //
  $ok_send = "OK";
  $priv_u_1 = '';
  $priv_u_2 = '';
  //
  // on récupère l'état de privilège de l'auteur chez le contact destinataire :
  $requete  = " select CNT_STATUS ";
  $requete .= " from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
  $requete .= " WHERE ID_USER_1 = " . $id_u_2 . " ";
  $requete .= " and ID_USER_2 = " . $id_u_1 . " ";
  //$requete .= " and CNT_STATUS = 2 "; // si VIP chez le contact distant
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-50a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($priv_u_2) = mysqli_fetch_row ($result);
  }
  //
  if ( (_SPECIAL_MODE_OPEN_COMMUNITY == "") and (_SPECIAL_MODE_GROUP_COMMUNITY == "") and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == "") )
  {
      if (intval($priv_u_2) <= 0)
        die(">F40#KO#7#"); // 7:pas dans ses contacts.
  }
  //
  // si mode invisible autorisé, et que les offlines ne sont pas autorisés.
  #if ( ($t_allow_invisible != '') and ($t_allow_send_to_offline_user == '') )
  #
  // si mode invisible autorisé
  if ($t_allow_invisible != '')
  {
    // on récupère l'état de privilège du destinataire :
    $requete  = " select CNT_STATUS ";
    $requete .= " from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
    $requete .= " WHERE ID_USER_1 = " . $id_u_1 . " ";
    $requete .= " and ID_USER_2 = " . $id_u_2 . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-50b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($priv_u_1) = mysqli_fetch_row ($result);
    }
    //
    // si on est masqué pour le destinataire, on ne peut pas lui écrire (non mais).
    if ($priv_u_1 == '5')
    {
      echo ">F40#KO#4#";  // 4:utilisateur ne peut pas vour voir.
      $ok_send = "Ko";
    }
  }
  if ($ok_send == "OK")
  {
    $requete  = " select SES_STATUS, ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " and USR.ID_USER = " . $id_u_2 . " ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-50c]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($etat_num, $id_ses) = mysqli_fetch_row ($result);
      if ( ($etat_num != '1') and ($etat_num != '2') and ($etat_num != '3') )
      {
        if ($etat_num == '4') // si en mode ne pas dérange (rouge), on vérifie si VIP
        {
          if ($priv_u_2 != '2') // si n'est pas VIP chez le distant
            $ok_send = "Ko";
        }
        else
        {
          if ($t_allow_send_to_offline_user == '') // si on a pas autorisé à envoyer en offline.
          {
            echo ">F40#KO#2#";  // 2:utilisateur indisponible.
            $ok_send = "Ko";
          }
        }
      }
    }
    else
    {
      if ($t_allow_send_to_offline_user == '') // si on a pas autorisé à envoyer en offline.
      {
        echo(">F40#KO#1#"); // 1:session non ouverte.
        $ok_send = "Ko";
      }
      else
      {
        $get_offline = 0;
        $requete  = " select USR_GET_OFFLINE_MSG ";
        $requete .= " from " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " WHERE ID_USER = " . $id_u_2 . " ";
        $requete .= " and USR_STATUS = 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-50d]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($get_offline) = mysqli_fetch_row ($result);
        }
        if ( (intval($get_offline) <= 0) or ( (intval($get_offline) == 1) and ($priv_u_2 != '2') ) )
        {
          echo(">F40#KO#2#"); // 2:utilisateur indisponible.
          $ok_send = "Ko";
        }
        // if $get_offline == 2 : OK.
      }
    }
  }
  //
  if ($ok_send == "OK")
  {
    // on censure les mots interdits par l'administrateur :
    if ( (_CRYPT_MESSAGES == '') and ($t_censor_messages != '') and ($msg_original != "") )
    {
      if (is_readable("../common/config/censure.txt"))
      {
        require ("../common/words_filtering.inc.php");
        $msg = textCensure($msg, "../common/config/censure.txt");
      }
      //
      // si on veut s'amuser à traduire en ch'it :
      # require ("../common/words_chti.inc.php");
      # $msg = textChti($msg);
      //
    }
    //
    if ($cr == "")
    {
      $msg_cr = f_encode64($msg);
      $msg = str_replace("/", "µ§", $msg);
      $cr = "64";
    }
    if ($cr == "AA")
    {
      if ($n_version >= 26) $cr = "BB";
    }
    //
    $requete = "INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE) ";
    //$requete .= "VALUES (" . $id_u_1 . ", " . $id_u_2 . ", '" . $msg_cr . "', '" . $cr . "', CURTIME(), CURDATE() ) ";
    $requete .= "VALUES (" . $id_u_1 . ", " . $id_u_2 . ", '" . $msg_cr . "', '" . $cr . "', '" . date("H:i:s") . "', CURDATE() ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-50e]", $requete);
    //
    echo ">F40#OK#" . date("H:i:s") . "#"; // message bien envoyé
    if ( ($msg_original != "") and ($msg != $msg_original) ) echo $msg; // si le texte a été modifié, on le renvoit pour l'afficher tel que réellement envoyé.
    echo "#";
    if ($is_offline != "") echo "OL"; // OffLine
    echo "####";
    //
    //
    // si option de log (archivage) des messages échangé activé :
    if ( ($t_log_messages != '') and (_CRYPT_MESSAGES == '') )
    {
      $msg = str_replace("%20", " ", $msg);
      //
      // on récupère le username expéditeur :
      $username_1 = f_get_username_of_id($id_u_1);
      // on récupère le username destinataire :
      $username_2 = f_get_username_of_id($id_u_2);
      //
      $ip = $_SERVER['REMOTE_ADDR'];	
      //$username_and_domaine = gethostbyaddr("$ip") . ";";   //. gethostbyaddr("");
      $plus = $ip ; // .";". $username_and_domaine ;
      //
      $chemin = "log/" . "messages_log.txt" ;
      $fp = fopen($chemin, "a");
      if (flock($fp, 2));
      {
        //fputs($fp,date("d/m/Y;H:i:s") . ";" . $username_1 . ";" . $username_2 . ";" . $msg . ";" . $plus ."\r\n");
        fputs($fp,date($l_date_format_display . ";" . $l_time_format_display) . ";" . $username_1 . ";" . $username_2 . ";" . $msg . ";" . $plus ."\r\n");
      }
      flock($fp, 3);
      fclose($fp);
    }
    //
    update_last_activity_user($id_u_1);
    //
    if (_STATISTICS != "")
    {
      if (!function_exists('stats_inc')) require ("../common/stats.inc.php");
      stats_inc("STA_NB_MSG"); // nb messages send by days
    }
    //
  }
  else
    die(">F40#KO#0#"); // 0: car on ne sait pas pourquoi (au cas où)...
  //
  mysqli_close($id_connect);
}
?>
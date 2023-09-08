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
if ( (!isset($_GET['u'])) or (!isset($_GET['id'])) or (!isset($_GET['m'])) or (!isset($_GET['ip'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['u']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$msg = 			  $_GET['m'];
$version =    intval($_GET['v']);
$id_conf = 		intval($_GET['id']);
if (isset($_GET['cr'])) $cr = $_GET['cr']; else $cr = "";
if (isset($_GET['gp'])) $group = $_GET['gp']; else $group = "";
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $id_conf)) $id_conf = "";
//
if ( ($id_user > 0) and ($version > 0) and ($msg != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F40#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_allow_conference = _ALLOW_CONFERENCE;
  $t_censor_messages = _CENSOR_MESSAGES;
  $t_log_messages = _HISTORY_MESSAGES_ON_ACP;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_conference = f_role_permission($id_role, "ALLOW_CONFERENCE", _ALLOW_CONFERENCE);
      $t_censor_messages = f_role_permission($id_role, "CENSOR_MESSAGES", _CENSOR_MESSAGES);
      $t_log_messages = f_role_permission($id_role, "HISTORY_MESSAGES_ON_ACP", _HISTORY_MESSAGES_ON_ACP);
    }
  }
  //
  if ($t_allow_conference  == '')
    die(">F40#KO#5#");
  //
  //
  $msg_original = "";
  $msg_cr = $msg;
  if ($cr == '64') 
  {
    if ( (_CRYPT_MESSAGES == '') and ( ($t_censor_messages != '') or ($t_log_messages != '') ) )
    {
      $msg = f_decode64_wd($msg);
      $msg_original = $msg;
      $msg_cr = "";
      $cr = "";
    }
  }
  else
  {
    $msg = str_replace("'", "`", $msg);
    $msg = str_replace('"', '`', $msg);
    $msg = str_replace("/", "", $msg);
  }
  $msg = trim($msg);
  //
  if ($group != "") $group = f_decode64_wd($group);
  //
  $ok_send = "";
  //
  if ($id_conf > 0)
  {
    // on vérifie que l'auteur est bien dans la conférence, et actif.
    $requete  = " select ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
    $requete .= " WHERE ID_CONFERENCE = " . $id_conf ;
    $requete .= " AND ID_USER = " . $id_user . " ";
    $requete .= " AND USC_ACTIVE = 1 "; // 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-63a]", $requete);
    if ( mysqli_num_rows($result) == 1 ) // normalement pas plus...
    {
      // On vérifié aussi qu'il y a au moins une autre personne présente (active).
      $requete  = " select count(ID_USER) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
      $requete .= " WHERE ID_CONFERENCE = " . $id_conf ;
      $requete .= " AND ID_USER <> " . $id_user . " ";
      $requete .= " AND USC_ACTIVE = 1 "; // 
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-63b]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($nb_p) = mysqli_fetch_row ($result);
        if (intval($nb_p) > 0)
        {
          $ok_send = "OK";
        }
      }
    }
  }
  else
  {
    if ($group != '') $ok_send = "OK";
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
    }
    //
    if ($cr == '') 
    {
      $msg_cr = f_encode64($msg);
      $cr = "64";
    }
    //
    //
    if ( ($id_conf > 0) and ($group == '') )
    {
      // on défile tous les destinataires (en vérifiant que leur session est toujours valide)
      $requete  = " select distinct(USC.ID_USER) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USC_USERCONF USC, " . $PREFIX_IM_TABLE . "SES_SESSION SES";
      $requete .= " WHERE SES.ID_USER = USC.ID_USER ";
      $requete .= " AND USC.ID_USER <> " . $id_user . " ";
      $requete .= " AND USC.ID_CONFERENCE = " . $id_conf ;
      $requete .= " AND USC.USC_ACTIVE = 1 "; // 
      //$requete .= " AND SES.SES_STATUS in (1, 2, 3, 4) ";
      $requete .= " AND SES.SES_STATUS in (1, 2, 3) ";
    }
    else
    {
      if ( (_SPECIAL_MODE_GROUP_COMMUNITY != "") or (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != "") )
      {
        $requete  = " select distinct(USG.ID_USER) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
        $requete .= " WHERE SES.ID_USER = USG.ID_USER ";
        $requete .= " AND USG.ID_GROUP = GRP.ID_GROUP ";
        $requete .= " AND GRP.GRP_NAME = '" . $group . "' ";
        $requete .= " AND USG.ID_USER <> " . $id_user . " ";
        //$requete .= " AND SES.SES_STATUS in (1, 2, 3, 4) ";
        $requete .= " AND SES.SES_STATUS in (1, 2, 3) ";
      }
      else
      {
        $requete  = " select distinct(CNT.ID_USER_2) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "SES_SESSION SES";
        $requete .= " WHERE SES.ID_USER = CNT.ID_USER_2 ";
        $requete .= " AND CNT.ID_USER_1 = " . $id_user . " ";
        $requete .= " AND CNT.CNT_USER_GROUP = '" . $group . "' ";
        //$requete .= " AND SES.SES_STATUS in (1, 2, 3, 4) ";
        $requete .= " AND SES.SES_STATUS in (1, 2, 3) ";
      }
    }
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-63c]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_u_2) = mysqli_fetch_row ($result) )
      {
        $requete2 = "INSERT INTO " . $PREFIX_IM_TABLE . "MSG_MESSAGE ( ID_USER_AUT, ID_USER_DEST, MSG_TEXT, MSG_CR, MSG_TIME, MSG_DATE, ID_CONFERENCE) ";
        $requete2 .= "VALUES (" . $id_user . ", " . $id_u_2 . ", '" . $msg_cr . "', '" . $cr . "', CURTIME(), CURDATE(), " . $id_conf . " ) ";
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-63d]", $requete2);
        //
        // si option de log (archivage) des messages échangé activé :
        if ( ($t_log_messages != '') and (_CRYPT_MESSAGES == '') )
        {
          // on récupère le username expéditeur :
          $username_1 = f_get_username_of_id($id_user);
          // on récupère le username destinataire :
          $username_2 = f_get_username_of_id($id_u_2);
          //
          $ip = $_SERVER['REMOTE_ADDR'];	
          //$username_and_domaine = gethostbyaddr("$ip") . ";";   //. gethostbyaddr("");
          $plus = $ip ; // .";". $username_and_domaine ;
          //
          //if ($c_option_log_messages_filename == '') $c_option_log_messages_filename = "messages_log.txt";
          $chemin = "log/" . "messages_log.txt" ;
          $fp = fopen($chemin, "a");
          if (flock($fp, 2));
          {
            fputs($fp,date("d/m/Y;H:i:s") . ";" . $username_1 . ";" . $username_2 . ";" . $msg . ";" . $plus ."\r\n");
          }
          flock($fp, 3);
          fclose($fp);
        }
      }
      //
      echo ">F40#OK#" . date("H:i:s") . "#"; // message bien envoyé
      //
    }
    //
    //
    if (_STATISTICS != "")
    {
      if (!function_exists('stats_inc')) require ("../common/stats.inc.php");
      stats_inc("STA_NB_MSG"); // nb messages send by days
    }
    //
  }
  else
    die(">F40#KO#5#"); // 5:Conférence terminée.
  //
  mysqli_close($id_connect);
}
?>
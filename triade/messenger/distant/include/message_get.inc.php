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
if ( (!isset($_GET['u1'])) or (!isset($_GET['u2'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_u_1 =	    intval(f_decode64_wd($_GET['u1']));
$id_u_1 = 		(intval($id_u_1) - intval($action));
$id_u_2 = 		intval(f_decode64_wd($_GET['u2']));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	  intval($_GET['v']);
// optionnel :
if (isset($_GET['dt_f'])) $dt_f = $_GET['dt_f'];  else  $dt_f = "";  
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
$user_2 = "";
if (isset($_GET['u3']))  $user_2 = f_decode64_wd($_GET['u3']);
$user_2  = f_clean_name($user_2);
//
$id_conf = 0;
if (isset($_GET['ic'])) $id_conf = intval(f_decode64_wd($_GET['ic']));
//
if (preg_match("#[^0-9]#", $id_u_1)) $id_u_1 = "";
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
//
if ( ($id_u_1 > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (!ctype_alnum($dt_f))    $dt_f = "";
  //
  if (f_check_session_id_user($id_u_1, $session_chk, $action) != 'OK')  die(">F42#KO#1#a#"); // 1:session non ouverte.
  //
  //
  // MODE Conférence
  //
  if ( ($id_conf > 0) and ($id_u_2 <= 0) )
  {
    // on vérifie que le receveur est bien dans la conférence, et actif.
    $requete  = " select ID_USER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
    $requete .= " WHERE ID_CONFERENCE = " . $id_conf;
    $requete .= " AND ID_USER = " . $id_u_1;
    $requete .= " AND USC_ACTIVE = 1 "; // 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-51a]", $requete);
    if ( mysqli_num_rows($result) == 1 ) // normalement pas plus...
    {
      $requete  = " SELECT MSG_TEXT, MSG_TIME, MSG_DATE, ID_MESSAGE, ID_USER_AUT, MSG_CR ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
      $requete .= " where ID_CONFERENCE = " . $id_conf ; // message de conférence.
      $requete .= " and ID_USER_DEST = " . $id_u_1;
      $requete .= " order by ID_MESSAGE ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-51b]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        list ($msg, $heure, $dt_msg, $id_msg, $id_u_2, $cr) = mysqli_fetch_row ($result);  // on ne parcours pas pour n'en afficher qu'un à la fois.
        $dt_form = "d/m/Y"; // FR
        if ($dt_f == 'EN') $dt_form = "m-d-Y";
        $dt_msg = date($dt_form, strtotime($dt_msg));
        if ($cr == "")
        {
          $cr = "64";
          $msg = f_encode64($msg);
        }
        //$user_2 = f_get_username_of_id($id_u_2); // obligé en message de conférence.
        if ( f_is_deja_in_contacts_id($id_u_1, $id_u_2) <= 0 ) $id_u_2 = f_get_username_of_id($id_u_2); // conférence, hors contact.
        //
        // si expédié un autre jour (peut propable en conférence)
        if ( $dt_msg != date($dt_form) )
          echo ">F42#OK#" . $msg . "#" . $dt_msg . "#" . f_encode64($id_u_2) . "#" . $cr . "##"; 
        else
          echo ">F42#OK#" . $msg . "#" . $heure  . "#" . f_encode64($id_u_2) . "#" . $cr . "##"; 
        //
        // on efface le message dans la foulée qu'on la envoyé
        $requete  = " delete from " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
        $requete .= " where ID_MESSAGE = " . $id_msg . " ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-51c]", $requete);
      }
      else
      {
        echo ">F42#OK###"; // pas de message 
      }
    }
  }
  else
  {
    //
    // MODE dialogue direct (hors conférence)
    //
    if ($id_u_2 <= 0) // si expediteur offline
    {
      if ($user_2 != '')
      {
        // on recherche l'id par rapport au pseudo
        $id_u_2 = f_get_id_nom_user($user_2);
        // si pas trouvé le username, ou essai avec le pseudo
        if ($id_u_2 <= 0)
          $id_u_2 = f_get_id_of_renamed_nickname($user_2);
        //
        // par mesure de sécurité, vu que c'est pour récupérer un message offline, il vérifie qu'il est bien offline
        if (intval(f_get_id_session_id_user($id_u_2)) > 0)
          $id_u_2 = 0;
      }
    }
    //
    if ($id_u_2 > 0)
    {
      $requete  = " SELECT MSG_TEXT, MSG_TIME, MSG_DATE, ID_MESSAGE, MSG_CR ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
      $requete .= " where ID_USER_AUT = " . $id_u_2;
      $requete .= " and ID_USER_DEST = " . $id_u_1;
      $requete .= " and ID_CONFERENCE = 0 ";
      $requete .= " order by ID_MESSAGE ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-51d]", $requete);
      if ( mysqli_num_rows($result) > 0  )
      {
        list ($msg, $heure, $dt_msg, $id_msg, $cr) = mysqli_fetch_row ($result);  // on ne parcours pas pour n'en afficher qu'un à la fois.
        $dt_form = "d/m/Y"; // FR
        if ($dt_f == 'EN') $dt_form = "m-d-Y";
        $dt_msg = date($dt_form, strtotime($dt_msg));
        if ($cr == "")
        {
          $cr = "64";
          $msg = f_encode64($msg);
        }
        if ($cr == "BB")
        {
          if ($n_version < 26) $cr = "AA";
        }
        // si expédié un autre jour
        if ($dt_msg != date($dt_form) )
          echo ">F42#OK#" . $msg . "#" . $dt_msg . "##" . $cr .  "##"; 
        else
          echo ">F42#OK#" . $msg . "#" . $heure  . "##" . $cr .  "##"; 
        //
        // on efface le message dans la foulée qu'on la envoyé
        $requete  = " delete from " . $PREFIX_IM_TABLE . "MSG_MESSAGE ";
        $requete .= " where ID_MESSAGE = " . $id_msg;
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-51e]", $requete);
      }
      else
      {
        $etat_num = 0;
        $etat_away = 0;
        // si pas de message, alors on indique qu'il n'y a pas de message en attente, et on récupère l'état du correspondant.
        $requete  = " select SES_STATUS, SES_AWAY_REASON ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
        $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
        $requete .= " and USR.ID_USER = " . $id_u_2;
        $requete .= " and USR.USR_STATUS = 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-51f]", $requete);
        if ( mysqli_num_rows($result) == 1 )
        {
          list ($etat_num, $etat_away) = mysqli_fetch_row ($result);
        }
        //
        $id_last_file_from_user_dest = 0;
        if ( (_SHARE_FILES != "") and (_SHARE_FILES_EXCHANGE != "") )
        {
          $requete  = " select max(ID_FILE) ";
          $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
          $requete .= " WHERE ID_USER_AUT = " . $id_u_2;
          $requete .= " and ID_USER_DEST = " . $id_u_1;
          $requete .= " and FIL_ONLINE = 'Y' ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-51g]", $requete);
          if ( mysqli_num_rows($result) == 1 )
          {
            list ($id_last_file_from_user_dest) = mysqli_fetch_row ($result);
          }
        }
        //
        echo ">F42#OK##h#" . $etat_num . "#" . $etat_away . "#" . $id_last_file_from_user_dest . "####"; // pas de message : attention, renvoyer OK suivi de vide (##) (h pour l'heure, on s'en fou) et surtout l'état.
      }
      //
      update_last_activity_user($id_u_1);
    }
    else
    {
      echo ">F42#OK###"; // pas de message 
    }
  }
  //
  mysqli_close($id_connect);
}
?>
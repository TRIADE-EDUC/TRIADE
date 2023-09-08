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
if ( (!isset($_GET['iu'])) or (!isset($_GET['i2'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user_1 =	  intval(f_decode64_wd($_GET['iu']));
$id_user_1 = 		(intval($id_user_1) - intval($action));
$id_user_2 =	  intval(f_decode64_wd($_GET['i2']));
$ip = 			    f_decode64_wd($_GET['ip']);
$version =      intval($_GET['v']);
if (isset($_GET['m'])) $msg = $_GET['m'];  else  $msg = ""; // message optionnel
if (isset($_GET['ad'])) $ad = $_GET['ad']; else  $ad = "";  // ajout par nickname
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user_1)) $id_user_1 = "";
if (preg_match("#[^0-9]#", $id_user_2)) $id_user_2 = "";
//
require ("../common/sql.inc.php");
//
if ( (intval($id_user_2) <= 0) and ($ad != "") )  
  $id_user_2 = f_get_id_nom_user(f_decode64_wd($ad));
//
if ( ($id_user_1 > 0) and ($id_user_2 > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user_1, $session_chk, $action) != 'OK')  die (">F20#KO#Session KO.#");
  //
  //
  $msg = f_decode64_wd($msg);
  $msg = str_replace("'", "`", $msg);
  $msg = str_replace('"', '', $msg);
  //
  if ( f_is_deja_in_contacts_id($id_user_1, $id_user_2) > 0 )
  {
    echo ">F20#KO####"; // deja dans la liste (peut être simplement en attente)
  }
  else
  {
    // On fait aussi le ménage sur les contacts dont le user n'existe plus :
    //$requete = " delete from " . $PREFIX_IM_TABLE . "CNT_CONTACT where ID_USER_1=" . $id_user_1 . " and ID_USER_2 not in (select distinct ID_USER from " . $PREFIX_IM_TABLE . "USR_USER ) ";
    $requete  = " select distinct(ID_USER_2) ";
    $requete .= " from " . $PREFIX_IM_TABLE . "CNT_CONTACT LEFT JOIN " . $PREFIX_IM_TABLE . "USR_USER ON " . $PREFIX_IM_TABLE . "CNT_CONTACT.ID_USER_2 = " . $PREFIX_IM_TABLE . "USR_USER.ID_USER ";
    $requete .= " where " . $PREFIX_IM_TABLE . "USR_USER.ID_USER IS NULL ";
    $requete .= " and " . $PREFIX_IM_TABLE . "CNT_CONTACT.ID_USER_1 = " . $id_user_1 . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-31a]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user_to_delete) = mysqli_fetch_row ($result) )
      {
        $requete_2  = " delete from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
        $requete_2 .= " where ID_USER_1 = " . $id_user_to_delete . " or ID_USER_2 = " . $id_user_to_delete . " ";
        $result2 = mysqli_query($id_connect, $requete_2);
        if (!$result2) error_sql_log("[ERR-31b]", $requete_2);
      }
    }
    //
    //
    $t_max_nb_contact_by_user_1 = _MAX_NB_CONTACT_BY_USER;
    $t_max_nb_contact_by_user_2 = _MAX_NB_CONTACT_BY_USER;
    $t_user_hiearchic_management_by_admin = _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN;
    $t_role_srv_offline_mode_2 = "";
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
    {
      require ("../common/roles.inc.php");
      $id_role = f_role_of_user($id_user_1);
      //
      if ($id_role > 0)
      {
        $t_max_nb_contact_by_user_1 = f_role_permission($id_role, "MAX_NB_CONTACT_BY_USER", _MAX_NB_CONTACT_BY_USER);
        $t_user_hiearchic_management_by_admin = f_role_permission($id_role, "USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN);
      }
      //
      //
      $id_role = f_role_of_user($id_user_2);
      //
      if ($id_role > 0)
      {
        $t_max_nb_contact_by_user_2 = f_role_permission($id_role, "MAX_NB_CONTACT_BY_USER", _MAX_NB_CONTACT_BY_USER);
        $t_role_srv_offline_mode_2 = f_role_permission($id_role, "ROLE_OFFLINE_MODE", ""); // c'est un role, pas une option !
      }
    }
    //
    if ($t_role_srv_offline_mode_2 != "")
    {
      $ok = 'KO';
      echo ">F20#KO#OFFLINE####"; // Dest is offline
    }
    else
      $ok = 'OK';
    //
    if ( ( ($t_max_nb_contact_by_user_1 != '0') and (intval($t_max_nb_contact_by_user_1) > 0) ) or ( ($t_max_nb_contact_by_user_2 != '0') and (intval($t_max_nb_contact_by_user_2) > 0) ) )
    {
      //
      // vérifier si ne dépasse pas le nombre de contacts autorisé par utilisateur.
      // pour l'utilisateur qui demande :
      if ( ($t_max_nb_contact_by_user_1 != '0') and (intval($t_max_nb_contact_by_user_1) > 0) )
      {
        $requete  = " select count(*) ";
        $requete .= " from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
        $requete .= " where ID_USER_1=" . $id_user_1 . " ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-31c]", $requete);
        list ($nb_cnt1) = mysqli_fetch_row ($result);
        //
        if ($nb_cnt1 >= $t_max_nb_contact_by_user_1)
        {
          // On récupère si on en a demandé qui n'ont pas encore accepté.
          $requete  = " select count(*) ";
          $requete .= " from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
          $requete .= " where ID_USER_1=" . $id_user_1;
          $requete .= " and CNT_STATUS = 0 ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-31e]", $requete);
          list ($nb_cnt_wait) = mysqli_fetch_row ($result);
          //
          $ok = 'KO';
          echo ">F20#KO#MAX#1#" . $nb_cnt_wait . "##"; // nbre maxi atteint (demandeur)
        }
      }
      //
      // vérifier si ne dépasse pas le nombre de contacts autorisé par utilisateur
      // pour celui qui recoit la demande :
      if ( ($t_max_nb_contact_by_user_2 != '0') and (intval($t_max_nb_contact_by_user_2) > 0) )
      {
        $requete  = " select count(*) from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
        $requete .= " where ID_USER_1=" . $id_user_2 . " ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-31d]", $requete);
        list ($nb_cnt2) = mysqli_fetch_row ($result);
        //
        if ($nb_cnt2 >= $t_max_nb_contact_by_user_2)
        {
          $ok = 'KO';
          echo ">F20#KO#MAX#2###"; // nbre maxi atteint (recepteur)
        }
      }
    }
    //
    //
    // si quota non dépassé, on vérifie le niveau hiéarchique :
    if ( ($ok == 'OK') and ($t_user_hiearchic_management_by_admin != "") and (_SPECIAL_MODE_OPEN_COMMUNITY == "") )
    {
      if ( f_level_of_user($id_user_1) > f_level_of_user($id_user_2) )
      {
        $ok = 'KO';
        echo ">F20#KO#LEVEL####"; // niveau hiérarchique supérieur.
      }
    }
    //
    // si le destinataire n'est pas d'un niveau hiérarchique supérieur
    if ($ok == 'OK')
    {
      $requete = "INSERT INTO " . $PREFIX_IM_TABLE . "CNT_CONTACT (ID_USER_1, ID_USER_2, CNT_STATUS, CNT_NEW_USERNAME) ";
      if (_SPECIAL_MODE_OPEN_COMMUNITY == "")
        $requete .= "VALUES (" . $id_user_1 . ", " . $id_user_2 . ", 0, '" . $msg . "') ";
      else
        $requete .= "VALUES (" . $id_user_1 . ", " . $id_user_2 . ", 5, '" . $msg . "') "; // on ajoute en masqué direct, sans attente validation
      //
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-31f]", $requete);
      if ( f_is_deja_in_contacts_id($id_user_1, $id_user_2) == 1 )
        echo ">F20#OK####"; 
      else
        echo ">F20#KO####"; // deja dans la liste (peut être simplement en attente)
      //
      update_last_activity_user($id_user_1);
    }
  }
  //
  mysqli_close($id_connect);
}
?>
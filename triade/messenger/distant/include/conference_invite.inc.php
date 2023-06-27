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
$id_u_2 =     intval(f_decode64_wd($_GET['u2']));
$ip = 			  f_decode64_wd($_GET['ip']);
$version =    intval($_GET['v']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_u_1)) $id_u_1 = "";
if (preg_match("#[^0-9]#", $id_u_2)) $id_u_2 = "";
//
if ( ($id_u_1 > 0) and ($id_u_2 > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_u_1, $session_chk, $action) != 'OK')  die(">F50#KO#1#"); // 1:session non ouverte.
  //
  // Si destinataire non "en ligne"
  if (f_get_id_session_id_user($id_u_2) == 0)
  {
    die(">F50#KO#2#"); // 2:session non ouverte (utilisateur distant indisponbile)
  }
  //
  //
  $t_allow_conference = _ALLOW_CONFERENCE;
  $t_allow_invisible = _ALLOW_HIDDEN_TO_CONTACTS;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_u_1);
    //
    if ($id_role > 0)
    {
      $t_allow_conference = f_role_permission($id_role, "ALLOW_CONFERENCE", _ALLOW_CONFERENCE);
      $t_allow_invisible = f_role_permission($id_role, "ALLOW_HIDDEN_TO_CONTACTS", _ALLOW_HIDDEN_TO_CONTACTS);
    }
  }
  //
  //
  if ($t_allow_conference  == '')
    die(">F50#KO#5#"); // 5:non autoris�
  //
  $ok_send = "OK";
  $priv_u_1 = '';
  //
  // si mode invisible autoris�.
  if ($t_allow_invisible != '')
  {
    // on r�cup�re l'�tat de privil�ge du destinataire :
    $requete = "select CNT_STATUS from " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
    $requete .= "WHERE ID_USER_1 = " . $id_u_1 . " ";
    $requete .= "and ID_USER_2 = " . $id_u_2 . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-60a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($priv_u_1) = mysqli_fetch_row ($result);
    }
    //
    // si on est masqu� pour le destinataire, on ne peut pas lui �crire (non mais).
    if ($priv_u_1 == '5')
    {
      echo ">F50#KO#4#";  // 4:utilisateur ne peut pas vour voir.
      $ok_send = "Ko";
    }
  }
  if ($ok_send == "OK")
  {
    $ok_send = "Ko";
    $requete  = " select SES_STATUS, ID_SESSION ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SES_SESSION SES, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
    $requete .= " and USR.ID_USER = " . $id_u_2 . " ";
    $requete .= " and USR.USR_STATUS = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-60b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($etat_num, $id_ses) = mysqli_fetch_row ($result);
      //if ($etat_num == '1') // seuls les utilisateurs dispos peuvent �tre invit�s � une conf�rence.
      if ( ($etat_num == '1') or ($etat_num == '2') or ($etat_num == '3') )
      {
        $ok_send = "OK"; 
      }
    }
  }
  //
  //
  if ($ok_send == "OK")
  {
    // On supprime ses attentes de confirmation aux autres conf�rences.
    $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
    $requete .= " WHERE ID_USER = " . $id_u_2 . " ";
    $requete .= " AND USC_ACTIVE = 0 "; // en attente de validation
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-60c]", $requete);
    //
    // On v�rifie si l'utilisateur n'est pas d�j� ACTIF dans une autre conf�rence.
    $requete  = " select USC.ID_CONFERENCE, USC.USC_ACTIVE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE CNF, " . $PREFIX_IM_TABLE . "USC_USERCONF USC ";
    $requete .= " WHERE CNF.ID_CONFERENCE = USC.ID_CONFERENCE and USC.ID_USER = " . $id_u_2 . " ";
    $requete .= " AND USC_ACTIVE = 1 "; // actif
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-60d]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      $ok_send == "Ko";
    }
  }
  //
  if ($ok_send == "OK")
  {
    // On v�rifie si la conf�rence existe (si ce n'est pas la 1�re personne que l'on invite).
    $id_conf = 0;
    $requete  = " select CNF.ID_CONFERENCE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE CNF, " . $PREFIX_IM_TABLE . "USC_USERCONF USC ";
    $requete .= " WHERE CNF.ID_CONFERENCE = USC.ID_CONFERENCE and USC.ID_USER = " . $id_u_1 . " ";
    //$requete .= " AND USC_ACTIVE = 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-60e]", $requete);
    if ( mysqli_num_rows($result) == 1 ) // normalement pas plus...
    {
      list ($id_conf) = mysqli_fetch_row ($result);
    }
    //
    // Si la conf�rence n'existe pas, on la cr��.
    if (intval($id_conf) <= 0)
    {
      $requete = "INSERT INTO " . $PREFIX_IM_TABLE . "CNF_CONFERENCE (ID_USER, CNF_DATE_CREAT, CNF_TIME_CREAT) ";
      $requete .= "VALUES (" . $id_u_1 . ", CURDATE(), CURTIME() ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-60f]", $requete);
      //
      $id_conf = 0;
      $requete  = " select max(ID_CONFERENCE) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ";
      $requete .= " WHERE ID_USER = " . $id_u_1 . " ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-60g]", $requete);
      if ( mysqli_num_rows($result) == 1 ) // normalement une seule conf, mais pas toujours ! (donc max et purge plus bas de la participation dans autres conf�rences)
      {
        list ($id_conf) = mysqli_fetch_row ($result);
      }
      //
      // Comme on vient de cr�er la conf�rence, il saut s'y ajouter comme participant (et actif).
      if (intval($id_conf) > 0)
      {
        // on ajoute le demandeur, en compte valid� (1).
        $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "USC_USERCONF (ID_CONFERENCE, ID_USER, USC_ACTIVE) ";
        $requete .= " VALUES (" . $id_conf . ", " . $id_u_1 . " , 1 ) ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-60h]", $requete);
        //
        // pour le cas o�, on supprime sa participation dans les autres conf�rences.
        $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
        $requete .= " WHERE ID_USER = " . $id_u_1;
        $requete .= " AND ID_CONFERENCE <> " . $id_conf;
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-60i]", $requete);
      }
    }
    //
    if (intval($id_conf) > 0)
    {
      // on ajoute le correspondant, en compte non valid� (0).
      $requete = "INSERT INTO " . $PREFIX_IM_TABLE . "USC_USERCONF (ID_CONFERENCE, ID_USER, USC_ACTIVE) ";
      $requete .= "VALUES (" . $id_conf . ", " . $id_u_2 . " , 0 ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-60k]", $requete);
      //
      echo ">F50#OK#" . $id_conf . "#####"; // conf�rence bien cr��e
    }
    else
      echo ">F50#KO######";
    //
  }
  else
    die(">F50#KO#0###");
  //
  mysqli_close($id_connect);
}
?>
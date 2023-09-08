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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$version =    intval($_GET['v']);
if (isset($_GET['is'])) $id_session = intval(f_decode64_wd($_GET['is'])); else $id_session = ""; // IS
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($version > 18) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/library/crwd.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F19#KO#1#"); // 1:session non ouverte.
  //
  $t_allow_rating = _ALLOW_CONTACT_RATING;
  $t_user_hiearchic_management_by_admin = _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_rating = f_role_permission($id_role, "ALLOW_CONTACT_RATING", _ALLOW_CONTACT_RATING);
      $t_user_hiearchic_management_by_admin = f_role_permission($id_role, "USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN);
    }
  }
  //
  //
  $requete  = " select USR.ID_USER, USR.USR_USERNAME, USR.USR_NICKNAME, USR.USR_NAME, USR.USR_LEVEL, USR.USR_COUNTRY_CODE, USR.USR_LANGUAGE_CODE, ";
  $requete .= " USR.USR_AVATAR, USR.USR_TIME_SHIFT, USR.USR_EMAIL, USR.USR_PHONE, USR.USR_GENDER, USR.USR_RATING, ";
  $requete .= " CNT.ID_CONTACT, CNT.CNT_STATUS, CNT.CNT_NEW_USERNAME, CNT.CNT_USER_GROUP ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT CNT, " . $PREFIX_IM_TABLE . "USR_USER USR ";
  $requete .= " WHERE USR.ID_USER = CNT.ID_USER_2 ";
  $requete .= " and CNT.ID_USER_1 = " . $id_user . " ";
  $requete .= " and USR.USR_STATUS = 1 "; // peut être pas...?
  //$requete .= " and CNT_STATUS > 0 ";
  //$requete .= " ORDER BY USR.ID_USER"; 
  $requete .= " ORDER BY USR.USR_USERNAME"; 
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[11a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo ">F19#OK####|";
    while( list ($id_user_2, $user_name, $nickname, $name, $level, $country_code, $language_code, $avatar, $timeshift, $email, $phone, $gender, $usr_rating,
    $id_contact, $status, $new_name, $group) = mysqli_fetch_row ($result) )
    {
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $user_name = $nickname;
      if (_FLAG_COUNTRY_FROM_IP == "") $country_code = "";
      if ($t_user_hiearchic_management_by_admin == "")  echo $level = "";
      if ($t_allow_rating == "") $usr_rating = ""; // 0
      if ($name == 'HIDDEN')	$name = '';
      if ($status == 0)	$new_name = ''; // pour les demandes d'ajout de contact, pour ne pas avoir la phrase de demande d'ajout en pseudo !
      //
      $msg  = $user_name . "#" . $id_user_2 . "#" . $name . "#" . $language_code . "#" . $avatar . "#" . $timeshift . "#" . $email . "#" . $phone . "#";
      $msg .= $gender . "#" . $country_code . "#" . $level . "#" . $usr_rating  . "#" . $id_contact . "#" . $status . "#" . $new_name . "#" . $group . "#";
      $msg = f_send_param($msg);
      $msg = f_encode64($msg);
      echo $msg . "|"; // séparateur de ligne : '|' (pipe).
    }
  }
  else
  {
    // renvoie : aucun contact 'disponible'
    echo ">F19#-#-#";
  }
  //
  mysqli_close($id_connect);
}
?>
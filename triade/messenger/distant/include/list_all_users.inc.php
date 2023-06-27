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
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F18#KO#1#"); // 1:session non ouverte.
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
  $requete  = " select SQL_CACHE ID_USER, USR_USERNAME, USR_NICKNAME, USR_NAME, USR_LEVEL, USR_COUNTRY_CODE, USR_LANGUAGE_CODE, ";
  $requete .= " USR_AVATAR, USR_TIME_SHIFT, USR_EMAIL, USR_PHONE, USR_GENDER, USR_RATING, ID_ROLE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  //$requete .= " WHERE ID_USER <> " . $id_user . " "; / pour SQL_CACHE (voir plus bas {1}).
  //$requete .= " WHERE ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 )  "; // ne pouvoir afficher dans ses contacts que des users validés.
  $requete .= " WHERE USR_STATUS = 1 ";
  $requete .= " and USR_NAME <> 'HIDDEN' "; // ne pas afficher les contacts masqués : pour ajout dans les contacts.
  //if (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != "") // vérification si niveau <=
  //{
  //  $requete .= " and USR_LEVEL >= " . f_level_of_user($id_user);
  //}
  $requete .= " ORDER BY USR_USERNAME";
  //$requete .= " ORDER BY ID_USER";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-10a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo ">F18#OK####|";
    while( list ($id_user_2, $user_name, $nickname, $name, $level, $country_code, $language_code, $avatar, $timeshift, $email, $phone, $gender, $usr_rating, $usr_id_role) = mysqli_fetch_row ($result) )
    {
      if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $user_name = $nickname;
      if (_FLAG_COUNTRY_FROM_IP == "") $country_code = "";
      if ($t_user_hiearchic_management_by_admin == "")  echo $level = "";
      if ($t_allow_rating == "") $usr_rating = ""; // 0
      //
      $ok = "";
      // on renvoi seulement ceux qui ne sont pas dans ses contacts
      if ($all == "not_in_contact_list")
      {
        if (  ($name != 'HIDDEN') and ( f_is_deja_in_contacts_id($id_user, $id_user_2) <= 0 ) and ( f_is_deja_in_contacts_id($id_user_2, $id_user) <= 0 )  ) $ok = "X";
      }
      else
        $ok = "X";
      //
      if ($id_user_2 == $id_user) $ok = ""; // pour SQL_CACHE (voir le where {1}).
      //
      // Hide user from a role - example: users id_role=2 cannot see users id_role=1
      if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
      {
        if ($id_role == 2) // example: 2
        {
          #if ($id_role_cnt == 1) $ok = 'Ko'; // example: 1
        }
      }
      //
      if ($name == 'HIDDEN')	$name = '';
      if ($ok != "")
      {
        $msg  = $user_name . "#" . $id_user_2 . "#" . $name . "#" . $language_code . "#";
        if ($all != "not_in_contact_list") $msg .= $avatar . "#" . $timeshift . "#" . $email . "#" . $phone . "#" . $gender . "#" . $country_code . "#" . $level . "#" . $usr_rating . "#";
        $msg = f_send_param($msg);
        $msg = f_encode64($msg);
        echo $msg . "|"; // séparateur de ligne : '|' (pipe).
      }
    }
  }
  else
  {
    // renvoie : aucun contact 'disponible'
    echo ">F18#-#-#";
  }
  //
  mysqli_close($id_connect);
}
?>
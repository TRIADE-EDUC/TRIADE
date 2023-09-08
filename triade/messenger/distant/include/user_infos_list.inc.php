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
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$ip = 			    f_decode64_wd($_GET['ip']);
$version =	    intval($_GET['v']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die ("Session KO.");
  //
  //
  $t_allow_rating = _ALLOW_CONTACT_RATING;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_rating = f_role_permission($id_role, "ALLOW_CONTACT_RATING", _ALLOW_CONTACT_RATING);
    }
  }
  //
  //
  $requete  = " select USR_USERNAME, USR_NICKNAME, USR_NAME, USR_PHONE, USR_EMAIL, USR_GENDER, USR_AVATAR, USR_COUNTRY_CODE, ";
  $requete .= " USR_LANGUAGE_CODE, USR_TIME_SHIFT, USR_GET_OFFLINE_MSG, USR_RATING ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE ID_USER = " . $id_user . " ";
  $requete .= " and USR_STATUS = 1 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-73a]", $requete);
  //
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($username, $nickname, $name, $usr_phone, $usr_email, $usr_gender, $avatar, $country_code, $language_code, $time_shift, $get_offline, $rating) = mysqli_fetch_row ($result);
    //
    if ($avatar != "")
    {
      if (is_readable("avatar/" . $avatar) == false) 
        $avatar = "";
    }
    else
    {
      if (is_readable("avatar/" . $username . ".jpg"))
        $avatar = $username . ".jpg";
    }
    #if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
    $name =       f_encode64($name);
    $usr_phone =  f_encode64($usr_phone);
    $usr_email =  f_encode64($usr_email);
    $avatar =     f_encode64($avatar);
    $rating =     f_encode64($rating);
    if ($t_allow_rating == "") $rating = "";
    //
    //
    echo ">F66#OK#" . $name . "#" . $usr_phone . "#" . $usr_email . "#" . $usr_gender .  "#" . $avatar . "#" .  $country_code . "#" . $language_code . "#" . $time_shift . "#" . $rating . "#" . $get_offline . "####|";
  }
  else
  {
    echo ">F66#-#####";
  }
  mysqli_close($id_connect);
}
?>
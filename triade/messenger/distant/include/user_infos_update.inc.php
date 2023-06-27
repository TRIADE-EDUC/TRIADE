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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) or (!isset($_GET['n'])) or (!isset($_GET['p'])) or (!isset($_GET['e'])) or (!isset($_GET['g'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$ip = 			    f_decode64_wd($_GET['ip']);
$version =	    intval($_GET['v']);
$name = 			  f_decode64_wd($_GET['n']);
$phone = 			  f_decode64_wd($_GET['p']);
$email = 			  f_decode64_wd($_GET['e']);
$gender =			  $_GET['g'];
if (isset($_GET['gof'])) $get_offline = intval($_GET['gof']);  else  $get_offline = -1;
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
  $email = trim(strtolower($email));
  $name  = f_clean_name($name);
  $gender= trim($gender);
  if (!preg_match('/^[-a-z0-9._@]+$/i', $email) ) $email = "";
  if (!preg_match('/^[+0-9.()]+$/i', $phone) ) $phone = "";
  //
  $t_allow_change_email_phone = _ALLOW_CHANGE_EMAIL_PHONE;
  $t_allow_change_function_name = _ALLOW_CHANGE_FUNCTION_NAME;
  $t_allow_send_to_offline_user = _ALLOW_SEND_TO_OFFLINE_USER;
  $t_censor_messages = _CENSOR_MESSAGES;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_change_email_phone = f_role_permission($id_role, "ALLOW_CHANGE_EMAIL_PHONE", _ALLOW_CHANGE_EMAIL_PHONE);
      $t_allow_change_function_name = f_role_permission($id_role, "ALLOW_CHANGE_FUNCTION_NAME", _ALLOW_CHANGE_FUNCTION_NAME);
      $t_allow_send_to_offline_user = f_role_permission($id_role, "ALLOW_SEND_TO_OFFLINE_USER", _ALLOW_SEND_TO_OFFLINE_USER);
      $t_censor_messages = f_role_permission($id_role, "CENSOR_MESSAGES", _CENSOR_MESSAGES);
    }
  }
  //
  // on censure les mots interdits par l'administrateur :
  if ($t_censor_messages != '')
  {
    if (is_readable("../common/censure.txt"))
    {
      require ("../common/words_filtering.inc.php");
      $email = textCensure($email, "../common/config/censure.txt");
      $name  = textCensure($name,  "../common/config/censure.txt");
    }
  }
  //
  //
  if ($t_allow_change_email_phone != "")
  {
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
    $requete .= " set USR_PHONE = '" . $phone . "' , USR_EMAIL = '" . $email. "' , USR_GENDER = '" . $gender. "', USR_DATE_PASSWORD = CURDATE() ";
    $requete .= " WHERE ID_USER = " . $id_user . " ";
    $requete .= " and USR_STATUS = 1 ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-72a]", $requete);
  }
  //
  if ($t_allow_change_function_name != "")
  {
    if ($name != "HIDDEN")
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
      $requete .= " set USR_NAME = '" . $name . "', USR_DATE_PASSWORD = CURDATE() ";
      $requete .= " WHERE ID_USER = " . $id_user . " ";
      $requete .= " and USR_STATUS = 1 ";
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-72b]", $requete);
    }
  }
  //
  if ( ($t_allow_send_to_offline_user != "") and ($get_offline <> "") )
  {
    if ( ($get_offline == 0) or ($get_offline == 1) or ($get_offline == 2) )
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER "; 
      $requete .= " set USR_GET_OFFLINE_MSG = " . $get_offline . " ";
      $requete .= " WHERE ID_USER = " . $id_user . " ";
      $requete .= " and USR_STATUS = 1 ";
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-72c]", $requete);
    }
  }
  //
  echo ">F67#OK####";
  //
  mysqli_close($id_connect);
}
?>
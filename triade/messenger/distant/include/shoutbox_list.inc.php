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
$last_id_m  = intval(f_decode64_wd($_GET['bi']));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
if (isset($_GET['dtf'])) $dt_f = $_GET['dtf'];  else  $dt_f = "";  
if (isset($_GET['ig'])) $id_grp = intval($_GET['ig']);  else  $id_grp = "0";  
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_grp)) $id_grp = "0";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $last_id_m)) $last_id_m = "";
if (preg_match("#[^0-9]#", $n_version)) $n_version = "";
//
if ( ($id_user > 0) and ($n_version > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (!ctype_alnum($dt_f))    $dt_f = "";  // après functions.inc.php !
  $dt_form = "d/m/Y"; // FR
  if ($dt_f == 'EN') $dt_form = "m-d-Y";
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F80#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_allow_shoutbox = _SHOUTBOX;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_shoutbox = f_role_permission($id_role, "SHOUTBOX", _SHOUTBOX);
    }
  }
  //
  if ($t_allow_shoutbox == "")
  {
    die(">F80#KO#2#"); // 2: Not allowed (option not activated)
  }
  //
  // $last_id_m  < 0 : juste récupérer le dernier ID.
  // $last_id_m == 0 : afficher la shoutbox.
  // $last_id_m  > 0 : n'afficher la shoutbox que si ya du nouveau.
  if ($last_id_m <> 0)
  {
    $requete  = " select max(ID_SHOUT)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " WHERE SBX_DISPLAY > 0 ";
    $requete .= " and ID_GROUP_DEST = " . $id_grp;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-110a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_max_msg) = mysqli_fetch_row ($result);
      if ( ($id_max_msg > $last_id_m) and ($last_id_m > 0) )
        $last_id_m = 0; // pour afficher la liste
      else
        echo ">F80#-#A#" . $id_max_msg . "##";
    }
  }
  //
  if ($last_id_m == 0)
  {
    $requete  = " select SBX.ID_SHOUT, SBX.ID_GROUP_DEST, SBX.ID_USER_AUT, SBX.SBX_TIME, SBX.SBX_DATE, SBX.SBX_RATING, SBX.SBX_TEXT, sum(SBV.SBV_VOTE_M), sum(SBV.SBV_VOTE_L) "; // SBX_NB_VOTE_M
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX as SBX ";
    $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE as SBV ON ( SBX.ID_SHOUT = SBV.ID_SHOUT ) ";
    $requete .= " WHERE SBX.SBX_DISPLAY > 0 ";
    $requete .= " and SBX.ID_GROUP_DEST = " . $id_grp;
    //$requete .= " ORDER BY SBX_DATE, SBX_TIME ";
    $requete .= " GROUP BY SBX.ID_SHOUT ";
    $requete .= " ORDER BY SBX.ID_SHOUT DESC ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-110b]", $requete);
    $nb_lig = mysqli_num_rows($result);
    if ( $nb_lig > 0 )
    {
      echo ">F80#OK#" . $nb_lig . "###|";
      while( list ($id_shout, $id_group_dest, $id_aut, $s_time, $s_date, $rating, $txt, $nb_vote_m, $nb_vote_l) = mysqli_fetch_row ($result) )
      {
        $s_date = date($dt_form, strtotime($s_date));
        //$username = f_get_username_of_id($id_aut);
        $username = f_get_username_nickname_of_id($id_aut); // avec espaces et majuscules
        $msg = "#" . $id_shout . "#" . $id_group_dest . "#" . $id_aut . "#" . $username . "#" . $s_time . "#" . $s_date . "#" . $rating . "#" . f_decode64_wd($txt) . "#" . $nb_vote_m . "#" . $nb_vote_l . "#";
        $msg = f_encode64($msg);
        echo $msg . "|"; // séparateur de ligne : '|' (pipe).
      }
    }
    else
    {
      // renvoie : la shout est vide.
      echo ">F80#-#B##";
    }
  }
  //
  mysqli_close($id_connect);
}
?>
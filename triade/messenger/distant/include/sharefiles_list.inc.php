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
if ( (!isset($_GET['iu'])) or (!isset($_GET['sc'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$session_chk =  f_decode64_wd($_GET['sc']);
$ip = 			    f_decode64_wd($_GET['ip']);
$fil_media =	  intval($_GET['md']);
$fil_project =	intval($_GET['pj']);
$filter_first =	intval($_GET['ff']);
$part =	        $_GET['p'];
$n_version =	  intval($_GET['v']);
if (isset($_GET['dtf'])) $dt_f = $_GET['dtf'];  else  $dt_f = "";  
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($n_version > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F146#KO#1#"); // 1:session non ouverte.
  //
  //
  if (!ctype_alnum($dt_f))  $dt_f = "";
  $dt_form = "d/m/Y"; // FR
  if ($dt_f == 'EN') $dt_form = "m-d-Y";
  //
  $t_sharefiles = _SHARE_FILES;
  $t_sharefiles_allow_vote = _SHARE_FILES_VOTE;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_sharefiles = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
      $t_sharefiles_allow_vote = f_role_permission($id_role, "SHARE_FILES_VOTE", _SHARE_FILES_VOTE);
    }
  }
  //
  if ($t_sharefiles == "")
  {
    die(">F146#KO#2#"); // 2: n'a pas les droits (option non activée).
  }
  //
  //
  if ($part == "G")
  {
    if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
      $rien = "";
    else
      die(">F146#KO#3#"); // 3: pas de groupes.
    //
    //$requete  = " SELECT GRP.ID_GROUP ";
    $requete  = " select FIL.ID_FILE, FIL.FIL_NAME, FIL.FIL_SIZE, FIL.FIL_DATE, FIL.FIL_DATE_ADD, ";
    $requete .= " FIL.ID_USER_AUT, FIL.ID_PROJET, FIL.ID_FILEMEDIA, FIL.FIL_RATING, FIL_COMMENT ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG, " . $PREFIX_IM_TABLE . "FIL_FILE FIL ";
    $requete .= " WHERE USG.ID_GROUP = GRP.ID_GROUP ";
    $requete .= " and USG.ID_GROUP = FIL.ID_GROUP_DEST ";
    //$requete .= " and FIL.ID_GROUP_DEST = " . $id_grp;
    $requete .= " and FIL.ID_GROUP_DEST is NOT null and ID_USER_DEST is null ";
    $requete .= " and USG.ID_USER = " . $id_user;
    $requete .= " and FIL.FIL_ONLINE = 'Y' ";
  }
  else
  {
    $requete  = " select ID_FILE, FIL_NAME, FIL_SIZE, FIL_DATE, FIL_DATE_ADD, ID_USER_AUT, ID_PROJET, ID_FILEMEDIA, FIL_RATING, FIL_COMMENT ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE FIL_ONLINE = 'Y' ";
    if ($part == "A")
      $requete .= " and ID_USER_DEST = " . $id_user . " ";
    else
      $requete .= " and ID_USER_DEST is null and ID_GROUP_DEST is null ";
  }
  if ($fil_media > 0)    $requete .= " and ID_FILEMEDIA = " . $fil_media . " ";
  if ($fil_project > 0)  $requete .= " and ID_PROJET = " . $fil_project . " ";
  $requete .= " order by FIL_DATE_ADD DESC ";
  if ($filter_first > 0) $requete .= " limit " . $filter_first . " ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-146a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo ">F146#OK####|";
    while( list ($id_file, $fil_name, $fil_size, $date_fil, $date_add, $id_aut, $id_projet, $id_media, $rating, $fil_comment) = mysqli_fetch_row ($result) )
    {
      $date_fil = date($dt_form, strtotime($date_fil));
      $date_add = date($dt_form, strtotime($date_add));
      //
      if ($t_sharefiles_allow_vote == "") $rating = "0";
      echo f_encode64("#" . $id_file . "#" . $fil_name . "#" . $fil_size . "#" . $date_fil . "#" . $date_add . "#" . $id_aut . "#" . $id_projet . "#" . $id_media . "#" . $rating . "#" . $fil_comment . "#") . "|"; // séparateur de ligne : '|' (pipe).
    }
  }
  else
  {
    // renvoie : aucun fichier pour ce user
    echo ">F146#0#-#0#";
  }
  //
  mysqli_close($id_connect);
}
?>
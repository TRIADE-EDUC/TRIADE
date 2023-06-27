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
if ( (!isset($_GET['iu'])) or (!isset($_GET['sc'])) or (!isset($_GET['fi'])) or (!isset($_GET['r'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$session_chk =  f_decode64_wd($_GET['sc']);
$file_id =    intval(f_decode64_wd($_GET['fi']));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
$vote =       $_GET['r'];
if ($vote == "p") $vote = 1;
if ($vote == "c") $vote = -1;
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $file_id)) $file_id = "";
//
if ( ($id_user > 0) and ($session_chk != "") and ($file_id > 0) and ($n_version > 0) and (abs(intval($vote)) == 1) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/share_files.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F145#KO#1#"); // 1:session non ouverte.
  //
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
  if ( ($t_sharefiles_allow_vote == "") or ($t_sharefiles == "") )
  {
    die(">F145#KO#2#"); // 2:n'a pas les droits (option non activée).
  }
  //
  $deja_vote = "";
  $requete  = " select (FLV_VOTE_M + FLV_VOTE_L) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
  $requete .= " WHERE ID_FILE = " . $file_id;
  $requete .= " and ID_USER_VOTE = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-145a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($deja_vote) = mysqli_fetch_row ($result);
    echo ">F145#KO#4#" . $deja_vote . "#";
  }
  //
  //
  if ($deja_vote == "")
  {
    // auteur du message
    $requete  = " select ID_USER_AUT";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " WHERE ID_FILE = " . $file_id;
    $requete .= " and ID_USER_AUT <> " . $id_user;    // on ne peut pas voter pour soi...
    $requete .= " and FIL_ONLINE = 'Y' ";
    $requete .= " and ID_USER_DEST is null "; // public share file only
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-145b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_aut) = mysqli_fetch_row ($result);
    }
    //
    $vote_p = 0;
    $vote_c = 0;
    if ($vote > 0) $vote_p = 1;
    if ($vote < 0) $vote_c = -1;
    //
    if ($id_aut > 0)
    {
      // On enregistre le vote
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ( ID_FILE, ID_USER_VOTE, ID_USER_AUT, FLV_VOTE_M, FLV_VOTE_L, FLV_DATE ) ";
      $requete .= " VALUES (" . $file_id . ", " . $id_user . ", " . $id_aut . ", " . $vote_p  . ", " . $vote_c . ",  CURDATE() ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-145c]", $requete);
      //
      // On met à jour la moyenne (votes du messages)
      $requete  = " select sum(FLV_VOTE_M) + sum(FLV_VOTE_L)";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
      $requete .= " WHERE ID_FILE = " . $file_id;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-145d]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($rating) = mysqli_fetch_row ($result);
        //if ($rating <> 0)
        $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FIL_FILE ";
        $requete .= " set FIL_RATING = " . $rating;
        $requete .= " WHERE ID_FILE = " . $file_id;
        $requete .= " limit 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-145e]", $requete);
      }
      //
      //
      echo ">F145#OK##"; // vote bien enregistré (le faire APRES avoir actualisé la moyenne !).
      //
      //
      //
      stats_sharefile_add_note_user($id_aut, $vote);
      //
      // Meilleurs scores
      stats_sharefile_update_scores($file_id, $id_aut);
    }
    else
      echo ">F145#KO#6#";
  }
  //
  mysqli_close($id_connect);
}
?>
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
if ( (!isset($_GET['iu'])) or (!isset($_GET['is'])) or (!isset($_GET['r'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$id_bookm =   intval(f_decode64_wd($_GET['is']));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
$vote =       $_GET['r'];
if ($vote == "p") $vote = 1;
if ($vote == "c") $vote = -1;
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $id_bookm)) $id_bookm = "";
//
if ( ($id_user > 0) and ($id_bookm > 0) and ($n_version > 34) and (abs(intval($vote)) == 1) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F59#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_bookmarks = _BOOKMARKS;
  $t_bookmarks_allow_vote = _BOOKMARKS_VOTE;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_bookmarks = f_role_permission($id_role, "BOOKMARKS", _BOOKMARKS);
      $t_bookmarks_allow_vote = f_role_permission($id_role, "BOOKMARKS_VOTE", _BOOKMARKS_VOTE);
    }
  }
  //
  if ( ($t_bookmarks_allow_vote == "") or ($t_bookmarks == "") )
  {
    die(">F59#KO#2#"); // 2:n'a pas les droits (option non activée).
  }
  //
  $deja_vote = "";
  $requete  = " select (BMV_VOTE_M + BMV_VOTE_L) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE ";
  $requete .= " WHERE ID_BOOKMARK = " . $id_bookm;
  $requete .= " and ID_USER_VOTE = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-132a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($deja_vote) = mysqli_fetch_row ($result);
    echo ">F59#KO#4#" . $deja_vote . "#";
  }
  //
  if ($deja_vote == "")
  {
    // auteur du message
    $requete  = " select ID_USER_AUT";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE ID_BOOKMARK = " . $id_bookm;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-132g]", $requete);
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
    // On enregistre le vote
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE ( ID_BOOKMARK, ID_USER_VOTE, ID_USER_AUT, BMV_VOTE_M, BMV_VOTE_L, BMV_DATE ) ";
    $requete .= " VALUES (" . $id_bookm . ", " . $id_user . ", " . $id_aut . ", " . $vote_p  . ", " . $vote_c . ",  CURDATE() ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-132h]", $requete);
    //
    // On met à jour la moyenne (votes du messages)
    $requete  = " select sum(BMV_VOTE_M) + sum(BMV_VOTE_L)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE ";
    $requete .= " WHERE ID_BOOKMARK = " . $id_bookm;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-132j]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($rating) = mysqli_fetch_row ($result);
      //if ($rating <> 0)
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
      $requete .= " set BMK_RATING = " . $rating;
      $requete .= " WHERE ID_BOOKMARK = " . $id_bookm;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-132k]", $requete);
    }
    //
    //
    echo ">F59#OK##"; // vote bien enregistré (le faire APRES avoir actualisé la moyenne !).
    //
  }
  //
  mysqli_close($id_connect);
}
?>
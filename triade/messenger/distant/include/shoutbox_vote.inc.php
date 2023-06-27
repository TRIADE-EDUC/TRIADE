<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2016 THeUDS           **
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
if ( (!isset($_GET['iu'])) or (!isset($_GET['r'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$id_shout =   intval(f_decode64_wd($_GET['is']));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
$vote =       $_GET['r'];
if ($vote == "p") $vote = 1;
if ($vote == "c") $vote = -1;
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $id_shout)) $id_shout = "";
//
if ( ($id_user > 0) and ($id_shout > 0) and ($n_version > 34) and (abs(intval($vote)) == 1) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/shoutbox.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F85#KO#1#"); // 1:session non ouverte.
  //
  $id_user_aut = 0;
  $requete  = " select ID_USER_AUT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
  $requete .= " WHERE ID_SHOUT = " . $id_shout;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-115m]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($id_user_aut) = mysqli_fetch_row ($result);
  }
  if ($id_user_aut <= 0) echo ">F85#KO#6##";
  //
  //
  $t_allow_shoutbox = _SHOUTBOX;
  $t_shoutbox_allow_vote = _SHOUTBOX_VOTE;
  $t_shoutbox_max_notes_user_day = _SHOUTBOX_MAX_NOTES_USER_DAY;
  $t_shoutbox_max_notes_user_week = _SHOUTBOX_MAX_NOTES_USER_WEEK;
  $t_shoutbox_remove_message_votes = _SHOUTBOX_REMOVE_MESSAGE_VOTES;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_shoutbox = f_role_permission($id_role, "SHOUTBOX", _SHOUTBOX);
      $t_shoutbox_allow_vote = f_role_permission($id_role, "SHOUTBOX_VOTE", _SHOUTBOX_VOTE);
      $t_shoutbox_max_notes_user_day = f_role_permission($id_role, "SHOUTBOX_MAX_NOTES_USER_DAY", _SHOUTBOX_MAX_NOTES_USER_DAY);
      $t_shoutbox_max_notes_user_week = f_role_permission($id_role, "SHOUTBOX_MAX_NOTES_USER_WEEK", _SHOUTBOX_MAX_NOTES_USER_WEEK);
      #$t_shoutbox_remove_message_votes = f_role_permission($id_role, "SHOUTBOX_REMOVE_MESSAGE_VOTES", _SHOUTBOX_REMOVE_MESSAGE_VOTES);
    }
    //
    $id_role = f_role_of_user($id_user_aut);
    if ($id_role > 0)
    {
      $t_shoutbox_remove_message_votes = f_role_permission($id_role, "SHOUTBOX_REMOVE_MESSAGE_VOTES", _SHOUTBOX_REMOVE_MESSAGE_VOTES);
    }
  }
  //
  if ( ($t_shoutbox_allow_vote == "") or ($t_allow_shoutbox == "") )
  {
    die(">F85#KO#2#"); // 2:n'a pas les droits (option non activée).
  }
  //
  $deja_vote = "";
  $requete  = " select (SBV_VOTE_M + SBV_VOTE_L) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
  $requete .= " WHERE ID_SHOUT = " . $id_shout;
  $requete .= " and ID_USER_VOTE = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-115a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($deja_vote) = mysqli_fetch_row ($result);
    echo ">F85#KO#4#" . $deja_vote . "#";
  }
  //
  if ( (intval($t_shoutbox_max_notes_user_day) > 0) and ($deja_vote == "") )
  {
    $requete  = " select count(*)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
    $requete .= " WHERE ID_USER_VOTE = " . $id_user;
    $requete .= " and SBV_DATE = CURDATE() ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-115b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_vote) = mysqli_fetch_row ($result);
      if ($nb_vote >= intval($t_shoutbox_max_notes_user_day) )
      {
        echo ">F85#KO#3#" . $nb_vote . "#"; // over quota
        $deja_vote = "X";
      }
    }
  }
  //
  if ( (intval($t_shoutbox_max_notes_user_week) > 0) and ($deja_vote == "") )
  {
    $requete  = " select count(*)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
    $requete .= " WHERE ID_USER_VOTE = " . $id_user;
    $requete .= " and TIMESTAMPDIFF(WEEK, SBV_DATE, CURDATE() ) = 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-115c]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_vote) = mysqli_fetch_row ($result);
      if ($nb_vote >= intval($t_shoutbox_max_notes_user_week) )
      {
        echo ">F85#KO#3#" . $nb_vote . "#"; // over quota
        $deja_vote = "X";
      }
    }
  }
  //
  if ( (intval($t_shoutbox_remove_message_votes) > 0) and ($deja_vote == "") )
  {
    $requete  = " select count(*)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
    $requete .= " WHERE ID_SHOUT = " . $id_shout;
    $requete .= " and SBV_VOTE_L < 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-115d]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_vote_negatif) = mysqli_fetch_row ($result);
      if ($vote < 0) $nb_vote_negatif++;
      if ($nb_vote_negatif >= intval($t_shoutbox_remove_message_votes) )
      {
        $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
        $requete .= " WHERE ID_SHOUT = " . $id_shout;
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-115e]", $requete);
        //
        $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
        $requete .= " WHERE ID_SHOUT = " . $id_shout;
        $requete .= " LIMIT 1 "; // (to protect)
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-115f]", $requete);
        //
        echo ">F85#KO#5#" . $nb_vote_negatif . "#"; // over quota
        $deja_vote = "X";
      }
    }
  }
  //
  if ($deja_vote == "")
  {
    // auteur du message
    $requete  = " select ID_USER_AUT ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " WHERE ID_SHOUT = " . $id_shout;
    $requete .= " and ID_USER_AUT <> " . $id_user;    // on ne peut pas voter pour soi...
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-115g]", $requete);
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
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ( ID_SHOUT, ID_USER_VOTE, ID_USER_AUT, SBV_VOTE_M, SBV_VOTE_L, SBV_DATE ) ";
      $requete .= " VALUES (" . $id_shout . ", " . $id_user . ", " . $id_aut . ", " . $vote_p  . ", " . $vote_c . ",  CURDATE() ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-115h]", $requete);
      //
      // On met à jour la moyenne (votes du messages)
      $requete  = " select sum(SBV_VOTE_M) + sum(SBV_VOTE_L)";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
      $requete .= " WHERE ID_SHOUT = " . $id_shout;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-115j]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($rating) = mysqli_fetch_row ($result);
        //if ($rating <> 0)
        $requete  = " UPDATE " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
        $requete .= " set SBX_RATING = " . $rating;
        $requete .= " WHERE ID_SHOUT = " . $id_shout;
        $requete .= " limit 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-115k]", $requete);
      }
      //
      //
      echo ">F85#OK##"; // vote bien enregistré (le faire APRES avoir actualisé la moyenne !).
      //
      //
      //
      stats_sbx_add_note_user($id_aut, $vote);
      //
      // Meilleurs scores
      stats_sbx_update_scores($id_shout, $id_aut);
    }
  }
  //
  mysqli_close($id_connect);
}
?>
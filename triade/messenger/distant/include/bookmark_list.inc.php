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
if (isset($_GET['ic'])) $id_categ = intval($_GET['ic']);  else  $id_categ = "0";  
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_categ)) $id_categ = "0";
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
  require("lang.inc.php"); // pour format date et heure.   et $l_admin_users_admin !
  //
  if (!ctype_alnum($dt_f))    $dt_f = "";  // après functions.inc.php !
  $dt_form = "d/m/Y"; // FR
  if ($dt_f == 'EN') $dt_form = "m-d-Y";
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F55#KO#1#"); // 1:session non ouverte.
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
  if ($t_bookmarks == "")
  {
    die(">F55#KO#2#"); // 2: Not allowed (option not activated)
  }
  //
  // $last_id_m  < 0 : juste récupérer le dernier ID.
  // $last_id_m == 0 : afficher bookmarks.
  // $last_id_m  > 0 : n'afficher les bookmarks que si ya du nouveau.
  if ($last_id_m <> 0)
  {
    $requete  = " select max(ID_BOOKMARK)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE BMK_DISPLAY > 0 ";
    //$requete .= " and ID_BOOKMCATEG = " . $id_categ;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-130a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_max_msg) = mysqli_fetch_row ($result);
      if ( ($id_max_msg > $last_id_m) and ($last_id_m > 0) )
        $last_id_m = 0; // pour afficher la liste
      else
        echo ">F55#-#A#" . $id_max_msg . "##";
    }
  }
  //
  if ($last_id_m == 0)
  {
    if ($id_categ > 0) 
    {
      $requete  = " select BMK.ID_BOOKMARK, null, BMK.ID_USER_AUT, BMK.BMK_DATE, BMK.BMK_RATING, BMK.BMK_TITLE, BMK.BMK_URL, sum(BMV.BMV_VOTE_M), sum(BMV.BMV_VOTE_L) "; // SBX_NB_VOTE_M
      $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK as BMK ";
      $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE as BMV ON ( BMK.ID_BOOKMARK = BMV.ID_BOOKMARK ) ";
      $requete .= " WHERE BMK.BMK_DISPLAY > 0 ";
      $requete .= " and BMK.ID_BOOKMCATEG = " . $id_categ;
      $requete .= " GROUP BY BMK.ID_BOOKMARK ";
      if ($t_bookmarks_allow_vote != "")
        $requete .= " ORDER BY BMK_RATING DESC, BMK_TITLE ";
      else
        $requete .= " ORDER BY BMK.BMK_TITLE ";
    }
    else
    {
      $categ_arr = array();
      $requete  = " SELECT BMC.ID_BOOKMCATEG , BMC.BMC_TITLE ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG as BMC ";
      //$requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK as BMK, " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG as BMC ";
      //$requete .= " WHERE BMK.ID_BOOKMCATEG = BMC.ID_BOOKMCATEG ";
      //$requete .= " and BMK.BMK_DISPLAY > 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-130b]", $requete);
      $nb_lig = mysqli_num_rows($result);
      if ( $nb_lig > 0 )
      {
        while( list ($id_categ, $categ) = mysqli_fetch_row ($result) )
        {
          $categ_arr[$id_categ] = $categ;
        }
      }
      //
      $requete  = " select BMK.ID_BOOKMARK, BMK.ID_BOOKMCATEG, BMK.ID_USER_AUT, BMK.BMK_DATE, BMK.BMK_RATING, BMK.BMK_TITLE, BMK.BMK_URL, sum(BMV.BMV_VOTE_M), sum(BMV.BMV_VOTE_L) "; // SBX_NB_VOTE_M
      $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK as BMK ";
      $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE as BMV ON ( BMK.ID_BOOKMARK = BMV.ID_BOOKMARK ) ";
      $requete .= " WHERE BMK.BMK_DISPLAY > 0 ";
      ///if ($id_categ > 0) $requete .= " and BMK.ID_BOOKMCATEG = " . $id_categ;
      $requete .= " GROUP BY BMK.ID_BOOKMARK ";
      if ($t_bookmarks_allow_vote != "")
        $requete .= " ORDER BY BMK_RATING DESC, BMK_TITLE ";
      else
        $requete .= " ORDER BY BMK.BMK_TITLE ";
        //$requete .= " ORDER BY BMK.ID_BOOKMARK DESC ";
    }
    //
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-130c]", $requete);
    $nb_lig = mysqli_num_rows($result);
    if ( $nb_lig > 0 )
    {
      echo ">F55#OK#" . $nb_lig . "###|";
      while( list ($id_book, $id_categ, $id_aut, $s_date, $rating, $title, $url, $nb_vote_m, $nb_vote_l) = mysqli_fetch_row ($result) )
      {
        $s_date = date($dt_form, strtotime($s_date));
        if ($id_aut > 0)
        {
          //$username = f_get_username_of_id($id_aut);
          $username = f_get_username_nickname_of_id($id_aut); // avec espaces et majuscules
        }
        else
          $username = $l_admin_users_admin;
        //
        $categ = "";
        if ($id_categ > 0) $categ = $categ_arr[$id_categ];
        //
        $msg  = "#" . $id_book . "#" . $categ . "#" . $id_aut . "#" . $username . "#" . $s_date . "#" . f_decode64_wd($title);
        $msg .= "#" . f_decode64_wd($url) . "#" . $nb_vote_m . "#" . $nb_vote_l . "#"; // $rating . "#" . 
        $msg = f_encode64($msg);
        echo $msg . "|"; // séparateur de ligne : '|' (pipe).
      }
    }
    else
    {
      // renvoie : la liste est vide.
      echo ">F55#-#B##";
    }
  }
  //
  mysqli_close($id_connect);
}
?>
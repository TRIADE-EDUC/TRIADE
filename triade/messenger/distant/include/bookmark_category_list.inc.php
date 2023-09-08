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
$n_version =	intval($_GET['v']);
//if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
//if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $n_version)) $n_version = "";
//
if ( ($id_user > 0) and ($n_version > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  /*
  require ("../common/sessions.inc.php");
  if (f_verif_id_session_id_user($id_user, $id_session) != 'OK') 
  {
    die(">F54#KO#1#"); // 1:session non ouverte.
  }
  */
  //
  //
  /*
  $t_bookmarks = _BOOKMARKS;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_bookmarks = f_role_permission($id_role, "BOOKMARKS", _BOOKMARKS);
    }
  }
  //
  if ($t_bookmarks == "")
  {
    die(">F54#KO#2#"); // 2: Not allowed (option not activated)
  }
  */
  //
  $requete  = " SELECT distinct BMC.ID_BOOKMCATEG , BMC.BMC_TITLE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK as BMK, " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG as BMC ";
  $requete .= " WHERE BMK.ID_BOOKMCATEG = BMC.ID_BOOKMCATEG ";
  $requete .= " and BMK.BMK_DISPLAY > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-133a]", $requete);
  $nb_lig = mysqli_num_rows($result);
  if ( $nb_lig > 0 )
  {
    echo ">F54#OK#" . $nb_lig . "###|";
    while( list ($id_categ, $categ) = mysqli_fetch_row ($result) )
    {
      $msg  = "#" . $id_categ . "#" . $categ . "#";
      $msg = f_encode64($msg);
      echo $msg . "|"; // séparateur de ligne : '|' (pipe).
    }
  }
  else
  {
    // renvoie : la liste est vide.
    echo ">F54#-#B##";
  }
  //
  mysqli_close($id_connect);
}
?>
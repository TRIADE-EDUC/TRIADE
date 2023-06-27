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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) or (!isset($_GET['g'])) ) die();
//
$id_user =	  f_decode64_wd($_GET['iu']);
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
$other_or_join = $_GET['g'];
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $n_version)) $n_version = "";
//
if ( ($id_user > 0) and ($n_version > 0) and ($other_or_join != "") and ($ip != "") )
{
  //if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  {
    require ("../common/acces.inc.php");
    f_verif_ip($ip);
    //
    require ("../common/sql.inc.php");
    require ("../common/sessions.inc.php");
    //
    if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F90#KO#1#"); // 1:session non ouverte.
    //
    //
    if ($other_or_join == "o")
    {
      // Liste des groupes dont l'utilisateur n'est pas inscrit :
      $requete  = " SELECT GRP.ID_GROUP, GRP.GRP_NAME, GRP.GRP_PRIVATE ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP as GRP ";
      $requete .= " WHERE NOT EXISTS (";
      $requete .= "         SELECT ID_USER FROM " . $PREFIX_IM_TABLE . "USG_USERGRP as USG ";
      $requete .= "         WHERE USG.ID_GROUP = GRP.ID_GROUP ";
      $requete .= "         and USG.ID_USER = " . $id_user;
      $requete .= " ) ";
      $requete .= " and GRP_PRIVATE < 2 "; //  +2 : on ne peut pas s'y inscrire  (mais l'a demandé).
      $requete .= " ORDER BY UPPER(GRP_NAME) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-120a]", $requete);
    }
    else
    {
      // Liste des groupes auquel l'utilisateur appartient
      $requete  = " SELECT GRP.ID_GROUP, GRP.GRP_NAME, GRP.GRP_PRIVATE ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
      $requete .= " WHERE USG.ID_GROUP = GRP.ID_GROUP ";
      $requete .= " and USG.ID_USER = " . $id_user;
      //$requete .= " and GRP_PRIVATE > -2 "; // -2 : on ne peut pas se désincrire (mais l'a demandé).
      $requete .= " and USG.USG_PENDING < 2 "; // -2 : on ne peut pas se désincrire (mais l'a demandé).
      $requete .= " GROUP BY ID_GROUP ";
      $requete .= " ORDER BY UPPER(GRP_NAME) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-120b]", $requete);
    }
    //
    $nb_grp = mysqli_num_rows($result);
    if ( $nb_grp > 0 )
    {
      echo ">F90#OK#" . $nb_grp . "###|";
      while( list ($id_group, $group_name, $grp_private) = mysqli_fetch_row ($result) )
      {
        $msg = "#" . $id_group . "#" . $group_name . "#" . $grp_private . "###";
        $msg = f_encode64($msg);
        echo $msg . "|"; // séparateur de ligne : '|' (pipe).
      }
    }
    else
    {
      // renvoie : aucun groupe
      echo ">F90#-#B##";
    }
    //
    mysqli_close($id_connect);
  }
  else
    die(">F90#KO#2#"); // 2: Not allowed (option not activated)
}
?>
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
$id_user =	  f_decode64_wd($_GET['iu']);
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $n_version)) $n_version = "";
//
if ( ($id_user > 0) and ($n_version > 0) and ($ip != "") )
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
    if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F87#KO#1#"); // 1:session non ouverte.
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
      die(">F87#KO#2#"); // 2: Not allowed (option not activated)
    }
    //
    /*
    $requete  = " SELECT GRP.ID_GROUP, GRP.GRP_NAME, GRP.GRP_SBX_NEED_APPROVAL, GRP.GRP_PRIVATE "; // , count(SBX.ID_SHOUT) , max(SBX.ID_SHOUT)
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG, " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX SBX ";
    $requete .= " WHERE USG.ID_GROUP = GRP.ID_GROUP ";
    $requete .= " and SBX.ID_GROUP_DEST = GRP.ID_GROUP ";
    */
    $requete  = " SELECT GRP.ID_GROUP, GRP.GRP_NAME, GRP.GRP_SBX_NEED_APPROVAL, GRP.GRP_PRIVATE "; // , count(SBX.ID_SHOUT) , max(SBX.ID_SHOUT)
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE USG.ID_GROUP = GRP.ID_GROUP ";
    $requete .= " and USG.ID_USER = " . $id_user;
    $requete .= " and GRP.GRP_SHOUTBOX > 0 ";
    $requete .= " GROUP BY ID_GROUP ";
    $requete .= " ORDER BY UPPER(GRP_NAME) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-116a]", $requete);
    $nb_grp = mysqli_num_rows($result);
    if ( $nb_grp > 0 )
    {
      echo ">F87#OK#" . $nb_grp . "###|";
      while( list ($id_group, $group_name, $sbx_need_approval, $grp_private) = mysqli_fetch_row ($result) )
      {
        $msg = "#" . $id_group . "#" . $group_name . "#" . $sbx_need_approval . "#" . $grp_private . "###";
        $msg = f_encode64($msg);
        echo $msg . "|"; // séparateur de ligne : '|' (pipe).
      }
    }
    else
    {
      // renvoie : aucun groupe
      echo ">F87#-#B##";
    }
    //
    mysqli_close($id_connect);
  }
  else
    die(">F87#KO#2#"); // 2: Not allowed (option not activated)
}
?>
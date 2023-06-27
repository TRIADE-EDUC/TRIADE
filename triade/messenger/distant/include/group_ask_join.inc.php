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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ig'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$id_grp =	      intval(f_decode64_wd($_GET['ig']));
$ip = 			    f_decode64_wd($_GET['ip']);
$version =      intval($_GET['v']);
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $id_grp)) $id_grp = "";
//
if ( ($id_user > 0) and ($id_grp > 0) and ($ip != "") )
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
    if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F91#KO#1#"); // 1:session non ouverte.
    //
    //
    $t_group_user_can_join = _GROUP_USER_CAN_JOIN;
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
    {
      require ("../common/roles.inc.php");
      $id_role = f_role_of_user($id_user);
      //
      if ($id_role > 0)
      {
        $t_group_user_can_join = f_role_permission($id_role, "GROUP_USER_CAN_JOIN", _GROUP_USER_CAN_JOIN);
      }
    }
    //
    if ($t_group_user_can_join != "")
    {
      $requete  = " SELECT GRP_PRIVATE ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP ";
      $requete .= " WHERE ID_GROUP = " . $id_grp;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-121a]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($grp_private) = mysqli_fetch_row ($result);
        //
        //if (intval($grp_private) == 2) -> no, because private !
        //
        // Official : need pending :
        if (intval($grp_private) == 1)
        {
          $requete  = "INSERT INTO " . $PREFIX_IM_TABLE . "USG_USERGRP (ID_GROUP, ID_USER, USG_PENDING) ";
          $requete .= "VALUES (" . $id_grp . ", " . $id_user . ", 1 ) ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-121b]", $requete);
        }
        // Public
        if (intval($grp_private) == 0)
        {
          $requete  = "INSERT INTO " . $PREFIX_IM_TABLE . "USG_USERGRP (ID_GROUP, ID_USER) ";
          $requete .= "VALUES (" . $id_grp . ", " . $id_user . " ) ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-121c]", $requete);
        }
        //
        echo ">F91#OK#" . $grp_private . "##"; 
        //
        update_last_activity_user($id_user);
      }
    }
    else
      die (">F91#KO#2#"); // 2: Not allowed (option not activated)
    //
    mysqli_close($id_connect);
  }
  else
    die(">F91#KO#2#"); // 2: Not allowed (option not activated)
}
?>
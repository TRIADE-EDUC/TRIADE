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


function user_add_valid_contact($id_user_1, $id_user_2)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
	$id_user_1 = intval($id_user_1);
	$id_user_2 = intval($id_user_2);
  if ( ($id_user_1 > 0 ) and ($id_user_2 > 0) and ($id_user_1 <> $id_user_2) )
  {
    if ( f_is_deja_in_contacts_id($id_user_1, $id_user_2) == 0 )
    {
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "CNT_CONTACT ";
      $requete .= " (ID_USER_1, ID_USER_2, CNT_STATUS) VALUES ";
      $requete .= " (" . $id_user_1 . ", " . $id_user_2 . ", 1) , ";
      $requete .= " (" . $id_user_2 . ", " . $id_user_1 . ", 1) ;";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-A7a]", $requete);
    }
  }
}


function user_add_in_default_group($id_user, $id_grp)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  if ( (_GROUP_FOR_SBX_AND_ADMIN_MSG != "")  XOR  (_SPECIAL_MODE_GROUP_COMMUNITY != '') XOR (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') )
  {
    $id_user = intval($id_user);
    $id_grp  = intval($id_grp);
    if ( ($id_user > 0 ) and ($id_grp > 0) )
    {
      // Check if group exist :
      $requete  = " SELECT GRP_NAME ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP ";
      $requete .= " WHERE ID_GROUP = " . $id_grp;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-A7b]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($grp_name) = mysqli_fetch_row ($result);
        if ($grp_name != "")
        {
          $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "USG_USERGRP ";
          $requete .= " (ID_GROUP, ID_USER) VALUES ";
          $requete .= " (" . $id_grp . ", " . $id_user . ") ;";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-A7c]", $requete);
        }
      }
    }
  }
}


?>
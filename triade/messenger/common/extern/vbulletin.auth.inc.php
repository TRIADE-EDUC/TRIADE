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

define("_EXTERNAL_AUTHENTICATION_NAME", "vBulletin");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si vBulletin n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  //$requete  = " select LOWER(username), password, salt FROM " . $extern_prefix . "users ";  version 3.6
  $requete  = " select LOWER(username), password, salt FROM " . $extern_prefix . "user ";
  $requete .= " WHERE LOWER(username) = '" . $t_user . "' ";
  //$requete .= " and user_active = 1 ";
  //$requete .= " and usergroupid in (2,5,6,7,9,10,11)  ";
  //$requete .= " and posts >= 5 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T4a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_vbulletin, $pass_vbulletin, $salt_vbulletin) = mysqli_fetch_row ($result);
    $passcr_1 = md5($t_pass . $salt_vbulletin);
    $passcr_2 = md5(md5($t_pass) . $salt_vbulletin);
    if ($login_vbulletin == $t_user)
    { 
      if ( ($pass_vbulletin == $passcr_1) or ($pass_vbulletin == $passcr_2) ) $t_verif_pass = "OK";
    }
  }
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysqli_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $t_verif_pass;
}



function f_extern_nb_unread_pm($t_user)
{
  GLOBAL $id_connect;
  //
  $nb_pm = 0;
  require("../common/config/extern/vbulletin.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si vBulletin n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(username_clean), pmunread FROM " . $extern_prefix . "user ";
  $requete .= " WHERE LOWER(username) = '" . $t_user . "' ";
  //$requete .= " and user_active = 1 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T4b]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $nb_pm) = mysqli_fetch_row ($result);
    if ($login_extern != $t_user) $nb_pm = 0;
    //
  }
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysqli_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $nb_pm;
}
?>
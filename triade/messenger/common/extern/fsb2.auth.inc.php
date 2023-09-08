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

define("_EXTERNAL_AUTHENTICATION_NAME", "Fire Soft Board");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si Fire Soft Board n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select SQL_CACHE cfg_value FROM " . $extern_prefix . "config ";
  $requete .= " WHERE cfg_name = 'fsb_hash' ";
  $requete .= " limit 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T161a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($salt_extern) = mysqli_fetch_row ($result);
    $passcr = sha1($t_pass . $salt_extern);
    //
    $requete  = " select LOWER(USR.u_nickname), u_password ";
    $requete .= " FROM " . $extern_prefix . "users USR , " . $extern_prefix . "users_password PWD ";
    $requete .= " WHERE USR.u_id = PWD.u_id ";
    $requete .= " and LOWER(USR.u_nickname) = '" . $t_user . "' ";
    $requete .= " and USR.u_activated = 1 ";
    $requete .= " limit 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-T161b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
      if ( ($login_extern == $t_user) and ($pass_extern == $passcr) )
        $t_verif_pass = "OK";
      //
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

?>
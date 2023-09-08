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

define("_EXTERNAL_AUTHENTICATION_NAME", "SocialEngine");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si socialengine n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(user_username ), user_password, user_code FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(user_username ) = '" . $t_user . "' ";
  $requete .= " AND user_logins > 0 ";
  $requete .= " AND user_enabled > 0 ";
  $requete .= " AND user_verified > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T107a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern, $salt_extern) = mysqli_fetch_row ($result);
    $passcr = $t_pass;
    if ($salt_extern != "")
    {
      list($salt1, $salt2) = str_split($salt_extern, ceil(strlen($salt_extern) / 2));
      $passcr = $salt1 . $t_pass . $salt2;
    }
    //
    if ( ($login_extern == $t_user) and ( ($pass_extern == md5($passcr)) or ($pass_extern == sha1($passcr)) or ($pass_extern == sha1($passcr)) ) )
      $t_verif_pass = "OK";
    //
    if ( ($login_extern == $t_user) and ($pass_extern == crypt($t_pass, '$1$'.str_pad(substr($salt_extern, 0, 8), 8, '0', STR_PAD_LEFT).'$')) )
      $t_verif_pass = "OK";
    //
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
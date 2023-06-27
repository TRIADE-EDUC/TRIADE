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

define("_EXTERNAL_AUTHENTICATION_NAME", "ZenCart");

function f_external_authentication($t_user, $t_pass)
{
  $t_verif_pass = "Ko";
  //
  GLOBAL $id_connect;
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si zencart n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  // Customers
  //
  $requete  = " select LOWER(customers_email_address), customers_password FROM " . $extern_prefix . "customers ";
  $requete .= " WHERE LOWER(customers_email_address) = '" . $t_user . "' ";
  $requete .= " AND customers_authorization = 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T113a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ( ($login_extern == $t_user)
    {
      $salt = strstr($pass_extern, ':');
      $pass_extern = substr($pass_extern, 0, strlen($pass_extern) - strlen($salt));
      $salt = substr($salt, 1, strlen($salt)-1);
      if (md5($salt . $t_pass) == $pass_extern) $t_verif_pass = "OK";
    }
  }
  //
  // Admins
  //
  if ($t_verif_pass != "OK")
  {
    $requete  = " select LOWER(admin_name), admin_pass FROM " . $extern_prefix . "admin ";
    $requete .= " WHERE LOWER(admin_name) = '" . $t_user . "' ";
    //$requete .= " AND admin_level =  1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-T113b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
      if ( ($login_extern == $t_user)
      {
        $salt = strstr($pass_extern, ':');
        $pass_extern = substr($pass_extern, 0, strlen($pass_extern) - strlen($salt));
        $salt = substr($salt, 1, strlen($salt)-1);
        if (md5($salt . $t_pass) == $pass_extern) $t_verif_pass = "OK";
      }
    }
  }
  //
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysqli_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $t_verif_pass;
}
?>
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

define("_EXTERNAL_AUTHENTICATION_NAME", "Freeway");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  $try_to_hack = "";
  //
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si Freeway n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  // ---- Admin ----
  //
  $requete  = " select LOWER(admin_email_address), admin_password FROM " . $extern_prefix . "employee ";
  $requete .= " WHERE LOWER(admin_email_address) = '" . $t_user . "' ";
  $requete .= " and admin_hide_backend = 'N' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T120a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user)
    { 
      // $pass_extern contient le mot de pass cripté en md5 avec hash (md5(pass+hash) suivi après ':' du hash.
      $tt = explode (':', $pass_extern);
      if ( ($tt[0] != '') and ($tt[1] != '') )
      {
        $passcr = md5($tt[1] . $t_pass);
        if ($tt[0] == $passcr) 
          $t_verif_pass = "OK";
        else
          $try_to_hack = "!!!";
      }
    } 
  }
  //
  if ($try_to_hack == "")
  {
    //
    // ---- Customers ----
    //
    $requete  = " select LOWER(customers_username), customers_password FROM " . $extern_prefix . "customers ";
    $requete .= " WHERE LOWER(customers_username) = '" . $t_user . "' ";
    $requete .= " and is_blocked = 'N' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-T120b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
      if ($login_extern == $t_user)
      { 
        // $pass_extern contient le mot de pass cripté en md5 avec hash (md5(pass+hash) suivi après ':' du hash.
        $tt = explode (':', $pass_extern);
        if ( ($tt[0] != '') and ($tt[1] != '') )
        {
          $passcr = md5($tt[1] . $t_pass);
          if ($tt[0] == $passcr) $t_verif_pass = "OK";
        }
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
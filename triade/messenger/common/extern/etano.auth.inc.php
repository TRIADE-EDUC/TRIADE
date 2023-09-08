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

define("_EXTERNAL_AUTHENTICATION_NAME", "Etano");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  $try_to_hack = "";
  $passcr = md5($t_pass);
  //
  $t_user = trim($t_user);
  if ( ($t_user != "") and ($passcr != "") )
  {
    //
    require("../common/config/extern.config.inc.php");
    if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
    {
      // Si Etano n'est pas sur le même serveur ou la même base de donnée.
      mysqli_close($id_connect);
      require("extern.sql.inc.php");
      $id_connect = $id_connect_extern;
    }
    //
    // ---- Admin ----
    //
    $requete  = " select LOWER(user), pass FROM " . $extern_prefix . "admin_accounts ";
    $requete .= " WHERE LOWER(user) = '" . $t_user . "' ";
    $requete .= " and status = 15 ";
    //$requete .= " and dept_id = 4 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-T154a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nom_extern, $pass_extern) = mysqli_fetch_row ($result);
      if ($nom_extern == $t_user)
      {
        if ($pass_extern == $passcr) 
          $t_verif_pass = "OK";
        else
          $try_to_hack = "!!!";
      }
    }
    //
    if ($try_to_hack == "")
    {
      //
      // ---- Customers ----
      //
      $requete  = " select LOWER(user), pass passwd FROM " . $extern_prefix . "user_accounts ";
      $requete .= " WHERE LOWER(user) = '" . $t_user . "' ";
      $requete .= " and status > 10 ";
      //$requete .= " and membership = 2 ";
      //$requete .= " and temp_pass = '' ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-T154b]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($nom_extern, $pass_extern) = mysqli_fetch_row ($result) )
        {
          if ( ($nom_extern == $t_user) and ($pass_extern == $passcr) )
            $t_verif_pass = "OK";
          //
        }
      }
    }
    //
    if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
    {
      mysqli_close($id_connect_extern);
      require("sql.2.inc.php");
    }
  }
  //
  return $t_verif_pass;
}
?>
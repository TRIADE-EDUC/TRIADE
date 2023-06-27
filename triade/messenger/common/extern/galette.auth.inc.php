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

define("_EXTERNAL_AUTHENTICATION_NAME", "Galette");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //$passcr = md5($t_pass);
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si Galette n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(login_adh), mdp_adh FROM " . $extern_prefix . "adherents ";
  $requete .= " WHERE LOWER(login_adh) = '" . $t_user . "' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T162a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user)
    {
      if ($pass_extern == md5($t_pass))
        $t_verif_pass = "OK";
      //
      if ($pass_extern == sha1($t_pass))
        $t_verif_pass = "OK";
      //
      if ($t_verif_pass != "OK")
      {
        $ret = crypt($t_pass, $pass_extern);
        if (strlen($ret) == strlen($pass_extern))
          $t_verif_pass = "OK";
      }
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
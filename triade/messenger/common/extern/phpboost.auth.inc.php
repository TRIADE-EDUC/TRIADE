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

define("_EXTERNAL_AUTHENTICATION_NAME", "PHPBoost");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  $passcr = md5($t_pass);
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si PHPBoost n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(login), password FROM " . $extern_prefix . "member ";
  $requete .= " WHERE LOWER(login) = '" . $t_user . "' ";
  //$requete .= " and level > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T61a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user) 
    {    
      if ($pass_extern == $passcr) 
        $t_verif_pass = "OK";
      else
      {
        if (phpversion() >='5.1.2')
        {
          $passcr = md5($t_pass).$t_pass;
          $passcr = hash('sha256',$passcr);
        }
        else
        {
          require("../common/library/sha256.class.php");
          $passcr = md5($t_pass).$t_pass;
          $passcr = SHA256::hash($passcr);
        }
        if ($pass_extern == $passcr)  $t_verif_pass = "OK";
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
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

define("_EXTERNAL_AUTHENTICATION_NAME", "Dotclear 2");

function hmac($key,$data)
{
  $blocksize = 64;
  $hashfunc = 'sha1';
  if (strlen($key)>$blocksize) $key = pack('H*', $hashfunc($key));
  $key = str_pad($key, $blocksize, chr(0x00));
  $ipad = str_repeat(chr(0x36), $blocksize);
  $opad = str_repeat(chr(0x5c), $blocksize);
  $hmac = pack('H*', $hashfunc(($key^$opad) . pack('H*', $hashfunc(($key^$ipad) . $data))));
  //
  return bin2hex($hmac);
}

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  //
  $passcr = hmac(DC_MASTER_KEY, $t_pass);
  //
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si Dotclear 2  n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(user_id), user_pwd FROM " . $extern_prefix . "user ";
  $requete .= " WHERE LOWER(user_id) = '" . $t_user . "' ";
  //$requete .= " and user_status > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T45a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ( ($login_extern == $t_user) and ($pass_extern == $passcr) )
      $t_verif_pass = "OK";
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
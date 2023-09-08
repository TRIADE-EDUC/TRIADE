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

define("_EXTERNAL_AUTHENTICATION_NAME", "ImpressCMS");

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
    // Si ImpressCMS n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(uname), pass, salt, enc_type FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(uname) = '" . $t_user . "' ";
  //$requete .= " and last_login > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T64a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    $mainSalt = SDATA_DB_SALT;
    list ($login_extern, $pass_extern, $salt_extern, $enc_type) = mysqli_fetch_row ($result);
  	if ($enc_type ==  0) $passcr = md5($pass);
  	if ($enc_type ==  1) $passcr = hash('sha256', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  2) $passcr = hash('sha384', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  3) $passcr = hash('sha512', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  4) $passcr = hash('ripemd128', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  5) $passcr = hash('ripemd160', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  6) $passcr = hash('whirlpool', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  7) $passcr = hash('haval128,4', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  8) $passcr = hash('haval160,4', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type ==  9) $passcr = hash('haval192,4', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 10) $passcr = hash('haval224,4', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 11) $passcr = hash('haval256,4', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 12) $passcr = hash('haval128,5', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 13) $passcr = hash('haval160,5', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 14) $passcr = hash('haval192,5', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 15) $passcr = hash('haval224,5', $salt_extern . md5($pass) . $mainSalt);
  	if ($enc_type == 16) $passcr = hash('haval256,5', $salt_extern . md5($pass) . $mainSalt);
    if ( ($login_extern == $t_user) and ($pass_extern == $passcr) )
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
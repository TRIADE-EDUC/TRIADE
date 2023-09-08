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

define("_EXTERNAL_AUTHENTICATION_NAME", "OBM");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si OBM n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(userobm_login), userobm_password, userobm_password_type FROM " . $extern_prefix . "UserObm ";
  $requete .= " WHERE LOWER(userobm_login) = '" . $t_user . "' ";
  //$requete .= " and userobm_perms = 'user' "; // admin editor admin_delegue
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T60a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern, $pass_type) = mysqli_fetch_row ($result);
    if ( ($login_extern == $t_user)
    {
      if ($pass_type == "MD5SUM")
      { 
        $passcr = md5($t_pass);
        if ($pass_extern == $passcr)  $t_verif_pass = "OK";
      }
      //if ($pass_type == "CRYPT")
    }
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



function f_extern_name_of_user($t_user)
{
  GLOBAL $id_connect;
  //
  $family_and_first_name = "";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si OBM n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(userobm_login), userobm_lastname, userobm_firstname FROM UserObm "; // userobm_title userobm_id
  $requete .= " WHERE LOWER(userobm_login) = '" . $t_user . "' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T60b]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $extern_familyname, $extern_firstname) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user)
      $family_and_first_name = ucfirst(trim($extern_firstname)) . " " . strtoupper(trim($extern_familyname));
    //
  }
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysqli_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $family_and_first_name;
}
?>

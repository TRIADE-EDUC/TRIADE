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

define("_EXTERNAL_AUTHENTICATION_NAME", "FUDforum");

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
    // Si FUDforum n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(login), passwd, salt FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(login) = '" . $t_user . "' ";
  //$requete .= " and level_id > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T31a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern, $salt_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user) 
    {
      if ($pass_extern == md5($t_pass))  $t_verif_pass = "OK";
      if ($pass_extern == sha1($salt_extern . sha1($t_pass)))  $t_verif_pass = "OK";
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


function f_extern_nb_unread_pm($t_user)
{
  GLOBAL $id_connect;
  //
  $nb_pm = 0;
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si fudforum n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select count(*) FROM " . $extern_prefix . "users USR, " . $extern_prefix . "pmsg MSG"; 
  $requete .= " WHERE USR.id = MSG.duser_id ";
  $requete .= " and LOWER(USR.username) = '" . $t_user . "' ";
  //$requete .= " and level_id > 0 ";
  $requete .= " and MSG.read_stamp = 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T31b]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($nb_pm) = mysqli_fetch_row ($result);
  }
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysqli_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $nb_pm;
}
?>
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

define("_EXTERNAL_AUTHENTICATION_NAME", "Joomla");

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
    // Si Joomla n'est pas sur le m�me serveur ou la m�me base de donn�e.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(username), password FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(username) = '" . $t_user . "' ";
  $requete .= " and block = 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T5a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user)
    { 
      if (strstr($pass_extern, ":"))
      {
        // $pass_extern contient le mot de pass cript� en md5 avec hash (md5(pass+hash) suivi apr�s ':' du hash.
        $tt = explode (':', $pass_extern);
        if ( ($tt[0] != '') and ($tt[1] != '') )
        {
          $passcr = md5($t_pass . $tt[1]);
          if ($tt[0] == $passcr) $t_verif_pass = "OK";
        }
        else
        {
          $passcr = md5($t_pass);
          if ($pass_extern == $passcr) $t_verif_pass = "OK";
        } 
      } 
      //
      if ($t_verif_pass != "OK")
      {
        if (substr($pass_extern, 0, 3) == '$H$') $pass_extern = '$P$' . substr($pass_extern, 3, strlen($pass_extern)-1);
        require ("../common/library/PasswordHash.php");
        $t_hasher = new PasswordHash(8, FALSE);
        $check = $t_hasher->CheckPassword($t_pass, $pass_extern);
        if ($check) $t_verif_pass = "OK";
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


function f_extern_nb_unread_pm($t_user)
{
  GLOBAL $id_connect;
  //
  $nb_pm = 0;
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si Joomla n'est pas sur le m�me serveur ou la m�me base de donn�e.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select id FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(username) = '" . $t_user . "' ";
  $requete .= " and block = 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T5b]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($id_extern) = mysqli_fetch_row ($result);
    //
    $requete  = " select count(*) FROM " . $extern_prefix . "messages ";
    $requete .= " WHERE user_id_to = " . $id_extern . " ";
    $requete .= " and state = 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-T5b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_pm) = mysqli_fetch_row ($result);
    }
  }
  //
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysqli_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $nb_pm;
}
?>
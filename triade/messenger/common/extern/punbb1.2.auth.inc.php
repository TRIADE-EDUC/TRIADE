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

define("_EXTERNAL_AUTHENTICATION_NAME", "PunBB 1.2");

function pun_hash($str)
{
	if (function_exists('sha1'))
		return sha1($str);
	else if (function_exists('mhash'))
		return bin2hex(mhash(MHASH_SHA1, $str));
	else
		return md5($str);
}

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si PunBB n'est pas sur le m�me serveur ou la m�me base de donn�e.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(username), password FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(username) = '" . $t_user . "' ";
  $requete .= " and registered > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T32a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user)
    { 
      $sha1_available = (function_exists('sha1') || function_exists('mhash')) ? true : false;
      $passcr = pun_hash($t_pass);
      //
      if ($sha1_available && $passcr == $form_password_hash) 
        $t_verif_pass = "OK";
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


function f_extern_nb_unread_pm($t_user)
{
  $nb_pm = 0;
/*   MUST have the mod "Private Message" http://www.punres.org/viewtopic.php?pid=25581#p25581     ### >>>
  
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si punbb n'est pas sur le m�me serveur ou la m�me base de donn�e.
    mysql_close();
    require("extern.sql.inc.php");
  }
   //
  $requete  = " select count(*) FROM " . $extern_prefix . "users USR, " . $extern_prefix . "messages MSG"; 
  $requete .= " WHERE USR.id = MSG.owner ";
  $requete .= " and LOWER(USR.username) = '" . $t_user . "' ";
  $requete .= " and USR.registered > 0 ";
  $requete .= " and showed = 0 ";
  $result = mysql_query($requete);
  if (!$result) error_sql_log("[ERR-T32b]", $requete);
  if ( mysql_num_rows($result) == 1 )
  {
    list ($nb_pm) = mysql_fetch_row ($result);
  }
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysql_close($id_connect_extern);
    require("sql.2.inc.php");
  }
*/         ### >>>
  //
  return $nb_pm;
}
?>
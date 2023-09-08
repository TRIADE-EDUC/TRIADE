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

define("_EXTERNAL_AUTHENTICATION_NAME", "PunBB");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  //
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si punbb n'est pas sur le même serveur ou la même base de donnée.
    mysqli_close($id_connect);
    require("extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  //
  $requete  = " select LOWER(username), password, salt FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(username) = '" . $t_user . "' ";
  $requete .= " and registered > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-T32a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($login_extern, $pass_extern, $salt_extern) = mysqli_fetch_row ($result);
    if ($login_extern == $t_user)
    { 
      $passcr = sha1($salt_extern . sha1($t_pass))
      //
      if ($passcr == $pass_extern) $t_verif_pass = "OK";
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


#>>
function f_extern_nb_unread_pm($t_user)
{
  $nb_pm = 0;
/*   MUST have the mod "pun_pm" extension http://punbb.informer.com/extensions/    ### >>>
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si punbb n'est pas sur le même serveur ou la même base de donnée.
    mysql_close();
    require("extern.sql.inc.php");
  }
   //
  $requete  = " select count(*) FROM " . $extern_prefix . "users USR, pun_pm_messages MSG"; 
  $requete .= " WHERE USR.id = MSG.receiver_id ";
  $requete .= " and LOWER(USR.username) = '" . $t_user . "' ";
  $requete .= " and USR.registered > 0 ";
  $requete .= " and read_at  = 0 ";
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
#>>


/*   MUST have the mod "Private Message" extension : http://www.punres.org/viewtopic.php?pid=25581#p25581    ##### >>>
function f_punbb_nb_unread_pm($t_user)
{
  $nb_pm = 0;
  require("../common/config/extern.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // Si punbb n'est pas sur le même serveur ou la même base de donnée.
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
  if (!$result) error_sql_log("[ERR-T32c]", $requete);
  if ( mysql_num_rows($result) == 1 )
  {
    list ($nb_pm) = mysql_fetch_row ($result);
  }
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    mysql_close($id_connect_extern);
    require("sql.2.inc.php");
  }
  //
  return $nb_pm;
}
*/         ##### >>>


?>
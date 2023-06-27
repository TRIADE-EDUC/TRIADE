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
//
if (isset($_GET['id_user'])) $id_user = intval($_GET['id_user']);  else $id_user = 0;
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else $tri = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['from'])) $from = $_GET['from']; else $from = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else  $only_status = "";
//
//
if ( ($from == 'user') and ($id_user > 0) )
  $url = "user.php?id_user=" . $id_user . "&lang=" . $lang . "&only_status=" . $only_status . "&";
else
  $url = "list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&only_status=" . $only_status . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    require ("../common/functions.inc.php");
    $username = f_get_username_of_id($id_user);
    //
    // Suppression du mot de passe.
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " set USR_PASSWORD = '', USR_PWD_ERRORS = 0 ";
    $requete .= " WHERE ID_USER = " . $id_user;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-B5]", $requete);
    //
    mysqli_close($id_connect);
    //
    write_log("log_user_pass_remove.txt", $username . ";" . $id_user);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
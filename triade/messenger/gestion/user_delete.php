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
if (isset($_POST['id_user'])) $id_user = intval($_POST['id_user']);  else $id_user = 0;
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['page'])) $page = $_POST['page']; else $page = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['only_status'])) $only_status = $_POST['only_status']; else $only_status = "";
//
//
$url = "list_users.php?tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&page=" . $page . "&";
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
    delete_user($id_user);
    //
    mysqli_close($id_connect);
    //
    write_log("log_user_delete", $username . ";" . $id_user . ";&nbsp;");
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
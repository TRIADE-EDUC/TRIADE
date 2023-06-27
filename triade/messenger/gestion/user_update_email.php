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
if (isset($_POST['email'])) $email = $_POST['email'];  else $email = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['page'])) $page = $_POST['page']; else $page = "";
if (isset($_POST['from'])) $from = $_POST['from']; else $from = "";
//
//
if ( ($from == 'user') and ($id_user > 0) )
  $url = "user.php?id_user=" . $id_user . "&lang=" . $lang . "&";
else
  $url = "list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user > 0) and (!preg_match("#[^0-9]#", $id_user)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/functions.inc.php");
    //
    $email = trim($email);
    $email = str_replace(" ", "_", $email);
    $email = str_replace("..", ".", $email);
    $email = f_clean_name($email);
    if (!preg_match('/^[-a-z0-9._@]+$/i', $email) ) $email = "";
    //
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " set USR_EMAIL = '" . $email . "' ";
    $requete .= " WHERE ID_USER = " . $id_user;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-B6]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
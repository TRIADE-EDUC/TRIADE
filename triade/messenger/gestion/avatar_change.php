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
if (isset($_POST['id_user_select'])) $id_user_select = intval($_POST['id_user_select']);  else $id_user_select = 0;
if (isset($_POST['avatar'])) $avatar = $_POST['avatar'];  else $avatar = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "user.php?id_user=" . $id_user_select . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_user_select > 0) and (!preg_match("#[^0-9]#", $id_user_select)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " set USR_AVATAR = '" . $avatar . "' ";
    $requete .= " WHERE ID_USER = " . $id_user_select . " ";
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-H1]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
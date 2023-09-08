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
//if (isset($_GET['tri'])) $tri = $_GET['tri'];  else $tri = "";
//if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
//$url = "list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&";
$url = "index.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  define('INTRAMESSENGER',true);
  //
  require ("../common/config/config.inc.php");
  require ("lang.inc.php"); // pour le log
  //
  // Suppression des comptes périmés
  if (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) > 9)
  {
    require ("../common/functions.inc.php");
    require ("../common/sql.inc.php");
    //
    $requete  = " select ID_USER FROM " . $PREFIX_IM_TABLE . "USR_USER ";
    $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION);
    $requete .= " and USR_STATUS <> 4 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-B7m]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($id_user_to_delete) = mysqli_fetch_row ($result) )
      {
        $username = f_get_username_of_id($id_user_to_delete);
        //
        delete_user($id_user_to_delete);
        //
        write_log("log_user_delete", $username . ";" . $id_user_to_delete . ";" . $l_admin_users_out_of_date);
      }
    }
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
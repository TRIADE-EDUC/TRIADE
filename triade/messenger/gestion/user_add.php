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
if (isset($_POST['username'])) $username = trim($_POST['username']);  else $username = "";
if (isset($_POST['nom'])) $nom = $_POST['nom'];  else $nom = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['page'])) $page = $_POST['page']; else $page = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_users.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ($username != "")
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    require ("../common/config/config.inc.php"); // important ! (pour f_if_already_max_nb_users)
    require ("../common/functions.inc.php");
    //
    $username = 	f_clean_username($username);
    $nom = 	      f_clean_name($nom);
    //
    if ( (intval(f_if_already_max_nb_users()) == 0) and ($username != "") )
    {
      $requete  = " insert into " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " (USR_USERNAME, USR_NAME, USR_CHECK, USR_DATE_CREAT, USR_STATUS) ";
      $requete .= " values ('" . $username . "', '" . $nom . "', '', CURDATE(), 3)";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-B2]", $requete);
      //
      write_log("log_user_create", $username . ";" . $nom);
    }
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>
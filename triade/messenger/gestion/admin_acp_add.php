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
if (isset($_POST['adm_username'])) $adm_username = $_POST['adm_username'];  else $adm_username = "";
if (isset($_POST['adm_pass'])) $adm_pass = trim($_POST['adm_pass']);  else $adm_pass = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/functions.inc.php");
require ("../common/acp_auth.inc.php");
$adm_username = f_clean_username($adm_username);
//
//
$url = "list_admin_acp.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( (strlen($adm_username) > 2) and (strlen($adm_pass) > 5) )
  {
    require ("../common/sql.inc.php");
    //
    // Voir aussi /install/install.php
    //
    $cannot = "";
    $requete  = " select LOWER(ADM_USERNAME) ";
    $requete .= " from " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
    $requete .= " WHERE LOWER(ADM_USERNAME) like '" . strtolower($adm_username) . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-P1b]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      while( list ($t_name) = mysqli_fetch_row ($result) )
      {
        if ($t_name == strtolower($adm_username)) $cannot = "X";
      }
    }
    if ($cannot == "")
    {
      $adm_salt = random(20);
      $pass_cr = chiffrer_pass($adm_pass, $adm_salt);
      //
      $requete  = " insert into " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
      $requete .= " (ADM_USERNAME, ADM_PASSWORD, ADM_SALT, ADM_LEVEL, ADM_DATE_CREAT, ADM_DATE_PASSWORD ) ";
      $requete .= " values ('" . $adm_username . "', '" . $pass_cr . "', '" . $adm_salt . "', 1048575, CURDATE(), CURDATE() ) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-P1c]", $requete);
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
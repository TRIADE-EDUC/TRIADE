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
define('INTRAMESSENGER',true);
//
session_start();
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (!isset($_SESSION['acp_login']))
{
  header("location:acp_pass_updating.php");
  die();
}
//
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
//require ("../common/functions.inc.php"); // NON car dans menu !
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//
//
if (isset($_POST['acp_pass_1'])) $acp_pass_1 = trim($_POST['acp_pass_1']); else $acp_pass_1 = "";
if (isset($_POST['acp_pass_2'])) $acp_pass_2 = trim($_POST['acp_pass_2']); else $acp_pass_2 = "";
if (isset($_POST['acp_pass_3'])) $acp_pass_3 = trim($_POST['acp_pass_3']); else $acp_pass_3 = "";
$acp_login = $_SESSION['acp_login'];
//
require ("../common/sql.inc.php");
require ("../common/acp_auth.inc.php");
//
$id_admin = intval(f_acp_check_login($acp_login, $acp_pass_1));
if ( ($id_admin > 0) and ($acp_pass_2 == $acp_pass_3) and (strlen($acp_pass_2) > 5) )
{
  $adm_salt = random(20);
  $pass_cr = chiffrer_pass($acp_pass_2, $adm_salt);
  //
  $requete  = " UPDATE " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
  $requete .= " SET ADM_PASSWORD = '" . $pass_cr . "', ";
  $requete .= " ADM_SALT = '" . $adm_salt . "', ";
  $requete .= " ADM_DATE_PASSWORD = CURDATE() ";
  $requete .= " WHERE ID_ADMIN = " . $id_admin . " ";
  $requete .= " limit 1 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-P1g]", $requete);
  //
  header("location:index.php");
}
else
{
  echo "<html><head>";
  echo "<title>[IM] " . $l_admin_acp_auth_title . "</title>";
  echo '<META http-equiv="refresh" content="2; url=acp_pass_updating.php"> ';
  echo "</head>";
  echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
  //    
  echo "<TABLE WIDTH='100%' HEIGHT='100%'>";
  echo "<TR>";
  echo "<TD ALIGN='CENTER'>";
  echo $l_admin_acp_auth_error;
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE>";
  sleep(1);
} 
?>
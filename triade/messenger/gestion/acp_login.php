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
//error_reporting(E_ALL);
//
define('INTRAMESSENGER',true);
//
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
//require ("../common/functions.inc.php"); // NON car dans menu !
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//
//
//
session_start();
##session_destroy();
//
if (!isset($_POST['acp_login']))
{
  header("location:acp_connect.php");
  die();
}
else
{
  if (isset($_POST['acp_pass'])) $acp_pass = $_POST['acp_pass']; else $acp_pass = "";
  if (isset($_POST['acp_pass_mem'])) $acp_pass_mem = $_POST['acp_pass_mem']; else $acp_pass_mem = "";
  $acp_login = $_POST['acp_login'];
  //echo ">> " . $acp_login . " - " . $acp_pass . " <br/>";
  //
  require ("../common/sql.inc.php");
  require ("../common/acp_auth.inc.php");
  //if (f_acp_check_login($acp_login, $acp_pass) == "OK")
  $id_admin = intval(f_acp_check_login($acp_login, $acp_pass));
  if ($id_admin > 0)
  {
    $requete  = " select ADM_LEVEL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
    $requete .= " WHERE ID_ADMIN = " . $id_admin . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-P1f]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($admn_level) = mysqli_fetch_row ($result);
      $admn_level = intval($admn_level);
      if ($admn_level > 0)
      {
        ##session_start();
        if (!isset($_SESSION['acp_init']))
        {
          //session_regenerate_id()
          $_SESSION['acp_init'] = time();
        }
        $_SESSION['acp_login'] = $acp_login;
        $_SESSION['acp_level'] = $admn_level;
        //
        if ( (_ACP_ALLOW_MEMORY_AUTH != "") and ($acp_pass_mem == '1') )
        {
          setcookie("im_acp_login", $acp_login, mktime(0,0,0,12,31,2014));
          setcookie("im_acp_pass", $acp_pass, mktime(0,0,0,12,31,2014));
        }
        //
        write_log("log_acp_admin_connect", $acp_login . ";" . $admn_level . ";");
        //
        header("location:index.php");
      }
    }
  }
  else
  {
    unset($_SESSION['acp_login']);
    unset($_SESSION['acp_level']);
    session_destroy();
    //
    if ($id_admin == -1) 
      write_log("log_acp_password_errors", $acp_login . ";");
    else
      write_log("log_acp_login_errors", $acp_login . ";");
    //
    sleep(1);
    //
    //require ("../common/menu.inc.php"); // après config.inc.php !
    echo "<html><head>";
    echo "<title>[IM] " . $l_admin_acp_auth_title . "</title>";
    //display_header();
    echo '<META http-equiv="refresh" content="2; url=acp_login.php"> ';
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
}
?>
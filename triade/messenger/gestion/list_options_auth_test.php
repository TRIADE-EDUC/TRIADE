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
//
require ("../common/display_errors.inc.php"); 
//
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
require ("../common/menu.inc.php"); // après config.inc.php ! et après check option manquantes !!!
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_info_10 . "</title>";
display_header();
echo '<META http-equiv="refresh" content="30;url="> ';
echo "</head>";
echo "<body>";
display_menu();
//
echo "<BR/>";
if (f_nb_auth_extern() == 1)
{
  $external_authentication_name = "";
  $external_authentication = _EXTERNAL_AUTHENTICATION;
  if ($external_authentication != "") 
  {
    $external_authentication_name = f_type_auth_extern();
    if ($external_authentication_name == "") $external_authentication = "";
  }
  if ($external_authentication_name != "")
  {
    echo "<U>Test - " . $l_admin_options_info_10 . "</U> : <BR/><BR/>" . $l_admin_authentication_extern . " : <B>" . $external_authentication_name . "</B> <BR/>";
    echo "<BR/>";
    require ("../common/sql.inc.php"); // on connect avant, vu qu'il y aura déconnexion...
    //
    require("../common/config/extern.config.inc.php");
    if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
    {
      echo "Check file : <B><U>extern.config.inc.php</U></B> : ";
      mysqli_close($id_connect);
      #require("../common/config/extern.sql.inc.php");
      $id_connect_extern = mysqli_connect($extern_dbhost, $extern_dbuname, $extern_dbpass) or die("<font color='red'>" . $l_admin_check_conf_not_ok . "</FONT> <BR/><BR/><SMALL>Unable to connect to MySQL server: " . mysqli_error($id_connect_extern) . "<BR/> ");
      mysqli_select_db($id_connect_extern, $extern_database) or die("<font color='red'>" . $l_admin_check_conf_not_ok . "</FONT><BR/>" . $l_admin_check_connect_to_server . ": OK<BR/>" . $l_admin_check_connect_to_database . ": Ko !   <BR/><BR/><SMALL>Unable to select database : " . mysqli_error($id_connect_extern) . "<BR/> ");
      //
      echo "OK.<BR/>";
      echo "<BR/>";
      echo $l_admin_check_connect_to_database . " : OK<BR/>";
    }
    echo "<BR/>";
    echo "Try to verify :<BR/>";

    $ret = f_check_if_auth_exten_ok("Teesss.stttttt", "Teesssstttttt..........", "test !"); // le point dans le login est important !!!!  (ex: pour Triade)
    //if ($ret == "KO-AUTH-EXT") echo "Ok"; // non justement, d'où l'intérêt du 3ème paramètre pour appeler  f_check_if_auth_exten_ok
    if ( ($ret == "OK") or ($ret == "Ko") ) 
    {
       echo " <font color='green'><B>Test is OK</B></font> ";
       echo "<IMG SRC='" . _FOLDER_IMAGES . "ok.gif' ALT='' TITLE='' WIDTH='16' HEIGHT='16' BORDER='0'>";
    }
    echo "<BR/>";
    //echo ">" . $ret . "<";
  }
  else
  {
    if ($lang == 'FR')
      echo $l_admin_authentication_extern . " : " . $external_authentication . " : ne peut pas fonctionner.";
    else
      echo $l_admin_authentication_extern . ": " . $external_authentication . ": cannot work.";
  }
}
else
{
  if ($lang == 'FR')
    echo "Pas d'" . $l_admin_options_info_10 . " sélectionnée, donc rien à tester !";
  else
    echo "No " . $l_admin_options_info_10 . " selected, nothing to test !";
}
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<FORM METHOD='POST' ACTION='list_options_auth_updating.php?'>";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "<INPUT TYPE='submit' VALUE = '" . $l_configure . "' class='liteoption' />";
echo "</FORM>";
//
display_menu_footer();
//
echo "</body></html>";
?>
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
require ("../common/display_errors.inc.php"); 
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . "im_setup.reg" . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="10;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
echo "<BR/>";
        
$repertoire  = getcwd() . "/"; 
$demo_folder = "";
if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) $demo_folder = "X";
//
if ( (!is_readable("../im_setup.reg")) and ($demo_folder == "") )
{
  //if ($demo_folder == "") $l_menu_need_reg = str_replace ("im_setup.reg", "<a href='../reg_create.php?lang=" . $lang . "&action=create&' title='im_setup.reg'>im_setup.reg</A>", $l_menu_need_reg);
  echo "<FONT COLOR='BLUE'><B>" . $l_menu_need_reg . "</B></font><BR/>";
  echo "<BR/>";
  echo "<FORM METHOD='POST' name='formulaire' ACTION ='reg_create.php?'>";
  echo $l_language;
  echo " <select name='reg_lang'> ";
    echo "<option value='' class='genmed'> </option>";
    echo "<option value='EN' class='genmed' ";
    if (_LANG == "EN") echo "SELECTED";
    echo ">EN</option>" ;
    //
    echo "<option value='FR' class='genmed' ";
    if (_LANG == "FR") echo "SELECTED";
    echo ">FR</option>" ;
    //
    echo "<option value='IT' class='genmed' ";
    if (_LANG == "IT") echo "SELECTED";
    echo ">IT</option>" ;
    //
    echo "<option value='BR' class='genmed' ";
    if (_LANG == "BR") echo "SELECTED";
    echo ">BR</option>" ;
    //
    echo "<option value='PT' class='genmed' ";
    if (_LANG == "PT") echo "SELECTED";
    echo ">PT</option>" ;
    //
    echo "<option value='ES' class='genmed' ";
    if (_LANG == "ES") echo "SELECTED";
    echo ">ES</option>" ;
    //
    echo "<option value='RO' class='genmed' ";
    if (_LANG == "RO") echo "SELECTED";
    echo ">RO</option>" ;
    //
    echo "<option value='BA' class='genmed' ";
    if (_LANG == "BA") echo "SELECTED";
    echo ">BA</option>" ;
    //
    echo "<option value='DE' class='genmed' ";
    if (_LANG == "DE") echo "SELECTED";
    echo ">DE</option>" ;
    //
    echo "<option value='NL' class='genmed' ";
    if (_LANG == "NL") echo "SELECTED";
    echo ">NL</option>" ;
    //
    echo "<option value='RS' class='genmed' ";
    if (_LANG == "RS") echo "SELECTED";
    echo ">RS</option>" ;
    //
    echo "<option value='SE' class='genmed' ";
    if (_LANG == "SE") echo "SELECTED";
    echo ">SE</option>" ;
    //
    echo "<option value='TR' class='genmed' ";
    if (_LANG == "TR") echo "SELECTED";
    echo ">TR</option>" ;
    //
    echo "<option value='ID' class='genmed' ";
    if (_LANG == "ID") echo "SELECTED";
    echo ">ID</option>" ;
    //
  echo " </select> ";
  echo "<BR/>";
  echo "<BR/>";

  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_create . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='hidden' name='action' value = 'create' />";
  echo "</FORM>";
}


echo "</CENTER>";

$url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
//$pos = strrpos(substr($url,0, strlen($url)-1) , "/", 2);
#$pos = strrpos($url, "/", 1);
$pos = strrpos($url, "/");
if ($pos > 0) $url = substr($url, 0, $pos + 1);

echo "<U>File full name</U> :<BR/>";
echo $url . "im_setup.reg <BR/>";
echo "<BR/>";
echo "<U>File will content</U> :<BR/>";
echo "<BR/>";

echo "Windows Registry Editor Version 5.00<BR/>";
echo "[HKEY_CURRENT_USER\Software\THe UDS\IM]<BR/>";
echo '"url"="' . $url . '" <BR/>';
echo '"lang"="" <BR/>';

  //
display_menu_footer();
//
echo "</body></html>";
?>

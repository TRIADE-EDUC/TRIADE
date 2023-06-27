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
//if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_COOKIE['im_background_color'])) $back_color = $_COOKIE['im_background_color']; else $back_color = "";
if (isset($_COOKIE['im_charset'])) $option_charset = $_COOKIE['im_charset']; else $option_charset = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_display_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
//echo "<B>" . $l_admin_display . "</B><BR/>";
//
//
echo '<noscript>';
echo "<div class='notice' align='center'><B>";
echo "<I>" . $l_menu_top . "</I> : <font color='red'>need javascript !</font>";
echo '</div>';
echo '</noscript>';

//
echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
echo "<TR>";
echo "<TH align='center' class='thHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_display_options . "&nbsp;</b></font>";
echo "</TH>";
echo "</TR>";
//
echo "<TR>";
echo "<TD align='center' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_display_menu . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";
//
echo "<FORM METHOD='POST' ACTION='display_update.php?'>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>";
echo "&nbsp;<INPUT name='position_menu' id='id_1' TYPE='radio' VALUE='T' class='genmed' ";
if (_MENU_ON == "TOP") echo "checked";
echo ">";
echo "<label for='id_1'>" . $l_menu_top . "</label>&nbsp;<BR/>";
//
echo "&nbsp;<INPUT name='position_menu' id='id_2' TYPE='radio' VALUE='L' class='genmed' ";
if (_MENU_ON == "LEFT") echo "checked";
echo ">";
echo "<label for='id_2'>" . $l_menu_left . "</label>&nbsp;<BR/>";
//
echo "&nbsp;<INPUT name='position_menu' id='id_3' TYPE='radio' VALUE='R' class='genmed' ";
if (_MENU_ON == "RIGHT") echo "checked";
echo ">";
echo "<label for='id_3'>" . $l_menu_right . "</label>&nbsp;<BR/>";
echo "</TD></TR>";
//
//
echo "<TR>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>";
echo "&nbsp;<INPUT name='full_menu' id='id_4' TYPE='radio' VALUE='1' class='genmed' ";
if ($full_menu != "") echo "checked";
echo ">";
echo "<label for='id_4'>" . $l_menu_full . "</label>&nbsp;<BR/>";
//
echo "&nbsp;<INPUT name='full_menu' id='id_5' TYPE='radio' VALUE='2' class='genmed' ";
if ($full_menu == "") echo "checked";
echo ">";
echo "<label for='id_5'>" . $l_menu_not_full . "</label>&nbsp;<BR/>";
//
echo "</TD></TR>";
//
//
//
echo "<TR>";
echo "<TD align='center' class='row3'>&nbsp;";
echo "</TD>";
echo "</TR>";
//
//
//
echo "<TR>";
echo "<TD align='center' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_display_style . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";
//
echo "<TR>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>";
echo "&nbsp;" . $l_admin_display_style_select . ": ";
echo " <select name='style'> ";
  foreach ($style_css_list as $nom_style) 
  {
    echo "<option value='" . $nom_style . "' class='genmed' ";
    if ($file_style_css == $nom_style) echo "SELECTED";
    echo ">" . $nom_style . "</option>" ;
  }
echo " </select>&nbsp;";
echo "</TD>";
//
//
//
echo "<TR>";
echo "<TD align='center' class='row3'>&nbsp;";
echo "</TD>";
echo "</TR>";
//
//
//

echo "<TR>";
echo "<TD align='center' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_display_background_color . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";
//
echo "<TR>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>";
echo "&nbsp;" . $l_admin_display_color_select . ": ";
echo " <select name='back_color'> ";

echo "<option value='blue' class='genmed' ";
if ($back_color == "blue") echo "SELECTED";
echo ">" . $l_color_blue . "</option>" ;
echo "<option value='green' class='genmed' ";
if ($back_color == "green") echo "SELECTED";
echo ">" . $l_color_green . "</option>" ;
echo "<option value='pink' class='genmed' ";
if ($back_color == "pink") echo "SELECTED";
echo ">" . $l_color_pink . "</option>" ;
echo "<option value='red' class='genmed' ";
if ($back_color == "red") echo "SELECTED";
echo ">" . $l_color_red . "</option>" ;
echo "<option value='yellow' class='genmed' ";
if ($back_color == "yellow") echo "SELECTED";
echo ">" . $l_color_yellow . "</option>" ;

echo " </select>&nbsp;";
echo "</TD>";

echo "<TR>";
echo "<TD align='center' class='row3'>&nbsp;";
echo "</TD>";
echo "</TR>";
//
//
//


echo "<TR>";
echo "<TD align='center' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_display_character_sets . "&nbsp;</b></font>";
//echo "<IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' BORDER='0' TITLE='" . $l_admin_options_new ."' />";
echo "";
echo "</TD>";
echo "</TR>";
echo "<TR>";
//
echo "<TR>";
echo "<TD class='row1' align='left'>";
echo "<font face=verdana size=2>";
echo "&nbsp;" . $l_admin_display_charset . ": ";
echo " <select name='option_charset'> ";

echo "<option value='' class='genmed' ";
if ($option_charset == "") echo "SELECTED";
echo ">" . $l_admin_display_default_charset . "</option>" ; 

echo "<option value='iso-8859-1' class='genmed' ";
if ($option_charset == "iso-8859-1") echo "SELECTED";
echo ">Western Alphabet (iso-8859-1)</option>" ;

echo "<option value='iso-8859-2' class='genmed' ";
if ($option_charset == "iso-8859-2") echo "SELECTED";
echo ">Central European Alphabet (iso-8859-2)</option>" ;

echo "<option value='iso-8859-3' class='genmed' ";
if ($option_charset == "iso-8859-3") echo "SELECTED";
echo ">Latin 3 Alphabet (iso-8859-3)</option>" ;

echo "<option value='windows-1256' class='genmed' ";
if ($option_charset == "windows-1256") echo "SELECTED";
echo ">Arabic (windows-1256)</option>" ; 

echo "<option value='iso-8859-6' class='genmed' ";
if ($option_charset == "iso-8859-6") echo "SELECTED";
echo ">Arabic Alphabet (iso-8859-6)</option>" ;

echo "<option value='windows-1257' class='genmed' ";
if ($option_charset == "windows-1257") echo "SELECTED";
echo ">Baltic (windows-1257)</option>" ; 

echo "<option value='iso-8859-4' class='genmed' ";
if ($option_charset == "iso-8859-4") echo "SELECTED";
echo ">Baltic Alphabet (iso-8859-4)</option>" ;

echo "<option value='big5' class='genmed' ";
if ($option_charset == "big5") echo "SELECTED";
echo ">Chinese Traditional (Big5)</option>" ;

echo "<option value='windows-1251' class='genmed' ";
if ($option_charset == "windows-1251") echo "SELECTED";
echo ">Cyrillic (windows-1251)</option>" ; 

echo "<option value='iso-8859-5' class='genmed' ";
if ($option_charset == "iso-8859-5") echo "SELECTED";
echo ">Cyrillic Alphabet (iso-8859-5)</option>" ;

echo "<option value='koi8-r' class='genmed' ";
if ($option_charset == "koi8-r") echo "SELECTED";
echo ">Cyrillic Alphabet (KOI8-R)</option>" ;

echo "<option value='windows-1253' class='genmed' ";
if ($option_charset == "windows-1253") echo "SELECTED";
echo ">Greek (windows-1253)</option>" ; 

echo "<option value='iso-8859-7' class='genmed' ";
if ($option_charset == "iso-8859-7") echo "SELECTED";
echo ">Greek Alphabet (iso-8859-7)</option>" ;

echo "<option value='windows-1255' class='genmed' ";
if ($option_charset == "windows-1255") echo "SELECTED";
echo ">Hebrew (windows-1255)</option>" ; 

echo "<option value='iso-8859-8' class='genmed' ";
if ($option_charset == "iso-8859-8") echo "SELECTED";
echo ">Hebrew Alphabet (iso-8859-8)</option>" ;

echo "<option value='shift-jis' class='genmed' ";
if ($option_charset == "shift-jis") echo "SELECTED";
echo ">Japanese (Shift-JIS)</option>" ;

echo "<option value='x-euc' class='genmed' ";
if ($option_charset == "x-euc") echo "SELECTED";
echo ">Japanese (x-EUC)</option>" ;

echo "<option value='euc-kr' class='genmed' ";
if ($option_charset == "euc-kr") echo "SELECTED";
echo ">Korean (EUC-kr)</option>" ;

echo "<option value='windows-874' class='genmed' ";
if ($option_charset == "windows-874") echo "SELECTED";
echo ">Thai (Windows-874)</option>" ; 

echo "<option value='windows-1254' class='genmed' ";
if ($option_charset == "windows-1254") echo "SELECTED";
echo ">Turkish (windows-1254)</option>" ; 

echo "<option value='windows-1258' class='genmed' ";
if ($option_charset == "windows-1258") echo "SELECTED";
echo ">Vietnamese (windows-1258)</option>" ; 

echo "<option value='utf-8' class='genmed' ";
if ($option_charset == "utf-8") echo "SELECTED";
echo ">Universal Alphabet (UTF-8)</option>" ;

echo "<option value='windows-1252' class='genmed' ";
if ($option_charset == "windows-1252") echo "SELECTED";
echo ">Western (windows-1252)</option>" ; 

echo "<option value='windows-1250' class='genmed' ";
if ($option_charset == "windows-1250") echo "SELECTED";
echo ">Central European (windows-1250)</option>" ; 

/*
echo "<option value='XXXXXXXXX' class='genmed' ";
if ($option_charset == "XXXXXXXXX") echo "SELECTED";
echo ">ZZZZZZZ (XXXXXXXXXXXX)</option>" ; 
*/

echo " </select>&nbsp;";
echo "</TD>";

//
//
//
echo "<TR>";
echo "<TD align='center' class='row3'>&nbsp;";
echo "</TD>";
echo "</TR>";
//
//
//
echo "<TR>";
echo "<TD align='center' class='catBottom'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='mainoption' />"; // liteoption
//echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "</TR>";
echo "</FORM>";
//
echo "</TABLE>";	//
//
display_menu_footer();
//
echo "</body></html>";
?>
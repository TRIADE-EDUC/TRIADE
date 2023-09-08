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
if (isset($_GET['id_user_select'])) $id_user_select = intval($_GET['id_user_select']);  else  $id_user_select = 0;
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_admin_messages);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_mess_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="40;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<br/>";
echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
echo "<FORM METHOD='GET' name='formulaire' ACTION ='messagerie.php?'>";
echo "<TR>";
echo "<TH colspan='2' class='thHead'>";
echo "<FONT size='3'>";
echo $l_admin_mess_title_4;
echo "</TH>";
echo "</TR>\n";
//
$rep = opendir('../distant/send/');
while ($file = readdir($rep))
{
	if($file != ".." && $file != "." && $file !="" ) // .inc.php && strpos(strtolower($file), ".*") 
	{
		$ext = strtolower(substr($file,-5));
		if ( (!is_dir($file)) and ( (strpos($ext, ".gif")) or (strpos($ext, ".jpg")) or (strpos($ext, ".jpeg")) or (strpos($ext, ".png")) ) )
		{
			echo "<TR>";
			echo "<TD VALIGN='MIDDLE' class='row1'>"; 
			echo "<INPUT name='nm_image' TYPE='radio' VALUE='" . $file . "' class='genmed' />";
			echo "<FONT size='2'>" . $file;
			echo "</TD>";
			echo "<TD class='row2'>";
			echo "<IMG SRC='../distant/send/" . $file . "' alt='" . $file . "' title='" . $file . "' >";
			echo "</select>";
			echo "</TD>";
			echo "</TR>\n";
			//echo "<BR/>";
		}
	}
}
closedir($rep);
//
echo "<TR>";
echo "<TD align='center' COLSPAN='2' class='row3'>";
echo "<font face=verdana size=2>";
echo $l_admin_mess_dir . " : <I>/distant/send/</I>";
echo "</TD>";
echo "</TR>";

echo "<TR>";
echo "<TD colspan='2' ALIGN='CENTER' class='catBottom'>";
echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_mess_select . "' />";
echo "<INPUT TYPE='hidden' name='id_user_select' value = '" . $id_user_select . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "</TR>";


echo "</FORM>";
echo "</TABLE>";
//
echo "<div class='notice'>";
echo $l_admin_mess_image_only;
echo "</div>";
//
display_menu_footer();
//
echo "</body></html>";
?>
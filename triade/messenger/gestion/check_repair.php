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
require ("../common/sql.inc.php");
require ("lang.inc.php");
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
//

function table_title($title)
{
	echo "<SMALL><BR/></SMALL>";
	echo "<table width='670' cellspacing='1' cellpadding='1' class='forumline'>";
	echo "<TR>";
	echo "<TH colspan='2' class='thHead'>";
	echo "<FONT size='3'>";
	echo $title;
	echo "</TH>";
	echo "</TR>";
}

function table_col_1($text)
{
	echo "<TR>";
	echo "<TD width='' class='row2'>";
	echo "<FONT size='2'>";
	echo $text;
	echo "</TD>";
}
//
function table_col_2($etat)
{
	echo "<TD width='20' class='row1' ALIGN='CENTER'>";
	if ($etat == 'OK')
		echo "<IMG SRC='" . _FOLDER_IMAGES . "ok.gif' WIDTH='16' HEIGHT='17' ALT='OK' TITLE='OK'>";
	else
		echo "<IMG SRC='" . _FOLDER_IMAGES . "ko.gif' WIDTH='17' HEIGHT='17' ALT='Not OK !' TITLE='Not OK !'>";
	//
	echo "</TD>";
	echo "</TR>";
}
//
function table_col_vide()
{
	echo "<TD width='20' class='row1' ALIGN='CENTER'>";
  echo "&nbsp;";
	echo "</TD>";
	echo "</TR>";
}
//

//
//
//
//
echo "<title>[IntraMessenger] " . $l_admin_check_title . "</title>";
require ("../common/menu.inc.php"); // après config.inc.php !  et APRES les test d'existantes des constantes !!!!
display_header();
echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//
//
//
//
//
//
$c_OK = "<B><FONT COLOR='GREEN'>OK</B></FONT>";
$c_not_found = "<B><FONT COLOR='RED'><BLINK>" . $l_admin_check_not_found . "</BLINK></FONT></B>";
$c_found = "<B><FONT COLOR='GREEN'>" . $l_admin_check_found . "</FONT></B>";
$c_on_ok = "<B><FONT COLOR='GREEN'>" . $l_admin_check_on . "</FONT></B>";
$c_on_ko = "<B><FONT COLOR='RED'>" . $l_admin_check_on . "</FONT></B>";
$c_off_ko = "<B><FONT COLOR='RED'>" . $l_admin_check_off . "</FONT></B>";
$c_off_ok = "<B><FONT COLOR='GREEN'>" . $l_admin_check_off . "</FONT></B>";
$if_prob = "OK";
//

//if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
if ($lang != "EN") echo " <A href='?lang=EN&'><IMG SRC='../images/flags/gb.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> <A href='?lang=EN&'>English</A> &nbsp;";
if ($lang != "FR") echo " <A href='?lang=FR&'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> <A href='?lang=FR&'>En français</A> &nbsp;";
if ($lang != "IT") echo " <A href='?lang=IT&'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> <A href='?lang=IT&'>Italiano</A> &nbsp;";

//echo "<BR/>";
echo "<CENTER>";

//
table_title($l_admin_check_optimize_tables);
//
//
$txt = "";
$if_prob_optimize = "OK";
$arrTableInit = array('#CNT_CONTACT#','#MSG_MESSAGE#','#SES_SESSION#','#USR_USER#', '#USG_USERGRP#', '#GRP_GROUP#', '#STA_STATS#', '#CNF_CONFERENCE#', 
                      '#USC_USERCONF#', '#BAN_BANNED#', '#SRV_SERVERSTATE#', '#SBX_SHOUTBOX#', '#SBS_SHOUTSTATS#', '#SBV_SHOUTVOTE#', 
                      '#BMC_BOOKMCATEG#', '#BMK_BOOKMARK#', '#BMV_BOOKMVOTE#', '#ROL_ROLE#', '#MDL_MODULE#', '#RLM_ROLEMODULE#',
                      '#FMD_FILEMEDIA#', '#FPJ_FILEPROJET#', '#FIL_FILE#', '#FLV_FILEVOTE#', '#FST_FILESTATS#', '#FSD_FILESTATSDOWNLOAD#',
                      '#ADM_ADMINACP', '#FIB_FILEBACKUP#');  
                      //   , '##', '##'
                      //   , '##', '##'
//
foreach($arrTableInit as $table) 
{
  $table_aff = str_replace("#", "", $table); // enlever les #
  $txt .= "Table <I>" . $table_aff . "</I> : ";
  //
  $requete = "REPAIR TABLE " . $PREFIX_IM_TABLE . $table_aff;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) 
    //$txt .= mysql_error() . '<BR/>' . '<span class="error">cannot repair table ' . $PREFIX_IM_TABLE . $table_aff . '</span><BR/>';
    $txt .= '<span class="error">cannot repair table</span><BR/>';
  else
    $txt .= $c_OK;
  //
  $txt .= '<BR>';

  //
}
//
mysqli_close($id_connect);
//
//
table_col_1($txt);
//table_col_2($if_prob_optimize);
table_col_vide();
//
//
echo "</TABLE>";
//
echo "</body></html>";
?>
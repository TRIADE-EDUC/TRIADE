<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2013 THeUDS           **
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
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("common/styles/style.css.inc.php"); 
require ("common/config/config.inc.php");
require ("common/functions.inc.php");
require ("lang.inc.php");
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo '<META NAME="ROBOTS" CONTENT="NOARCHIVE">';
echo '<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">';
echo '<META NAME="Author" CONTENT="THeUDS.com">';
echo '<META NAME="copyright" content="THeUDS.com">';
echo '<meta http-equiv="Content-Type" content=text/html;charset=iso-8859-1 />';
echo '<meta http-equiv="Content-Style-Type" content="text/css">';
echo "<link href='common/styles/subSilverPlus.css' rel='stylesheet' media='screen' type='text/css'/>";
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_info_book . "</title>";
echo "</head>";
$t = _FOLDER_IMAGES;
$t = substr($t, 3, strlen($t) - 3);
echo "<body background='" . $t . "blue/". "background.jpg'>";
//
echo "<font face='verdana' size='2'>";
echo "<BR/>";
//
$url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
if (substr($url, strlen($url)-1, 1) != "/") $url .= "/";
$url = str_replace("'", "", $url);
$url = str_replace('"', "", $url);
$server = trim($_SERVER['SERVER_NAME']);
$bad_config_to_public_book = "";
if (_MAINTENANCE_MODE != "") $bad_config_to_public_book = "X";
if (_ENTERPRISE_SERVER != "") $bad_config_to_public_book = "X";
if (_PASSWORD_FOR_PRIVATE_SERVER != "") $bad_config_to_public_book = "X";
if (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") $bad_config_to_public_book = "X";
if (_PENDING_USER_ON_COMPUTER_CHANGE != "") $bad_config_to_public_book = "X";
if (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != "") $bad_config_to_public_book = "X";
if (_FORCE_USERNAME_TO_PC_SESSION_NAME != "") $bad_config_to_public_book = "X";
if (_FORCE_UPDATE_BY_SERVER != "") $bad_config_to_public_book = "X";
if (_FORCE_UPDATE_BY_INTERNET == "") $bad_config_to_public_book = "X";
if (_USER_NEED_PASSWORD == "") $bad_config_to_public_book = "X";
if (_HISTORY_MESSAGES_ON_ACP != "") $bad_config_to_public_book = "X";
if (_IM_ADDRESS_BOOK_PASSWORD == "") $bad_config_to_public_book = "X";
if ( (intval(_MAX_NB_USER) < 100) and (intval(_MAX_NB_USER) >0) ) $bad_config_to_public_book = "X";
if ( (intval(_MAX_NB_SESSION) < 50) and (intval(_MAX_NB_SESSION) > 0) ) $bad_config_to_public_book = "X";
if ($bad_config_to_public_book == "")
{
  if (strlen($url) > 100) $bad_config_to_public_book = "X";
  if ( ($server == "127.0.0.1") or ($server == "localhost") ) $bad_config_to_public_book = "X";
  if (substr_count($server, ".") == 3)
  {
    $server = str_replace(".", "", $server);
    if ($server == intval($server)) $bad_config_to_public_book = "X";
  }
}
//
if ($bad_config_to_public_book != "") 
{
  echo "<font color='red'>" . $l_admin_options_info_8 . "</font> : <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A><BR/>";
  echo "<BR/>";
  echo "See : <BR/>" . $url . "<I>admin</I>/register_to_public_servers_list.php<BR/>";
}
else
{
  echo "<CENTER>";
	echo "<BR/>";
	echo "<BR/>";
	echo "<BR/>";
	echo "<BR/>";
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
	echo "<TR>";
	echo "<TD align='center' COLSPAN='2' class='catHead'>";
	echo "<font face='verdana' size=3><b>Register your server to official public list</b></font>";
	echo "</TD>";
	echo "</TR>";
	echo "<TR>";
  echo "<FORM METHOD='POST' ACTION='http://www.intramessenger.net/list/servers/request_server_add.php?'>";
  echo "<TD class='row1'>";
  echo "<font face='verdana' size='2'>";
  echo "&nbsp;IntraMessenger URL :&nbsp;</TD>";
  echo "<TD class='row1'>";
  echo "<font face='verdana' size='2'>" . $url;
  echo "</TD></TR>";
  
  echo "<TR><TD class='row2' COLSPAN=2></TD></TR>";
  
  echo "<TR><TD class='row1'><font face='verdana' size='2'>";
  echo "&nbsp;Title (site name) : </TD>";
  echo "<TD class='row1'><input type='text' name='title' maxlength='80' size='60' class='post'>";
  echo "</TD></TR>";

  echo "<TR><TD class='row2' COLSPAN=2></TD></TR>";

  echo "<TR><TD class='row1'><font face='verdana' size='2'>";
  echo "&nbsp;Website address : </TD>";
  echo "<TD class='row1'><input type='text' name='website' maxlength='80' size='60' class='post'>";
  echo "</TD></TR>";

  echo "<TR><TD class='row2' COLSPAN=2></TD></TR>";

  echo "<TR><TD class='row1'><font face='verdana' size='2'>";
  echo "&nbsp;Email : </TD>";
  echo "<TD class='row1'><input type='text' name='email' maxlength='70' size='50' class='post'>";
  echo "</TD></TR>";


  echo "<TR><TD class='row2' COLSPAN=2></TD></TR>";

  echo "<TR><TD COLSPAN=2 class='catBottom' ALIGN='CENTER'>";
  //echo " ";
  echo "<INPUT TYPE='submit' VALUE = 'Register this server' class='liteoption'>";
  echo "<input type='hidden' name='pass' value = '" . _IM_ADDRESS_BOOK_PASSWORD . "'>";
  echo "<input type='hidden' name='url' value = '" . $url . "'>";
  echo "<input type='hidden' name='urlc' value = '" . base64_encode($url) . "'>";
  //echo "<input type='hidden' name='country_code' value = " . $country_code . ">";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'>";
  echo "</TD></TR>";
  echo "</FORM>";
  echo "</TABLE>";
  echo "<BR>";
  echo "<font size='1'>";
  echo "Don't forget email : just to contact you (only if problem) and for you can update url/title...<BR>";
  echo "</font>";
  echo "<BR>";
  echo "<BR>";
  echo "<BR>";
  echo "<BR>";
  echo "<BR>";
  echo "<BR>";
  echo "<BR>";
  echo "<BR>";
	echo "<BR/>";
  echo "<A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A><BR/>";
  //echo "(1) Not <I>IntraMessenger</I> address<BR>";
} 
//
echo "</body></html>";
?>
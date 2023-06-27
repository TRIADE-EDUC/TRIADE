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
//check_acp_rights(_C_ACP_RIGHT_options);
require ("../common/menu.inc.php"); // après config.inc.php !
//require ("../common/check_version.inc.php");
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . "Donate" . "</title>";
display_header();
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2 color='green'>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<BIG><B>" . $l_menu_donate_info . " !</B></BIG>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";


?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8323960">
<input type="image" src="../images/donate_paypal.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<!-- <img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1"> -->
</form>

<?php
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
echo '<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.intramessenger.net%2F&amp;layout=standard&amp;show_faces=false&amp;width=500&amp;action=recommend&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:23px;" allowTransparency="true"></iframe><BR/>';
//
display_menu_footer();
//
echo "</body></html>";
?>
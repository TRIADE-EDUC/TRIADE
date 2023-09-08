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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php");
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_users_title . "</title>";
display_header();
if ( (_SHOUTBOX == "") or (_SHOUTBOX_PUBLIC == "") ) // not allowed to display...
{
  echo '<META http-equiv="refresh" content="0;url=../"> ';
  die();
}
echo '<META http-equiv="refresh" content="400;url="> ';
if ( (_SHOUTBOX != "") and (_SHOUTBOX_PUBLIC != "") ) echo '<link rel="alternate" title="test RSS" type="application/rss+xml" href="rss/shoutbox.xml">';
?>
<link rel="stylesheet" type="text/css" href="../common/styles/sticker.css">
<script type="text/javascript" src="../common/library/jquery.min.js"></script>
<script type="text/javascript" src="../common/library/jscroller-0.4.js"></script>
<?php
echo "</head>";
echo "<body>";
require ("../common/sql.inc.php");
//
//echo "<font face=verdana size=2>";
//echo "<CENTER>";
/*
  echo "<div id='box1_container' class='scroller_container_left_right'>";
  echo "<div id='box1' class='scroller_left_right'>tot oze psdifposdi poisdpofip";
  echo " tot oze psdifposdi poisdpofip ";
  echo " tot oze psdifposdi poisdpofip ";
  echo " tot oze psdifposdi poisdpofip ";
	echo "</div>";
	echo "</div>";
*/
//
$requete  = " select ID_SHOUT, SBX_DISPLAY, ID_USER_AUT, SBX_TIME, SBX_DATE, SBX_RATING, SBX_TEXT";
$requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
$requete .= " WHERE SBX_DISPLAY > 0 ";
$requete .= " and ID_GROUP_DEST = 0 ";
//$requete .= " ORDER BY SBX_DATE, SBX_TIME ";
$requete .= " ORDER BY ID_SHOUT DESC ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-V1b]", $requete);
$nb_row = mysqli_num_rows($result);
echo "<div id='box1_container' class='scroller_container_left_right'>";
echo "<div id='box1' class='scroller_left_right'>";
if ( $nb_row > 0 )
{
  while( list ($id_shout, $s_display, $id_aut, $s_time, $s_date, $rating, $txt) = mysqli_fetch_row ($result) )
	{
    $s_date = date($l_date_format_display, strtotime($s_date));
    $s_time = date($l_time_short_format_display, strtotime($s_time));
    $username = f_get_username_of_id($id_aut);
    //
    //if ( $s_date != date($l_date_format_display) )      echo "<font color='gray'>";
    //echo "&nbsp;" . $s_date . " - " . $s_time . "&nbsp;";
    if ( $s_date != date($l_date_format_display) ) echo $s_date . " - ";
    echo $s_time;
    echo "</font>";
    echo "&nbsp;<B>" . $username . "</B> : ";
    echo f_decode64_wd($txt) . "&nbsp; &nbsp; &nbsp; ";
    if (intval($rating) > 0) 
    {
      echo "<IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='16' ALT='+" . $rating . "' TITLE='+" . $rating . "'>";
      echo "<font color='green'>&nbsp;+" . $rating . "</font>";
    }
    if (intval($rating) < 0) 
    {
      echo "<IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='16' ALT='" . $rating . "' TITLE='" . $rating . "'>";
      echo "<font color='red'>&nbsp;" . $rating . "</font>";
    }
	}
}
else
  echo $l_admin_shoutbox_empty;
echo "</div>";
echo "</div>";
echo "\n";
mysqli_close($id_connect);
//
//
echo "<br/>";
if ( _SHOUTBOX_PUBLIC != '') echo "<A HREF='rss/" . "shoutbox.xml'><img src='" . _FOLDER_IMAGES . "rss.png' ALT='RSS' TITLE='RSS' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
//
//
?>
<script type="text/javascript">
 $(document).ready(function(){
   $jScroller.add("#box1_container", "#box1", "left", 2, true);
   $jScroller.start();
 });
</script>
</body>
</html>
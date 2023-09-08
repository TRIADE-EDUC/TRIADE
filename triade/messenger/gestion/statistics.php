<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2019 THeUDS           **
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
//echo "require ....";
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['group_by'])) $group_by = $_GET['group_by']; else $group_by = "";
if (isset($_GET['only'])) $only = intval($_GET['only']); else $only = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_stats_title . "</title>";
display_header();
//echo "<link href='../common/styles/graph.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
//echo "<BR/>";
echo "<TABLE border='0' width='99%'>";
echo "<TR>";
echo "<TD>";


  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
    echo "<TD align='center' COLSPAN='9' class='catBottom'>";
    echo "<font face=verdana size=2>&nbsp; ";
    if ($group_by == '') echo "<I><IMG SRC='" . _FOLDER_IMAGES . "calendar_view_day.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_day . "' TITLE='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_day . "'> ";
    echo "<A HREF='statistics.php?&lang=" . $lang . "&group_by=&only=" . $only . "&' class='cattitle' >" . $l_admin_stats_by_day . "</A>";
    if ($group_by == '') echo "</I>";
    echo " - ";
    //
    if ($group_by == 'week') echo "<I><IMG SRC='" . _FOLDER_IMAGES . "calendar_view_week.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_week . "' TITLE='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_week . "'> ";
    echo "<A HREF='statistics.php?lang=" . $lang . "&group_by=week&only=" . $only . "&' class='cattitle' >" . $l_admin_stats_by_week . "</A>";
    if ($group_by == 'week') echo "</I>";
    echo " - ";
    //
    if ($group_by == 'month') echo "<I><IMG SRC='" . _FOLDER_IMAGES . "calendar_view_month.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_month . "' TITLE='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_month . "'> ";
    echo "<A HREF='statistics.php?lang=" . $lang . "&group_by=month&only=" . $only . "&' class='cattitle' >" . $l_admin_stats_by_month . "</A>";
    if ($group_by == 'month') echo "</I>";
    echo " - ";
    //
    if ($group_by == 'year') echo "<I><IMG SRC='" . _FOLDER_IMAGES . "calendar_view_month.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_year . "' TITLE='" . $l_admin_options_legende_up2u . " : " . $l_admin_stats_by_year . "'> ";
    echo "<A HREF='statistics.php?lang=" . $lang . "&group_by=year&only=&' class='cattitle' >" . $l_admin_stats_by_year . "</A>";
    if ($group_by == 'year') echo "</I>";
    //
    echo "&nbsp; ";
    echo "</TD>";
    echo "</TR>";
  echo "</TABLE>";
  echo "\n";


echo "</TD>";
echo "<TD align='right'>";


  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
    echo "<TD align='center' COLSPAN='9' class='catBottom'>";
    echo "<font face=verdana size=2>&nbsp; ";
    echo $l_admin_stats_latest . " : ";

    if ($only == '30') 
      echo "<I><A HREF='statistics.php?&lang=" . $lang . "&group_by=&only=&' class='cattitle' >30</A> </I><IMG SRC='" . _FOLDER_IMAGES . "book_next.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . "' TITLE='" . $l_admin_options_legende_up2u . "'>";
    else
      echo "<A HREF='statistics.php?&lang=" . $lang . "&group_by=&only=30&' class='cattitle' >30</A>";
    echo " &nbsp;-&nbsp; ";
    //
    if ($only == '60') 
      echo "<I><A HREF='statistics.php?lang=" . $lang . "&group_by=" . $group_by . "&only=&' class='cattitle' >60</A></I> <IMG SRC='" . _FOLDER_IMAGES . "book_next.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . "' TITLE='" . $l_admin_options_legende_up2u . "'>";
    else
      echo "<A HREF='statistics.php?lang=" . $lang . "&group_by=" . $group_by . "&only=60&' class='cattitle' >60</A>";
    echo " &nbsp;-&nbsp; ";
    //
    if ($only == '90') 
      echo "<I><A HREF='statistics.php?lang=" . $lang . "&group_by=" . $group_by . "&only=&' class='cattitle' >90</A></I> <IMG SRC='" . _FOLDER_IMAGES . "book_next.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_legende_up2u . "' TITLE='" . $l_admin_options_legende_up2u . "'>";
    else
      echo "<A HREF='statistics.php?lang=" . $lang . "&group_by=" . $group_by . "&only=90&' class='cattitle' >90</A>";
    //
    echo " " . $l_days;
    echo "&nbsp; ";
    echo "</TD>";
    echo "</TR>";
  echo "</TABLE>";
  

echo "</TD>";
echo "</TR>";
echo "</TABLE>";
echo "<BR/>";

//echo '<div id="attente" class="section_to_show"><BR/><BR/><BR/><BR/><BR/>Please wait...</div>';


require ("statistics_graph.inc.php");
mysqli_close($id_connect);

//sleep(1);

if ($have_stats != "") 
{
  if ( ($group_by != '') and ($graph_mix != "") )
  {
    echo '<div id="graph_mix" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
    echo "<BR/>";
  }
  echo '<div id="graph_session" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>'; // >
  echo "<BR/>";
  echo '<div id="graph_user" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>'; // class="section_to_hide" // margin: 0 auto; clear:both;
  echo "<BR/>";
  echo '<div id="graph_create" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
  echo "<BR/>";
  echo '<div id="graph_message" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
  echo "<BR/>";
  if (_SHOUTBOX != "")
  {
    echo '<div id="graph_shoutbox" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
    echo "<BR/>";
  }
  if (_SHARE_FILES != "")
  {
    echo '<div id="graph_share_files_nb_share" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
    echo "<BR/>";
    if (_SHARE_FILES_EXCHANGE != "")
    {
      echo '<div id="graph_share_files_nb_exchange" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
      echo "<BR/>";
    }
    echo '<div id="graph_share_files_nb_download" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
    echo "<BR/>";
  }
  echo '<div id="graph_week" style="width: 98%; height: 300px; background-color: #eaedf4;"></div>';
  echo "<BR/>";
  //
}
else
{
  echo "<div class='info'>";
  echo $l_admin_stats_empty;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";

?>
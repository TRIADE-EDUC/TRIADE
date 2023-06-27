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
echo "<title>[IM] " . $l_time_zone . "</title>";
display_header();
echo '<META http-equiv="refresh" content="60;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
//
//
//
$requete  = " SELECT count(*) ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-K1t]", $requete);
list ($nb_users) = mysqli_fetch_row ($result);
//
//
$requete  = " SELECT distinct(USR_TIME_SHIFT), count(*) as NB";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
//$requete .= " WHERE USR_TIME_SHIFT <> '' ";
$requete .= " GROUP by USR_TIME_SHIFT ";
//$requete .= " ORDER by NB desc, USR_TIME_SHIFT ";
$requete .= " ORDER by USR_TIME_SHIFT, NB desc ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-K1s]", $requete);
if ( mysqli_num_rows($result) > 0 )
{
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='3' class='thHead'>";
    echo "<font face='verdana' size='2'><b>&nbsp;" . $l_time_zone . "&nbsp;</b></font></TH>";
  echo "</TR>";
  while( list ($timeshift, $nb) = mysqli_fetch_row ($result) )
  {
    echo "<TR>";
    echo "<TD align='left' class='row2' width='236'>";
      echo "&nbsp;<font face='verdana' size='2'>";
      if (intval($timeshift) <> 0) 
      {
        if ($timeshift < 0) 
          $t = "-"; 
        else
          $t = "+";
        $t .= intval(abs($timeshift) / 10);
        if ( (abs($timeshift / 10) - intval(abs($timeshift) / 10)) <> 0 )
          $t .= ":30";
        else
          $t .= ":00";
        echo $t;
      }
      echo "&nbsp;";
    echo "</font></TD>";
    echo "<TD class='row1' align='center' width='40'>";
      echo "&nbsp;<font face='verdana' size='2'>";
      echo $nb;
    echo "</font></TD>";
    echo "<TD class='row2' align='center' width='60'>";
      echo "&nbsp;<font face='verdana' size='2'>";
      echo round($nb / $nb_users * 100, 1);
      echo " %";
    echo "</font></TD>";
    echo "</TR>";
    echo "\n";
  }
  echo "</TABLE>";
}
else
{
  echo "<BR/>";
  echo "<div class='info'>";
  echo $l_admin_acp_admin_list_empty;
  echo "</div>";
}
//
mysqli_close($id_connect);
//
//
display_menu_footer();
//
echo "</body></html>";
?>
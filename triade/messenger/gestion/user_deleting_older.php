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
check_acp_rights(_C_ACP_RIGHT_users);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_users_out_of_date . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if (intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) > 10)
{
  require ("../common/sql.inc.php");
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " WHERE TO_DAYS(NOW()) - TO_DAYS(USR_DATE_LAST) > " . intval(_OUTOFDATE_AFTER_NOT_USE_DURATION) . " ";
  $requete .= " and USR_STATUS <> 4 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-A3c]", $requete);
  list ($nb_perim) = mysqli_fetch_row ($result);
  //
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR>";
  echo "<TD align='center' COLSPAN='3' class='catHead'>";
  echo "<font face=verdana size=3><b>" . $l_admin_users_out_of_date . "</b></font>";  // <SMALL> (non utilisés depuis plus de " . _OUTOFDATE_AFTER_NOT_USE_DURATION . " jours)</SMALL>
  echo "</TD>";

  echo "</TR>";
  echo "<TR>";
    
  if (intval($nb_perim) > 0)
  {
    echo "<TD align='right' COLSPAN='2' class='row2'>";
    echo "<font face=verdana size=2>";
    echo "<A HREF='list_users.php?tri=name&only_outofdate=x&lang=" . $lang . "&' >" . $nb_perim . " " . $l_admin_users_out_of_date . "</A> &nbsp;"; // target='_blank'
    echo "</TD>";
    echo "<FORM METHOD='GET' ACTION='user_delete_older.php?'>";
    echo "<TD class='row2'>";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "</TD></TR>";
    echo "</FORM>";
  }
  else
  {
    echo "<TD align='center' COLSPAN='2' class='row1'>";
    echo "<font face=verdana size=2 color='GREEN'>";
    echo $l_admin_users_no_out_of_date;
    echo "</FONT>";
    echo "</TD></TR>";
  }
  //
  $g_link_doc = "";
  if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
  {
    $file_doc = "../doc/fr/liste_options.html";
    if (is_readable($file_doc)) 
      $g_link_doc = $file_doc;
    else
      $g_link_doc = "http://www.intramessenger.net/doc/liste_options.html";
  }
  else
  {
    $file_doc = "../doc/en/options_list.html";
    if (is_readable($file_doc)) 
      $g_link_doc = $file_doc;
    else
      $g_link_doc = "http://www.intramessenger.net/doc/options_list.html";
  }
  //
  echo "<TR>";
  echo "<TD align='center' COLSPAN='3' class='catBottom'>";
  echo "<font face=verdana size=2>";
  //echo $l_admin_users_for_out_of_date_1 . " " . _OUTOFDATE_AFTER_NOT_USE_DURATION . " " . $l_admin_users_for_out_of_date_2 . "</font>";
  echo $l_admin_users_for_out_of_date_1 . " " . _OUTOFDATE_AFTER_NOT_USE_DURATION . " ";
  echo "<A HREF='" . $g_link_doc . "#_OUTOFDATE_AFTER_NOT_USE_DURATION' target='_blank' TITLE='_OUTOFDATE_AFTER_NOT_USE_DURATION'>";
  echo  $l_admin_users_for_out_of_date_2 . "</A></font>";
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE>";
  //
  mysqli_close($id_connect);
}
//
display_menu_footer();
//
echo "</body></html>";
?>
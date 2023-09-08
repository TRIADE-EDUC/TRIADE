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
if (isset($_GET['only_pending'])) $only_pending = $_GET['only_pending'];  else  $only_pending = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_bookmars);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_bookmarks_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _BOOKMARKS != '')
{
  require ("../common/sql.inc.php");
  //
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='7' class='thHead'>";
    echo "<font face=verdana size=3>&nbsp;<b>" . $l_admin_bookmarks_all_category . " </B>&nbsp;</font></TH>";
  echo "</TR>";
  //
  $requete  = " SELECT ID_BOOKMCATEG, BMC_TITLE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG ";
  $requete .= " ORDER BY UPPER(BMC_TITLE) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-F1g]", $requete);
  $nb_categ = mysqli_num_rows($result);
  if ( $nb_categ > 0 )
  {
    echo "<TR>";
      display_row_table("&nbsp;" . $l_admin_bookmarks_category . "&nbsp;", '');
      display_row_table($l_admin_bt_delete, '');
    echo "</TR>";
  }
  echo "</THEAD>";
  echo "<TFOOT>";
  //
  if ( $nb_categ < 11)
  {
    echo "<TR>";
      echo "<TD class='row2' COLSPAN='2'>&nbsp;";
      echo "</TD>";
    echo "</TR>";
    //
    echo "<TR>";
      echo "<TH align='center' COLSPAN='2' class='thHead'>";
      echo "<font face=verdana size=3>&nbsp;<b>" . $l_admin_bt_add . " </B>&nbsp;</font></TH>";
    echo "</TR>";
    
    echo "<TR>";
      echo "<FORM METHOD='POST' ACTION='bookmark_category_add.php?'>";
      echo "<TD class='row1' COLSPAN='2'>";
        echo "<input type='text' name='bmc_title' maxlength='60' value='' size='28' class='post' />";
        //echo "<font face=verdana size=2>";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "&nbsp;";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_create . "' class='liteoption' />";
      echo "</TD>";
      echo "</FORM>";
    echo "</TR>";

    echo "<TR>";
      echo "<TD class='row3' COLSPAN='2'>&nbsp;";
      echo "</TD>";
    echo "</TR>";
  }
  echo "<TR>";
    echo "<TD align='center' COLSPAN='2' class='catBottom'>";
      echo "<font face=verdana size=2>";
      echo "<A HREF='list_bookmarks.php?lang=" . $lang . "&'>" . $l_menu_bookmarks . "</A>";
    echo "</TD>";
  echo "</TR>";
  echo "</TFOOT>";
  echo "<TBODY>";
  if ( $nb_categ > 0 )
  {
    //
    while( list ($id_categ, $categ) = mysqli_fetch_row ($result) )
    {
      echo "<TR>";
      
      echo "<FORM METHOD='POST' ACTION='bookmark_category_update.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
      echo "<input type='text' name='bmc_title' maxlength='60' value='" . $categ . "' size='25' class='post' />";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "<input type='hidden' name='id_categ' value = '" . $id_categ . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      //
      // Col action
      /*
      echo "<TD valign='bottom' align='center' class='row2'>&nbsp;";
        echo "<A HREF='bookmark_deleting.php?id_book=" . $id_book . "&bmk_title=" . $bmk_title . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      echo "</TD>";
      */
      echo "<FORM METHOD='POST' ACTION='bookmark_category_delete.php?'>";
        echo "<TD valign='MIDDLE' align='center' class='row2'>";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
        echo "<input type='hidden' name='id_categ' value = '" . $id_categ . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
        echo "</TD>";
      echo "</FORM>";
      //
      echo "</TR>";
      echo "\n";
    }
  }
    echo "</TABLE>";
    echo "</TBODY>";
    //
  /*
  else
  {
    echo "<TR>";
    echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_bookmarks_list_empty;
    echo "</TD>";
    echo "</TR>";

    echo "<TR>";
    echo "<FORM METHOD='POST' ACTION='bookmark_category_adding.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bookmarks_creat . "' class='liteoption' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";
    echo "</TR>";
    //
    echo "</TABLE>";
  }
  */
	//
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_bookmarks_cannot;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
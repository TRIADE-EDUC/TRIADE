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
if ( (_BOOKMARKS != "") and (_BOOKMARKS_PUBLIC != "") ) echo '<link rel="alternate" title="test RSS" type="application/rss+xml" href="../' . _PUBLIC_FOLDER . '/rss/bookmarks.xml">';
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
  //
  //  ------------------------------ Bookmarks pending ------------------------------
  //
  //
  $requete  = " SELECT ID_BOOKMARK, ID_USER_AUT, BMK_URL, BMK_TITLE, BMK_DATE, BMK_RATING ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
  $requete .= " WHERE BMK_DISPLAY < 1 ";
  $requete .= " ORDER BY UPPER(BMK_TITLE) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-F1a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<BR/>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<THEAD>";
    echo "<TR>";
      echo "<TH align='center' COLSPAN='6' class='thHead'>";
      echo "<font face=verdana size=3>&nbsp;<b>" . $l_menu_bookmarks . " - " . $l_admin_users_info_wait_valid . " </B>&nbsp;</font></TH>";
    echo "</TR>";
    //
    echo "<TR>";
      display_row_table("&nbsp;" . $l_admin_bookmarks_url_title . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_bookmarks_url_address . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_conference_col_creator . "&nbsp;", ''); // $l_admin_users_col_user 
      //display_row_table($l_admin_users_col_action, '');
      //echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $l_admin_users_col_action . "</b></font> </TD>\n";
      display_row_table($l_admin_bt_delete, '');
    echo "</TR>";
    echo "</THEAD>";
    // Dernière ligne : 
    //echo "<TFOOT>";
    //echo "</TFOOT>";
    echo "<TBODY>";
    //
    $last_first_letter_group = "";
    $last_first_letter_user = "";
    $last_user = "";
    $last_group = "";
    $id_book_max = 0;
    while( list ($id_book, $id_aut, $bmk_url, $bmk_title, $bmk_date, $bmk_rating) = mysqli_fetch_row ($result) )
    {
      if ($id_book > $id_book_max) $id_book_max = $id_book;
      echo "<TR>";
      //
      // Col Title
      //
      echo "<FORM METHOD='POST' ACTION='bookmark_update.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
      echo "<input type='text' name='bmk_title' maxlength='80' value='" . f_decode64_wd($bmk_title) . "' size='30' class='post' />";
      echo " ";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      // Col URL
      //
      echo "<FORM METHOD='POST' ACTION='bookmark_update.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
      echo "<input type='url' name='bmk_url' maxlength='210' value='" . f_decode64_wd($bmk_url) . "' size='50' class='post' />";
      echo " ";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      // Col Author
      //
      echo "<TD valign='center' class='row2'>";
      echo "<font face=verdana size=2>&nbsp;";
      if ($id_aut > 0) 
      {
        //$username = f_get_username_of_id($id_aut);
        $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
        echo "<A HREF='user.php?id_user=" . $id_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
        echo $username . "</A>";
      }
      else
        echo $l_admin_users_admin;
      //
      echo "&nbsp;";
      echo "</TD>";
      //
      //
      // Col action
      /*
      echo "<TD valign='bottom' align='center' class='row2'>&nbsp;";
        echo "<A HREF='bookmark_deleting.php?id_book=" . $id_book . "&bmk_title=" . $bmk_title . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      echo "</TD>";
      */
      echo "<FORM METHOD='POST' ACTION='bookmark_deleting.php?'>";
        echo "<TD valign='MIDDLE' align='center' class='row2'>";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
        echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
        echo "</TD>";
      echo "</FORM>";
      //
      echo "</TR>";
      echo "\n";
    }
    echo "</TBODY>";
    //
    echo "<TR>";
      echo "<TD align='center' COLSPAN='4' class='catHead'>"; // catBottom
        echo "<FORM METHOD='POST' ACTION='bookmark_valid.php?'>";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bookmarks_valid_all . "' class='liteoption' />";
        echo "<input type='hidden' name='id_book_max' value = '" . $id_book_max . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
        echo "</FORM>";
      echo "</TD>";
    echo "</TR>";
    //
    echo "</TABLE>";
    echo "<BR/>";
  }
  //
  //
  // ------------------------------------------------------------------------------------------------------------------------
  //
  //
  if ($only_pending == "")
  {
    $categorys_list = "";
    // Categorys list :
    $requete  = " SELECT SQL_CACHE ID_BOOKMCATEG, BMC_TITLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG ";
    $requete .= " ORDER BY UPPER(BMC_TITLE) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F1b]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      $categorys_list = "<select name='categ'>";
      $categorys_list .= "<option value='null' class='genmed'></option>";
      while( list ($id_categ, $categ) = mysqli_fetch_row ($result) )
      {
        $categorys_list .= "<option value='" . $id_categ . "' ZZZ class='genmed'>" . $categ . "</option>";
      }
      $categorys_list .= "</select>";
    }
    //
    $requete  = " SELECT ID_BOOKMARK, ID_USER_AUT, BMK_URL, BMK_TITLE, BMK_DATE, BMK_RATING, ID_BOOKMCATEG ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE BMK_DISPLAY > 0 ";
    $requete .= " ORDER BY UPPER(BMK_DATE) DESC ";
    //$requete .= " ORDER BY UPPER(BMK_TITLE) ";
    /*
    $requete  = " SELECT BMK.ID_BOOKMARK, BMK.ID_USER_AUT, BMK.BMK_URL, BMK.BMK_TITLE, BMK.BMK_DATE, BMK.BMK_RATING, BMC.BMC_TITLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK BMK, " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG BMC";
    $requete .= " WHERE BMK.ID_BOOKMCATEG = BMC.ID_BOOKMCATEG ";
    $requete .= " AND BMK.BMK_DISPLAY > 0 ";
    $requete .= " ORDER BY UPPER(BMC_TITLE), BMK_TITLE ";
    //
    */
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-F1c]", $requete);
    //
    echo "<BR/>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<THEAD>";
    echo "<TR>";
      echo "<TH align='center' COLSPAN='7' class='thHead'>";
      echo "<font face=verdana size=3>&nbsp;<b>" . $l_admin_bookmarks_title . " </B>&nbsp;</font>";
      if ( _BOOKMARKS_PUBLIC != '') echo "<A HREF='../" . _PUBLIC_FOLDER . "/rss/bookmarks.xml'><img src='" . _FOLDER_IMAGES . "rss.png' ALT='RSS' TITLE='RSS' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";      
      echo "</TH>";
    echo "</TR>";
    //
    if ( mysqli_num_rows($result) > 0 )
    {
      echo "<TR>";
        display_row_table("&nbsp;" . $l_admin_bookmarks_category . "&nbsp;", '');
        display_row_table("&nbsp;" . $l_admin_bookmarks_url_title . "&nbsp;", '');
        display_row_table("&nbsp;" . $l_admin_bookmarks_url_address . "&nbsp;", '');
        display_row_table("&nbsp;" . $l_admin_conference_col_creator . "&nbsp;", ''); // $l_admin_users_col_user 
        display_row_table("&nbsp;" . $l_admin_stats_col_date . "&nbsp;", '');
        if (_BOOKMARKS_VOTE != "") 
        {
          display_row_table($l_admin_shoutbox_average, '');
        }
        //display_row_table($l_admin_users_col_action, '');
        //echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $l_admin_users_col_action . "</b></font> </TD>\n";
        display_row_table($l_admin_bt_delete, '');
      echo "</TR>";
      echo "</THEAD>";
      echo "<TFOOT>";
      // Dernière ligne : trier.
      echo "<TR>";
        echo "<TD align='center' class='catBottom'>";
          echo "<font face=verdana size=2>";
          echo "<A HREF='list_bookmarks_categories.php?lang=" . $lang . "&'>" . $l_admin_bt_update . "</A>";
        echo "</TD>";
        echo "<TD align='center' COLSPAN='6' class='catBottom'>";
          echo "<font face=verdana size=2>";
          echo "<A HREF='bookmark_adding.php?lang=" . $lang . "&'>" . $l_admin_bookmarks_creat . "</A>";
        echo "</TD>";
      echo "</TR>";
      echo "</TFOOT>";
      echo "<TBODY>";
      //
      $last_first_letter_group = "";
      $last_first_letter_user = "";
      $last_user = "";
      $last_group = "";
      $categorys_list_original = $categorys_list;
      while( list ($id_book, $id_aut, $bmk_url, $bmk_title, $bmk_date, $bmk_rating, $id_categ) = mysqli_fetch_row ($result) )
      {
        $bmk_date = date($l_date_format_display, strtotime($bmk_date));
        $categorys_list = $categorys_list_original;
        echo "<TR>";
        
        echo "<FORM METHOD='POST' ACTION='bookmark_update.php?'>";
        echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        if ($categorys_list <> "")
        {
          if ($id_categ > 0) $categorys_list = str_replace("'" . $id_categ . "' ZZZ", "'" . $id_categ . "' SELECTED", $categorys_list);
          echo $categorys_list;
          echo " ";
          echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
          echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        }
        echo "</TD>";
        echo "</FORM>";
        
        //
        // Col Title
        //
        echo "<FORM METHOD='POST' ACTION='bookmark_update.php?'>";
        echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<input type='text' name='bmk_title' maxlength='80' value='" . f_decode64_wd($bmk_title) . "' size='30' class='post' />";
        echo " ";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        echo "</FORM>";
        //
        // Col URL
        //
        echo "<FORM METHOD='POST' ACTION='bookmark_update.php?'>";
        echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<input type='url' name='bmk_url' maxlength='210' value='" . f_decode64_wd($bmk_url) . "' size='50' class='post' />";
        echo " ";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "</TD>";
        echo "</FORM>";
        //
        // Col Author
        //
        echo "<TD valign='center' class='row2'>";
        echo "<font face=verdana size=2>&nbsp;";
        if ($id_aut > 0) 
        {
          //$username = f_get_username_of_id($id_aut);
          $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
          echo "<A HREF='user.php?id_user=" . $id_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
          echo $username . "</A>";
        }
        else
          echo $l_admin_users_admin;
        //
        echo "&nbsp;";
        echo "</TD>";
        //
        echo "<TD valign='center' class='row2'>";
        echo "<font face=verdana size=2>&nbsp;" . $bmk_date . "&nbsp;";
        echo "</TD>";
        //
        if (_BOOKMARKS_VOTE != "") 
        {
          echo "<TD align='right' class='row2'>";
          echo "<font face=verdana size=2>";
          if ($bmk_rating > 0) echo "<font color='green'>" . $bmk_rating;
          if ($bmk_rating < 0) echo "<font color='red'>" . $bmk_rating;
          echo "&nbsp;";
          echo "</TD>";
        }
        //
        // Col action
        /*
        echo "<TD valign='bottom' align='center' class='row2'>&nbsp;";
          echo "<A HREF='bookmark_deleting.php?id_book=" . $id_book . "&bmk_title=" . $bmk_title . "&lang=" . $lang . "&' class='cattitle'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        echo "</TD>";
        */
        echo "<FORM METHOD='POST' ACTION='bookmark_deleting.php?'>";
          echo "<TD valign='MIDDLE' align='center' class='row2'>";
          echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
          //echo "<input type='hidden' name='id_grp' value = '" . $id_grp . "' />";
          echo "<input type='hidden' name='id_book' value = '" . $id_book . "' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
          echo "</TD>";
        echo "</FORM>";
        //
        echo "</TR>";
        echo "\n";
      }
      echo "</TBODY>";
      //
      echo "</TABLE>";
    }
    else
    {
      echo "<TR>";
      echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
        echo "<font face='verdana' size='2'>" . $l_admin_bookmarks_list_empty;
      echo "</TD>";
      echo "</TR>";
      echo "<TR>";

      echo "<FORM METHOD='POST' ACTION='bookmark_adding.php?'>";
      echo "<TD valign='bottom' align='center' class='row2'>";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bookmarks_creat . "' class='liteoption' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
      echo "</TD>";
      echo "</FORM>";

      echo "</TR>";
      echo "</TABLE>";
    }
  }
	//
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_bookmarks_cannot . "<BR/>";
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>
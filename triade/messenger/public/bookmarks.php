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
if (isset($_GET['only_categ'])) $only_categ = intval($_GET['only_categ']); else $only_categ = 0;
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_bookmarks_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="120;url="> ';
if ( (_BOOKMARKS != "") and (_BOOKMARKS_PUBLIC != "") ) echo '<link rel="alternate" title="test RSS" type="application/rss+xml" href="rss/bookmarks.xml">';
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//display_menu();
//
echo "<CENTER>";
echo "<font face=verdana size=2>";
if ( _BOOKMARKS != '')
{
  require ("../common/sql.inc.php");
  //
  $requete  = " SELECT BMK.ID_BOOKMARK, BMK.ID_USER_AUT, BMK.BMK_URL, BMK.BMK_TITLE, BMK.BMK_DATE, BMK.BMK_RATING, BMC.BMC_TITLE, BMK.ID_BOOKMCATEG ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK as BMK LEFT JOIN " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG as BMC ON (BMK.ID_BOOKMCATEG = BMC.ID_BOOKMCATEG) ";
  $requete .= " WHERE BMK.BMK_DISPLAY > 0 ";
  if ($only_categ > 0) $requete .= " AND BMK.ID_BOOKMCATEG =" . $only_categ;
  //$requete .= " ORDER BY UPPER(BMC_TITLE), BMK_TITLE ";
  /*
  $requete  = " SELECT ID_BOOKMARK, ID_USER_AUT, BMK_URL, BMK_TITLE, BMK_DATE, BMK_RATING ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
  $requete .= " WHERE BMK_DISPLAY > 0 ";
  */
  //$requete .= " ORDER BY UPPER(BMK_TITLE) ";
  if (_BOOKMARKS_VOTE != "")
    $requete .= " ORDER BY BMK_RATING DESC, UPPER(BMK_TITLE) ";
  else
    $requete .= " ORDER BY UPPER(BMC.BMC_TITLE), UPPER(BMK.BMK_TITLE) ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-F1a]", $requete);
  //
  echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
  if ($lang != 'FR') echo " <A HREF='?lang=FR&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'EN') echo " <A HREF='?lang=EN&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'IT') echo " <A HREF='?lang=IT&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'ES') echo " <A HREF='?lang=ES&' TITLE='Spanish'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'PT') echo " <A HREF='?lang=PT&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'BR') echo " <A HREF='?lang=BR&' TITLE='Portuguese'><IMG SRC='../images/flags/br.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'RO') echo " <A HREF='?lang=RO&' TITLE='Romana'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'DE') echo " <A HREF='?lang=DE&' TITLE='German'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  if ($lang != 'NL') echo " <A HREF='?lang=NL&' TITLE='Netherlands'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  echo "<BR/>";
  echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='6' class='thHead'>";
    echo "<font face=verdana size=3>&nbsp;<b>" . $l_admin_bookmarks_title . " </B>&nbsp;</font>";
    if ( _BOOKMARKS_PUBLIC != '') echo "<A HREF='rss/bookmarks.xml'><img src='" . _FOLDER_IMAGES . "rss.png' ALT='RSS' TITLE='RSS' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
    echo "</TH>";
  echo "</TR>";
  //
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
      display_row_table("&nbsp;" . $l_admin_bookmarks_category . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_bookmarks_url_address . "&nbsp;", '');
      if (_BOOKMARKS_VOTE != "") display_row_table($l_admin_shoutbox_average, '');
      display_row_table("&nbsp;" . $l_admin_conference_col_creator . "&nbsp;", '');  //  l_admin_users_col_user
      display_row_table("&nbsp;" . $l_admin_stats_col_date . "&nbsp;", '');
    echo "</TR>";
    echo "</THEAD>";
    // Dernière ligne : 
    if ($only_categ > 0) 
    {
      echo "<TFOOT>";
      echo "<TR>";
        echo "<TD align='center' COLSPAN='6' class='catBottom'>";
          echo "<font face=verdana size=2>";
          echo "<A HREF='?lang=" . $lang . "&'>" . $l_admin_bookmarks_all_category . "</A>";
        echo "</TD>";
      echo "</TR>";
      echo "</TFOOT>";
    }
    echo "<TBODY>";
    //
    $last_first_letter_group = "";
    $last_first_letter_user = "";
    $last_user = "";
    $last_group = "";
    while( list ($id_book, $id_aut, $bmk_url, $bmk_title, $bmk_date, $bmk_rating, $categ, $id_categ) = mysqli_fetch_row ($result) )
    {
      $bmk_date = date($l_date_format_display, strtotime($bmk_date));
      echo "<TR>";
      //
      //
      // --------------------------------------------------------------------
      //
      //
      echo "<TD class='row1'>";
      echo "<font face=verdana size=2>&nbsp;";
      if ($categ <> "") echo "<A HREF='?lang=" . $lang . "&only_categ=" . $id_categ . "&'>" . $categ . "</A>" ;
      echo "&nbsp;</TD>";
      //
      // Col URL
      //
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
      echo "<font face=verdana size=2><B>";
      echo "&nbsp;<A HREF='" . f_decode64_wd($bmk_url) . "' target='blank' title='" . f_decode64_wd($bmk_url) . "'>" .  f_decode64_wd($bmk_title) . "</A>&nbsp;";
      echo "</B></TD>";
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
      // Col Author
      //
      echo "<TD valign='center' class='row2'>";
      echo "<font face=verdana size=2>&nbsp;";
      if ($id_aut > 0) 
      {
        //$username = f_get_username_of_id($id_aut);
        $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
        echo $username;
      }
      else
        echo $l_admin_users_admin;
      //
      echo "&nbsp;";
      echo "</TD>";
      //
      // Col Date
      //
      echo "<TD valign='center' VALIGN='MIDDLE' class='row3'>";
      echo "<font face=verdana size=2>&nbsp;";
      echo $bmk_date . "&nbsp;";
      echo "</TD>";
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

    echo "</TR>";
    echo "</TABLE>";
  }
	//
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo $l_admin_bookmarks_cannot . "<BR/>";
}
//
//display_menu_footer();
//
echo "</body></html>";
?>
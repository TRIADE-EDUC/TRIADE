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
if (isset($_GET['i'])) $id = $_GET['i']; else $id = 0;
if (isset($_GET['c'])) $check = $_GET['c']; else $check = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php");
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_bookmarks_title . "</title>";
display_header();
//
$id_bookm = intval(f_decode64_wd($id));
if ( $check <> f_encode64(substr(md5(f_encode64($id_bookm)) . md5($id_bookm), 0, 10)) )  $id_bookm = 0;
//echo $check;
if ( (_BOOKMARKS == "") or (_BOOKMARKS_PUBLIC == "") or ($id_bookm <= 0) ) // not allowed to display...
{
  echo '<META http-equiv="refresh" content="0;url=../"> ';
  die();
}
echo "</head>";
//echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
echo "<body>";
require ("../common/sql.inc.php");
//
echo "<font face=verdana size=2>";
//echo "<CENTER>";
//
$requete  = " select ID_USER_AUT, BMK_DATE, BMK_RATING, BMK_TITLE, BMK_URL";
$requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
$requete .= " WHERE BMK_DISPLAY > 0 ";
//$requete .= " and ID_BOOKMCATEG = 0 ";
$requete .= " and ID_BOOKMARK = " . $id_bookm;
$requete .= " limit 1 ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-zz1p]", $requete);
$nb_row = mysqli_num_rows($result);
if ( $nb_row > 0 )
{
  while( list ($id_aut, $s_date, $rating, $bmk_title, $bmk_url) = mysqli_fetch_row ($result) )
	{
    $s_date = date($l_date_format_display, strtotime($s_date));
    if ($id_aut > 0)
    {
      //$username = f_get_username_of_id($id_aut);
      $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
    }
    else
      $username = $l_admin_users_admin;
    //
    //if ( $s_date != date($l_date_format_display) )      echo "<font color='gray'>";
    //echo "&nbsp;" . $s_date . " - " . $s_time . "&nbsp;";
    //
    echo "<font size='3'><B>";
    //echo "<A HREF='" . f_decode64_wd($bmk_url) . "' target='_blank'>" . f_decode64_wd($bmk_title)  . "</A></B> : ";
    echo f_decode64_wd($bmk_title)  . "</B> : ";
    echo "<A HREF='" . f_decode64_wd($bmk_url) . "' target='_blank'>" . f_decode64_wd($bmk_url)  . "</A>";
    echo "</font>";
    //
    echo "&nbsp; &nbsp; &nbsp; ";
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
    echo "<BR/><BR/>";
    //
    echo "<font color='gray'>";
    //if ( $s_date != date($l_date_format_display) ) echo $s_date . " - ";
    echo $s_date . " - ";
    echo "&nbsp;" . $username . " &nbsp;";
    echo "</font>";
	}
}
//else
  //echo $l_admin_bookmarks_list_empty;
echo "\n";
//
mysqli_close($id_connect);
//
echo "<br/><br/><br/>";
echo "<A HREF='bookmarks.php?lang='" . $lang . "&'>" . $l_menu_bookmarks . "</A>";
//
echo "</body></html>";
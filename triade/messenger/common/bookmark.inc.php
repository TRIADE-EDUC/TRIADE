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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
//require("lang.inc.php"); // pour format date et heure.
//
// Flux RSS :
//
function bookmarks_update_rss()
{
  GLOBAL $PREFIX_IM_TABLE, $id_connect, $l_date_format_display, $l_admin_users_admin;
  //
  if (_BOOKMARKS_PUBLIC != "")
  {
    $max_len = 30; // title max length
    $url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
    $pos = strrpos($url, "/");
    if ( $pos == (strlen($url)) ) $url = substr($url, 0, strlen($url)-1);
    //$pos = strrpos($url, "/", -2); NO ! cannot ! php bug !
    $pos = strrpos($url, "/");
    $url = substr($url, 0, ($pos +1));
    //
    //$chemin = "../" . _PUBLIC_FOLDER . "/" . "bookmarks.xml" ;
    $chemin = "../" . _PUBLIC_FOLDER . "/rss/" . "bookmarks" ;
    //if ($id_grp > 0) $chemin .= f_encode64($id_grp);
    $chemin .= ".xml";
    //
    $fp = fopen($chemin, "w");
    if (flock($fp, 2));
    {
      $requete  = " select ID_BOOKMARK, ID_USER_AUT, BMK_DATE, BMK_RATING, BMK_TITLE, BMK_URL";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
      $requete .= " WHERE BMK_DISPLAY > 0 "; // publié
      //
      $requete .= " ORDER BY ID_BOOKMARK DESC ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-131c]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        fputs($fp, "<?xml version='1.0' encoding='ISO-8859-1' ?>" ."\r\n");
        fputs($fp, "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>" ."\r\n");
        fputs($fp, "<channel>" ."\r\n");
        if (_SITE_TITLE_TO_SHOW != "")
          fputs($fp, "<title>" . _SITE_TITLE_TO_SHOW . "</title>" ."\r\n");
        else
          fputs($fp, "<title>Bookmarks</title>" ."\r\n");
        //
        if (_SITE_URL_TO_SHOW != "")
          fputs($fp, "<link>" . _SITE_URL_TO_SHOW . "</link>" ."\r\n");
        else
          fputs($fp, "<link>" . $url . "</link>" ."\r\n");
        //
        fputs($fp, "<description>IntraMessenger - Bookmarks</description>" ."\r\n");
        //fputs($fp, "<pubDate>" . date(DATE_RFC822) . "</pubDate>" ."\r\n");
        //fputs($fp, "<lastBuildDate>" . date(DATE_RFC822) . "</lastBuildDate>" ."\r\n");
        fputs($fp, "<pubDate>" . date("D, d M Y H:i:s O") . "</pubDate>" ."\r\n");
        fputs($fp, "<lastBuildDate>" . date("D, d M Y H:i:s O") . "</lastBuildDate>" ."\r\n");
        while( list ($id_bookm, $id_aut, $s_date, $rating, $bmk_title, $bmk_url) = mysqli_fetch_row ($result) )
        {
          if ($id_aut > 0) 
          {
            //$username = f_get_username_of_id($id_aut);
            $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
          }
          else
            $username = $l_admin_users_admin;
          //
          $check = f_encode64(substr(md5(f_encode64($id_bookm)) . md5($id_bookm), 0, 10));
          $bmk_title = f_decode64_wd($bmk_title);
          $title = $bmk_title;
          if (strlen($title) > $max_len)
          {    
            $title = substr($bmk_title, 0, $max_len);
            // Récupération de la position du dernier espace (afin déviter de tronquer un mot)
            $position_espace = strrpos($title, " ");    
            if ($position_espace > 0) $title = substr($title, 0, $position_espace);    
            $title = $title . "...";
          }
          //
          fputs($fp, "<item>" ."\r\n");
          fputs($fp, "<title>" . $title  . "</title>" ."\r\n");
          fputs($fp, "<link>" . $url . _PUBLIC_FOLDER . "/bookmark.php?i=" . f_encode64($id_bookm) . "&amp;c=" . $check . "&amp;</link>" ."\r\n");
          fputs($fp, "<guid isPermaLink='false'>" . $url . _PUBLIC_FOLDER . "/bookmark.php?i=" . f_encode64($id_bookm) . "&amp;</guid>" ."\r\n");
          fputs($fp, "<pubDate>" . date("D, d M Y", strtotime($s_date)) . " " . "</pubDate>" ."\r\n");
          $s_date = date($l_date_format_display, strtotime($s_date));
          fputs($fp, "<description>" . $s_date . " [" . $username . "] " . $bmk_title . "</description>" ."\r\n");
          fputs($fp, "<dc:creator>" . $username . "</dc:creator>" ."\r\n");
          fputs($fp, "</item>" ."\r\n");
        }
        fputs($fp, "</channel>" ."\r\n");
        fputs($fp, "</rss>" ."\r\n");
      }
    }
    flock($fp, 3);
    fclose($fp);
  }
}
?>
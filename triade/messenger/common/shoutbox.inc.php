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
// Flux RSS :
//
function shoutbox_update_rss($id_grp)
{
  GLOBAL $PREFIX_IM_TABLE, $id_connect, $l_date_format_display, $l_time_short_format_display;
  //
  if (_SHOUTBOX_PUBLIC != "")
  {
    $max_len = 30; // title max length
    $url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
    $pos = strrpos($url, "/");
    if ( $pos < (strlen($url)-1) ) $url .= "/";
    //$url = str_replace("distant/", "", $url);
    $pos = strrpos($url, "/", -2);
    $url = substr($url, 0, ($pos +1));
    //
    //$chemin = "../" . _PUBLIC_FOLDER . "/" . "shoutbox.xml" ;
    $chemin = "../" . _PUBLIC_FOLDER . "/rss/" . "shoutbox" ;
    if ($id_grp > 0) $chemin .= f_encode64($id_grp);
    $chemin .= ".xml";
    //
    $fp = fopen($chemin, "w");
    if (flock($fp, 2));
    {
      $requete  = " select ID_SHOUT, SBX_DISPLAY, ID_USER_AUT, SBX_TIME, SBX_DATE, SBX_RATING, SBX_TEXT";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
      $requete .= " WHERE SBX_DISPLAY > 0 "; // publié
      if ($id_grp > 0) 
        $requete .= " and ID_GROUP_DEST = " . $id_grp;
      else
        $requete .= " and ID_GROUP_DEST = 0";
      //
      $requete .= " ORDER BY ID_SHOUT DESC ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-111k]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        fputs($fp, "<?xml version='1.0' encoding='ISO-8859-1' ?>" ."\r\n");
        /* fputs($fp, "<?xml-stylesheet type='text/xsl' href='shoutbox.xslt' ?>" ."\r\n");  */
        fputs($fp, "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>" ."\r\n");
        fputs($fp, "<channel>" ."\r\n");
        if (_SITE_TITLE_TO_SHOW != "")
          fputs($fp, "<title>" . _SITE_TITLE_TO_SHOW . "</title>" ."\r\n");
        else
          fputs($fp, "<title>ShoutBox</title>" ."\r\n");
        //
        if (_SITE_URL_TO_SHOW != "")
          fputs($fp, "<link>" . _SITE_URL_TO_SHOW . "</link>" ."\r\n");
        else
          fputs($fp, "<link>" . $url . "</link>" ."\r\n");
        //
        fputs($fp, "<description>IntraMessenger - ShoutBox</description>" ."\r\n");
        //fputs($fp, "<pubDate>" . date(DATE_RFC822) . "</pubDate>" ."\r\n");
        //fputs($fp, "<lastBuildDate>" . date(DATE_RFC822) . "</lastBuildDate>" ."\r\n");
        fputs($fp, "<pubDate>" . date("D, d M Y H:i:s O") . "</pubDate>" ."\r\n");
        fputs($fp, "<lastBuildDate>" . date("D, d M Y H:i:s O") . "</lastBuildDate>" ."\r\n");
        while( list ($id_shout, $s_display, $id_aut, $s_time, $s_date, $rating, $txt) = mysqli_fetch_row ($result) )
        {
          //$username = f_get_username_of_id($id_aut);
          $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
          $check = md5(md5(f_encode64($id_shout)) . md5($id_shout));
          $txt = f_decode64_wd($txt);
          $title = $txt;
          if (strlen($title) > $max_len)
          {    
            $title = substr($txt, 0, $max_len);
            // Récupération de la position du dernier espace (afin déviter de tronquer un mot)
            $position_espace = strrpos($title, " ");    
            if ($position_espace > 0) $title = substr($title, 0, $position_espace);    
            $title = $title . "...";
          }
          //
          fputs($fp, "<item>" ."\r\n");
          fputs($fp, "<title>" . $title  . "</title>" ."\r\n");
          fputs($fp, "<link>" . $url . _PUBLIC_FOLDER . "/shoutbox_message.php?i=" . f_encode64($id_shout) . "&amp;c=" . $check . "&amp;</link>" ."\r\n");
          fputs($fp, "<guid isPermaLink='false'>" . $url . _PUBLIC_FOLDER . "/shoutbox_message.php?i=" . f_encode64($id_shout) . "&amp;</guid>" ."\r\n");
          fputs($fp, "<pubDate>" . date("D, d M Y", strtotime($s_date)) . " " . date("H:i:s O", strtotime($s_time)) . "</pubDate>" ."\r\n");
          $s_date = date($l_date_format_display, strtotime($s_date));
          $s_time = date($l_time_short_format_display, strtotime($s_time));
          fputs($fp, "<description>" . $s_date . " - " . $s_time . " [" . $username . "] " . $txt . "</description>" ."\r\n");
          //fputs($fp, "<content>" . $txt . "</content>" ."\r\n");
          fputs($fp, "<dc:creator>" . $username . "</dc:creator>" ."\r\n");
          //fputs($fp, "" ."\r\n");
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


function f_shoutbox_last_id_if_new($last_id_m)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $id_max_msg = -1;
  if (_SHOUTBOX != "")
  {
    $id_max_msg = 0;
    $requete  = " select max(ID_SHOUT)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " WHERE SBX_DISPLAY > 0 ";
    //if (intval($last_id_m) <= 0) $requete .= " and SBX_DATE = CURDATE() ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5p]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_max_msg) = mysqli_fetch_row ($result);
      if ( ($id_max_msg <= intval($last_id_m)) and (intval($last_id_m) > 0) )
        $id_max_msg = 0; // rien de neuf
    }
  }
  //
  return $id_max_msg;
}


function f_id_group_id_sbx($id_m)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $id_grp = 0;
  $requete  = " select ID_GROUP_DEST ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
  $requete .= " WHERE ID_SHOUT = " . $id_m;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M5m]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($id_grp) = mysqli_fetch_row ($result);
  }
  //
  return $id_grp;
}


function shoutbox_remove_old_msg()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  if (intval(_SHOUTBOX_STORE_DAYS) > 1)
  {
    $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " WHERE TO_DAYS(CURDATE()) - TO_DAYS(SBX_DATE) > ". intval(_SHOUTBOX_STORE_DAYS);
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5n]", $requete);
  }
  //
  if (intval(_SHOUTBOX_STORE_MAX) > 5)
  {
    $requete  = " select count(ID_SHOUT)";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5q]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nbtotal) = mysqli_fetch_row ($result);
      if ($nbtotal > intval(_SHOUTBOX_STORE_MAX))
      {
        $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
        $requete .= " order by ID_SHOUT LIMIT " . ($nbtotal - intval(_SHOUTBOX_STORE_MAX));
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M5h]", $requete);
      }
    }

  }
}



function stats_sbx_inc($id_user)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " SELECT SBS_NB, SBS_NB_LAST_DATE, SBS_LAST_DATE, SBS_NB_LAST_WEEK, SBS_LAST_WEEK, WEEK(CURDATE()) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M5a]", $requete);
  if ( mysqli_num_rows($result) == 1 ) 
  {
    list($nb, $nb_last_date, $last_date, $nb_last_week, $last_week, $this_week) = mysqli_fetch_row ($result);
    //
    $nb++;
    $last_date = date("Ymd", strtotime($last_date));
    if ($last_date != date("Ymd"))
    {
      $nb_last_date = 1;
      shoutbox_remove_old_msg();
    }
    else
      $nb_last_date++;
    //
    if ($nb_last_week != $this_week)
      $nb_last_week = 1;
    else
      $nb_last_week++;
    //
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $requete .= " SET SBS_NB = " . $nb . ", ";
    $requete .= "     SBS_NB_LAST_DATE = " . $nb_last_date . ", SBS_LAST_DATE = CURDATE(), ";
    $requete .= "     SBS_NB_LAST_WEEK = " . $nb_last_week . ", SBS_LAST_WEEK = WEEK(CURDATE()) ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5b]", $requete);
  }
  else
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS (ID_USER_AUT, SBS_NB, SBS_NB_LAST_DATE, SBS_LAST_DATE, SBS_NB_LAST_WEEK, SBS_LAST_WEEK ) ";
    $requete .= " VALUES (" . $id_user . ", 1, 1, CURDATE(), 1, WEEK(CURDATE()) ) " ;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5c]", $requete);
    //
    shoutbox_remove_old_msg();
  }
  //
  if (_STATISTICS != "")
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " SET STA_SBX_NB_MSG = STA_SBX_NB_MSG +  1 ";
    $requete .= " WHERE STA_DATE = CURDATE()";
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5k]", $requete);
  }
  //
  update_last_activity_user($id_user);
}


function stats_sbx_add_note_user($id_user_aut, $note)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " UPDATE " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
  if (intval($note) > 0)
    $requete .= " SET SBS_NB_VOTE_M = SBS_NB_VOTE_M +  1 ";
  else
    $requete .= " SET SBS_NB_VOTE_L = SBS_NB_VOTE_L +  1 ";
  //
  $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
  $requete .= " limit 1 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M5d]", $requete);
}


function stats_sbx_add_reject_msg($id_user_aut)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " UPDATE " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
  $requete .= " SET SBS_NB_REJECT = SBS_NB_REJECT +  1 ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
  $requete .= " limit 1 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M5e]", $requete);
}


function stats_sbx_update_scores($id_shout, $id_user_aut)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $score_more = 0;
  $requete  = " select count(SBV_VOTE_M)";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
  $requete .= " WHERE ID_SHOUT = " . $id_shout;
  $requete .= " AND SBV_VOTE_M > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M5f]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($score_more) = mysqli_fetch_row ($result);
  }
  //
  $score_less = 0;
  $requete  = " select count(SBV_VOTE_L)";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
  $requete .= " WHERE ID_SHOUT = " . $id_shout;
  $requete .= " AND SBV_VOTE_L < 0 ";
  //$requete .= " AND SBV_VOTE_L > 0 ";  NON !
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M5g]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($score_less) = mysqli_fetch_row ($result);
  }
  //
  //
  if ($score_more > 0)
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $requete .= " SET SBS_MAX_VOTE_M = " . $score_more;
    $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
    $requete .= " AND SBS_MAX_VOTE_M < " . $score_more;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5h]", $requete);
  }
  if ($score_less > 0)
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ";
    $requete .= " SET SBS_MAX_VOTE_L = " . $score_less;
    $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
    $requete .= " AND SBS_MAX_VOTE_L < " . $score_less;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M5j]", $requete);
  }
}

?>

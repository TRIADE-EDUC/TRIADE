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
if ( (!isset($_GET['iu'])) or (!isset($_GET['t'])) or (!isset($_GET['u'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$n_version =	intval($_GET['v']);
$title =      $_GET['t'];
$url =        $_GET['u'];
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//if (isset($_GET['ig'])) $id_grp = intval($_GET['ig']);  else  $id_grp = "0";  
//
//if (preg_match("#[^0-9]#", $id_grp)) $id_grp = "0";
if (preg_match("#[^0-9]#", $id_user)) $id_user = "0";
if (preg_match("#[^0-9]#", $n_version)) $n_version = "0";
//
if ( ($id_user > 0) and ($n_version > 34) and ($title != "") and ($url != "") and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  require ("../common/bookmark.inc.php");
  require("lang.inc.php"); // pour format date et heure.
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F56#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_bookmarks = _BOOKMARKS;
  $t_bookmarks_need_approval = _BOOKMARKS_NEED_APPROVAL; 
  $t_censor_messages = _CENSOR_MESSAGES;
  $t_log_messages = _HISTORY_MESSAGES_ON_ACP;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_bookmarks = f_role_permission($id_role, "BOOKMARKS", _BOOKMARKS);
      $t_bookmarks_need_approval = f_role_permission($id_role, "BOOKMARKS_NEED_APPROVAL", _BOOKMARKS_NEED_APPROVAL);
      $t_censor_messages = f_role_permission($id_role, "CENSOR_MESSAGES", _CENSOR_MESSAGES);
      $t_log_messages = f_role_permission($id_role, "HISTORY_MESSAGES_ON_ACP", _HISTORY_MESSAGES_ON_ACP);
    }
  }
  //
  if ($t_bookmarks == "")
  {
    die(">F56#KO#2#"); // 2: Not allowed (option not activated)  
  }
  //
  $this_group_bookmark_need_approval = ""; // que ce soit en groupe OU non.
  if ($t_bookmarks_need_approval != "")
  {
    $this_group_bookmark_need_approval = "X"; // default  = _BOOKMARKS_NEED_APPROVAL
  }
  //
  //
  if ($this_group_bookmark_need_approval != "")
  {
    $requete  = " select count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ";
    $requete .= " WHERE BMK_DISPLAY = 0 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-131d]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($nb_reject) = mysqli_fetch_row ($result);
      //if ($nb_reject >= intval(_BOOKMARKS_APPROVAL_QUEUE) )
      if ($nb_reject >= 20)
        die(">F56#KO#5#"); // 5: Approval queue Over quota
    }
  }
  //
  //
  $title_clair = "";
  if ( ($t_censor_messages != '') or ($t_log_messages != '') )
  {
    $title_clair = f_decode64_wd($title);
  }
  //
  //
  // on censure les mots interdits par l'administrateur :
  if ($t_censor_messages != '')
  {
    if (is_readable("../common/config/censure.txt"))
    {
      $title_clair = trim($title_clair);
      require ("../common/words_filtering.inc.php");
      $title_clair = textCensure($title_clair, "../common/config/censure.txt");
      $title = f_encode64($title_clair);
    }
  }
  //
  //
  $sending = "#";
  $requete  = "INSERT INTO " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ( ID_BOOKMCATEG, ID_USER_AUT, BMK_TITLE, BMK_URL, BMK_DATE, BMK_DISPLAY) ";
  $requete .= "VALUES (null, " . $id_user . ", '" . $title . "', '" . $url . "', CURDATE(), ";
  if ($this_group_bookmark_need_approval != "")
    $requete .= "0 ) ";
  else
  {
    $requete .= "1 ) ";
    $sending = date("H:i:s") . "#"; 
  }
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-131a]", $requete);
  //
  // Bookmark bien envoyé
  echo ">F56#OK#" . $sending . "#";
  //
  //
  if ( ($this_group_bookmark_need_approval != "") and (_SEND_ADMIN_ALERT != "") )
  {
    // Do not send admin notify, if already admin.
    if (f_is_user_admin($id_user) == "")
    {
      $txt = $l_index_bookmarks_pending;
      if ($txt == "") $txt = "Bookmark(s) waiting Approval...";
      send_alert_message_to_admins($txt);
    }
  }
  //
  //
  // si option de log (archivage) des bookmars activé :
  if ($t_log_messages != '')
  {
    // on récupère le username expéditeur :
    $username = f_get_username_of_id($id_user);
    //
    $ip = $_SERVER['REMOTE_ADDR'];	
    //
    $chemin = "log/" . "bookmarks_log.txt" ;
    $fp = fopen($chemin, "a");
    if (flock($fp, 2));
    {
      //fputs($fp,date("d/m/Y;H:i:s") . ";" . $username . ";" . $msg_clair . ";" . $ip ."\r\n");
      fputs($fp,date($l_date_format_display . ";" . $l_time_format_display) . ";" . $username . ";" . $title_clair . ";" . f_decode64_wd($url) . ";"  . $ip ."\r\n");
    }
    flock($fp, 3);
    fclose($fp);
  }
  //
  //
  // Flux RSS :
  if ( (_BOOKMARKS_PUBLIC != "") and ($t_bookmarks_need_approval == "") )
  {
    bookmarks_update_rss();
  }
  //
  mysqli_close($id_connect);
}
?>
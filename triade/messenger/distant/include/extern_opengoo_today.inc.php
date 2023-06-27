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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
if ( (!isset($_GET['u'])) or (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) or (!isset($_GET['c'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$id_session = intval(f_decode64_wd($_GET['s']));
$ip = 			  f_decode64_wd($_GET['ip']);
$version =	  intval($_GET['v']);
$username = 	f_decode64_wd($_GET['u']);
$username =   f_clean_username($username);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( (_EXTERNAL_AUTHENTICATION == "opengoo") and ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sessions.inc.php");
  require ("../common/sql.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die("KO#1#"); 
  //
  //
  function DisplayTime($hr, $gmt, $dec) 
  {
    $hor = explode ('.', $hr);
    return date("H:i",mktime($hor[0] + strval($gmt) + strval($dec),($hor[1] / 100 * 60)%60,0,1,1,2000)); 
  }
  //
  //
  $idUserOpenGoo = 0;
  require("../common/config/extern/opengoo.config.inc.php");
  if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
  {
    // If OpenGoo not on same server/database
    mysqli_close($id_connect);
    require("../common/extern/extern.sql.inc.php");
    $id_connect = $id_connect_extern;
  }
  $requete  = " select id, username, company_id, timezone  FROM " . $extern_prefix . "users ";
  $requete .= " WHERE LOWER(username) = '" . $username . "' ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-S2a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($idUserOpenGoo, $login_opengoo, $company_id, $gmt_user) = mysqli_fetch_row ($result);
    if ($login_opengoo != $username) $idUserOpenGoo = 0;
  }
  //
  if (intval($idUserOpenGoo) > 0) // is user valid
  {
    //
    // TASK (T).
    //
    $requete  = " SELECT id, title, due_date ";
    $requete .= " FROM " . $extern_prefix . "project_tasks ";
    $requete .= " WHERE (assigned_to_user_id = " . $idUserOpenGoo . " or (assigned_to_user_id = 0 and assigned_to_company_id = " . $company_id . ") ) ";
    //$requete .= " and due_date = '" . date("Ymd") . "' ";
    $requete .= " and due_date = '" . date("Y-m-d") . "' ";
    $requete .= " and completed_by_id = 0 ";
    $requete .= " and trashed_by_id = 0 ";
    $requete .= " ORDER BY orders";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) 
      error_sql_log("[ERR-S2b]", $requete);
    else
    {
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($id, $title, $due_date) = mysqli_fetch_row ($result) )
        {
          //$due_date = DisplayTime($due_date, $gmt_user, $dec);
          //
          echo ">F71#T#" . $id . "#" . f_encode64($title) . "#" . $due_date . "####" . "#|"; // séparateur de ligne : '|' (pipe).
        }
      }
      //else
        //echo("KO#7#");
    }
    //
    //
    // MILESTONES (M).
    //
    $requete  = " SELECT id, title, due_date ";
    $requete .= " FROM " . $extern_prefix . "project_milestones ";
    $requete .= " WHERE (assigned_to_user_id = " . $idUserOpenGoo . " or (assigned_to_user_id = 0 and assigned_to_company_id = " . $company_id . ") ) ";
    //$requete .= " and due_date = '" . date("Ymd") . "' ";
    $requete .= " and due_date = '" . date("Y-m-d") . "' ";
    $requete .= " and completed_by_id = 0 ";
    $requete .= " and trashed_by_id = 0 ";
    $requete .= " ORDER BY orders";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) 
      error_sql_log("[ERR-S2d]", $requete);
    else
    {
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($id, $title, $due_date) = mysqli_fetch_row ($result) )
        {
          //$due_date = DisplayTime($due_date, $gmt_user, $dec);
          //
          echo ">F71#M#" . $id . "#" . f_encode64($title) . "#" . $due_date . "####" . "#|"; // séparateur de ligne : '|' (pipe).
        }
      }
      //else
        //echo("KO#7#");
    }
    //
    //
    // EVENTS (E).
    //
    $requete  = " SELECT id, subject, start, duration, repeat_num, repeat_d, repeat_m, repeat_y, repeat_h ";
    $requete .= " FROM " . $extern_prefix . "project_events EVN, " . $extern_prefix . "event_invitations INV ";
    $requete .= " WHERE EVN.id = INV.event_id ";
    $requete .= " and INV.user_id = " . $idUserOpenGoo;
    //$requete .= " and start = '" . date("Ymd") . "' ";
    $requete .= " and start = '" . date("Y-m-d") . "' ";
    //$requete .= " and duration   ";
    //$requete .= " ORDER BY ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) 
      error_sql_log("[ERR-S2c]", $requete);
    else
    {
      if ( mysqli_num_rows($result) > 0 )
      {
        while( list ($id, $subject, $start) = mysqli_fetch_row ($result) )
        {
          //$due_date = DisplayTime($due_date, $gmt_user, $dec);
          //
          echo ">F71#E#" . $id . "#" . f_encode64($subject) . "#" . $start . "####" . "#|"; // séparateur de ligne : '|' (pipe).
        }
      }
      //else
        //echo("KO#7#");
    }
  }
  else
    die("KO#5#"); // user not find
  }
  //
  mysqli_close($id_connect);
}
else
  echo("KO#6#"); // user not find
?>
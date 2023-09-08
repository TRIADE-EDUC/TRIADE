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
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	    intval(f_decode64_wd($_GET['iu']));
$id_user = 		  (intval($id_user) - intval($action));
$ip = 			    f_decode64_wd($_GET['ip']);
$n_version =	  intval($_GET['v']);
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($n_version > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  //require ("../common/sessions.inc.php");
  //
  //
  /*
  $t_sharefiles = _SHARE_FILES;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_sharefiles = f_role_permission($id_role, "SHARE_FILES", _SHARE_FILES);
    }
  }
  if ($t_sharefiles == "")
  {
    die(">F148#KO#2#"); // 2: n'a pas les droits (option non activ�e).
  }
  */
  //
  //
  $requete  = " select SQL_CACHE ID_FILEMEDIA, FMD_NAME ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA ";
  $requete .= " order by FMD_NAME ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-148a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo ">F148#OK####|";
    while( list ($id_media, $media_name) = mysqli_fetch_row ($result) )
    {
      echo f_encode64("#" . $id_media . "#" . $media_name . "#") . "|"; // s�parateur de ligne : '|' (pipe).
    }
  }
  else
  {
    echo ">F148#0#-#0#";
    //
    require ("../common/share_files.inc.php");
    sf_remplir_medias();
  }
  //
  mysqli_close($id_connect);
}
?>
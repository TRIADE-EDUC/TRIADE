<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2012 THeUDS           **
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
$version =	    intval($_GET['v']);
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
if ( ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  /*
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  if (f_verif_id_session_id_user($id_user, $id_session) <> 'OK')
    die ("Session KO.");
  mysqli_close($id_connect);
  */
  //
  //
  $repert = "avatar/";
  $rep = opendir($repert);
  echo ">F68#";
  while ($file = readdir($rep))
  {
    if ( ($file != "..") and ($file != ".") and ($file != "") and (!is_dir($file)) ) // .inc.php && strpos(strtolower($file), ".*") 
    {
      $ext = strtolower(array_pop(explode('.', $file)));
      //$tmime = mime_content_type($repert . $file);
      //if (substr($tmime, 0, 6) == "image/")
      //{
        $size = getimagesize($repert . $file);
        if ( (strlen($file) <= 20) and (strpos(".png.gif.jpg.jpeg", $ext)) and (intval($size[0]) >= 30) and (intval($size[1]) >= 30) and (intval($size[0]) <= 150) and (intval($size[1]) <= 150) )
        {
          $avatar = f_encode64($file);
          echo $avatar . "#";
        }
      //}
    }
  }
  closedir($rep);
}
?>
